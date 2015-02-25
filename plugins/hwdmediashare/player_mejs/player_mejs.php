<?php
/**
 * @package     Joomla.site
 * @subpackage  Plugin.hwdmediashare.player_mejs
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class plgHwdmediasharePlayer_MEjs extends JObject
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
	 * Returns the plgHwdmediasharePlayer_MEjs object, only creating it if it
	 * doesn't already exist.
	 *
	 * @access  public
	 * @return  object  The plgHwdmediasharePlayer_MEjs object.
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
	 * Method to preload any assets for this player.
         * 
         * @access  public
	 * @param   JRegistry  $params  The plyaer plugin parameters.
         * @return  void
	 **/
	public function preloadAssets($params)
	{
                // Initialise variable.
                $app = JFactory::getApplication();
                $doc = JFactory::getDocument();

                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                JHtml::_('bootstrap.framework');
                $doc->addScript(JURI::root() . "/plugins/hwdmediashare/player_mejs/assets/mediaelement-and-player.min.js");
                
                // Load skin.
                if (JFile::exists(JPATH_SITE . "/plugins/hwdmediashare/player_mejs/assets/skins/" . $params->get('skin', 'dark') . "/" . $params->get('skin', 'dark') . ".css"))
                {
                        $doc->addStyleSheet(JURI::root() . "plugins/hwdmediashare/player_mejs/assets/skins/" . $params->get('skin', 'dark') . "/" . $params->get('skin', 'dark') . ".css");
                }
        }

        /**
	 * Method to render the player for a video.
         * 
	 * @access  public
	 * @param   object     $item     The item being displayed.
	 * @param   JRegistry  $sources  Media sources for the item.
	 * @return  void
	 **/
	public function getVideoPlayer($item, $sources)
	{
		// Initialise variables.
                $app = JFactory::getApplication();
                $doc = JFactory::getDocument();

                // Get HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // Load plugin.
		$plugin = JPluginHelper::getPlugin('hwdmediashare', 'player_mejs');
		
                // Load the language file.
                $lang = JFactory::getLanguage();
                $lang->load('plg_hwdmediashare_player_mejs', JPATH_ADMINISTRATOR, $lang->getTag());

                if (!$plugin)
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_PLAYER_MEJS_ERROR_NOT_PUBLISHED'));
                        return false;
                }

                // Load parameters.
                $params = new JRegistry($plugin->params);
                
                // Load poster.
                $poster = hwdMediaShareThumbnails::getVideoPreview($item);
                        
                // Preload assets.
                $this->preloadAssets($params);
                               
                ob_start();
                ?>
<div class="media-respond" style="max-width:<?php echo $config->get('mediaitem_size', '500'); ?>px;">
  <div id="media-aspect-<?php echo $item->id; ?>" class="media-aspect" data-aspect="<?php echo $config->get('video_aspect', '0.75'); ?>"></div>
  <div class="media-content mejs-<?php echo $params->get('skin', 'dark'); ?>">
    <video id="media-mejs-<?php echo $item->id; ?>" controls="controls" preload="none" class="" width="100%" height="100%"
      <?php echo $poster ? 'poster="' . $poster . '"' : ''; ?>
      <?php echo $config->get('media_autoplay') ? 'autoplay="autoplay"' : ''; ?>
    >
      <?php if ($sources->get('mp4')) : ?><source src="<?php echo $sources->get('mp4')->url; ?>" type="video/mp4" /><?php endif; ?>
      <?php if ($sources->get('webm')) : ?><source src="<?php echo $sources->get('webm')->url; ?>" type="video/webm" /><?php endif; ?>
      <?php if ($sources->get('ogg')) : ?><source src="<?php echo $sources->get('ogg')->url; ?>" type="video/ogg" /><?php endif; ?>
      <?php if ($sources->get('flv')) : ?><source src="<?php echo $sources->get('flv')->url; ?>" type="video/flv" /><?php endif; ?>
    </video>
  </div>
