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

class hwdMediaShareViewMediaForm extends JViewLegacy 
{
	protected $state;

	protected $item;

	protected $form;

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
		$user = JFactory::getUser();
                
                // Get data from the model.
		$this->state = $this->get('State');
		$this->item = $this->get('Item');
		$this->form = $this->get('Form');
                $this->return_page = $this->get('ReturnPage');
		$this->params = $this->state->params;

                // Register classes.
                JLoader::register('JHtmlHwdIcon', JPATH_COMPONENT . '/helpers/icon.php');
                JLoader::register('JHtmlHwdDropdown', JPATH_COMPONENT . '/helpers/dropdown.php');
                JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');
                
                // Import HWD libraries.                
                hwdMediaShareFactory::load('activities');
                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('files');
                hwdMediaShareFactory::load('media');
                hwdMediaShareFactory::load('thumbnails');
		hwdMediaShareFactory::load('utilities');
                
                $this->utilities = hwdMediaShareUtilities::getInstance();
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));
                $this->isNew = $this->item->id == 0;

                // Check access.
		if (empty($this->item->id))
		{
			$authorised = $user->authorise('core.create', 'com_hwdmediashare') || (count($user->getAuthorisedCategories('com_hwdmediashare', 'core.create')));
		}
		else
		{
			$authorised = $this->item->attributes->get('access-edit');
		}
		if ($authorised !== true)
		{
			JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
			return false;
		}
                
                // Check for errors.
                if (count($errors = $this->get('Errors')))
                {
                        JError::raiseError(500, implode('<br />', $errors));
                        return false;
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
		// Initialise variables.
                $app = JFactory::getApplication();
                
                // Add page assets.
                JHtml::_('bootstrap.framework');
                $this->document->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/hwd.css');
                if ($this->params->get('load_joomla_css') != 0) $this->document->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/joomla.css');
                if ($this->params->get('list_thumbnail_aspect') != 0) $this->document->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/aspect.css');
                if ($this->params->get('list_thumbnail_aspect') != 0) $this->document->addScript(JURI::base( true ).'/media/com_hwdmediashare/assets/javascript/aspect.js');

		// Define the page heading.                
                if ($app->input->get('view', '', 'word') == 'mediaform')
                {
                        $this->params->set('page_heading', JText::sprintf('COM_HWDMS_EDIT_MEDIAX', $this->escape($this->item->title)));
                }
                elseif ($app->input->get('task', '', 'word') == 'share')
                {
                        $this->params->set('page_heading', JText::_('COM_HWDMS_SHARE_THIS_MEDIA'));
                        JHtml::_('jquery.framework');
                        
                        // Add script to select input when focused.
                        $this->document->addScriptDeclaration('
                            
                        (function($){
                          $(document).ready(function() {
                            $("input:text").focus(function () { $(this).select(); } ).mouseup(function (e) {e.preventDefault(); });
                          });
                        })(jQuery);
                        
                        ');         
                }
                else
                {
                        $this->params->set('page_heading', JText::_('COM_HWDMS_MEDIA'));
                }
                
		// Define the page title.                
                $this->document->setTitle($this->params->get('page_heading'));  
	}  

	/**
	 * Display the report view.
	 *
	 * @access  public
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 * @return  void
	 */
	public function report($tpl = null)
	{
		// Initialise variables.
		$user = JFactory::getUser();
		$app = JFactory::getApplication();
                
                // Get data from the model.
                $this->form = $this->get('ReportForm');
                $this->item = $this->get('Item');
		$this->state = $this->get('State');
		$this->params = $this->state->params;
                
                // Import HWD libraries.                
		hwdMediaShareFactory::load('utilities');
                
                $this->utilities = hwdMediaShareUtilities::getInstance();
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));
                $this->return = base64_encode(JFactory::getURI()->toString());

                if (!$user->authorise('hwdmediashare.report', 'com_hwdmediashare'))
                {
                        $this->utilities->printModalNotice('COM_HWDMS_NOTICE_NO_FEATURE_ACCESS', 'COM_HWDMS_NOTICE_NO_FEATURE_ACCESS_DESC'); 
                        return;
                }
                
                // Check for errors.
                if (count($errors = $this->get('Errors')))
                {
                        JError::raiseError(500, implode('<br />', $errors));
                        return false;
                }

		$this->_prepareDocument();
                
		// Display the template.
		parent::display('report');               
	}
        
	/**
	 * Display the view.
	 *
	 * @access  public
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 * @return  void
	 */
	public function share($tpl = null)
	{
                // Get data from the model.
                $this->item = $this->get('Item');
		$this->state = $this->get('State');
		$this->params = $this->state->params;

                // Register classes.
                JLoader::register('JHtmlHwdIcon', JPATH_COMPONENT . '/helpers/icon.php');
                JLoader::register('JHtmlHwdDropdown', JPATH_COMPONENT . '/helpers/dropdown.php');
                JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');
                
                // Import HWD libraries.                
                hwdMediaShareFactory::load('activities');
                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('files');
                hwdMediaShareFactory::load('media');
                hwdMediaShareFactory::load('thumbnails');
		hwdMediaShareFactory::load('utilities');
                
                $this->utilities = hwdMediaShareUtilities::getInstance();
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));
                $this->return = base64_encode(JFactory::getURI()->toString());

                // Check for errors.
                if (count($errors = $this->get('Errors')))
                {
                        JError::raiseError(500, implode('<br />', $errors));
                        return false;
                }

		$this->_prepareDocument();
                
		// Display the template.
		parent::display('share');
	}
        
	/**
	 * Display the view.
	 *
	 * @access  public
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 * @return  void
	 */
	public function link($tpl = null)
	{
                // Get data from the model.
                $this->item = $this->get('Item');
		$this->form = JForm::getInstance('share', JPATH_SITE.'/components/com_hwdmediashare/models/forms/link.xml', array('control' => 'jform'));
                $this->state = $this->get('State');
		$this->params = $this->state->params;

                // Register classes.
                JLoader::register('JHtmlHwdIcon', JPATH_COMPONENT . '/helpers/icon.php');
                JLoader::register('JHtmlHwdDropdown', JPATH_COMPONENT . '/helpers/dropdown.php');
                JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');
                
                // Import HWD libraries.                
                hwdMediaShareFactory::load('activities');
                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('files');
                hwdMediaShareFactory::load('media');
                hwdMediaShareFactory::load('thumbnails');
		hwdMediaShareFactory::load('utilities');
                
                $this->utilities = hwdMediaShareUtilities::getInstance();
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));
                $this->return = base64_encode(JFactory::getURI()->toString());

                // Check for errors.
                if (count($errors = $this->get('Errors')))
                {
                        JError::raiseError(500, implode('<br />', $errors));
                        return false;
                }

		$this->_prepareDocument();
                
		// Display the template.
		parent::display('link');
	}

	/**
	 * Display the view.
	 *
	 * @access  public
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 * @return  void
	 */
	public function download($tpl = null)
	{
                // Initialise variables.
		$user = JFactory::getUser();
                $groups	= $user->getAuthorisedViewLevels();
                
                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                // Get data from the model.
                $this->item = $this->get('Item');
		$this->downloads = $this->get('Downloads');
                $this->state = $this->get('State');
		$this->params = $this->state->params;

                // Register classes.
                JLoader::register('JHtmlHwdIcon', JPATH_COMPONENT . '/helpers/icon.php');
                JLoader::register('JHtmlHwdDropdown', JPATH_COMPONENT . '/helpers/dropdown.php');
                JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');
                
                // Import HWD libraries.                
                hwdMediaShareFactory::load('activities');
                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('files');
                hwdMediaShareFactory::load('media');
                hwdMediaShareFactory::load('thumbnails');
		hwdMediaShareFactory::load('utilities');
                
                $this->utilities = hwdMediaShareUtilities::getInstance();
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));
                $this->return = base64_encode(JFactory::getURI()->toString());

                // Check for errors.
                if (count($errors = $this->get('Errors')))
                {
                        JError::raiseError(500, implode('<br />', $errors));
                        return false;
                }
                
                // Check download access.
                if (!in_array($config->get('default_download'), $groups) || ($this->item->download > 0 && !in_array($this->item->download, $groups))) 
                {
                        $this->utilities->printModalNotice('COM_HWDMS_NOTICE_NO_DOWNLOAD_ACCESS', 'COM_HWDMS_NOTICE_NO_DOWNLOAD_ACCESS_DESC'); 
                        return;
		}

		$this->_prepareDocument();
                
		// Display the template.
		parent::display('download');
	}

	/**
	 * Display the view.
	 *
	 * @access  public
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 * @return  void
	 */
	public function meta($tpl = null)
	{
                // Get data from the model.
                $this->item = $this->get('Item');
		$this->meta = $this->get('Meta');                
		$this->state = $this->get('State');
		$this->params = $this->state->params;

                // Register classes.
                JLoader::register('JHtmlHwdIcon', JPATH_COMPONENT . '/helpers/icon.php');
                JLoader::register('JHtmlHwdDropdown', JPATH_COMPONENT . '/helpers/dropdown.php');
                JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');
                
                // Import HWD libraries.                
                hwdMediaShareFactory::load('activities');
                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('files');
                hwdMediaShareFactory::load('media');
                hwdMediaShareFactory::load('thumbnails');
		hwdMediaShareFactory::load('utilities');
                
                $this->utilities = hwdMediaShareUtilities::getInstance();
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));
                $this->return = base64_encode(JFactory::getURI()->toString());

                // Check for errors.
                if (count($errors = $this->get('Errors')))
                {
                        JError::raiseError(500, implode('<br />', $errors));
                        return false;
                }

		$this->_prepareDocument();
                
		// Display the template.
		parent::display('meta');
	}        
}
