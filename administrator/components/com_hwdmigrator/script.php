<?php
/**
 * @version    SVN $Id: script.php 425 2012-06-28 07:48:57Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Script file of hwdMediaShare component
 */
class com_hwdMigratorInstallerScript
{
        /**
	 * Stored error
	 */
        protected $error;
        
        /**
	 * Method to install the component
	 *
	 * @return void
	 */
	function install($parent)
	{
		//echo '<p>' . JText::_('COM_HWDMIGRATOR_INSTALL_TEXT') . '</p>';
	}

	/**
	 * Method to uninstall the component
	 *
	 * @return void
	 */
	function uninstall($parent)
	{
		//echo '<p>' . JText::_('COM_HWDMIGRATOR_UNINSTALL_TEXT') . '</p>';
	}

	/**
	 * Method to update the component
	 *
	 * @return void
	 */
	function update($parent)
	{
		//echo '<p>' . JText::_('COM_HWDMIGRATOR_UPDATE_TEXT') . '</p>';
	}

	/**
	 * Method to run before an install/update/uninstall method
	 *
	 * @return void
	 */
	function preflight($type, $parent)
	{
		//echo '<p>' . JText::_('COM_HWDMIGRATOR_PREFLIGHT_' . $type . '_TEXT') . '</p>';
	}

	/**
	 * Method to run after an install/update/uninstall method
	 *
	 * @return void
	 */
	function postflight($type, $parent)
	{
		//echo '<p>' . JText::_('COM_HWDMIGRATOR_POSTFLIGHT_' . $type . '_TEXT') . '</p>';
	}
}