</div>  
                <?php
                $html = ob_get_contents();
                ob_end_clean();
                
                $doc->addScriptDeclaration("
jQuery(document).ready(function(){
  jQuery('#media-mejs-" . $item->id . "').mediaelementplayer({
    videoVolume: 'horizontal',
    features: ['playpause', 'loop', 'current', 'progress', 'duration', 'tracks', 'volume', 'sourcechooser', 'playlist', 'fullscreen', 'postroll'],
    pauseOtherPlayers: true
  }); 
});
                "); 
                        
                return $html;            
        }   

        /**
	 * Method to render the player for an audio.
         * 
	 * @access  public
	 * @param   object     $item     The item being displayed.
	 * @param   JRegistry  $sources  Media sources for the item.
	 * @return  void
	 **/
	public function getAudioPlayer($item, $sources)
	{
		// Initialise variables.
                $app = JFactory::getApplication();
                $doc = JFactory::getDocument();

                // Get HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // Load plugin.
		$plugin = JPluginHelper::getPlugin('hwdmediashare', 'player_mejs');
		
                // Load the language file.
                $lang = JFactory::getLanguage();
                $lang->load('plg_hwdmediashare_player_mejs', JPATH_ADMINISTRATOR, $lang->getTag());

                if (!$plugin)
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_PLAYER_MEJS_ERROR_NOT_PUBLISHED'));
                        return false;
                }

                // Load parameters.
                $params = new JRegistry($plugin->params);
                
                // Preload assets.
                $this->preloadAssets($params);
                        
                switch ($params->get('skin', 'dark'))
                {
                        case 'classic':
                                $height = 30;
                        break;
                        case 'dark':
                                $height = 48;
                        break;
                        case 'ted':
                                $height = 65;
                        break;
                        case 'wmp':
                                $height = 80;
                        break;                
                        default:
                                $height = 30;
                }
                ob_start();
                ?>
<div class="media-respond" style="max-width:<?php echo $config->get('mediaitem_size', '500'); ?>px;">
  <div class="mejs-<?php echo $params->get('skin', 'dark'); ?>">
    <audio id="media-mejs-<?php echo $item->id; ?>" controls="controls" preload="none" class="" width="100%" height="<?php echo $height; ?>"
      <?php echo $config->get('media_autoplay') ? 'autoplay="autoplay"' : ''; ?>
    >
      <?php if ($sources->get('mp3')) : ?><source src="<?php echo $sources->get('mp3')->url; ?>" type="audio/mp3" /><?php endif; ?>
      <?php if ($sources->get('ogg')) : ?><source src="<?php echo $sources->get('ogg')->url; ?>" type="audio/ogg" /><?php endif; ?>
    </audio>
  </div>
</div>   
                <?php
                $html = ob_get_contents();
                ob_end_clean();
                
                $doc->addScriptDeclaration("
jQuery(document).ready(function(){
  jQuery('#media-mejs-" . $item->id . "').mediaelementplayer({
    videoVolume: 'horizontal',
    features: ['playpause', 'loop', 'current', 'progress', 'duration', 'tracks', 'volume', 'sourcechooser', 'playlist', 'fullscreen', 'postroll'],
    pauseOtherPlayers: true
  }); 
});
                "); 
                
                return $html; 
        }  
        
        /**
	 * Method to render the player for streams.
         * 
	 * @access  public
	 * @param   object     $item     The item being displayed.
	 * @param   JRegistry  $sources  Media sources for the item.
	 * @return  void
	 **/
	public function getStreamPlayer($item, $sources)
	{
		// Initialise variables.
                $app = JFactory::getApplication();
                $doc = JFactory::getDocument();

                // Get HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // Load plugin.
		$plugin = JPluginHelper::getPlugin('hwdmediashare', 'player_mejs');
		
                // Load the language file.
                $lang = JFactory::getLanguage();
                $lang->load('plg_hwdmediashare_player_mejs', JPATH_ADMINISTRATOR, $lang->getTag());

                if (!$plugin)
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_PLAYER_MEJS_ERROR_NOT_PUBLISHED'));
                        return false;
                }

                // Load parameters.
                $params = new JRegistry($plugin->params);
                
                // Load poster.
                $poster = hwdMediaShareThumbnails::getVideoPreview($item);
                        
                // Preload assets.
                $this->preloadAssets($params);

                ob_start();
                ?>
