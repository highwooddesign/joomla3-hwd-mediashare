<?php
/**
 * @version    SVN $Id: view.html.php 938 2013-01-25 15:52:30Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * hwdMediaShare View
 */
class hwdMediaShareViewConfiguration extends JViewLegacy {
	/**
	 * Method to display the view.
	 */
	public function display($tpl = null)
	{
                $hwdms = hwdMediaShareFactory::getInstance();          
                $config = $hwdms->getConfig();

                // get the Data
		$form = $this->get('Form');
		$script = $this->get('Script');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

                // Bind the form to the data.
		if ($form && $config)
                {
			$form->bind($config);
		}

                // Assign the Data
		$this->form = $form;
		$this->config = $config;
		$this->script = $script;

		// Set the toolbar
		$this->addToolBar();
		$this->setSubMenu();

		// Display the template
		parent::display($tpl);

		// Set the document
		$this->setDocument();
	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar()
	{
		JToolBarHelper::title(JText::_('COM_HWDMS_CONFIGURATION'), 'hwdmediashare');

                // We can save the new record
                JToolBarHelper::apply('configuration.apply');
                JToolBarHelper::save('configuration.save');
                JToolBarHelper::divider();
                JToolBarHelper::cancel('configuration.cancel');
                JToolBarHelper::divider();
                JToolBarHelper::help('JHELP_SITE_GLOBAL_CONFIGURATION');
	}
        
	/**
	 * Setup the SubMenu
	 *
	 * @since	1.6
	 */
	protected function setSubMenu()
	{
                // Load submenu template, using element id 'submenu' as needed by behavior.switcher
		$contents = $this->loadTemplate('navigation');
		$document = JFactory::getDocument();
		$document->setBuffer($contents, 'modules', 'submenu');
	}        

	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument()
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_HWDMS_HWDMEDIASHARE').' '.JText::_('COM_HWDMS_CONFIGURATION'));
		$document->addScript(JURI::root() . $this->script);
		// Prevent user end javascript submission as it generally causes significnat delay
                //$document->addScript(JURI::root() . "/administrator/components/com_hwdmediashare/views/".JRequest::getCmd('view')."/submitbutton.js");
		$document->addStyleSheet(JURI::root() . "media/com_hwdmediashare/assets/css/administrator.css");
                JText::script('COM_HWDMS_ERROR_UNACCEPTABLE');
	}
}