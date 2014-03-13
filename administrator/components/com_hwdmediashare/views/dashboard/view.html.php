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

class hwdMediaShareViewDashboard extends JViewLegacy
{
	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
                // Get data from the model
                $this->media = $this->get('Media');
                $this->activity = $this->get('Activity');
                $this->version = $this->get('Version');
                $this->nummedia = $this->get('CountMedia');
                $this->numcategories = $this->get('CountCategories');
                $this->numalbums = $this->get('CountAlbums');
                $this->numgroups = $this->get('CountGroups');
                $this->numchannels = $this->get('CountChannels');
                $this->numplaylists = $this->get('CountPlaylists');
                
                // Load HWD libaries
                hwdMediaShareFactory::load('activities');
                
                // Check for errors
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
                
		JToolBarHelper::title(JText::_('COM_HWDMS_DASHBOARD'), 'home');

                // Sample data install option
                if ($this->nummedia == 0 && $this->numcategories == 0 && $this->numalbums == 0 && $this->numgroups == 0 && $this->numchannels == 0 && $this->numplaylists == 0) 
                {
                        $document = JFactory::getDocument();
                        $document->addStyleDeclaration('.icon-32-sample {background-image: url(../media/com_hwdmediashare/assets/images/icons/32/sample-data.png);width:98px!important;}');
                        JToolBarHelper::custom('sample.install', 'sample.png', 'sample_f2.png', JText::_('COM_HWDMS_INSTALL_SAMPLE_DATA'), true);
                }
                JToolbarHelper::help('HWD', false, 'http://hwdmediashare.co.uk/learn/docs');
	}
}
