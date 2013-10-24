<?php
/**
 * @version    SVN $Id: migrate.raw.php 319 2012-04-16 17:16:34Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

class HwdMigratorControllerMigrate extends JControllerAdmin
{
        /**
	 * Proxy for getModel.
	 * @since	0.1
	 */
	public function getModel($name = 'Migrate', $prefix = 'hwdMigratorModel')
	{
                $model = parent::getModel($name, $prefix, array('ignore_request' => true));
                return $model;
	}
        
        /**
	 * Method to run the mainenance
	 * @since	0.1
	 */
        function run()
        {
		// Initialise variables.
		$user	 = JFactory::getUser();
		$task	 = $this->getTask();
                $migrate = JRequest::getWord('migrate');

                $model = $this->getModel();

                // Add embed code
                if (!$model->$migrate())
                {
                        $retval = array("success" => "0",
                                        "errors" => "1",
                                        "data" => array("task" => $migrate, "error_msg" => $model->getError()));
                }
                else
                {
                        $retval = array("success" => "1",
                                        "errors" => "0",
                                        "data" => array("task" => $migrate));
                }

                // Get the document object.
                $document =& JFactory::getDocument();

                // Set the MIME type for JSON output.
                $document->setMimeEncoding( 'application/json' );

                // Change the suggested filename.
                JResponse::setHeader( 'Content-Disposition', 'attachment; filename="'.$migrate.'.json"' );

                // Output the JSON data.
                echo json_encode( $retval );

                // Exit the application.
                return;
        }
}