<div class="media-respond" style="max-width:<?php echo $config->get('mediaitem_size', '500'); ?>px;">
  <div id="media-aspect-<?php echo $item->id; ?>" class="media-aspect" data-aspect="<?php echo $config->get('video_aspect', '0.75'); ?>"></div>
  <div class="media-content mejs-<?php echo $params->get('skin', 'dark'); ?>">
    <video id="media-mejs-<?php echo $item->id; ?>" controls="controls" preload="none" class="" width="100%" height="100%"
      <?php echo $poster ? 'poster="' . $poster . '"' : ''; ?>
      <?php echo $config->get('media_autoplay') ? 'autoplay="autoplay"' : ''; ?>
    >
      <?php foreach($sources as $i => $source): ?>
        <?php if ($source->type == 1): 
          $rtmp = hwdMediaShareStreaming::parseRtmpStream($source->url); ?>
          <source src="<?php echo $rtmp->stream; ?>" type="video/rtmp" id="media-flow-rtmp-source-<?php echo $item->id; ?>" />
        <?php elseif ($source->type == 2): ?>
          <source src="<?php echo $source->url; ?>" type="application/x-mpegURL" />
        <?php elseif ($source->type == 3): ?>
          <source src="<?php echo $source->url; ?>" type="video/mp4" />
        <?php endif; ?>
      <?php endforeach; ?>
    </video>
  </div>
</div>  
                <?php
                $html = ob_get_contents();
                ob_end_clean();
                
                $doc->addScriptDeclaration("
jQuery(document).ready(function(){
  // The Joomla SEF plugin will attempt to fix this problem src attribute, so we undo that before loading the player.
  var el = jQuery('#media-rtmp-source-" . $item->id . "');
  if (el.length){
    var src = el.attr('src').replace('" . JURI::root(true) . "/', '');
    jQuery('#media-rtmp-source-" . $item->id . "').attr('src', src);
  }
      
  jQuery('#media-mejs-" . $item->id . "').mediaelementplayer({
    flashStreamer: '" . (isset($rtmp->application) ? $rtmp->application : '') . "',
    videoVolume: 'horizontal',
    features: ['playpause', 'loop', 'current', 'progress', 'duration', 'tracks', 'volume', 'sourcechooser', 'playlist', 'fullscreen', 'postroll'],
    pauseOtherPlayers: true
  }); 
});
                "); 
                        
                return $html;            
        }  
                
        /**
	 * Method to render the player for a Youtube video.
         * 
	 * @access  public
	 * @param   object  $item  The item being displayed.
	 * @param   string  $id    The Youtube ID.
	 * @return  void
	 **/
	public function getYoutubePlayer($item, $id)
	{
		// Initialise variables.
                $app = JFactory::getApplication();
                $doc = JFactory::getDocument();

                // Get HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // Load plugin.
		$plugin = JPluginHelper::getPlugin('hwdmediashare', 'player_mejs');
		
                // Load the language file.
                $lang = JFactory::getLanguage();
                $lang->load('plg_hwdmediashare_player_mejs', JPATH_ADMINISTRATOR, $lang->getTag());

                if (!$plugin)
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_PLAYER_MEJS_ERROR_NOT_PUBLISHED'));
                        return false;
                }

                // Load parameters.
                $params = new JRegistry($plugin->params);
                
                // Load poster.
                $poster = hwdMediaShareThumbnails::getVideoPreview($item);
                        
                // Preload assets.
                $this->preloadAssets($params);
                               
                ob_start();
                ?>
<div class="media-respond" style="max-width:<?php echo $config->get('mediaitem_size', '500'); ?>px;">
  <div id="media-aspect-<?php echo $item->id; ?>" class="media-aspect" data-aspect="<?php echo $config->get('video_aspect', '0.75'); ?>"></div>
  <div class="media-content mejs-<?php echo $params->get('skin', 'dark'); ?>">
    <video id="media-mejs-<?php echo $item->id; ?>" controls="controls" preload="none" class="" width="100%" height="100%"
      <?php echo $poster ? 'poster="' . $poster . '"' : ''; ?>
      <?php echo $config->get('media_autoplay') ? 'autoplay="autoplay"' : ''; ?>
    >
      <?php if ($id) : ?><source src="http://www.youtube.com/watch?v=<?php echo $id; ?>" type="video/youtube" /><?php endif; ?>
    </video>
  </div>
</div>       
                <?php
                $html = ob_get_contents();
                ob_end_clean();
                
                $doc->addScriptDeclaration("
jQuery(document).ready(function(){
  jQuery('#media-mejs-" . $item->id . "').mediaelementplayer({
    videoVolume: 'horizontal',
    features: ['playpause', 'loop', 'current', 'progress', 'duration', 'tracks', 'volume', 'sourcechooser', 'playlist', 'fullscreen', 'postroll'],
    pauseOtherPlayers: true
  }); 
});
                "); 
                return $html;   
        }     
}
