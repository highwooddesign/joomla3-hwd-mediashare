<?php
/**
 * @version    SVN $Id: radio.php 287 2012-03-30 13:33:27Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  (C) 2008 by Slashes & Dots Sdn Bhd (JomSocial)
 * @copyright  (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class hwdMediaShareFieldsRadio
{
        public function getFieldHTML( $field , $required )
	{
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();

                $html			= '';
		$selectedElement	= 0;		
		$class			= ($field->required == 1) ? ' required validate-custom-radio' : '';
		$elementSelected	= 0;
		$elementCnt	        = 0;		

		if (!empty($field->options))
                {
                        for( $i = 0; $i < count( $field->options ); $i++ )
                        {
                                $option		= $field->options[ $i ];
                                $selected	= ( $option == $field->value ) ? ' checked="checked"' : '';

                                if( empty( $selected ) )
                                {
                                        $elementSelected++;
                                }			
                                $elementCnt++;				
                        }
                }
		
		$cnt = 0;

		$class	= !empty( $field->tooltip ) ? ' hasTip ' : '';
		$html  .= '<div class="' . $class . '" style="display: inline-block;" title="' . JText::_( $field->name ) . '::' . $utilities->escape( JText::_( $field->tooltip ) ). '">';
		if (!empty($field->options))
                {
                        for( $i = 0; $i < count( $field->options ); $i++ )
                        {
                                $option	  = $field->options[ $i ];

                                $selected = ( $option == $field->value ) ? ' checked="checked"' : '';		    		    

                                $html 	 .= '<label class="lblradio-block">';
                                $html	 .= '<input type="radio" name="field' . $field->id . '" value="' . $option . '"' . $selected . '  class="radio" style="margin: 0 5px 0 0;" />';			
                                $html	 .= JText::_( $option ) . '</label>';
                        }
                }
		$html   .= '<span id="errfield'.$field->id.'msg" style="display: none;">&nbsp;</span>';
		$html	.= '</div>';				
		
		return $html;
	}
	
	public function isValid( $value , $required )
	{
		if($required && empty($value))
		{
			return false;
		}		
		return true;
	}
}