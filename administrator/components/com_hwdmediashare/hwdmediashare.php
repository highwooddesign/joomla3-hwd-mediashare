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
if (!JFactory::getUser()->authorise('core.manage', 'com_hwdmediashare'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Require helper file.
JLoader::register('hwdMediaShareHelper', dirname(__FILE__).'/helpers/hwdmediashare.php');

// Require hwdMediaShare factory.
JLoader::register('hwdMediaShareFactory', JPATH_ROOT.'/components/com_hwdmediashare/libraries/factory.php');

// Get an instance of the controller.
$controller = JControllerLegacy::getInstance('hwdMediaShare');

// Perform the task.
$controller->execute(JFactory::getApplication()->input->get('task'));

// Redirect if set by the controller.
$controller->redirect();
