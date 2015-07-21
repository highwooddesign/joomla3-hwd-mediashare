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

class hwdMediaShareControllerAddMedia extends JControllerForm
{
        /**
	 * Method to process a file upload using FancyUpload2.
	 *
	 * @access  public
         * @return  void
	 */
        public function upload()
        {
                // Initialise variables.
                $app = JFactory::getApplication();
                $user = JFactory::getUser();
                $document = JFactory::getDocument();

                // Cross check and restore session from Flash request.
                if( $user->id == 0 )
                {
                        $tokenId	= $app->input->get('token', '', 'alnum');
                        $userId		= $app->input->get('uploaderid', '', 'int');

                        $user		= hwdMediaShareFactory::getUserFromTokenId($tokenId , $userId);

                        $session = JFactory::getSession();
                        $session->set('user', $user);
                }

		// Define input field to process from the request.
                $upload = new stdClass();
                $upload->input = 'Filedata';
                
                // Load HWD library.
                hwdMediaShareFactory::load('upload');
                $model = hwdMediaShareUpload::getInstance();
                
                // Process the upload.
                if (!$model->process($upload))
                {
                        // Need to output JSON for FancyUpload2.
                        $return = array(
                                'status' => '0',
                                'error' => $model->getError(),
                                'code' => 0
                        );
                }
                else
                {
                        // Need to output JSON for FancyUpload2.
                        $return = array(
                                'status' => '1',
                                'name' => $model->_item->title,
                                'id' => $model->_item->id
                        );
                }    

                // Set the MIME type for JSON output.
                $document->setMimeEncoding('application/json');

                // Output the JSON data.      
                echo json_encode($return);
                
		JFactory::getApplication()->close();
        }
        
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
                                                'task' => 'addmedia'
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
                                                'task' => 'addmedia',
                                                'name' => $HWDplatform->_item->title,
                                                'id' => $HWDplatform->_item->id                                            
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
