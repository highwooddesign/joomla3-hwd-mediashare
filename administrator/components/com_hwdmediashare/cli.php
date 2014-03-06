<?php
/**
 * @package     Joomla.administrator
 * @subpackage  Component.hwdmediashare
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

// Define some things
define('_JEXEC', 1);
define('_JCLI', 1);

define('JPATH_BASE', dirname(__FILE__).'/../../..');
define('JPATH_SITE', JPATH_BASE);
define('JPATH_ROOT', JPATH_BASE);
define('JPATH_CACHE', JPATH_BASE . '/cache');
define('JPATH_PLATFORM', JPATH_BASE . '/libraries');
define('JPATH_ADMINISTRATOR', JPATH_BASE . '/administrator');
define('JPATH_INSTALLATION', JPATH_BASE . '/installation');
                                
if (file_exists(JPATH_PLATFORM . '/import.php'))                    require_once JPATH_PLATFORM.'/import.php';
if (file_exists(JPATH_PLATFORM . '/joomla/observer/mapper.php'))    require_once JPATH_PLATFORM.'/joomla/observer/mapper.php';

jimport( 'joomla.application.cli' );
jimport( 'joomla.database.database' );
jimport( 'joomla.database.table' );
jimport( 'joomla.database.table.extension' );
jimport( 'joomla.filesystem.file' );
jimport( 'joomla.filesystem.folder' );

class hwdMediaShare extends JApplicationCli
{
        function process()
        {
                // Require hwdMediaShare factory
                JLoader::register('JApplication', JPATH_PLATFORM.'/joomla/application/application.php');
                JLoader::register('JApplicationHelper', JPATH_PLATFORM.'/joomla/application/helper.php');
                JLoader::register('JRequest', JPATH_PLATFORM.'/joomla/environment/request.php');
                JLoader::register('JComponentHelper', JPATH_PLATFORM.'/joomla/application/component/helper.php');
                JLoader::register('JComponentHelper', JPATH_PLATFORM.'/legacy/component/helper.php');  // Register in J3
                JLoader::register('JComponentHelper', JPATH_PLATFORM.'/cms/component/helper.php');  // Register in J3.2
                JLoader::register('hwdMediaShareFactory', JPATH_BASE.'/components/com_hwdmediashare/libraries/factory.php');

                // Load process object
                hwdMediaShareFactory::load('processes');
                $model = hwdMediaShareProcesses::getInstance();
                                        
                $args = $GLOBALS['argv'];
                $processes = array_slice($args,1);
                if (count($processes) > 1)
                {
                        foreach($processes as $process)
                        {   
                                $process = (int) $process;
                                if ($process > 0)
                                {
                                        //$this->out($arg);
                                        $model->run(array($process));
                                }

                        }   
                }
                else
                {
                        for ($i = 1; $i <= 50; $i++)
                        {
                                $model->run();
                        }
                }
        }

        function cdn()
        {
                // Require hwdMediaShare factory
                JLoader::register('JRequest', JPATH_PLATFORM.'/joomla/environment/request.php');
                JLoader::register('JParameter', JPATH_PLATFORM.'/joomla/html/parameter.php');
                JLoader::register('JComponentHelper', JPATH_PLATFORM.'/joomla/application/component/helper.php');
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

        function createTestFile()
        {
                // Create test file
                $filename = JPATH_SITE.'/tmp/hwdms.background';
                $buffer = '';
                JFile::write($filename, $buffer);
        }

        public function execute()
        {
                $args = $GLOBALS['argv'];
                if ($args[1] == 'test')
                {
                        $this->createTestFile();
                }
                else if ($args[1] == 'cdn')
                {
                        $this->cdn();
                }
                else if ($args[1] == 'process')
                {
                        $this->process();
                }
        }
}

JApplicationCli::getInstance('hwdMediaShare')->execute();