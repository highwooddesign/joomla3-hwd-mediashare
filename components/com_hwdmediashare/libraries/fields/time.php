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

class hwdMediaShareFieldsTime
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
		$html	= '';
		$hour	= '';
		$minute	= '';
		$second	= '';

                // Get the saved time value.
		if(!empty($field->value))
		{
                        $value	= explode(':', $field->value);
                        
                        if (count($value) == 3)
                        {
                                $hour 	= (int) $value[0] <= 24 ? sprintf("%02s", (int) $value[0]) : '00';
                                $minute = (int) $value[1] <= 59 ? sprintf("%02s", (int) $value[1]) : '00';
                                $second = (int) $value[2] <= 59 ? sprintf("%02s", (int) $value[2]) : '00';
                        }
		}

                // Get the markup for the hour input.
		$html  .= '<div class="pull-left">';
		$html  .= '<label id="' . $field->id . '-hh-lbl" for="field' . $field->id . '-hh" class="hide">' . (string) $field->name . ' ' . JText::_('COM_HWDMS_HOUR_FORMAT') . '</label>';
		$html  .= '<select id="field' . $field->id . '-hh" name="field' . $field->id . '[]" class="input-small"';
                                  
                if ($field->required)
                {
			$html .= ' required aria-required="true"';
                }
                
		$html  .= '>';
                $html  .= '<option value="">' . JText::_('COM_HWDMS_HOUR_FORMAT') . '</option>';
		for($i=0; $i<24; $i++)
		{
                        $loopHour = sprintf("%02s", $i);
                        $selected = (trim($loopHour) == trim($hour) ? ' selected="selected"' : '');
                        $html .= '<option value="' . $loopHour . '"' . $selected . '>' . $loopHour . '</option>';
		}
		$html  .= '</select>';
		$html  .= '</div>';
                
                // Get the markup for the minute input.
		$html  .= '<div class="pull-left">';
		$html  .= '<label id="' . $field->id . '-mm-lbl" for="field' . $field->id . '-mm" class="hide">' . (string) $field->name . ' ' . JText::_('COM_HWDMS_MINUTE_FORMAT') . '</label>';
		$html  .= '<select id="field' . $field->id . '-mm" name="field' . $field->id . '[]" class="input-small"';
                                  
                if ($field->required)
                {
			$html .= ' required aria-required="true"';
                }
                
		$html  .= '>';
                $html  .= '<option value="">' . JText::_('COM_HWDMS_MINUTE_FORMAT') . '</option>';
		for($i=0; $i<60; $i++)
		{
                        $loopMinute = sprintf("%02s", $i);                    
                        $selected = (trim($loopMinute) == trim($minute) ? ' selected="selected"' : '');
                        $html .= '<option value="' . $loopMinute . '"' . $selected . '>' . $loopMinute . '</option>';
		}
		$html  .= '</select>';
		$html  .= '</div>';
                
                // Get the markup for the second input.
		$html  .= '<div class="pull-left">';
		$html  .= '<label id="' . $field->id . '-ss-lbl" for="field' . $field->id . '-ss" class="hide">' . (string) $field->name . ' ' . JText::_('COM_HWDMS_SECOND_FORMAT') . '</label>';
		$html  .= '<select id="field' . $field->id . '-ss" name="field' . $field->id . '[]" class="input-small"';
                                  
                if ($field->required)
                {
			$html .= ' required aria-required="true"';
                }
                
		$html  .= '>';
                $html  .= '<option value="">' . JText::_('COM_HWDMS_SECOND_FORMAT') . '</option>';
		for($i=0; $i<60; $i++)
		{
                        $loopSecond = sprintf("%02s", $i);                    
                        $selected = (trim($loopSecond) == trim($second) ? ' selected="selected"' : '');
                        $html .= '<option value="' . $loopSecond . '"' . $selected . '>' . $loopSecond . '</option>';
		}
		$html  .= '</select>';
		$html  .= '</div>';

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
		$finalvalue = '';
		if(is_array($value))
		{
			if (empty($value[0]) || empty($value[1]) || empty($value[2]))
			{
				$finalvalue = '';
			}
			else
			{
                                $hour 	= (int) $value[0] <= 24 ? sprintf("%02s", (int) $value[0]) : '00';
				$minute = (int) $value[1] <= 59 ? sprintf("%02s", (int) $value[1]) : '00';
 				$second = (int) $value[2] <= 59 ? sprintf("%02s", (int) $value[2]) : '00';

				$finalvalue = $hour . ':' . $minute . ':' . $second;
			}
		}               

		return $finalvalue;
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
		return $field->value;         
        }
}