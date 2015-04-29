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
	 * Method to run before an install/update/uninstall method.
	 *
	 * @access  public
         * @param   string  $type    The type of change (install, update or discover_install).
         * @param   string  $parent  The class calling this method.
         * @return  void
	 */
	public function preflight($type, $parent)
	{
		// echo '<p>' . JText::_('COM_HWDMIGRATOR_PREFLIGHT_' . $type . '_TEXT') . '</p>';

		// Initialise variables.
                $app = JFactory::getApplication();
                
                // Get Joomla version.
                $version = new JVersion();

                // Check Joomla compatibility.
                if ($version->RELEASE < 3 || $version->RELEASE >= 4)
                {
			$app->enqueueMessage(JText::_('COM_HWDMIGRATOR_MESSAGE_NOT_COMPATIBLE'));
			$app->redirect('index.php?option=com_installer');
                }
        }             
}
