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

class hwdMigratorViewDashboard extends JViewLegacy
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
                $this->video_items = $this->get('VideoItems');
                $this->video_categories = $this->get('VideoCategories');
                $this->video_groups = $this->get('VideoGroups');
                $this->video_playlists = $this->get('VideoPlaylists');
                $this->photo_items = $this->get('PhotoItems');
                $this->photo_categories = $this->get('PhotoCategories');
                $this->photo_groups = $this->get('PhotoGroups');
                $this->photo_albums = $this->get('PhotoAlbums');
                
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
		}
                
		// Display the template.
		parent::display($tpl);
                
		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::root() . "media/com_hwdmigrator/assets/css/administrator.css");                
                $document->addScript(JURI::root() . "/administrator/components/com_hwdmigrator/views/dashboard/submitbutton.js");
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 */
	protected function addToolBar()
	{
		JToolBarHelper::title(JText::_('COM_HWDMIGRATOR_MIGRATOR'), 'home');

		JToolBarHelper::custom('maintenance.refresh', 'refresh', 'refresh', JText::_('COM_HWDMIGRATOR_REFRESH'), false);
		JToolbarHelper::divider();
		JToolBarHelper::custom('maintenance.run', 'apply', 'apply', JText::_('COM_HWDMIGRATOR_RUN'), false);
		JToolbarHelper::divider();
		JToolbarHelper::help('HWD', false, 'http://hwdmediashare.co.uk/learn/docs/59-getting-started/migration/115-migrating-from-hwdvideoshare-to-hwdmediashare');
	}
}
