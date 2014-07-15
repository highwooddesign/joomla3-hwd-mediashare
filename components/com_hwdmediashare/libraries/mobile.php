<?php
/**
 * @package     Joomla.site
 * @subpackage  Component.hwdmediashare
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

if (!class_exists('Mobile_Detect'))
{
        hwdMediaShareFactory::load('mobile.mobile_detect');
}
                
class hwdMediaShareMobile extends Mobile_Detect
{
	/**
	 * Returns the hwdMediaShareMobile object, only creating it if it
	 * doesn't already exist.
	 *
	 * @access  public
         * @static
	 * @return  hwdMediaShareMobile Object.
	 */
	public static function getInstance()
	{
		static $instance;

		if (!isset ($instance))
                {
			$c = 'hwdMediaShareMobile';
                        $instance = new $c;
		}

		return $instance;
	}
}
