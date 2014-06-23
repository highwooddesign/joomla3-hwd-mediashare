<?php
/**
 * @package     Joomla.site
 * @subpackage  Module.mod_hwd_dailymotion_videobox
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class modHwdDailymotionVideoBoxHelper extends JObject
{
	/**
	 * Modal data
	 * @var array
	 */    
	public $params;
	public $module;
	public $url;
	public $container;
        
	public function __construct($module, $params)
	{
                // Load caching.
                $cache = JFactory::getCache();
                $cache->setCaching(1);

                JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');

                // Get data.
                $this->module = $module;                
                $this->params = $params;                
                $this->items = $items = $cache->call(array($this, 'getItems'));
		$this->url = JURI::root().'modules/mod_hwd_dailymotion_videobox/';
		$this->container = 'mod_hwd_dailymotion_videobox'.$module->id;

                // Add assets to the head tag.
                $this->addHead();              
	}

	public function addHead()
	{
                JHtml::_('bootstrap.tooltip');
                $doc = JFactory::getDocument();
                $doc->addScript(JURI::root().'modules/mod_hwd_dailymotion_videobox/js/jquery.magnific-popup.js');
                $doc->addScript(JURI::root().'modules/mod_hwd_dailymotion_videobox/js/aspect.js');
                $doc->addStylesheet(JURI::root().'modules/mod_hwd_dailymotion_videobox/css/magnific-popup.css');
                $doc->addStylesheet(JURI::root().'modules/mod_hwd_dailymotion_videobox/css/strapped.3.hwd.css');
                $doc->addScriptDeclaration("
jQuery.noConflict();
(function( $ ) {
  $(function() {
    $(document).ready(function() {
      $('.popup-title-" . $this->module->id . "').magnificPopup({ 
        type: 'iframe',
        mainClass: 'hwd-dailymotion-popup',
        gallery: {
          enabled: true
        }
      }); 
      $('.popup-thumbnail-" . $this->module->id . "').magnificPopup({ 
        type: 'iframe',
        mainClass: 'hwd-dailymotion-popup',
        gallery: {
          enabled: true
        }
      });       
    });
  });
})(jQuery);");                
	}

	public function getItems()
	{
                JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');
                $feed = $this->getFeed();
                
                $media = 'http://search.yahoo.com/mxml';
                $dm = 'http://www.dailymotion.com/dmxml';
                
                $counter = 0;
                $data = new DOMDocument();
                if ($data->load($feed))
                {       
                        foreach ($data->getElementsByTagName('video') as $video)
                        {
                                $object                    = new stdClass();

                                $object->url               = ($video->getElementsByTagName('url')->item(0) ? $video->getElementsByTagName('url')->item(0)->nodeValue : '');
                                $object->title             = ($video->getElementsByTagName('title')->item(0) ? JHtmlString::truncate($video->getElementsByTagName('title')->item(0)->nodeValue, 100, true, false) : '');
                                $object->id                = ($video->getElementsByTagName('id')->item(0) ? $video->getElementsByTagName('id')->item(0)->nodeValue : '');
                                $object->description       = ($video->getElementsByTagName('description')->item(0) ? JHtmlString::truncate($video->getElementsByTagName('description')->item(0)->nodeValue, 200, true, false) : '');
                                $object->duration          = ($video->getElementsByTagName('duration_formatted')->item(0) ? $video->getElementsByTagName('duration_formatted')->item(0)->nodeValue : '');
                                $object->views             = ($video->getElementsByTagName('views_total')->item(0) ? $video->getElementsByTagName('views_total')->item(0)->nodeValue : '');
                                $object->thumbnail         = ($video->getElementsByTagName('thumbnail_url')->item(0) ? $video->getElementsByTagName('thumbnail_url')->item(0)->nodeValue : '');

                                $buffer = $this->getBuffer($object->url);
                                preg_match('/<meta property="og:image" content="([^"]+)/', $buffer, $match);
                                if (!empty($match[1]))
                                {
                                        $object->thumbnail = $match[1];
                                }

                                // Check data is valid
                                if (!$object->duration) continue;
                                if (!$object->thumbnail) continue;

                                $items[] = $object;
                                $counter++;

                                if ($counter >= $this->get('params')->get('count'))
                                {
                                        break;
                                }
                        }
                }
        
		return $items; 
	}

	public function getBuffer($url)
	{
                // A large number of CURL installations will not support SSL, so switch back to http
                $url = str_replace("https", "http", $url);

                if ($url)
                {
                        if (function_exists('curl_init'))
                        {
                                $useragent = "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1)";

                                $curl_handle = curl_init();
                                curl_setopt($curl_handle, CURLOPT_URL, $url);
                                curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 30);
                                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
                                curl_setopt($curl_handle, CURLOPT_REFERER, 'dailymotion.com');
                                curl_setopt($curl_handle, CURLOPT_USERAGENT, $useragent);
                                $buffer = curl_exec($curl_handle);
                                curl_close($curl_handle);

                                if (!empty($buffer))
                                {
                                        return $buffer;
                                }
                                else
                                {
                                        return false;
                                }
                        }
                }

		return false;
	}

	public function getFeed()
	{  
                // Set a default feed
                $feed = 'http://www.dailymotion.com/xml';

                // Set the ordering
                $order = '/'.$this->get('params')->get('order');
                
                switch ($this->get('params')->get('video_source'))
                {
                    case 'all':
                        $feed = 'http://www.dailymotion.com/xml'.$order;
                        break;
                    case 'featured':
                        $feed = 'http://www.dailymotion.com/xml/featured';
                        break;
                    case 'user':
                        $feed = 'http://www.dailymotion.com/xml/user/'.$this->get('params')->get('user').$order;
                        break;
                    case 'playlist':
                        $feed = 'http://www.dailymotion.com/xml/playlist/'.$this->get('params')->get('playlist').$order;
                        break;
                    case 'group':
                        $feed = 'http://www.dailymotion.com/xml/group/'.$this->get('params')->get('group').$order;
                        break;
                    case 'channel':
                        $feed = 'http://www.dailymotion.com/xml/channel/'.$this->get('params')->get('channel').$order;
                        break;
                    case 'keyword':
                        $feed = 'http://www.dailymotion.com/xml/search/'.$this->get('params')->get('keywords').$order;
                        break;
                    case 'keyword_relavancy':
                        $feed = 'http://www.dailymotion.com/xml/search/'.$this->get('params')->get('keywords').'/relevance';
                        break;
                }             
                    
                return $feed;
	}      
}