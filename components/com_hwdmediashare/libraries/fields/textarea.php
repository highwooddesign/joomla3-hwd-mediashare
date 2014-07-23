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

class hwdMediaShareFieldsTextarea
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
		$html = '<textarea id="field' . $field->id . '" name="field' . $field->id . '"';

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
			$html .= ' maxlength="' . (int) $field->params->get('maxlength') . '"';
                }
                
                if ($field->params->get('rows'))
                {
			$html .= ' rows="' . (int) $field->params->get('rows') . '"';
                }
                
                if ($field->params->get('cols'))
                {
			$html .= ' cols="' . (int) $field->params->get('cols') . '"';
                }
                
		$html .= '>';
                
		if (!empty($field->value))
		{
			$html .= htmlspecialchars($field->value, ENT_COMPAT, 'UTF-8');
		}
                
		$html .= '</textarea>';

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
		return '<div style="display:inline-block;vertical-align:top">' . nl2br($field->value) . '</div>';
        }        
}
