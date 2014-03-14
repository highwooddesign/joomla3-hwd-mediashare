<?php
/**
 * @package     Joomla.administrator
 * @subpackage  Component.hwdmediashare
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class hwdMediaShareViewMedia extends JViewLegacy
{
	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
                // Get data from the model.
                $this->items = $this->get('Items');
                $this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
                $this->filterForm = $this->get('FilterForm');

                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('files');

                // Check for errors.
                if (count($errors = $this->get('Errors')))
                {
                        JError::raiseError(500, implode('<br />', $errors));
                        return false;
                }
                
		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal')
		{
			$this->addToolbar();
			$this->sidebar = JHtmlSidebar::render();
		}

		// Display the template
		parent::display($tpl);
                
		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::root() . "media/com_hwdmediashare/assets/css/administrator.css");
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 */
	protected function addToolBar()
	{
		$canDo = hwdMediaShareHelper::getActions();
		$user  = JFactory::getUser();
                
		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');
                                
		JToolBarHelper::title(JText::_('COM_HWDMS_MEDIA'), 'video');

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
			JToolBarHelper::custom('media.feature', 'featured', 'featured', 'COM_HWDMS_FEATURE', true);
                        JToolBarHelper::custom('media.unfeature','removeg','remove','COM_HWDMS_UNFEATURE', true);
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
		// Add a batch button
		if ($user->authorise('core.create', 'com_hwdmediashare') && $user->authorise('core.edit', 'com_hwdmediashare') && $user->authorise('core.edit.state', 'com_hwdmediashare'))
		{
			JHtml::_('bootstrap.modal', 'collapseModal');
			$title = JText::_('JTOOLBAR_BATCH');

			// Instantiate a new JLayoutFile instance and render the batch button
			$layout = new JLayoutFile('joomla.toolbar.batch');

			$dhtml = $layout->render(array('title' => $title));
			$bar->appendButton('Custom', $dhtml, 'batch');
		}
		JToolbarHelper::help('HWD', false, 'http://hwdmediashare.co.uk/learn/docs');
	}

	/**
	 * Method to get the icon for the media type
	 *
	 * @param	object	$item   Media object
	 * @return	string	Icon URL
	 **/
	public function getMediaTypeIcon($item)
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
	 * Method to get the human readable media type
	 *
	 * @param	object	$item   Media object
	 * @return	string	Translated media type text
	 **/
	public function getMediaType($item)
	{
                hwdMediaShareFactory::load('media');
                return hwdMediaShareMedia::getMediaType($item);
	}
        
	/**
	 * Method to get the linked category list
	 *
	 * @param	object	$item   Media object
	 * @return	string	Linked, comma separated, category list
	 **/
	public function getCategories($item)
	{
                $href = '';
                if (count($item->categories) > 0)
                {
                        for($i = 0; $i < count($item->categories); $i++)
                        {
                                $value = $item->categories[$i];
                                if ($i != 0) $href.= ', ';
                                $href.= '<a href="index.php?option=com_hwdmediashare&view=media&filter_category_id='.$value->id.'">';
                                $href.= $this->escape($value->title);
                                $href.= '</a>';
                        }                     
                }
                else
                {
                        $href = JText::_('COM_HWDMS_NONE');
                }

                return $href;
	}      
}
