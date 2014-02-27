<?php
/**
 * @package    HWD.MediaApps
 * @copyright  Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JLoader::register('modHwdYoutubeVideoBoxHelper', dirname(__FILE__).'/helper.php');

// Get a reference to the global cache object.
$cache = & JFactory::getCache();
$cache->setCaching( 1 );

$helper = new modHwdYoutubeVideoBoxHelper($module, $params);

$helper->addHead();
//$items = $helper->getItems();
$items = $cache->call( array( $helper, 'getItems' ) );
if (count($items) == 0) return;

require JModuleHelper::getLayoutPath('mod_hwd_youtube_videobox', $params->get('layout', 'default'));