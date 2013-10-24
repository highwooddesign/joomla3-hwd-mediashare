<?php
/**
 * @version    SVN $Id: time.php 287 2012-03-30 13:33:27Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  (C) 2008 by Slashes & Dots Sdn Bhd (JomSocial)
 * @copyright  (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.utilities.date');

class hwdMediaShareFieldsTime
{
        /**
	 * Method to format the specified value for text type
	 **/

	public function getFieldHTML( $field , $required )
	{
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                
		$html	= '';

		$hour	= '';
		$minute	= 0;
		$second	= '';

		if(! empty($field->value))
		{
			$myTimeArr	= explode(' ', $field->value);

			if(is_array($myTimeArr) && count($myTimeArr) > 0)
			{
				$myTime	= explode(':', $myTimeArr[0]);

				$hour	= !empty($myTime[0]) ? $myTime[0] : '00';
				$minute	= !empty($myTime[1]) ? $myTime[1] : '00';
				$second	= !empty($myTime[2]) ? $myTime[2] : '00';
			}
		}

		$hours = array();
		for($i=0; $i<24; $i++)
		{
			$hours[] = ($i<10)? '0'.$i : $i;
		}

		$minutes = array();
		for($i=0; $i<60; $i++)
		{
			$minutes[] = ($i<10)? '0'.$i : $i;
		}

		$seconds = array();
		for($i=0; $i<60; $i++)
		{
			$seconds[] = ($i<10)? '0'.$i : $i;
		}

                $class  = ($field->required == 1) ? ' required ' : '';
                $class .= !empty( $field->tooltip ) ? ' hasTip ' : '';
		$html  .= '<div class="' . $class . '" style="display: inline-block;" title="' . JText::_( $field->name ) . '::' . $utilities->escape( JText::_( $field->tooltip ) ) . '">';
		$html  .= '<select name="field' . $field->id . '[]" >';
		for($i = 0; $i < count($hours); $i++)
		{
			if($hours[$i]==$hour)
			{
				$html .= '<option value="' . $hours[$i] . '" selected="selected">' . $hours[$i] . '</option>';
			}
			else
			{
				$html .= '<option value="' . $hours[$i] . '">' . $hours[$i] . '</option>';
			}
		}
		$html .= '</select> <span style="padding: 5px 0 0 0; float:left;">&nbsp;' . JText::_('COM_HWDMS_HOUR_FORMAT') . '&nbsp;</span>';
		$html .= '<select name="field' . $field->id . '[]" >';
		for( $i = 0; $i < count($minutes); $i++)
		{
			if($minutes[$i]==$minute)
			{
				$html .= '<option value="' . $minutes[$i] . '" selected="selected">' . $minutes[$i] . '</option>';
			}
			else
			{
				$html .= '<option value="' . $minutes[$i] . '">' . $minutes[$i] . '</option>';
			}
		}
		$html .= '</select> <span style="padding: 5px 0 0 0; float:left;">&nbsp;' . JText::_('COM_HWDMS_MINUTE_FORMAT') . '&nbsp;</span>';
		$html .= '<select name="field' . $field->id . '[]" >';
		for( $i = 0; $i < count($seconds); $i++)
		{
			if($seconds[$i]==$second)
			{
				$html .= '<option value="' . $seconds[$i] . '" selected="selected">' . $seconds[$i] . '</option>';
			}
			else
			{
				$html .= '<option value="' . $seconds[$i] . '">' . $seconds[$i] . '</option>';
			}
		}
		$html .= '</select> <span style="padding: 5px 0 0 0; float:left;">&nbsp;' . JText::_('COM_HWDMS_SECOND_FORMAT') . '&nbsp;</span>';
		$html .= '<span id="errfield'.$field->id.'msg" style="display:none;">&nbsp;</span>';
		$html .= '</div>';

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
		if(is_array($value))
		{
			if( empty( $value[0] ) || empty( $value[1] ) || empty( $value[2] ) )
			{
				$finalvalue = '';
			}
			else
			{
				$hour 	= !empty($value[0]) ? $value[0]	: '00';
				$minute = !empty($value[1]) ? $value[1]	: '00';
				$second = !empty($value[2]) ? $value[2]	: '00';

				$finalvalue = $hour . ':' . $minute . ':' . $second;
			}
		}
		return $finalvalue;
	}
}