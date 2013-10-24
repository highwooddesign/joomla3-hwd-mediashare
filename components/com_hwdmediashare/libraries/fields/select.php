<?php
/**
 * @version    SVN $Id: select.php 287 2012-03-30 13:33:27Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  (C) 2008 by Slashes & Dots Sdn Bhd (JomSocial)
 * @copyright  (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class hwdMediaShareFieldsSelect
{
        public function getFieldHTML( $field , $required, $isDropDown = true)
	{
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                
		$class		= ($field->required == 1) ? ' required ' : '';
		$class	       .= !empty( $field->tooltip ) ? ' hasTip ' : '';		
		$optionSize	= 1;
		
		if( !empty( $field->options ) )
		{
			$optionSize += count($field->options);
		}
		
		$dropDown	= ($isDropDown) ? '' : ' size="'.$optionSize.'"';
		
		$html		= '<select id="field'.$field->id.'" name="field' . $field->id . '"' . $dropDown . ' class="select'.$class.'" title="' . JText::_( $field->name ) . '::' . $utilities->escape( JText::_( $field->tooltip ) ). '">';
                
		$defaultSelected = '';
		
		//@rule: If there is no value, we need to default to a default value
		if(empty( $field->value ) )
		{
			$defaultSelected .= ' selected="selected"';
		}
		
		if($isDropDown)
		{
			$html .= '<option value="" ' . $defaultSelected . '>' . JText::_('COM_HWDMS_LIST_SELECT_OPTION') . '</option>';
		}	
		
		if( !empty( $field->options ) )
		{
			$selectedElement	= 0;
			
			foreach( $field->options as $option )
			{
                                $selected = (trim($option) == trim($field->value) ? ' selected="selected"' : '');
				
				if( !empty( $selected ) )
				{
					$selectedElement++;
				}
                                
				$html .= '<option value="' . $utilities->escape( $option ) . '"' . $selected . '>' . JText::_( $option ) . '</option>';
			}
		}
		$html	.= '</select>';
		$html   .= '<span id="errfield'.$field->id.'msg" style="display:none;">&nbsp;</span>';
		
		return $html;
	}
}
