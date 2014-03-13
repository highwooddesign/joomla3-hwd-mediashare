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

class hwdMediaShareViewProcess extends JViewLegacy
{
	protected $state;

	protected $item;

	protected $form;

	/**
	 * The logs for this process.
	 * @var    object
	 */        
	protected $items;
        
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
		$this->items	= $this->get('Items');               
                
                hwdMediaShareFactory::load('processes');

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

		JToolBarHelper::title(JText::_('COM_HWDMS_PROCESS_LOG'), 'cog');

		JToolbarHelper::cancel('process.cancel', 'JTOOLBAR_CLOSE');
		JToolbarHelper::divider();
		JToolBarHelper::custom('help', 'help.png', 'help.png', 'JHELP', false);                
	}
        
	/**
	 * Add the human readable process type.
	 * @return  void
	 */
	public function getProcessType($item)
	{
                hwdMediaShareFactory::load('processes');
                return hwdMediaShareProcesses::getType($item);
	}         
}
