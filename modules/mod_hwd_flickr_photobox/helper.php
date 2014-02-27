<?php
/**
 * @package    HWD.MediaApps
 * @copyright  Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class modHwdFlickrPhotoBoxHelper extends JObject
{
	public $module 		= null;
	public $params 		= null;
	public $url		= null;

	public function __construct($module, $params)
	{                
                $this->set('module', $module);
                $this->set('params', $params);
		$this->set('url', JURI::root().'modules/mod_hwd_flickr_photobox/');
                JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');                
	}

	public function addHead()
	{
                JHtml::_('bootstrap.tooltip');
                $doc = JFactory::getDocument();
                $doc->addScript(JURI::root().'modules/mod_hwd_flickr_photobox/js/jquery.magnific-popup.js');
                $doc->addScript(JURI::root().'modules/mod_hwd_flickr_photobox/js/aspect.js');
                $doc->addStylesheet(JURI::root().'modules/mod_hwd_flickr_photobox/css/magnific-popup.css');
                $doc->addStylesheet(JURI::root().'modules/mod_hwd_flickr_photobox/css/strapped.3.hwd.css'); 
                $doc->addScriptDeclaration("
jQuery.noConflict();
(function( $ ) {
  $(function() {
    $(document).ready(function() {
      $('.popup-title-" . $this->module->id . "').magnificPopup({ 
        type: 'image',
        gallery: {
          enabled: true
        }
      }); 
      $('.popup-thumbnail-" . $this->module->id . "').magnificPopup({ 
        type: 'image',
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
                
                $media = 'http://search.yahoo.com/mrss/';
                $flickr = 'urn:flickr:user';
                
                // Only define the correct number of items
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
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/
	public function getFeed()
	{  
                // Set a default feed
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
         * Get the ID based on the name
         */
        function nameToId($name)
        {
                switch ($this->get('params')->get('video_source'))
                {
                        case 'user_photos':
                        case 'friend_photos':
                            $url = 'http://idgettr.com/?photostream=http://www.flickr.com/photos/'.$name;
                            break;
                        case 'group_photos':
                            $url = 'http://idgettr.com/?photostream=https://www.flickr.com/groups/'.$name;
                            break;
                        case 'set_photos':
                            $url = 'http://idgettr.com/?photostream=https://www.flickr.com/photos/'.$name.'/sets/'.$this->get('params')->get('flickrset');
                            break;
                }

                if ($url)
                {
                        if (function_exists('curl_init'))
                        {
                                $useragent = "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1)";

                                $curl_handle = curl_init();
                                curl_setopt($curl_handle, CURLOPT_URL, $url);
                                curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 30);
                                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
                                curl_setopt($curl_handle, CURLOPT_REFERER, 'idgettr.com');
                                curl_setopt($curl_handle, CURLOPT_USERAGENT, $useragent);
                                $id = curl_exec($curl_handle);
                                curl_close($curl_handle);

                                if (!empty($id))
                                {
                                        $pos = strpos($id, '<p><strong>id:</strong> ');
                                        $pos_start = $pos + 24;
                                        $pos_end = strpos($id, '</p></div>');
                                        $length = $pos_end - $pos_start;
                                        $id = substr($id, $pos_start, $length);
                                        return $id;
                                }
                                else
                                {
                                        return false;
                                }
                        }
                }
                
		return false;
        }
}