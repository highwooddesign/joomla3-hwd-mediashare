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

class hwdMediaShareViewGroupForm extends JViewLegacy
{
    	protected $state;

	protected $item;

	protected $form;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
        public function display($tpl = null)
	{
                // Get data from the model.
		$this->state = $this->get('State');
		$this->item = $this->get('Item');
		$this->form = $this->get('Form');
                $this->return_page = $this->get('ReturnPage');
		$this->params = $this->state->params;

                // Load libraries.
                JLoader::register('JHtmlHwdIcon', JPATH_COMPONENT . '/helpers/icon.php');
                JLoader::register('JHtmlHwdDropdown', JPATH_COMPONENT . '/helpers/dropdown.php');
                JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');
                hwdMediaShareFactory::load('files');
                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('media');
		hwdMediaShareFactory::load('utilities');
                
                $this->utilities = hwdMediaShareUtilities::getInstance();
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));
                $this->isNew = $this->item->id == 0;
                
		// Initialise variables.
		$user = JFactory::getUser();
                
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
	 * Prepares the document
	 *
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
                
		if ($this->isNew)
		{
			$this->params->set('page_heading', JText::_('COM_HWDMS_NEW_GROUP'));
		}
		else
		{
			$this->params->set('page_heading', JText::sprintf('COM_HWDMS_EDIT_GROUPX', $this->escape($this->item->title)));
		}
                
		$this->document->setTitle($this->params->get('page_heading'));  
	}

	/**
	 * Display the report view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function report($tpl = null)
	{
		// Initialise variables.
		$user = JFactory::getUser();
                            
                if (!$user->authorise('hwdmediashare.report', 'com_hwdmediashare'))
                {
                        hwdMediaShareFactory::load('utilities');
                        $utilities = hwdMediaShareUtilities::getInstance();
                        $utilities->printModalNotice('COM_HWDMS_NOTICE_NO_FEATURE_ACCESS', 'COM_HWDMS_NOTICE_NO_FEATURE_ACCESS_DESC'); 
                        return;
                }
                
                // Get data from the model.
                $this->form = $this->get('ReportForm');
                $this->id = JFactory::getApplication()->input->get('id', '', 'int');

		// Display the template.
		parent::display('report');                
	}
}
