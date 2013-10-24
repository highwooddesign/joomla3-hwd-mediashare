<?php
/**
 * @version    SVN $Id: hwdmediashare.php 1050 2013-02-07 14:48:50Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Temporary solution to load Mootools before all HWD content
JHtml::_('behavior.framework', true);

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_hwdmediashare'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Require helper file
JLoader::register('hwdMediaShareHelper', dirname(__FILE__).'/helpers/hwdmediashare.php');

// Require hwdMediaShare factory
JLoader::register('hwdMediaShareFactory', JPATH_ROOT.'/components/com_hwdmediashare/libraries/factory.php');

// Import joomla controller library
jimport('joomla.application.component.controller');

// Get an instance of the controller prefixed by hwdMediaShare
$controller = JControllerLegacy::getInstance('hwdMediaShare');

// Perform the Request task
$controller->execute(JFactory::getApplication()->input->get('task'));

// Redirect if set by the controller
$controller->redirect();
