<?php
/**
 * @version    SVN $Id: downloads.php 1606 2013-06-25 12:47:40Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      06-Dec-2011 14:26:40
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * hwdMediaShare framework downloads class
 *
 * @package hwdMediaShare
 * @since   0.1
 */
class hwdMediaShareDownloads
{
	/**
	 * Class constructor.
	 *
	 * @param   array  $config  A configuration array including optional elements.
	 *
	 * @since   0.1
	 */
	public function __construct($config = array())
	{
	}

	/**
	 * Returns the hwdMediaShareDownloads object, only creating it if it
	 * doesn't already exist.
	 *
	 * @return  hwdMediaShareDownloads A hwdMediaShareDownloads object.
	 * @since   0.1
	 */
	public static function getInstance()
	{
		static $instance;

		if (!isset ($instance))
                {
			$c = 'hwdMediaShareDownloads';
                        $instance = new $c;
		}

		return $instance;
	}
        
	/**
	 * Method to push deliver a file using php
         * 
         * @since   0.1
	 **/
	public function push()
	{
                // Create a new query object.
                $db = JFactory::getDBO();
                $app =& JFactory::getApplication();

                $mediaId = JRequest::getInt( 'id' , '' );
                $elementId = JRequest::getInt( 'element_id' , '1' );
                $fileType = JRequest::getInt( 'file_type' , '' );
                $time = JRequest::getCmd( 'time' , '' );
                $expire = JRequest::getCmd( 'expire' , '' );

                $timeIsValid = (md5($app->getCfg('secret') . $time) == $expire ? true : false);

                // Die gracefully if url has expired
                // @TODO: UI message?
                if (!$timeIsValid || (time() - $time > 1800))
                {
                    jexit();
                }

                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');               

                switch ($elementId)
                {
                        case 1:
                            // Media
                            $table =& JTable::getInstance('Media', 'hwdMediaShareTable');
                            break;
                        case 2:
                            // Album
                            $table =& JTable::getInstance('Album', 'hwdMediaShareTable');
                            break;
                        case 3:
                            // Group
                            $table =& JTable::getInstance('Group', 'hwdMediaShareTable');
                            break;
                        case 4:
                            // Playlist
                            $table =& JTable::getInstance('Playlist', 'hwdMediaShareTable');
                            break;
                        case 5:
                            // Channel
                            $table =& JTable::getInstance('UserChannel', 'hwdMediaShareTable');
                            break;
                }  
                        
                $table->load( $mediaId );
                $properties = $table->getProperties(1);
                $media = JArrayHelper::toObject($properties, 'JObject');          
          
                hwdMediaShareFactory::load('files');
                hwdMediaShareFiles::getLocalStoragePath();
                $hwdmsFiles = hwdMediaShareFiles::getInstance();
                $files = $hwdmsFiles->getMediaFiles($media);

                $folders = hwdMediaShareFiles::getFolders($media->key);
                $filename = hwdMediaShareFiles::getFilename($media->key, $fileType);
                $ext = hwdMediaShareFiles::getExtension($media, $fileType);

                $path = hwdMediaShareFiles::getPath($folders, $filename, $ext);
                if (!file_Exists($path) || filesize($path) == 0)
                {
                        // If trying to get an image, check for custom thumbnail
                        if (in_array($fileType, array(2,3,4,5,6,7)))
                        {
                                hwdMediaShareFactory::load('images');

                                $folders = hwdMediaShareFiles::getFolders($media->key);
                                $filename = hwdMediaShareFiles::getFilename($media->key, 1);
                                $ext = hwdMediaShareFiles::getExtension($media, 1);
                                $path = hwdMediaShareFiles::getPath($folders, $filename, $ext);

                                // @TODO: Write a format cross check method to deliver fallback content
                                if (!file_Exists($path) || filesize($path) == 0 || !hwdMediaShareImages::isNativeImage($ext))
                                {
                                        jexit('COM_HWDMS_ERROR_CAN_NOT_FIND_MEDIA_FILE');
                                }
                        }
		}
               
                // Transfer file in chunks to preserve memory on the server
                $fileTitle = $media->alias.".".$ext;

                $type = 'application/octet-stream';
                
                switch ($fileType) {
                    case 11:
                    case 12:
                    case 13:
                        $type = 'video/flv';
                        break;
                    case 14:
                    case 15:
                    case 16:
                    case 17:
                        $type = 'video/mp4';
                        break;
                    case 1:
                        hwdMediaShareFactory::load('files');
                        $ext = hwdMediaShareFiles::getExtension($media, 1);
                        switch ($ext) {
                            case 'ppt':
                                $type = 'application/vnd.ms-powerpoint';
                                break;
                            case 'xls':
                                $type = 'application/vnd.ms-excel';
                                break;
                            case 'xlsx':
                                $type = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                                break;
                            case 'pptx':
                                $type = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
                                break;
                            case 'docx':
                                $type = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
                                break;                            
                        }
                }
                
                header('Content-Type: ' . $type);
                header('Content-Disposition: attachment; filename="' . $fileTitle . '"');
                header('Content-Length: ' . filesize($path));
                hwdMediaShareDownloads::readfile_chunked($path, true);
                jexit();
	}
   
