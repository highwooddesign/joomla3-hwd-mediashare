<?php
/**
 * @version    $Id: remote_dailymotioncom.php 1669 2013-08-21 12:34:37Z dhorsfall $
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
class plgHwdmediashareRemote_dailymotioncom extends JObject
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
			$c = 'plgHwdmediashareRemote_dailymotioncom';
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
                        hwdMediaShareFactory::load('remote');
                        $this->getBuffer();
                        $this->_duration = hwdMediaShareRemote::getDuration($this->_buffer); 
                }             
                return $this->_duration;   
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
                        hwdMediaShareFactory::load('remote');
                        $this->getBuffer();
                        $this->_thumbnail = hwdMediaShareRemote::getThumbnail($this->_buffer); 
                }             
                return $this->_thumbnail;              
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
		$plugin =& JPluginHelper::getPlugin('hwdmediashare', 'remote_dailymotioncom');
		$params = new JRegistry( @$plugin->params );
                
                // Load hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                $autoplay = (JRequest::getInt('media_autoplay', $config->get('media_autoplay')) == 1 ? '1' : '0');

                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();

                $this->width = $utilities->getMediaWidth();
                $this->height = (int) $config->get('mediaitem_height') ? $config->get('mediaitem_height') : $this->width*$config->get('video_aspect',0.75);

                $id = plgHwdmediashareRemote_dailymotioncom::parse($item->source);
                ob_start();
                ?>
                <iframe frameborder="0" width="<?php echo $this->width; ?>" height="<?php echo $this->height; ?>" src="http://www.dailymotion.com/embed/video/<?php echo $id; ?>?logo=0&amp;autoPlay=<?php echo $autoplay; ?>"></iframe>
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
                // Dailymotion url
                preg_match('#http://www.dailymotion.com/video/([A-Za-z0-9]+)#s', $url, $matches);
                if(isset($matches[1])) 
                {
                        return $matches[1];                       
                }
		return false;            
        }
}