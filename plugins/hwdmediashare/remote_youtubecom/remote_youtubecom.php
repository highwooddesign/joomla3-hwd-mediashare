<?php
/**
 * @package     Joomla.site
 * @subpackage  Plugin.hwdmediashare.comments_disqus
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class plgHwdmediashareRemote_youtubecom extends JObject
{    
	/**
	 * The remote media type integer: http://hwdmediashare.co.uk/learn/api/68-api-definitions
         * 
         * @access      public
	 * @var         integer
	 */
	public $mediaType = 4;
        
	/**
	 * Library data
	 * @var strings
	 */
        var $_url;
        var $_host;
        var $_buffer;
        var $_title;
        var $_description;
        var $_source;
        var $_duration;
        var $_thumbnail;
        
	/**
	 * Class constructor.
	 *
	 * @access	public
         * @return      void
	 */
	public function __construct()
	{
		parent::__construct();
	}
        
	/**
	 * Returns the plgHwdmediashareRemote_youtubecom object, only creating it if it
	 * doesn't already exist.
	 *
	 * @access	public
	 * @return      object      The plgHwdmediashareRemote_youtubecom object.
	 */
	public static function getInstance()
	{
		static $instance;

		if (!isset ($instance))
                {
			$c = 'plgHwdmediashareRemote_youtubecom';
                        $instance = new $c;
		}

		return $instance;
	}
    
        /**
	 * Get the title of the media.
	 *
	 * @access	public
         * @return      void
	 */
	public function getTitle()
	{
                if( !$this->_title )
		{
                        hwdMediaShareFactory::load('remote');
                        $this->getBuffer();
                        $this->_title = hwdMediaShareRemote::getTitle($this->_buffer);
                        $this->_title = str_replace(" - YouTube", "", $this->_title);
                }
                return $this->_title;            
        }   

        /**
	 * Get the description of the media.
	 *
	 * @access	public
         * @return      void
	 */
	public function getDescription()
	{
                if( !$this->_description )
		{
                        hwdMediaShareFactory::load('remote');
                        $this->getBuffer();
                        $this->_description = hwdMediaShareRemote::getDescription($this->_buffer); 
                }             
                return $this->_description;            
        }  
        
        /**
	 * Get the source of the media.
	 *
	 * @access	public
         * @return      void
	 */
	public function getSource()
	{
                if( !$this->_source )
		{
                        hwdMediaShareFactory::load('remote');
                        $this->getBuffer();
                        $this->_source = hwdMediaShareRemote::getSource();
                }             
                return $this->_source;             
        } 

        /**
	 * Get the duration of the media.
	 *
	 * @access	public
         * @return      void
	 */
	public function getDuration()
	{
                if( !$this->_duration )
		{
                        $duration = '';
                        
                        //preg_match("/amp;length_seconds=(.*)\\\\u/siU", $this->_buffer, $match);
                        //preg_match("/length_seconds=(.*)\\\\u/siU", $this->_buffer, $match);
                        preg_match("/\"length_seconds\": (.*),/siU", $this->_buffer, $match);
                        
                        if (!empty($match[1]))
                        {
                                $duration = (int) $match[1];
                        }
   
                        $duration == 0 ? $this->_duration = null : $this->_duration = $duration;
                }   

                return $this->_duration;
        } 

        /**
	 * Get the thumbnail location of the media.
	 *
	 * @access	public
         * @return      void
	 */
	public function getThumbnail()
	{
                if( !$this->_thumbnail )
		{
                        $this->_thumbnail = plgHwdmediashareRemote_youtubecom::parse($this->_url, 'hqthumb');
                }             
                return $this->_thumbnail;              
        } 
        
        /**
	 * Request the source, and set to buffer.
	 *
	 * @access	public
         * @return      void
	 */
	public function getBuffer()
	{
                $this->getHost();
                $this->getUrl();

                if (!$this->_buffer)
                {
                        hwdMediaShareFactory::load('remote');
                        $this->_buffer = hwdMediaShareRemote::getBuffer($this->_url);
                }

		return $this->_buffer;
	}

        /**
	 * Get the host of the media source.
	 *
	 * @access	public
         * @return      void
	 */
	public function getHost()
	{
                if (!$this->_host)
                {
                        hwdMediaShareFactory::load('remote');
                        $this->_host = hwdMediaShareRemote::getHost();
                }

		return $this->_host;
	}
        
        /**
	 * Get the url of the media source.
	 *
	 * @access	public
         * @return      void
	 */
	public function getUrl()
	{
                if (!$this->_url)
                {
                        hwdMediaShareFactory::load('remote');
                        $this->_url = hwdMediaShareRemote::getUrl();
                }

		return $this->_url;
	}
        
        /**
	 * Render the HTML to display the media.
	 *
	 * @access	public
	 * @param       object      $item       The media item being displayed.
         * @return      void
	 */
	public function display($item)
	{
		// Initialise variables.
                $app = JFactory::getApplication();

                // Load plugin.
		$plugin = JPluginHelper::getPlugin('hwdmediashare', 'remote_youtubecom');
		
                // Load the language file.
                $lang = JFactory::getLanguage();
                $lang->load('plg_hwdmediashare_remote_youtubecom', JPATH_SITE . '/administrator');

                if (!$plugin)
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_REMOTE_YOUTUBECOM_ERROR_NOT_PUBLISHED'));
                        return false;
                }

                // Load parameters.
                $params = new JRegistry($plugin->params);

                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // Load HWD utilities.
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                
                // Get Youtube ID
                $id = plgHwdmediashareRemote_youtubecom::parse($item->source, '');

                // Pull parameters from the original Youtube url and transfer these to the iframe tag where appropriate
                $url = parse_url($item->source);
                parse_str($url['query'], $ytvars);
                if (isset($ytvars['cc_load_policy'])) $params->set('cc_load_policy', $ytvars['cc_load_policy']);
                if (isset($ytvars['cc_lang_pref'])) $params->set('cc_lang_pref', $ytvars['cc_lang_pref']);
                if (isset($ytvars['hl'])) $params->set('hl', $ytvars['hl']);

                if ($params->get('play_local') == 1)
                {
                        $pluginClass = 'plgHwdmediashare'.$config->get('media_player');
                        $pluginPath = JPATH_ROOT.'/plugins/hwdmediashare/'.$config->get('media_player').'/'.$config->get('media_player').'.php';
                        if (file_exists($pluginPath))
                        {
                                JLoader::register($pluginClass, $pluginPath);
                                $player = call_user_func(array($pluginClass, 'getInstance'));
                                if (method_exists($player, 'getYoutubePlayer'))
                                {
                                        $params = new JRegistry('{"id":"'.$id.'"}');
                                        return $player->getYoutubePlayer($params);
                                }
                        }
                }

                $this->autoplay = $app->input->get('media_autoplay', $config->get('media_autoplay'), 'integer') == 1 ? '1' : '0';
                $this->width = '100%';
                $this->height = '100%';
                ob_start();
                ?>
                <div class="media-respond" style="max-width:<?php echo $config->get('mediaitem_size', '500'); ?>px;">
                  <div class="media-aspect" data-aspect="<?php echo $config->get('video_aspect', '0.75'); ?>"></div>
                  <div class="media-content">
                    <iframe width="<?php echo $this->width; ?>" height="<?php echo $this->height; ?>" src="<?php echo JURI::getInstance()->getScheme(); ?>://www.youtube.com/embed/<?php echo $id; ?>?wmode=opaque&amp;autoplay=<?php echo $this->autoplay; ?>&amp;autohide=<?php echo $params->get('autohide',2); ?>&amp;border=<?php echo $params->get('border',0); ?>&amp;cc_load_policy=<?php echo $params->get('cc_load_policy',1); ?>&amp;cc_lang_pref=<?php echo $params->get('cc_lang_pref','en'); ?>&amp;hl=<?php echo $params->get('hl','en'); ?>&amp;color=<?php echo $params->get('color','red'); ?>&amp;color1=<?php echo $params->get('color1'); ?>&amp;color2=<?php echo $params->get('color2'); ?>&amp;controls=<?php echo $params->get('controls',1); ?>&amp;fs=<?php echo $params->get('fs',1); ?>&amp;hd=<?php echo $params->get('hd',0); ?>&amp;iv_load_policy=<?php echo $params->get('iv_load_policy',1); ?>&amp;modestbranding=<?php echo $params->get('modestbranding',1); ?>&amp;rel=<?php echo $params->get('rel',1); ?>&amp;theme=<?php echo $params->get('theme','dark'); ?>" frameborder="0" allowfullscreen></iframe>	
                  </div>
                </div>
                <?php
                $html = ob_get_contents();
                ob_end_clean();
                
                return $html;
	}
        
        /**
	 * Parse the source to extract the media.
	 *
	 * @access	public
	 * @param       string      $url        The media url.
	 * @param       string      $return     The format to return.
	 * @param       integer     $width      The width.
	 * @param       integer     $height     The height.
	 * @param       integer     $rel        The option to display relative videos after the video finishes playing.
         * @return      void
	 */      
        protected function parse($url, $return='embed', $width='', $height='', $rel=0)
        {
                $urls = parse_url($url);

                // Url is http://youtu.be/xxxx
                if($urls['host'] == 'youtu.be'){
                    $id = ltrim($urls['path'],'/');
                }
                // Url is http://www.youtube.com/embed/xxxx
                else if(strpos($urls['path'],'embed') == 1){
                    $id = end(explode('/',$urls['path']));
                }
                // Url is xxxx only
                else if(strpos($url,'/')===false){
                    $id = $url;
                }
                // http://www.youtube.com/watch?feature=player_embedded&v=m-t4pcO99gI
                // Url is http://www.youtube.com/watch?v=xxxx
                else
                {
                    parse_str($urls['query'], $arrayVars);
                    $id = $arrayVars['v'];    
                }
                
                // Return embed iframe.
                if($return == 'embed')
                {
                    return '<iframe width="'.($width?$width:560).'" height="'.($height?$height:349).'" src="http://www.youtube.com/embed/'.$id.'?rel='.$rel.'" frameborder="0" allowfullscreen></iframe>';
                }
                // Return normal thumb.
                else if($return == 'thumb')
                {
                    return 'http://i1.ytimg.com/vi/'.$id.'/default.jpg';
                }
                // Return hqthumb.
                else if($return == 'hqthumb')
                {
                    return 'http://i1.ytimg.com/vi/'.$id.'/hqdefault.jpg';
                }
                // Return id.
                else
                {
                    return $id;
                }
        }
        
        /**
	 * Method to construct a video link for use with SWF, as an open graph video tag.
	 *
	 * @access	public
	 * @param       object      $item       The media item being displayed.
         * @return      void
	 */
	public function getOgVideoTag($item)
	{
                $id = plgHwdmediashareRemote_youtubecom::parse($item->source, '');
                return 'http://www.youtube.com/v/'.$id.'?version=3&amp;autohide=1';
        } 
}