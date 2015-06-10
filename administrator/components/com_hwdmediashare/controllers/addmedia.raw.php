<?php
/**
 * @package     Joomla.administrator
 * @subpackage  Component.hwdmediashare
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class hwdMediaShareControllerAddMedia extends JControllerForm
{       
        /**
	 * Method to process a platform upload.
	 *
	 * @access  public
         * @return  void
	 */
        public function platform()
        {
		// Initialise variables.
                $document = JFactory::getDocument();
                            
                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                $pluginClass = 'plgHwdmediashare' . $config->get('platform');
                $pluginPath = JPATH_ROOT . '/plugins/hwdmediashare/' . $config->get('platform') . '/' . $config->get('platform') . '.php';
                if (file_exists($pluginPath))
                {
                        JLoader::register($pluginClass, $pluginPath);
                        $HWDplatform = call_user_func(array($pluginClass, 'getInstance'));

                        if (!$HWDplatform->addUpload())
                        {
                                // Set JSON output in JSEND spec http://labs.omniti.com/labs/jsend
                                $return = array(
                                        'status' => 'fail',
                                        'data' => array(
                                                'task' => 'addUpload'
                                        ),
                                        'message' => $HWDplatform->getError()
                                );
                        }
                        else
                        {
                                // Set JSON output in JSEND spec http://labs.omniti.com/labs/jsend
                                $return = array(
                                        'status' => 'success',
                                        'data' => array(
                                                'task' => 'addUpload'
                                        ),
                                        'message' => null
                                );
                        } 

                        // Set the MIME type for JSON output.
                        $document->setMimeEncoding( 'application/json' );

                        // Output the JSON data.      
                        echo json_encode($return);

                        JFactory::getApplication()->close();                       
                }
        }
}
