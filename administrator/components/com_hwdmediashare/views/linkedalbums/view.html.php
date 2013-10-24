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
class hwdMediaShareViewLinkedAlbums extends JViewLegacy {
        /**
	 * View list
	 */
        var $view_list = "linkedalbums";
        var $view_edit = "editalbum";
        var $view_type = "album";
        var $view_header = "Albums linked with this media";
        var $view_title = "COM_HWDMS_LINKED_ALBUMS";

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
                $this->mediaId = JRequest::getInt( 'media_id', '' );
                $this->items = $items;
                $this->pagination = $pagination;
                $this->state = $state;
                $this->viewAll = $this->state->get('filter.linked') == "all" ? true:false;

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
		JToolBarHelper::title(JText::_($this->view_title), 'hwdmediashare');
	}
 	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument()
	{
                $document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_HWDMS_HWDMEDIASHARE').' '.JText::_($this->view_title));
	}
	/**
	 * Method to get the publish status HTML
	 *
	 * @param	object	Field object
	 * @param	string	Type of the field
	 * @param	string	The ajax task that it should call
	 * @return	string	HTML source
	 **/
	public function getConnection( &$row, $i )
	{
                $state = $row->connection ? 'publish' : 'unpublish';

                unset($func);
                unset($alt);

                $func = $row->connection ? 'unlink' : 'link';
                $alt  = $row->connection ? JText::_('COM_HWDMS_LINK') : JText::_('COM_HWDMS_UNLINK');

                $image = '<span class="state '.$state.'"><span class="text">'.$alt.'</span></span>';

                $href = '<a class="jgrid" href="javascript:void(0);" onclick="return listItemTask(\'cb'.$i.'\',\''.$this->view_list.'.'.$func.'\')" title="'.JText::_($alt).'">';
                $href .= $image.'</a>';

		return $href;
	}
}