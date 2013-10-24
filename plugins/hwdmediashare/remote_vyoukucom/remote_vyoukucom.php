<?php
/**
 * @version    $Id: remote_vyoukucom.php 1316 2013-03-20 10:22:56Z dhorsfall $
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
class plgHwdmediashareRemote_vyoukucom extends JObject
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
			$c = 'plgHwdmediashareRemote_vyoukucom';
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
                        $this->_title = str_replace("视频:", "", $this->_title);
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
                        
                        $code = plgHwdmediashareRemote_vyoukucom::parse($this->_url);
                        $api_url = "http://api.youku.com/api_ptvideoinfo/pid/XMTI5Mg==/id/$code/rt/xml";
                        $buffer = hwdMediaShareRemote::getBuffer($api_url);
                        
                        if (!empty($buffer))
                        {       
                                preg_match("/<duration>(.*)<\/duration>/siU", $buffer, $match);  
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
                        jimport( 'joomla.filter.filterinput' );
                        JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');
                        // We will apply the most strict filter to the variable
                        $noHtmlFilter = JFilterInput::getInstance();
                        $code = plgHwdmediashareRemote_vyoukucom::parse($this->_url);
                        $api_url = "http://api.youku.com/api_ptvideoinfo/pid/XMTI5Mg==/id/$code/rt/xml";
                        $buffer = hwdMediaShareRemote::getBuffer($api_url);
                        if (!empty($buffer))
                        {       
                                preg_match("/<imagelink_large>(.*)<\/imagelink_large>/siU", $buffer, $match);                   
                                if (!empty($match[1]))
                                {
                                        $thumbnail = $match[1];                                        
                                        $thumbnail = str_replace("<![CDATA[", "", $thumbnail);
                                        $thumbnail = str_replace("]]>", "", $thumbnail);
                                        $thumbnail = (string)str_replace(array("\r", "\r\n", "\n"), '', $thumbnail);
                                        $thumbnail = $noHtmlFilter->clean($thumbnail);
                                        $thumbnail = JHtmlString::truncate($thumbnail, 255);
                                        $thumbnail = trim($thumbnail);       
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
		$plugin =& JPluginHelper::getPlugin('hwdmediashare', 'remote_vyoukucom');
		$params = new JRegistry( @$plugin->params );
                
                // Load hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();

                $this->width = $utilities->getMediaWidth();
                $this->height = (int) $config->get('mediaitem_height') ? $config->get('mediaitem_height') : $this->width*$config->get('video_aspect',0.75);

                $id = plgHwdmediashareRemote_vyoukucom::parse($item->source);
                ob_start();
                ?>
                <embed src="http://player.youku.com/player.php/sid/<?php echo $id; ?>/v.swf" quality="high" width="<?php echo $this->width; ?>" height="<?php echo $this->height; ?>" align="middle" allowScriptAccess="sameDomain" allowFullscreen="true" type="application/x-shockwave-flash"></embed>
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
		$pos_u = strpos($url, "id_");
		$code = array();

		if ($pos_u === false)
		{
			return null;
		}
		else if ($pos_u)
		{
			$pos_u_start = $pos_u + 3;
			$pos_u_end = $pos_u_start + 13;

			$length = $pos_u_end - $pos_u_start;
			$code = substr($url, $pos_u_start, $length);                       
			$code = strip_tags($code);
                        $code = preg_replace("/[^a-zA-Z0-9s]/", "", $code);
		}

		return $code; 
        }
}