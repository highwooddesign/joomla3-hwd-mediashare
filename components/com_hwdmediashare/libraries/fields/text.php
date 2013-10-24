<?php
/**
 * @version    SVN $Id: text.php 1452 2013-04-30 10:33:31Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  (C) 2008 by Slashes & Dots Sdn Bhd (JomSocial)
 * @copyright  (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class hwdMediaShareFieldsText
{
        public function getFieldHTML( $field , $required )
	{
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                
		jimport( 'joomla.application.component.view' );

                $params     = new JRegistry($field->params);
		$readonly   = $params->get('readonly') ? ' readonly=""' : '';
		$disabled   = $params->get('disabled') ? ' disabled=""' : '';
		
		// If maximum is not set, we define it to a default
		$field->max = empty( $field->max ) ? 200 : $field->max;
                
		$class	    = ($field->required == 1) ? ' required ' : '';
		$class	   .= !empty( $field->tooltip ) ? ' hasTip ' : '';
		$html	    = '<input title="' . JText::_( $field->name ) . '::'. $utilities->escape( JText::_( $field->tooltip ) ).'" type="text" value="' . $field->value . '" id="field' . $field->id . '" name="field' . $field->id . '" maxlength="' . $field->max . '" size="40" class="hasTip inputbox' . $class . '" '.$readonly.$disabled.' />';
		$html      .= '<span id="errfield'.$field->id.'msg" style="display:none;">&nbsp;</span>';
		
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
}