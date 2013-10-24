<?php
/**
 * @version    $Id: remote_ukextremecom.php 509 2012-09-19 14:23:44Z
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Sam Cummings
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import hwdMediaShare remote library
hwdMediaShareFactory::load('remote');

/**
 * hwdMediaShare Extreme.com remote plugin class
 *
 * @package hwdMediaShare
 * @since   1.0.5
 */
class plgHwdmediashareRemote_ukextremecom extends JObject
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
        var $_id;

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
	 * Returns the plgHwdmediashareRemote_ukextremecom object, only creating it if it
	 * doesn't already exist.
	 *
	 * @return  plgHwdmediashareRemote_ukextremecom A plgHwdmediashareRemote_ukextremecom object.
	 * @since   0.1
	 */
	public static function getInstance()
	{
		static $instance;
                unset($instance);
		if (!isset ($instance))
                {
			$c = 'plgHwdmediashareRemote_ukextremecom';
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
                        $this->_title = str_replace(" - video", "", $this->_title);
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
                        $this->getBuffer();
                        $this->_description = hwdMediaShareRemote::getDescription($this->_buffer);
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
//                if( !$this->_duration )
//		{
//                        jimport( 'joomla.filter.filterinput' );
//                        JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');
//
//                        // We will apply the most strict filter to the variable
//                        $noHtmlFilter = JFilterInput::getInstance();
//
//                        $duration = false;
//
//                        // Check Open Graph tag
//                        preg_match('/" /></a><div class="video_duration">(.*)</', $this->_buffer, $match);
//                        if (!empty($match[1]))
//                        {
//                                $duration = (int) $match[1];
//                        }
//                }
//                if ($duration > 0)
//                {
//                        $this->_duration = $duration;
//                        return $this->_duration;
//                }
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
		$plugin =& JPluginHelper::getPlugin('hwdmediashare', 'remote_ukextremecom');
		$params = new JParameter( @$plugin->params );

                // Load hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();

                $this->video_width = $utilities->getMediaWidth();
                $this->video_height = (int) (($this->video_width*9)/16);

                $id = $this->parse($item->source);
                ob_start();
                ?>
                <div id="fcplayer_container" style="width:<?php echo $this->video_width; ?>px;margin:0 auto;"></div><script type="text/javascript" src="http://player.extreme.com/embed/<?php echo $id; ?>.js?width=<?php echo $this->video_width; ?>&amp;height=<?php echo $this->video_height; ?>"></script>
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
        public function parse($url)
        {
                $buffer = hwdMediaShareRemote::getBuffer($url);
                preg_match('/video_id":"([^"]+)/', $buffer, $match);
                if (!empty($match[1]))
                {
                        return $match[1];
                }
                
                return false;
        }
}