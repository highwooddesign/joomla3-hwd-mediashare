<?php
/**
 * @version    SVN $Id: view.html.php 493 2012-08-28 13:20:17Z dhorsfall $
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
class hwdMediaShareViewExtensions extends JViewLegacy {
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
		JToolBarHelper::title(JText::_('COM_HWDMS_FILE_EXTENSIONS'), 'hwdmediashare');

                if ($canDo->get('core.create'))
		{
			JToolBarHelper::addNew('extension.add');
		}
		if ($canDo->get('core.edit'))
		{
			JToolBarHelper::editList('extension.edit');
		}
		if ($canDo->get('core.edit.state'))
                {
			JToolBarHelper::divider();
			JToolBarHelper::publish('extensions.publish', 'JTOOLBAR_PUBLISH', true);
			JToolBarHelper::unpublish('extensions.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			//JToolBarHelper::custom('extensions.featured', 'featured.png', 'featured_f2.png', 'JFEATURED', true);
			JToolBarHelper::divider();
			JToolBarHelper::archiveList('extensions.archive');
			JToolBarHelper::checkin('extensions.checkin');
		}
		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete'))
                {
			JToolBarHelper::divider();
                        JToolBarHelper::deleteList('', 'extensions.delete', 'JTOOLBAR_EMPTY_TRASH');
                        JToolBarHelper::divider();
                }
		else if ($canDo->get('core.edit.state'))
                {
			JToolBarHelper::divider();
                        JToolBarHelper::trash('extensions.trash');
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
		$document->addScript(JURI::root() . "/administrator/components/com_hwdmediashare/views/extensions/submitbutton.js");
		$document->setTitle(JText::_('COM_HWDMS_HWDMEDIASHARE').' '.JText::_('COM_HWDMS_FILE_EXTENSIONS'));
	}
        
	/**
	 * Method to get the publish status HTML
	 *
	 * @param	object	Field object
	 * @param	string	Type of the field
	 * @param	string	The ajax task that it should call
	 * @return	string	HTML source
	 **/
	public function getMediaType( &$item )
	{
                hwdMediaShareFactory::load('media');
                return hwdMediaShareMedia::getMediaType($item);
	}
}
