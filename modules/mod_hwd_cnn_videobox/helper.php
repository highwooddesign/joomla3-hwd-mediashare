<?php
/**
 * @package     Joomla.site
 * @subpackage  Module.mod_hwd_cnn_videobox
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class modHwdCnnVideoBoxHelper extends JObject
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
		$this->url = JURI::root().'modules/mod_hwd_cnn_videobox/';
		$this->container = 'mod_hwd_cnn_videobox'.$module->id;

                // Add assets to the head tag.
                $this->addHead();              
	}

	public function addHead()
	{
                JHtml::_('bootstrap.tooltip');
                $doc = JFactory::getDocument();
                $doc->addScript(JURI::root().'modules/mod_hwd_cnn_videobox/js/jquery.magnific-popup.js');
                $doc->addScript(JURI::root().'modules/mod_hwd_cnn_videobox/js/aspect.js');
                $doc->addStylesheet(JURI::root().'modules/mod_hwd_cnn_videobox/css/magnific-popup.css');
                $doc->addStylesheet(JURI::root().'modules/mod_hwd_cnn_videobox/css/strapped.3.hwd.css');
                $doc->addScriptDeclaration("
jQuery.noConflict();
(function( $ ) {
  $(function() {
    $(document).ready(function() {
      $('.popup-title-" . $this->module->id . "').magnificPopup({ 
        type: 'iframe',
        mainClass: 'hwd-cnn-popup',
        gallery: {
          enabled: true
        }
      }); 
      $('.popup-thumbnail-" . $this->module->id . "').magnificPopup({ 
        type: 'iframe',
        mainClass: 'hwd-cnn-popup',
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
                $feed = $this->getFeed();

                $counter = 0;
                $data = new DOMDocument();
                if ($data->load($feed))
                {
                        foreach ($data->getElementsByTagName('item') as $video)
                        {
                                $object                    = new stdClass();
                                
                                $object->title             = ($video->getElementsByTagName('title')->item(0) ? $video->getElementsByTagName('title')->item(0)->nodeValue : '');
                                $object->description       = ($video->getElementsByTagName('description')->item(0) ? JHtmlString::truncate($video->getElementsByTagName('description')->item(0)->nodeValue, 300, true, false) : '');
                                $object->category          = ($video->getElementsByTagName('category')->item(0) ? $video->getElementsByTagName('category')->item(0)->nodeValue : '');
                                
                                $object->publishedDate        = ($video->getElementsByTagName('pubDate')->item(0) ? $video->getElementsByTagName('pubDate')->item(0)->nodeValue : '');
                                $object->publishedDate        = substr($object->publishedDate, 0, strlen($object->publishedDate)-4);
                                    
                                $object->id                = ($video->getElementsByTagName('guid')->item(0) ? $video->getElementsByTagName('guid')->item(0)->nodeValue : '');
                                $pos = strpos($object->id, "#/video/");
                                $pos_start = $pos + 8;
                                $object->id = substr($object->id, $pos_start, strlen($object->id));

                                // Get thumbnail & duration
                                $urlXml = 'http://edition.cnn.com/video/data/3.0/video/'.$object->id.'/index.xml';
                                $buffer = $this->getBuffer($urlXml);                              

                                $pos_u = strpos($buffer, 'name="640x360');
                                $pos_start = $pos_u + 15; 
                                $pos_end = strpos($buffer, "</image>");
                                $length = $pos_end - $pos_start;
                                $object->thumbnail         = substr($buffer, $pos_start, $length);
                                
                                $pos_u = strpos($buffer, "<length>");
                                $pos_start = $pos_u + 8;                                        
                                $pos_end = strpos($buffer, "</length>");
                                $length = $pos_end - $pos_start;                                    
                                $object->duration          = substr($buffer, $pos_start, $length);     
                                
                                // Check data is valid
                                if (!$object->duration) continue;
                                if (!$object->thumbnail) continue;

                                $items[] = $object;
                                $counter++;

                                if ($counter >= $this->get('params')->get('count', 6))
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
                                curl_setopt($curl_handle, CURLOPT_REFERER, 'cnn.com');
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
                // Only one feed
                $feed = 'http://rss.cnn.com/rss/cnn_freevideo.rss';
                return $feed;
	}

        public function getCategory($category)
        {
                switch ($category)
                {
                        case 'bestoftv':
                            return 'Best of TV';
                        case 'us':
                            return 'U.S.';                    
                        case 'world':
                            return 'World';
                        case 'crime':
                            return 'Crime';
                        case 'education':
                            return 'Education';
                        case 'international':
                            return 'International';
                        case 'sports':
                            return 'Sports';
                        case 'tech':
                            return 'Tech';
                        default: 
                            return $category;
                }           
        }
}