<?php
/**
 * @version    SVN $Id: country.php 1148 2013-02-21 11:17:09Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  (C) 2008 by Slashes & Dots Sdn Bhd (JomSocial)
 * @copyright  (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class hwdMediaShareFieldsCountry
{
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
	
	public function getFieldHTML( $field , $required )
	{
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                
                // If maximum is not set, we define it to a default
		$field->max = empty( $field->max ) ? 200 : $field->max;

		$class = ($field->required == 1) ? ' required ' : '';
		
		jimport( 'joomla.filesystem.file' );
		$file = JPATH_ROOT.'/components/com_hwdmediashare/libraries/fields/countries.xml';
		
		if( JFile::exists( $file ) )
		{
                        $parser         =& JFactory::getXML($file);                                        
	
			$element	=& $parser->countries;
			$countries	= $element->children();

			$tooltips	= !empty( $field->tooltip ) ? ' title="' . JText::_( $field->name ) . '::' . $utilities->escape( JText::_( $field->tooltip ) ) . '"' : '';

                        ob_start();
                        ?>
			<select id="field<?php echo $field->id;?>" name="field<?php echo $field->id;?>" class="<?php echo !empty( $field->tooltip ) ? ' hasTip ' : '';?>select validate-country<?php echo $class;?> inputbox"<?php echo $tooltips;?>>
				<option value=""<?php echo empty($field->value) ? ' selected="selected"' : '';?>><?php echo JText::_('COM_HWDMS_LIST_SELECT_COUNTRY');?></option>
                                <?php
                                foreach($countries as $country )
                                {
                                        $name	= $country->name;
                                        ?>
                                        <option value="<?php echo $name;?>"<?php echo ($field->value == $name) ? ' selected="selected"' : '';?>><?php echo JText::_($name);?></option>
                                        <?php			
                                }
                                ?>
			</select>
			<span id="errfield<?php echo $field->id;?>msg" style="display:none;">&nbsp;</span>
                        <?php
			$html	= ob_get_contents();
			ob_end_clean();
		}
		else
		{
			$html	= JText::_('Countries list not found');
		}

		return $html;
	}
	
	public function isValid( $value , $required )
	{
		if( $value === 'selectcountry' && $required )
			return false;
			
		return true;
	}

}