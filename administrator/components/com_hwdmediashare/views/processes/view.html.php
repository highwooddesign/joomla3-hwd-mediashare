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

class hwdMediaShareViewProcesses extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

	protected $successful;
        
	protected $unnecessary;
        
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
		$this->successful = $this->get('Successful');
		$this->unnecessary = $this->get('Unnecessary');
                $this->filterForm = $this->get('FilterForm');
                $this->return = base64_encode(JFactory::getURI()->toString());

                // Import HWD libraries.
                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('files');
                hwdMediaShareFactory::load('processes');
                hwdMediaShareFactory::load('thumbnails');

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
                
                JToolBarHelper::title(JText::_('COM_HWDMS_PROCESSES'), 'cog');

                JToolBarHelper::custom('process.run', 'cog', 'cog', JText::_('COM_HWDMS_PROCESS_SELECTED'), true);
                JToolBarHelper::custom('process.runall', 'cog', 'cog', JText::_('COM_HWDMS_PROCESS_ALL'), false);
		if ($canDo->get('core.edit.state'))
                {
                        JToolBarHelper::custom('processes.reset', 'switch', 'switch','COM_HWDMS_RESET', true);
                        JToolBarHelper::custom('processes.resetall', 'switch', 'switch','COM_HWDMS_FORCE_RESET', true);
                        JToolBarHelper::checkin('processes.checkin');
		}
                if ($canDo->get('core.delete'))
		{
                        JToolBarHelper::deleteList('', 'processes.delete');
                }
                if ($this->successful) 
                {
                        JToolBarHelper::custom('processes.deletesuccessful', 'delete', 'delete', JText::sprintf('COM_HWDMS_DELETE_X_SUCCESSFUL', $this->successful), false);
                }
                if ($this->unnecessary) 
                {
                        JToolBarHelper::custom('processes.deleteunnecessary', 'delete', 'delete', JText::sprintf('COM_HWDMS_DELETE_X_UNNECESSARY', $this->unnecessary), false);
                }
		JToolbarHelper::help('HWD', false, 'http://hwdmediashare.co.uk/learn/docs');
	}
                
	/**
	 * Method to display a human readable process status.
	 *
	 * @access  public
	 * @return  string  The process status.
	 */
	public function getStatus($item)
	{
                switch ($item->status) 
                {
                        case 1:
                                return '<span class="label label-warning">' . JText::_('COM_HWDMS_QUEUED') . '</span>';
                        break;
                        case 2:
                                return '<span class="label label-success">' . JText::_('COM_HWDMS_SUCCESSFUL') . '</span>';
                        break;
                        case 3:
                                return '<span class="label label-important">' . JText::_('COM_HWDMS_FAILED') . '</span>';
                        break;
                        case 4:
                                return '<span class="label label-info">' . JText::_('COM_HWDMS_UNNECESSARY') . '</span>';
                        break;
                }
	}
        
	/**
	 * Display the run view.
	 *
         * @access  public
	 * @param   array  $cid  The array of process that should be run.
	 * @return  void
	 */
	public function run($cid = array())
	{
                // Make sure the ids are integers.
                jimport('joomla.utilities.arrayhelper');
                JArrayHelper::toInteger($cid);
                        
                $this->cid = $cid;
                
                // Check for errors.
                if (count($errors = $this->get('Errors')))
                {
                        JError::raiseError(500, implode('<br />', $errors));
                        return false;
                }

                JToolBarHelper::title(JText::_('COM_HWDMS_PROCESSES'), 'cog');
                
                JToolBarHelper::cancel('processes.close', 'JTOOLBAR_CLOSE');
		JToolbarHelper::help('HWD', false, 'http://hwdmediashare.co.uk/learn/docs');

                // Display the template.
		parent::display('run');

		$this->document->addStyleSheet(JURI::root() . "media/com_hwdmediashare/assets/css/administrator.css");              
	}        
}
