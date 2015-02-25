<?php
/**
 * @package     Joomla.site
 * @subpackage  Plugin.hwdmediashare.player_jwplayer
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class plgHwdmediasharePlayer_JwPlayer extends JObject
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
	 * Returns the plgHwdmediasharePlayer_JwPlayer object, only creating it if it
	 * doesn't already exist.
	 *
	 * @access  public
	 * @return  object  The plgHwdmediasharePlayer_JwPlayer object.
	 */
	public static function getInstance()
	{
		static $instance;

		if (!isset ($instance))
                {
			$c = 'plgHwdmediasharePlayer_JwPlayer';
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

                // Add page assets.
                JHtml::_('bootstrap.framework');
                if ($params->get('hosting') == 'cloud')
                {
                        // Check if the user has entered the full script tag.
                        if (preg_match('/< *script[^>]*src *= *["\']?([^"\']*)/i', $params->get('cloud_host_key'), $matches))
                        {
                                list ($match, $src) = $matches;
                                $doc->addScript($src);                        
                        }
                        else
                        {
                                // Check if the user has entered the full URL.
                                if (filter_var($params->get('cloud_host_key'), FILTER_VALIDATE_URL))
                                { 
                                        $doc->addScript($params->get('cloud_host_key'));
                                }
                                else
                                {
                                        $doc->addScript('http://jwpsrv.com/library/' . $params->get('cloud_host_key', '1DQjeAHxEeSEyiIACyaB8g') . '.js');
                                }    
                        }
                }
                else
                {
                        $doc->addScript(JURI::root( true ) . '/plugins/hwdmediashare/player_jwplayer/assets/jwplayer/jwplayer.js');
                        $doc->addScriptDeclaration('jwplayer.key="' . $params->get('self_host_key', 'bAwF3Hdn7fiSh7YpWL1YopRWDNTTd1bMHyE9Sg==') . '";'); 
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

                // Get HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // Load plugin.
		$plugin = JPluginHelper::getPlugin('hwdmediashare', 'player_jwplayer');
		
                // Load the language file.
                $lang = JFactory::getLanguage();
                $lang->load('plg_hwdmediashare_player_jwplayer', JPATH_ADMINISTRATOR, $lang->getTag());

                if (!$plugin)
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_PLAYER_JWPLAYER_ERROR_NOT_PUBLISHED'));
                        return false;
                }

                // Load parameters.
                $params = new JRegistry($plugin->params);
                 
                // Preload assets.
                $this->preloadAssets($params);
                               
                ob_start();
                ?>
<div class="media-respond" style="max-width:<?php echo $config->get('mediaitem_size', '500'); ?>px;">
  <div class="media-aspect" data-aspect="<?php echo $config->get('video_aspect', '0.75'); ?>"></div>
  <div class="media-content">
    <div id="media-jwplayer-<?php echo $item->id; ?>"><?php echo JText::_('PLG_HWDMEDIASHARE_PLAYER_JWPLAYER_LOADING_PLAYER'); ?></div>
  </div>
</div>
<script type="text/javascript">
    jwplayer("media-jwplayer-<?php echo $item->id; ?>").setup({
        // Sources.
        sources: [
            { 'file': 'dummy', type: 'none' } // Dummy source to prevent trailing commas.
            <?php echo ($sources->get('mp4') ?  ",{ 'file': '".$sources->get('mp4')->url."', type: 'mp4', label: 'HD MP4' }    // H.264 version\n" : ''); ?>
            <?php echo ($sources->get('webm') ? ",{ 'file': '".$sources->get('webm')->url."', type: 'webm', label: 'HD WEBM' } // WebM version\n" : ''); ?>
            <?php echo ($sources->get('ogg') ?  ",{ 'file': '".$sources->get('ogg')->url."', type: 'vorbis', label: 'HD OGG' } // Ogg Theora version\n" : ''); ?>
            <?php echo ($sources->get('flv') ?  ",{ 'file': '".$sources->get('flv')->url."', type: 'flv', label: 'HD FLV' }    // Flash version\n" : ''); ?>
        ],
        // Basic options.
        /** aspectratio: "16:9", // We don't set this because code from HWD manages the responsiveness of the player. */
        autostart: <?php echo $config->get('media_autoplay') ? 'true' : 'false'; ?>,
        controls: true,
        height: "100%",
        image: "<?php echo hwdMediaShareThumbnails::getVideoPreview($item); ?>",
        mute: false,
        primary: "<?php echo $config->get('fallback') == 3 ? 'flash' : 'html5'; ?>",
        repeat: false,
        skin: "<?php echo $params->get('hosting') == 'cloud' ? '' : JURI::root( true ) . '/plugins/hwdmediashare/player_jwplayer/assets/jwplayer-skins/' . $params->get('skin', 'six') . '.xml'; ?>",
        width: "100%",
        androidhls: false,
        stretching: "<?php echo $params->get('stretching', 'uniform'); ?>",
        // Logo block.
        logo: {
            file: '<?php echo $params->get('logofile'); ?>',
            link: '<?php echo $params->get('logolink'); ?>',
            hide: '<?php echo $params->get('logohide'); ?>',
            margin: '<?php echo $params->get('logomargin'); ?>',
            position: '<?php echo $params->get('logoposition'); ?>'
        },
        <?php if ($params->get('videoadsclient') != '') : ?>
        advertising: {
          client: '<?php echo $params->get('videoadsclient', 'vast'); ?>',
          tag: '<?php echo $params->get('videoadstag', ''); ?>'
        },
        <?php endif; ?>                
    });
</script>
                <?php
                $html = ob_get_contents();
                ob_end_clean();
                
                return $html;            
        }   

        /**
	 * Method to render the player for an audio
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

                // Get HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // Load plugin.
		$plugin = JPluginHelper::getPlugin('hwdmediashare', 'player_jwplayer');
		
                // Load the language file.
                $lang = JFactory::getLanguage();
                $lang->load('plg_hwdmediashare_player_jwplayer', JPATH_ADMINISTRATOR, $lang->getTag());

                if (!$plugin)
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_PLAYER_JWPLAYER_ERROR_NOT_PUBLISHED'));
                        return false;
                }

                // Load parameters.
                $params = new JRegistry($plugin->params);
                   
                // Preload assets.
                $this->preloadAssets($params);
                        
                switch ($params->get('skin'))
                {
                        case 'six':
                        case 'beelden':
                        case 'bekle':
                        case 'five':
                        case 'roundster':
                                $height = 30;
                        break;
                        case 'glow':
                                $height = 27;
                        break;
                        case 'stormtrooper':
                                $height = 25;
                        break;
                        case 'vapor':
                                $height = 35;
                        break;                
                        default:
                                $height = 30;
                }
                ob_start();
                ?>
<div class="media-respond" style="max-width:<?php echo $config->get('mediaitem_size', '500'); ?>px;">
  <div id="media-jwplayer-<?php echo $item->id; ?>"><?php echo JText::_('PLG_HWDMEDIASHARE_PLAYER_JWPLAYER_LOADING_PLAYER'); ?></div>
</div>
<script type="text/javascript">
    jwplayer("media-jwplayer-<?php echo $item->id; ?>").setup({
        // Sources.
        sources: [
            { 'file': 'dummy', type: 'none' } // Dummy source to prevent trailing commas.
            <?php echo ($sources->get('mp3') ? ",{ 'file': '".$sources->get('mp3')->url."', type: 'mp3', label: 'MP3' } // MP3 version\n" : ''); ?>
            <?php echo ($sources->get('ogg') ? ",{ 'file': '".$sources->get('ogg')->url."', type: 'ogg', label: 'OGG' } // OGG version\n" : ''); ?>
        ],
        // Basic options.
        /** aspectratio: "16:9", // We don't set this because code from HWD manages the responsiveness of the player. */
        autostart: <?php echo $config->get('media_autoplay') ? 'true' : 'false'; ?>,
        controls: true,
        height: "<?php echo $height; ?>",
        image: "",
        mute: false,
        primary: "<?php echo $config->get('fallback') == 3 ? 'flash' : 'html5'; ?>",
        repeat: false,
        skin: "<?php echo JURI::root( true ); ?>/plugins/hwdmediashare/player_jwplayer/assets/jwplayer-skins/<?php echo $params->get('skin', 'six'); ?>.xml",
        width: "100%",
        androidhls: false,
        stretching: "<?php echo $params->get('stretching', 'uniform'); ?>",
        // Logo block.
        logo: {
            file: '<?php echo $params->get('logofile'); ?>',
            link: '<?php echo $params->get('logolink'); ?>',
            hide: '<?php echo $params->get('logohide'); ?>',
            margin: '<?php echo $params->get('logomargin'); ?>',
            position: '<?php echo $params->get('logoposition'); ?>'
        },
    });