	/**
	 * Method to generate the dynamically delivered php url of a media file
         * 
         * @since   0.1
	 **/
        function protectedUrl($mediaId, $fileType=1, $elementId=1)
        {
                // Create a new query object.
                $db = JFactory::getDBO();
                $app =& JFactory::getApplication();
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                if ($mediaId === false)
		{
			JError::raiseError(404, JText::_('COM_HWDMS_ERROR'));
			return false;
		}

                $width = JRequest::getInt( 'width' , '' );
                $time = time();
                $expire = md5($app->getCfg('secret') . time());
		$width = min($width,$config->get('mediaitem_size'));

                // If trying to get an image, check for optimal size
                if ($width > 0 && in_array($fileType, array(3,4,5,6,7)))
                {
                        if( $width <= 100 )
                        {
                                $fileType = 3;
                        }
                        elseif( $width <= 240 )
                        {
                                $fileType = 4;
                        }
                        elseif( $width <= 500 )
                        {
                                $fileType = 5;
                        }
                        elseif( $width <= 640 )
                        {
                                $fileType = 6;
                        }
                        else
                        {
                                $fileType = 7;
                        }
                }

                return JRoute::_( 'index.php?option=com_hwdmediashare&task=get.file&id=' . $mediaId . '&file_type=' . $fileType . '&time=' . $time . '&expire=' . $expire . '&format=raw&element_id=' . $elementId );
        }
        
