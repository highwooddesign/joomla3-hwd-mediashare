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

class hwdMediaShareFieldsCheckbox
{	
    	/**
	 * Method to generate the input markup for the text field type.
	 *
	 * @access  public
	 * @param   object  $field  The field to show.
	 * @return  string  The HTML markup.
	 */ 
        public function getInput($field)
	{
		$document = JFactory::getDocument();
		$document->addStyleDeclaration('
.checkbox-toolbar input[type="checkbox"] {
    float:left;
    vertical-align:middle;
}
.checkbox-toolbar label {
    padding:2px 20px;
}      
');
		$html	= '';

		if(!empty($field->options))
		{
                        // Get an array of the saved values.
                        $savedValues = explode(',', $field->value);
                        
                        // Build the checkbox fieldset.
                        $html  .= '<fieldset class="checkbox-toolbar">';
			foreach($field->options as $option)
			{
                                $optionValue = JFilterOutput::stringURLSafe($option);
                                $selected = (in_array($optionValue, $savedValues) ? ' checked' : '');
                                
                                // Get the markup for the hour input.
                                $html  .= '<input type="checkbox" id="' . $field->id . '-' . $optionValue . '" name="field' . $field->id . '[]" value="' . $optionValue . '"' . $selected;

                                if ($field->params->get('disabled'))
                                {
                                        $html .= ' disabled';
                                }

                                $html  .= '><label id="' . $field->id . '-' . $optionValue . '-lbl" for="' . $field->id . '-' . $optionValue . '">' . $option . '</label>';                
			}
                        $html  .= '</fieldset>';
		}
                
                return $html;
	}

    	/**
	 * Method to check field value is valid.
	 *
	 * @access  public
	 * @param   object  $field  The field to show.
	 * @param   mixed   $value  The valut to check.
	 * @return  boolean True for valid, false for invalid.
	 */ 
	public function isValid($field, $value)
	{
		if($field->required && empty($value))
		{
			return false;
		}
                
		return true;
	}

    	/**
	 * Method to format the input value.
	 *
	 * @access  public
	 * @param   mixed   $value  The valut to check.
	 * @return  mixed   The formatted input value.
	 */           
	public function formatdata($value)
	{  
                // If the field value isn't an array then return an empty array.
                if (!is_array($value)) return array();
            
		$finalvalue = '';
                        
                foreach($value as &$item)
                {
                        $item = JFilterOutput::stringURLSafe($item);
                }

                return implode(',', $value);
	}
        
    	/**
	 * Method to get the type of filter for this field.
	 *
	 * @access  public
	 * @return  string  The filter.
	 */ 
	public function getFilter()
	{
		return 'array';
	}   

	/**
	 * Method to display a field value.
         *
	 * @access  public
	 * @param   object  $field  The field to validate.
	 * @return  string  The markup to display the field value.
	 */ 
	public function display($field)
        {
                $return = array();
                
                // Get an array of the saved values.
                $savedValues = explode(',', $field->value);
                        
		if(!empty($field->options))
		{
			foreach($field->options as $option)
			{
                                $optionValue = JFilterOutput::stringURLSafe($option);
                                if (in_array($optionValue, $savedValues))
                                {
                                        // Remove whitespaces and linebreaks.
                                        $return[] = trim($option);
                                }
			}
		}

		return implode(', ', $return);  
        } 
}