<?php
/**
 * @version    $Id: remote_soundcloudcom.php 1321 2013-03-20 10:23:58Z dhorsfall $
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
class plgHwdmediashareRemote_soundcloudcom extends JObject
{
	/**
	 * Remote media type integer.
	 *
	 * @var		int
	 */
	public $mediaType = 1;

        var $_url;
        var $_host;
        var $_buffer;
        var $_title;
        var $_description;
        var $_source;
        var $_duration;
        var $_id;
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
			$c = 'plgHwdmediashareRemote_soundcloudcom';
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

                        $resolverData = json_decode($this->_buffer);
                        if( $resolverData->title )
                        {
                                $this->_title = $resolverData->title;
                        }

                        if( !$this->_title )
                        {
                                $this->_title = $resolverData->full_name;
                        }

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

                        $resolverData = json_decode($this->_buffer);
                        if( $resolverData->description )
                        {
                                $this->_description = $resolverData->description;
                        }
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

                        $resolverData = json_decode($this->_buffer);
                        if( $resolverData->duration )
                        {
                                $this->_duration = $resolverData->duration;
                                $this->_duration = round($this->_duration/1000);
                        }
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

                        $resolverData = json_decode($this->_buffer);
                        if( $resolverData->artwork_url )
                        {
                                $this->_thumbnail = $resolverData->artwork_url;
                        }
                        if( !$this->_thumbnail )
                        {
                                $this->_thumbnail = $resolverData->avatar_url;
                        }
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
                $resolverUrl =  'http://api.soundcloud.com/resolve.json?url='.$this->getUrl().'&client_id=YOUR_CLIENT_ID';
                
                if (!$this->_buffer)
                {
                        hwdMediaShareFactory::load('remote');
                        $this->_buffertemp = hwdMediaShareRemote::getBuffer($resolverUrl);
                        $resolverData = json_decode($this->_buffertemp);
                        if (!empty($resolverData->location))
                        {
                            $this->_buffer = hwdMediaShareRemote::getBuffer($resolverData->location);
                        }
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
		$plugin =& JPluginHelper::getPlugin('hwdmediashare', 'remote_soundcloudcom');
		$params = new JRegistry( @$plugin->params );

                // Load hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                $autoplay = (JRequest::getInt('media_autoplay', $config->get('media_autoplay')) == 1 ? '1' : '0');

                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();

                $this->width = $utilities->getMediaWidth();
                $this->height = 166;

                $player = plgHwdmediashareRemote_soundcloudcom::parse($item->source);
                
                if(!$player)
                {
                    return 'error message';
                }

                ob_start();
                ?>
                <iframe width="<?php echo $this->width; ?>" height="<?php echo $this->height; ?>" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=<?php echo $player; ?>"></iframe>
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
                hwdMediaShareFactory::load('remote');

                // Get data for playback
                $resolverUrl =  'http://api.soundcloud.com/resolve.json?url='.$url.'&client_id=YOUR_CLIENT_ID';
                $buffer = hwdMediaShareRemote::getBuffer($resolverUrl);
                $resolverData = json_decode($buffer);

                if (!empty($resolverData->location))
                {
                        $pos = strpos($resolverData->location, "/tracks/");
                        if ($pos === false)
                        {
                                $pos = strpos($resolverData->location, "/users/");
                                if ($pos === false)
                                {
                                        return false;
                                }
                                else if ($pos)
                                {
                                        // Get the necessary string
                                        $pos_start = $pos + 7;
                                        $pos_end = $pos_start + 8;
                                        $length = $pos_end - $pos_start;
                                        $code = substr($resolverData->location, $pos_start, $length);
                                        $code = strip_tags($code);
                                        $code = preg_replace("/[^a-zA-Z0-9s_-]/", "", $code);
                                        
                                        // Set height for this type
                                        $this->height = 450;
                                        
                                        return 'http://api.soundcloud.com/users/' . $code;
                                }
                        }
                        else if ($pos)
                        {
                                // Get the necessary string
                                preg_match('/tracks\/(.*?).json/', $resolverData->location, $match);                                
                                if (!empty($match[1]))
                                {
                                        $code = preg_replace("[^0-9]", "", $match[1]);                                    
                                        return 'http://api.soundcloud.com/tracks/' . $code;
                                }
                        }
                }
		return false;
        }
}