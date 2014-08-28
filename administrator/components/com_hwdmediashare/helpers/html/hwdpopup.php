<?php
/**
 * @package     Joomla.administrator
 * @subpackage  Component.hwdmediashare
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

abstract class JHtmlHwdPopup
{
	/**
	 * Method to load assets for the popup window to display an alert.
	 *
	 * @access	public
         * @static 
	 * @return      void
	 */
	public static function alert() 
	{
		$document = JFactory::getDocument();
                
                // Load jQuery.
                JHtml::_('jquery.framework');
                        
                // Load MagnificPopup CSS file.
                $document->addStyleSheet(JURI::root() . "media/com_hwdmediashare/assets/css/magnific-popup.css");
                
                // Load MagnificPopup JS file.
                $document->addScript(JURI::root() . "media/com_hwdmediashare/assets/javascript/jquery.magnific-popup.js");
                
                $script = "
(function($){
  $(document).ready(function() {
    $('.media-popup-alert').magnificPopup({
      type: 'iframe',
      closeOnBgClick: false,
      mainClass: 'mfp-alert',
      closeOnContentClick: true,
      closeMarkup: '<span title=\"%title%\" class=\"mfp-close\">Dismiss</span>',
      removalDelay: 0
    });
  });
})(jQuery);
";
                
                // Add the script to the document head.
                $document->addScriptDeclaration($script);
	}
        
	/**
	 * Method to load assets for the popup window to display an iframe.
	 *
	 * @access	public
         * @static 
	 * @return      void
	 */
	public static function iframe($type, $class = null)
	{
		$document = JFactory::getDocument();
                
                // Load jQuery.
                JHtml::_('jquery.framework');
                        
                // Load MagnificPopup CSS file.
                $document->addStyleSheet(JURI::root() . "media/com_hwdmediashare/assets/css/magnific-popup.css");
                
                // Load MagnificPopup JS file.
                $document->addScript(JURI::root() . "media/com_hwdmediashare/assets/javascript/jquery.magnific-popup.js");
                
                $element = 'media-popup-iframe-' . $type;
                $element.= $class ? '-' . $class : '';

                $mainClass = 'mfp-iframe-' . $type;
                $mainClass.= $class ? '-' . $class : '';
                
                $script = "
(function($){
  $(document).ready(function() {
    $('." . $element . "').magnificPopup({
      type: 'iframe',
      mainClass: '" . $mainClass . "',
      removalDelay: 0,
      iframe: {
        patterns: {
          youtube: {
            index: 'youtube.com/',
            id: null,
            src: '%id%'
          }   
        }
      }        
    });
  });
})(jQuery);
";
                
                // Add the script to the document head.
                $document->addScriptDeclaration($script);
	}

	/**
	 * Method to load assets for the popup window to display an image.
	 *
	 * @access	public
         * @static 
	 * @return      void
	 */
	public static function image($type, $class = null)
	{
		$document = JFactory::getDocument();
                
                // Load jQuery.
                JHtml::_('jquery.framework');
                        
                // Load MagnificPopup CSS file.
                $document->addStyleSheet(JURI::root() . "media/com_hwdmediashare/assets/css/magnific-popup.css");
                
                // Load MagnificPopup JS file.
                $document->addScript(JURI::root() . "media/com_hwdmediashare/assets/javascript/jquery.magnific-popup.js");
                
                $element = 'media-popup-' . $type;
                $element.= $class ? '-' . $class : '';

                $mainClass = 'mfp-' . $type;
                $mainClass.= $class ? '-' . $class : '';

                $script = "
(function($){
  $(document).ready(function() {
    $('." . $element . "').magnificPopup({
      type: 'image',
      mainClass: '" . $mainClass . "',
      removalDelay: 0
    });
  });
})(jQuery);
";
                
                // Add the script to the document head.
                $document->addScriptDeclaration($script);
	}      

	/**
	 * Method to load assets for the popup window to display an image.
	 *
	 * @access	public
         * @static 
	 * @return      void
	 */
	public static function ajax($type, $class = null)
	{
		$document = JFactory::getDocument();
                
                // Load jQuery.
                JHtml::_('jquery.framework');
                        
                // Load MagnificPopup CSS file.
                $document->addStyleSheet(JURI::root() . "media/com_hwdmediashare/assets/css/magnific-popup.css");
                
                // Load MagnificPopup JS file.
                $document->addScript(JURI::root() . "media/com_hwdmediashare/assets/javascript/jquery.magnific-popup.js");
                
                $element = 'media-popup-ajax-' . $type;
                $element.= $class ? '-' . $class : '';

                $mainClass = 'mfp-ajax-' . $type;
                $mainClass.= $class ? '-' . $class : '';

                $script = "
(function($){
  $(document).ready(function() {
    $('." . $element . "').magnificPopup({
      type: 'ajax',
      mainClass: '" . $mainClass . "',
      removalDelay: 0,
      closeOnBgClick: false
    });
  });
})(jQuery);
";
                
                // Add the script to the document head.
                $document->addScriptDeclaration($script);
	}
        
	/**
	 * Write a <a></a> element
	 *
	 * @param   string  $url      The relative URL to use for the href attribute
	 * @param   string  $text     The target attribute to use
	 * @param   array   $attribs  An associative array of attributes to add
	 *
	 * @return  string  <a></a> string
	 *
	 * @since   1.5
	 */
	public static function link($item, $text, $attribs = array())
	{
                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                // Load HWD utilities.
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                
                // Load HWD libraries.
                hwdMediaShareFactory::load('audio');
                hwdMediaShareFactory::load('documents');
                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('remote');
                hwdMediaShareFactory::load('videos');

                // Set an empty URL.
                $url = '';
                
                // Set an empty class.
                $class = '';

                if(!isset($item->media_type) || $item->media_type == 0)
                {
                        $item->media_type = hwdMediaShareMedia::loadMediaType($item);
                }
                        
                switch ($item->type) 
                {
                        case 1: // Local
                        case 4: // RTMP
                        case 7: // Linked file
                                switch ($item->media_type) 
                                {
                                        case 1: // Audio
                                                $url = 'index.php?option=com_hwdmediashare&view=editmedia&layout=modal&tmpl=component&id=' . $item->id;
                                                $class = 'media-popup-ajax-audio';
                                                JHtml::_('HwdPopup.ajax', 'audio'); 
                                        break;
                                        case 2: // Document
                                                $url = 'index.php?option=com_hwdmediashare&view=editmedia&layout=modal&tmpl=component&id=' . $item->id;
                                                $class = 'media-popup-ajax-document';
                                                JHtml::_('HwdPopup.ajax', 'document'); 
                                        break;
                                        case 3: // Image
                                                $url = hwdMediaShareDownloads::url($item);    
                                                $class = 'media-popup-image';
                                                JHtml::_('HwdPopup.image', 'image');                                
                                        break;
                                        case 4: // Video
                                                $url = 'index.php?option=com_hwdmediashare&view=editmedia&layout=modal&tmpl=component&id=' . $item->id;
                                                $class = 'media-popup-ajax-video';
                                                JHtml::_('HwdPopup.ajax', 'video'); 
                                        break;
                                }
                                break;
                        case 2: // Remote
                                $lib = hwdMediaShareRemote::getInstance();
                                $lib->_url = $item->source;
                                $host = $lib->getHost();                       
                                $remotePluginClass = $lib->getRemotePluginClass($host);
                                $remotePluginPath = $lib->getRemotePluginPath($host);
                                $remotePluginHost = preg_replace('/[^a-zA-Z0-9\s]/', '', $host);
                                $type = 0;

                                // Import HWD plugins.
                                JLoader::register($remotePluginClass, $remotePluginPath);
                                if (class_exists($remotePluginClass))
                                {
                                        $HWDremote = call_user_func(array($remotePluginClass, 'getInstance'));
                                        if (method_exists($HWDremote, 'getDirectDisplayLocation'))
                                        {
                                                $url = $HWDremote->getDirectDisplayLocation($item);
                                        }
                                        if ($url && method_exists($HWDremote, 'getDirectDisplayType'))
                                        {
                                                $type = $HWDremote->getDirectDisplayType($item);
                                        }  
                                }
                                else
                                {
                                        $remotePluginClass = $lib->getRemotePluginClass($lib->getDomain());
                                        $remotePluginPath = $lib->getRemotePluginPath($lib->getDomain());
                                        JLoader::register($remotePluginClass, $remotePluginPath);
                                        if (class_exists($remotePluginClass))
                                        {
                                                $HWDremote = call_user_func(array($remotePluginClass, 'getInstance'));
                                                if (method_exists($HWDremote, 'getDirectDisplayLocation'))
                                                {
                                                        $url = $HWDremote->getDirectDisplayLocation($item);
                                                }
                                                if ($url && method_exists($HWDremote, 'getDirectDisplayType'))
                                                {
                                                        $type = $HWDremote->getDirectDisplayType($item);
                                                }  
                                        }                          
                                }
                                switch ($type) 
                                {
                                        case 1: // Audio
                                                $class = 'media-popup-iframe-audio-' . $remotePluginHost;
                                                JHtml::_('HwdPopup.iframe', 'audio', $remotePluginHost);
                                        break; 
                                        case 2: // Document
                                                $class = 'media-popup-iframe-document-' . $remotePluginHost;
                                                JHtml::_('HwdPopup.iframe', 'document', $remotePluginHost);
                                        break; 
                                        case 3: // Image
                                                $class = 'media-popup-iframe-image-' . $remotePluginHost;
                                                JHtml::_('HwdPopup.image', 'image', $remotePluginHost);
                                        break; 
                                        case 4: // Video
                                                $class = 'media-popup-iframe-video-' . $remotePluginHost;
                                                JHtml::_('HwdPopup.iframe', 'video', $remotePluginHost);
                                        break; 
                                }
                                break;
                        case 3: // Embed code
                                $url = 'index.php?option=com_hwdmediashare&view=editmedia&layout=modal&tmpl=component&id=' . $item->id;
                                $class = 'media-popup-ajax-embed';
                                JHtml::_('HwdPopup.ajax', 'embed'); 
                        break;
                        case 6: // Platform
                        break;
                }
        
                // If we still don't have an URL then we use the default modal display.
                if (!$url) 
                {
                        $url = 'index.php?option=com_hwdmediashare&view=editmedia&layout=modal&tmpl=component&id=' . $item->id;
                }
                
                // Define the default class.
                if (!$class) 
                {
                        $class = 'media-popup-ajax-document';
                        JHtml::_('HwdPopup.ajax', 'document'); 
                }

		if (is_array($attribs))
		{
                        !isset($attribs['class']) ? $attribs['class'] = $class : $attribs['class'] .= ' ' . $class;
                        !isset($attribs['title']) ? $attribs['title'] = htmlspecialchars($item->title) : $attribs['title'] .= htmlspecialchars($item->title);

			$attribs = JArrayHelper::toString($attribs);
		}

		return '<a href="' . $url . '" ' . $attribs . '>' . $text . '</a>';
	}        
}
