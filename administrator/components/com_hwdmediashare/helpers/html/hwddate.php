<?php
/**
 * @package     Joomla.administrator
 * @subpackage  Component.hwdmediashare
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

abstract class JHtmlHwdDate
{
	/**
	 * Function to convert a static time into a relative measurement
	 *
	 * @access  public
         * @static 
	 * @param   string  $date  The date to convert
	 * @param   string  $unit  The optional unit of measurement to return
	 *                         if the value of the diff is greater than one
	 * @param   string  $time  An optional time to compare to, defaults to now
	 * @return  string  The converted time string
	 */
	public static function relative($date, $unit = null, $time = null)
	{
		// To return the absolute time, uncomment the following.
		// return JHtml::_('date', $date);
                
		if (is_null($time))
		{
			// Get now.
			$time = JFactory::getDate('now');
		}

		// Get the difference in seconds between now and the time.
		$diff = strtotime($time) - strtotime($date);

		// Less than a minute.
		if ($diff < 60)
		{
			return strtolower(JText::_('JLIB_HTML_DATE_RELATIVE_LESSTHANAMINUTE'));
		}

		// Round to minutes.
		$diff = round($diff / 60);

		// 1 to 59 minutes.
		if ($diff < 60 || $unit == 'minute')
		{
			return JText::plural('JLIB_HTML_DATE_RELATIVE_MINUTES', $diff);
		}

		// Round to hours.
		$diff = round($diff / 60);

		// 1 to 23 hours.
		if ($diff < 24 || $unit == 'hour')
		{
			return JText::plural('JLIB_HTML_DATE_RELATIVE_HOURS', $diff);
		}

		// Round to days.
		$diff = round($diff / 24);

		// 1 to 6 days.
		if ($diff < 7 || $unit == 'day')
		{
			return JText::plural('JLIB_HTML_DATE_RELATIVE_DAYS', $diff);
		}

		// Round to weeks.
		$diff = round($diff / 7);

		// 1 to 4 weeks.
		if ($diff <= 4 || $unit == 'week')
		{
			return JText::plural('JLIB_HTML_DATE_RELATIVE_WEEKS', $diff);
		}

		// Round to months (52 weeks in a year and 12 months gives 4.3333 weeks in a month).
		$diff = round($diff / 4.333);

		// 1 to 12 months.
		if ($diff <= 12 || $unit == 'month')
		{
			return JText::plural('COM_HWDMS_DATE_RELATIVE_MONTHS', $diff);
		}
                
		// Round to years.
		$diff = round($diff / 12);

		return JText::plural('COM_HWDMS_DATE_RELATIVE_YEARS', $diff);
	}
}
