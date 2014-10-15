<?php
/**
 * @package     Joomla.site
 * @subpackage  Plugin.hwdmediashare.remote_youtubecom
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

// Load the HWD remote library.
JLoader::register('hwdMediaShareRemote', JPATH_ROOT.'/components/com_hwdmediashare/libraries/remote.php');

class plgHwdmediashareRemote_youtubecom extends hwdMediaShareRemote
{    
	/**
	 * The remote media type integer: http://hwdmediashare.co.uk/learn/api/68-api-definitions
         * 
         * @access  public
	 * @var     integer
	 */
	public $mediaType = 4;

	/**
	 * The API buffer (holding the snippet part).
         * 
         * @access  public
	 * @var     string
	 */
        public $_v3snippet = false;
        
	/**
	 * The API buffer (holding the contentDetails part).
         * 
         * @access  public
	 * @var     string
	 */
        public $_v3content = false;
        
        public $_url;
        public $_host;
        public $_buffer;
        public $_title;
        public $_description;
        public $_tags;
        public $_source;
        public $_duration;
        public $_thumbnail;
        
	/**
	 * Class constructor.
	 *
	 * @access  public
	 * @param   mixed  $properties  Either and associative array or another
	 *                              object to set the initial properties of the object.
         * @return  void
	 */
	public function __construct($properties = null)
	{
                /**
                 * We extend the Joomla Platform Object Class for this plugin instead of JPlugin. This class
                 * allows for simple but smart objects with get and set methods and an internal error handler.
                 * The 'hwdmediashare' plugin group is loaded on some media events, such as onAfterMediaAdd.
                 * When loaded by Joomla, it is exepected the plugin classes will extend the JPlugin class, 
                 * and the __construct() method is passed a $subject and $config variable:
                 *                  
                 *     parent::__construct($subject, $config);
                 * 
                 * However, the JObject __construct() method expects a single $properties variable, and when loaded
                 * by JEventDispatcher, a fatal error is thrown.
                 * 
                 *     Fatal error: Cannot access property started with '\0' in C:\wamp\www\joomla3-hwdmediashare\libraries\joomla\object\object.php on line 194
                 * 
                 * We avoid the error by overloading the parent constructs (which are not necessary for these
                 * plugin types).
                 */  
	}
        
	/**
	 * Returns the plgHwdmediashareRemote_youtubecom object, only creating it if it
	 * doesn't already exist.
	 *
	 * @access  public
	 * @return  object  The plgHwdmediashareRemote_youtubecom object.
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
	 * @access  public
         * @param   string  $buffer  The buffer of the remote source.
         * @return  string  The title.
	 */
	public function getTitle($buffer = null)
	{
                if(!$this->_title)
		{            
                        // Initialise variables.
                        $app = JFactory::getApplication();

                        // Request the required API buffer.
                        if (!$this->_v3snippet) $this->_v3snippet = parent::getBuffer('https://www.googleapis.com/youtube/v3/videos?part=snippet&id=' . $this->parse($this->_url, '') . '&key=AIzaSyB2oL3uUZWDuMLiiSXc_El9Mcgg4nAaNFU', true);

                        // Check request was successful.
                        if ($this->_v3snippet)
                        {
                                $json = json_decode($this->_v3snippet);
                                if (!isset($json->error) && isset($json->items[0]->snippet->title))
                                {
                                        if ($this->_title = parent::clean($json->items[0]->snippet->title, 255))
                                        {
                                                return $this->_title; 
                                        }
                                }
                                else
                                {
                                        foreach ($json->error->errors as $error)
                                        {
                                                $app->enqueueMessage(JText::sprintf('COM_HWDMS_WARNING_YOUTUBE_API_REQUEST_FAILED_REASON_N', $error->reason));
                                        }
                                }
                        }
                }
                
                if(!$this->_title)
		{
                        $this->_title = parent::getTitle($this->_buffer);
                        $this->_title = str_replace(" - YouTube", "", $this->_title);
                }

                return $this->_title;               
        }   

        /**
	 * Get the description of the media.
	 *
	 * @access  public
         * @param   string  $buffer  The buffer of the remote source.
         * @return  string  The description.
	 */
	public function getDescription($buffer = null)
	{
                if(!$this->_description)
		{            
                        // Request the required API buffer.
                        if (!$this->_v3snippet) $this->_v3snippet = parent::getBuffer('https://www.googleapis.com/youtube/v3/videos?part=snippet&id=' . $this->parse($this->_url, '') . '&key=AIzaSyB2oL3uUZWDuMLiiSXc_El9Mcgg4nAaNFU', true);

                        // Check request was successful.                        
                        if ($this->_v3snippet)
                        {
                                $json = json_decode($this->_v3snippet);
                                if (!isset($json->error) && isset($json->items[0]->snippet->description))
                                {
                                        if ($this->_description = parent::clean($json->items[0]->snippet->description))
                                        {
                                                return $this->_description; 
                                        }
                                }
                        }
                }
                
                if(!$this->_description)
		{
                        $this->_description = parent::getDescription($this->_buffer);    
                }
                
                return $this->_description;         
        }  

        /**
	 * Get the duration of the media.
	 *
	 * @access  public
         * @param   string  $buffer  The buffer of the remote source.
         * @return  string  The duration.
	 */
	public function getDuration($buffer = null)
	{
                if(!$this->_duration)
		{            
                        // Request the required API buffer.
                        if (!$this->_v3content) $this->_v3content = parent::getBuffer('https://www.googleapis.com/youtube/v3/videos?part=contentDetails&id=' . $this->parse($this->_url, '') . '&key=AIzaSyB2oL3uUZWDuMLiiSXc_El9Mcgg4nAaNFU', true);

                        // Check request was successful.                                                
                        if ($this->_v3content)
                        {
                                $json = json_decode($this->_v3content);
                                if (!isset($json->error) && isset($json->items[0]->contentDetails->duration))
                                {
                                        // Get duration from ISO 8601 string.
                                        preg_match('/(\d+)H/', $json->items[0]->contentDetails->duration, $match);
                                        $h = isset($match[0]) ? filter_var($match[0], FILTER_SANITIZE_NUMBER_INT) : 0;
                                        preg_match('/(\d+)M/', $json->items[0]->contentDetails->duration, $match);
                                        $m = isset($match[0]) ? filter_var($match[0], FILTER_SANITIZE_NUMBER_INT) : 0;
                                        preg_match('/(\d+)S/', $json->items[0]->contentDetails->duration, $match);
                                        $s = isset($match[0]) ? filter_var($match[0], FILTER_SANITIZE_NUMBER_INT) : 0;

                                        $duration = ($h * 60 * 60) + ($m * 60) + ($s);
                                        if ($duration > 0) 
                                        {
                                                $this->_duration = $duration;
                                        }
                                }
                        }
                }

                if(!$this->_duration)
		{
                        //preg_match("/amp;length_seconds=(.*)\\\\u/siU", $this->_buffer, $match);
                        //preg_match("/length_seconds=(.*)\\\\u/siU", $this->_buffer, $match);
                        preg_match("/\"length_seconds\": (.*),/siU", $this->_buffer, $match);
                        $this->_duration = isset($match[1]) ? filter_var($match[1], FILTER_SANITIZE_NUMBER_INT) : 0;  
                }
                
                if(!$this->_duration)
		{
                        $this->_duration = parent::getDuration($this->_buffer);    
                }
                
                return $this->_duration; 
        } 

        /**
	 * Get the thumbnail location of the media.
	 *
	 * @access  public
         * @param   string  $buffer  The buffer of the remote source.
         * @return  string  The thumbnail.
	 */
	public function getThumbnail($buffer = null)
	{
                if(!$this->_thumbnail)
		{            
                        // Load HWD utilities.
                        hwdMediaShareFactory::load('utilities');
                        $utilities = hwdMediaShareUtilities::getInstance();

                        // Request the required API buffer.
                        if (!$this->_v3snippet) $this->_v3snippet = parent::getBuffer('https://www.googleapis.com/youtube/v3/videos?part=snippet&id=' . $this->parse($this->_url, '') . '&key=AIzaSyB2oL3uUZWDuMLiiSXc_El9Mcgg4nAaNFU', true);

                        // Check request was successful.                                                
                        if ($this->_v3snippet)
                        {
                                // Check for high resolution thumbnail.
                                $json = json_decode($this->_v3snippet);
                                if (!isset($json->error) && isset($json->items[0]->snippet->thumbnails->maxres->url))
                                {
                                        if ($this->_thumbnail = parent::clean($json->items[0]->snippet->thumbnails->maxres->url, 255))
                                        {
                                                if ($utilities->validateUrl($this->_thumbnail))
                                                {
                                                        return $this->_thumbnail; 
                                                }                                      
                                        }
                                }
                        }
                }
                
                if(!$this->_thumbnail)
		{
                        $this->_thumbnail = $this->parse($this->_url, 'hqthumb'); 
                }
                
                if(!$this->_thumbnail)
		{
                        $this->_thumbnail = parent::getThumbnail($this->_buffer);    
                }
                
                return $this->_thumbnail;              
        } 
        
        /**
	 * Get the tags for the media.
	 *
	 * @access  public
         * @param   string  $buffer  The buffer of the remote source.
         * @return  array   The tags.
	 */
	public function getTags($buffer = null)
	{
                return parent::getTags($this->_buffer);
        } 
        
        /**
	 * Render the HTML to display the media.
	 *
	 * @access  public
	 * @param   object  $item  The media item being displayed.
         * @return  string  The HTML to render the media player.
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
                
                // Lookup the embed code.
                $embedLookup = $this->parse($item->source, '');

                // Pull parameters from the original Youtube url and transfer these to the iframe tag where appropriate
                $url = parse_url($item->source);
                if (isset($url['query'])) parse_str($url['query'], $ytvars);
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
                                $HWDplayer = call_user_func(array($pluginClass, 'getInstance'));
                                if (method_exists($HWDplayer, 'getYoutubePlayer'))
                                {                          
                                        if ($player = $HWDplayer->getYoutubePlayer($item, $embedLookup))
                                        {
                                                return $player;
                                        }
                                        else
                                        {
                                                return $utilities->printNotice($HWDplayer->getError());
                                        }
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
                    <iframe width="<?php echo $this->width; ?>" height="<?php echo $this->height; ?>" src="<?php echo JURI::getInstance()->getScheme(); ?>://www.youtube.com/embed/<?php echo $embedLookup; ?>?wmode=opaque&amp;autoplay=<?php echo $this->autoplay; ?>&amp;autohide=<?php echo $params->get('autohide',2); ?>&amp;border=<?php echo $params->get('border',0); ?>&amp;cc_load_policy=<?php echo $params->get('cc_load_policy',1); ?>&amp;cc_lang_pref=<?php echo $params->get('cc_lang_pref','en'); ?>&amp;hl=<?php echo $params->get('hl','en'); ?>&amp;color=<?php echo $params->get('color','red'); ?>&amp;color1=<?php echo $params->get('color1'); ?>&amp;color2=<?php echo $params->get('color2'); ?>&amp;controls=<?php echo $params->get('controls',1); ?>&amp;fs=<?php echo $params->get('fs',1); ?>&amp;hd=<?php echo $params->get('hd',0); ?>&amp;iv_load_policy=<?php echo $params->get('iv_load_policy',1); ?>&amp;modestbranding=<?php echo $params->get('modestbranding',1); ?>&amp;rel=<?php echo $params->get('rel',1); ?>&amp;theme=<?php echo $params->get('theme','dark'); ?>" frameborder="0" allowfullscreen></iframe>	
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
	 * @access  protected
	 * @param   string     $url     The media url.
	 * @param   string     $return  The format to return.
	 * @param   integer    $width   The width.
	 * @param   integer    $height  The height.
	 * @param   integer    $rel     The option to display relative videos after the video finishes playing.
         * @return  string     The ID.
	 */      
        protected function parse($url, $return = 'embed', $width = '', $height = '', $rel = 0)
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
	 * Method to construct the direct display location for the media.
	 *
	 * @access  public
	 * @param   object  $item  The media item being displayed.
         * @return  string  The direct display location.
	 */
	public function getDirectDisplayLocation($item)
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
                
                // Lookup the embed code.
                $embedLookup = $this->parse($item->source, '');

                // Pull parameters from the original Youtube url and transfer these to the iframe tag where appropriate
                $url = parse_url($item->source);
                if (isset($url['query'])) parse_str($url['query'], $ytvars);
                if (isset($ytvars['cc_load_policy'])) $params->set('cc_load_policy', $ytvars['cc_load_policy']);
                if (isset($ytvars['cc_lang_pref'])) $params->set('cc_lang_pref', $ytvars['cc_lang_pref']);
                if (isset($ytvars['hl'])) $params->set('hl', $ytvars['hl']);

                $this->autoplay = $app->input->get('media_autoplay', $config->get('media_autoplay'), 'integer') == 1 ? '1' : '0';

                return JURI::getInstance()->getScheme() .'://www.youtube.com/embed/' . $embedLookup . '?wmode=opaque&amp;autoplay=' . $this->autoplay . '&amp;autohide=' . $params->get('autohide',2) . '&amp;border=' . $params->get('border',0) . '&amp;cc_load_policy=' . $params->get('cc_load_policy',1) . '&amp;cc_lang_pref=' . $params->get('cc_lang_pref','en') . '&amp;hl=' . $params->get('hl','en') . '&amp;color=' . $params->get('color','red') . '&amp;color1=' . $params->get('color1') . '&amp;color2=' . $params->get('color2') . '&amp;controls=' . $params->get('controls',1) . '&amp;fs=' . $params->get('fs',1) . '&amp;hd=' . $params->get('hd',0) . '&amp;iv_load_policy=' . $params->get('iv_load_policy',1) . '&amp;modestbranding=' . $params->get('modestbranding',1) . '&amp;rel=' . $params->get('rel',1) . '&amp;theme=' . $params->get('theme','dark');
        }
        
        /**
	 * Method to determine the type of media that will be displayed.
	 *
	 * @access  public
	 * @param   object   $item  The media item being displayed.
         * @return  integer  The API value of the media type being displayed.
	 */
	public function getDirectDisplayType($item)
	{
                return $this->mediaType;
        }         
}