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
         * in JSON format, for AJAX requests
	 *
	 * @access	public
         * @return      void
	 */
        public function fieldparameters()
        {
		/*
		 * Note: we don't do a token check as we're fetching information
		 * asynchronously. This means that between requests the token might
		 * change, making it impossible for AJAX to work.
		 */
            
		// Initialise variables.
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

                        // Load the customfield table.
                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                        $table = JTable::getInstance('CustomField', 'hwdMediaShareTable');
                        
                        // Load the field.
                        $table->load($field);
                        
                        // Convert params to registry and bind to the form. 
                        if (property_exists($table, 'params'))
                        {
                                $registry = new JRegistry;
                                $registry->loadString($table->params);
                                $table->params = $registry;
                                $form->bind($table->params); 
                        }   

                        /*
                         * Returning the markup for the label and input isn't ideal, so 
                         * this should be reviewed in the future. 
                         */
                        
                        // Add the label and input to the return array.
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
