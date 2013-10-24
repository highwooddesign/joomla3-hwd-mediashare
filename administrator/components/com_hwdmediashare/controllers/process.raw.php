<?php
/**
 * @version    SVN $Id: process.raw.php 425 2012-06-28 07:48:57Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      08-Nov-2011 17:23:26
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class HwdMediaShareControllerProcess extends JControllerLegacy {
        /**
	 * Method to run a process.
	 * @since	0.1
	 */
        function run()
        {
		// Check for request forgeries
		// JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$user	= JFactory::getUser();
		$task	= $this->getTask();
		$cids	= JRequest::getVar('cid', array(), '', 'array');

                // Load the embed library
                hwdMediaShareFactory::load('processes');
                $model = hwdMediaShareProcesses::getInstance();

                // Add embed code
                if (!$model->run($cids))
                {
                        $retval = array("success" => "0",
                                        "errors" => "1",
                                        "data" => array("total" => $model->_total, "error_msg" => $model->getError()));
                }
                else
                {
                        if ($model->_complete)
                        {
                                $retval = array("complete" => "1");
                        }
                        else
                        {
                                $retval = array("success" => "1",
                                                "errors" => "0",
                                                "data" => array("total" => $model->_total));
                        }
                }

                // Get the document object.
                $document =& JFactory::getDocument();

                // Set the MIME type for JSON output.
                $document->setMimeEncoding( 'application/json' );

                // Change the suggested filename.
                JResponse::setHeader( 'Content-Disposition', 'attachment; filename="process.json"' );

                // Output the JSON data.
                echo json_encode( $retval );

                // Exit the application.
                return;
        }
}