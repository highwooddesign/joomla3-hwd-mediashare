<?php
/**
 * @package     Joomla.site
 * @subpackage  Module.mod_hwd_cnn_videobox
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

JLoader::register('modHwdCnnVideoBoxHelper', dirname(__FILE__).'/helper.php');

$helper = new modHwdCnnVideoBoxHelper($module, $params);

require JModuleHelper::getLayoutPath('mod_hwd_cnn_videobox', $params->get('layout', 'default'));
