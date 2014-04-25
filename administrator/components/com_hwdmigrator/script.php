<?php
/**
 * @package     Joomla.administrator
 * @subpackage  Component.hwdmigrator
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class com_hwdMigratorInstallerScript
{
        /**
	 * Method to install the component.
	 *
	 * @return void
	 */
	function install($parent)
	{
		//echo '<p>' . JText::_('COM_HWDMIGRATOR_INSTALL_TEXT') . '</p>';
	}

	/**
	 * Method to uninstall the component.
	 *
	 * @return void
	 */
	function uninstall($parent)
	{
		//echo '<p>' . JText::_('COM_HWDMIGRATOR_UNINSTALL_TEXT') . '</p>';
	}

	/**
	 * Method to update the component.
	 *
	 * @return void
	 */
	function update($parent)
	{
		//echo '<p>' . JText::_('COM_HWDMIGRATOR_UPDATE_TEXT') . '</p>';
	}

	/**
	 * Method to run before an install/update/uninstall method.
	 *
	 * @return void
	 */
	function preflight($type, $parent)
	{
		//echo '<p>' . JText::_('COM_HWDMIGRATOR_PREFLIGHT_' . $type . '_TEXT') . '</p>';
	}

	/**
	 * Method to run after an install/update/uninstall method.
	 *
	 * @return void
	 */
	function postflight($type, $parent)
	{
		//echo '<p>' . JText::_('COM_HWDMIGRATOR_POSTFLIGHT_' . $type . '_TEXT') . '</p>';
	}
}
