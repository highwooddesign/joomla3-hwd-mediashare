<?php
/**
 * @version    SVN $Id: maintenance.raw.php 277 2012-03-28 10:03:31Z dhorsfall $
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
        function run()
        {
		// Check for request forgeries
		// JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$user	= JFactory::getUser();
		$task	= $this->getTask();
                $maintenance = JRequest::getWord('maintenance');

                // Load the embed library
                hwdMediaShareFactory::load('maintenance');
                $model = hwdMediaShareMaintenance::getInstance();
                
                // Add embed code
                if (!$model->$maintenance())
                {
                        $retval = array("success" => "0",
                                        "errors" => "1",
                                        "data" => array("task" => $maintenance, "error_msg" => $model->getError()));
                }
                else
                {
                        $retval = array("success" => "1",
                                        "errors" => "0",
                                        "data" => array("task" => $maintenance));
                }

                // Get the document object.
                $document =& JFactory::getDocument();

                // Set the MIME type for JSON output.
                $document->setMimeEncoding( 'application/json' );

                // Change the suggested filename.
                JResponse::setHeader( 'Content-Disposition', 'attachment; filename="'.$maintenance.'.json"' );

                // Output the JSON data.
                echo json_encode( $retval );

                // Exit the application.
                return;
        }
}