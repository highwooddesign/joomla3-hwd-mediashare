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
class hwdMediaShareViewCustomFields extends JViewLegacy {
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
		JToolBarHelper::title(JText::_('COM_HWDMS_CUSTOM_FIELDS'), 'hwdmediashare');

                if ($canDo->get('core.create'))
		{
			JToolBarHelper::addNew('customfield.add');
		}
		if ($canDo->get('core.edit'))
		{
			JToolBarHelper::editList('customfield.edit');
		}
		if ($canDo->get('core.edit.state'))
                {
			JToolBarHelper::divider();
			JToolBarHelper::publish('customfields.publish', 'JTOOLBAR_PUBLISH', true);
			JToolBarHelper::unpublish('customfields.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			JToolBarHelper::divider();
			JToolBarHelper::archiveList('customfields.archive');
			JToolBarHelper::checkin('customfields.checkin');
		}
		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete'))
                {
			JToolBarHelper::divider();
                        JToolBarHelper::deleteList('', 'customfields.delete', 'JTOOLBAR_EMPTY_TRASH');
                        JToolBarHelper::divider();
                }
		else if ($canDo->get('core.edit.state'))
                {
			JToolBarHelper::divider();
                        JToolBarHelper::trash('customfields.trash');
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
		$document->setTitle(JText::_('COM_HWDMS_HWDMEDIASHARE').' '.JText::_('COM_HWDMS_CUSTOM_FIELDS'));
	}
	/**
	 * Method to get the Field type in text
	 *
	 * @param	string	Type of field
	 *
	 * @return	string	Text representation of the field type.
	 **/
	public function getFieldText( $type )
	{
		$types	= $this->get('ProfileTypes');
		$value	= isset( $types[ $type ] ) ? $types[ $type ] : '';

		return $value;
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

                if ($type == "published")
                {
                        $func = $row->$type ? 'unpublish' : 'publish';
                        $alt  = $row->$type ? JText::_('COM_HWDMS_PUBLISHED') : JText::_('COM_HWDMS_UNPUBLISH');
                }
                else if ($type == "searchable")
                {
                        $func = $row->$type ? 'unsearchable' : 'searchable';
                        $alt  = $row->$type ? JText::_('COM_HWDMS_SEARCHABLE') : JText::_('COM_HWDMS_NOTSEARCHABLE');
                }
                else if ($type == "visible")
                {
                        $func = $row->$type ? 'unvisible' : 'visible';
                        $alt  = $row->$type ? JText::_('COM_HWDMS_VISIBLE') : JText::_('COM_HWDMS_INVISIBLE');
                }
                else if ($type == "required")
                {
                        $func = $row->$type ? 'unrequired' : 'required';
                        $alt  = $row->$type ? JText::_('COM_HWDMS_REQUIRED') : JText::_('COM_HWDMS_NOTREQUIRED');
                }

                $image = '<span class="state '.$state.'"><span class="text">'.$alt.'</span></span>';

                $href = '<a class="jgrid" href="javascript:void(0);" onclick="return listItemTask(\'cb'.$i.'\',\'customfields.'.$func.'\')" title="'.JText::_($alt).'">';
                $href .= $image.'</a>';

		return $href;
	}
	/**
	 * Method to get the publish status HTML
	 *
	 * @param	object	Field object
	 * @param	string	Type of the field
	 * @param	string	The ajax task that it should call
	 * @return	string	HTML source
	 **/
	public function getElementText( $element )
	{
                switch ($element) {
                    case "1":
                        return JText::_( 'COM_HWDMS_MEDIA' );
                        break;
                    case "2":
                        return JText::_( 'COM_HWDMS_ALBUM' );
                        break;
                    case "3":
                        return JText::_( 'COM_HWDMS_GROUP' );
                        break;
                    case "4":
                        return JText::_( 'COM_HWDMS_PLAYLIST' );
                        break;
                    case "5":
                        return JText::_( 'COM_HWDMS_CHANNEL' );
                        break;
                }
	}
}
