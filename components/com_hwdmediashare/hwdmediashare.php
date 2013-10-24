<?php
/**
 * @version    SVN $Id: hwdmediashare.php 1101 2013-02-12 13:16:11Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JLoader::register('hwdMediaShareFactory', dirname(__FILE__).'/libraries/factory.php');

// Temp solution
JHtml::_('behavior.framework', true);

// Require helper files
JLoader::register('hwdMediaShareHelperRoute', dirname(__FILE__).'/helpers/route.php');
JLoader::register('hwdMediaShareHelperNavigation', dirname(__FILE__).'/helpers/navigation.php');
JLoader::register('hwdMediaShareHelperMobile', dirname(__FILE__).'/helpers/mobile.php');
JLoader::register('hwdMediaShareHelperModule', dirname(__FILE__).'/helpers/module.php');

// Entry check
hwdMediaShareHelperRoute::entry();

// Import joomla controller library
jimport('joomla.application.component.controller');

// Get an instance of the controller prefixed by hwdMediaShare
$controller = JControllerLegacy::getInstance('hwdMediaShare');

// Perform the Request task
$controller->execute(JFactory::getApplication()->input->get('task'));

// Redirect if set by the controller
$controller->redirect();
