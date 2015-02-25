<?php
/**
 * @package     Joomla.site
 * @subpackage  Plugin.hwdmediashare.player_jwadvanced
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class plgHwdmediasharePlayer_JwAdvanced extends JObject
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
	 * Returns the plgHwdmediasharePlayer_JwAdvanced object, only creating it if it
	 * doesn't already exist.
	 *
	 * @access  public
	 * @return  object  The plgHwdmediasharePlayer_JwAdvanced object.
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
	 * Method to check the plg_jwadvanced plugin is installed.
         * 
         * @access  public
         * @return  boolean  True if installed, false otherwise.
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
	 * Method to preload any assets for this player.
         * 
         * @access  public
	 * @param   JRegistry  $params  The plyaer plugin parameters.
         * @return  void
	 **/
	public function preloadAssets($params)
	{
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
		$plugin = JPluginHelper::getPlugin('hwdmediashare', 'player_jwadvanced');
		
                // Load the language file.
                $lang = JFactory::getLanguage();
                $lang->load('plg_hwdmediashare_player_jwadvanced', JPATH_ADMINISTRATOR, $lang->getTag());

                if (!$this->checkInstalled())
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_PLAYER_JWADVANCED_ERROR_NOT_INSTALLED'));
                        return false;
                }
                
                if (!$plugin)
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_PLAYER_JWADVANCED_ERROR_NOT_PUBLISHED'));
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

                if (isset($sources->get('mp4')->url))
                {
                        $file = $sources->get('mp4')->url;
                }
                elseif (isset($sources->get('flv')->url)) 
                {
                        $file = $sources->get('flv')->url;
                }
                else
                {
                        $file = false;
                }

                if (!$file)
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_PLAYER_JWADVANCED_ERROR_NOT_PLAYABLE'));
                        return false;
                }
                
                ob_start();
                ?>
<div class="media-respond" style="max-width:<?php echo $config->get('mediaitem_size', '500'); ?>px;">
  <div id="media-aspect-<?php echo $item->id; ?>" class="media-aspect" data-aspect="<?php echo $config->get('video_aspect', '0.75'); ?>"></div>
  <?php echo JHtml::_('content.prepare', '{jwplayer}&file=' . $file . '&width=100%&height=100%&autostart=' . ($config->get('media_autoplay') ? 'true' : 'false') . '&provider=video&image=' . $poster . '&class=media-content{/jwplayer}'); ?>
</div>
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
		$plugin = JPluginHelper::getPlugin('hwdmediashare', 'player_jwadvanced');
		
                // Load the language file.
                $lang = JFactory::getLanguage();
                $lang->load('plg_hwdmediashare_player_jwadvanced', JPATH_ADMINISTRATOR, $lang->getTag());

                if (!$this->checkInstalled())
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_PLAYER_JWADVANCED_ERROR_NOT_INSTALLED'));
                        return false;
                }
                
                if (!$plugin)
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_PLAYER_JWADVANCED_ERROR_NOT_PUBLISHED'));
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

                if (isset($sources->get('mp3')->url))
                {
                        $file = $sources->get('mp3')->url;
                }
                else
                {
                        $file = false;
                }

                if (!$file)
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_PLAYER_JWADVANCED_ERROR_NOT_PLAYABLE'));
                        return false;
                }
                
                ob_start();
                ?>
<div class="media-respond" style="max-width:<?php echo $config->get('mediaitem_size', '500'); ?>px;">
  <div id="media-aspect-<?php echo $item->id; ?>" class="media-aspect" data-aspect="<?php echo $config->get('video_aspect', '0.75'); ?>"></div>
  <?php echo JHtml::_('content.prepare', '{jwplayer}&file=' . $file . '&width=100%&height=100%&autostart=' . ($config->get('media_autoplay') ? 'true' : 'false') . '&provider=audio&image=' . $poster . '&class=media-content{/jwplayer}'); ?>
</div>
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
                $doc = JFactory::getDocument();

                // Load plugin.
		$plugin = JPluginHelper::getPlugin('hwdmediashare', 'player_jwadvanced');
		
                // Load the language file.
                $lang = JFactory::getLanguage();
                $lang->load('plg_hwdmediashare_player_jwadvanced', JPATH_ADMINISTRATOR, $lang->getTag());

                if (!$this->checkInstalled())
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_PLAYER_JWADVANCED_ERROR_NOT_INSTALLED'));
                        return false;
                }
                
                if (!$plugin)
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_PLAYER_JWADVANCED_ERROR_NOT_PUBLISHED'));
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

                foreach($sources as $i => $source)
                {
                        if ($source->type == 2)
                        {
                                $rtmp = hwdMediaShareStreaming::parseRtmpStream($source->url);
                                $stream = $rtmp->stream;
                                $application = $rtmp->application;
                        }
                }

                if (!$file)
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_PLAYER_JWADVANCED_ERROR_NOT_PLAYABLE'));
                        return false;
                }
                
                ob_start();
                ?>
<div class="media-respond" style="max-width:<?php echo $config->get('mediaitem_size', '500'); ?>px;">
  <div id="media-aspect-<?php echo $item->id; ?>" class="media-aspect" data-aspect="<?php echo $config->get('video_aspect', '0.75'); ?>"></div>
  <?php echo JHtml::_('content.prepare', '{jwplayer}&file=' . $stream . '&streamer=' . $application . '&width=100%&height=100%&autostart=' . ($config->get('media_autoplay') ? 'true' : 'false') . '&provider=rtmp&image=' . $poster . '&class=media-content{/jwplayer}'); ?>
</div>
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

                if (!$this->checkInstalled())
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_PLAYER_JWADVANCED_ERROR_NOT_INSTALLED'));
                        return false;
                }
                
                if (!$plugin)
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_PLAYER_JWPLAYER_ERROR_NOT_PUBLISHED'));
                        return false;
                }

                // Load parameters.
                $params = new JRegistry($plugin->params);
                 
                // Preload assets.
                $this->preloadAssets($params);

                if (isset($id))
                {
                        $file = 'http://www.youtube.com/watch?v=' . $id;
                }
                else
                {
                        $file = false;
                }

                if (!$file)
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_PLAYER_JWADVANCED_ERROR_NOT_PLAYABLE'));
                        return false;
                }
                
                ob_start();
                ?>
<div class="media-respond" style="max-width:<?php echo $config->get('mediaitem_size', '500'); ?>px;">
  <div id="media-aspect-<?php echo $item->id; ?>" class="media-aspect" data-aspect="<?php echo $config->get('video_aspect', '0.75'); ?>"></div>
  <?php echo JHtml::_('content.prepare', '{jwplayer}&file=' . $file . '&width=100%&height=100%&autostart=' . ($config->get('media_autoplay') ? 'true' : 'false') . '&provider=youtube&image=' . $poster . '&class=media-content{/jwplayer}'); ?>
</div>
                <?php
                $html = ob_get_contents();
                ob_end_clean();
                
                return $html;     
        }     
}