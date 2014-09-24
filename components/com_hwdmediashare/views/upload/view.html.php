<?php
/**
 * @package     Joomla.site
 * @subpackage  Component.hwdmediashare
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class hwdMediaShareViewUpload extends JViewLegacy
{
	/**
	 * The show form view variable.
         * 
         * @access  public
	 * @var     boolean
	 */ 
	public $show_form = true;

	/**
	 * The upload method view variable.
         * 
         * @access  public
	 * @var     boolean
	 */ 
	public $method = false;
        
	/**
	 * Display the view.
	 *
	 * @access  public
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 * @return  void
	 */
	public function display($tpl = null)
	{
                // Initialise variables.
                $hwdms = hwdMediaShareFactory::getInstance();
		$app = JFactory::getApplication();
                $lang = JFactory::getLanguage();
                $document = JFactory::getDocument();
                $user = JFactory::getUser();
                
                // Get data from the model.
		$this->config = $hwdms->getConfig();
                $this->form = $this->get('Form');
		$this->state = $this->get('State');
		$this->params = $this->state->params;
                $this->method = ($app->input->get('method', '', 'word') ? $app->input->get('method', '', 'word') : false);

                // Check access.
                if (!$user->authorise('hwdmediashare.upload','com_hwdmediashare') && !$user->authorise('hwdmediashare.import','com_hwdmediashare'))
                {
                        $app->enqueueMessage( JText::_('COM_HWDMS_ERROR_NOAUTHORISED_ADDMEDIA') );
                        $app->redirect( $this->config->get('no_access_redirect') > 0 ? ContentHelperRoute::getArticleRoute($this->config->get('no_access_redirect')) : hwdMediaShareHelperRoute::getMediaRoute() );
                }
                
                // Include JHtml helpers.
                JHtml::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/html');
                JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
                
                // Import HWD libraries.                
                hwdMediaShareFactory::load('activities');
                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('files');
                hwdMediaShareFactory::load('media');
		hwdMediaShareFactory::load('remote');
                hwdMediaShareFactory::load('thumbnails');
		hwdMediaShareFactory::load('utilities');

                $this->utilities = hwdMediaShareUtilities::getInstance();
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));
                $this->return = base64_encode(JFactory::getURI()->toString());

                // Check upload limits.
                if (!hwdMediaShareUpload::checkLimits())
                {
                        $app->redirect(hwdMediaShareHelperRoute::getMyMediaRoute());   
                }
                
		// Determine if we need to show the form.
		if ($this->config->get('upload_workflow') == 0 && $this->show_form && $this->method != 'remote') 
		{
			$this->setLayout('form');
		}
                else
                {              
                        $this->jformdata = hwdMediaShareUpload::getProcessedUploadData(); 
                        $this->jformreg = new JRegistry($this->jformdata);

                        $this->localExtensions = $this->get('localExtensions');
                        $this->showPlatform = $this->get('platformStatus');

                        if ($this->config->get('enable_uploads_file') == 1) 
                        {
                                if ($this->config->get('upload_tool') == 1 && count($this->localExtensions)) 
                                {
                                        // Add assets to the document.
                                        $this->get('FancyUploadScript');
                                }
                                elseif ($this->config->get('upload_tool') == 2 && $this->config->get('upload_tool_perl') == 1) 
                                {
                                        $this->uberUploadHtml = $this->get('UberUploadScript');
                                }
                        }
                        
                        if ($this->method == 'remote') 
                        {
                                $this->setLayout('remote');
                        }                           
                }
                
                // Check for errors.
                if (count($errors = $this->get('Errors')))
                {
                        JError::raiseError(500, implode('<br />', $errors));
                        return false;
                }

                // Check for terms prompt.
		if ($this->get('terms') === true)
		{
                        $tpl = 'terms';                        
		}
                
		$this->_prepareDocument();
                
		// Display the template.
		parent::display($tpl);
	}
        
	/**
	 * Prepares the document.
	 *
         * @access  protected
	 * @return  void
	 */
	protected function _prepareDocument()
	{
		$app = JFactory::getApplication();
		$menus = $app->getMenu();
		$pathway = $app->getPathway();
		$title = null;

                // Add page assets.
                JHtml::_('hwdhead.core', $this->params);

		// Define the page title and headings. 
		$menu = $menus->getActive();
		if ($menu)
		{
                        $title = $this->params->get('page_title');
                        $heading = $this->params->get('page_heading', JText::_('COM_HWDMS_UPLOAD'));
		}
		else
		{
                        $title = JText::_('COM_HWDMS_UPLOAD');
                        $heading = JText::_('COM_HWDMS_UPLOAD');
		}

                $this->params->set('page_title', $title);
                $this->params->set('page_heading', $heading);
                
		// If the menu item does not concern this view then add a breadcrumb.
		if ($menu && ($menu->query['option'] != 'com_hwdmediashare' || $menu->query['view'] != 'upload'))
		{       
                        // Breadcrumb support.
			$path = array(array('title' => JText::_('COM_HWDMS_UPLOAD'), 'link' => ''));
                                                
			$path = array_reverse($path);
			foreach($path as $item)
			{
				$pathway->addItem($item['title'], $item['link']);
			}                    
		}
                
		// Check for empty title and add site name when configured.
		if (empty($title))
                {
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1)
                {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2)
                {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
                
                // Set metadata.
		$this->document->setTitle($title);

		if ($menu && $menu->query['option'] == 'com_hwdmediashare' && $menu->query['view'] == 'upload' && $this->params->get('menu-meta_description'))
                {
			$this->document->setDescription($this->params->get('menu-meta_description'));
                } 
                elseif ($this->params->get('meta_desc'))
                {
			$this->document->setDescription($this->params->get('meta_desc'));
                }   

		if ($menu && $menu->query['option'] == 'com_hwdmediashare' && $menu->query['view'] == 'upload' && $this->params->get('menu-meta_keywords'))
                {
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
                } 
		elseif ($this->params->get('meta_keys'))
                {
			$this->document->setMetadata('keywords', $this->params->get('meta_keys'));
                }

		if ($this->params->get('meta_rights'))
                {
			$this->document->setMetadata('copyright', $this->params->get('meta_rights'));
                } 
	}
        
 	/**
	 * Method to render the platform upload tool.  
	 *
         * @access  protected
	 * @return  void
	 */
	protected function getPlatformUploadForm()
	{
                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                // Load HWD utilities.
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                
                $pluginClass = 'plgHwdmediashare'.$config->get('platform');
                $pluginPath = JPATH_ROOT.'/plugins/hwdmediashare/'.$config->get('platform').'/'.$config->get('platform').'.php';
                if (file_exists($pluginPath))
                {
                        JLoader::register($pluginClass, $pluginPath);
                        $HWDplatform = call_user_func(array($pluginClass, 'getInstance'));
                        if ($form = $HWDplatform->getUploadForm())
                        {
                                return $form;
                        }
                        else
                        {
                                return $utilities->printNotice($HWDcomments->getError());
                        }
                }
	}
}
