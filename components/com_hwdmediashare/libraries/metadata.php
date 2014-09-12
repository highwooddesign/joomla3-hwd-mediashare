<?php
/**
 * @package     Joomla.site
 * @subpackage  Component.hwdmediashare
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class hwdMediaShareMetaData extends JObject
{
	/**
	 * Class constructor.
	 *
	 * @access  public
	 * @param   mixed  $properties  Either and associative array or another
	 *                              object to set the initial properties of the object.
         * @return  void
	 */
	public function __construct($properties = null)
	{
		parent::__construct($properties);
	}

	/**
	 * Returns the hwdMediaShareMetaData object, only creating it if it
	 * doesn't already exist.
	 *
	 * @access  public
         * @static
	 * @return  hwdMediaShareMetaData Object.
	 */
	public static function getInstance()
	{
		static $instance;

		if (!isset ($instance))
                {
			$c = 'hwdMediaShareMetaData';
                        $instance = new $c;
		}

		return $instance;
	}

	/**
	 * Method to insert metadata tags into a mediaitem view.
         * 
	 *
	 * @access  public
         * @param   object  $item  The media object.
	 * @return  void
	 */
	public function addMediaItemTags($item)
	{
                // Initialise variable.
                $app = JFactory::getApplication();
                $doc = JFactory::getDocument();

                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                // Load HWD utilities.
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();

                // Add google canonical link: https://support.google.com/webmasters/answer/139066?hl=en
                $doc->addCustomTag('<link rel="canonical" href="'.hwdMediaShareMedia::getPermalink($item->id).'" />');

                // Although probably redundant, we'll include this too.
                $doc->addCustomTag('<link rel="image_src" href="'.$utilities->relToAbs(JRoute::_(hwdMediaShareThumbnails::thumbnail($item))).'"/>');
                
                // Add Facebook AppId: https://developers.facebook.com/docs/insights
                $doc->addCustomTag('<meta property="fb:app_id" content="'.$config->get('facebook_appid').'"/>');
                
                // Add basic metadata: http://ogp.me/#metadata
                $doc->addCustomTag('<meta property="og:title" content="'.$utilities->escape($item->title).'"/>');
                $doc->addCustomTag('<meta property="og:image" content="'.$utilities->relToAbs(JRoute::_(hwdMediaShareThumbnails::thumbnail($item))).'"/>');
                $doc->addCustomTag('<meta property="og:url" content="'.hwdMediaShareMedia::getPermalink($item->id).'"/>');
                
                // Add optional metadata: http://ogp.me/#optional
                $doc->addCustomTag('<meta property="og:site_name" content="'.$utilities->escape($app->getCfg('sitename')).'"/>');
                $doc->addCustomTag('<meta property="og:description" content="'.$utilities->escape(JHtmlString::truncate($item->description, $config->get('list_desc_truncate'), true, false)).'"/>');

                // Add content specific data.
                switch ($item->type)
                {
                        case 2: // Remote
                                hwdMediaShareFactory::load('remote');
                                $lib = hwdMediaShareRemote::getInstance();
                                $lib->_url = $item->source;
                                $host = $lib->getHost();
                                $remotePluginClass = $lib->getRemotePluginClass($host);
                                $remotePluginPath = $lib->getRemotePluginPath($host);

                                // Import HWD remote plugin.
                                JLoader::register($remotePluginClass, $remotePluginPath);
                                if (class_exists($remotePluginClass))
                                {
                                        $remote = call_user_func(array($remotePluginClass, 'getInstance'));
                                        if (method_exists($remote, 'getDirectDisplayLocation'))
                                        {
                                                $width = 640;
                                                $height = (int) ($width*$config->get('video_aspect',0.75));
                                                $doc->addCustomTag('<meta property="og:type" content="video.movie"/>');
                                                $doc->addCustomTag('<meta property="og:video" content="' . $remote->getDirectDisplayLocation($item) . '"/>');
                                                $doc->addCustomTag('<meta property="og:video:width" content="' . $width . '"/>');
                                                $doc->addCustomTag('<meta property="og:video:height" content="' . $height . '"/>');
                                                $doc->addCustomTag('<meta property="og:video:type" content="application/x-shockwave-flash"/>');
                                        }
                                }
                                return;
                        break;
                }

                switch ($item->media_type)
                {
                        case 1: // Audio
                                hwdMediaShareFactory::load('audio');
                            
                                // Add object type: http://ogp.me/#types
                                $doc->addCustomTag('<meta property="og:type" content="music.song"/>');
                                $doc->addCustomTag('<meta property="og:duration" content="' . (int) $item->duration . '"/>');

                                // Add structuured property: http://ogp.me/#structured
                                if ($mp3 = hwdMediaShareAudio::getMp3($item))
                                {
                                        $doc->addCustomTag('<meta property="og:audio" content="' . $utilities->relToAbs($mp3->url) . '"/>');
                                        $doc->addCustomTag('<meta property="og:audio:secure_url" content="' . $utilities->relToAbs($mp3->url) . '"/>');
                                        $doc->addCustomTag('<meta property="og:audio:type" content="' . $mp3->type . '"/>');
                                }
                                return;
                        break;
                        case 2: // Document
                                return;
                        break;
                        case 3: // Image
                                return;
                        break;
                        case 4: // Video
                                hwdMediaShareFactory::load('videos');
                            
                                // Add object type: http://ogp.me/#types
                                $doc->addCustomTag('<meta property="og:type" content="video.movie"/>');
                                $doc->addCustomTag('<meta property="og:duration" content=""/>');

                                // Add structuured property: http://ogp.me/#structured
                                $doc->addCustomTag('<meta property="og:video" content=""/>');
                                $doc->addCustomTag('<meta property="og:og:video:secure_url" content=""/>');
                                $doc->addCustomTag('<meta property="og:video:type" content="application/x-shockwave-flash"/>');
                                $doc->addCustomTag('<meta property="og:video:width" content="640"/>');
                                $doc->addCustomTag('<meta property="og:video:height" content="' . (int) (640*$config->get('video_aspect',0.75)) . '"/>');
                                return;
                        break;
                }
	}
}
