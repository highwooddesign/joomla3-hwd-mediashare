<?php
/**
 * @version    SVN $Id: get.raw.php 308 2012-04-08 16:48:20Z dhorsfall $
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
        
	/**
	 * Method to render the display html of a media item
	 * @since	0.1
	 */
        function html()
        {
                $id = JRequest::getInt( 'id' , '' );
                if ($id > 0)
                {
                        // Load group
                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                        $table =& JTable::getInstance('Media', 'hwdMediaShareTable');
                        $table->load( $id );

                        $properties = $table->getProperties(1);
                        $row = JArrayHelper::toObject($properties, 'JObject');
                }
                else
                {
			return;
                }
                
                hwdMediaShareFactory::load('media');
                hwdMediaShareFactory::load('downloads');

                $row->media_type = hwdMediaShareMedia::loadMediaType($row);

                $html = hwdMediaShareMedia::get($row);
                print $html;
                
                // Exit the application.
                return;
        }
        
	/**
	 * Method to render the display html of a media item
	 * @since	0.1
	 */
        function unsubscribe()
        {
                $app = & JFactory::getApplication();

                $model = $this->getModel('User', 'hwdMediaShareModel');
                if ($model->unsubscribe())
                {
                        $retval = array("success" => "1",
                                        "errors" => "0",
                                        "data" => array("error_msg" => $model->getError(),
                                                        "success_msg" => JText::_('COM_HWDMS_SUBSCRIBE')));  
                }
                else 
                {
                        $retval = array("success" => "0",
                                        "errors" => "1",
                                        "data" => array("error_msg" => $model->getError(),
                                                        "success_msg" => JText::_('COM_HWDMS_SUBSCRIBE'))); 
                }
                
                header('Content-type: application/json');
                echo json_encode($retval);
                
                // Exit the application.
                return;
        }
        
	/**
	 * Method to render the display html of a media item
	 * @since	0.1
	 */
        function subscribe()
        {
                $app = & JFactory::getApplication();

                $model = $this->getModel('User', 'hwdMediaShareModel');
                if ($model->subscribe())
                {
                        $retval = array("success" => "1",
                                        "errors" => "0",
                                        "data" => array("error_msg" => $model->getError(),
                                                        "success_msg" => JText::_('COM_HWDMS_UNSUBSCRIBE')));  
                }
                else 
                {
                        $retval = array("success" => "0",
                                        "errors" => "1",
                                        "data" => array("error_msg" => $model->getError(),
                                                        "success_msg" => JText::_('COM_HWDMS_UNSUBSCRIBE'))); 
                }

                header('Content-type: application/json');
                echo json_encode($retval);
                
                // Exit the application.
                return;
        }
        
	/**
	 * Method to render the display html of a media item
	 * @since	0.1
	 */
        function like()
        {
                $app = & JFactory::getApplication();

                $model = $this->getModel('MediaItem', 'hwdMediaShareModel');
                if ($model->like())
                {
                        $retval = array("success" => "1",
                                        "errors" => "0",
                                        "data" => array("error_msg" => $model->getError(),
                                                        "success_msg" => JText::_('COM_HWDMS_LIKED'),
                                                        "likes" => $model->_likes,
                                                        "dislikes" => $model->_dislikes));  
                }
                else 
                {
                        $retval = array("success" => "0",
                                        "errors" => "1",
                                        "data" => array("error_msg" => $model->getError(),
                                                        "success_msg" => JText::_('COM_HWDMS_LIKED'),
                                                        "likes" => $model->_likes,
                                                        "dislikes" => $model->_dislikes)); 
                }

                header('Content-type: application/json');
                echo json_encode($retval);
                
                // Exit the application.
                return;
        }
        
	/**
	 * Method to render the display html of a media item
	 * @since	0.1
	 */
        function dislike()
        {
                $app = & JFactory::getApplication();

                $model = $this->getModel('MediaItem', 'hwdMediaShareModel');
                if ($model->dislike())
                {
                        $retval = array("success" => "1",
                                        "errors" => "0",
                                        "data" => array("error_msg" => $model->getError(),
                                                        "success_msg" => JText::_('COM_HWDMS_DISLIKED'),
                                                        "likes" => $model->_likes,
                                                        "dislikes" => $model->_dislikes));  
                }
                else 
                {
                        $retval = array("success" => "0",
                                        "errors" => "1",
                                        "data" => array("error_msg" => $model->getError(),
                                                        "success_msg" => JText::_('COM_HWDMS_DISLIKED'),
                                                        "likes" => $model->_likes,
                                                        "dislikes" => $model->_dislikes)); 
                }

                header('Content-type: application/json');
                echo json_encode($retval);
                
                // Exit the application.
                return;
        }
        
	/**
	 * Method to render the display html of a media item
	 * @since	0.1
	 */
        function favour()
        {
                $app = & JFactory::getApplication();

                $params = new StdClass;
                $params->elementType = 1;
                $params->elementId = JRequest::getInt('id');
                                
                hwdMediaShareFactory::load('favourites');
                $model = hwdMediaShareFavourites::getInstance();
                if ($model->favour($params))
                {
                        $retval = array("success" => "1",
                                        "errors" => "0",
                                        "data" => array("error_msg" => $model->getError(),
                                                        "success_msg" => JText::_('COM_HWDMS_FAVOURED')));  
                }
                else 
                {
                        $retval = array("success" => "0",
                                        "errors" => "1",
                                        "data" => array("error_msg" => $model->getError(),
                                                        "success_msg" => JText::_('COM_HWDMS_FAVOURED'))); 
                }

                header('Content-type: application/json');
                echo json_encode($retval);
                
                // Exit the application.
                return;
        }
        
	/**
	 * Method to render the display html of a media item
	 * @since	0.1
	 */
        function unfavour()
        {
                $app = & JFactory::getApplication();

                $params = new StdClass;
                $params->elementType = 1;
                $params->elementId = JRequest::getInt('id');
                                
                hwdMediaShareFactory::load('favourites');
                $model = hwdMediaShareFavourites::getInstance();
                if ($model->unfavour($params))
                {
                        $retval = array("success" => "1",
                                        "errors" => "0",
                                        "data" => array("error_msg" => $model->getError(),
                                                        "success_msg" => JText::_('COM_HWDMS_UNFAVOURED')));  
                }
                else 
                {
                        $retval = array("success" => "0",
                                        "errors" => "1",
                                        "data" => array("error_msg" => $model->getError(),
                                                        "success_msg" => JText::_('COM_HWDMS_UNFAVOURED'))); 
                }

                header('Content-type: application/json');
                echo json_encode($retval);
                
                // Exit the application.
                return;
        }
}
