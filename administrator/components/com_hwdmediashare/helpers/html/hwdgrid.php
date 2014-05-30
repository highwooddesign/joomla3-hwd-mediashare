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

abstract class JHtmlHwdGrid
{
	/**
	 * Method to create a customised checkbox for a grid row.
	 *
	 * @access	public
         * @static 
	 * @param       integer     $rowNum      The row index.
	 * @param       integer     $recId       The record id.
	 * @param       string      $idName      The form element id identifer.
	 * @param       boolean     $checkedOut  True if item is checked out.
	 * @param       string      $name        The name of the form element.
	 * @param       string      $class       The class of the form element.
	 * @return      string      String of html with a checkbox if item is not checked out, null if checked out.
	 */
	public static function id($rowNum, $recId, $idName = 'cb', $checkedOut = false, $name = 'cid', $class = '')
	{
		return $checkedOut ? '' : '<input type="checkbox" id="' . $idName . $rowNum . '" name="' . $name . '[]" value="' . $recId
			. '" onclick="Joomla.isChecked(this.checked);" class="' . $class . '" />';
	}
}
