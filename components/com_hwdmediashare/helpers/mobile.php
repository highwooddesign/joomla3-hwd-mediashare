<?php
/**
 * @version    SVN $Id: mobile.php 518 2012-09-28 10:04:36Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      24-Jan-2012 15:46:04
 */  

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * hwdMediaShare Mobile Helper
 *
 * @package	hwdMediaShare
 * @since       0.1
 */
class hwdMediaShareHelperMobile
{
	/**
	 * isMobile data
	 *
	 * @var object
	 **/
        var $_isMobile;
        var $_isIpad;

	/**
	 * Constructor.
	 *
	 * @param	array	$config	An optional associative array of configuration settings.

	 * @return	hwdMediaShareHelperMobile
	 * @since	0.1
	 */
	public function __construct($config = array())
	{
                if (!class_exists('Mobile_Detect'))
                {
                        hwdMediaShareFactory::load('mobile.mobile_detect');
                }
		
                $mobile = new Mobile_Detect();
                if( !$this->_isMobile )
		{
                        $this->_isMobile = $mobile->isMobile();
                }
                if( !$this->_isIpad )
		{
                        $this->_isIpad = $mobile->isIpad();
                }
	}

	/**
	 * Returns the global hwdMediaShareHelperMobile object, only creating it if it
	 * doesn't already exist.
         *
	 * @since   0.1
	 */
	public static function getInstance()
	{
		static $instance;

		if (!isset ($instance))
                {
			$c = 'hwdMediaShareHelperMobile';
                        $instance = new $c;
		}

		return $instance;
	}
}
