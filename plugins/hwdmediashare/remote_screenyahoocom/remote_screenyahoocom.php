<?php
/**
 * @version    $Id: remote_screenyahoocom.php 509 2012-09-19 14:23:44Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Sam Cummings
 * @since      02-Nov-2012 10:14:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import hwdMediaShare remote library
hwdMediaShareFactory::load('remote');

/**
 * hwdMediaShare Screenyahoo.com remote plugin class
 *
 * @package hwdMediaShare
 * @since   1.0.5
 */
class plgHwdmediashareRemote_screenyahoocom extends JObject
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
	 * Returns the plgHwdmediashareRemote_screenyahoocom object, only creating it if it
	 * doesn't already exist.
	 *
	 * @return  plgHwdmediashareRemote_screenyahoocom A plgHwdmediashareRemote_screenyahoocom object.
	 * @since   0.1
	 */
	public static function getInstance()
	{
		static $instance;
                unset($instance);
		if (!isset ($instance))
                {
			$c = 'plgHwdmediashareRemote_screenyahoocom';
                        $instance = new $c;
		}

		return $instance;
	}

        /**
	 * Method to extract title from remote page
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
	 * Method to extract description from remote page
         *
	 * @since   0.1
	 **/
	public function getDescription()
	{
                if( !$this->_description )
		{
                        hwdMediaShareFactory::load('remote');
                        $buffer = $this->getBuffer();
                        //$this->_description = hwdMediaShareRemote::getDescription($this->_buffer);

                        jimport( 'joomla.filter.filterinput' );
                        JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');

                        // We will apply the most strict filter to the variable
                        $noHtmlFilter = JFilterInput::getInstance();

                        $description = false;

                        // Check standard description meta tag
                        preg_match('/<meta name="description"(.*)content="([^"]+)/', $this->_buffer, $match);
                        if (!empty($match[2]))
                        {
                                $description = $match[2];
                                $description = (string)str_replace(array("\r", "\r\n", "\n", "&#039;"), '', $description);
                                $description = $noHtmlFilter->clean($description);
                                $description = JHtmlString::truncate($description, 5120);
                                $description = trim($description);
                        }
                        $descriptionIsTitle = "Watch the video " . $this->_title . " on Yahoo! Screen.";
                        if($description === $descriptionIsTitle){
                            preg_match('/<p class="description">([^"]+)/', $this->_buffer, $match2);
                            if (!empty($match2[1])){
                                $description = $match2[1];
                                $description = (string)str_replace(array("\r", "\r\n", "\n"), '', $description);
                                $description = $noHtmlFilter->clean($description);
                                $description = JHtmlString::truncate($description, 5120);
                                $description = trim($description);
                            }
                            $this->_description = $description;
                            $this->_description = str_replace("div class=", "", $this->_description);
                        }
                        else{
                            $this->_description = $description;
                            $this->_description = str_replace("Watch the video ", "", $this->_description);
                            $this->_description = str_replace("$this->_title", "", $this->_description);
                            $this->_description = str_replace(" on Yahoo! Screen.", "", $this->_description);
                            $this->_description = str_replace(" on Yahoo! Screen . ", "", $this->_description);
                        }
                }
                return $this->_description;
        }

        /**
	 * Method to set srouce of remote media
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
	 * Method to extract duration from remote page
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
                        preg_match('/durtn":"([^"]+)/', $this->_buffer, $match);
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
                                if (count(explode(':', $ts)) == 0)
                                {
                                        $duration = (int) $match[1];
                                }

                         }
                }

                if ($duration > 0)
                {
                        $this->_duration = $duration;
                        return $this->_duration;
                }
        }

        /**
	 * Method to extract thumbnail from remote page
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

                        $thumbnail = false;

                        // Check Open Graph tag
                        preg_match('/<meta property="og:image" content="([^"]+)/', $this->_buffer, $match);
                        if (!empty($match[1]))
                        {
                                $thumbnail = $match[1];
                                $thumbnail = (string)str_replace(array("\r", "\r\n", "\n"), '', $thumbnail);
                                $thumbnail = $noHtmlFilter->clean($thumbnail);
                                $thumbnail = JHtmlString::truncate($thumbnail, 5120);
                                $thumbnail = trim($thumbnail);
                        }
                }
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
	 * Method to load remote page into buffer
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
	 * Method to get host of remote media
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
	 * Method to get url of remote media
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
	 * Method to get display remote media from remote source
         *
	 * @since   0.1
	 **/
	public function display($item)
	{
		$plugin =& JPluginHelper::getPlugin('hwdmediashare', 'remote_screenyahoocom');
		$params = new JParameter( @$plugin->params );

                // Load hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();

                $this->width = $utilities->getMediaWidth();
                $this->height = (int) (($this->width*9)/16);

                $id = plgHwdmediashareRemote_screenyahoocom::parse($item->source);
                $vidid = plgHwdmediashareRemote_screenyahoocom::parse2($item->source);
                ob_start();
                ?>
<!--            There are different embeds!!-->
                <iframe width="<?php echo $this->width; ?>" height="<?php echo $this->height; ?>" scrolling="no" frameborder="0" src="http://screen.yahoo.com/<?php echo $id; ?>?format=embed"></iframe>
                <!--<div><iframe frameborder="0" width="<?php echo $this->width; ?>" height="<?php echo $this->height; ?>" src="http://d.yimg.com/nl/vyc/site/player.html#shareUrl=http%3A%2F%2Fscreen.yahoo.com%2F<?php echo $id; ?>&vid=<?php echo $vidid; ?>&browseCarouselUI=hide&repeat=0&startScreenCarouselUI=hide"></iframe></div>-->
                <?php
                $html = ob_get_contents();
                ob_end_clean();

                return $html;
	}

        /**
	 * Method to parse the source to extract the media code
         *
	 * @since   0.1
	 **/
        protected function parse($url)
        {
		$pos_u = strpos($url, "com/");
		$code = array();

		if ($pos_u === false)
		{
			return null;
		}
		else if ($pos_u)
		{
			$pos_u_start = $pos_u + 4;
			//$pos_u_end = $pos_u_start + 17;

			$length = strlen($url);
			$code = substr($url, $pos_u_start, $length);
			$code = strip_tags($code);
                        //$code = preg_replace("/[^a-zA-Z0-9s]/", "", $code);
		}
		return $code;
        }

                /**
	 * Method to parse the source to extract the media code
         *
	 * @since   0.1
	 **/
        protected function parse2($url)
        {
		$pos_u = strpos($url, ".html");
		$vidCode = array();

		if ($pos_u === false)
		{
			return null;
		}
		else if ($pos_u)
		{
			$pos_u_start = $pos_u - 8;

                        $vidCode = substr($url, $pos_u_start, $pos_u);
			$vidCode = strip_tags($vidCode);
		}
		return $vidCode;
        }
}