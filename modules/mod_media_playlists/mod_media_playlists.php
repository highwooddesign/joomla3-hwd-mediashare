<?php
/**
 * @package     Joomla.site
 * @subpackage  Module.mod_media_playlists
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

JLoader::register('modMediaPlaylistsHelper', dirname(__FILE__).'/helper.php');

$helper = new modMediaPlaylistsHelper($module, $params);

require JModuleHelper::getLayoutPath('mod_media_playlists', $params->get('layout', 'default'));
