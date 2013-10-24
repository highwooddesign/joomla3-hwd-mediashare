<?php
/**
 * @version    $Id: remote_vimeocom.php 1667 2013-08-21 12:31:43Z dhorsfall $
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
class plgHwdmediashareRemote_vimeocom extends JObject
{    
	/**
	 * Remote media type integer.
	 *
	 * @var		int
	 */
	public $mediaType = 4;
        
        var $_url;
        var $_host;
        var $_buffer;
        var $_title;
        var $_description;
        var $_source;
        var $_duration;
        var $_thumbnail;
        
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
                unset($instance);
		if (!isset ($instance))
                {
			$c = 'plgHwdmediashareRemote_vimeocom';
                        $instance = new $c;
		}

		return $instance;
	}
    
        /**
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/
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
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/
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
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/
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
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/
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
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/
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
                                                $length = $pos_thumb_end+4 - $pos_thumb_start;
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
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/
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
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/
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
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/
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
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/
	public function display($item)
	{
		$plugin =& JPluginHelper::getPlugin('hwdmediashare', 'remote_vimeocom');
		$params = new JRegistry( @$plugin->params );
                
                // Load hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                $autoplay = (JRequest::getInt('media_autoplay', $config->get('media_autoplay')) == 1 ? 'true' : 'false');

                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();

                $this->width = $utilities->getMediaWidth();
                $this->height = (int) $config->get('mediaitem_height') ? $config->get('mediaitem_height') : $this->width*$config->get('video_aspect',0.75);

                $id = plgHwdmediashareRemote_vimeocom::parse($item->source);                
                ob_start();
                ?>
                <iframe src="<?php echo JURI::getInstance()->getScheme(); ?>://player.vimeo.com/video/<?php echo $id; ?>?title=0&amp;autoplay=<?php echo $autoplay; ?>&amp;byline=0&amp;portrait=0" width="<?php echo $this->width; ?>" height="<?php echo $this->height; ?>" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>	
                <?php
                $html = ob_get_contents();
                ob_end_clean();
		return $html;
	}
        
        /**
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/        
        protected function parse($url)
        {
                // Switch back to http
                $url = str_replace("https", "http", $url);
                
                if (preg_match('~^http://(?:www\.)?vimeo\.com/(?:clip:)?(\d+)~', $url, $match)) 
                {
                        if (!empty($match[1])) return $match[1];
                } 
                return null;
        }
}