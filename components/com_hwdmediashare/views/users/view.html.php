<?php
/**
 * @version    SVN $Id: view.html.php 1205 2013-02-28 10:48:39Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      14-Nov-2011 20:56:50
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla view library
jimport('joomla.application.component.view');

/**
 * HTML View class for the hwdMediaShare Component
 */
class hwdMediaShareViewUsers extends JViewLegacy {
    	protected $view_item = 'user';
        protected $elementType = 5;
        protected $elementName = 'User'; 
        
	// Overwriting JView display method
	function display($tpl = null)
	{
                // Get data from the model
                $items = $this->get('Items');
                $pagination = $this->get('Pagination');
		$state	= $this->get('State');

                // Download links
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
                        JFactory::getApplication()->enqueueMessage( JText::_('COM_HWDMS_NOTICE_NO_USER_CHANNELS') );
                }
                
		if ($items === false)
		{
			JError::raiseError(404, JText::_('COM_HWDMS_ERROR'));
			return false;
		}

                $params = &$state->params;

                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

                $this->assign('columns',	        $params->get('list_columns', 3));
                $this->assign('display',		$state->get('media.display'));                
                $this->assign('return',                 base64_encode(JFactory::getURI()->toString()));
                
                $this->assignRef('items',		$items);
                $this->assignRef('pagination',		$pagination);
                $this->assignRef('state',		$state);
                $this->assignRef('params',		$params);

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
		$title	= null;

                $this->document->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/hwd.css');
                if ($this->state->params->get('load_joomla_css') != 0) $this->document->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/joomla.css');

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
                else
                {
			$this->params->def('page_heading', JText::_('COM_HWDMS_USER_CHANNELS'));
		}
		$title = $this->params->get('page_title', '');
		if (empty($title))
                {
			$title = JText::_('COM_HWDMS_USER_CHANNELS');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1)
                {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2)
                {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
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
         	
                if ($this->params->get('meta_author'))
		{
			//$this->document->setMetadata('author', $this->params->get('meta_author'));
		}       
	}
}
