<?php
/**
 * @version    SVN $Id: label.php 287 2012-03-30 13:33:27Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  (C) 2008 by Slashes & Dots Sdn Bhd (JomSocial)
 * @copyright  (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class hwdMediaShareFieldsLabel
{
        public function getFieldHTML( $field , $required )
	{
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                
                $class	= !empty( $field->tooltip ) ? ' hasTip ' : '';
		
		$html	= '<textarea title="' . JText::_( $field->name ) . '::'. $utilities->escape( JText::_( $field->tooltip ) ) .'" id="field' . $field->id . '" name="field' . $field->id . '"  class="textarea inputbox' . $class . '" cols="20" rows="5" readonly="readonly">' . $utilities->escape( $field->tooltip ) . '</textarea>';
		$html  .= '<span id="errfield'.$field->id.'msg" style="display:none;">&nbsp;</span>';

		return $html;
	}
}
