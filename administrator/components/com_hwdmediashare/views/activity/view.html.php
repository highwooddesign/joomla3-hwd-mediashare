<?php
/**
 * @version    SVN $Id: view.html.php 204 2012-02-23 15:26:26Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      26-Oct-2011 10:32:50
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * hwdMediaShare View
 */
class hwdMediaShareViewActivity extends JViewLegacy {
	/**
	 * display method of Hello view
	 * @return void
	 */
	public function display($tpl = null)
	{
                // get the Data
		$form = $this->get('Form');
                $item = $this->get('Item');
		$script = $this->get('Script');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		// Assign the Data
		$this->form = $form;
		$this->item = $item;
		$this->script = $script;

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
		JRequest::setVar('hidemainmenu', true);
		$user = JFactory::getUser();
		$userId = $user->id;
		$isNew = $this->item->id == 0;
		$canDo = hwdMediaShareHelper::getActions($this->item->id, 'activity');
		JToolBarHelper::title($isNew ? JText::_('COM_HWDMS_NEW_ACTIVITY') : JText::_('COM_HWDMS_EDIT_ACTIVITY'), 'hwdmediashare');
		// Built the actions for new and existing records.
		if ($isNew)
		{
			// For new records, check the create permission.
			if ($canDo->get('core.create'))
			{
				JToolBarHelper::apply('activity.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('activity.save', 'JTOOLBAR_SAVE');
			}
			JToolBarHelper::cancel('activity.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			if ($canDo->get('core.edit'))
			{
				// We can save the new record
				JToolBarHelper::apply('activity.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('activity.save', 'JTOOLBAR_SAVE');
			}
			JToolBarHelper::cancel('activity.cancel', 'JTOOLBAR_CLOSE');
		}
                JToolBarHelper::divider();
                JToolBarHelper::custom('help', 'help.png', 'help.png', 'JHELP', false);
	}
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument()
	{
		$isNew = $this->item->id == 0;
		$document = JFactory::getDocument();
		$document->setTitle($isNew ? JText::_('COM_HWDMS_HWDMEDIASHARE').' '.JText::_('COM_HWDMS_NEW_ACTIVITY') : JText::_('COM_HWDMS_HWDMEDIASHARE').' '.JText::_('COM_HWDMS_EDIT_ACTIVITY'));
		$document->addScript(JURI::root() . $this->script);
		$document->addScript(JURI::root() . "/administrator/components/com_hwdmediashare/views/".JRequest::getCmd('view')."/submitbutton.js");
                JText::script('COM_HWDMS_ERROR_UNACCEPTABLE');
	}
}