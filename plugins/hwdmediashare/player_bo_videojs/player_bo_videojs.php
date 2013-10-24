<?php
/**
 * @version    $Id: player_bo_videojs.php 1116 2013-02-15 10:25:45Z dhorsfall $
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


/**
 * hwdMediaShare framework files class
 *
 * @package hwdMediaShare
 * @since   0.1
 */
class plgHwdmediasharePlayer_Bo_videoJS
{ 
        var $width;
        var $height;
        
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

		if (!isset ($instance))
                {
			$c = 'plgHwdmediasharePlayer_Bo_videoJS';
                        $instance = new $c;
		}

		return $instance;
	}

        /**
	 * Method to add a file to the database
         *
	 * @since   0.1
	 **/
	public function checkInstalled()
	{
                if (file_exists(JPATH_ROOT.'/plugins/content/bo_videojs/bo_videojs.php'))
                {
                        return true;
                }
                else
                {
                        return false;
                }
        }
        
        /**
	 * Method to add a file to the database
         *
	 * @since   0.1
	 **/
	public function preloadAssets()
	{
        }
    
        /**
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/
	public function getVideoPlayer($params)
	{
                // Load hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                $autoplayBoolean = (JRequest::getInt('media_autoplay', $config->get('media_autoplay')) == 1 ? 'true' : 'false');
                     
                if ($params->get('flv') || $params->get('mp4') || $params->get('webm') || $params->get('ogg'))
                {
                        hwdMediaShareFactory::load('utilities');
                        $utilities = hwdMediaShareUtilities::getInstance();
   
                        $this->width = $utilities->getMediaWidth();
                        $this->height = (int) ($this->width*$config->get('video_aspect',0.75));
                        
                        ob_start();
                        echo  JHtml::_('content.prepare', '<center>{bo_videojs width='.$this->width.' height='.$this->height.' autoplay='.$autoplayBoolean.' video_mp4='.$params->get('mp4').' video_webm='.$params->get('webm').' video_ogg='.$params->get('ogg').' flash='.$params->get('flv').' image='.$params->get('jpg').'}</center>');
                        $html = ob_get_contents();
                        ob_end_clean();
                }
                return $html;            
        }   
    
        /**
	 * Method to show an audio player
         * 
	 * @since   0.1
	 **/
	public function getAudioPlayer($params)
	{
                $html = null;
                if ($params->get('mp3') || $params->get('ogg'))
                {
                        ob_start();
                        ?>No supported players<?php
                        $html = ob_get_contents();
                        ob_end_clean();
                }
                return $html;            
        }  
        
	public function getRtmpPlayer($params)
	{
                $html = null;
                if ($params->get('file') || $params->get('streamer'))
                {
                        ob_start();
                        ?>No supported players<?php
                        $html = ob_get_contents();
                        ob_end_clean();
                }
                return $html;            
        }          
}