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

class hwdMediaShareViewMedia extends JViewLegacy
{
	/**
	 * Display the view.
	 *
	 * @access  public
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 * @return  void
	 */
	public function display($tpl = null)
	{
                // Initialise variables
		// $this->document->setTitle('');
                // $this->document->setLink('');
                // $this->document->setDescription('');
                // $this->document->setLanguage('');
                
                // Get data from the model.
                $this->items = $this->get('Items');
		$this->state = $this->get('State');
		$this->params = $this->state->params;
                
                // Register classes.
                JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');

                // Import HWD libraries.                
                hwdMediaShareFactory::load('files');
                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('media');
		hwdMediaShareFactory::load('utilities');
                //hwdMediaShareFactory::load('renderer.xml');
                //hwdMediaShareFactory::load('renderer.rssgeo');
                //hwdMediaShareFactory::load('renderer.xspf');
                
                $this->utilities = hwdMediaShareUtilities::getInstance();
                $this->return = base64_encode(JFactory::getURI()->toString());

                // Process each item.
		foreach ($this->items as $item)
		{
			// Prepare the feed title.
			$title = $this->escape($item->title);
			$title = html_entity_decode($title, ENT_COMPAT, 'UTF-8');

			// Compute the slug.
			$item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;

                        // Load individual feed creator class.
			$feed = new JFeedItem();
                        
                        // Define the feed elements (RSS).
			$feed->title		= $title;
			$feed->link		= JRoute::_(hwdMediaShareHelperRoute::getMediaItemRoute($item->slug));
			$feed->description	= JHtmlString::truncate($item->description, $this->params->get('list_desc_truncate'), false, false);
			$feed->date		= date('r', strtotime($item->created));
			$feed->author		= $item->author;
			$feed->image		= JRoute::_(hwdMediaShareDownloads::thumbnail($item));
                            
                        // Define additional elements (GEO-RSS).                        
			$feed->location		= $item->location;

                        // Define content specific elements.
                        switch ($item->type) 
                        {
                                case 1: // Local
                                        switch ($item->media_type) 
                                        {
                                                case 1: // Audio
                                                        hwdMediaShareFactory::load('audio');
                                                        if ($mp3 = hwdMediaShareAudio::getMp3($item))
                                                        {
                                                                $enclosure = new JFeedEnclosure;
                                                                $enclosure->url = $mp3->url;
                                                                $enclosure->length = $mp3->size;
                                                                $enclosure->type = $mp3->type;
                                                                $feed->setEnclosure($enclosure);
                                                        }
                                                break;
                                                case 3: // Image
                                                        hwdMediaShareFactory::load('images');
                                                        if ($jpg = hwdMediaShareImages::getJpg($item))
                                                        {
                                                                $enclosure = new JFeedEnclosure;
                                                                $enclosure->url = $jpg->url;
                                                                $enclosure->length = $jpg->size;
                                                                $enclosure->type = $jpg->type;
                                                                $feed->setEnclosure($enclosure);
                                                        }
                                                break;                                                
                                                case 4: // Video
                                                        hwdMediaShareFactory::load('videos');
                                                        if ($mp4 = hwdMediaShareVideos::getMp4($item))
                                                        {
                                                                $feed->enclosure->url = $mp4;
                                                                $feed->enclosure->length = "";
                                                                $feed->enclosure->type = "video/mp4";
                                                        }
                                                        elseif ($flv = hwdMediaShareVideos::getFlv($item))
                                                        {
                                                                $feed->enclosure->url = $flv;
                                                                $feed->enclosure->length = "";
                                                                $feed->enclosure->type = "video/flv";
                                                        }
                                                break;
                                        }
                                break;
                        }

                        // If no enclosure set, then add original.
			if ($feed->enclosure == null && $original = hwdMediaShareMedia::getOriginal($item))
			{
                                $enclosure = new JFeedEnclosure;
                                $enclosure->url = $original->url;
                                $enclosure->length = $original->size;
                                $enclosure->type = $original->type;
                                $feed->setEnclosure($enclosure);
			}
                                
			// Add feed item into rss array.
			$this->document->addItem($feed);
		}
	}
}
