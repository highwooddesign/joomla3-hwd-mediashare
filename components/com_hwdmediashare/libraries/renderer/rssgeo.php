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

class JDocumentRendererRSSGEO extends JDocumentRenderer
{
	/**
	 * Renderer mime type.
	 *
         * @access  portected
	 * @var     string
	 */
	protected $_mime = "application/rss+xml";

	/**
	 * Render the RSSGEO feed, that implements WGS84 Geo Positioning
	 *
         * @access  public
	 * @param   string  $name     The name of the element to render
	 * @param   array   $params   Array of values
	 * @param   string  $content  Override the output of the renderer
	 * @return  string  The output.
	 */
	public function render($name = 'rssgeo', $params = null, $content = null)
	{
                // Initialise variables.
		$app = JFactory::getApplication();
                $data = $this->_doc;
                
		// Gets and sets timezone offset from site configuration
		$tz = new DateTimeZone($app->getCfg('offset'));
		$now = JFactory::getDate();

                // Define the feed URL.
		$uri = JFactory::getURI();
		$url = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
		$syndicationURL = JRoute::_('&format=feed&type=rssgeo');

                // Define the title.
		$feed_title = htmlspecialchars($data->title, ENT_COMPAT, 'UTF-8');

		$feed = "<rss version=\"2.0\" xmlns:atom=\"http://www.w3.org/2005/Atom\"\n";
		$feed.= "                     xmlns:geo=\"http://www.w3.org/2003/01/geo/wgs84_pos#\"\n";
		$feed.= "                     xmlns:georss=\"http://www.georss.org/georss\"\n";
		$feed.= "                     xmlns:media=\"http://search.yahoo.com/mrss\">\n";
		$feed.= "	<channel>\n";
		$feed.= "		<title>".$feed_title."</title>\n";
		$feed.= "		<description>".$data->description."</description>\n";
		$feed.= "		<link>".str_replace(' ','%20',$url.$data->link)."</link>\n";
		$feed.= "		<lastBuildDate>".htmlspecialchars($now->toRFC822(true), ENT_COMPAT, 'UTF-8')."</lastBuildDate>\n";
		$feed.= "		<generator>".$data->getGenerator()."</generator>\n";
		$feed.= '		<atom:link rel="self" type="application/rss+xml" href="'.str_replace(' ','%20',$url.$syndicationURL)."\"/>\n";

		if ($data->image!=null) {
			$feed.= "		<image>\n";
			$feed.= "			<url>".$data->image->url."</url>\n";
			$feed.= "			<title>".htmlspecialchars($data->image->title, ENT_COMPAT, 'UTF-8')."</title>\n";
			$feed.= "			<link>".str_replace(' ','%20',$data->image->link)."</link>\n";
			if ($data->image->width != "") {
				$feed.= "			<width>".$data->image->width."</width>\n";
			}
			if ($data->image->height!="") {
				$feed.= "			<height>".$data->image->height."</height>\n";
			}
			if ($data->image->description!="") {
				$feed.= "			<description><![CDATA[".$data->image->description."]]></description>\n";
			}
			$feed.= "		</image>\n";
		}
		if ($data->language!="") {
			$feed.= "		<language>".$data->language."</language>\n";
		}
		if ($data->copyright!="") {
			$feed.= "		<copyright>".htmlspecialchars($data->copyright,ENT_COMPAT, 'UTF-8')."</copyright>\n";
		}
		if ($data->editorEmail!="") {
			$feed.= "		<managingEditor>".htmlspecialchars($data->editorEmail, ENT_COMPAT, 'UTF-8').' ('.
				htmlspecialchars($data->editor, ENT_COMPAT, 'UTF-8').")</managingEditor>\n";
		}
		if ($data->webmaster!="") {
			$feed.= "		<webMaster>".htmlspecialchars($data->webmaster, ENT_COMPAT, 'UTF-8')."</webMaster>\n";
		}
		if ($data->pubDate!="") {
			$pubDate = JFactory::getDate($data->pubDate);
			$pubDate->setTimeZone($tz);
			$feed.= "		<pubDate>".htmlspecialchars($pubDate->toRFC822(true), ENT_COMPAT, 'UTF-8')."</pubDate>\n";
		}
		if (empty($data->category) === false) {
			if (is_array($data->category)) {
				foreach ($data->category as $cat) {
					$feed.= "		<category>".htmlspecialchars($cat, ENT_COMPAT, 'UTF-8')."</category>\n";
				}
			}
			else {
				$feed.= "		<category>".htmlspecialchars($data->category, ENT_COMPAT, 'UTF-8')."</category>\n";
			}
		}
		if ($data->docs!="") {
			$feed.= "		<docs>".htmlspecialchars($data->docs, ENT_COMPAT, 'UTF-8')."</docs>\n";
		}
		if ($data->ttl!="") {
			$feed.= "		<ttl>".htmlspecialchars($data->ttl, ENT_COMPAT, 'UTF-8')."</ttl>\n";
		}
		if ($data->rating!="") {
			$feed.= "		<rating>".htmlspecialchars($data->rating, ENT_COMPAT, 'UTF-8')."</rating>\n";
		}
		if ($data->skipHours!="") {
			$feed.= "		<skipHours>".htmlspecialchars($data->skipHours, ENT_COMPAT, 'UTF-8')."</skipHours>\n";
		}
		if ($data->skipDays!="") {
			$feed.= "		<skipDays>".htmlspecialchars($data->skipDays, ENT_COMPAT, 'UTF-8')."</skipDays>\n";
		}

		for ($i=0, $count = count($data->items); $i < $count; $i++)
		{
			if ((strpos($data->items[$i]->link, 'http://') === false) and (strpos($data->items[$i]->link, 'https://') === false)) {
				$data->items[$i]->link = str_replace(' ','%20',$url.$data->items[$i]->link);
			}
			$feed.= "		<item>\n";
			$feed.= "			<title>".htmlspecialchars(strip_tags($data->items[$i]->title), ENT_COMPAT, 'UTF-8')."</title>\n";
			$feed.= "			<link>".str_replace(' ','%20',$data->items[$i]->link)."</link>\n";

			if (empty($data->items[$i]->guid) === true) {
				$feed.= "			<guid isPermaLink=\"true\">".str_replace(' ','%20',$data->items[$i]->link)."</guid>\n";
			}
			else {
				$feed.= "			<guid isPermaLink=\"false\">".htmlspecialchars($data->items[$i]->guid, ENT_COMPAT, 'UTF-8')."</guid>\n";
			}

			$feed.= "			<description><![CDATA[".$this->_relToAbs($data->items[$i]->description)."]]></description>\n";

			if ($data->items[$i]->authorEmail!="") {
				$feed.= "			<author>".htmlspecialchars($data->items[$i]->authorEmail . ' (' .
										$data->items[$i]->author . ')', ENT_COMPAT, 'UTF-8')."</author>\n";
			}
			/*
			// On hold
			if ($data->items[$i]->source!="") {
					$data.= "			<source>".htmlspecialchars($data->items[$i]->source, ENT_COMPAT, 'UTF-8')."</source>\n";
			}
			*/
			if (empty($data->items[$i]->category) === false) {
				if (is_array($data->items[$i]->category)) {
					foreach ($data->items[$i]->category as $cat) {
						$feed.= "			<category>".htmlspecialchars($cat, ENT_COMPAT, 'UTF-8')."</category>\n";
					}
				}
				else {
					$feed.= "			<category>".htmlspecialchars($data->items[$i]->category, ENT_COMPAT, 'UTF-8')."</category>\n";
				}
			}
			if ($data->items[$i]->comments!="") {
				$feed.= "			<comments>".htmlspecialchars($data->items[$i]->comments, ENT_COMPAT, 'UTF-8')."</comments>\n";
			}
			if ($data->items[$i]->date!="") {
				$itemDate = JFactory::getDate($data->items[$i]->date);
				$itemDate->setTimeZone($tz);
				$feed.= "			<pubDate>".htmlspecialchars($itemDate->toRFC822(true), ENT_COMPAT, 'UTF-8')."</pubDate>\n";
			}

                        if (count($data->items[$i]->mediafiles))
			{
                                hwdMediaShareFactory::load('files');

                                foreach ($data->items[$i]->mediafiles as $file)
                                {
                                        if ($file = hwdMediaShareFiles::getFileData($data->items[$i]->_media, $file->file_type))
                                        {
                                                $feed.= '			<media:content ';
                                                $feed.= 'url = "' . $file->url . '" ';
                                                $feed.= 'type = "' . $file->type . '" ';
                                                $feed.= 'medium = "image" ';
                                                $feed.= 'duration = "10">';
                                                $feed.= "</media:content>\n";  
                                        }        
                                }
			}

                        $geocode = file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.urlencode($data->items[$i]->location).'&sensor=false');
                        $output = json_decode($geocode);
                        $lat = (isset($output->results[0]->geometry->location->lat) ? $output->results[0]->geometry->location->lat : null);
                        $long = (isset($output->results[0]->geometry->location->lng) ? $output->results[0]->geometry->location->lng : null);
                        if ($lat && $long) 
                        {
                                $feed.= "			<georss:point>$lat $long</georss:point>\n";
                                $feed.= "			<geo:lat>$lat</geo:lat>\n";
                                $feed.= "			<geo:long>$long</geo:long>\n";
                        }
                        
			$feed.= "		</item>\n";
		}
		$feed.= "	</channel>\n";
		$feed.= "</rss>\n";
		return $feed;
	}

	/**
	 * Convert links in a text from relative to absolute
	 *
         * @access  public
	 * @param   string  $text  The text to be processed.
	 * @return  string  Text with converted links.
	 */
	public function _relToAbs($text)
	{
		$base = JURI::base();
		$text = preg_replace("/(href|src)=\"(?!http|ftp|https|mailto|data)([^\"]*)\"/", "$1=\"$base\$2\"", $text);

		return $text;
	}
}
