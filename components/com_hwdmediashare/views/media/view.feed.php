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
                hwdMediaShareFactory::load('activities');
                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('files');
                hwdMediaShareFactory::load('media');
                hwdMediaShareFactory::load('thumbnails');
		hwdMediaShareFactory::load('utilities');
                
                hwdMediaShareFactory::load('renderer.mrss');
                hwdMediaShareFactory::load('renderer.rssgeo');
                hwdMediaShareFactory::load('renderer.xml');
                hwdMediaShareFactory::load('renderer.xspf');
                
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
                        
                        // Having access to the original media object in the renderer may be useful.
			$feed->_media		= $item;
                        
                        // Define standard feed attributes.
			$feed->title		= $title;
			$feed->link		= JRoute::_(hwdMediaShareHelperRoute::getMediaItemRoute($item->slug));
			$feed->description	= JHtmlString::truncate($item->description, $this->params->get('list_desc_truncate'), false, false);
			$feed->date		= date('r', strtotime($item->created));
			$feed->author		= $item->author;
			$feed->image		= JRoute::_(hwdMediaShareThumbnails::thumbnail($item));
                            
                        // Define specific feed attributes.

                        // RSS.
                        switch ($item->type) 
                        {
                                case 1: // Local
                                        hwdMediaShareFactory::load('media');
                                        if ($original = hwdMediaShareMedia::getOriginal($item))
                                        {
                                                $enclosure = new JFeedEnclosure;
                                                $enclosure->url = $original->url;
                                                $enclosure->length = $original->size;
                                                $enclosure->type = $original->type;
                                                $feed->setEnclosure($enclosure);
                                        }
                                break;
                        }
                        
                        // GEO-RSS.                        
			$feed->location		= $item->location;

                        // MRSS.
                        hwdMediaShareFactory::load('files');
                        $HWDfiles = hwdMediaShareFiles::getInstance();
                        $feed->mediafiles = $HWDfiles->getMediaFiles($item);                               
                                
			// Add item into feed array.
			$this->document->addItem($feed);
		}
	}
}
