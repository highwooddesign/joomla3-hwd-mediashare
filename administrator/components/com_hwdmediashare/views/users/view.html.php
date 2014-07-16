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

class hwdMediaShareViewUsers extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;
        
	public $filterForm;

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
                $this->items = $this->get('Items');
                $this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
                $this->filterForm = $this->get('FilterForm');

                // Import HWD libraries.
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
                
		// Display the template.
		parent::display($tpl);
                
		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::root() . "media/com_hwdmediashare/assets/css/administrator.css");                
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @access  protected
	 * @return  void
	 */
	protected function addToolBar()
	{
		$canDo = hwdMediaShareHelper::getActions();
		$user  = JFactory::getUser();
                
		// Get the toolbar object instance.
		$bar = JToolBar::getInstance('toolbar');
                                
		JToolBarHelper::title(JText::_('COM_HWDMS_USER_CHANNELS'), 'user');

                if ($canDo->get('core.create'))
		{
			JToolBarHelper::addNew('user.add');
		}
		if ($canDo->get('core.edit'))
		{
			JToolBarHelper::editList('user.edit');
		}
		if ($canDo->get('core.edit.state'))
                {
			JToolBarHelper::publish('users.publish', 'JTOOLBAR_PUBLISH', true);
			JToolBarHelper::unpublish('users.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			JToolBarHelper::custom('users.feature', 'featured', 'featured', 'COM_HWDMS_FEATURE', true);
                        JToolBarHelper::custom('users.unfeature', 'unfeatured', 'unfeatured', 'COM_HWDMS_UNFEATURE', true);
			JToolBarHelper::archiveList('users.archive');
			JToolBarHelper::checkin('users.checkin');
		}
		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete'))
                {
                        JToolBarHelper::deleteList('', 'users.delete', 'JTOOLBAR_EMPTY_TRASH');
                }
		elseif ($canDo->get('core.edit.state'))
                {
                        JToolBarHelper::trash('users.trash');
		}
		// Add a batch button.
		if ($user->authorise('core.create', 'com_hwdmediashare') && $user->authorise('core.edit', 'com_hwdmediashare') && $user->authorise('core.edit.state', 'com_hwdmediashare'))
		{
			JHtml::_('bootstrap.modal', 'collapseModal');
			$title = JText::_('JTOOLBAR_BATCH');

			// Instantiate a new JLayoutFile instance and render the batch button.
			$layout = new JLayoutFile('joomla.toolbar.batch');

			$dhtml = $layout->render(array('title' => $title));
			$bar->appendButton('Custom', $dhtml, 'batch');
		}                
		JToolbarHelper::help('HWD', false, 'http://hwdmediashare.co.uk/learn/docs'); 
	}        
}
