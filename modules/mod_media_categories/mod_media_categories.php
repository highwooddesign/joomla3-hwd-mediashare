<?php
/**
 * @package     Joomla.site
 * @subpackage  Module.mod_media_categories
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

JLoader::register('modMediaCategoriesHelper', dirname(__FILE__).'/helper.php');

$helper = new modMediaCategoriesHelper($module, $params);

require JModuleHelper::getLayoutPath('mod_media_categories', $params->get('layout', 'default'));
