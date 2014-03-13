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

class hwdMediaShareViewGroup extends JViewLegacy 
{
	protected $state;

	protected $item;

	protected $form;
        
	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
                // Get data from the model.
		$this->state	= $this->get('State');
		$this->item	= $this->get('Item');
		$this->form	= $this->get('Form');

                // Check for errors.
                if (count($errors = $this->get('Errors')))
                {
                        JError::raiseError(500, implode('<br />', $errors));
                        return false;
                }

		$this->addToolbar();
		$this->sidebar = JHtmlSidebar::render();

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
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));

		// Since we don't track these assets at the item level, use the category id.
		$canDo		= hwdMediaShareHelper::getActions($this->item->id, 'group');

		JToolBarHelper::title($isNew ? JText::_('COM_HWDMS_NEW_GROUP') : JText::_('COM_HWDMS_EDIT_GROUP'), 'users');

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit')||(count($user->getAuthorisedCategories('com_hwdmediashare', 'core.create')))))
		{
			JToolbarHelper::apply('group.apply');
			JToolbarHelper::save('group.save');
		}
		if (!$checkedOut && (count($user->getAuthorisedCategories('com_hwdmediashare', 'core.create'))))
		{
			JToolbarHelper::save2new('group.save2new');
		}
		// If an existing item, can save to a copy.
		if (!$isNew && (count($user->getAuthorisedCategories('com_hwdmediashare', 'core.create')) > 0))
		{
			JToolbarHelper::save2copy('group.save2copy');
		}
		if (empty($this->item->id))
		{
			JToolbarHelper::cancel('group.cancel');
		}
		else
		{
			JToolbarHelper::cancel('group.cancel', 'JTOOLBAR_CLOSE');
		}

		JToolbarHelper::divider();
		JToolBarHelper::custom('help', 'help.png', 'help.png', 'JHELP', false);                
	}
}
