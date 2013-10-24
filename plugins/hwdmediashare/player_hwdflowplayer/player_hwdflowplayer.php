<?php
/**
 * @version    $Id: player_hwdflowplayer.php 1117 2013-02-15 10:26:04Z dhorsfall $
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
class plgHwdmediasharePlayer_HwdFlowPlayer
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
			$c = 'plgHwdmediasharePlayer_HwdFlowPlayer';
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
                if (file_exists(JPATH_ROOT.'/plugins/content/hwdflowplayer/hwdflowplayer.php'))
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

                if ($params->get('flv') || $params->get('mp4'))
                {
                        hwdMediaShareFactory::load('utilities');
                        $utilities = hwdMediaShareUtilities::getInstance();

                        if ($config->get('protect_media') == 1)
                        {
                                $flv   = ($params->get('flv') ? htmlspecialchars_decode($params->get('flv')) : '');
                                $mp4   = ($params->get('mp4') ? htmlspecialchars_decode($params->get('mp4')) : '');
                                $webm  = ($params->get('webm') ? htmlspecialchars_decode($params->get('webm')) : '');
                                $ogg   = ($params->get('ogg') ? htmlspecialchars_decode($params->get('ogg')) : '');
                                $image = ($params->get('jpg') ? htmlspecialchars_decode($params->get('jpg')) : '');
                        }
                        else
                        {
                                $flv   = ($params->get('flv') ? $params->get('flv') : '');
                                $mp4   = ($params->get('mp4') ? $params->get('mp4') : '');
                                $webm  = ($params->get('webm') ? $params->get('webm') : '');
                                $ogg   = ($params->get('ogg') ? $params->get('ogg') : '');
                                $image = ($params->get('jpg') ? $params->get('jpg') : '');
                        }

                        $this->width = $utilities->getMediaWidth();
                        $this->height = (int) ($this->width*$config->get('video_aspect',0.75));

                        ob_start();
                        echo  JHtml::_('content.prepare', '<center>{hwdflowplayer align=center,provider=video,flv='.$flv.',mp4='.$mp4.',width='.$this->width.',height='.$this->height.',autostart='.$autoplayBoolean.',image='.$image.'}</center>');
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
                
                $autoplayBoolean = (JRequest::getInt('media_autoplay', $config->get('media_autoplay')) == 1 ? 'true' : 'false');

                if ($params->get('mp3'))
                {
                        hwdMediaShareFactory::load('utilities');
                        $utilities = hwdMediaShareUtilities::getInstance();

                        if ($config->get('protect_media') == 1)
                        {
                                $media = ($params->get('mp3') ? htmlspecialchars_decode($params->get('mp3')) : ""); 
                                $image = ($params->get('jpg') ? htmlspecialchars_decode($params->get('jpg')) : ""); 
                        }     
                        else
                        {
                                $media = ($params->get('mp3') ? $params->get('mp3') : ""); 
                                $image = ($params->get('jpg') ? $params->get('jpg') : ""); 
                        }
   
                        $this->width = $utilities->getMediaWidth();
                        $this->height = ($image) ? (int) ($this->width*$config->get('video_aspect',0.75)) : 24;

                        ob_start();
                        echo  JHtml::_('content.prepare', '<center>{hwdflowplayer align=center,provider=sound,mp3='.$media.',width='.$this->width.',height='.$this->height.',autostart='.$autoplayBoolean.',image='.$image.',controlbarposition=bottom,viral=0}</center>');
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
	public function getRtmpPlayer($params)
	{
                // Load hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                $autoplayBoolean = (JRequest::getInt('media_autoplay', $config->get('media_autoplay')) == 1 ? 'true' : 'false');

                if ($params->get('file') || $params->get('streamer'))
                {
                        hwdMediaShareFactory::load('utilities');
                        $utilities = hwdMediaShareUtilities::getInstance();

			$image = ($params->get('jpg') ? $params->get('jpg') : "");
                                                
                        $this->width = $utilities->getMediaWidth();
                        $this->height = (int) ($this->width*$config->get('video_aspect',0.75));                     
   
                        ob_start();
                        echo  JHtml::_('content.prepare', '<center>{hwdflowplayer align=center,provider=rtmp,file='.$params->get('file').',streamer='.$params->get('streamer').',width='.$this->width.',height='.$this->height.',autostart='.$autoplayBoolean.',image='.$image.'}</center>');
                        $html = ob_get_contents();
                        ob_end_clean();
                }
                return $html;
        }
}