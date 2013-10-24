<?php
/**
 * @version    $Id: remote_videogooglecom.php 1318 2013-03-20 10:23:23Z dhorsfall $
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
class plgHwdmediashareRemote_videogooglecom extends JObject
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
			$c = 'plgHwdmediashareRemote_videogooglecom';
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
                        preg_match('/<span class=gray id=video-duration>(.*)<\/span>/', $this->_buffer, $match);                     
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
                        $code = plgHwdmediashareRemote_videogooglecom::parse($this->_url);
                        $api_url = "http://video.google.com/videofeed?docid=$code";

                        $buffer = hwdMediaShareRemote::getBuffer($api_url);

                        if (!empty($buffer))
                        {
				preg_match('/<media:thumbnail url="([^"]+)/',$buffer,$thumbnail_array);
				$thumbnail = $thumbnail_array[1];
				//Remove amp;
				$thumbnail = str_replace('amp;','',$thumbnail);
                        }

                        $thumbnail = trim(strip_tags($thumbnail));

                        $this->_thumbnail = $thumbnail;
                        return $this->_thumbnail;
                        
                        // Not validating due to dynamic format
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
		$plugin =& JPluginHelper::getPlugin('hwdmediashare', 'remote_videogooglecom');
		$params = new JRegistry( @$plugin->params );
                
                // Load hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();

                $this->width = $utilities->getMediaWidth();
                $this->height = (int) $config->get('mediaitem_height') ? $config->get('mediaitem_height') : $this->width*$config->get('video_aspect',0.75);

                $id = plgHwdmediashareRemote_videogooglecom::parse($item->source);
                ob_start();
                ?>
                <embed id=VideoPlayback src=http://video.google.com/googleplayer.swf?docid=<?php echo $id; ?>&hl=en&fs=true style=width:<?php echo $this->width; ?>px;height:<?php echo $this->height; ?>px allowFullScreen=true allowScriptAccess=always type=application/x-shockwave-flash> </embed>
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
                
                $id = false;

                if (preg_match('/^http:\/\/video\.google\.com\/videoplay\?docid=([^&]+)(&hl=.+)?$/', $url, $preg))
                {
                        $id = $preg[1];
                } 
                elseif (preg_match('/^http:\/\/video\.google\.com\/googleplayer\.swf\?docId=(.+)$/', $url, $preg))
                {
                        $id = $preg[1];
                }

                preg_match('/([0-9-]+)/', $id, $preg);
                $id = $preg[1];

                return $id;
        }
}