<?php
/**
 * @version    $Id: player_mejs.php 1122 2013-02-15 10:56:11Z dhorsfall $
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
class plgHwdmediasharePlayer_MEjs
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
			$c = 'plgHwdmediasharePlayer_MEjs';
                        $instance = new $c;
		}

		return $instance;
	}

        /**
	 * Method to add a file to the database
         *
	 * @since   0.1
	 **/
	public function preloadAssets()
	{
                $doc = & JFactory::getDocument();
                $doc->addStyleSheet(JURI::root() . "plugins/hwdmediashare/player_mejs/assets/mediaelementplayer.min.css");
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
                
                if ($params->get('flv') || $params->get('mp4') || $params->get('webm') || $params->get('ogg'))
                {
                        $doc = & JFactory::getDocument();
                        $doc->addStyleSheet(JURI::root() . "plugins/hwdmediashare/player_mejs/assets/mediaelementplayer.min.css");
                        //$doc->addScript("http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js");
                        //$doc->addScript(JURI::root() . "/plugins/hwdmediashare/player_mejs/assets/mediaelement-and-player.min.js");
                                                
                        hwdMediaShareFactory::load('utilities');
                        $utilities = hwdMediaShareUtilities::getInstance();
   
                        $this->width = $utilities->getMediaWidth();
                        $this->height = (int) $config->get('mediaitem_height') ? $config->get('mediaitem_height') : $this->width*$config->get('video_aspect',0.75);
                        
                        $doc->addStyleDeclaration('#hwd-container .media-respond { max-width:'.$config->get('mediaitem_size', '500').'px!important;}');
                
                        ob_start();
                        ?>
                        <div class="media-respond">
                        <div class="media-aspect" data-aspect="<?php echo $config->get('video_aspect', '0.75'); ?>"></div>
                        <div class="media-content">
                        <video style="width:100%;height:100%;" id="<?php echo $params->get('id', 'player-'.rand()); ?>" controls="controls" preload="none" <?php if ($params->get('jpg')) : ?>poster="<?php echo $params->get('jpg'); ?>"<?php endif; ?> <?php if ($config->get('media_autoplay') == 1) : ?>autoplay="autoplay"<?php endif; ?>>
                        <?php if ($params->get('mp4')) : ?><source src="<?php echo $params->get('mp4'); ?>" type="video/mp4" /><?php endif; ?>
                        <?php if ($params->get('webm')) : ?><source src="<?php echo $params->get('webm'); ?>" type="video/webm" /><?php endif; ?>
                        <?php if ($params->get('ogg')) : ?><source src="<?php echo $params->get('ogg'); ?>" type="video/ogg" /><?php endif; ?>
                        <?php if ($params->get('flv')) : ?><source src="<?php echo $params->get('flv'); ?>" type="video/flv" /><?php endif; ?>
                        <?php if ($params->get('mp4') || $params->get('flv')) : ?>
                        <object width="<?php echo $this->width; ?>" height="<?php echo $this->height; ?>" type="application/x-shockwave-flash" data="<?php echo JURI::root(); ?>plugins/hwdmediashare/player_mejs/assets/flashmediaelement.swf">
                                <param name="movie" value="<?php echo JURI::root(); ?>plugins/hwdmediashare/player_mejs/assets/flashmediaelement.swf" />
                                <param name="flashvars" value="controls=true&amp;file=<?php echo ($params->get('mp4') ? urlencode($params->get('mp4')) : urlencode($params->get('flv'))); ?>" />
                                <?php if ($params->get('jpg')) : ?><img src="<?php echo $params->get('jpg'); ?>" width="<?php echo $this->width; ?>" height="<?php echo $this->height; ?>" alt="No suitable players" title="No video playback capabilities" /><?php endif; ?>
                        </object>
                        <?php endif; ?>
                        </video>
                        <script type="text/javascript">var jQueryScriptOutputted=false;function initJQuery(){if(typeof(jQuery)=='undefined'){if(!jQueryScriptOutputted){jQueryScriptOutputted=true;document.write("<scr"+"ipt type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js\"></scr"+"ipt>")}setTimeout("initJQuery()",50)}}initJQuery();</script>
                        <script src="<?php echo JURI::root(); ?>/plugins/hwdmediashare/player_mejs/assets/mediaelement-and-player.min.js" type="text/javascript"></script>
                        <script type="text/javascript">
                        var $j = jQuery.noConflict();
                        $j('video').mediaelementplayer({
                            // if the <video width> is not specified, this is the default
                            defaultVideoWidth: 480,
                            // if the <video height> is not specified, this is the default
                            defaultVideoHeight: 270,
                            // if set, overrides <video width>
                            videoWidth: -1,
                            // if set, overrides <video height>
                            videoHeight: -1,
                            // width of audio player
                            audioWidth: 400,
                            // height of audio player
                            audioHeight: 30,
                            // initial volume when the player starts
                            startVolume: 0.8,
                            // useful for <audio> player loops
                            loop: false,
                            // enables Flash and Silverlight to resize to content size
                            enableAutosize: false,
                            // the order of controls you want on the control bar (and other plugins below)
                            features: ['playpause','progress','current','duration','tracks','volume','fullscreen'],
                            // Hide controls when playing and mouse is not over the video
                            alwaysShowControls: false,
                            // force iPad's native controls
                            iPadUseNativeControls: false,
                            // force iPhone's native controls
                            iPhoneUseNativeControls: false,
                            // force Android's native controls
                            AndroidUseNativeControls: false,
                            // forces the hour marker (##:00:00)
                            alwaysShowHours: false,
                            // show framecount in timecode (##:00:00:00)
                            showTimecodeFrameCount: false,
                            // used when showTimecodeFrameCount is set to true
                            framesPerSecond: 25,
                            // turns keyboard support on and off for this instance
                            enableKeyboard: true,
                            // when this player starts, it will pause other players
                            pauseOtherPlayers: true,
                            // array of keyboard commands
                            keyActions: []

                        });</script>
                        <div class="clear"></div>
                        </div></div>
                        <?php 
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
                 
                if ($params->get('mp3') || $params->get('ogg'))
                {
                        $doc = & JFactory::getDocument();
                        $doc->addStyleSheet(JURI::root() . "plugins/hwdmediashare/player_mejs/assets/mediaelementplayer.min.css");

                        hwdMediaShareFactory::load('utilities');
                        $utilities = hwdMediaShareUtilities::getInstance();
   
                        $this->width = $utilities->getMediaWidth();
                        $this->height = (int) ($this->width*$config->get('video_aspect',0.75));
                        
                        // Check for an auotplay override
                        $plugin =& JPluginHelper::getPlugin('hwdmediashare', 'player_mejs');
                        if (isset($plugin->params)) $params->merge(new JRegistry( $plugin->params ));
                        $params->merge(new JRegistry( '{"autoplay":"'.JRequest::getInt('autoplay', $params->get('autoplay')).'"}' ));
    
                        ob_start();
                        ?>
                        <audio style="width:100%;" id="<?php echo $params->get('id', 'player-'.rand()); ?>" controls="controls" preload="none" <?php if ($params->get('autoplay') == 1) : ?>autoplay="autoplay"<?php endif; ?>>
                            <?php if ($params->get('mp3')) : ?><source src="<?php echo $params->get('mp3'); ?>" type="audio/mpeg" /><?php endif; ?>
                            <?php if ($params->get('ogg')) : ?><source src="<?php echo $params->get('ogg'); ?>" type="video/ogg" /><?php endif; ?>
                            <?php if ($params->get('mp3')) : ?>
                            <object width="<?php echo $this->width; ?>" height="<?php echo $this->height; ?>" type="application/x-shockwave-flash" data="<?php echo JURI::root(); ?>plugins/hwdmediashare/player_mejs/assets/flashmediaelement.swf">
                                    <param name="movie" value="<?php echo JURI::root(); ?>plugins/hwdmediashare/player_mejs/assets/flashmediaelement.swf" />
                                    <param name="flashvars" value="controls=true&amp;file=<?php echo $params->get('mp3'); ?>" />
                            </object>
                            <?php endif; ?>
                        </audio>
                        <script type="text/javascript">var jQueryScriptOutputted=false;function initJQuery(){if(typeof(jQuery)=='undefined'){if(!jQueryScriptOutputted){jQueryScriptOutputted=true;document.write("<scr"+"ipt type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js\"></scr"+"ipt>")}setTimeout("initJQuery()",50)}}initJQuery();</script>
                        <script src="<?php echo JURI::root(); ?>/plugins/hwdmediashare/player_mejs/assets/mediaelement-and-player.min.js" type="text/javascript"></script>
                        <script>
                        var $j = jQuery.noConflict();
                        $j('audio').mediaelementplayer({
                            // if the <video width> is not specified, this is the default
                            defaultVideoWidth: 480,
                            // if the <video height> is not specified, this is the default
                            defaultVideoHeight: 270,
                            // if set, overrides <video width>
                            videoWidth: -1,
                            // if set, overrides <video height>
                            videoHeight: -1,
                            // width of audio player
                            audioWidth: 400,
                            // height of audio player
                            audioHeight: 30,
                            // initial volume when the player starts
                            startVolume: 0.8,
                            // useful for <audio> player loops
                            loop: false,
                            // enables Flash and Silverlight to resize to content size
                            enableAutosize: false,
                            // the order of controls you want on the control bar (and other plugins below)
                            features: ['playpause','progress','current','duration','tracks','volume','fullscreen'],
                            // Hide controls when playing and mouse is not over the video
                            alwaysShowControls: false,
                            // force iPad's native controls
                            iPadUseNativeControls: false,
                            // force iPhone's native controls
                            iPhoneUseNativeControls: false,
                            // force Android's native controls
                            AndroidUseNativeControls: false,
                            // forces the hour marker (##:00:00)
                            alwaysShowHours: false,
                            // show framecount in timecode (##:00:00:00)
                            showTimecodeFrameCount: false,
                            // used when showTimecodeFrameCount is set to true
                            framesPerSecond: 25,
                            // turns keyboard support on and off for this instance
                            enableKeyboard: true,
                            // when this player starts, it will pause other players
                            pauseOtherPlayers: true,
                            // array of keyboard commands
                            keyActions: []
                        });</script>
                        <div class="clear"></div>
                        <?php
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

                if ($params->get('file') || $params->get('streamer'))
                {
                        $doc = & JFactory::getDocument();
                        $doc->addStyleSheet(JURI::root() . "plugins/hwdmediashare/player_mejs/assets/mediaelementplayer.min.css");

                        hwdMediaShareFactory::load('utilities');
                        $utilities = hwdMediaShareUtilities::getInstance();
   
                        $this->width = $utilities->getMediaWidth();
                        $this->height = (int) ($this->width*$config->get('video_aspect',0.75));
                        
                        ob_start();
                        ?>
                        <video width="<?php echo $this->width; ?>" height="<?php echo $this->height; ?>" id="<?php echo $params->get('id', 'player-'.rand()); ?>" controls="controls" preload="none" <?php if ($params->get('jpg')) : ?>poster="<?php echo $params->get('jpg'); ?>"<?php endif; ?> <?php if ($config->get('media_autoplay') == 1) : ?>autoplay="autoplay"<?php endif; ?>>
                        <source src="<?php echo $params->get('streamer'); ?>/mp4:<?php echo $params->get('file'); ?>" type="video/mp4" />
                        </video>
                        <script type="text/javascript">var jQueryScriptOutputted=false;function initJQuery(){if(typeof(jQuery)=='undefined'){if(!jQueryScriptOutputted){jQueryScriptOutputted=true;document.write("<scr"+"ipt type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js\"></scr"+"ipt>")}setTimeout("initJQuery()",50)}}initJQuery();</script>
                        <script src="<?php echo JURI::root(); ?>/plugins/hwdmediashare/player_mejs/assets/mediaelement-and-player.min.js" type="text/javascript"></script>
                        <script type="text/javascript">
                        var $j = jQuery.noConflict();
                        $j('video').mediaelementplayer({
                            // if the <video width> is not specified, this is the default
                            defaultVideoWidth: 480,
                            // if the <video height> is not specified, this is the default
                            defaultVideoHeight: 270,
                            // if set, overrides <video width>
                            videoWidth: -1,
                            // if set, overrides <video height>
                            videoHeight: -1,
                            // width of audio player
                            audioWidth: 400,
                            // height of audio player
                            audioHeight: 30,
                            // initial volume when the player starts
                            startVolume: 0.8,
                            // useful for <audio> player loops
                            loop: false,
                            // enables Flash and Silverlight to resize to content size
                            enableAutosize: false,
                            // the order of controls you want on the control bar (and other plugins below)
                            features: ['playpause','progress','current','duration','tracks','volume','fullscreen'],
                            // Hide controls when playing and mouse is not over the video
                            alwaysShowControls: false,
                            // force iPad's native controls
                            iPadUseNativeControls: false,
                            // force iPhone's native controls
                            iPhoneUseNativeControls: false,
                            // force Android's native controls
                            AndroidUseNativeControls: false,
                            // forces the hour marker (##:00:00)
                            alwaysShowHours: false,
                            // show framecount in timecode (##:00:00:00)
                            showTimecodeFrameCount: false,
                            // used when showTimecodeFrameCount is set to true
                            framesPerSecond: 25,
                            // turns keyboard support on and off for this instance
                            enableKeyboard: true,
                            // when this player starts, it will pause other players
                            pauseOtherPlayers: true,
                            // array of keyboard commands
                            keyActions: []

                        });</script>
                        <div class="clear"></div>
                        <?php
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

            $swf = JURI::root() . 'plugins/hwdmediashare/player_mejs/assets/flashmediaelement.swf?controls=true&';
            $swf.= 'file=' . urlencode($link);
            return $swf;
        }        
}