<?php
// no direct access
defined('_JEXEC') or die;

JLoader::register('hwdMediaShareFactory', JPATH_ROOT.'/components/com_hwdmediashare/libraries/factory.php');
JLoader::register('hwdMediaShareHelperRoute', JPATH_ROOT.'/components/com_hwdmediashare/helpers/route.php');
JLoader::register('modMediaItemHelper', dirname(__FILE__).'/helper.php');

hwdMediaShareFactory::load('downloads');

$helper = new modMediaItemHelper($module, $params);

$helper->addHead();
$item = $helper->getItem();
if (!$item) return;
          
require JModuleHelper::getLayoutPath('mod_media_item', $params->get('layout', 'default'));