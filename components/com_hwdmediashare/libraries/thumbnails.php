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

class hwdMediaShareThumbnails extends JObject
{        
	/**
	 * Class constructor.
	 *
	 * @access  public
	 * @param   mixed  $properties  Either and associative array or another
	 *                              object to set the initial properties of the object.
         * @return  void
	 */
	public function __construct($properties = null)
	{
		parent::__construct($properties);
	}

	/**
	 * Returns the hwdMediaShareThumbnails object, only creating it if it
	 * doesn't already exist.
	 *
	 * @access  public
         * @static
	 * @return  hwdMediaShareThumbnails Object.
	 */ 
	public static function getInstance()
	{
		static $instance;

		if (!isset ($instance))
                {
			$c = 'hwdMediaShareThumbnails';
                        $instance = new $c;
		}

		return $instance;
	}
        
	/**
	 * Method to get the channel art url for a channel.
	 *
	 * @access  public
         * @static
	 * @param   object  $item   The channel to display.
	 * @return  string  The url of the channel art.
	 */ 
        public static function getChannelArt($item)
	{
                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
  
                if (empty($item->key))
                {
                        return JURI::root( true ).'/media/com_hwdmediashare/assets/images/default-channel.png';
                }
                
                // Check for saved channel art.
                hwdMediaShareFactory::load('files');
                
                $folders = hwdMediaShareFiles::getFolders($item->key);
                $filename = hwdMediaShareFiles::getFilename($item->key, 10);
                $ext = hwdMediaShareFiles::getExtension($item, 10);

                $path = hwdMediaShareFiles::getPath($folders, $filename, $ext);

                if (file_exists($path))
                {
                        return hwdMediaShareFiles::getUrl($folders, $filename, $ext);
                }
                
                return JURI::root( true ).'/media/com_hwdmediashare/assets/images/default-channel.png';
        }
}
