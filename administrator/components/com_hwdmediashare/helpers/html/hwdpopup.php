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
      mainClass: 'media-popup-alert',
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
	 * Method to load assets for the popup window to display form view.
	 *
	 * @access	public
         * @static 
	 * @return      void
	 */
	public static function form()
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
    $('.media-popup-form').magnificPopup({
      type: 'iframe',
      mainClass: 'media-popup-form',
      removalDelay: 0
    });
  });
})(jQuery);
";

                // Add the script to the document head.
                $document->addScriptDeclaration($script);
	}

	/**
	 * Method to load assets for the popup window to display full page/media view
	 *
	 * @access	public
         * @static 
	 * @return      void
	 */
	public static function page()
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
    $('.media-popup-page').magnificPopup({
      type: 'iframe',
      mainClass: 'media-popup-page',
      removalDelay: 0
    });
  });
})(jQuery);
";
                
                // Add the script to the document head.
                $document->addScriptDeclaration($script);
	}
}
