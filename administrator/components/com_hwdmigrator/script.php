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
	 * @access	public
         * @param       string      $parent     The class calling this method.
         * @return      void
	 */
	public function install($parent)
	{
		// echo '<p>' . JText::_('COM_HWDMIGRATOR_INSTALL_TEXT') . '</p>';
	}

	/**
	 * Method to uninstall the component.
	 *
	 * @access	public
         * @param       string      $parent     The class calling this method.
         * @return      void
	 */
	public function uninstall($parent)
	{
                // echo '<p>' . JText::_('COM_HWDMIGRATOR_UNINSTALL_TEXT') . '</p>';
	}

	/**
	 * Method to update the component.
	 *
	 * @access	public
         * @param       string      $parent     The class calling this method.
         * @return      void
	 */
	public function update($parent)
	{
		// echo '<p>' . JText::_('COM_HWDMIGRATOR_UPDATE_TEXT') . '</p>';
	}

	/**
	 * Method to run before an install/update/uninstall method.
	 *
	 * @access	public
         * @param       string      $type       The type of change (install, update or discover_install).
         * @param       string      $parent     The class calling this method.
         * @return      void
	 */
	public function preflight($type, $parent)
	{
		// echo '<p>' . JText::_('COM_HWDMIGRATOR_PREFLIGHT_' . $type . '_TEXT') . '</p>';
	}

	/**
	 * Method to run after an install/update/uninstall method.
	 *
	 * @access	public
         * @param       string      $type       The type of change (install, update or discover_install).
         * @param       string      $parent     The class calling this method.
         * @return      void
	 */
	public function postflight($type, $parent)
	{
		// echo '<p>' . JText::_('COM_HWDMIGRATOR_POSTFLIGHT_' . $type . '_TEXT') . '</p>';
	}
}