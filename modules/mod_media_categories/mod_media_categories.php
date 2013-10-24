<?php
// no direct access
defined('_JEXEC') or die;

JLoader::register('hwdMediaShareFactory', JPATH_ROOT.'/components/com_hwdmediashare/libraries/factory.php');
JLoader::register('hwdMediaShareHelperRoute', JPATH_ROOT.'/components/com_hwdmediashare/helpers/route.php');
JLoader::register('modMediaCategoriesHelper', dirname(__FILE__).'/helper.php');

hwdMediaShareFactory::load('downloads');

$helper = new modMediaCategoriesHelper($module, $params);

$helper->addHead();
$items = $helper->getItems();
if (count($items) == 0) return;

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
$startLevel = reset($items)->getParent()->level;

require JModuleHelper::getLayoutPath('mod_media_categories', $params->get('layout', 'default'));