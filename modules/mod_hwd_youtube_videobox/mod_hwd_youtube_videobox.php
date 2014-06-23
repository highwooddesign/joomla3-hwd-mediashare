<?php
/**
 * @package     Joomla.site
 * @subpackage  Module.mod_hwd_youtube_videobox
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

JLoader::register('modHwdYoutubeVideoBoxHelper', dirname(__FILE__).'/helper.php');

$helper = new modHwdYoutubeVideoBoxHelper($module, $params);

require JModuleHelper::getLayoutPath('mod_hwd_youtube_videobox', $params->get('layout', 'default'));
