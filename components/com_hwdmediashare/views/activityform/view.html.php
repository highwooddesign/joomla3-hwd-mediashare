<?php
/**
 * @version    SVN $Id: view.html.php 1560 2013-06-13 09:59:18Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      10-Jan-2012 11:04:04
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla view library
jimport('joomla.application.component.view');

/**
 * HTML View class for the hwdMediaShare Component
 */
class hwdMediaShareViewActivityForm extends JViewLegacy {
	protected $form;
	protected $item;
	protected $return_page;
	protected $state;

	public function display($tpl = null)
	{
		// Initialise variables.
		$app		= JFactory::getApplication();
		$user		= JFactory::getUser();

		// Get model data.
		$this->state		= $this->get('State');
                $this->item		= $this->get('Item');
		$this->form		= $this->get('Form');
		$this->return_page	= $this->get('ReturnPage');
                
                $isNew = $this->item->id == 0;
                
		if (empty($this->item->id)) {
			$authorised = $user->authorise('core.create', 'com_hwdmediashare');
		}
		else {
			$authorised = $this->item->params->get('access-edit');
		}

		if ($authorised !== true) {
			JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
			return false;
		}

		if (!empty($this->item)) {
			$this->form->bind($this->item);
		}

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseWarning(500, implode("\n", $errors));
			return false;
		}

		// Create a shortcut to the parameters.
		$params	= &$this->state->params;

		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

		$this->params	= $params;
		$this->user		= $user;
                $this->isNew		= $isNew;
                
		if ($this->params->get('enable_category') == 1) {
			$catid = JRequest::getInt('catid');
			$category = JCategories::getInstance('Content')->get($this->params->get('catid', 1));
			$this->category_title = $category->title;
		}

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
			$this->params->def('page_heading', JText::_('COM_HWDMS_ACTIVITY'));
		}
		$title = $this->params->get('page_title', '');
		if (empty($title))
                {
			$title = JText::_('COM_HWDMS_ACTIVITY');
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

        // Overwriting JView display method
	function report($tpl = null)
	{
                if (!JFactory::getUser()->authorise('hwdmediashare.report','com_hwdmediashare'))
                {
                        hwdMediaShareFactory::load('utilities');
                        $utilities = hwdMediaShareUtilities::getInstance();
                        $utilities->printModalNotice('COM_HWDMS_NOTICE_NO_FEATURE_ACCESS', 'COM_HWDMS_NOTICE_NO_FEATURE_ACCESS_DESC'); 
                        return;
                }
                
                $form = $this->get('ReportForm');
                $this->assignRef('form', $form);
                $this->assign('id', JRequest::getInt( 'id' ));
                
                // Display the view
                parent::display('report');
	}
        

        // Overwriting JView display method
	function reply($tpl = null)
	{
                $form = $this->get('ReplyForm');
                $this->assignRef('form', $form);
                $this->assign('element_type', JRequest::getInt( 'element_type' ));
                $this->assign('element_id', JRequest::getInt( 'element_id' ));
                $this->assign('reply_id', JRequest::getInt( 'reply_id' ));
                
                // Display the view
                parent::display('reply');
	}       
}
