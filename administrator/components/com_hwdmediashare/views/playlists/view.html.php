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
class hwdMediaShareViewPlaylists extends JViewLegacy {
	var $name = "playlists";
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
		JToolBarHelper::title(JText::_('COM_HWDMS_PLAYLISTS'), 'hwdmediashare');

                if ($canDo->get('core.create'))
		{
			JToolBarHelper::addNew('playlist.add');
		}
		if ($canDo->get('core.edit'))
		{
			JToolBarHelper::editList('playlist.edit');
		}
		if ($canDo->get('core.edit.state'))
                {
			JToolBarHelper::divider();
			JToolBarHelper::publish('playlists.publish', 'JTOOLBAR_PUBLISH', true);
			JToolBarHelper::unpublish('playlists.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			JToolBarHelper::custom('playlists.feature', 'featured.png', 'featured_f2.png', 'COM_HWDMS_FEATURE', true);
                        JToolBarHelper::custom('playlists.unfeature','remove.png','remove_f2.png','COM_HWDMS_UNFEATURE', true);
                        JToolBarHelper::divider();
			JToolBarHelper::archiveList('playlists.archive');
			JToolBarHelper::checkin('playlists.checkin');
		}
		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete'))
                {
			JToolBarHelper::divider();
                        JToolBarHelper::deleteList('', 'playlists.delete', 'JTOOLBAR_EMPTY_TRASH');
                        JToolBarHelper::divider();
                }
		else if ($canDo->get('core.edit.state'))
                {
			JToolBarHelper::divider();
                        JToolBarHelper::trash('playlists.trash');
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
		$document->setTitle(JText::_('COM_HWDMS_HWDMEDIASHARE').' '.JText::_('COM_HWDMS_PLAYLISTS'));
	}
	/**
	 * Method to get the publish status HTML
	 *
	 * @param	object	Field object
	 * @param	string	Type of the field
	 * @param	string	The ajax task that it should call
	 * @return	string	HTML source
	 **/
	public function getPublish( &$row, $type, $i )
	{
                $state = $row->$type ? 'publish' : 'unpublish';

                unset($func);
                unset($alt);

                if ($type == "status")
                {
                        $func = ($row->$type == 1) ? 'unapprove' : 'approve';
                        $alt  = $row->$type ? JText::_('COM_HWDMS_APPROVED') : JText::_('COM_HWDMS_UNAPPROVED');
                        if ($row->$type == 2)
                        {
                                $state = 'pending';
                                $func = 'approve';
                                $alt = JText::_('COM_HWDMS_PENDING');
                        }
                        else if ($row->$type == 3)
                        {
                                $state = 'expired';
                                $func = 'approve';
                                $alt = JText::_('COM_HWDMS_REPORTED');
                        }
                }
                else if ($type == "featured")
                {
                        $func = $row->$type ? 'unfeature' : 'feature';
                        $alt  = $row->$type ? JText::_('COM_HWDMS_FEATURE') : JText::_('COM_HWDMS_UNFEATURE');
                }

                $image = '<span class="state '.$state.'"><span class="text">'.$alt.'</span></span>';

                $href = '<a class="jgrid" href="javascript:void(0);" onclick="return listItemTask(\'cb'.$i.'\',\'playlists.'.$func.'\')" title="'.JText::_($alt).'">';
                $href .= $image.'</a>';

		return $href;
	}
}
