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

class hwdMediaShareViewSubscription extends JViewLegacy 
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

		$canDo		= hwdMediaShareHelper::getActions($this->item->id, 'subscription');
		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);

		JToolBarHelper::title($isNew ? JText::_('COM_HWDMS_NEW_SUBSCRIPTION') : JText::_('COM_HWDMS_EDIT_SUBSCRIPTION'), 'users');

		// If not checked out, can save the item.
		if ($canDo->get('core.edit')||(count($user->getAuthorisedCategories('com_hwdmediashare', 'core.create'))))
		{
			JToolbarHelper::apply('subscription.apply');
			JToolbarHelper::save('subscription.save');
		}
		if (empty($this->item->id))
		{
			JToolbarHelper::cancel('subscription.cancel');
		}
		else
		{
			JToolbarHelper::cancel('subscription.cancel', 'JTOOLBAR_CLOSE');
		}
		JToolbarHelper::help('HWD', false, 'http://hwdmediashare.co.uk/learn/docs'); 
	}
}
