<?php
/**
 * @version    SVN $Id: view.html.php 750 2012-10-31 17:12:29Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Mar-2012 21:25:29
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * hwdMediaShare View
 */
class hwdMediaShareViewFiles extends JViewLegacy {
	var $name = "files";
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
		JToolBarHelper::title(JText::_('COM_HWDMS_FILES'), 'hwdmediashare');

		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete'))
                {
                        JToolBarHelper::deleteList('', 'files.delete', 'JTOOLBAR_EMPTY_TRASH');
                        JToolBarHelper::divider();
                }
		else if ($canDo->get('core.edit.state'))
                {
                        JToolBarHelper::trash('files.trash');
                        JToolBarHelper::divider();
		}
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
		$document->setTitle(JText::_('COM_HWDMS_HWDMEDIASHARE').' '.JText::_('COM_HWDMS_FILES'));
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
	/**
	 * Method to get the publish status HTML
	 *
	 * @param	object	Field object
	 * @param	string	Type of the field
	 * @param	string	The ajax task that it should call
	 * @return	string	HTML source
	 **/
	public function getPath( &$item )
	{
                hwdMediaShareFactory::load('files');
                hwdMediaShareFiles::getLocalStoragePath();
                $folders = hwdMediaShareFiles::getFolders($item->key);
                $filename = hwdMediaShareFiles::getFilename($item->key, $item->file_type);
                $ext = hwdMediaShareFiles::getExtension($item, $item->file_type);
                $path = hwdMediaShareFiles::getPath($folders, $filename, $ext);
                return str_replace(JPATH_SITE, '', $path);
	}
	/**
	 * Method to get the publish status HTML
	 *
	 * @param	object	Field object
	 * @param	string	Type of the field
	 * @param	string	The ajax task that it should call
	 * @return	string	HTML source
	 **/
	public function getExtension( &$item )
	{
                hwdMediaShareFactory::load('files');
                return hwdMediaShareFiles::getExtension($item, $item->file_type);
	}
}
