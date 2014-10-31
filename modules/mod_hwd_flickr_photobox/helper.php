<?php
/**
 * @package     Joomla.site
 * @subpackage  Module.mod_hwd_flickr_photobox
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class modHwdFlickrPhotoBoxHelper extends JObject
{
        /**
	 * Class constructor.
	 *
	 * @access  public
	 * @param   array   $module  The module object.
	 * @param   array   $params  The module parameters object.
         * @return  void
	 */       
	public function __construct($module, $params)
	{                
                // Load caching.
                $cache = JFactory::getCache('mod_hwd_youtube_videobox');
                $cache->setCaching(1);

                // Get data.              
                $this->module = $module;                
                $this->params = $params;                
                $this->items = $cache->call(array($this, 'getItems'), $params);

                // Add assets to the head tag.
                $this->addHead();  
	}

        /**
	 * Method to add assets to the head.
	 *
	 * @access  public
         * @return  void
	 */         
	public function addHead()
	{           
                JHtml::_('bootstrap.tooltip');
                $doc = JFactory::getDocument();
                $doc->addScript(JURI::root() . 'modules/mod_hwd_flickr_photobox/js/jquery.magnific-popup.js');
                $doc->addStylesheet(JURI::root() . 'modules/mod_hwd_flickr_photobox/css/magnific-popup.css');
                $doc->addStylesheet(JURI::root() . 'modules/mod_hwd_flickr_photobox/css/strapped.3.hwd.css');
                
                // Extract the layout.
                list($template, $layout) = explode(':', $this->params->get('layout', '_:default'));
                
                // Check for layout stylesheet.
                if (file_exists(__DIR__ . '/css/' . $layout . '.css'))
                {
                        $doc->addStyleSheet(JURI::base( true ) . '/modules/mod_hwd_flickr_photobox/css/' . $layout . '.css');
                }

                $doc->addScriptDeclaration("
jQuery.noConflict();
(function( $ ) {
  $(function() {
    $(document).ready(function() {
      $('.popup-title-" . $this->module->id . "').magnificPopup({ 
        type: 'image',
        mainClass: 'hwd-flickr-popup',
        gallery: {
          enabled: true
        }
      }); 
      $('.popup-thumbnail-" . $this->module->id . "').magnificPopup({ 
        type: 'image',
        mainClass: 'hwd-flickr-popup',
        gallery: {
          enabled: true
        }
      });       
    });
  });
})(jQuery);");                
	}

        /**
	 * Method to get a list of media items.
	 *
	 * @access  public
         * @return  object  A list of media items.
	 */          
	public function getItems()
	{
                $feed = $this->getFeed();
                
                $media = 'http://search.yahoo.com/mrss/';
                $flickr = 'urn:flickr:user';
                
                // Only define the correct number of items.
                $counter = 0;
                $items = array();

                $data = new DOMDocument();
                if ($data->load($feed))
                {       
                        foreach ($data->getElementsByTagName('entry') as $video)
                        {
                                $obj                    = new stdClass();
                              
                                $obj->title             = ($video->getElementsByTagName('title')->item(0) ? $video->getElementsByTagName('title')->item(0)->nodeValue : '');                                
                                $obj->id                = ($video->getElementsByTagName('id')->item(0) ? $video->getElementsByTagName('id')->item(0)->nodeValue : '');                              
                                $obj->thumbnail         = ($video->getElementsByTagName('content')->item(0) ? $video->getElementsByTagName('content')->item(0)->nodeValue : '');
                                $obj->description       = '';
                                $obj->created           = ($video->getElementsByTagNameNS($flickr,'date_taken')->item(0) ? $video->getElementsByTagNameNS($flickr,'date_taken')->item(0)->nodeValue : '');

                                // Redefine ID                    
                                $pos = strpos($obj->id, '/photo/');
                                $pos_start = $pos + 7;
                                $obj->id                = substr($obj->id, $pos_start, strlen($obj->id));  

                                // Define the media link (but don't include if the link points to the license page)
                                $obj->media             = ($video->getElementsByTagName('link')->item(1) ? $video->getElementsByTagName('link')->item(1)->getAttribute('href') : '');                     
                                if (strpos($obj->media, 'creativecommons') !== false) continue;

                                // Define the thumbnail
                                $pos = strpos($obj->thumbnail, '<img src="');
                                $pos_start = $pos + 10;
                                $pos_end = strpos($obj->thumbnail, '.jpg')+4;
                                $length = $pos_end - $pos_start;
                                $obj->thumbnail         = substr($obj->thumbnail, $pos_start, $length);  
                                
                                // We probably want a slightly larger thumbnail to avoid pixelation, so lets do that
                                // http://www.flickr.com/services/api/misc.urls.html
                                $obj->thumbnail = str_replace("_m.jpg", "_n.jpg", $obj->thumbnail);
                                
                                $items[] = $obj;
                                
                                $counter++;                                
                                if ($counter >= $this->get('params')->get('count')) break;
                        }
                }
                
		return $items;                              
	}

        /**
	 * Method to load the correct feed based on configurtion.
         * 
         * @access  public
         * @return  string  The URL of the feed.
	 **/
	public function getFeed()
	{  
                // Set a default feed.
                $feed = 'http://api.flickr.com/services/feeds/photos_public.gne';                                  
  
                switch ($this->get('params')->get('source'))
                {
                    case 'recent_photos':
                        $feed = 'http://api.flickr.com/services/feeds/photos_public.gne';
                        break;
                    case 'user_photos':
                        if ($this->get('params')->get('flickruser')) return 'http://api.flickr.com/services/feeds/photos_public.gne?id='.$this->nameToId($this->get('params')->get('flickruser'));
                        break;  
                    case 'group_photos':
                        if ($this->get('params')->get('flickrgroup')) return 'http://api.flickr.com/services/feeds/groups_pool.gne?id='.$this->nameToId($this->get('params')->get('flickrgroup'));
                        break;  
                    case 'friends_photos':
                        if ($this->get('params')->get('flickruser')) return 'http://api.flickr.com/services/feeds/photos_friends.gne?user_id='.$this->nameToId($this->get('params')->get('flickruser'));
                        break; 
                    case 'keyword_photos':
                        if ($this->get('params')->get('keywords')) return 'http://api.flickr.com/services/feeds/photos_public.gne?tags='.$this->get('params')->get('keywords');
                        break;
                    case 'set_photos':
                        if ($this->get('params')->get('flickrset')) return 'http://api.flickr.com/services/feeds/photoset.gne?set='.$this->get('params')->get('flickrset').'&nsid='.$this->nameToId($this->get('params')->get('flickruser'));
                        break;    
                }
                
                return $feed;
	}
        
        /**
	 * Method to convert a Flickr username to a Flickr user ID.
         * 
         * @access  public
         * @param   string  $name  The Flickr username.
         * @return  string  The Flickr user ID.
	 **/
	public function nameToId($name)
        {
                // If the username already contains an ampersand, then we don't need to process. 
                $pos = strpos($name, '@');
                if ($pos !== false)
                {
                        return $name;
                }
                
                $url = 'http://idgettr.com';
                switch ($this->get('params')->get('source'))
                {
                        case 'user_photos':
                        case 'friend_photos':
                            $data = array('photostream' => 'http://www.flickr.com/photos/'.$name);
                            break;
                        case 'group_photos':
                            $data = array('photostream' => 'https://www.flickr.com/groups/'.$name);
                            break;
                        case 'set_photos':
                            $data = array('photostream' => 'https://www.flickr.com/photos/'.$name.'/sets/'.$this->get('params')->get('flickrset'));
                            break;
                }

                if ($url)
                {
                        if (function_exists('curl_init'))
                        {
                                $useragent = "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1)";

                                $curl_handle = curl_init();
                                curl_setopt($curl_handle, CURLOPT_URL, $url);
                                curl_setopt($curl_handle, CURLOPT_POST, 1);
                                curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $data);
                                curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 30);
                                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
                                curl_setopt($curl_handle, CURLOPT_REFERER, 'idgettr.com');
                                curl_setopt($curl_handle, CURLOPT_USERAGENT, $useragent);
                                $buffer = curl_exec($curl_handle);
                                curl_close($curl_handle);

                                if (!empty($buffer))
                                {
                                        $pos = strpos($buffer, '<p><strong>id:</strong> ');
                                        if ($pos !== false)
                                        {
                                                $pos_start = $pos + 24;
                                                $pos_end = strpos($buffer, '</p>', $pos_start);
                                                $length = $pos_end - $pos_start;
                                                $id = substr($buffer, $pos_start, $length);
                                                return $id;
                                        }  
                                }
                        }
                }

		return false;
        }
}