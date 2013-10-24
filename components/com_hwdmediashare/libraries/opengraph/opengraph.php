<?php
/**
 * @version    SVN $Id: opengraph.php 1509 2013-05-13 13:38:33Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      04-Feb-2013 11:39:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * hwdMediaShare framework activities class
 *
 * @package hwdMediaShare
 * @since   0.1
 */
class hwdMediaShareOpenGraph
{
	/**
	 * Class constructor.
	 *
	 * @param   array  $config  A configuration array including optional elements.
	 *
	 * @since   0.1
	 */
	public function __construct($config = array())
	{
	}

	/**
	 * Returns the hwdMediaShareActivities object, only creating it if it
	 * doesn't already exist.
	 *
	 * @return  hwdMediaShareActivities A hwdMediaShareActivities object.
	 * @since   0.1
	 */
	public static function getInstance()
	{
		static $instance;

		if (!isset ($instance))
                {
			$c = 'hwdMediaShareOpenGraph';
                        $instance = new $c;
		}

		return $instance;
	}
        
	/**
	 * Method to save an activity
         * 
         * @since   0.1
	 **/
	public function get($item)
	{
                $app = & JFactory::getApplication();
                $doc = & JFactory::getDocument();

                // Load hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();

                $doc->addCustomTag('<meta property="og:title" content="'.$utilities->escape($item->title).'"/>');
                $doc->addCustomTag('<meta property="og:url" content="'.hwdMediaShareMedia::getPermalink($item->id).'"/>');
                $doc->addCustomTag('<meta property="og:image" content="'.$utilities->relToAbs(JRoute::_(hwdMediaShareDownloads::thumbnail($item))).'"/>');
                $doc->addCustomTag('<meta property="og:site_name" content="'.$app->getCfg('sitename').'"/>');
                $doc->addCustomTag('<meta property="fb:app_id" content="'.$config->get('facebook_appid').'"/>');
                $doc->addCustomTag('<meta property="og:description" content="'.$utilities->escape(JHtmlString::truncate($item->description, $config->get('list_desc_truncate'), true, false)).'"/>');

                switch ($item->type) {
                    case 2:
                        // Remote
                        hwdMediaShareFactory::load('remote');
                        $lib = hwdMediaShareRemote::getInstance();
                        $lib->url = $item->source;
                        $host = $lib->getHost();                       
                        $remotePluginClass = $lib->getRemotePluginClass($host);
                        $remotePluginPath = $lib->getRemotePluginPath($host);

                        // Import hwdMediaShare plugins
                        JLoader::register($remotePluginClass, $remotePluginPath);
                        if (class_exists($remotePluginClass))
                        { 
                                $remote = call_user_func(array($remotePluginClass, 'getInstance'));
                                if (method_exists($remote, 'getOgVideoTag'))
                                {
                                        $width = 640;
                                        $height = (int) ($width*$config->get('video_aspect',0.75));
                                        $doc->addCustomTag('<meta property="og:type" content="video.movie"/>');
                                        $doc->addCustomTag('<meta property="og:video" content="' . $remote->getOgVideoTag($item) . '"/>');
                                        $doc->addCustomTag('<meta property="og:video:width" content="' . $width . '"/>');
                                        $doc->addCustomTag('<meta property="og:video:height" content="' . $height . '"/>');
                                        $doc->addCustomTag('<meta property="og:video:type" content="application/x-shockwave-flash"/>'); 
                                }
                        }
                        return true;
                        break;
                    case 3:
                        // Embed code
                        return false;
                        break;
                    case 4:
                        // RTMP
                        return false;
                        break;
                    case 6:
                        // Platform
                        return false;
                        break;
                    case 7:
                        // Linked file
                        return false;
                        break;
                }
                
                switch ($item->media_type) {
                    case 1:
                        // Audio
                        hwdMediaShareFactory::load('audio');
                        $doc->addCustomTag('<meta property="og:type" content="music.song"/>');
                        $doc->addCustomTag('<meta property="og:audio" content="' . $utilities->relToAbs(hwdMediaShareAudio::getMp3($item)) . '"/>');
                        //$doc->addCustomTag('<meta property="og:audio:secure_url" content="' . $utilities->relToAbs(hwdMediaShareAudio::getMp3($item)) . '"/>');
                        $doc->addCustomTag('<meta property="og:audio:type" content="audio/vnd.facebook.bridge"/>');
                        return true;
                        break;
                    case 2:
                        // Document
                        return false;
                        break;
                    case 3:
                        // Image
                        return false;
                        break;
                    case 4:
                        // Video
                        $width = 640;
                        $height = (int) ($width*$config->get('video_aspect',0.75));
                        hwdMediaShareFactory::load('videos');
                        $doc->addCustomTag('<meta property="og:type" content="video.movie"/>');
                        $doc->addCustomTag('<meta property="og:video:url" content="' . $this->getOgVideoTag($item) . '"/>');
                        $doc->addCustomTag('<meta property="og:video:width" content="' . $width . '"/>');
                        $doc->addCustomTag('<meta property="og:video:height" content="' . $height . '"/>');
                        $doc->addCustomTag('<meta property="og:video:type" content="application/x-shockwave-flash"/>');
                        //$doc->addCustomTag('<meta property="og:video:url" content="' . $utilities->relToAbs(hwdMediaShareVideos::getMp4($item)) . '"/>');
                        //$doc->addCustomTag('<meta property="og:video:type" content="video/mp4"/>');
                        return true;
                        break;
                } 
                
                return false;
	}
        
        /**
         * Get location of the player SWF file
         *
         * @param integer $seconds Number of seconds to parse
         * @return array
         */
        function getOgVideoTag($item)
        {
                // Load hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                // Load hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                $flv = hwdMediaShareDownloads::flvUrl($item);
                $mp4 = hwdMediaShareDownloads::mp4Url($item);
                $jpg = hwdMediaShareDownloads::jpgUrl($item);

                if ($mp4 || $flv)
                {
                        $pluginClass = 'plgHwdmediashare'.$config->get('media_player');
                        $pluginPath = JPATH_ROOT.'/plugins/hwdmediashare/'.$config->get('media_player').'/'.$config->get('media_player').'.php';

                        // Import hwdMediaShare plugins
                        if (file_exists($pluginPath))
                        {
                                JLoader::register($pluginClass, $pluginPath);
                                $player = call_user_func(array($pluginClass, 'getInstance'));
                                if (method_exists($player, 'getOgVideoTag'))
                                {
                                        $params = new JRegistry('{"mp4":"'.$mp4.'","flv":"'.$flv.'","jpg":"'.$jpg.'"}');
                                        return $player->getOgVideoTag($params);
                                }
                        }
                }
        }        
}
