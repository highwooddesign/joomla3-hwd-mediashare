<?php
/**
 * @version    SVN $Id: maintenance.raw.php 1274 2013-03-13 14:14:36Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class HwdMediaShareControllerMaintenance extends JControllerLegacy {
        /**
	 * Method to run the mainenance
	 * @since	0.1
	 */
        function process()
        {
                $app =& JFactory::getApplication();

                // Check token
                if ($app->getCfg('secret') != JRequest::getVar('token'))
                {
                    die('Invalid secret token');
                }

                // Require hwdMediaShare factory
                JLoader::register('hwdMediaShareFactory', JPATH_BASE.'/components/com_hwdmediashare/libraries/factory.php');

                // Load process object
                hwdMediaShareFactory::load('processes');
                $model = hwdMediaShareProcesses::getInstance();                

                for ($i = 1; $i <= 50; $i++)
                {
                        $model->run();
                }
        }

        function cdn()
        {
                $app =& JFactory::getApplication();

                // Check token
                if ($app->getCfg('secret') != JRequest::getVar('token'))
                {
                    die('Invalid secret token');
                }
                
                // Require hwdMediaShare factory
                JLoader::register('hwdMediaShareFactory', JPATH_BASE.'/components/com_hwdmediashare/libraries/factory.php');

                // Load hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                $pluginClass = 'plgHwdmediashare'.$config->get('cdn','cdn_amazons3');
                $pluginPath = JPATH_ROOT.'/plugins/hwdmediashare/'.$config->get('cdn','cdn_amazons3').'/'.$config->get('cdn','cdn_amazons3').'.php';

                // Import hwdMediaShare CDN plugin and run maintenance method
                if (file_exists($pluginPath))
                {
                        JLoader::register($pluginClass, $pluginPath);
                        $cdn = call_user_func(array($pluginClass, 'getInstance'));
                        return $cdn->maintenance();
                }
        }
}