	/**
	 * Method to get the static url of a media file
         * 
         * @since   0.1
	 **/
        function publicUrl($media, $fileType=1)
        {
                // Create a new query object.
                $db = JFactory::getDBO();
                $app =& JFactory::getApplication();
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                if (!isset($media->id) || !isset($media->key) || !isset($media->ext_id))
		{
                        return JURI::root( true ).'/media/com_hwdmediashare/assets/images/default-image-'.$config->get('list_thumbnail_size').'.png';
		}

                if ($media->type == 5)
		{
                        if (empty($media->storage)) $media->storage = $config->get('cdn','cdn_amazons3');
                        $pluginClass = 'plgHwdmediashare'.$media->storage;
                        $pluginPath = JPATH_ROOT.'/plugins/hwdmediashare/'.$media->storage.'/'.$media->storage.'.php';

                        // Import hwdMediaShare CDN plugin and get the public resource
                        if (file_exists($pluginPath))
                        {
                                JLoader::register($pluginClass, $pluginPath);
                                $cdn = call_user_func(array($pluginClass, 'getInstance'));
                                return $cdn->publicUrl($media, $fileType);
                        }                  
                }
                
                hwdMediaShareFactory::load('files');
                hwdMediaShareFiles::getLocalStoragePath();
                $hwdmsFiles = hwdMediaShareFiles::getInstance();
                $files = $hwdmsFiles->getMediaFiles($media);

                $folders = hwdMediaShareFiles::getFolders($media->key);
                $filename = hwdMediaShareFiles::getFilename($media->key, $fileType);
                $ext = hwdMediaShareFiles::getExtension($media, $fileType);

                $path = hwdMediaShareFiles::getPath($folders, $filename, $ext);
                
                if (file_exists($path))
                {
                        return hwdMediaShareFiles::getUrl($folders, $filename, $ext);
                }
                else
                {
                        // If trying to get an image, check for other images, then a thumbnail
                        if (in_array($fileType, array(2,3,4,5,6,7)))
                        {
                                // Search for all images, starting the largest
                                // @TODO: Improve media type ordering
                                $fileTypes = array(7,6,5,4,3,2);
                                foreach ($fileTypes as $fileType)
                                {
                                        $filename = hwdMediaShareFiles::getFilename($media->key, $fileType);
                                        $ext = hwdMediaShareFiles::getExtension($media, $fileType);
                                        $path = hwdMediaShareFiles::getPath($folders, $filename, $ext);
                                        if (file_exists($path))
                                        {
                                                return hwdMediaShareDownloads::url($media, $fileType);
                                        }
                                }
                                
                                // Can't find a suitbale image, so just return the thumbnail
                                return hwdMediaShareDownloads::thumbnail($media, 1);
                        }
                        // Otherwise, if the original media is a native image then use that instead
                        else
                        {
                                hwdMediaShareFactory::load('images');

                                $folders = hwdMediaShareFiles::getFolders($media->key);
                                $filename = hwdMediaShareFiles::getFilename($media->key, 1);
                                $ext = hwdMediaShareFiles::getExtension($media, 1);
                                $path = hwdMediaShareFiles::getPath($folders, $filename, $ext);

                                if (file_exists($path) && hwdMediaShareImages::isNativeImage($ext))
                                {
                                        return hwdMediaShareFiles::getUrl($folders, $filename, $ext);
                                }
                                else
                                {
                                        return JURI::root( true ).'/media/com_hwdmediashare/assets/images/default-image-'.$fileType.'.png';
                                }
                        }
		}
        }
        
	/**
	 * Method to get the static url of a media file
         * 
         * @since   0.1
	 **/
        function url($media, $fileType=1)
        {
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();        

                if ($config->get('protect_media') == 1 && $media->type == 1)
                {
                        return hwdMediaShareDownloads::protectedUrl($media->id, $fileType);
                }
                else
                {
                        return hwdMediaShareDownloads::publicUrl($media, $fileType);
                }
        }

        /**
	 * Method to get the static url of a media file
         * 
         * @since   0.1
	 **/
        function flvUrl($media)
        {
                // CDN
                if ($media->type == 5)
		{
                        $hwdms = hwdMediaShareFactory::getInstance();
                        $config = $hwdms->getConfig(); 
                        if (empty($media->storage)) $media->storage = $config->get('cdn','cdn_amazons3');
                        $pluginClass = 'plgHwdmediashare'.$media->storage;
                        $pluginPath = JPATH_ROOT.'/plugins/hwdmediashare/'.$media->storage.'/'.$media->storage.'.php';

                        // Import hwdMediaShare CDN plugin and get the public resource
                        if (file_exists($pluginPath))
                        {
                                JLoader::register($pluginClass, $pluginPath);
                                $cdn = call_user_func(array($pluginClass, 'getInstance'));
                                return $cdn->publicUrl($media, 11);
                        } 
                }  
                
                hwdMediaShareFactory::load('videos');
                return hwdMediaShareVideos::getFlv($media);
        }

