<?php
/**
 * @package     Joomla.site
 * @subpackage  Module.mod_hwd_youtube_videobox
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class modHwdYoutubeVideoBoxHelper extends JObject
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
                $doc->addScript(JURI::root() . 'modules/mod_hwd_youtube_videobox/js/jquery.magnific-popup.js');
                $doc->addScript(JURI::root() . 'modules/mod_hwd_youtube_videobox/js/aspect.js');
                $doc->addStylesheet(JURI::root() . 'modules/mod_hwd_youtube_videobox/css/magnific-popup.css');
                $doc->addStylesheet(JURI::root() . 'modules/mod_hwd_youtube_videobox/css/strapped.3.hwd.css');
                
                // Extract the layout.
                list($template, $layout) = explode(':', $this->params->get('layout', '_:default'));
                
                // Check for layout stylesheet.
                if (file_exists(__DIR__ . '/css/' . $layout . '.css'))
                {
                        $doc->addStyleSheet(JURI::base( true ) . '/modules/mod_hwd_youtube_videobox/css/' . $layout . '.css');
                }

                $doc->addScriptDeclaration("
jQuery.noConflict();
(function( $ ) {
  $(function() {
    $(document).ready(function() {
      $('.popup-title-" . $this->module->id . "').magnificPopup({ 
        type: 'iframe',
        mainClass: 'hwd-youtube-popup',
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
      $('.popup-thumbnail-" . $this->module->id . "').magnificPopup({ 
        type: 'iframe',
        mainClass: 'hwd-youtube-popup',
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
  });
})(jQuery);");                
	}

        /**
	 * Method to get a list of media items.
	 *
	 * @access  public
         * @return  object  A list of media items.
	 */          
	public function getItems($dummy)
	{
                $feed = $this->getFeed();

                $yt    = 'http://gdata.youtube.com/schemas/2007';
                $media = 'http://search.yahoo.com/mrss/';
                
                // http://stackoverflow.com/questions/4902288/help-parsing-xml-with-domdocument
                // https://developers.google.com/youtube/2.0/developers_guide_protocol_video_feeds
                
                $data = new DOMDocument();
                if ($data->load($feed.'&max-results='.$this->get('params')->get('count', 5)))
                {       
                        foreach ($data->getElementsByTagName('entry') as $video)
                        {
                                $obj                    = new stdClass();

                                $obj->title             = ($video->getElementsByTagName('title')->item(0) ? $video->getElementsByTagName('title')->item(0)->nodeValue : '');
                                $obj->id                = ($video->getElementsByTagNameNS($yt, 'videoid')->item(0) ? $video->getElementsByTagNameNS($yt, 'videoid')->item(0)->nodeValue : '');
                                $obj->thumbnail         = ($video->getElementsByTagNameNS($media, 'thumbnail')->item(0) ? $video->getElementsByTagNameNS($media, 'thumbnail')->item(2)->getAttribute('url') : '');
                                $obj->description	= ($video->getElementsByTagName('description')->item(0) ? JHtml::_('string.truncate', $video->getElementsByTagName('description')->item(0)->nodeValue, 300, true, false) : '');
                                $obj->duration          = ($video->getElementsByTagNameNS($yt, 'duration')->item(0) ? $video->getElementsByTagNameNS($yt, 'duration')->item(0)->getAttribute('seconds') : '');
                                $obj->views             = ($video->getElementsByTagNameNS($yt, 'statistics')->item(0) ? $video->getElementsByTagNameNS($yt, 'statistics')->item(0)->getAttribute('viewCount') : '');
                                $obj->category          = ($video->getElementsByTagNameNS($media, 'category')->item(0) ? $video->getElementsByTagNameNS($media, 'category')->item(0)->nodeValue : '');

                                $items[] = $obj;
                        }
                }
        
		return $items;                              
	}

        /**
	 * Method to get the feed URI.
	 *
	 * @access  public
         * @return  string  The feed URI
	 */         
	public function getFeed()
	{  
                // Set a default feed.
                $feed = 'http://gdata.youtube.com/feeds/api/standardfeeds/top_rated?v=2';
                
                switch ($this->get('params')->get('source', 'standard_list'))
                {
                    case 'standard_list':
                        switch ($this->get('params')->get('standard_list'))
                        {
                            case 'top_rated':
                                return 'http://gdata.youtube.com/feeds/api/standardfeeds/top_rated?v=2';
                                break;
                            case 'top_favorites':
                                return 'http://gdata.youtube.com/feeds/api/standardfeeds/top_favorites?v=2';
                                break;
                            case 'most_shared':
                                return 'http://gdata.youtube.com/feeds/api/standardfeeds/most_shared?v=2';
                                break;
                            case 'most_popular':
                                return 'http://gdata.youtube.com/feeds/api/standardfeeds/most_popular?v=2';
                                break;
                            case 'most_recent':
                                return 'http://gdata.youtube.com/feeds/api/standardfeeds/most_recent?v=2';
                                break;
                            case 'most_discussed':
                                return 'http://gdata.youtube.com/feeds/api/standardfeeds/most_discussed?v=2';
                                break;
                            case 'most_responded':
                                return 'http://gdata.youtube.com/feeds/api/standardfeeds/most_responded?v=2';
                                break;
                            case 'recently_featured':
                                return 'http://gdata.youtube.com/feeds/api/standardfeeds/recently_featured?v=2';
                                break;
                            case 'on_the_web':
                                return 'http://gdata.youtube.com/feeds/api/standardfeeds/on_the_web?v=2';
                                break;
                            case 'most_viewed':
                                return 'http://gdata.youtube.com/feeds/api/standardfeeds/most_viewed?v=2';
                                break;                                                        
                        }                        
                        break;
                    case 'keyword_search':
                        $keywords = explode(" ", $this->get('params')->get('keywords'));                      
                        return 'http://gdata.youtube.com/feeds/api/videos?q='.implode('+',$keywords).'&v=2';
                        break;
                    case 'playlist_videos':
                        if ($this->get('params')->get('ytplaylist')) return 'http://gdata.youtube.com/feeds/api/playlists/'.$this->get('params')->get('ytplaylist').'?v=2';
                        break;
                    case 'user_videos':
                        if ($this->get('params')->get('ytuser')) return 'http://gdata.youtube.com/feeds/api/users/'.$this->get('params')->get('ytuser').'/uploads?v=2';
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

                // Prepent seconds with zero if necessary.
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