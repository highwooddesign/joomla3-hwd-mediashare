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

class HwdMediaShareControllerProcess extends JControllerLegacy 
{
        /**
	 * Method to run a process.
	 *
	 * @access  public
         * @return  void
	 */
        public function run()
        {
		/*
		 * Note: we don't do a token check as we're fetching information
		 * asynchronously. This means that between requests the token might
		 * change, making it impossible for AJAX to work.
		 */

		// Initialise variables.
                $document = JFactory::getDocument();
                
		// Get items to process from the request.
		$cid = $this->input->get('cid', array(), 'array');
                
                // Load HWD library.
                hwdMediaShareFactory::load('processes');
                $model = hwdMediaShareProcesses::getInstance();

                // Perform maintenance task.
                if (!$model->run($cid))
                {
                        // Set JSON output in JSEND spec http://labs.omniti.com/labs/jsend
                        $return = array(
                                'status' => 'fail',
                                'data' => array(
                                        'complete' => $model->_complete,
                                        'total' => $model->_total
                                ),
                                'message' => $model->getError()
                        );
                }
                else
                {
                        // Set JSON output in JSEND spec http://labs.omniti.com/labs/jsend
                        $return = array(
                                'status' => 'success',
                                'data' => array(
                                        'complete' => $model->_complete,
                                        'total' => $model->_total
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

