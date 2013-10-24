<?php
/**
 * @version    SVN $Id: singleselect.php 287 2012-03-30 13:33:27Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  (C) 2008 by Slashes & Dots Sdn Bhd (JomSocial)
 * @copyright  (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once (JPATH_ROOT.'/components/com_hwdmediashare/libraries/fields/select.php');

class hwdMediaShareFieldsSingleSelect extends hwdMediaShareFieldsSelect
{
	public function getFieldHTML( $field , $required)
	{
		$isDropDown = false;
		return parent::getFieldHTML($field, $required, $isDropDown);
	}

}