</script>
                <?php
                $html = ob_get_contents();
                ob_end_clean();
                
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

                // Get HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // Load plugin.
		$plugin = JPluginHelper::getPlugin('hwdmediashare', 'player_jwplayer');
		
                // Load the language file.
                $lang = JFactory::getLanguage();
                $lang->load('plg_hwdmediashare_player_jwplayer', JPATH_ADMINISTRATOR, $lang->getTag());

                if (!$plugin)
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_PLAYER_JWPLAYER_ERROR_NOT_PUBLISHED'));
                        return false;
                }

                // Load parameters.
                $params = new JRegistry($plugin->params);
                  
                // Preload assets.
                $this->preloadAssets($params);
                               
                ob_start();
                ?>
<div class="media-respond" style="max-width:<?php echo $config->get('mediaitem_size', '500'); ?>px;">
  <div class="media-aspect" data-aspect="<?php echo $config->get('video_aspect', '0.75'); ?>"></div>
  <div class="media-content">
    <div id="media-jwplayer-<?php echo $item->id; ?>"><?php echo JText::_('PLG_HWDMEDIASHARE_PLAYER_JWPLAYER_LOADING_PLAYER'); ?></div>
  </div>
</div>
<script type="text/javascript">
    jwplayer("media-jwplayer-<?php echo $item->id; ?>").setup({
        // Sources.
        playlist: [{
        sources: [
            { 'file': 'dummy', type: 'none' } // Dummy source to prevent trailing commas.
            <?php foreach($sources as $i => $source): ?>
                <?php if ($source->type == 1): ?>
                    ,{ 'file': '<?php echo $source->url; ?>' }
                <?php elseif ($source->type == 2): ?>
                    ,{ 'file': '<?php echo $source->url; ?>' }
                <?php elseif ($source->type == 3): ?>
                    ,{ 'file': '<?php echo $source->url; ?>', type: 'mp4', label: '<?php echo $source->quality; ?>p' }
                <?php endif; ?>
            <?php endforeach; ?>
        ]
        }],
        // Basic options.
        /** aspectratio: "16:9", // We don't set this because code from HWD manages the responsiveness of the player. */
        autostart: <?php echo $config->get('media_autoplay') ? 'true' : 'false'; ?>,
        controls: true,
        height: "100%",
        image: "<?php echo hwdMediaShareThumbnails::getVideoPreview($item); ?>",
        mute: false,
        primary: "flash",
        repeat: false,
        skin: "<?php echo JURI::root(); ?>plugins/hwdmediashare/player_jwplayer/assets/jwplayer-skins/<?php echo $params->get('skin', 'six'); ?>.xml",
        width: "100%",
        androidhls: true,
        stretching: "<?php echo $params->get('stretching', 'uniform'); ?>",
        // Logo block.
        logo: {
            file: '<?php echo $params->get('logofile'); ?>',
            link: '<?php echo $params->get('logolink'); ?>',
            hide: '<?php echo $params->get('logohide'); ?>',
            margin: '<?php echo $params->get('logomargin'); ?>',
            position: '<?php echo $params->get('logoposition'); ?>'
        },
        <?php if ($params->get('videoadsclient') != '') : ?>
        advertising: {
          client: '<?php echo $params->get('videoadsclient', 'vast'); ?>',
          tag: '<?php echo $params->get('videoadstag', ''); ?>'
        },
        <?php endif; ?>   
    });
