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

class hwdMediaShareFieldsRadio
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
		$document = JFactory::getDocument();
		$document->addStyleDeclaration('
.radio-toolbar input[type="radio"] {
    display:none;
}
.radio-toolbar label {
    display:inline-block;
    background-color:#ddd;
    padding:4px 11px;
}
.radio-toolbar input[type="radio"]:checked + label {
    background-color:#bbb;
}          
');
		$html	= '';

		if(!empty($field->options))
		{
                        $html  .= '<div class="radio-toolbar">';
			foreach($field->options as $option)
			{
                                $optionValue = JFilterOutput::stringURLSafe($option);
                                $selected = (trim($optionValue) == trim($field->value) ? ' checked' : '');
                                
                                // Get the markup for the hour input.
                                $html  .= '<input type="radio" id="' . $field->id . '-' . $optionValue . '" name="field' . $field->id . '" value="' . $optionValue . '"' . $selected;
                                
                                if ($field->required)
                                {
                                        $html .= ' required aria-required="true"';
                                }

                                if ($field->params->get('disabled'))
                                {
                                        $html .= ' disabled';
                                }

                                $html  .= '><label id="' . $field->id . '-' . $optionValue . '-lbl" for="' . $field->id . '-' . $optionValue . '">' . $option . '</label>';                
			}
                        $html  .= '</div>';
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
                                        return $option;
                                }
			}
		}

		return $field->value;        
        }
}