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
	 * Display the view.
	 *
	 * @access  public
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 * @return  void
	 */
	public function display($tpl = null)
	{
                // Get data from the model
                $this->media = $this->get('Media');
                $this->activity = $this->get('Activity');
                $this->version = $this->get('Version');
                $this->nummedia = $this->get('MediaCount');
                $this->numcategories = $this->get('CategoryCount');
                $this->numalbums = $this->get('AlbumCount');
                $this->numgroups = $this->get('GroupCount');
                $this->numchannels = $this->get('UserCount');
                $this->numplaylists = $this->get('PlaylistCount');
                
                // Load HWD libaries.
                hwdMediaShareFactory::load('activities');
                
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
                
		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');
                
		JToolBarHelper::title(JText::_('COM_HWDMS_DASHBOARD'), 'home');

                // If no data exists in the gallery, then show the sample data installation button.
                if ($this->nummedia == 0 && $this->numcategories == 0 && $this->numalbums == 0 && $this->numgroups == 0 && $this->numchannels == 0 && $this->numplaylists == 0) 
                {
                        JToolBarHelper::custom('sample.install', 'database', 'database', JText::_('COM_HWDMS_INSTALL_SAMPLE_DATA'), false);
                }
                JToolbarHelper::help('HWD', false, 'http://hwdmediashare.co.uk/learn/docs');
	}
}
