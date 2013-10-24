<?php
/**
 * @version    SVN $Id: view.html.php 1482 2013-04-30 11:09:51Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Aug-2012 10:34:45
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla view library
jimport('joomla.application.component.view');

/**
 * HTML View class for the hwdMediaShare Component
 */
class hwdMediaShareViewSlideshow extends JViewLegacy {
        // Overwriting JView display method
	function display($tpl = null)
	{
                $app = & JFactory::getApplication();

                $mobile = & hwdMediaShareHelperMobile::getInstance();

                // Get the Data
                $items = $this->get('Items');               
		$item = $this->get('Item');
		$element = $this->get('Element');
		$script = $this->get('Script');
                $state = $this->get('State');

                // Download links
                hwdMediaShareFactory::load('files');
                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('media');
                hwdMediaShareFactory::load('utilities');
                hwdMediaShareFactory::load('recaptcha.recaptchalib');
                JLoader::register('JHtmlHwdIcon', JPATH_COMPONENT . '/helpers/icon.php');
                JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');
                JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');
                hwdMediaShareHelperNavigation::setJavascriptVars();
                
                // Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

                // Check for errors.
		if (isset($item->agerestricted))
		{
			$this->assign('dob', JFactory::getApplication()->getUserState( "media.dob" ));
                        $tpl = 'dob';                        
		}
                
                // Check for errors.
		if (isset($item->passwordprotected))
		{
			$tpl = 'password';                        
		}
                
		$params = &$state->params;

		// Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

                $this->assign('return',                 base64_encode(JFactory::getURI()->toString()));
                $this->assign('mobile',                 $mobile);
                $this->assign('columns',	        $params->get('list_columns', 3));
                $this->assign('searchword',             $state->get('related.searchword'));     
                
                $this->assignRef('params',		$params);
                $this->assignRef('items',		$items);         
		$this->assignRef('item',		$item);
		$this->assignRef('element',		$element);
		$this->assignRef('state',		$state);
                $this->assignRef('utilities',		hwdMediaShareUtilities::getInstance());

                $model = $this->getModel();
		$model->hit();

		$this->_prepareDocument();            
                
                // Display the view
                //parent::display($tpl);
                parent::display('fullscreen');
	}
	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app	= JFactory::getApplication();
		$menus	= $app->getMenu();
		$pathway = $app->getPathway();
		$title = null;

                $this->document->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/hwd.css');
                $this->document->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/slideshow.css');
                if ($this->state->params->get('load_joomla_css') != 0) $this->document->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/joomla.css');

                $this->document->addScript(JURI::base( true ).'/media/com_hwdmediashare/assets/javascript/hwd.min.js');
                $this->document->addCustomTag('<meta property="og:image" content="'.JRoute::_(hwdMediaShareDownloads::thumbnail($this->item)).'"/>');
                $this->document->addCustomTag('<link rel="image_src" href="'.JRoute::_(hwdMediaShareDownloads::thumbnail($this->item)).'" />');

                // Load carousel files
                $this->document->addScript(JURI::base( true ).'/media/com_hwdmediashare/assets/javascript/Carousel.js');
                $this->document->addScript(JURI::base( true ).'/media/com_hwdmediashare/assets/javascript/Carousel.Extra.js');
                $this->document->addScript(JURI::base( true ).'/media/com_hwdmediashare/assets/javascript/Carousel.Rotate3D.js');
                $this->document->addScript(JURI::base( true ).'/media/com_hwdmediashare/assets/javascript/PeriodicalExecuter.js');

                // Declare the key to load the carousel at the open media item
                for ($i=0, $n=count($this->items); $i < $n; $i++)
                {
                        $item = $this->items[$i];
                        if ($item->id == $this->state->get('media.id'))
                        {
                                $this->document->addScriptDeclaration( "var key = ".$i.";" );
                                break;
                        }
                }
                
                if(!isset($this->item->media_type))
                {
                        $this->item->media_type = hwdMediaShareMedia::loadMediaType($this->item);
                }

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', JText::_('COM_HWDMS_MEDIA'));
		}

		$title = $this->params->get('page_title', '');

		$id = (int) @$menu->query['id'];

		// If the menu item does not concern this item
		if ($menu && ($menu->query['option'] != 'com_hwdmediashare' || $menu->query['view'] != 'mediaitem' || $id != $this->item->id))
		{
			// If this is not a single item menu item, set the page title to the item title
			if ($this->item->title) 
                        {
				$title = $this->item->title;
			}      
                        
                        // Breadcrumb support
			$path = array(array('title' => $this->item->title, 'link' => ''));
                        
                        // Category breadcrumb support
	/** 
	 * Comment out due to error
                        if (count($this->item->categories) == 1)
                        {
                                // Load JCategories
                                jimport('joomla.application.categories');
                                $category = JCategories::getInstance('hwdMediaShare')->get($this->item->categories[0]->id);                                
                                while ($category && ($menu->query['option'] != 'com_hwdmediashare' || $menu->query['view'] == 'mediaitem' || $id != $category->id) && $category->id > 1)
                                {
                                        $path[] = array('title' => $category->title, 'link' => hwdMediaShareHelperRoute::getCategoryRoute($category->id));
                                        $category = $category->getParent();
                                }
                        }
	 */                        
     
			$path = array_reverse($path);
			foreach($path as $item)
			{
				$pathway->addItem($item['title'], $item['link']);
			}                    
		}

		// Check for empty title and add site name if param is set
		if (empty($title)) {
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		if (empty($title)) {
			$title = $this->item->title;
		}
		$this->document->setTitle($title);

		if (isset($this->item->params) && $this->item->params->get('meta_desc'))
		{
			$this->document->setDescription($this->item->params->get('meta_desc'));
		}
                elseif ($this->params->get('meta_desc'))
                {
			$this->document->setDescription($this->params->get('meta_desc'));
                }
                else
                {                        
			$this->document->setDescription($this->escape(JHtmlString::truncate($this->item->description, $this->params->get('list_desc_truncate'), true, false)));   
                }                

		if (isset($this->item->params) && $this->item->params->get('meta_keys'))
		{
			$this->document->setMetadata('keywords', $this->item->params->get('meta_keys'));
		}
                elseif ($this->params->get('meta_keys'))
                {
			$this->document->setMetadata('keywords', $this->params->get('meta_keys'));
                }

		if (isset($this->item->params) && $this->item->params->get('meta_rights'))
		{
			$this->document->setMetadata('keywords', $this->item->params->get('meta_rights'));
		}
                elseif ($this->params->get('meta_rights'))
                {
			$this->document->setMetadata('keywords', $this->params->get('meta_rights'));
                }             
                
		if (isset($this->item->params) && $this->item->params->get('meta_author') == 1)
		{
			$this->document->setMetadata('author', $this->item->author);
		}
                elseif ($this->params->get('meta_author') == 1)
                {
			$this->document->setMetadata('author', $this->item->author);
                }  
	}         
}