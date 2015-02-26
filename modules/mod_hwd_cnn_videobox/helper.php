<?php
/**
 * @package     Joomla.site
 * @subpackage  Module.mod_hwd_cnn_videobox
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class modHwdCnnVideoBoxHelper extends JObject
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
                $cache = JFactory::getCache('mod_hwd_cnn_videobox');
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
                $doc->addScript(JURI::root() . 'modules/mod_hwd_cnn_videobox/js/jquery.magnific-popup.js');
                $doc->addScript(JURI::root() . 'modules/mod_hwd_cnn_videobox/js/aspect.js');
                $doc->addStylesheet(JURI::root() . 'modules/mod_hwd_cnn_videobox/css/magnific-popup.css');
                $doc->addStylesheet(JURI::root() . 'modules/mod_hwd_cnn_videobox/css/strapped.3.hwd.css');
                
                // Extract the layout.
                list($template, $layout) = explode(':', $this->params->get('layout', '_:default'));
                
                // Check for layout stylesheet.
                if (file_exists(__DIR__ . '/css/' . $layout . '.css'))
                {
                        $doc->addStyleSheet(JURI::base( true ) . '/modules/mod_hwd_cnn_videobox/css/' . $layout . '.css');
                }

                $doc->addScriptDeclaration("
jQuery(document).ready(function() {
  jQuery('.popup-title-" . $this->module->id . "').magnificPopup({ 
    type: 'iframe',
    mainClass: 'hwd-cnn-popup',
    iframe: {     
      patterns: {
        generic: {
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
    mainClass: 'hwd-cnn-popup',
    iframe: {
      patterns: {
        generic: {
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
                
                $data = new DOMDocument();
                if ($data->load($feed))
                {       
                        foreach ($data->getElementsByTagName('item') as $video)
                        {
                                $object                    = new stdClass();

                                $object->title             = ($video->getElementsByTagName('title')->item(0) ? $video->getElementsByTagName('title')->item(0)->nodeValue : '');
                                $object->id                = ($video->getElementsByTagName('guid')->item(0) ? $video->getElementsByTagName('guid')->item(0)->nodeValue : '');
                                $object->description	   = ($video->getElementsByTagName('description')->item(0) ? JHtml::_('string.truncate', $video->getElementsByTagName('description')->item(0)->nodeValue, 300, true, false) : '');
 
                                // Extract ID.
                                preg_match('#http://www.cnn.com/video/\#/video/(.*)#s', $object->id, $matches);
                                if(isset($matches[1])) 
                                {
                                        $object->id = $matches[1];   
                                        
                                        // Define video details resource.
                                        $url = 'http://edition.cnn.com/video/data/3.0/video/'.$object->id.'/index.xml';
                                        $buffer = file_get_contents($url);
 
                                        // Get thumbnail.
                                        preg_match('#<image height="360" width="640" name="640x360">(.*)<\/image>#siU', $buffer, $matches);
                                        if (!empty($matches[1]))
                                        {
                                                $object->thumbnail = $matches[1];   
                                        }
                
                                        // Get duration.
                                        preg_match('#<length>(.*)<\/length>#siU', $buffer, $matches);
                                        if (!empty($matches[1]))
                                        {
                                                $object->duration = $matches[1];   
                                        }   
                                }

                                // Check data is valid
                                if (!$object->duration) continue;
                                if (!$object->thumbnail) continue;

                                $items[] = $object;
                                
                                $counter++;
                                if ($counter >= $this->params->get('count', 6))
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
         * @return  string  The feed URI
	 */         
	public function getFeed()
	{  
                return 'http://rss.cnn.com/rss/cnn_freevideo.rss';
	}
}