<?php
/**
 * @version    $Id: remote_bliptv.php 1322 2013-03-20 10:24:06Z dhorsfall $
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
class plgHwdmediashareRemote_bliptv extends JObject
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
			$c = 'plgHwdmediashareRemote_bliptv';
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
                        $this->_title = str_replace(" on Blip", "", $this->_title);
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
                        preg_match('/<meta itemprop="duration" content="([^"]+)/', $this->_buffer, $match);                     
                        if (!empty($match[1]))
                        {
                                $ts = $match[1];                                        
                                if (count(explode(':', $ts)) == 1)
                                {
                                        list($secs) = explode(':', $ts);
                                        $duration = $secs;
                                } 
                                else if (count(explode(':', $ts)) == 2)
                                {
                                        list($mins, $secs) = explode(':', $ts);
                                        $duration = ($mins * 60) + $secs;
                                } 
                                else if (count(explode(':', $ts)) == 3)
                                {
                                        list($hours, $mins, $secs) = explode(':', $ts);
                                        $duration = ($hours * 3600) + ($mins * 60) + $secs;
                                }                                                                                 
                        }                    
                }
                $duration = (int) $duration;

                // Reteurn a valid duration
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
                        $code = plgHwdmediashareRemote_bliptv::parse($this->_url);
                        $api_url = "http://blip.tv/rss/view/$code";

                        $buffer = hwdMediaShareRemote::getBuffer($api_url);

                        if (!empty($buffer))
                        {
                                preg_match('/<media:thumbnail url="([^"]+)/', $buffer, $match);
                                if (!empty($match[1]))
                                {
                                        // Import JHtmlString library
                                        JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');

                                        // We will apply the most strict filter to the variable
                                        $noHtmlFilter = JFilterInput::getInstance();
                                    
                                        $thumbnail = $match[1];
                                        $thumbnail = (string)str_replace(array("\r", "\r\n", "\n"), '', $thumbnail);
                                        $thumbnail = $noHtmlFilter->clean($thumbnail);
                                        $thumbnail = JHtmlString::truncate($thumbnail, 255);
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
                        
                        // Couldn't find a thumbnail
                        return false;
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
		$plugin =& JPluginHelper::getPlugin('hwdmediashare', 'remote_bliptv');
		$params = new JRegistry( @$plugin->params );
                
                // Load hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();

                $this->width = $utilities->getMediaWidth();
                $this->height = (int) $config->get('mediaitem_height') ? $config->get('mediaitem_height') : $this->width*$config->get('video_aspect',0.75);

                $id = null;
                $code = plgHwdmediashareRemote_bliptv::parse($item->source);
                $api_url = "http://blip.tv/rss/view/$code";
                $buffer = hwdMediaShareRemote::getBuffer($api_url);
                
                if (!empty($buffer))
                {
                        // Check standard title tag
                        preg_match("/<blip:embedLookup>(.*)<\/blip:embedLookup>/siU", $buffer, $match);                        
                        if (!empty($match[1]))
                        {
                                $id = $match[1];
                                $id = preg_replace("/[^a-zA-Z0-9-_+]/", "", $id);
                        }
                }                
                
                ob_start();
                ?>
                <iframe src="http://blip.tv/play/<?php echo $id; ?>.html?p=1" width="<?php echo $this->width; ?>" height="<?php echo $this->height; ?>" frameborder="0" allowfullscreen></iframe><embed type="application/x-shockwave-flash" src="http://a.blip.tv/api.swf#<?php echo $id; ?>" style="display:none"></embed>
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
                $code = substr($url, -7);
                $code = preg_replace("/[^0-9]/", "", $code);

                if (!empty($code)) return $code;

                return null;
        }
}