        /**
	 * Method to get the static url of a media file
         * 
         * @since   0.1
	 **/
        function mp4Url($media)
        {
                // CDN
                if ($media->type == 5)
		{
                        $hwdms = hwdMediaShareFactory::getInstance();
                        $config = $hwdms->getConfig(); 
                        if (empty($media->storage)) $media->storage = $config->get('cdn','cdn_amazons3');
                        $pluginClass = 'plgHwdmediashare'.$media->storage;
                        $pluginPath = JPATH_ROOT.'/plugins/hwdmediashare/'.$media->storage.'/'.$media->storage.'.php';

                        // Import hwdMediaShare CDN plugin and get the public resource
                        if (file_exists($pluginPath))
                        {
                                JLoader::register($pluginClass, $pluginPath);
                                $cdn = call_user_func(array($pluginClass, 'getInstance'));
                                return $cdn->publicUrl($media, 14);
                        }
                } 
                
                hwdMediaShareFactory::load('videos');
                return hwdMediaShareVideos::getMp4($media);
        } 

        /**
	 * Method to get the static url of a media file
         * 
         * @since   0.1
	 **/
        function webmUrl($media)
        {
                // CDN
                if ($media->type == 5)
		{
                        $hwdms = hwdMediaShareFactory::getInstance();
                        $config = $hwdms->getConfig(); 
                        if (empty($media->storage)) $media->storage = $config->get('cdn','cdn_amazons3');
                        $pluginClass = 'plgHwdmediashare'.$media->storage;
                        $pluginPath = JPATH_ROOT.'/plugins/hwdmediashare/'.$media->storage.'/'.$media->storage.'.php';

                        // Import hwdMediaShare CDN plugin and get the public resource
                        if (file_exists($pluginPath))
                        {
                                JLoader::register($pluginClass, $pluginPath);
                                $cdn = call_user_func(array($pluginClass, 'getInstance'));
                                return $cdn->publicUrl($media, 18);
                        }
                } 
                
                hwdMediaShareFactory::load('videos');
                return hwdMediaShareVideos::getWebm($media);
        } 

        /**
	 * Method to get the static url of a media file
         * 
         * @since   0.1
	 **/
        function oggUrl($media)
        {
                // CDN
                if ($media->type == 5)
		{
                        $hwdms = hwdMediaShareFactory::getInstance();
                        $config = $hwdms->getConfig(); 
                        if (empty($media->storage)) $media->storage = $config->get('cdn','cdn_amazons3');
                        $pluginClass = 'plgHwdmediashare'.$media->storage;
                        $pluginPath = JPATH_ROOT.'/plugins/hwdmediashare/'.$media->storage.'/'.$media->storage.'.php';

                        // Import hwdMediaShare CDN plugin and get the public resource
                        if (file_exists($pluginPath))
                        {
                                JLoader::register($pluginClass, $pluginPath);
                                $cdn = call_user_func(array($pluginClass, 'getInstance'));
                                return $cdn->publicUrl($media, 22);
                        }
                } 
                
                hwdMediaShareFactory::load('videos');
                return hwdMediaShareVideos::getOgg($media);
        } 
  
        /**
	 * Method to get the static url of a media file
         * 
         * @since   0.1
	 **/
        function jpgUrl($media)
        {
                hwdMediaShareFactory::load('images');
                return hwdMediaShareImages::getJpg($media);
        } 
        
