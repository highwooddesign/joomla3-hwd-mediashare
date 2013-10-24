<?php
/**
 * @version    SVN $Id: checkbox.php 564 2012-10-12 12:55:06Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  (C) 2008 by Slashes & Dots Sdn Bhd (JomSocial)
 * @copyright  (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class hwdMediaShareFieldsCheckbox
{	
        public function _translateValue( &$string )
	{
		$string	= JText::_( $string );
	}

	/**
	 * Method to format the specified value for text type
	 **/	 	
	public function getFieldData( $field )
	{
                $value = $field['value'];
		
		// Since multiple select has values separated by commas, we need to replace it with <br />.
		$fieldArray	= explode ( ',' , $value );
		
		array_walk($fieldArray, array('hwdMediaShareFieldsCheckbox', '_translateValue'));
		
		$fieldValue = implode('<br />', $fieldArray);				
		return $fieldValue;
	}	
	
	public function getFieldHTML( $field , $required )
	{
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                
		$class			= ($field->required == 1) ? ' required validate-custom-checkbox' : '';
		$lists			= is_array( $field->value ) ? $field->value : explode(',', $field->value);
		$html			= '';
		$elementSelected	= 0;
		$elementCnt	        = 0;

		$cnt = 0;
		
		$class	= !empty( $field->tooltip ) ? ' hasTip ' : '';
		$html	.= '<div class="' . $class . '" style="display: inline-block;" title="' . JText::_( $field->name ) . '::' . $utilities->escape( JText::_( $field->tooltip ) ). '">';
				
		if( is_array( $field->options ) )
		{
			foreach( $field->options as $option )
			{
                                // Remove any line breaks
                                $option = (string)str_replace(array("\r", "\r\n", "\n"), '', $option);                            
				$selected = in_array( JString::trim( $option ) , $lists ) ? ' checked="checked"' : '';
				
				if( empty( $selected ) )
				{
					$elementSelected++;
				}
				
				$html .= '<label class="lblradio-block">';
				$html .= '<input type="checkbox" name="field' . $field->id . '[]" value="' . $option . '"' . $selected . ' class="checkbox '.$class.'" style="margin: 0 5px 5px 0;" />';
				$html .= JText::_( $option ) . '</label>';
				$elementCnt++;
			}
		}
		
		$html   .= '<span id="errfield'.$field->id.'msg" style="display: none;">&nbsp;</span>';
		$html	.= '</div>';		
		
		return $html;
	}
	
	public function isValid( $value , $required )
	{
		if( $required && empty($value))
		{
			return false;
		}		
		return true;
	}
	
	public function formatdata( $value )
	{
		$finalvalue = '';
		if(!empty($value))
		{
			foreach($value as $listValue)
                        {
				$finalvalue	.= $listValue . ',';
			}
		}	
                
                // Remove any line breaks
                $finalvalue = (string)str_replace(array("\r", "\r\n", "\n"), '', $finalvalue);
                                
		return $finalvalue;	
	}
}