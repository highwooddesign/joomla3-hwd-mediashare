<?php
/**
 * @package     Joomla.site
 * @subpackage  Plugin.hwdmediashare.remote_vimeocom
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class plgHwdmediashareRemote_vimeocom extends JObject
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
	 * Returns the plgHwdmediashareRemote_vimeocom object, only creating it if it
	 * doesn't already exist.
	 *
	 * @access	public
	 * @return      object      The plgHwdmediashareRemote_vimeocom object.
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
                        $this->_title = str_replace(" on Vimeo", "", $this->_title);
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
                        jimport( 'joomla.filter.filterinput' );
                        JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');

                        // We will apply the most strict filter to the variable
                        $noHtmlFilter = JFilterInput::getInstance();

                        $duration = false;                      
   
                        // Check Open Graph tag
                        preg_match('/<div class="ag"><div class="o"><span>(.*)<\/span>/', $this->_buffer, $match);                     
                        if (!empty($match[1]))
                        {
                                $duration = (int) $match[1];
                        }                    
                }   
                if ($duration > 0)
                {
                        $this->_duration = $duration;
                        return $this->_duration;
                }                
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
                        $code = plgHwdmediashareRemote_vimeocom::parse($this->_url);
                        $api_url = "http://vimeo.com/api/v2/video/$code.xml";

                        $buffer = hwdMediaShareRemote::getBuffer($api_url);

                        if (!empty($buffer))
                        {
                                $pos_thumb_search = strpos($buffer, "thumbnail_medium");

                                if ($pos_thumb_search === false)
                                {
                                        return null;
                                }
                                else
                                {
                                        $pos_thumb_start = strpos($buffer, "http", $pos_thumb_search);
                                        $pos_thumb_end = strpos($buffer, '.jpg', $pos_thumb_start);
                                        if ($pos_thumb_end === false)
                                        {
                                                return null;
                                        }
                                        else
                                        {
                                                $length = $pos_thumb_end + 4 - $pos_thumb_start;
                                                $thumbnail = substr($buffer, $pos_thumb_start, $length);
                                                $thumbnail = strip_tags($thumbnail);
                                        }
                                }
                        }

                        $thumbnail = trim(strip_tags($thumbnail));

                        hwdMediaShareFactory::load('utilities');
                        $utilities = hwdMediaShareUtilities::getInstance();
                        $isValid = $utilities->validateUrl( $thumbnail );
                        
                        if ($isValid)
                        {
                                $this->_thumbnail = $thumbnail;
                                return $this->_thumbnail;
                        }
                }             
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
                
                // Get Vimeo ID
                $id = plgHwdmediashareRemote_vimeocom::parse($item->source);
              
                $this->autoplay = $app->input->get('media_autoplay', $config->get('media_autoplay'), 'integer') == 1 ? '1' : '0';
                $this->width = '100%';
                $this->height = '100%';
                ob_start();
                ?>
                <div class="media-respond" style="max-width:<?php echo $config->get('mediaitem_size', '500'); ?>px;">
                  <div class="media-aspect" data-aspect="<?php echo $config->get('video_aspect', '0.75'); ?>"></div>
                  <div class="media-content">
                    <iframe src="<?php echo JURI::getInstance()->getScheme(); ?>://player.vimeo.com/video/<?php echo $id; ?>?title=0&amp;autoplay=<?php echo $this->autoplay; ?>&amp;byline=0&amp;portrait=0" width="<?php echo $this->width; ?>" height="<?php echo $this->height; ?>" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>	
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
         * @return      void
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
}