<?php
/**
 * @package     Joomla.site
 * @subpackage  Component.hwdmediashare
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class HwdMediaShareControllerMaintenance extends JControllerLegacy
{
	/**
	 * Method to run media processing tasks.
         * 
         * @access  public
	 * @return  void
	 */
        public function process()
        {
                // Initialise variables.
                $app = JFactory::getApplication();

                // Check token in request.
                if ($app->getCfg('secret') != $app->input->get('token', '', 'var'))
                {
                        die('Invalid secret token');
                }

                // Require HWD factory.
                JLoader::register('hwdMediaShareFactory', JPATH_BASE.'/components/com_hwdmediashare/libraries/factory.php');

                // Load process object
                hwdMediaShareFactory::load('processes');
                $model = hwdMediaShareProcesses::getInstance();                

                for ($i = 1; $i <= 50; $i++)
                {
                        $model->run();
                }
        }

	/**
	 * Method to run cdn file transfer matinenance.
         * 
         * @access  public
	 * @return  void
	 */        
        public function cdn()
        {
                // Initialise variables.
                $app = JFactory::getApplication();

                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                // Check token in request.
                if ($app->getCfg('secret') != $app->input->get('token', '', 'var'))
                {
                        die('Invalid secret token');
                }
                
                // Require HWD factory.
                JLoader::register('hwdMediaShareFactory', JPATH_BASE.'/components/com_hwdmediashare/libraries/factory.php');

                // Import HWD CDN plugin and run maintenance method.
                $pluginClass = 'plgHwdmediashare' . $config->get('cdn', 'cdn_amazons3');
                $pluginPath = JPATH_ROOT . '/plugins/hwdmediashare/' . $config->get('cdn', 'cdn_amazons3') . '/' . $config->get('cdn', 'cdn_amazons3') . '.php';
                if (file_exists($pluginPath))
                {
                        JLoader::register($pluginClass, $pluginPath);
                        $HWDcdn = call_user_func(array($pluginClass, 'getInstance'));
                        return $HWDcdn->maintenance();
                }
        }
}