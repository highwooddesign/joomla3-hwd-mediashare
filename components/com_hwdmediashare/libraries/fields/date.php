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

class hwdMediaShareFieldsDate
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
                // Get a date object based on the saved value.
                $date = JFactory::getDate($field->value);
                    
                $attribs = array();
                if ($field->required)
                {
			$attribs['required'] = 'required';
			//$html .= ' required aria-required="true"';
                }
                
                if ($field->params->get('readonly'))
                {
			$attribs['readonly'] = 'readonly';
                }

                if ($field->params->get('disabled'))
                {
			$attribs['disabled'] = 'disabled';
                }
                
            	// Including fallback code for HTML5 non supported browsers.
		JHtml::_('jquery.framework');
		JHtml::_('script', 'system/html5fallback.js', false, true);

                // Return the calander input.
            	return JHtml::_('calendar', $date->toSql(), 'field' . $field->id, 'field' . $field->id, '%Y-%m-%d', $attribs);
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
                // Get a date object based on the input value.
                $date = JFactory::getDate($value);

                // Transform the date string to a unix timestamp.
                return $date->format('Y-m-d', false, false);
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
                // Get a date object based on the input value.
                $date = JFactory::getDate($field->value);
                
                // Transform the date string to a unix timestamp.
                return $date->format($field->params->get('date_format', 'Y-m-d'), false, false);        
        }      
}
