<?php
/**
 * @version    SVN $Id: view.html.php 1699 2013-10-17 13:39:07Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      14-Nov-2011 21:02:42
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla view library
jimport('joomla.application.component.view');

/**
 * HTML View class for the hwdMediaShare Component
 */
class hwdMediaShareViewAccount extends JViewLegacy {
	// Overwriting JView display method
	function display($tpl = null)
	{
                $app = & JFactory::getApplication();
                $layout = JRequest::getWord('layout', '');

                if (!empty($layout))
                {
                        $items = $this->get('Items');
                        $pagination = $this->get('Pagination');
                        $form = $this->get('Form');
                        $batch = $this->get('BatchForm');
                }
                else
                {
                         $item = $this->get('Item');               
                }
                
		$state	= $this->get('State');

                // Download links
                hwdMediaShareFactory::load('files');
                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('media');
                JLoader::register('JHtmlHwdIcon', JPATH_COMPONENT . '/helpers/icon.php');
                JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		$params = &$state->params;

		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

                $this->assign('columns',	        $params->get('list_columns', 3));
                $this->assign('display',		$state->get('media.display'));                
                $this->assign('return',                 base64_encode(JFactory::getURI()->toString()));
                
                $this->assignRef('item',		$item);

                $this->assignRef('items',		$items);
                $this->assignRef('pagination',		$pagination);
                $this->assignRef('form',		$form);
                $this->assignRef('batch',		$batch);
                
                $this->assignRef('state',		$state);
                $this->assignRef('params',		$params);
                
		$this->_prepareDocument();

                // Display the view
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
			$this->params->def('page_heading', JText::_('COM_HWDMS_MY_ACCOUNT'));
		}
		$title = $this->params->get('page_title', '');
		if (empty($title))
                {
			$title = JText::_('COM_HWDMS_MY_ACCOUNT');
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
	}    
}