<?php
/**
 * @version    SVN $Id: view.feed.php 425 2012-06-28 07:48:57Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      06-Jan-2012 15:40:58
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');
jimport('joomla.document.feed.feed');

/**
 * HTML View class for the Content component
 *
 * @package		Joomla.Site
 * @subpackage	com_content
 * @since 1.5
 */
class hwdMediaShareViewMedia extends JViewLegacy {
	function display()
	{
                $app = JFactory::getApplication();
		$doc	= JFactory::getDocument();
                
                hwdMediaShareFactory::load('files');
                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('renderer.xml');
                hwdMediaShareFactory::load('renderer.rssgeo');
                hwdMediaShareFactory::load('renderer.xspf');

                // Initialise variables
		$state		= $this->get('State');
		$rows		= $this->get('Items');

		foreach ($rows as $row)
		{
			// strip html from feed item title
			$title = $this->escape($row->title);
			$title = html_entity_decode($title, ENT_COMPAT, 'UTF-8');

			// Compute the article slug
			$row->slug = $row->alias ? ($row->id . ':' . $row->alias) : $row->id;

                        // load individual item creator class
			$item = new JFeedItem();
			$item->title		= $title;
			$item->link		= JRoute::_(hwdMediaShareHelperRoute::getMediaItemRoute($row->slug));
			$item->description	= '<div class="feed-description">'.$row->description.'</div>';
			$item->date		= ($row->created ? date('r', strtotime($row->created)) : '');
			$item->author		= $row->created_by_alias ? $row->created_by_alias : $row->author;
			$item->location		= $row->location;
			$item->image		= JRoute::_(hwdMediaShareDownloads::thumbnail($row));
                                
                        // @TODO: Cleanup
                        switch ($row->type) 
                        {
                            case 1:  
                            // Local
                            switch ($row->media_type) 
                            {
                                case 1:
                                    // Audio
                                    hwdMediaShareFactory::load('audio');
                                    if ($mp3 = hwdMediaShareAudio::getMp3($row))
                                    {
                                            $item->enclosure->url = $mp3;
                                            $item->enclosure->length = "";
                                            $item->enclosure->type = "audio/mpeg";
                                    }
                                    else if ($ogg = hwdMediaShareAudio::getOgg($row))
                                    {
                                            $item->enclosure->url = $ogg;
                                            $item->enclosure->length = "";
                                            $item->enclosure->type = "audio/ogg";
                                    }
                                    else
                                    {
                                            hwdMediaShareFiles::getLocalStoragePath();

                                            $folders = hwdMediaShareFiles::getFolders($row->key);
                                            $filename = hwdMediaShareFiles::getFilename($row->key, 1);
                                            $ext = hwdMediaShareFiles::getExtension($row, 1);

                                            $path = hwdMediaShareFiles::getPath($folders, $filename, $ext);
                                            if (file_exists($path))
                                            {
                                                    $item->enclosure->url = hwdMediaShareFiles::getUrl($folders, $filename, $ext);
                                                    $item->enclosure->length = "";
                                                    $item->enclosure->type = "";
                                            }
                                    }
                                break;
                                case 4:
                                    // Video
                                    hwdMediaShareFactory::load('videos');
                                    if ($mp4 = hwdMediaShareVideos::getMp4($row))
                                    {
                                            $item->enclosure->url = $mp4;
                                            $item->enclosure->length = "";
                                            $item->enclosure->type = "video/mp4";
                                    }
                                    else if ($flv = hwdMediaShareVideos::getFlv($row))
                                    {
                                            $item->enclosure->url = $flv;
                                            $item->enclosure->length = "";
                                            $item->enclosure->type = "video/flv";
                                    }
                                    else
                                    {
                                            hwdMediaShareFiles::getLocalStoragePath();

                                            $folders = hwdMediaShareFiles::getFolders($row->key);
                                            $filename = hwdMediaShareFiles::getFilename($row->key, 1);
                                            $ext = hwdMediaShareFiles::getExtension($row, 1);

                                            $path = hwdMediaShareFiles::getPath($folders, $filename, $ext);
                                            if (file_exists($path))
                                            {
                                                    $item->enclosure->url = hwdMediaShareFiles::getUrl($folders, $filename, $ext);
                                                    $item->enclosure->length = "";
                                                    $item->enclosure->type = "";
                                            }
                                    }
                            }
                            case 7:                                
                            // Remote
                                $item->enclosure->url = $row->source;
                                $item->enclosure->length = "";
                                $item->enclosure->type = "video/mp4";                                
                        }

                        // Add thumbnail to enclosure if it hasn't been defined yet
			if ($item->enclosure == null)
			{
                                $item->enclosure->url = $item->image;
                                $item->enclosure->length = "";
                                $item->enclosure->type = "image/jpeg";  
			}
                                
			// loads item info into rss array
			$doc->addItem($item);
		}
	}
}
