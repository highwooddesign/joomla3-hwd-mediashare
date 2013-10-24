<?php
/**
 * @version    $Id: remote_youtube.php 425 2012-06-28 07:48:57Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import hwdMediaShare remote library
hwdMediaShareFactory::load('remote');

// Load the main Youtube.com plugin
JLoader::register('plgHwdmediashareRemote_youtubecom', JPATH_PLUGINS.'/hwdmediashare/remote_youtubecom/remote_youtubecom.php');

/**
 * hwdMediaShare framework files class
 *
 * @package hwdMediaShare
 * @since   0.1
 */
class plgHwdmediashareRemote_youtube extends plgHwdmediashareRemote_youtubecom
{     
        /**
	 * Constructor
	 *
	 * @access      protected
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 * @since       1.5
	 */
	public function __construct()
	{
	}
        
	/**
	 * Returns the hwdMediaShareFiles object, only creating it if it
	 * doesn't already exist.
	 *
	 * @return  hwdMediaShareFiles A hwdMediaShareFiles object.
	 * @since   0.1
	 */
	public static function getInstance()
	{
		static $instance;
                unset($instance);
		if (!isset ($instance))
                {
			$c = 'plgHwdmediashareRemote_youtube';
                        $instance = new $c;
		}

		return $instance;
	}
        
        /**
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/
	function getUrl()
	{
                $id = plgHwdmediashareRemote_youtubecom::parse($this->url, '');
                $this->_url = 'http://www.youtube.com/watch?v='.$id;
	}
}