</script>
                <?php
                $html = ob_get_contents();
                ob_end_clean();
                
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

                // Get HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // Load plugin.
		$plugin = JPluginHelper::getPlugin('hwdmediashare', 'player_jwplayer');
		
                // Load the language file.
                $lang = JFactory::getLanguage();
                $lang->load('plg_hwdmediashare_player_jwplayer', JPATH_ADMINISTRATOR, $lang->getTag());

                if (!$plugin)
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_PLAYER_JWPLAYER_ERROR_NOT_PUBLISHED'));
                        return false;
                }

                // Load parameters.
                $params = new JRegistry($plugin->params);
                 
                // Preload assets.
                $this->preloadAssets($params);
                               
                ob_start();
                ?>
<div class="media-respond" style="max-width:<?php echo $config->get('mediaitem_size', '500'); ?>px;">
  <div class="media-aspect" data-aspect="<?php echo $config->get('video_aspect', '0.75'); ?>"></div>
  <div class="media-content">
    <div id="media-jwplayer-<?php echo $item->id; ?>"><?php echo JText::_('PLG_HWDMEDIASHARE_PLAYER_JWPLAYER_LOADING_PLAYER'); ?></div>
  </div>
</div>
<script type="text/javascript">
    jwplayer("media-jwplayer-<?php echo $item->id; ?>").setup({
        // Basic options.
        /** aspectratio: "16:9", // We don't set this because code from HWD manages the responsiveness of the player. */
        autostart: <?php echo $config->get('media_autoplay') ? 'true' : 'false'; ?>,
        controls: true,
        file: "http://www.youtube.com/watch?v=<?php echo $id; ?>",
        height: "100%",
        image: "<?php echo hwdMediaShareThumbnails::getVideoPreview($item); ?>",
        mute: false,
        primary: "<?php echo $config->get('fallback') == 3 ? 'flash' : 'html5'; ?>",
        repeat: false,
        skin: "<?php echo JURI::root( true ); ?>/plugins/hwdmediashare/player_jwplayer/assets/jwplayer-skins/<?php echo $params->get('skin', 'six'); ?>.xml",
        width: "100%",
        androidhls: false,
        stretching: "<?php echo $params->get('stretching', 'uniform'); ?>",
        // Logo block.
        logo: {
            file: '<?php echo $params->get('logofile'); ?>',
            link: '<?php echo $params->get('logolink'); ?>',
            hide: '<?php echo $params->get('logohide'); ?>',
            margin: '<?php echo $params->get('logomargin'); ?>',
            position: '<?php echo $params->get('logoposition'); ?>'
        },
        <?php if ($params->get('videoadsclient') != '') : ?>
        advertising: {
          client: '<?php echo $params->get('videoadsclient', 'vast'); ?>',
          tag: '<?php echo $params->get('videoadstag', ''); ?>'
        },
        <?php endif; ?>   
    });
</script>
                <?php
                $html = ob_get_contents();
                ob_end_clean();
                
                return $html; 
        }      
}