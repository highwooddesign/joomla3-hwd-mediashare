<?php
/**
 * @version    SVN $Id: get.raw.php 269 2012-03-22 10:07:58Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      25-Oct-2011 15:39:20
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * hwdMediaShare Controller
 */
class hwdMediaShareControllerGet extends JControllerForm
{
	/**
	 * Method to get the url of a media file
	 * @since	0.1
	 */
        function url()
        {
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                $mediaId = JRequest::getInt( 'id' , '' );
                $fileType = JRequest::getInt( 'file_type' , '' );
                $width = JRequest::getInt( 'width' , '' );

                hwdMediaShareFactory::load('downloads');
                try
                {
                        if ($config->get('protect_media') == 1)
                        {
                                $url = hwdMediaShareDownloads::protectedUrl($mediaId, $fileType);
                        }
                        else
                        {
                                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                                $table =& JTable::getInstance('Media', 'hwdMediaShareTable');
                                $table->load( $mediaId );
                                $properties = $table->getProperties(1);
                                $media = JArrayHelper::toObject($properties, 'JObject');
                                $url = hwdMediaShareDownloads::publicUrl($media, $fileType);
                        }
                }
                catch(Exception $e)
                {
                        $url = '';
                } 

                $data = array();
                $data['url'] = $url;
                $data['id'] = $mediaId;

                // Get the document object.
                $document =& JFactory::getDocument();

                // Set the MIME type for JSON output.
                $document->setMimeEncoding( 'application/json' );

                // Change the suggested filename.
                JResponse::setHeader( 'Content-Disposition', 'attachment; filename="url'.@$data['id'].'.json"' );

                // Output the JSON data.
                echo json_encode( $data );

                // Exit the application.
                return;
        }
        
	/**
	 * Method to deliver a media file
	 * @since	0.1
	 */
        function file()
        {
                hwdMediaShareFactory::load('files');
                hwdMediaShareFactory::load('downloads');
                $process = hwdMediaShareDownloads::push();

                // Exit the application.
                return;
        }
}
