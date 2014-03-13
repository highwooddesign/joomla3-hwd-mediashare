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

class hwdMediaShareViewCustomFields extends JViewLegacy 
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
                            
		JToolBarHelper::title(JText::_('COM_HWDMS_CUSTOM_FIELDS'), 'checkmark-circle');

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
	 * Method to display the human readable field type.
	 *
	 * @return  void
	 */
	public function getFieldText( $type )
	{
		$types	= $this->get('ProfileTypes');
		$value	= isset( $types[ $type ] ) ? $types[ $type ] : '';

		return $value;
	}

	/**
	 * Method to display the human readable element type.
	 *
	 * @return  void
	 */
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
