<?php
/**
 * @version    SVN $Id: xml.php 425 2012-06-28 07:48:57Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      26-Jan-2012 11:02:01
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * JDocumentRendererXML is a feed that implements XML 1.0 Specification
 *
 * @since       0.1
 */
class JDocumentRendererXML extends JDocumentRenderer
{
	/**
	 * Renderer mime type
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $_mime = "application/rss+xml";

	/**
	 * Render the feed
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	public function render()
	{
		$app	= JFactory::getApplication();

		// Gets and sets timezone offset from site configuration
		$tz	= new DateTimeZone($app->getCfg('offset'));
		$now	= JFactory::getDate();
		$now->setTimeZone($tz);

		$data	= &$this->_doc;

		$uri = JFactory::getURI();
		$url = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
		$syndicationURL = JRoute::_('&format=feed&type=rss');

		if ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $data->title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $data->title, $app->getCfg('sitename'));
		}
		else {
			$title = $data->title;
		}

		$feed_title = htmlspecialchars($title, ENT_COMPAT, 'UTF-8');

		$feed = "<rss version=\"2.0\" xmlns:atom=\"http://www.w3.org/2005/Atom\">\n";
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
			if ($data->items[$i]->enclosure != NULL)
			{
					$feed.= "			<enclosure url=\"";
					$feed.= $data->items[$i]->enclosure->url;
					$feed.= "\" length=\"";
					$feed.= $data->items[$i]->enclosure->length;
					$feed.= "\" type=\"";
					$feed.= $data->items[$i]->enclosure->type;
					$feed.= "\"/>\n";
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
	 * @param   string  $text  The text processed
	 *
	 * @return  string   Text with converted links
	 *
	 * @since   11.1
	 */
	public function _relToAbs($text)
	{
		$base = JURI::base();
		$text = preg_replace("/(href|src)=\"(?!http|ftp|https|mailto|data)([^\"]*)\"/", "$1=\"$base\$2\"", $text);

		return $text;
	}
}
