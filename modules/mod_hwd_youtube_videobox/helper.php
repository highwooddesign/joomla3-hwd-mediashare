<?php
/**
 * @version    $Id: helper.php 1103 2013-02-12 14:59:30Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Sam Cummings
 * @since      20-Dec-2012 11:01:24
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class modHwdYoutubeVideoBoxHelper extends JObject
{
	public $params 		= null;
	public $url		= null;

	public function __construct($module, &$params)
	{                
                $this->set('params', $params);
		$this->set('url', JURI::root().'modules/mod_hwd_youtube_videobox/');
	}

	public function addHead()
	{
                $doc = JFactory::getDocument();
                $doc->addScript(JURI::root().'modules/mod_hwd_youtube_videobox/js/aspect.js');
	}

	public function getItems()
	{
                JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');
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
                                $obj->description	= ($video->getElementsByTagName('description')->item(0) ? JHtmlString::truncate($video->getElementsByTagName('description')->item(0)->nodeValue, 300, true, false) : '');
                                $obj->duration          = ($video->getElementsByTagNameNS($yt, 'duration')->item(0) ? $video->getElementsByTagNameNS($yt, 'duration')->item(0)->getAttribute('seconds') : '');
                                $obj->views             = ($video->getElementsByTagNameNS($yt, 'statistics')->item(0) ? $video->getElementsByTagNameNS($yt, 'statistics')->item(0)->getAttribute('viewCount') : '');
                                $obj->category          = ($video->getElementsByTagNameNS($media, 'category')->item(0) ? $video->getElementsByTagNameNS($media, 'category')->item(0)->nodeValue : '');

                                $items[] = $obj;
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
                $feed = 'http://gdata.youtube.com/feeds/api/standardfeeds/top_rated?v=2';
                
                switch ($this->get('params')->get('video_source'))
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
         * Convert number of seconds into hours, minutes and seconds
         * and return an array containing those values
         *
         * @param integer $seconds Number of seconds to parse
         * @return array
         */
        function secondsToTime($seconds, $returnObject = false)
        {
                // Extract hours
                $hours = floor($seconds / (60 * 60));

                // Extract minutes
                $divisor_for_minutes = $seconds % (60 * 60);
                $minutes = floor($divisor_for_minutes / 60);

                // Extract the remaining seconds
                $divisor_for_seconds = $divisor_for_minutes % 60;
                $seconds = ceil($divisor_for_seconds);

                // Return the final array
                $obj = array(
                    "h" => (int) $hours,
                    "m" => (int) $minutes,
                    "s" => (int) $seconds,
                );

                if ($returnObject)
                {
                        return $obj;
                }
                else
                {
                        // Prepent seconds with zero if necessary
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
}