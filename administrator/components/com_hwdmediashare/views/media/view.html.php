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
class hwdMediaShareViewMedia extends JViewLegacy {
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

                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('files');

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
		JToolBarHelper::title(JText::_('COM_HWDMS_MEDIA'), 'hwdmediashare');

                if ($canDo->get('core.create'))
		{
			JToolBarHelper::addNew('addmedia.add');
		}
		if ($canDo->get('core.edit'))
		{
			JToolBarHelper::editList('editmedia.edit');
		}
		if ($canDo->get('core.edit.state'))
                {
			JToolBarHelper::divider();
			JToolBarHelper::publish('media.publish', 'JTOOLBAR_PUBLISH', true);
			JToolBarHelper::unpublish('media.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			JToolBarHelper::custom('media.feature', 'featured.png', 'featured_f2.png', 'COM_HWDMS_FEATURE', true);
                        JToolBarHelper::custom('media.unfeature','remove.png','remove_f2.png','COM_HWDMS_UNFEATURE', true);
                        JToolBarHelper::divider();
			JToolBarHelper::archiveList('media.archive');
			JToolBarHelper::checkin('media.checkin');
		}
		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete'))
                {
			JToolBarHelper::divider();
                        JToolBarHelper::deleteList('', 'media.delete', 'JTOOLBAR_EMPTY_TRASH');
                        JToolBarHelper::divider();
                }
		else if ($canDo->get('core.edit.state'))
                {
			JToolBarHelper::divider();
                        JToolBarHelper::trash('media.trash');
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
                $document->addScript(JURI::root() . "/administrator/components/com_hwdmediashare/views/media/submitbutton.js");
		$document->setTitle(JText::_('COM_HWDMS_HWDMEDIASHARE').' '.JText::_('COM_HWDMS_MEDIA'));
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

                $href = '<a class="jgrid" href="javascript:void(0);" onclick="return listItemTask(\'cb'.$i.'\',\'media.'.$func.'\')" title="'.JText::_($alt).'">';
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
	public function getMediaTypeIcon( &$item )
	{
                switch ($item->media_type) 
                {
                        case 1:
                            return JURI::root( true ).'/media/com_hwdmediashare/assets/images/icons/32/audio.png';
                            break;
                        case 2:
                            return JURI::root( true ).'/media/com_hwdmediashare/assets/images/icons/32/document.png';
                            break;
                        case 3:
                            return JURI::root( true ).'/media/com_hwdmediashare/assets/images/icons/32/image.png';
                            break;
                        case 4:
                            return JURI::root( true ).'/media/com_hwdmediashare/assets/images/icons/32/video.png';
                            break;
                }
                switch ($item->type) 
                {
                        case 2:
                            return JURI::root( true ).'/media/com_hwdmediashare/assets/images/icons/32/remote.png';
                            break;
                        case 3:
                            return JURI::root( true ).'/media/com_hwdmediashare/assets/images/icons/32/embed.png';
                            break;
                        case 4:
                            return JURI::root( true ).'/media/com_hwdmediashare/assets/images/icons/32/rtmp.png';
                            break;
                        case 4:
                            return JURI::root( true ).'/media/com_hwdmediashare/assets/images/icons/32/cdn.png';
                            break;
                }
	}
        /**
	 * Method to get the publish status HTML
	 *
	 * @param	object	Field object
	 * @param	string	Type of the field
	 * @param	string	The ajax task that it should call
	 * @return	string	HTML source
	 **/
	public function getCategories( &$item )
	{
                 if (count($item->categories) > 1)
                 {
                        $tooltip = JText::sprintf( 'COM_HWDMS_NCATEGORIES', count($item->categories)).'::';
                        foreach ($item->categories as $value)
                        {
                                $tooltip.= $value->title . '<br/>';
                        }
                        unset($value);

                        $href = '<span class="editlinktip hasTip" title="'.$tooltip.'" >';
                        $href.= $this->escape($item->categories[0]->title);
                        $href.= '<p class="smallsub">';
                        $href.= JText::sprintf( 'COM_HWDMS_NMORECATEGORIES', count($item->categories)-1);
                        $href.= '</p>';
                        $href.= '</span>';
                }
                else if (count($item->categories) == 1)
                {
                        $href = $this->escape($item->categories[0]->title);
                }
                else
                {
                        $href = JText::_('COM_HWDMS_NONE');
                }

                return $href;
	}
}
