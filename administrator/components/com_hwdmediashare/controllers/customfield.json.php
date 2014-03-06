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

class hwdMediaShareControllerCustomField extends JControllerForm
{
        /**
	 * Method to get the field parameters of a custom field type
	 * @return	json
	 */
        function fieldparameters()
        {
		$return = null;
                
                // Load field xml file
                $type = JRequest::getCmd('type');
                $field = JRequest::getInt('field');
                $xmlPath = JPATH_ROOT.'/components/com_hwdmediashare/libraries/fields/'.$type.'.xml';
                
                jimport( 'joomla.filesystem.file' );
                if(JFile::exists($xmlPath))
		{   
                        $form = JForm::getInstance($type, $xmlPath);

                        if (empty($form))
                        {
                                $this->setError(JText::_('COM_HWDMS_ERROR_FAILED_TO_LOAD_FORM'));
                                return false;
                        }

                        // Load params from database
                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                        $row = JTable::getInstance('CustomField', 'hwdMediaShareTable');
                        $row->load( $field );
                        
                        $form->bind($row->params); 
                        
                        $return = array();
                        foreach($form->getFieldset('details') as $field)
                        {
                                $return[$field->name]['label'] = $field->label;
                                $return[$field->name]['input'] = $field->input;
                        }
		}

                // Get the document object
                $document = JFactory::getDocument();

                // Set the MIME type for JSON output
                $document->setMimeEncoding( 'application/json' );

                // Change the suggested filename.
                // But throws Corrupted Content Error in FF
                // JResponse::setHeader( 'Content-Disposition', 'attachment; filename="field.json"' );

                // Output the JSON data.      
                echo json_encode($return);
        }
}
