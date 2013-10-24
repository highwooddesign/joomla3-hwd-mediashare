<?php
/**
 * @version    $Id: remote_viveco.php 1317 2013-03-20 10:23:09Z dhorsfall $
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
class plgHwdmediashareRemote_vineco extends JObject
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
			$c = 'plgHwdmediashareRemote_vineco';
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
                        $duration = 6;

                        if ($duration > 0)
                        {
                                $this->_duration = $duration;
                                return $this->_duration;
                        }
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
		$plugin =& JPluginHelper::getPlugin('hwdmediashare', 'remote_vineco');
		$params = new JRegistry( @$plugin->params );

                // Load hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                $autoplay = (JRequest::getInt('media_autoplay', $config->get('media_autoplay')) == 1 ? 'true' : 'false');

                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();

                $this->width = $utilities->getMediaWidth();
                $this->height = (int) $config->get('mediaitem_height') ? $config->get('mediaitem_height') : $this->width*$config->get('video_aspect',0.75);
                $this->height = (int) $this->width;

                $id = plgHwdmediashareRemote_vineco::parse($item->source);
                ob_start();
                ?>
                <iframe class="vine-embed" src="https://vine.co/v/<?php echo $id ?>/embed/simple" width="<?php echo $this->width; ?>" height="<?php echo $this->height; ?>" frameborder="0"></iframe><script async src="//platform.vine.co/static/scripts/embed.js" charset="utf-8"></script>                <?php
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
                $pos_u = strpos($url, "v/");
		$code = array();

		if ($pos_u === false)
		{
			return null;
		}
		else if ($pos_u)
		{
			$pos_u_start = $pos_u + 2;
			$pos_u_end = $pos_u_start + 11;

			$length = $pos_u_end - $pos_u_start;
			$code = substr($url, $pos_u_start, $length);
			$code = strip_tags($code);
                        $code = preg_replace("/[^a-zA-Z0-9s]/", "", $code);
		}

		return $code;
        }
}