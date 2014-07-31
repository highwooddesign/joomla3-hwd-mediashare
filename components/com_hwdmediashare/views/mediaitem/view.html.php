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

class hwdMediaShareViewMediaItem extends JViewLegacy
{
        public $item;

	public $state;
        
	public $params;
        
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
                $app = JFactory::getApplication();
                
                // Get data from the model.
                $this->item = $this->get('Item');
		$this->state = $this->get('State');
		$this->params = $this->state->params;
                $this->activities = $this->get('Activities');
                
                // Register classes.
                JLoader::register('JHtmlHwdIcon', JPATH_COMPONENT . '/helpers/icon.php');
                JLoader::register('JHtmlHwdDropdown', JPATH_COMPONENT . '/helpers/dropdown.php');
                JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');
                JLoader::register('hwdMediaShareHelperModule', JPATH_COMPONENT . '/helpers/module.php');  
                
                // Import HWD libraries.                
                hwdMediaShareFactory::load('files');
                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('media');
		hwdMediaShareFactory::load('utilities');
                hwdMediaShareFactory::load('activities'); 
                hwdMediaShareFactory::load('mobile'); 
                hwdMediaShareFactory::load('thumbnails'); 
                
                // Set JavaScript variables.
                hwdMediaShareHelperNavigation::setJavascriptVars();
                
