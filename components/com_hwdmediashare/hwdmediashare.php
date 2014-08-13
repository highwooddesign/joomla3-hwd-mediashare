<?php
/**
 * @package     Joomla.site
 * @subpackage  Component.hwdmediashare
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

// Register HWD factory.
JLoader::register('hwdMediaShareFactory', dirname(__FILE__).'/libraries/factory.php');

// Register helper files.
JLoader::register('hwdMediaShareHelperRoute', dirname(__FILE__).'/helpers/route.php');
JLoader::register('hwdMediaShareHelperNavigation', dirname(__FILE__).'/helpers/navigation.php');
JLoader::register('hwdMediaShareHelperModule', dirname(__FILE__).'/helpers/module.php');

// Access check.
hwdMediaShareHelperRoute::entry();

// Get an instance of the controller.
$controller = JControllerLegacy::getInstance('hwdMediaShare');

// Perform the task.
$controller->execute(JFactory::getApplication()->input->get('task'));

// Redirect if set by the controller.
$controller->redirect();
