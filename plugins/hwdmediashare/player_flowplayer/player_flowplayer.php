<?php
/**
 * @package     Joomla.site
 * @subpackage  Plugin.hwdmediashare.player_flowplayer
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class plgHwdmediasharePlayer_FlowPlayer extends JObject
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
	 * Returns the plgHwdmediasharePlayer_FlowPlayer object, only creating it if it
	 * doesn't already exist.
	 *
	 * @access  public
	 * @return  object  The plgHwdmediasharePlayer_FlowPlayer object.
	 */
	public static function getInstance()
	{
		static $instance;

		if (!isset ($instance))
                {
			$c = 'plgHwdmediasharePlayer_FlowPlayer';
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
                
                if ($params->get('hosting') == 'cloud')
                {
                        $doc->addScript("//releases.flowplayer.org/5.5.0/commercial/flowplayer.min.js");
                }
                else
                {
                        $doc->addScript(JURI::root() . "plugins/hwdmediashare/player_flowplayer/assets/flowplayer/commercial/flowplayer.min.js");
                }

                // Load skin.
                if (JFile::exists(JPATH_SITE . "/plugins/hwdmediashare/player_flowplayer/assets/flowplayer/skin/" . $params->get('skin', 'minimalist') . ".css"))
                {
                        $doc->addStyleSheet(JURI::root() . "plugins/hwdmediashare/player_flowplayer/assets/flowplayer/skin/" . $params->get('skin', 'minimalist') . ".css");
                }        
                
                // Display logo.
                $doc->addStyleDeclaration(".flowplayer .fp-logo {display: block;opacity: 1;}");
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

                // Load plugin.
		$plugin = JPluginHelper::getPlugin('hwdmediashare', 'player_flowplayer');
		
                // Load the language file.
                $lang = JFactory::getLanguage();
                $lang->load('plg_hwdmediashare_player_flowplayer', JPATH_ADMINISTRATOR, $lang->getTag());

                if (!$plugin)
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_PLAYER_FLOWPLAYER_ERROR_NOT_PUBLISHED'));
                        return false;
                }

                // Load parameters.
                $params = new JRegistry($plugin->params);

                // Get HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig($params);

                // Load poster.
                $poster = hwdMediaShareThumbnails::getVideoPreview($item);
                        
                // Preload assets.
                $this->preloadAssets($params);
                               
                ob_start();
                ?>
<div class="media-respond" style="max-width:<?php echo $config->get('mediaitem_size', '500'); ?>px;">
  <div id="media-aspect-<?php echo $item->id; ?>" class="media-aspect" data-aspect="<?php echo $config->get('video_aspect', '0.75'); ?>"></div>
  <div id="media-flow-<?php echo $item->id; ?>" class="media-content">
    <video 
      <?php echo $config->get('media_autoplay') ? 'autoplay="autoplay"' : ''; ?>
      controls="controls"
      height="100%"
      <?php echo $config->get('media_loop') ? 'loop="loop"' : ''; ?>
      <?php echo $config->get('media_muted') ? 'muted="muted"' : ''; ?>
      <?php echo $poster ? 'poster="' . $poster . '"' : ''; ?>
      preload="metadata"
      width="100%"
    >
      <?php if ($sources->get('mp4')) : ?><source src="<?php echo $sources->get('mp4')->url; ?>" type="video/mp4" /><?php endif; ?>
      <?php if ($sources->get('webm')) : ?><source src="<?php echo $sources->get('webm')->url; ?>" type="video/webm" /><?php endif; ?>
      <?php if ($sources->get('ogg')) : ?><source src="<?php echo $sources->get('ogg')->url; ?>" type="video/ogg" /><?php endif; ?>
      <?php if ($sources->get('flv')) : ?><source src="<?php echo $sources->get('flv')->url; ?>" type="video/flv" /><?php endif; ?>
    </video>
  </div>
</div>
<script type="text/javascript">
(function($) {
  $(document).ready(function () {
    $("#media-flow-<?php echo $item->id; ?>").flowplayer({ 
      swf: '<?php echo JURI::root( true ); ?>/plugins/hwdmediashare/player_flowplayer/assets/flowplayer/commercial/flowplayer.swf'
      , ratio: <?php echo $config->get('video_aspect', '0.56'); ?>
      , engine: '<?php echo ($config->get('fallback', '3') == '3' ? 'flash' : 'html5'); ?>'
      , debug: <?php echo ($config->get('debug') == '1' ? 'true' : 'false'); ?>
      , key: '<?php echo $config->get('licensekey'); ?>'
      , logo: '<?php echo JURI::root() . $config->get('logofile'); ?>'
   });
  });
})(jQuery);
</script>         
                <?php
                $html = ob_get_contents();
                ob_end_clean();
                
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

                // Load plugin.
		$plugin = JPluginHelper::getPlugin('hwdmediashare', 'player_flowplayer');
		
                // Load the language file.
                $lang = JFactory::getLanguage();
                $lang->load('plg_hwdmediashare_player_flowplayer', JPATH_ADMINISTRATOR, $lang->getTag());

                if (!$plugin)
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_PLAYER_FLOWPLAYER_ERROR_NOT_PUBLISHED'));
                        return false;
                }

                // Load parameters.
                $params = new JRegistry($plugin->params);

                // Get HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig($params);

                // Load poster.
                $poster = hwdMediaShareThumbnails::getVideoPreview($item);
                        
                // Preload assets.
                $this->preloadAssets($params);
                          
                $this->setError(JText::_('PLG_HWDMEDIASHARE_PLAYER_FLOWPLAYER_ERROR_NO_AUDIO_SUPPORT'));
                return false;
                        
                ob_start();
                ?>
                <?php
                $html = ob_get_contents();
                ob_end_clean();
                
                return $html; 
        }  
        
        /**
	 * Method to render the player for a video.
         * 
	 * @access  public
	 * @param   object     $item     The item being displayed.
	 * @param   JRegistry  $sources  Media sources for the item.
	 * @return  void
	 **/
	public function getRtmpPlayer($item, $sources)
	{
		// Initialise variables.
                $app = JFactory::getApplication();

                // Load plugin.
		$plugin = JPluginHelper::getPlugin('hwdmediashare', 'player_flowplayer');
		
                // Load the language file.
                $lang = JFactory::getLanguage();
                $lang->load('plg_hwdmediashare_player_flowplayer', JPATH_ADMINISTRATOR, $lang->getTag());

                if (!$plugin)
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_PLAYER_FLOWPLAYER_ERROR_NOT_PUBLISHED'));
                        return false;
                }

                // Load parameters.
                $params = new JRegistry($plugin->params);

                // Get HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig($params);

                // Load poster.
                $poster = hwdMediaShareThumbnails::getVideoPreview($item);
                        
                // Preload assets.
                $this->preloadAssets($params);
                               
                ob_start();
                ?>
