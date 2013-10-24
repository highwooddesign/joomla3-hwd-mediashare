<?php
/**
 * @version    SVN $Id: email.php 287 2012-03-30 13:33:27Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  (C) 2008 by Slashes & Dots Sdn Bhd (JomSocial)
 * @copyright  (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class hwdMediaShareFieldsEmail
{
        /**
	 * Method to format the specified value for text type
	 **/	 	
	public function getFieldData( $field )
	{
		$value = $field['value'];

		if( empty( $value ) )
			return $value;
		
		$email = JString::trim( $value );
		
		// @rule: Process email cloaking by Joomla. If it fails,
		// we will need to find another solution.
		$link = JHTML::_( 'email.cloak', $email );

		if(empty($link))
		{
			$link	= '<a href="mailto:'.$email.'">'.$email.'</a>'; 	
		}
			
		return $link;
	}
	
	public function getFieldHTML( $field , $required )
	{
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                
		// If maximum is not set, we define it to a default
		$field->max = empty( $field->max ) ? 200 : $field->max;

		$class	= ($field->required == 1) ? ' required ' : '';
		$class .= !empty( $field->tooltip ) ? ' hasTip ' : '';
		
		ob_start();
                ?>
                        <input class="inputbox validate-profile-email<?php echo $class;?>" title="<?php echo JText::_( $field->name ) . '::'. $utilities->escape( JText::_( $field->tooltip ) );?>" type="text" value="<?php echo $field->value;?>" id="field<?php echo $field->id;?>" name="field<?php echo $field->id;?>" maxlength="<?php echo $field->max;?>" size="40" />
                        <span id="errfield<?php echo $field->id;?>msg" style="display:none;">&nbsp;</span>
                <?php
		$html	= ob_get_contents();
		ob_end_clean();

		return $html;
	}
	
	public function isValid( $value , $required )
	{
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                
		$isValid = $utilities->validateEmail( $value );

		if( !empty($value) && $isValid )
		{
			return true;
		}
		else if( empty($value) && !$required )
		{
			return true;
		}

		return false; 
	}
}