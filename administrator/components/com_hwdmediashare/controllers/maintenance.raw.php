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

class HwdMediaShareControllerMaintenance extends JControllerLegacy
{
        
        /**
	 * Proxy for getModel.
	 * @return	void
	 */                
	public function getModel($name = 'Maintenance', $prefix = 'hwdMediaShareModel', $config = array())
	{
                $model = parent::getModel($name, $prefix, array('ignore_request' => true));
                return $model;
	}
        
        /**
	 * Method to run the maintenance.
	 * @return	void
	 */
        function run()
        {
		/*
		 * Note: we don't do a token check as we're fetching information
		 * asynchronously. This means that between requests the token might
		 * change, making it impossible for AJAX to work.
		 */

		// Initialise variables.
                $document = JFactory::getDocument();
                
		// Get maintenance to run from the request.
                $maintenance = $this->input->get('maintenance', '', 'word');

                // Get the model.
                $model = $this->getModel();

                // Perform maintenance task.
                if (!$model->$maintenance())
                {
                        // Set JSON output in JSEND spec http://labs.omniti.com/labs/jsend
                        $return = array(
                                'status' => 'fail',
                                'data' => array(
                                        'task' => $maintenance
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
                                        'task' => $maintenance
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