<div class="media-respond" style="max-width:<?php echo $config->get('mediaitem_size', '500'); ?>px;">
  <div id="media-aspect-<?php echo $item->id; ?>" class="media-aspect" data-aspect="<?php echo $config->get('video_aspect', '0.75'); ?>"></div>
  <div id="media-flow-<?php echo $item->id; ?>" class="media-content">
    <video 
      <?php echo $config->get('media_autoplay') ? 'autoplay="autoplay"' : ''; ?>
      controls="controls"
      height="100%"
      <?php echo $config->get('media_loop') ? 'loop="loop"' : ''; ?>
      <?php echo $config->get('media_muted') ? 'muted="muted"' : ''; ?>
      <?php echo $poster ? 'poster="' . $poster . '"' : ''; ?>
      preload="metadata"
      width="100%"
    >
      <?php if ($item->get('file')) : ?><source src="<?php echo $item->get('file'); ?>" type="video/flash" id="media-rtmp-source-<?php echo $item->id; ?>" /><?php endif; ?>
    </video>
  </div>
</div>
<script type="text/javascript">
(function($) {
  $(document).ready(function () {
    // The Joomla SEF plugin will attempt to fix this problem src attribute, so we undo that before loading the player.
    var newSrc = $('#media-rtmp-source-<?php echo $item->id; ?>').attr('src').replace('<?php echo JURI::root(true) . '/'; ?>', '');
    $('#media-rtmp-source-<?php echo $item->id; ?>').attr('src', newSrc);
                
    $("#media-flow-<?php echo $item->id; ?>").flowplayer({ 
      swf: '<?php echo JURI::root( true ); ?>/plugins/hwdmediashare/player_flowplayer/assets/flowplayer/commercial/flowplayer.swf'
      , rtmp: '<?php echo $item->get('streamer'); ?>'
      , ratio: <?php echo $config->get('video_aspect', '0.56'); ?>
      , engine: '<?php echo ($config->get('fallback', '3') == '3' ? 'flash' : 'html5'); ?>'
      , debug: <?php echo ($config->get('debug') == '1' ? 'true' : 'false'); ?>
      , key: '<?php echo $config->get('licensekey'); ?>'
      , logo: '<?php echo JURI::root() . $config->get('logofile'); ?>'
   });
  });
})(jQuery);
</script>         
                <?php
                $html = ob_get_contents();
                ob_end_clean();
                
                return $html;            
        }              
}
