<?php
/**
 * @version    SVN $Id: hwdmediashare.php 541 2012-10-03 13:03:42Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * hwdMediaShare component helper
 */
abstract class hwdMediaShareHelper
{
        /**
	 * Configure the Linkbar
	 */
	public static function addSubmenu($submenu)
	{
                $version = new JVersion();

                JSubMenuHelper::addEntry(JText::_('COM_HWDMS_DASHBOARD'), 'index.php?option=com_hwdmediashare&view=dashboard', $submenu == 'dashboard');
		JSubMenuHelper::addEntry(JText::_('COM_HWDMS_MEDIA'), 'index.php?option=com_hwdmediashare&view=media', $submenu == 'media');
		JSubMenuHelper::addEntry(JText::_('COM_HWDMS_CATEGORIES'), 'index.php?option=com_categories&extension=com_hwdmediashare', $submenu == 'categories');
		JSubMenuHelper::addEntry(JText::_('COM_HWDMS_ALBUMS'), 'index.php?option=com_hwdmediashare&view=albums', $submenu == 'albums');
		JSubMenuHelper::addEntry(JText::_('COM_HWDMS_GROUPS'), 'index.php?option=com_hwdmediashare&view=groups', $submenu == 'groups');
		JSubMenuHelper::addEntry(JText::_('COM_HWDMS_USER_CHANNELS'), 'index.php?option=com_hwdmediashare&view=users', $submenu == 'users');
		JSubMenuHelper::addEntry(JText::_('COM_HWDMS_PLAYLISTS'), 'index.php?option=com_hwdmediashare&view=playlists', $submenu == 'playlists');
		JSubMenuHelper::addEntry(JText::_('COM_HWDMS_ACTIVITIES'), 'index.php?option=com_hwdmediashare&view=activities', $submenu == 'activities');
		JSubMenuHelper::addEntry(JText::_('COM_HWDMS_MAINTENANCE'), 'index.php?option=com_hwdmediashare&view=maintenance', $submenu == 'maintenance');
		JSubMenuHelper::addEntry(JText::_('COM_HWDMS_CONFIGURATION'), 'index.php?option=com_hwdmediashare&view=configuration', $submenu == 'configuration');
		// Set some global property
		$document = JFactory::getDocument();
		$document->addStyleDeclaration('.icon-48-hwdmediashare {background-image: url(../media/com_hwdmediashare/assets/images/icons/48/icon-48-hwdms.png);padding-left:60px!important;}');
                // Load some styles when viewing in Joomla 3.0 and above
                if ($version->RELEASE >= 3.0) $document->addStyleSheet(JURI::root( true ).'/media/com_hwdmediashare/assets/css/j3.css');
        }
        
	/**
	 * Get the actions
	 */
	public static function getActions($id = 0, $type = 'media')
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		if (empty($id)) 
                {
			$assetName = 'com_hwdmediashare';
		}
		else 
                {
			$assetName = 'com_hwdmediashare.'.$type.'.'.(int) $id;
		}

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.delete', 'core.edit', 'core.edit.state', 'core.edit.own'
		);

		foreach ($actions as $action) 
                {
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
}
