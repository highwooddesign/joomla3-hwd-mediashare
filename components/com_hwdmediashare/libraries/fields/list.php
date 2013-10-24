<?php
/**
 * @version    SVN $Id: list.php 565 2012-10-12 12:55:40Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  (C) 2008 by Slashes & Dots Sdn Bhd (JomSocial)
 * @copyright  (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class hwdMediaShareFieldsList
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
		$fieldArray = explode ( ',' , $value );
		
		array_walk($fieldArray, array('hwdMediaShareFieldsList', '_translateValue'));
		
		$fieldValue = implode('<br />', $fieldArray);				
		return $fieldValue;
	}
	
	public function getFieldHTML( $field , $required )
	{
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                
		$class	= ($field->required == 1) ? ' required ' : '';
		$class .= !empty( $field->tooltip ) ? ' hasTip ' : '';
		$lists	= explode(',', $field->value);
		
		$html	= '<select id="field'.$field->id.'" name="field' . $field->id . '[]" type="select-multiple" multiple="multiple" class="hasTip select'.$class.'" title="' . JText::_( $field->name ) . '::' . $utilities->escape( JText::_( $field->tooltip ) ) . '">';

		$elementSelected	= 0;
		
		foreach( $field->options as $option )
		{
                        // Remove any line breaks
                        $option = (string)str_replace(array("\r", "\r\n", "\n"), '', $option);
                        $selected = in_array( $option, $lists ) ? ' selected="selected"' : '';
			
			if( empty($selected) )
			{
				$elementSelected++;
			}
			$html	.= '<option value="' . $option . '"' . $selected . '>' . JText::_( $option ) . '</option>'; 
		}

		$html	.= '</select>';
		$html   .= '<span id="errfield'.$field->id.'msg" style="display:none;">&nbsp;</span>';
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
				$finalvalue .= $listValue . ',';
			}
		}
                
                // Remove any line breaks
                $finalvalue = (string)str_replace(array("\r", "\r\n", "\n"), '', $finalvalue);
                
		return $finalvalue;	
	}
}