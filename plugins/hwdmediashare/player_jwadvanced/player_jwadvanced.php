<?php
/**
 * @version    $Id: player_jwadvanced.php 1651 2013-08-16 09:30:35Z dhorsfall $
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
class plgHwdmediasharePlayer_JwAdvanced
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
			$c = 'plgHwdmediasharePlayer_JwAdvanced';
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
                if (file_exists(JPATH_ROOT.'/plugins/content/plg_jwadvanced/plg_jwadvanced.php'))
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
                $doc = JFactory::getDocument();
                            
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
                                $media = ($params->get('mp4') ? htmlspecialchars_decode($params->get('mp4')) : htmlspecialchars_decode($params->get('flv')));
                                $image = ($params->get('jpg') ? htmlspecialchars_decode($params->get('jpg')) : ""); 
                        }     
                        else
                        {
                                $media = ($params->get('mp4') ? $params->get('mp4') : $params->get('flv'));
                                $image = ($params->get('jpg') ? $params->get('jpg') : ""); 
                        }

                        $this->width = $utilities->getMediaWidth();
                        $this->height = (int) $config->get('mediaitem_height') ? $config->get('mediaitem_height') : $this->width*$config->get('video_aspect',0.75);
                        
                        $this->width = '100%';
                        $this->height = '100%';
                        $doc->addStyleDeclaration('#hwd-container .media-respond { max-width:'.$config->get('mediaitem_size', '500').'px!important;}');
                
                        ob_start();
                        echo '<div class="media-respond">';
                        echo '<div class="media-aspect" data-aspect="'.$config->get('video_aspect', '0.75').'"></div>';
                        echo  JHtml::_('content.prepare', '{jwplayer}&file='.$media.'&width='.$this->width.'&height='.$this->height.'&autostart='.$autoplayBoolean.'&provider=video&image='.$image.'&class=media-content{/jwplayer}');
                        echo '</div>';
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
                        $this->height = ($this->width) ? (int) ($this->width*$config->get('video_aspect',0.75)) : 24;

                        ob_start();
                        echo  JHtml::_('content.prepare', '<center>{jwplayer}&file='.$media.'&width='.$this->width.'&height='.$this->height.'&autostart='.$autoplayBoolean.'&provider=audio{/jwplayer}</center>');
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
                        $this->height = ($this->width) ? (int) ($this->width*$config->get('video_aspect',0.75)) : 24;

                        ob_start();
                        echo  JHtml::_('content.prepare', '<center>{jwplayer}&file='.$params->get('file').'&streamer='.$params->get('streamer').'&width='.$this->width.'&height='.$this->height.'&autostart='.$autoplayBoolean.'&provider=rtmp&image='.$image.'{/jwplayer}</center>');
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
	public function getYoutubePlayer($params)
	{
                // Load hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                $autoplayBoolean = (JRequest::getInt('media_autoplay', $config->get('media_autoplay')) == 1 ? 'true' : 'false');

                if ($params->get('id'))
                {
                        hwdMediaShareFactory::load('utilities');
                        $utilities = hwdMediaShareUtilities::getInstance();

                        $image = ($params->get('jpg') ? $params->get('jpg') : 'http://i1.ytimg.com/vi/'.$params->get('id').'/maxresdefault.jpg');    
                                                
                        $this->width = $utilities->getMediaWidth();
                        $this->height = (int) ($this->width*$config->get('video_aspect',0.75));                     
   
                        ob_start();
                        echo  JHtml::_('content.prepare', '<center>{jwplayer}&file=http://www.youtube.com/watch?v='.$params->get('id').'&width='.$this->width.'&height='.$this->height.'&provider=youtube{/jwplayer}</center>');
                        $html = ob_get_contents();
                        ob_end_clean(); 
                }
                return $html;
        }  
        
       /**
	 * Method to create a SWF link to use as an open graph vidoe tag.
         *
	 * @since   0.1
	 **/
	public function getOgVideoTag($params)
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
            $link.= ($params->get('mp4') ? $params->get('mp4') : $params->get('flv'));

            $swf = JURI::root() . 'media/jwadvanced/player/5/player.swf?';
            $swf.= 'file=' . urlencode($link);
            return $swf;
        } 
}