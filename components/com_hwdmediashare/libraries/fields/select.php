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
	 * Method to generate the input markup for the text field type.
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
                
		if(!empty($field->options))
		{
			foreach($field->options as $option)
			{
                                $selected = (trim($option) == trim($field->value) ? ' selected="selected"' : '');
				$html .= '<option value="' . $option . '"' . $selected . '>' . JText::_($option) . '</option>';
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
}
