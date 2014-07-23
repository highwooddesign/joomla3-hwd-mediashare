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

class hwdMediaShareFieldsSelect
{
    	/**
	 * Method to generate the input markup for the field type.
	 *
	 * @access  public
	 * @param   object  $field  The field to show.
	 * @return  string  The HTML markup.
	 */ 
        public function getInput($field)
	{
		// Add the opening input tag and main attributes attributes.
		$html = '<select id="field' . $field->id . '" name="field' . $field->id . '"';
                                  
                if ($field->required)
                {
			$html .= ' required aria-required="true"';
                }

                if ($field->params->get('disabled'))
                {
			$html .= ' disabled';
                }

                if ($field->params->get('multiple'))
                {
			$html .= ' multiple';
                }
                
		$html .= '>';
                
                // Default value
		$html .= '<option value="">' . JText::_('COM_HWDMS_LIST_SELECT_OPTION') . '</option>';
                
		if(!empty($field->options))
		{
			foreach($field->options as $option)
			{
                                $optionValue = JFilterOutput::stringURLSafe($option);
                                $selected = (trim($optionValue) == trim($field->value) ? ' selected="selected"' : '');
				$html .= '<option value="' . $optionValue . '"' . $selected . '>' . JText::_($option) . '</option>';
			}
		}
                
		$html .= '</select>';               
                
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
	 * Method to display a field value.
         *
	 * @access  public
	 * @param   object  $field  The field to validate.
	 * @return  string  The markup to display the field value.
	 */ 
	public function display($field)
        {
		if(!empty($field->options))
		{
			foreach($field->options as $option)
			{
                                $optionValue = JFilterOutput::stringURLSafe($option);
                                if (trim($optionValue) == trim($field->value))
                                {
                                        // Remove whitespaces and linebreaks.
                                        return trim($option);
                                }
			}
		}

		return $field->value;         
        }
}
