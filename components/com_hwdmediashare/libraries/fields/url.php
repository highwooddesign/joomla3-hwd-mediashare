<?php
/**
 * @version    SVN $Id: url.php 287 2012-03-30 13:33:27Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  (C) 2008 by Slashes & Dots Sdn Bhd (JomSocial)
 * @copyright  (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class hwdMediaShareFieldsUrl
{
        /**
	 * Method to format the specified value for text type
	 **/	 	
	public function getFieldData( $field )
	{
		$value = $field['value'];
		
		if( empty( $value ) )
			return $value;
		
		return '<a rel="nofollow" href="' . $value . '" target="_blank">' . $value . '</a>';
	}
	
	public function getFieldHTML( $field , $required )
	{
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
            
		// If maximum is not set, we define it to a default
		$field->max = empty( $field->max ) ? 200 : $field->max;

		$class	= ($field->required == 1) ? ' required ' : '';
		$class .= !empty( $field->tooltip ) ? ' hasTip ' : '';
		$scheme	= '';
		$host	= '';

		if(! empty($field->value))
		{
			// Let's correct the format bfore passing to parse_url()
			$field->value = implode('', explode(',', $field->value));
			
			$url	= parse_url($field->value);
			
			$scheme	= isset( $url[ 'scheme' ] ) ? $url['scheme'] : 'http://';
			$host	= isset( $url[ 'host' ] ) ? $url['host'] : '';
			$path	= isset( $url[ 'path'] ) ? $url['path'] : '';
			$query	= isset( $url[ 'query'] ) ? '?' . $url['query'] : '';
			$fragment = isset( $url['fragment'] ) ? '#' . $url['fragment'] : '' ;
			$field->value	= $host . $path . $query . $fragment;
		}
		
		ob_start();
                ?>
                        <select name="field<?php echo $field->id;?>[]">
                                <option value="http://"<?php echo ($scheme == 'http') ? ' selected="selected"' : '';?>><?php echo JText::_('http://');?></option>
                                <option value="https://"<?php echo ($scheme == 'https') ? ' selected="selected"' : '';?>><?php echo JText::_('https://');?></option>
                        </select>
                        <input title="<?php echo $field->name . '::'. $utilities->escape( $field->tooltip );?>" type="text" value="<?php echo $field->value;?>" id="field<?php echo $field->id;?>" name="field<?php echo $field->id;?>[]" maxlength="<?php echo $field->max;?>" size="40" class="hasTip inputbox validate-profile-url<?php echo $class;?>" />
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
                
		$isValid = $utilities->validateUrl( $value );

		$url		= parse_url( $value );
		$host		= isset($url['host']) ? $url['host'] : '';

		if( !$isValid && $required )
			return false;
		else if( !empty($host) && !$isValid )
			return false; 

		return true;
	}
	
	public function formatdata( $value )
	{
		if( empty( $value[0] ) || empty( $value[1] ) )
		{
			$value = '';
		}
		else
		{
			$scheme	= $value[ 0 ];
			$url	= $value[ 1 ];
			$value	= $scheme . $url;			
		}
		return $value;
	}
}
