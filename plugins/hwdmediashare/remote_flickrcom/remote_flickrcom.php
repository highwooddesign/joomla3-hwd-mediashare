<?php
/**
 * @package     Joomla.site
 * @subpackage  Plugin.hwdmediashare.remote_flickrcom
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

// Load the HWD remote library.
JLoader::register('hwdMediaShareRemote', JPATH_ROOT.'/components/com_hwdmediashare/libraries/remote.php');

class plgHwdmediashareRemote_flickrcom extends hwdMediaShareRemote
{    
	/**
	 * The remote media type integer: http://hwdmediashare.co.uk/learn/api/68-api-definitions
         * 
         * @access  public
	 * @var     integer
	 */
	public $mediaType = 3;
        
	/**
	 * The API buffer.
         * 
         * @access  public
	 * @var     string
	 */
        public $_api_info = false;
        public $_api_sizes = false;

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
	 * Returns the plgHwdmediashareRemote_flickrcom object, only creating it if it
	 * doesn't already exist.
	 *
	 * @access  public
	 * @return  object  The plgHwdmediashareRemote_flickrcom object.
	 */
	public static function getInstance()
	{
		static $instance;

		if (!isset ($instance))
                {
			$c = 'plgHwdmediashareRemote_flickrcom';
                        $instance = new $c;
		}

		return $instance;
	}

        /**
	 * Reset properties.
	 *
	 * @access  public
         * @return  void.
	 */
	public function reset()
	{
                // Standard properties.
                $this->_url = false;
                $this->_host = false;
                $this->_buffer = false;
                $this->_title = false;
                $this->_description = false;
                $this->_tags = false;
                $this->_source = false;
                $this->_duration = false;
                $this->_thumbnail = false;
                // Custom properties.
                $this->_api_info = false;
                $this->_api_sizes = false;
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
		// Initialise variables.
                $app = JFactory::getApplication();

                // Load plugin.
		$plugin = JPluginHelper::getPlugin('hwdmediashare', 'remote_flickrcom');

                if(!$this->_title)
		{
                        if ($plugin)
                        {
                                // Load parameters.
                                $params = new JRegistry($plugin->params);

                                // Request the required API buffer.
                                if (!$this->_api_info) $this->_api_info = parent::getBuffer('https://api.flickr.com/services/rest/?method=flickr.photos.getInfo&api_key=' . $params->get('api_key') . '&photo_id=' . $this->parse($this->_url) . '&format=json&nojsoncallback=1', true);

                                // Check request was successful.
                                if ($this->_api_info)
                                {
                                        $json = json_decode($this->_api_info);
                                        if (!isset($json->error) && isset($json->photo->title->_content))
                                        {
                                                if ($this->_title = parent::clean($json->photo->title->_content, 255))
                                                {
                                                        return $this->_title; 
                                                }
                                        }
                                }
                        }
                }
                
                if(!$this->_title)
		{
                        $this->_title = parent::getTitle($this->_buffer);
                        $this->_title = str_replace(" | Flickr - Photo Sharing!", "", $this->_title);                        
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
		// Initialise variables.
                $app = JFactory::getApplication();

                // Load plugin.
		$plugin = JPluginHelper::getPlugin('hwdmediashare', 'remote_flickrcom');
            
                if(!$this->_description)
		{
                        if ($plugin)
                        {
                                // Load parameters.
                                $params = new JRegistry($plugin->params);

                                // Request the required API buffer.
                                if (!$this->_api_info) $this->_api_info = parent::getBuffer('https://api.flickr.com/services/rest/?method=flickr.photos.getInfo&api_key=' . $params->get('api_key') . '&photo_id=' . $this->parse($this->_url) . '&format=json&nojsoncallback=1', true);
                                
                                // Check request was successful.                        
                                if ($this->_api_info)
                                {
                                        $json = json_decode($this->_api_info);
                                        if (!isset($json->error) && isset($json->photo->description->_content))
                                        {
                                                if ($this->_description = parent::clean($json->photo->description->_content))
                                                {
                                                        return $this->_description; 
                                                }
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
                return 0;             
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
		// Initialise variables.
                $app = JFactory::getApplication();

                // Load plugin.
		$plugin = JPluginHelper::getPlugin('hwdmediashare', 'remote_flickrcom');

                if(!$this->_thumbnail)
		{
                        if ($plugin)
                        {
                                // Load HWD utilities.
                                hwdMediaShareFactory::load('utilities');
                                $utilities = hwdMediaShareUtilities::getInstance();

                                // Load parameters.
                                $params = new JRegistry($plugin->params);

                                // Request the required API buffer.
                                if (!$this->_api_sizes) $this->_api_sizes = parent::getBuffer('https://api.flickr.com/services/rest/?method=flickr.photos.getSizes&api_key=' . $params->get('api_key') . '&photo_id=' . $this->parse($this->_url) . '&format=json&nojsoncallback=1', true);

                                // Check request was successful.                                                
                                if ($this->_api_sizes)
                                {
                                        $json = json_decode($this->_api_sizes); 

                                        // Look for large thumbnail.
                                        if (!isset($json->error) && isset($json->sizes->size[5]->url))
                                        {
                                                if ($this->_thumbnail = parent::clean($json->sizes->size[5]->source, 255))
                                                {
                                                        if ($utilities->validateUrl($this->_thumbnail))
                                                        {
                                                                return $this->_thumbnail; 
                                                        }      
                                                }
                                        }

                                        // Look for medium thumbnail.
                                        if (!isset($json->error) && isset($json->sizes->size[4]->url))
                                        {
                                                if ($this->_thumbnail = parent::clean($json->sizes->size[4]->source, 255))
                                                {
                                                        if ($utilities->validateUrl($this->_thumbnail))
                                                        {
                                                                return $this->_thumbnail; 
                                                        }  
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
		$plugin = JPluginHelper::getPlugin('hwdmediashare', 'remote_flickrcom');
		
                // Load the language file.
                $lang = JFactory::getLanguage();
                $lang->load('plg_hwdmediashare_remote_flickrcom', JPATH_ADMINISTRATOR, $lang->getTag());

                if (!$plugin)
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_REMOTE_FLICKRCOM_ERROR_NOT_PUBLISHED'));
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

                $this->width = '100%';
                $this->height = '100%';
                        
                // We force method caching to lookup the available sizes.
                $cache = JFactory::getCache('com_hwdmediashare');
                $cache->setCaching(1);
                $lookupImage = $cache->call(array($this, 'lookupImage'), $item, $params);
                if ($lookupImage)
                {
                        ob_start();
                        ?>
                        <img src="<?php echo $lookupImage; ?>" style="max-width:<?php echo $this->width; ?>px;max-height:<?php echo $this->height; ?>px;" />                      
                        <?php
                        $html = ob_get_contents();
                        ob_end_clean();
                        return $html;
                }    
                
                // Fallback to thumbnail display.
                ob_start();
                ?>
                <img src="<?php echo $item->thumbnail; ?>" style="max-width:<?php echo $this->width; ?>px;max-height:<?php echo $this->height; ?>px;" />                      
                <?php
                $html = ob_get_contents();
                ob_end_clean();
		return $html;                
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
                
                // Thanks: http://www.patricktalmadge.com/2011/12/20/regex-flickr-parser/
$regexstr = '~
# Match Flickr link and embed code
(?:<a [^>]*href=")?		# If a tag match up to first quote of src
(?:				# Group Flickr url
	https?:\/\/		# Either http or https
	(?:[\w]+\.)*		# Optional subdomains
	(?:               		# Group host alternatives.
		flic\.kr     	# Either flic.kr
	        	| flickr\.com	# or flickr.com 
	)			# End Host Group
	(?:\/photos)?		# Optional video sub directory
	\/[^\/]+\/		# Slash and stuff before Id
	([0-9a-zA-Z]+)	# $1: PHOTO_ID is numeric
	[^\s]*			# Not a space
)				# End group
"?				# Match end quote if part of src
(?:.*></a>)?			# Match the end of the a tag
~ix';

                if (preg_match($regexstr, $url, $match)) 
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
		$plugin = JPluginHelper::getPlugin('hwdmediashare', 'remote_flickrcom');
		
                // Load the language file.
                $lang = JFactory::getLanguage();
                $lang->load('plg_hwdmediashare_remote_flickrcom', JPATH_ADMINISTRATOR, $lang->getTag());

                if (!$plugin)
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_REMOTE_FLICKRCOM_ERROR_NOT_PUBLISHED'));
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

                // We force method caching to lookup the available sizes.
                $cache = JFactory::getCache('com_hwdmediashare');
                $cache->setCaching(1);
                $lookupImage = $cache->call(array($this, 'lookupImage'), $item, $params);
                if ($lookupImage)
                {
                        return $lookupImage;
                }    
                
                // Fallback to thumbnail display.
                return $item->thumbnail;
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
        
        /**
	 * Method to lookup the available image sizes from Flickr API.
	 *
	 * @access  public
	 * @param   object  $item    The media item being displayed.
	 * @param   object  $params  The plugin parameters.
         * @return  mixed   The image URL or false on failure.
	 */
	public function lookupImage($item, $params)
	{
                // Load HWD utilities.
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();

                // Request the required API buffer.
                if (!$this->_api_sizes) $this->_api_sizes = parent::getBuffer('https://api.flickr.com/services/rest/?method=flickr.photos.getSizes&api_key=' . $params->get('api_key') . '&photo_id=' . $this->parse($item->source) . '&format=json&nojsoncallback=1', true);

                // Check request was successful.                                                
                if ($this->_api_sizes)
                {
                        $json = json_decode($this->_api_sizes); 

                        // Lookup large 1600.
                        if (!isset($json->error) && isset($json->sizes->size[9]->url))
                        {
                                if ($image = parent::clean($json->sizes->size[9]->source, 255))
                                {
                                        if ($utilities->validateUrl($image))
                                        {
                                                return $image; 
                                        }      
                                }
                        }

                        // Lookup large 1024.
                        if (!isset($json->error) && isset($json->sizes->size[8]->url))
                        {
                                if ($image = parent::clean($json->sizes->size[8]->source, 255))
                                {
                                        if ($utilities->validateUrl($image))
                                        {
                                                return $image; 
                                        }  
                                }
                        }
                } 
                
                return false;
        } 
}
