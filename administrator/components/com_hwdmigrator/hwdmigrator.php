<?php
/**
 * @version    SVN $Id: hwdmigrator.php 1688 2013-10-16 15:09:12Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_hwdmigrator'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Require helper file
JLoader::register('hwdMigratorHelper', dirname(__FILE__).'/helpers/hwdmigrator.php');

// Import joomla controller library
jimport('joomla.application.component.controller');

// Get an instance of the controller prefixed by hwdMigrator
$controller = JControllerLegacy::getInstance('hwdMigrator');

// Perform the Request task
$controller->execute(JFactory::getApplication()->input->get('task'));

// Redirect if set by the controller
$controller->redirect();
