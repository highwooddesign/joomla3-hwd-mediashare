<?php
// no direct access
defined('_JEXEC') or die;

JLoader::register('hwdMediaShareFactory', JPATH_ROOT.'/components/com_hwdmediashare/libraries/factory.php');
JLoader::register('hwdMediaShareHelperRoute', JPATH_ROOT.'/components/com_hwdmediashare/helpers/route.php');
JLoader::register('modMediaImagesHelper', dirname(__FILE__).'/helper.php');

hwdMediaShareFactory::load('downloads');

$helper = new modMediaImagesHelper($module, $params);

$helper->addHead();
$items = $helper->getItems();
if (count($items) == 0) return;

require JModuleHelper::getLayoutPath('mod_media_images', $params->get('layout', 'default'));