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

class hwdMediaShareViewSubscriptions extends JViewLegacy 
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
                
		JToolBarHelper::title(JText::_('COM_HWDMS_SUBSCRIPTIONS'), 'users');

                if ($canDo->get('core.create'))
		{
			JToolBarHelper::addNew('subscription.add');
		}
                if ($canDo->get('core.delete'))
		{
			JToolBarHelper::divider();
			JToolBarHelper::deleteList('', 'subscriptions.delete');
                        JToolBarHelper::divider();
                }
		JToolbarHelper::help('HWD', false, 'http://hwdmediashare.co.uk/learn/docs');   
        }        
}

