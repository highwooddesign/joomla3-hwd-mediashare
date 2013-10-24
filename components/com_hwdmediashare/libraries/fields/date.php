<?php
/**
 * @version    SVN $Id: date.php 1452 2013-04-30 10:33:31Z dhorsfall $
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

class hwdMediaShareFieldsDate
{
        /**
	 * Method to format the specified value for text type
	 **/
	public function getFieldData( $field )
	{
                $value = $field['value'];
		if( empty( $value ) )
			return $value;
                
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                $date = JHtml::_('date', $value, $config->get('list_date_format'));

		return $date;
	}

	public function getFieldHTML( $field , $required )
	{
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                
		$html	= '';

		$day	= '';
		$month	= 0;
		$year	= '';

		if(! empty($field->value))
		{
                        if(! is_array($field->value))
                        {
				$myDateArr = explode(' ', $field->value);
			}
			else
			{
                                $myDateArr[0] = $field->value[2] . '-' . $field->value[1] . '-' . $field->value[0];
			}

			if(is_array($myDateArr) && count($myDateArr) > 0)
			{
				$myDate	= explode('-', $myDateArr[0]);

				$day	= !empty($myDate[2]) ? $myDate[2] : '';
				$month	= !empty($myDate[1]) ? $myDate[1] : 0;
				$year	= !empty($myDate[0]) ? $myDate[0] : '';
			}
		}

		$months	= Array(
                            JText::_('JANUARY'),
                            JText::_('FEBRUARY'),
                            JText::_('MARCH'),
                            JText::_('APRIL'),
                            JText::_('MAY'),
                            JText::_('JUNE'),
                            JText::_('JULY'),
                            JText::_('AUGUST'),
                            JText::_('SEPTEMBER'),
                            JText::_('OCTOBER'),
                            JText::_('NOVEMBER'),
                            JText::_('DECEMBER')
                          );

                $class = ($field->required == 1) ? ' required ' : '';

                $class = !empty( $field->tooltip ) ? ' hasTip ' : '';
		$html .= '<div class="' . $class . '" style="display: inline-block;" title="' . JText::_( $field->name ) . '::' . $utilities->escape( JText::_( $field->tooltip ) ). '">';

		// Individual field should not have a tooltip
		$class	= '';

		$html .= '<input type="text" size="3" maxlength="2" name="field' . $field->id . '[]" value="' . $day . '" class="inputbox validate-custom-date" /> <span style="padding: 5px 0 0 0; float:left;">&nbsp;' . JText::_('COM_HWDMS_DAY_FORMAT') . '&nbsp;</span>';
		$html .= '<select name="field' . $field->id . '[]" class="select validate-custom-date' . $class . '">';

		$defaultSelected = '';

		//@rule: If there is no value, we need to default to a default value
		if( $month == 0 )
		{
			$defaultSelected	.= ' selected="selected"';
		}
		$html	.= '<option value=""' . $defaultSelected . '>' . JText::_('COM_HWDMS_LIST_SELECT_OPTION') . '</option>';

		for( $i = 0; $i < count($months); $i++)
		{
			if(($i + 1)== $month)
			{
				$html .= '<option value="' . ($i + 1) . '" selected="selected">' . $months[$i] . '</option>';
			}
			else
			{
				$html .= '<option value="' . ($i + 1) . '">' . $months[$i] . '</option>';
			}
		}
		$html .= '</select>';
		$html .= '<input type="text" size="5" maxlength="4" name="field' . $field->id . '[]" value="' . $year . '" class="inputbox validate-custom-date' . $class . '" /> <span style="padding: 5px 0 0 0; float:left;">&nbsp;' . JText::_('COM_HWDMS_YEAR_FORMAT') . '&nbsp;</span>';
		$html .= '<span id="errfield'.$field->id.'msg" style="display:none;">&nbsp;</span>';
		$html .= '</div>';

		return $html;
	}

	public function isValid( $value , $required )
	{
		if( ($required && empty($value)) || !isset($this->fieldId))
		{
			return false;
		}

		$db	=& JFactory::getDBO();
		$query	= 'SELECT * FROM '.$db->quoteName('#__hwdms_fields')
			. ' WHERE '.$db->quoteName('id').'='.$db->quote($this->fieldId);
		$db->setQuery($query);
		$field	= $db->loadAssoc();

		$params	= new JRegistry($field['params']);
		$max_range = $params->get('maxrange');
		$min_range = $params->get('minrange');
		$value = JFactory::getDate(strtotime($value))->toUnix();
		$max_ok = true;
		$min_ok = true;

		if ($max_range)
		{
			$max_range = JFactory::getDate(strtotime($max_range))->toUnix();
			$max_ok = ($value < $max_range);
		}
		if ($min_range)
		{
			$min_range = JFactory::getDate(strtotime($min_range))->toUnix();
			$min_ok = ($value > $min_range);
		}

		return ($max_ok && $min_ok) ? true : false;
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
				$day	= intval($value[0]);
				$month	= intval($value[1]);
				$year	= intval($value[2]);

				$day 	= !empty($day) 		? $day 		: 1;
				$month 	= !empty($month) 	? $month 	: 1;
				$year 	= !empty($year) 	? $year 	: 1970;

				if( !checkdate($month, $day, $year) )
				{
					return $finalvalue;
				}

				$finalvalue	= $year . '-' . $month . '-' . $day . ' 23:59:59';
			}
		}

		return $finalvalue;
	}

	public function getType()
	{
		return 'date';
	}
}