	/**
	 * Method to get the thumbnail url of an item
         * 
         * @since   0.1
	 **/
        function thumbnail($media, $elementId=1)
        {
                // Create a new query object.
                $db = JFactory::getDBO();
                $app =& JFactory::getApplication();
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                $limit = JRequest::getInt('limit', $app->getCfg('list_limit'));
                
                // Check for a custom thumbnail on the element item itself
                if ($custom = hwdMediaShareFactory::getElementThumbnail($media, $elementId))
                {
                        return $custom;
                }
                
                // Get element thumbnail
                if ($elementId > 1)
                {                        
                        // Get an instance of the generic media model
                        JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                        $model =& JModelLegacy::getInstance('Media', 'hwdMediaShareModel', array('ignore_request' => true));
                        $model->setState('com_hwdmediashare.media.list.ordering', 'a.created');
                        $model->setState('com_hwdmediashare.media.list.direction', 'DESC');

                        $user = JFactory::getUser();
                        if ((!$user->authorise('core.edit.state', 'com_hwdmediashare')) &&  (!$user->authorise('core.edit', 'com_hwdmediashare')))
                        {
                                // Limit to published for people who can't edit or edit.state.
                                $model->setState('filter.published',	1);
                                $model->setState('filter.status',	1);

                                // Filter by start and end dates.
                                $model->setState('filter.publish_date', true);
                        }
                        else
                        {
                                // Limit to published for people who can't edit or edit.state.
                                $model->setState('filter.published',	array(0,1));
                                $model->setState('filter.status',	1);
                        }
                                                
                        switch ($elementId)
                        {
                                case 2:
                                    // Album
                                    $model->setState('filter.album_id', $media->id);
                                    break;
                                case 3:
                                    // Group
                                    $model->setState('filter.group_id', $media->id);
                                    break;
                                case 4:
                                    // Playlist
                                    $model->setState('filter.playlist_id', $media->id);
                                    break;
                                case 5:
                                    // Channel
                                    hwdMediaShareFactory::load('utilities');
                                    $utilities = hwdMediaShareUtilities::getInstance();
                                    return $utilities->getAvatar($media);
                                    break;
                                case 6:
                                    // Category
                                    $model->setState('filter.category_id', $media->id);
                                    break;
                        }
                        $query = $model->getListQuery();
                        $db->setQuery($query);
                        $elementMedia = $db->loadObject();                       
                        $id = ((count($elementMedia) > 0) ? $elementMedia->id : null);
                        if ($id)
                        {
                                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                                $table =& JTable::getInstance('Media', 'hwdMediaShareTable');
                                $table->load( $id );
                                $properties = $table->getProperties(1);
                                $media = JArrayHelper::toObject($properties, 'JObject');
                        }
                        else
                        {
                                $media = null;
                        }
                }

                // Check for a custom thumbnail after getting the media item
                if ($custom = hwdMediaShareFactory::getElementThumbnail($media, 1))
                {
                        return $custom;
                }
                
                // Check for a remote thumbnail
                if (!empty($media->thumbnail))
                {
                        $media->thumbnail = str_replace("http://i1.ytimg.com", "https://i1.ytimg.com", $media->thumbnail);
                        return $media->thumbnail;
                }                    
                
                if (!isset($media->id) || !isset($media->key) || !isset($media->ext_id))
		{
                        return JURI::root( true ).'/media/com_hwdmediashare/assets/images/default-image-'.$config->get('list_thumbnail_size').'.png';
		}

                if ($media->type == 5)
		{
                        if (empty($media->storage)) $media->storage = $config->get('cdn','cdn_amazons3');
                        $pluginClass = 'plgHwdmediashare'.$media->storage;
                        $pluginPath = JPATH_ROOT.'/plugins/hwdmediashare/'.$media->storage.'/'.$media->storage.'.php';

                        // Import hwdMediaShare CDN plugin and get the public resource for custom thumbnail
                        if (file_exists($pluginPath))
                        {
                                JLoader::register($pluginClass, $pluginPath);
                                $cdn = call_user_func(array($pluginClass, 'getInstance'));
                                // Custom thumbnail
                                if ($custom = $cdn->publicUrl($media, 10)) return $custom;
                                return $cdn->publicUrl($media, $config->get('list_thumbnail_size'));
                        }
                }
                
                hwdMediaShareFactory::load('files');
                hwdMediaShareFiles::getLocalStoragePath();
                $hwdmsFiles = hwdMediaShareFiles::getInstance();
                $files = $hwdmsFiles->getMediaFiles($media);

                $folders = hwdMediaShareFiles::getFolders($media->key);

                // Custom thumbnail (is this still necessary?)
                $filename = hwdMediaShareFiles::getFilename($media->key, 10);
                $ext = hwdMediaShareFiles::getExtension($media, 10);
                $path = hwdMediaShareFiles::getPath($folders, $filename, $ext);
                if (file_exists($path))
                {
                        if ($config->get('protect_media') == 1)
                        {
                                return hwdMediaShareDownloads::protectedUrl($media->id, $config->get('list_thumbnail_size'));
                        }
                        else
                        {
                                return hwdMediaShareFiles::getUrl($folders, $filename, $ext);
                        }
                }
                
                // Preferred thumbnail
                $filename = hwdMediaShareFiles::getFilename($media->key, $config->get('list_thumbnail_size'));
                $ext = hwdMediaShareFiles::getExtension($media, $config->get('list_thumbnail_size'));
                $path = hwdMediaShareFiles::getPath($folders, $filename, $ext);
                if (file_exists($path))
                {
                        if ($config->get('protect_media') == 1)
                        {
                                return hwdMediaShareDownloads::protectedUrl($media->id, $config->get('list_thumbnail_size'));
                        }
                        else
                        {
                                return hwdMediaShareFiles::getUrl($folders, $filename, $ext);
                        }
                }
                else
                {
                        $ext = hwdMediaShareFiles::getExtension($media, 1);
                        hwdMediaShareFactory::load('images');
                        if (hwdMediaShareImages::isNativeImage($ext))
                        {
                                hwdMediaShareFiles::getLocalStoragePath();
                                $hwdmsFiles = hwdMediaShareFiles::getInstance();
                                $files = $hwdmsFiles->getMediaFiles($media);

                                $folders = hwdMediaShareFiles::getFolders($media->key);
                                $filename = hwdMediaShareFiles::getFilename($media->key, 1);
                                $ext = hwdMediaShareFiles::getExtension($media, 1);

                                $path = hwdMediaShareFiles::getPath($folders, $filename, $ext);
                                if (file_exists($path))
                                {
                                        if ($config->get('protect_media') == 1)
                                        {
                                                return hwdMediaShareDownloads::protectedUrl($media->id, 1);
                                        }
                                        else
                                        {
                                                return hwdMediaShareFiles::getUrl($folders, $filename, $ext);
                                        }
                                }
                        }
                        else
                        {
                                $fileTypes = array(4,3,2,5,6);
                                foreach ($fileTypes as $fileType)
                                {
                                        $folders = hwdMediaShareFiles::getFolders($media->key);
                                        $filename = hwdMediaShareFiles::getFilename($media->key, $fileType);
                                        $ext = hwdMediaShareFiles::getExtension($media, $fileType);
                                        $path = hwdMediaShareFiles::getPath($folders, $filename, $ext);
                                        if (file_exists($path))
                                        {
                                                if ($config->get('protect_media') == 1)
                                                {
                                                        return hwdMediaShareDownloads::protectedUrl($media->id, $fileType);
                                                }
                                                else
                                                {
                                                        return hwdMediaShareFiles::getUrl($folders, $filename, $ext);
                                                }
                                        }
                                }
                                return JURI::root( true ).'/media/com_hwdmediashare/assets/images/default-image-'.$config->get('list_thumbnail_size').'.png';
                        }
		}
        }
     
        /**
         * Method to read and deliver the contents of a file in chunks
         * 
         * @param array  $path  string  Path of file
         * @return       $code
         */
        function readfile_chunked($path, $retbytes=true)
        {
                $chunksize = 1*(1024*1024); // how many bytes per chunk
                $buffer = '';
                $cnt =0;

                $handle = fopen($path, 'rb');
                if ($handle === false)
                {
                       return false;
                }

                while (!feof($handle))
                {
                       $buffer = fread($handle, $chunksize);
                       echo $buffer;
                       ob_flush();
                       flush();
                       if ($retbytes)
                       {
                               $cnt += strlen($buffer);
                       }
                }

                $status = fclose($handle);
                if ($retbytes && $status)
                {
                       return $cnt; // return num. bytes delivered like readfile() does.
                }
                return $status;
        }
}
