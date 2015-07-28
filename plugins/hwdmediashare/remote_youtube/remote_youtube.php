<?php
/**
 * @package     Joomla.site
 * @subpackage  Plugin.hwdmediashare.remote_youtubecom
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

// Import hwdMediaShare remote library.
JLoader::register('hwdMediaShareRemote', JPATH_ROOT.'/components/com_hwdmediashare/libraries/remote.php');

// Load the main Youtube.com plugin.
JLoader::register('plgHwdmediashareRemote_youtubecom', JPATH_PLUGINS.'/hwdmediashare/remote_youtubecom/remote_youtubecom.php');

class plgHwdmediashareRemote_youtube extends plgHwdmediashareRemote_youtubecom
{     
	/**
	 * Class constructor.
	 *
	 * @access  public
         * @return  void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Returns the plgHwdmediashareRemote_youtube object, only creating it if it
	 * doesn't already exist.
	 *
	 * @access  public
	 * @return  object  The plgHwdmediashareRemote_youtube object.
	 */
	public static function getInstance()
	{
		static $instance;

		if (!isset ($instance))
                {
			$c = 'plgHwdmediashareRemote_youtube';
                        $instance = new $c;
		}

		return $instance;
	}
        
        /**
	 * Get the url of the media source.
	 *
	 * @access  public
         * @return  void
	 */
	public function getUrl()
	{
                $id = plgHwdmediashareRemote_youtubecom::parse($this->_url, '');
                $this->_url = 'http://www.youtube.com/watch?v='.$id;
	}
}