                $this->utilities = hwdMediaShareUtilities::getInstance();
                $this->mobile = hwdMediaShareMobile::getInstance();
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));
                $this->columns = $this->params->get('list_columns', 3);
                $this->return = base64_encode(JFactory::getURI()->toString());
                $this->display = $this->state->get('media.display', 'details');
                $this->media_tab_modules = $this->document->countModules('media-tabs') ? true : false;     

                // Check for errors.
                if (count($errors = $this->get('Errors')))
                {
                        JError::raiseError(500, implode('<br />', $errors));
                        return false;
                }

                // Check for age restrictions.
		if (isset($this->item->agerestricted))
		{
			$this->assign('dob', $app->getUserState( "media.dob" ));
                        $tpl = 'dob';                        
		}
                
                // Check for password protection.
		if (isset($this->item->passwordprotected))
		{
			$tpl = 'password';                        
		}
                
                $model = $this->getModel();
		$model->hit();

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
                JHtml::_('bootstrap.framework');
                $this->document->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/hwd.css');
                if ($this->params->get('load_joomla_css') != 0) $this->document->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/joomla.css');
                if ($this->params->get('list_thumbnail_aspect') != 0) $this->document->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/aspect.css');
                if ($this->params->get('list_thumbnail_aspect') != 0) $this->document->addScript(JURI::base( true ).'/media/com_hwdmediashare/assets/javascript/aspect.js');

                // Add JavaScript assets.                
                $this->document->addScript(JURI::base( true ).'/media/com_hwdmediashare/assets/javascript/hwd.min.js');
                
                // Add open graph tags (facebook support).
                hwdMediaShareFactory::load('opengraph.opengraph');
                $HWDopengraph = hwdMediaShareOpenGraph::getInstance();
                $HWDopengraph->get($this->item);

		// Define the page title and headings. 
		$menu = $menus->getActive();
		if ($menu)
		{
                        $title = $this->params->get('page_title');
                        $heading = $this->params->get('page_heading', JText::_('COM_HWDMS_MEDIA'));
		}
		else
		{
                        $title = JText::_('COM_HWDMS_ALBUM');
                        $heading = JText::_('COM_HWDMS_ALBUM');
		}
                
		// If the menu item does not concern this view then add a breadcrumb.
		if ($menu && ($menu->query['option'] != 'com_hwdmediashare' || $menu->query['view'] != 'mediaitem' || (int) @$menu->query['id'] != $this->item->id))
		{
			// Reset title and heading if menu item doesn't point 
                        // directly to this item.
			if ($this->item->title) 
                        {
				$title = $this->item->title;
                                $heading = $this->item->title;                           
			}      
                        
                        // Breadcrumb support.
			$path = array(array('title' => $this->item->title, 'link' => ''));

                        /**
                         * Category breadcrumb support.
                         * 
                         * We check if the media is associated with one category, then we add 
                         * category breadcrumbs. However, we also check if the menu item is 
                         * associated with that the category, in which case we don't include 
                         * additional breadcrumbs because the Joomla menu breadcrumbs are likely 
                         * to be sufficient. This is difficult to predict. 
                         */
                        if (count($this->item->categories) == 1)
                        {
                                $category = JCategories::getInstance('hwdMediaShare')->get(reset($this->item->categories)->id);        
                                if ($category && $menu->query['view'] == 'mediaitem' || $menu->query['view'] == 'media')
                                {
                                        while ($category)
                                        {
                                                
                                                $path[] = array('title' => $category->title, 'link' => hwdMediaShareHelperRoute::getCategoryRoute($category->id));
                                                $category = $category->getParent();
                                        }
                                        
                                        // Remove the last element, which will be the ROOT category.
                                        array_pop($path);                                         
                                }
                        }

			$path = array_reverse($path);
			foreach($path as $item)
			{
				$pathway->addItem($item['title'], $item['link']);
			}                    
		}
                
		// Redefine the page title and headings. 
                $this->params->set('page_title', $title);
                $this->params->set('page_heading', $heading); 
                
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
		if (empty($title))
		{
			$title = $this->item->title;
		}
                
                // Set metadata.
		$this->document->setTitle($title);

                if ($this->item->params->get('meta_desc'))
		{
			$this->document->setDescription($this->item->params->get('meta_desc'));
		}
                elseif ($this->item->description)
                {                        
			$this->document->setDescription($this->escape(JHtmlString::truncate($this->item->description, 160, true, false)));   
                }                 
                elseif ($this->params->get('meta_desc'))
                {
			$this->document->setDescription($this->params->get('meta_desc'));
                }                

		if ($this->item->params->get('meta_keys'))
		{
			$this->document->setMetadata('keywords', $this->item->params->get('meta_keys'));
		}
                elseif ($this->params->get('meta_keys'))
                {
			$this->document->setMetadata('keywords', $this->params->get('meta_keys'));
                }

		if ($this->item->params->get('meta_rights'))
		{
			$this->document->setMetadata('keywords', $this->item->params->get('meta_rights'));
		}
                elseif ($this->params->get('meta_rights'))
                {
			$this->document->setMetadata('copyright', $this->params->get('meta_rights'));
                }             
                
		if ($this->item->params->get('meta_author') == 1 && isset($this->item->author))
		{
			$this->document->setMetadata('author', $this->item->author);
		}
                elseif ($this->params->get('meta_author') == 1 && isset($this->item->author))
                {
			$this->document->setMetadata('author', $this->item->author);
                }      
	}

	/**
	 * Method to check if current item has any downloads.
	 *
         * @access  public
	 * @return  boolean True if downloads, false if none.
	 */
	public function hasDownloads()
	{
                if ($this->item->type == 1 || $this->item->type == 5 || $this->item->type == 7)
                {
                        if (count($this->item->mediafiles))
                        {
                                return true;
                        }
                }
                
                return false;
	}
        
	/**
	 * Method to check if current item has meta potential.
	 *
         * @access  public
	 * @return  boolean True if downloads, false if none.
	 */
	public function hasMeta()
	{
                if ($this->item->type == 1 && ($this->item->media_type == 1 || $this->item->media_type == 3 || $this->item->media_type == 4))
                {
                        return true;
                }
                
                return false;
	}
        
	/**
	 * Method to check and return video qualities.
	 *
         * @access  public
	 * @return  mixed   Array of available qualities or false if none.
	 */
	public function hasQualities()
	{
                if ($this->item->media_type == 4 && ($this->item->type == 1 || $this->item->type == 5))
                {
                        if (count($this->item->mediafiles))
                        {
                                $types = array();
                                foreach($this->item->mediafiles as $file)
                                {
                                        $types[] = $file->file_type;
                                }  

                                $qualities = array();
                                if (array_intersect(array(11), $types)) {
                                    $qualities[] = '240';
                                }
                                if (array_intersect(array(12, 14, 18, 22), $types)) {
                                    $qualities[] = '360';
                                }
                                if (array_intersect(array(13, 15, 19, 23), $types)) {
                                    $qualities[] = '480';
                                }     
                                if (array_intersect(array(16, 20, 24), $types)) {
                                    $qualities[] = '720';
                                }     
                                if (array_intersect(array(17, 21, 25), $types)) {
                                    $qualities[] = '1080';
                                }    

                                return $qualities;
                        }
                }
                
                return false;
	}  

	/**
	 * Prepares the commenting framework.
	 *
         * @access  public
         * @param   object  $media  The media object.
	 * @return  string  The markup to show the commenting framework.
	 */
	public function getComments($media)
	{
                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // Load HWD utilities.
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();

                $pluginClass = 'plgHwdmediashare'.$config->get('commenting');
                $pluginPath = JPATH_ROOT.'/plugins/hwdmediashare/'.$config->get('commenting').'/'.$config->get('commenting').'.php';
                if (file_exists($pluginPath))
                {
                        JLoader::register($pluginClass, $pluginPath);
                        $HWDcomments = call_user_func(array($pluginClass, 'getInstance'));
                        if ($comments = $HWDcomments->getComments($media))
                        {
                                return $comments;
                        }
                        else
                        {
                                return $utilities->printNotice($HWDcomments->getError());
                        }
                }
	} 
}