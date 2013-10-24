<?php
/**
 * @version    SVN $Id: view.html.php 425 2012-06-28 07:48:57Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * hwdMigrator View
 */
class hwdMigratorViewDashboard extends JViewLegacy {
	/**
	 * display method of Hello view
	 * @return void
	 */
	public function display($tpl = null)
	{
                // Get data from the model
                $video_items = $this->get('VideoItems');
                $video_categories = $this->get('VideoCategories');
                $video_groups = $this->get('VideoGroups');
                $video_playlists = $this->get('VideoPlaylists');
                $photo_items = $this->get('PhotoItems');
                $photo_categories = $this->get('PhotoCategories');
                $photo_groups = $this->get('PhotoGroups');
                $photo_albums = $this->get('PhotoAlbums');
                
                // Check for errors.
                if (count($errors = $this->get('Errors')))
                {
                        JError::raiseError(500, implode('<br />', $errors));
                        return false;
                }

                // Assign data to the view
                $this->video_items = $video_items;
                $this->video_categories = $video_categories;
                $this->video_groups = $video_groups;
                $this->video_playlists = $video_playlists;
                $this->photo_items = $photo_items;
                $this->photo_categories = $photo_categories;
                $this->photo_groups = $photo_groups;
                $this->photo_albums = $photo_albums;
                
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		// Set the toolbar
		$this->addToolBar();

		// Display the template
		parent::display($tpl);

		// Set the document
		$this->setDocument();
	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar()
	{
		//JRequest::setVar('hidemainmenu', true);

		JToolBarHelper::title(JText::_('COM_HWDMIGRATOR_MIGRATOR'), 'hwdmigrator');

                JToolBarHelper::custom('maintenace.refresh', 'refresh.png', 'refresh_f2.png', JText::_('COM_HWDMIGRATOR_REFRESH'), false);
                JToolBarHelper::custom('maintenace.run', 'apply.png', 'apply.png', JText::_('COM_HWDMIGRATOR_RUN'), false);
		JToolBarHelper::divider();
                JToolBarHelper::custom('help', 'help.png', 'help.png', 'JHELP', false);
	}
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument()
	{
		$document = JFactory::getDocument();
                $document->addScript(JURI::root() . "/administrator/components/com_hwdmigrator/views/dashboard/submitbutton.js");
		$document->addStyleSheet(JURI::root() . "media/com_hwdmigrator/assets/css/administrator.css");
	}
}
