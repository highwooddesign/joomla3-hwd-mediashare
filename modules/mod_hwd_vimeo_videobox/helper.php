<?php
/**
 * @package     Joomla.site
 * @subpackage  Module.mod_hwd_vimeo_videobox
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class modHwdVimeoVideoBoxHelper extends JObject
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
                $cache = JFactory::getCache('mod_hwd_vimeo_videobox');
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
                $doc->addScript(JURI::root() . 'modules/mod_hwd_vimeo_videobox/js/jquery.magnific-popup.js');
                $doc->addScript(JURI::root() . 'modules/mod_hwd_vimeo_videobox/js/aspect.js');
                $doc->addStylesheet(JURI::root() . 'modules/mod_hwd_vimeo_videobox/css/magnific-popup.css');
                $doc->addStylesheet(JURI::root() . 'modules/mod_hwd_vimeo_videobox/css/strapped.3.hwd.css');
                
                // Extract the layout.
                list($template, $layout) = explode(':', $this->params->get('layout', '_:default'));
                
                // Check for layout stylesheet.
                if (file_exists(__DIR__ . '/css/' . $layout . '.css'))
                {
                        $doc->addStyleSheet(JURI::base( true ) . '/modules/mod_hwd_vimeo_videobox/css/' . $layout . '.css');
                }

                $doc->addScriptDeclaration("
jQuery(document).ready(function() {
  jQuery('.popup-title-" . $this->module->id . "').magnificPopup({ 
    type: 'iframe',
    mainClass: 'hwd-vimeo-popup',
    iframe: {     
      patterns: {
        youtube: {
          id: function(url) {        
            return url;
          },
          src: '%id%'
        }
      }
    },
    gallery: {
      enabled: true
    }
  }); 
  jQuery('.popup-thumbnail-" . $this->module->id . "').magnificPopup({ 
    type: 'iframe',
    mainClass: 'hwd-vimeo-popup',
    iframe: {
      patterns: {
        youtube: {
          id: function(url) {        
            return url;
          },
          src: '%id%'
        }
      }
    },
    gallery: {
      enabled: true
    }
  });       
});
");                 
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
                $items = array();
                $counter = 0;

                $media = 'http://search.yahoo.com/mrss/';
                
                $data = new DOMDocument();
                if ($data->load($feed))
                {       
                        foreach ($data->getElementsByTagName('video') as $video)
                        {
                                $obj                    = new stdClass();

                                $obj->title             = ($video->getElementsByTagName('title')->item(0) ? $video->getElementsByTagName('title')->item(0)->nodeValue : '');
                                $obj->id                = ($video->getElementsByTagName('id')->item(0) ? $video->getElementsByTagName('id')->item(0)->nodeValue : '');
                                $obj->thumbnail         = ($video->getElementsByTagName('thumbnail_large')->item(0) ? $video->getElementsByTagName('thumbnail_large')->item(0)->nodeValue : '');                                                              
                                $obj->description	= ($video->getElementsByTagName('description')->item(0) ? JHtml::_('string.truncate', $video->getElementsByTagName('description')->item(0)->nodeValue, 300, true, false) : '');
                                $obj->category          = ($video->getElementsByTagNameNS($media, 'category')->item(0) ? $video->getElementsByTagNameNS($media, 'category')->item(0)->nodeValue : '');
                                $obj->uploadDate        = ($video->getElementsByTagName('upload_date')->item(0) ? $video->getElementsByTagName('upload_date')->item(0)->nodeValue : '');
                                $obj->duration          = ($video->getElementsByTagName('duration')->item(0) ? $video->getElementsByTagName('duration')->item(0)->nodeValue : '');
                                $obj->views             = ($video->getElementsByTagName('stats_number_of_plays')->item(0) ? $video->getElementsByTagName('stats_number_of_plays')->item(0)->nodeValue : '');
                                $obj->tags              = ($video->getElementsByTagName('tags')->item(0) ? $video->getElementsByTagName('tags')->item(0)->nodeValue : '');
                                $obj->userName          = ($video->getElementsByTagName('user_name')->item(0) ? $video->getElementsByTagName('user_name')->item(0)->nodeValue : '');
                                $obj->userPic           = ($video->getElementsByTagName('user_portrait_small')->item(0) ? $video->getElementsByTagName('user_portrait_small')->item(0)->nodeValue : '');
                                $obj->likes             = ($video->getElementsByTagName('stats_number_of_likes')->item(0) ? $video->getElementsByTagName('stats_number_of_likes')->item(0)->nodeValue : '');
                                
                                $items[] = $obj;
                                
                                $counter++;
                                if ($counter >= $this->get('params')->get('count'))
                                {
                                    break;
                                }
                        }                         
                }                        
		return $items;                              
	}
        
        /**
	 * Method to get the feed URI.
	 *
	 * @access  public
         * @return  string  The feed URI.
	 */          
	public function getFeed()
	{  
                // Set a default feed.
                $feed = 'http://vimeo.com/api/v2/channel/staffpicks/videos.xml';

                switch ($this->get('params')->get('source'))
                {
                    case 'staff_picks':
                        $feed = 'http://vimeo.com/api/v2/channel/staffpicks/videos.xml';
                        break;
                    case 'user':
                        $feed = 'http://vimeo.com/api/v2/'.$this->get('params')->get('user').'/videos.xml';
                        break;                    
                    case 'channel':
                        $feed = 'http://vimeo.com/api/v2/channel/'.$this->get('params')->get('channel').'/videos.xml';
                        break;
                    case 'group':
                        $feed = 'http://vimeo.com/api/v2/group/'.$this->get('params')->get('group').'/videos.xml';
                        break;
                }
  
                return $feed;
	}   
        
        /**
	 * Method to convert an integer number of seconds into a timestamp.
	 *
	 * @access  public
         * @param   integer  $seconds  The number of seconds.
         * @return  void
	 */        
        public function secondsToTime($seconds)
        {
                // Extract hours.
                $hours = floor($seconds / (60 * 60));

                // Extract minutes.
                $divisor_for_minutes = $seconds % (60 * 60);
                $minutes = floor($divisor_for_minutes / 60);

                // Extract the remaining seconds.
                $divisor_for_seconds = $divisor_for_minutes % 60;
                $seconds = ceil($divisor_for_seconds);

                // Prepend seconds with zero if necessary.
                if ($seconds < 10)
                {
                        $seconds = '0'.$seconds;
                }

                if ($hours > 0)
                {
                        return "$hours:$minutes:$seconds";
                }
                else
                {
                        return "$minutes:$seconds";
                }
        }        
}