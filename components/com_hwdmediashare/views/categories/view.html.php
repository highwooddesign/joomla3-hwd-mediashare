<?php
/**
 * @version    SVN $Id: view.html.php 1215 2013-03-05 13:30:52Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla view library
jimport('joomla.application.component.view');

/**
 * HTML View class for the hwdMediaShare Component
 */
class hwdMediaShareViewCategories extends JViewLegacy {
	protected $state = null;
	protected $item = null;
	protected $items = null;
        protected $view_item = 'category';
        protected $elementType = 6;
        protected $elementName = 'Category';

        /**
	 * Display the view
	 *
	 * @return	mixed	False on error, null otherwise.
	 */
	function display($tpl = null)
	{
                // Get data from the model
                $items = $this->get('Items');
		$state = $this->get('State');
                $parent = $this->get('Parent');

                // Download links
                hwdMediaShareFactory::load('media');
                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('files');
                JLoader::register('JHtmlHwdIcon', JPATH_COMPONENT . '/helpers/icon.php');
                JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
                {
			JError::raiseWarning(500, implode("\n", $errors));
			return false;
		}
                
		if (count($items) == 0)
		{
                        JFactory::getApplication()->enqueueMessage( JText::_('COM_HWDMS_NOTICE_NO_CATEGORIES') );
		}
                
		if ($items === false)
		{
			JError::raiseError(404, JText::_('COM_HWDMS_ERROR'));
			return false;
		}

                if ($parent == false)
		{
			return JError::raiseError(404, JText::_('COM_HWDMS_ERROR_CATEGORY_NOT_FOUND'));
		}

                $params = &$state->params;

                $items = array($parent->id => $items);

		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

		$this->assign('maxLevelcat',	        $params->get('maxLevelcat', -1));
                $this->assign('columns',	        $params->get('list_columns', 3));
                $this->assign('display',		$state->get('media.category-display'));                

                $this->assignRef('items',		$items);
                $this->assignRef('state',		$state);
                $this->assignRef('params',		$params);
                $this->assignRef('parent',		$parent);
                $this->assignRef('media',		$media);

		$this->_prepareDocument();

		parent::display($tpl);
	}
	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app	= JFactory::getApplication();
                $menus	= $app->getMenu();
		$pathway = $app->getPathway();
		$title	= null;

                $this->document->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/hwd.css');
                if ($this->state->params->get('load_joomla_css') != 0) $this->document->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/joomla.css');
                if ($this->state->params->get('list_thumbnail_aspect') != 0) $this->document->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/aspect.css');
                if ($this->state->params->get('list_thumbnail_aspect') != 0) $this->document->addScript(JURI::base( true ).'/media/com_hwdmediashare/assets/javascript/aspect.js');

                $this->document->addScript(JURI::base( true ).'/media/com_hwdmediashare/assets/javascript/ToolTip.js');
           
		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', JText::_('COM_HWDMS_CATEGORIES'));
		}

		$title = $this->params->get('page_title', '');

		// If the menu item does not concern this item
		if ($menu && ($menu->query['option'] != 'com_hwdmediashare' || $menu->query['view'] != 'categories'))
		{       
			// If this is not a single item menu item, set the page title to the item title
			$title = JText::_('COM_HWDMS_CATEGORIES');    
                        
                        // Breadcrumb support
			$path = array(array('title' => JText::_('COM_HWDMS_CATEGORIES'), 'link' => ''));
                                                
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
			$title = JText::_('COM_HWDMS_CATEGORIES');
		}
		$this->document->setTitle($title);

                if ($this->params->get('meta_desc'))
                {
			$this->document->setDescription($this->params->get('meta_desc'));
                }              

		if ($this->params->get('meta_keys'))
                {
			$this->document->setMetadata('keywords', $this->params->get('meta_keys'));
                }

		if ($this->params->get('meta_rights'))
                {
			$this->document->setMetadata('copyright', $this->params->get('meta_rights'));
                }    
	}
        
	/**
	 * Method to get the publish status HTML
	 *
	 * @param	object	Field object
	 * @param	string	Type of the field
	 * @param	string	The ajax task that it should call
	 * @return	string	HTML source
	 **/
	public function getCategoryMedia( &$item )
	{
                return $this->get('Media');
	}
}
