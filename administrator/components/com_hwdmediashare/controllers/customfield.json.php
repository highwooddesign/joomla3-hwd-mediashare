<?php
/**
 * @version    SVN $Id: customfield.json.php 544 2012-10-03 13:35:07Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * hwdMediaShare Controller
 */
class hwdMediaShareControllerCustomField extends JControllerForm
{
        /**
	 * Method to get the field parameters of a custom field type
	 * @since	0.1
	 */
        function fieldparameters()
        {
		$return = null;
                
                // Load field xml file
                $type = JRequest::getCmd('type');
                $field = JRequest::getInt('field');
                $xmlPath = JPATH_ROOT.'/components/com_hwdmediashare/libraries/fields/'.$type.'.xml';
                
                jimport( 'joomla.filesystem.file' );
                if( JFile::exists($xmlPath) )
		{                
                        // Load params from database
                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                        $row =& JTable::getInstance('CustomField', 'hwdMediaShareTable');
                        $row->load( $field );
                        $params = $row->params;

                        $params = new JParameter($params, $xmlPath);
			$return = $params->renderToArray();
		}

                // Get the document object.
                $document =& JFactory::getDocument();

                // Set the MIME type for JSON output.
                $document->setMimeEncoding( 'application/json' );

                // Change the suggested filename.
                // But throws Corrupted Content Error in FF
                // JResponse::setHeader( 'Content-Disposition', 'attachment; filename="field.json"' );

                // Output the JSON data.
                echo json_encode( $return );
        }
}
