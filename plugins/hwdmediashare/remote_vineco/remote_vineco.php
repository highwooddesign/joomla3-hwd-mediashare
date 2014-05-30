<?php
/**
 * @package     Joomla.site
 * @subpackage  Plugin.hwdmediashare.remote_youtubecom
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class plgHwdmediashareRemote_vineco extends JObject
{
	/**
	 * The remote media type integer: http://hwdmediashare.co.uk/learn/api/68-api-definitions
         * 
         * @access      public
	 * @var         integer
	 */
	public $mediaType = 4;
        
	/**
	 * Library data
	 * @var strings
	 */
        var $_url;
        var $_host;
        var $_buffer;
        var $_title;
        var $_description;
        var $_source;
        var $_duration;
        var $_thumbnail;
        
	/**
	 * Class constructor.
	 *
	 * @access	public
         * @return      void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Returns the plgHwdmediashareRemote_vineco object, only creating it if it
	 * doesn't already exist.
	 *
	 * @access	public
	 * @return      object      The plgHwdmediashareRemote_vineco object.
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
	 * Get the title of the media.
	 *
	 * @access	public
         * @return      void
	 */
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
	 * Get the description of the media.
	 *
	 * @access	public
         * @return      void
	 */
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
	 * Get the source of the media.
	 *
	 * @access	public
         * @return      void
	 */
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
	 * Get the duration of the media.
	 *
	 * @access	public
         * @return      void
	 */
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
	 * Get the thumbnail location of the media.
	 *
	 * @access	public
         * @return      void
	 */
	public function getThumbnail()
	{
                if( !$this->_thumbnail )
		{
                        jimport( 'joomla.filter.filterinput' );
                        JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');

                        // We will apply the most strict filter to the variable.
                        $noHtmlFilter = JFilterInput::getInstance();

                        $thumbnail = false;

                        // Check OpenGraph tag.
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
	 * Request the source, and set to buffer.
	 *
	 * @access	public
         * @return      void
	 */
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
	 * Get the host of the media source.
	 *
	 * @access	public
         * @return      void
	 */
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
	 * Get the url of the media source.
	 *
	 * @access	public
         * @return      void
	 */
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
	 * Render the HTML to display the media.
	 *
	 * @access	public
	 * @param       object      $item       The media item being displayed.
         * @return      void
	 */
	public function display($item)
	{
                // Initialise variables.
                $app = JFactory::getApplication();

                // Load plugin.
		$plugin = JPluginHelper::getPlugin('hwdmediashare', 'remote_vineco');
		
                // Load the language file.
                $lang = JFactory::getLanguage();
                $lang->load('plg_hwdmediashare_remote_vineco', JPATH_SITE . '/administrator');

                if (!$plugin)
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_REMOTE_VINECO_ERROR_NOT_PUBLISHED'));
                        return false;
                }

                // Load parameters.
                $params = new JRegistry($plugin->params);

                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // Load HWD utilities.
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                
                // Get Vine ID
                $id = plgHwdmediashareRemote_vineco::parse($item->source);
                
                $this->autoplay = $app->input->get('media_autoplay', $config->get('media_autoplay'), 'integer') == 1 ? '1' : '0';
                $this->width = '100%';
                $this->height = '100%';
                ob_start();
                ?>
                <div class="media-respond" style="max-width:<?php echo $config->get('mediaitem_size', '500'); ?>px;">
                  <div class="media-aspect" data-aspect="<?php echo $config->get('video_aspect', '0.75'); ?>"></div>
                  <div class="media-content">
                    <iframe class="vine-embed" src="https://vine.co/v/<?php echo $id ?>/embed/simple" width="<?php echo $this->width; ?>" height="<?php echo $this->height; ?>" frameborder="0"></iframe><script async src="//platform.vine.co/static/scripts/embed.js" charset="utf-8"></script>
                  </div>
                </div>
                <?php
                $html = ob_get_contents();
                ob_end_clean();
                
                return $html;
	}

        /**
	 * Parse the source to extract the media.
	 *
	 * @access	public
	 * @param       string      $url        The media url.
         * @return      void
	 */ 
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