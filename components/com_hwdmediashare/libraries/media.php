<?php
/**
 * @version    SVN $Id: media.php 1547 2013-06-11 10:47:21Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * hwdMediaShare framework media class
 *
 * @package hwdMediaShare
 * @since   0.1
 */
abstract class hwdMediaShareMedia
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
	 * Returns the hwdMediaShareMedia object, only creating it if it
	 * doesn't already exist.
	 *
	 * @return  hwdMediaShareMedia A hwdMediaShareMedia object.
	 * @since   0.1
	 */
	public static function getInstance()
	{
		static $instance;

		if (!isset ($instance))
                {
			$c = 'hwdMediaShareMedia';
                        $instance = new $c;
		}

		return $instance;
	}
        
	/**
	 * Method to render a media item
         * 
	 * @since   0.1
	 **/
	public function get($item)
	{
                // Load hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

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
                                $remote = new $remotePluginClass();
                                return $remote->display($item);
                        }
                        else
                        {
                                return null;
                        }
                        break;
                    case 3:
                        // Embed code
                        return $item->embed_code;
                        break;
                    case 4:
                        // RTMP
                        hwdMediaShareFactory::load('rtmp');
                        return hwdMediaShareRtmp::get($item);
                        break;
                    case 6:
                        // Platform
                        $pluginClass = 'plgHwdmediashare'.$config->get('platform');
                        $pluginPath = JPATH_ROOT.'/plugins/hwdmediashare/'.$config->get('platform').'/'.$config->get('platform').'.php';

                        // Import hwdMediaShare plugins
                        if (file_exists($pluginPath))
                        {
                                JLoader::register($pluginClass, $pluginPath);
                                $player = call_user_func(array($pluginClass, 'getInstance'));
                                return $player->display($item);
                        }
                        break;
                    case 7:
                        // Linked file
                        hwdMediaShareFactory::load('documents');
                        return hwdMediaShareDocuments::get($item);
                        break;
                }
                
                if(!isset($item->media_type) || $item->media_type == 0)
                {
                        $item->media_type = hwdMediaShareMedia::loadMediaType($item);
                }

                switch ($item->media_type) {
                    case 1:
                        // Audio
                        hwdMediaShareFactory::load('audio');
                        return hwdMediaShareAudio::get($item);
                        break;
                    case 2:
                        // Document
                        hwdMediaShareFactory::load('documents');
                        return hwdMediaShareDocuments::get($item);
                        break;
                    case 3:
                        // Image
                        hwdMediaShareFactory::load('images');
                        return hwdMediaShareImages::get($item);
                        break;
                    case 4:
                        // Video
                        hwdMediaShareFactory::load('videos');
                        return hwdMediaShareVideos::get($item);
                        break;
                }

                return false;
	}

        /**
	 * Method to get the type of media from the media extension
         * 
	 * @since   0.1
	 **/
	public static function loadMediaType($item)
	{
                if (!isset($item->ext_id)) return;
                
                // Create a new query object.
                $db = JFactory::getDBO();
                $query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('a.media_type');

                // From the albums table
                $query->from('#__hwdms_ext AS a');

                $query->where($db->quoteName('id').' = '.$item->ext_id);

                $db->setQuery($query);
                $result = $db->loadResult();
                
                if ($result > 0)
                {
                        // If we get a result then it is a local media type
                        return $db->loadResult();
                }
                else
                {
                        if ($item->media_type > 0)
                        {
                                return $item->media_type;
                        }
                        else
                        {
                                return 0;
                        } 
                }                
	}
        
        /**
	 * Method to get the media icon for administrator
         * 
	 * @since   0.1
	 **/
	public function getMediaTypeIcon( &$item )
	{
                if (!isset($item->media_type)) return;

                switch ($item->media_type) {
                    case 1:
                        return JURI::root( true ).'/media/com_hwdmediashare/assets/images/audio.png';
                        break;
                    case 2:
                        return JURI::root( true ).'/media/com_hwdmediashare/assets/images/document.png';
                        break;
                    case 3:
                        return JURI::root( true ).'/media/com_hwdmediashare/assets/images/image.png';
                        break;
                    case 4:
                        return JURI::root( true ).'/media/com_hwdmediashare/assets/images/video.png';
                        break;
                    }
	}
        
        /**
	 * Method to get permenant link to a media item
         * 
	 * @since   0.1
	 **/
	public function getPermalink( $id )
	{
                if(@$_SERVER['HTTPS'])
                {
                        $link = 'https://';
                }
                else
                {
                        $link = 'http://';
                } 
                $link.= $_SERVER['SERVER_NAME'];
                $link.= JRoute::_('index.php?option=com_hwdmediashare&view=mediaitem&id='.$id);
                return $link;
	}
        
        /**
	 * Method to get embed code for a media item
         * 
	 * @since   0.1
	 **/
	public function getEmbedCode( $id )
	{
                $width = 560;
                $height = 315;
                if(@$_SERVER['HTTPS'])
                {
                        $link = 'https://';
                }
                else
                {
                        $link = 'http://';
                } 
                $link.= $_SERVER['SERVER_NAME'];
                $link.= JRoute::_('index.php?option=com_hwdmediashare&task=get.embed&id='.$id.'&width='.$width.'&height='.$height);
                $code = '<iframe width="'.$width.'" height="'.$height.'" src="'.$link.'" frameborder="0" scrolling="no"></iframe>';
                return $code;
	}
        
	/**
	 * Method to get human readable media type
         * 
         * @since   0.1
	 **/
	public function getType($item)
	{
                switch ($item->type) {
                    case 1:
                        return JText::_('COM_HWDMS_LOCAL_MEDIA');
                        break;
                    case 2:
                        return JText::_('COM_HWDMS_REMOTE_MEDIA');
                        break;
                    case 3:
                        return JText::_('COM_HWDMS_EMBEDDED_MEDIA');
                        break;
                    case 4:
                        return JText::_('COM_HWDMS_RTMP');
                        break;
                    case 5:
                        return JText::_('COM_HWDMS_CDN');
                        break;
                    case 6:
                        return JText::_('COM_HWDMS_MEDIA_HOSTING_PLATFORM');
                        break;
                    case 7:
                        return JText::_('COM_HWDMS_REMOTE_FILE');
                        break;                   
                }
	}
        
        /**
	 * Method to get human readable media type
         * 
         * @since   0.1
	 **/
	public function getMediaType($item)
	{
                switch ($item->media_type) {
                    case 1:
                        return JText::_('COM_HWDMS_AUDIO');
                        break;
                    case 2:
                        return JText::_('COM_HWDMS_DOCUMENT');
                        break;
                    case 3:
                        return JText::_('COM_HWDMS_IMAGE');
                        break;
                    case 4:
                        return JText::_('COM_HWDMS_VIDEO');
                        break;
                }
	}
        
	/**
	 * Method to render a media item
         * 
	 * @since   0.1
	 **/
	public function getMeta($item)
	{
                if($item->type != 1) return false;
                
                if(!isset($item->media_type))
                {
                        $item->media_type = hwdMediaShareMedia::getMediaType($item);
                }
                
                switch ($item->media_type) {
                    case 1:
                        // Audio
                        hwdMediaShareFactory::load('audio');
                        return hwdMediaShareAudio::getMeta($item);
                        break;
                    case 2:
                        // Document
                        hwdMediaShareFactory::load('documents');
                        return hwdMediaShareDocuments::getMeta($item);
                        break;
                    case 3:
                        // Image
                        hwdMediaShareFactory::load('images');
                        return hwdMediaShareImages::getMeta($item);
                        break;
                    case 4:
                        // Video
                        hwdMediaShareFactory::load('videos');
                        return hwdMediaShareVideos::getMeta($item);
                        break;
                }

                return false;
	}
        
        /**
         * Convert number of seconds into hours, minutes and seconds
         * and return an array containing those values
         *
         * @param integer $seconds Number of seconds to parse
         * @return array
         */
        function secondsToTime($seconds, $returnObject = false)
        {
                // Extract hours
                $hours = floor($seconds / (60 * 60));

                // Extract minutes
                $divisor_for_minutes = $seconds % (60 * 60);
                $minutes = floor($divisor_for_minutes / 60);

                // Extract the remaining seconds
                $divisor_for_seconds = $divisor_for_minutes % 60;
                $seconds = ceil($divisor_for_seconds);

                // Return the final array
                $obj = array(
                    "h" => (int) $hours,
                    "m" => (int) $minutes,
                    "s" => (int) $seconds,
                );

                if ($returnObject)
                {
                        return $obj;
                }
                else
                {
                        // Prepent seconds with zero if necessary
                        if ($seconds < 10)
                        {
                                $seconds = '0'.$seconds;
                        }
                        
                        if ($hours > 0)
                        {
                                return "$hours:$minutes:$seconds";
                        }
                        else
                        {
                                return "$minutes:$seconds";
                        }
                }
        }
}