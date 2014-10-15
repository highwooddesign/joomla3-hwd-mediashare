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

class plgHwdmediashareRemote_vimeocom extends hwdMediaShareRemote
{    
	/**
	 * The remote media type integer: http://hwdmediashare.co.uk/learn/api/68-api-definitions
         * 
         * @access  public
	 * @var     integer
	 */
	public $mediaType = 4;
        
	/**
	 * The API buffer.
         * 
         * @access  public
	 * @var     string
	 */
        public $_v2api = false;

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
	 * Returns the plgHwdmediashareRemote_vimeocom object, only creating it if it
	 * doesn't already exist.
	 *
	 * @access  public
	 * @return  object  The plgHwdmediashareRemote_vimeocom object.
	 */
	public static function getInstance()
	{
		static $instance;

		if (!isset ($instance))
                {
			$c = 'plgHwdmediashareRemote_vimeocom';
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
                        // Request the required API buffer.
                        if (!$this->_v2api) $this->_v2api = parent::getBuffer('https://vimeo.com/api/v2/video/' . $this->parse($this->_url) . '.json', true);

                        // Check request was successful.
                        if ($this->_v2api)
                        {
                                $json = json_decode($this->_v2api);
                                if (!isset($json->error) && isset($json[0]->title))
                                {
                                        if ($this->_title = parent::clean($json[0]->title, 255))
                                        {
                                                return $this->_title; 
                                        }
                                }
                        }
                }
                
                if(!$this->_title)
		{
                        $this->_title = parent::getTitle($this->_buffer);
                        $this->_title = str_replace(" on Vimeo", "", $this->_title);                        
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
                        if (!$this->_v2api) $this->_v2api = parent::getBuffer('https://vimeo.com/api/v2/video/' . $this->parse($this->_url) . '.json', true);

                        // Check request was successful.                        
                        if ($this->_v2api)
                        {
                                $json = json_decode($this->_v2api);
                                if (!isset($json->error) && isset($json[0]->description))
                                {
                                        if ($this->_description = parent::clean($json[0]->description))
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
                        if (!$this->_v2api) $this->_v2api = parent::getBuffer('https://vimeo.com/api/v2/video/' . $this->parse($this->_url) . '.json', true);

                        // Check request was successful.                                                
                        if ($this->_v2api)
                        {
                                $json = json_decode($this->_v2api);                        
                                if (!isset($json->error) && isset($json[0]->duration))
                                {
                                        $this->_duration = (int) $json[0]->duration;
                                        if ($this->_duration > 0) 
                                        {
                                                return $this->_duration;
                                        }
                                }
                        }  
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
                        if (!$this->_v2api) $this->_v2api = parent::getBuffer('https://vimeo.com/api/v2/video/' . $this->parse($this->_url) . '.json', true);

                        // Check request was successful.                                                
                        if ($this->_v2api)
                        {
                                $json = json_decode($this->_v2api); 

                                // Look for large thumbnail.
                                if (!isset($json->error) && isset($json[0]->thumbnail_large))
                                {
                                        if ($this->_thumbnail = parent::clean($json[0]->thumbnail_large, 255))
                                        {
                                                if ($utilities->validateUrl($this->_thumbnail))
                                                {
                                                        return $this->_thumbnail; 
                                                }      
                                        }
                                }

                                // Look for medium thumbnail.
                                if (!isset($json->error) && isset($json[0]->thumbnail_medium))
                                {
                                        if ($this->_thumbnail = parent::clean($json[0]->thumbnail_medium, 255))
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
		$plugin = JPluginHelper::getPlugin('hwdmediashare', 'remote_vimeocom');
		
                // Load the language file.
                $lang = JFactory::getLanguage();
                $lang->load('plg_hwdmediashare_remote_vimeocom', JPATH_SITE . '/administrator');

                if (!$plugin)
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_REMOTE_VIMEOCOM_ERROR_NOT_PUBLISHED'));
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
                $embedLookup = $this->parse($item->source);
                if ($embedLookup)
                {
                        $this->autoplay = $app->input->get('media_autoplay', $config->get('media_autoplay'), 'integer') == 1 ? '1' : '0';
                        $this->width = '100%';
                        $this->height = '100%';
                        ob_start();
                        ?>
                        <div class="media-respond" style="max-width:<?php echo $config->get('mediaitem_size', '500'); ?>px;">
                          <div class="media-aspect" data-aspect="<?php echo $config->get('video_aspect', '0.75'); ?>"></div>
                          <div class="media-content">
                            <iframe src="<?php echo JURI::getInstance()->getScheme(); ?>://player.vimeo.com/video/<?php echo $embedLookup; ?>?title=0&amp;autoplay=<?php echo $this->autoplay; ?>&amp;byline=0&amp;portrait=0" width="<?php echo $this->width; ?>" height="<?php echo $this->height; ?>" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>	
                          </div>
                        </div>
                        <?php
                        $html = ob_get_contents();
                        ob_end_clean();
                        return $html;
                }
                else
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_REMOTE_VIMEOCOM_ERROR_PLAYBACK_PROBLEM_SEE_ORIGINAL'));
                        return false;
                }
                

	}
        
        /**
	 * Parse the source to extract the media.
	 *
	 * @access  protected
	 * @param   string     $url  The media url.
         * @return  string     The ID.
	 */         
        protected function parse($url)
        {
                // Switch back to http.
                $url = str_replace("https", "http", $url);
                
                if (preg_match('~^http://(?:www\.)?vimeo\.com/(?:clip:)?(\d+)~', $url, $match)) 
                {
                        if (!empty($match[1])) return $match[1];
                }
                
                return null;
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
		$plugin = JPluginHelper::getPlugin('hwdmediashare', 'remote_vimeocom');
		
                // Load the language file.
                $lang = JFactory::getLanguage();
                $lang->load('plg_hwdmediashare_remote_vimeocom', JPATH_SITE . '/administrator');

                if (!$plugin)
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_REMOTE_VIMEOCOM_ERROR_NOT_PUBLISHED'));
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
                $embedLookup = $this->parse($item->source);
            
                $this->autoplay = $app->input->get('media_autoplay', $config->get('media_autoplay'), 'integer') == 1 ? '1' : '0';

                return JURI::getInstance()->getScheme() .'://player.vimeo.com/video/' . $embedLookup . '?title=0&amp;autoplay=' . $this->autoplay . '&amp;byline=0&amp;portrait=0';
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