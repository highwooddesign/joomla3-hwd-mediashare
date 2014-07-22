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

class hwdMediaShareFieldsCountry 
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
                
		// Default option.
                $html .= '<option value="">' . JText::_('COM_HWDMS_LIST_SELECT_COUNTRY') . '</option>';                            
                                
		jimport('joomla.filesystem.file');
		$file = JPATH_ROOT.'/components/com_hwdmediashare/libraries/fields/countries.xml';
		if(JFile::exists($file))
		{
                        $parser         = JFactory::getXML($file);                                        
			$element	= $parser->countries;
			$countries	= $element->children();
                        
                        foreach($countries as $country)
                        {
                                $selected = (trim($country->code) == trim($field->value) ? ' selected="selected"' : '');
				$html .= '<option value="' . $country->code . '"' . $selected . '>' . JText::_($country->name) . '</option>';                            
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
	 * Method to format the specified value for text type
	 **/	 	
	public function getFieldData( $field )
	{
		$value = $field['value'];
		if( empty( $value ) )
			return $value;
		
		return $value;
	}        

}