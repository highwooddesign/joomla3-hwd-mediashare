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

abstract class hwdMediaShareHelper
{
	/**
	 * Configure the Linkbar.
         * 
	 * @param   string  $vName  The name of the active view.
         * 
	 * @return  void
	 */
	public static function addSubmenu($submenu)
	{
                $version = new JVersion();

		JHtmlSidebar::addEntry(
			JText::_('COM_HWDMS_DASHBOARD'),
			'index.php?option=com_hwdmediashare&view=dashboard',
			$submenu == 'dashboard'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_HWDMS_MEDIA'),
			'index.php?option=com_hwdmediashare&view=media',
			$submenu == 'media'
		);                
		JHtmlSidebar::addEntry(
			JText::_('COM_HWDMS_CATEGORIES'),
			'index.php?option=com_categories&extension=com_hwdmediashare',
			$submenu == 'categories'
		);    
		JHtmlSidebar::addEntry(
			JText::_('COM_HWDMS_ALBUMS'),
			'index.php?option=com_hwdmediashare&view=albums',
			$submenu == 'albums'
		);    
		JHtmlSidebar::addEntry(
			JText::_('COM_HWDMS_GROUPS'),
			'index.php?option=com_hwdmediashare&view=groups',
			$submenu == 'groups'
		);    
		JHtmlSidebar::addEntry(
			JText::_('COM_HWDMS_USER_CHANNELS'),
			'index.php?option=com_hwdmediashare&view=users',
			$submenu == 'users'
		);    
		JHtmlSidebar::addEntry(
			JText::_('COM_HWDMS_PLAYLISTS'),
			'index.php?option=com_hwdmediashare&view=playlists',
			$submenu == 'playlists'
		);    
		JHtmlSidebar::addEntry(
			JText::_('COM_HWDMS_ACTIVITIES'),
			'index.php?option=com_hwdmediashare&view=activities',
			$submenu == 'activities'
		);    
		JHtmlSidebar::addEntry(
			JText::_('COM_HWDMS_MAINTENANCE'),
			'index.php?option=com_hwdmediashare&view=maintenance',
			$submenu == 'maintenance'
		);   
		JHtmlSidebar::addEntry(
			JText::_('COM_HWDMS_CONFIGURATION'),
			'index.php?option=com_hwdmediashare&view=configuration',
			$submenu == 'configuration'
		); 
		JHtmlSidebar::addEntry(
			JText::_('COM_HWDMS_MEDIA_PROCESSING'),
			'index.php?option=com_hwdmediashare&view=processes',
			$submenu == 'processes'
		);  
        }
        
	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param   string   $component  The component name.
	 * @param   string   $section    The access section name.
	 * @param   integer  $id         The item ID.
	 *
	 * @return  JObject
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
