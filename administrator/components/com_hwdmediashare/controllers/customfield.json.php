<?php
/**
 * @package     Joomla.administrator
 * @subpackage  Component.hwdmediashare
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */
/**
 * UNFINISHED
 */ 
defined('_JEXEC') or die;

class hwdMediaShareControllerCustomField extends JControllerForm
{
        /**
	 * Method to get the field parameters of a custom field type, in JSON format, for AJAX requests
	 * @return	json
	 */
        function fieldparameters()
        {
		/*
		 * Note: we don't do a token check as we're fetching information
		 * asynchronously. This means that between requests the token might
		 * change, making it impossible for AJAX to work.
		 */
            
                // Get the document object.
                $document = JFactory::getDocument();
                
                $return = array();
                
                // Load field xml file.
                $type = $this->input->get('type', '', 'cmd');
                $field = $this->input->get('field', '', 'int');
                $xmlPath = JPATH_ROOT.'/components/com_hwdmediashare/libraries/fields/'.$type.'.xml';
                
                jimport('joomla.filesystem.file');
                if(JFile::exists($xmlPath))
		{   
                        $form = JForm::getInstance($type, $xmlPath, array('control' => 'params'));

                        if (empty($form))
                        {
                                $this->setError(JText::_('COM_HWDMS_ERROR_FAILED_TO_LOAD_FORM'));
                                return false;
                        }

                        // Load params from database
                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                        $row = JTable::getInstance('CustomField', 'hwdMediaShareTable');
                        $row->load($field);
                        
                        // Bind params to form
                        $form->bind($row->params); 

                        foreach($form->getFieldset('details') as $field)
                        {
                                $return[$field->name]['label'] = $field->label;
                                $return[$field->name]['input'] = $field->input;
                        }
		}


                // Set the MIME type for JSON output.
                $document->setMimeEncoding( 'application/json' );

                // Output the JSON data.      
                echo json_encode($return);
                
		JFactory::getApplication()->close();
        }
}
