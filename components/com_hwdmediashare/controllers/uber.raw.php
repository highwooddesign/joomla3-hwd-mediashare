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

class hwdMediaShareControllerUber extends JControllerForm
{
	/**
	 * Function that allows uber upload to communicate with HWD during active uploads
	 * @return  void
	 */
        function link_upload()
        {
                // Get the document object
                $document = JFactory::getDocument();
                            
                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                // Set the MIME type for output
                $document->setMimeEncoding( 'text/javascript' );
    
                hwdMediaShareFactory::load('uber.ubr_link_upload');
                
		JFactory::getApplication()->close();
        }
        
	/**
	 * Function that allows uber upload to communicate with HWD during active uploads
	 * @return  void
	 */
        function set_progress()
        {
                // Get the document object
                $document = JFactory::getDocument();
                            
                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                // Set the MIME type for output
                $document->setMimeEncoding( 'text/javascript' );
    
                hwdMediaShareFactory::load('uber.ubr_set_progress');
                
		JFactory::getApplication()->close();
        }
        
	/**
	 * Function that allows uber upload to communicate with HWD during active uploads
	 * @return  void
	 */
        function get_progress()
        {
                // Get the document object
                $document = JFactory::getDocument();
                            
                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                // Set the MIME type for output
                $document->setMimeEncoding( 'text/javascript' );
    
                hwdMediaShareFactory::load('uber.ubr_get_progress');
                
		JFactory::getApplication()->close();
        }
}
