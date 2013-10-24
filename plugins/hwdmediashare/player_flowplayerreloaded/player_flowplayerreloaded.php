<?php
/**
 * @version    $Id: player_flowplayerreloaded.php 991 2013-01-30 09:32:20Z dhorsfall $
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
class plgHwdmediasharePlayer_FlowPlayerReloaded
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
			$c = 'plgHwdmediasharePlayer_FlowPlayerReloaded';
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
                if (file_exists(JPATH_ROOT.'/plugins/system/flowplayerreloaded/flowplayerreloaded.php'))
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
                $image = null;

                if ($params->get('flv') || $params->get('mp4'))
                {
                        hwdMediaShareFactory::load('utilities');
                        $utilities = hwdMediaShareUtilities::getInstance();

                        if ($params->get('jpg')) $image = "img=".$params->get('jpg');
                        
                        $this->width = $utilities->getMediaWidth();
                        $this->height = (int) ($this->width*$config->get('video_aspect',0.75));                     
   
                        ob_start();
                        echo  JHtml::_('content.prepare', '{flowplayer '.$image.' width='.$this->width.' height='.$this->height.'}'.($params->get('mp4') ? urlencode($params->get('mp4')) : urlencode($params->get('flv'))).'{/flowplayer}');
                        $html = ob_get_contents();
                        ob_end_clean();
                }
                return $html;            
        }   
    
        /**
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/
	public function getAudioPlayer($params)
	{
                // Load hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                $image = null;
                
                if ($params->get('mp3'))
                {
                        hwdMediaShareFactory::load('utilities');
                        $utilities = hwdMediaShareUtilities::getInstance();

                        if ($params->get('jpg')) $image = "img=".$params->get('jpg');
                        
                        $this->width = $utilities->getMediaWidth();
                        $this->height = ($image) ? (int) ($this->width*$config->get('video_aspect',0.75)) : 75;

                        ob_start();
                        echo JHtml::_('content.prepare', '{flowplayer '.$image.' width='.$this->width.' height='.$this->height.'}'.urlencode($params->get('mp3')).'{/flowplayer}');
                        $html = ob_get_contents();
                        ob_end_clean();
                }
                return $html;             
        }   
        
        /**
	 * Method to show an rtmp stream
         * 
	 * @since   0.1
	 **/
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