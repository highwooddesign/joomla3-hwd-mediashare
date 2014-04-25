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

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_hwdmigrator'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Require helper file.
JLoader::register('hwdMigratorHelper', dirname(__FILE__).'/helpers/hwdmigrator.php');

// Get an instance of the controller prefixed by hwdMediaShare.
$controller = JControllerLegacy::getInstance('hwdMigrator');

// Perform the task.
$controller->execute(JFactory::getApplication()->input->get('task'));

// Redirect if set by the controller
$controller->redirect();
