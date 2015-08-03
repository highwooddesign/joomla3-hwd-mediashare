<?php
/**
 * @package     Joomla.site
 * @subpackage  Module.mod_hwd_soundcloud_jplayer
 *
 * @copyright   (C) 2014 Joomlabuzz.com
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

JLoader::register('modHwd_soundcloud_jplayerHelper', dirname(__FILE__).'/helper.php');

$helper = new modHwd_soundcloud_jplayerHelper($module, $params);

require JModuleHelper::getLayoutPath('mod_hwd_soundcloud_jplayer', $params->get('layout', 'default'));

