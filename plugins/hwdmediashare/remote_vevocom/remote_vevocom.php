<?php
/**
 * @version    $Id: remote_vevocom.php 509 2012-09-19 14:23:44Z dhorsfall $
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
 * hwdMediaShare Vevo.com remote plugin class
 *
 * @package hwdMediaShare
 * @since   1.1.7
 */
class plgHwdmediashareRemote_vevocom extends JObject
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
	 * Returns the plgHwdmediashareRemote_vevocom object, only creating it if it
	 * doesn't already exist.
	 *
	 * @return  plgHwdmediashareRemote_vevocom A plgHwdmediashareRemote_vevocom object.
	 * @since   0.1
	 */
	public static function getInstance()
	{
		static $instance;
                unset($instance);
		if (!isset ($instance))
                {
			$c = 'plgHwdmediashareRemote_vevocom';
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
                        $this->_title = str_replace("Watch Videos Online | ", "", $this->_title);
                        $this->_title = str_replace(" | Vevo.com", "", $this->_title);
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
                if( !$this->_duration )
		{
                        jimport( 'joomla.filter.filterinput' );
                        JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');

                        // We will apply the most strict filter to the variable
                        $noHtmlFilter = JFilterInput::getInstance();

                        $duration = false;

                        // Check Open Graph tag
                        preg_match('/<meta property="video:duration" content="([^"]+)/', $this->_buffer, $match);
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
		$plugin =& JPluginHelper::getPlugin('hwdmediashare', 'remote_vevocom');
		$params = new JParameter( @$plugin->params );

                // Load hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();

                $this->width = $utilities->getMediaWidth();
                $this->height = (int) (($this->width*9)/16);

                $id = plgHwdmediashareRemote_vevocom::parse($item->source);
                ob_start();
                ?>
                <object width="<?php echo $this->width; ?>" height="<?php echo $this->height; ?>"><param name="movie" value="http://videoplayer.vevo.com/embed/Embedded?videoId=GB1101200977&playlist=false&autoplay=0&playerId=62FF0A5C-0D9E-4AC1-AF04-1D9E97EE3961&playerType=embedded&env=0&cultureName=en-US&cultureIsRTL=False"></param><param name="wmode" value="transparent"></param><param name="bgcolor" value="#000000"></param><param name="allowFullScreen" value="true"></param><param name="allowScriptAccess" value="always"></param><embed src="http://videoplayer.vevo.com/embed/Embedded?videoId=<?php echo $id; ?>&playlist=false&autoplay=0&playerId=62FF0A5C-0D9E-4AC1-AF04-1D9E97EE3961 &playerType=embedded&env=0&cultureName=en-US&cultureIsRTL=False" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="<?php echo $this->width; ?>" height="<?php echo $this->height; ?>" bgcolor="#000000" wmode="transparent"></embed></object>
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
		$pos_u = strpos($url, "watch/");
		$code = array();

		if ($pos_u === false)
		{
			return null;
		}
		else if ($pos_u)
		{

		$pos_start = $pos_u + 6;
		$length = strlen($url) - strlen($pos_start);
		$temp = substr($url, $pos_start, $length);

        $pos_v = strpos($temp, "/");
        $pos_start = $pos_v + 1;
        $length = strlen($temp) - strlen($pos_start);
		$temp2 = substr($temp, $pos_start, $length);

        $pos_w = strpos($temp2, "/");
		$pos_start = $pos_w + 1;
		$length = strlen($temp2) - strlen($pos_start);
		$code = substr($temp2, $pos_start, $length);

	   	$code = strip_tags($code);
		$code = preg_replace("/[^a-zA-Z0-9s]/", "", $code);
		}
		return $code;
        }
}