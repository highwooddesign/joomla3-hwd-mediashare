<?php
/**
 * @package     Joomla.site
 * @subpackage  Module.mod_media_activities
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

JLoader::register('modMediaActivitiesHelper', dirname(__FILE__).'/helper.php');

$helper = new modMediaActivitiesHelper($module, $params);

require JModuleHelper::getLayoutPath('mod_media_activities', $params->get('layout', 'default'));
