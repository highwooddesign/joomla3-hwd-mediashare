<?php
/**
 * @version    SVN $Id: view.html.php 277 2012-03-28 10:03:31Z dhorsfall $
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
class hwdMediaShareViewMaintenance extends JViewLegacy {
	/**
	 * display method of Hello view
	 * @return void
	 */
	public function display($tpl = null)
	{
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		// Set the toolbar
		$this->addToolBar();

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
		//JRequest::setVar('hidemainmenu', true);

		JToolBarHelper::title(JText::_('COM_HWDMS_MAINTENANCE'), 'hwdmediashare');

		JToolBarHelper::custom('maintenace.run', 'apply.png', 'apply.png', 'COM_HWDMS_RUN', false);
	}
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument()
	{
		$document = JFactory::getDocument();
                $document->addScript(JURI::root() . "/administrator/components/com_hwdmediashare/views/maintenance/submitbutton.js");
		$document->addStyleSheet(JURI::root() . "media/com_hwdmediashare/assets/css/administrator.css");
	}
}
