<?php
/**
 * @version    SVN $Id: view.html.php 459 2012-08-13 12:58:37Z dhorsfall $
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
class hwdMediaShareViewProcesses extends JViewLegacy {
        /**
	 * display method of Hello view
	 * @return void
	 */
	function display($tpl = null)
	{
                // Get data from the model
                $items = $this->get('Items');
                $pagination = $this->get('Pagination');
		$state	= $this->get('State');
		$successful = $this->get('Successful');
		$unnecessary = $this->get('Unnecessary');

                // Check for errors.
                if (count($errors = $this->get('Errors')))
                {
                        JError::raiseError(500, implode('<br />', $errors));
                        return false;
                }

                // Assign data to the view
                $this->items = $items;
                $this->pagination = $pagination;
                $this->state = $state;
                $this->successful = $successful;
                $this->unnecessary = $unnecessary;

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
		$canDo = hwdMediaShareHelper::getActions();
		JToolBarHelper::title(JText::_('COM_HWDMS_PROCESSES'), 'hwdmediashare');

                // Sample data install option
                $document = JFactory::getDocument();
                $document->addStyleDeclaration('.icon-32-process {background-image: url(../media/com_hwdmediashare/assets/images/icons/32/process.png);}');
                JToolBarHelper::custom('process.run', 'process.png', 'process_f2.png', JText::_('COM_HWDMS_PROCESS'), true);

                $document = JFactory::getDocument();
                $document->addStyleDeclaration('.icon-32-process-all {background-image: url(../media/com_hwdmediashare/assets/images/icons/32/process-all.png);}');
                JToolBarHelper::custom('process.all', 'process-all.png', 'process-all_f2.png', JText::_('COM_HWDMS_PROCESS_ALL'), false);
                
		if ($canDo->get('core.edit.state'))
                {
			JToolBarHelper::divider();
                        JToolBarHelper::custom('processes.reset','remove.png','remove_f2.png','COM_HWDMS_RESET', true);
                        JToolBarHelper::custom('processes.resetall','remove.png','remove_f2.png','COM_HWDMS_RESET_ALL', true);
			JToolBarHelper::divider();
                        JToolBarHelper::checkin('processes.checkin');
		}

                if ($canDo->get('core.delete'))
		{
			JToolBarHelper::divider();
                        JToolBarHelper::deleteList('', 'processes.delete');
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
		$document = JFactory::getDocument();
                $document->addScript(JURI::root() . "/administrator/components/com_hwdmediashare/views/".JRequest::getCmd('view')."/submitbutton.js");
		$document->addStyleSheet(JURI::root() . "media/com_hwdmediashare/assets/css/administrator.css");
                $document->setTitle(JText::_('COM_HWDMS_HWDMEDIASHARE').' '.JText::_('COM_HWDMS_PROCESSES'));
        }
	/**
	 * Method to get the publish status HTML
	 *
	 * @param	object	Field object
	 * @param	string	Type of the field
	 * @param	string	The ajax task that it should call
	 * @return	string	HTML source
	 **/
	public function getProcessType( &$item )
	{
                hwdMediaShareFactory::load('processes');
                return hwdMediaShareProcesses::getType($item);
	}
	/**
	 * Method to get the publish status HTML
	 *
	 * @param	object	Field object
	 * @param	string	Type of the field
	 * @param	string	The ajax task that it should call
	 * @return	string	HTML source
	 **/
	public function getStatus( &$item )
	{
                hwdMediaShareFactory::load('processes');
                return hwdMediaShareProcesses::getStatus($item);
	}
}
