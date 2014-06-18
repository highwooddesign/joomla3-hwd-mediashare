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

class hwdMediaShareViewCustomField extends JViewLegacy
{
	protected $state;

	protected $item;

	protected $form;
        
	/**
	 * Display the view.
	 *
	 * @access  public
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
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

		// Display the template.
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @access  protected
	 * @return  void
	 */
	protected function addToolBar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$canDo		= hwdMediaShareHelper::getActions($this->item->id, 'customfield');
		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);

		JToolBarHelper::title($isNew ? JText::_('COM_HWDMS_NEW_CUSTOM_FIELD') : JText::_('COM_HWDMS_EDIT_CUSTOM_FIELD'), 'checkmark-circle');

		// If not checked out, can save the item.
		if ($canDo->get('core.edit')||(count($user->getAuthorisedCategories('com_hwdmediashare', 'core.create'))))
		{
			JToolbarHelper::apply('customfield.apply');
			JToolbarHelper::save('customfield.save');
		}
		if (count($user->getAuthorisedCategories('com_hwdmediashare', 'core.create')))
		{
			JToolbarHelper::save2new('customfield.save2new');
		}
		// If an existing item, can save to a copy.
		if (!$isNew && (count($user->getAuthorisedCategories('com_hwdmediashare', 'core.create')) > 0))
		{
			JToolbarHelper::save2copy('customfield.save2copy');
		}
		if (empty($this->item->id))
		{
			JToolbarHelper::cancel('customfield.cancel');
		}
		else
		{
			JToolbarHelper::cancel('customfield.cancel', 'JTOOLBAR_CLOSE');
		}
		JToolbarHelper::help('HWD', false, 'http://hwdmediashare.co.uk/learn/docs'); 
	}
}
