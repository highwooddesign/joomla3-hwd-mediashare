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
	 * Load assets for the popup alert window
	 * @return  void
	 */
	public static function alert()
	{
		$document = JFactory::getDocument();
                
                // Load jQuery
                JHtml::_('jquery.framework');
                        
                // Load MagnificPopup CSS file
                $document->addStyleSheet(JURI::root() . "media/com_hwdmediashare/assets/css/magnific-popup.css");
                
                // Load MagnificPopup JS file
                $document->addScript(JURI::root() . "media/com_hwdmediashare/assets/javascript/jquery.magnific-popup.js");
                
                $script = "
(function($){
        $(document).ready(function() {
    $('.media-popup-alert').magnificPopup({
      type: 'iframe',
      closeOnBgClick: false,
      mainClass: 'hwd-modal',
      closeOnContentClick: true,
      closeMarkup: '<span title=\"%title%\" class=\"mfp-close\">Dismiss</span>',
      removalDelay: 0,
      callbacks: {
        markupParse: function(template, values, item) {
            template.find('iframe').addClass('bad-site-class');
        }
      }
    });
        });
})(jQuery);
";
                
                // Add the script to the document head.
                $document->addScriptDeclaration($script);

                // Add some style overrides to improve the alert window
                $style = "
.hwd-modal .mfp-content {
  max-width: 600px;
  max-height: 250px;
}
.mfp-iframe-holder span.mfp-close {
  color: #FFFFFF!important;
  padding-right: 6px;
  right: -6px;
  text-align: right;
  font-size: 14px;
  cursor: pointer;
  width: 100%;
  opacity: 1;
}
";

		$document->addStyleDeclaration($style);
	}
        
	/**
	 * Load assets for the popup window to display a full media item
	 * @return  void
	 */
	public static function page()
	{
		$document = JFactory::getDocument();
                
                // Load jQuery
                JHtml::_('jquery.framework');
                        
                // Load MagnificPopup CSS file
                $document->addStyleSheet(JURI::root() . "media/com_hwdmediashare/assets/css/magnific-popup.css");
                
                // Load MagnificPopup JS file
                $document->addScript(JURI::root() . "media/com_hwdmediashare/assets/javascript/jquery.magnific-popup.js");
                
                $script = "
(function($){
        $(document).ready(function() {
    $('.media-popup-page').magnificPopup({
      type: 'iframe',
      mainClass: 'hwd-modal',
      removalDelay: 0,
      callbacks: {
        markupParse: function(template, values, item) {
            template.find('iframe').addClass('bad-site-class');
        }
      }
    });
        });
})(jQuery);
";
                
                // Add the script to the document head.
                $document->addScriptDeclaration($script);
	}
}
