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

class hwdMediaShareFieldsUrl
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
		$html = '<input type="' . $field->params->get('type', 'text') . '" id="field' . $field->id . '" name="field' . $field->id . '"';
                
		if (!empty($field->value))
		{
			$html .= ' value="' . htmlspecialchars($field->value, ENT_COMPAT, 'UTF-8') . '"';
		}
                  
                if ($field->required)
                {
			$html .= ' required aria-required="true"';
                }
                
                if ($field->params->get('readonly'))
                {
			$html .= ' readonly';
                }

                if ($field->params->get('disabled'))
                {
			$html .= ' disabled';
                }

                if ($field->params->get('maxlength'))
                {
			$html .= ' maxlength="' . (int) $field->params->get('maxlength');
                }
                
		$html .= ' />';
                
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
 
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                
		if (!$isValid = $utilities->validateUrl($value))
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
		return '<a rel="nofollow" href="' . $field->value . '" target="_blank">' . $field->value . '</a>';
        }     
}
