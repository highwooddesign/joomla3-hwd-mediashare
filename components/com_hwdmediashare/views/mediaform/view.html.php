<?php
/**
 * @version    SVN $Id: view.html.php 1566 2013-06-13 10:09:26Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      26-Nov-2011 12:06:27
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla view library
jimport('joomla.application.component.view');

/**
 * HTML View class for the hwdMediaShare Component
 */
class hwdMediaShareViewMediaForm extends JViewLegacy {
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
                $this->item			= $this->get('Item');
		$this->form			= $this->get('Form');
		$this->return_page	= $this->get('ReturnPage');
                // Download links
                hwdMediaShareFactory::load('downloads');
                
		if (empty($this->item->id)) {
			$authorised = $user->authorise('core.create', 'com_hwdmediashare');
		}
		else {
			$authorised = $this->item->controls->get('access-edit');
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
		$this->user	= $user;

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
			$this->params->def('page_heading', JText::_('COM_HWDMS_MEDIA'));
		}
		$title = $this->params->get('page_title', '');
		if (empty($title))
                {
			$title = JText::_('COM_HWDMS_MEDIA');
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
	function share($tpl = null)
	{
                hwdMediaShareFactory::load('media');

                $id = JRequest::getInt('id');
                $item = $this->get('Item');
                $link = hwdMediaShareMedia::getPermalink($id);
                $embed_code = hwdMediaShareMedia::getEmbedCode($id);

                hwdMediaShareFactory::load('downloads');

                $this->assign('id', $id);
                $this->assign('link', $link);
                
                // Get model data.
                $form = $this->get('ShareForm');   

                $user = & JFactory::getUser();
                $object = new StdClass;
                $object->permalink = $link;
                $object->embed_code = $embed_code;
   
                $form->bind($object); 
                
                $this->assignRef('form', $form);
                $this->assignRef('item', $item);

                $doc = & JFactory::getDocument();
                $doc->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/hwd.css');
                
                // Display the view
                parent::display('share');
	}
        
	// Overwriting JView display method
	function link($tpl = null)
	{
		$user = JFactory::getUser();
		if (!$user->id) 
                {
                        hwdMediaShareFactory::load('utilities');
                        $utilities = hwdMediaShareUtilities::getInstance();
                        $utilities->printModalNotice('COM_HWDMS_NOTICE_NO_FEATURE_ACCESS', 'COM_HWDMS_NOTICE_NO_FEATURE_ACCESS_DESC'); 
                        return;
		}

                $form = $this->get('LinkForm');
                $item = $this->get('Item');
                $state = $this->get('State');

		$params = &$state->params;
                
                $this->assignRef('item', $item);
                $this->assignRef('form', $form);
                $this->assignRef('params', $params);

                // Display the view
                parent::display('link');
	}

	// Overwriting JView display method
	function meta($tpl = null)
	{
                $app = & JFactory::getApplication();
                
                // get the Data
		$items = $this->get('Meta');
		$script = $this->get('Script');
                $state = $this->get('State');

                // Download links
                hwdMediaShareFactory::load('files');
                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('media');
                JLoader::register('JHtmlHwdIcon', JPATH_COMPONENT . '/helpers/icon.php');

                // Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		$params = &$state->params;

                $this->assign('return',                 base64_encode(JFactory::getURI()->toString()));

                $this->assignRef('params',		$params);
		$this->assignRef('items',		$items);
		$this->assignRef('state',		$state);

		//$this->_prepareDocument();
                $doc =& JFactory::getDocument();
                $doc->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/hwd.css');

                // Display the view
                parent::display('meta');
	}
        
	// Overwriting JView display method
	function download($tpl = null)
	{
                $app = & JFactory::getApplication();
		$user = JFactory::getUser();
                $groups	= $user->getAuthorisedViewLevels();
                
                // Load hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                // Check global download access
                if (!in_array($config->get('default_download'), $groups)) 
                {
                        hwdMediaShareFactory::load('utilities');
                        $utilities = hwdMediaShareUtilities::getInstance();
                        $utilities->printModalNotice('COM_HWDMS_NOTICE_NO_DOWNLOAD_ACCESS', 'COM_HWDMS_NOTICE_NO_DOWNLOAD_ACCESS_DESC'); 
                        return;
		}

		// Get model data.
		$state = $this->get('State');
                $item = $this->get('Item');
		$items = $this->get('Downloads');
		$script = $this->get('Script');
                $state = $this->get('State');

                // Download links
                hwdMediaShareFactory::load('files');
                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('media');
                JLoader::register('JHtmlHwdIcon', JPATH_COMPONENT . '/helpers/icon.php');

                // Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

                // Check download access for specific item (if set)
                if ($item->download > 0 && !in_array($item->download, $groups)) 
                {
                        hwdMediaShareFactory::load('utilities');
                        $utilities = hwdMediaShareUtilities::getInstance();
                        $utilities->printModalNotice('COM_HWDMS_NOTICE_NO_DOWNLOAD_ACCESS', 'COM_HWDMS_NOTICE_NO_DOWNLOAD_ACCESS_DESC'); 
                        return;
		}
                
		$params = &$state->params;

                $this->assign('return',                 base64_encode(JFactory::getURI()->toString()));

                $this->assignRef('params',		$params);
		$this->assignRef('item',		$item);
		$this->assignRef('items',		$items);
		$this->assignRef('state',		$state);

		//$this->_prepareDocument();
                $doc =& JFactory::getDocument();
                $doc->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/hwd.css');

                // Display the view
                parent::display('download');
	}
        
	/**
	 * Method to get the publish status HTML
	 *
	 * @param	object	Field object
	 * @param	string	Type of the field
	 * @param	string	The ajax task that it should call
	 * @return	string	HTML source
	 **/
	public function getFileType( &$item )
	{
                hwdMediaShareFactory::load('files');
                return hwdMediaShareFiles::getFileType($item);
	}
}
