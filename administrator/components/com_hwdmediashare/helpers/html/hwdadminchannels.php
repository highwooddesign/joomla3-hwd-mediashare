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

abstract class JHtmlHwdAdminChannels
{
	/**
	 * Show the feature/unfeature links.
	 *
	 * @access	public
         * @static 
	 * @param       int         $value      The state value
	 * @param       int         $i          Row number
	 * @param       boolean     $canChange  Is user allowed to change?
	 * @return      string      HTML code
	 */
	public static function featured($value = 0, $i, $canChange = true)
	{
		JHtml::_('bootstrap.tooltip');

		// Array of image, task, title, action
		$states	= array(
			0	=> array('unfeatured',	'channels.feature',	'COM_HWDMS_UNFEATURED',         'COM_HWDMS_TOGGLE_TO_FEATURE'),
			1	=> array('featured',	'channels.unfeature',	'COM_HWDMS_FEATURED',		'COM_HWDMS_TOGGLE_TO_UNFEATURE'),
		);
		$state	= JArrayHelper::getValue($states, (int) $value, $states[1]);
		$icon	= $state[0];

		if ($canChange)
		{
			$html	= '<a href="#" onclick="return listItemTask(\'cb' . $i . '\',\'' . $state[1] . '\')" class="btn btn-micro hasTooltip' . ($value == 1 ? ' active' : '') . '" title="' . JHtml::tooltipText($state[3]) . '"><i class="icon-'
					. $icon . '"></i></a>';
		}
		else
		{
			$html	= '<a class="btn btn-micro hasTooltip disabled' . ($value == 1 ? ' active' : '') . '" title="' . JHtml::tooltipText($state[2]) . '"><i class="icon-'
					. $icon . '"></i></a>';
		}

		return $html;
	}
        
	/**
	 * Show the approve/unapprove links
	 *
	 * @access	public
         * @static 
	 * @param       int         $value      The state value
	 * @param       int         $i          Row number
	 * @param       boolean     $canChange  Is user allowed to change?
	 * @return      string      HTML code
	 */
	public static function status($value = 0, $i, $canChange = true)
	{
		JHtml::_('bootstrap.tooltip');

		// Array of image, task, title, action.
		$states	= array(
			0	=> array('unpublish',	'channels.approve',	'COM_HWDMS_UNAPPROVED',         'COM_HWDMS_TOGGLE_TO_APPROVE'),
			1	=> array('publish',	'channels.unapprove',	'COM_HWDMS_APPROVED',		'COM_HWDMS_TOGGLE_TO_UNAPPROVE'),
			2	=> array('pending',	'channels.approve',	'COM_HWDMS_PENDING',		'COM_HWDMS_TOGGLE_TO_APPROVE'),
			3	=> array('not-ok',	'channels.approve',	'COM_HWDMS_REPORTED',		'COM_HWDMS_TOGGLE_TO_APPROVE'),
		);
		$state	= JArrayHelper::getValue($states, (int) $value, $states[1]);
		$icon	= $state[0];

		if ($canChange)
		{
			$html	= '<a href="#" onclick="return listItemTask(\'cb' . $i . '\',\'' . $state[1] . '\')" class="btn btn-micro hasTooltip' . ($value == 1 ? ' active' : '') . '" title="' . JHtml::tooltipText($state[3]) . '"><i class="icon-'
					. $icon . '"></i></a>';
		}
		else
		{
			$html	= '<a class="btn btn-micro hasTooltip disabled' . ($value == 1 ? ' active' : '') . '" title="' . JHtml::tooltipText($state[2]) . '"><i class="icon-'
					. $icon . '"></i></a>';
		}

		return $html;
	}        
}
