<?php
/**
 * @version    SVN $Id: files.php 1546 2013-06-11 10:46:54Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * hwdMediaShare framework files class
 *
 * @package hwdMediaShare
 * @since   0.1
 */
class hwdMediaShareFiles
{
	/**
	 * Fileset data array
	 *
	 * @var array
	 */
	protected $_fileset = null;

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
	 * Returns the hwdMediaShareFiles object, only creating it if it
	 * doesn't already exist.
	 *
	 * @return  hwdMediaShareFiles A hwdMediaShareFiles object.
	 * @since   0.1
	 */
	public static function getInstance()
	{
		static $instance;

		if (!isset ($instance))
                {
			$c = 'hwdMediaShareFiles';
                        $instance = new $c;
		}

		return $instance;
	}
        
        /**
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/
	public function add( $media , $fileType = null , $elementType = 1 )
	{
                $date =& JFactory::getDate();
                $user = & JFactory::getUser();
                $db =& JFactory::getDBO();

                $query = "
                      DELETE
                        FROM ".$db->quoteName('#__hwdms_files')."
                        WHERE ".$db->quoteName('element_type')." = ".$db->quote($elementType)."
                        AND ".$db->quoteName('element_id')." = ".$db->quote($media->id)."
                        AND ".$db->quoteName('file_type')." = ".$db->quote($fileType)."
                      ";

                $db->setQuery($query);
                if (!$db->query() && $config->getValue( 'debug' ))
                {
                        $app->enqueueMessage(nl2br($db->getErrorMsg()),'error');
                }

                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                $row =& JTable::getInstance('File', 'hwdMediaShareTable');

                $folders = hwdMediaShareFiles::getFolders($media->key);
                $filename = hwdMediaShareFiles::getFilename($media->key, $fileType);
                $ext = hwdMediaShareFiles::getExtension($media, $fileType);
                $path = hwdMediaShareFiles::getPath($folders, $filename, $ext);

                if (file_exists($path))
                {
                        $post                           = array();
                        $post['element_type']           = $elementType;
                        $post['element_id']             = $media->id;
                        $post['file_type']              = $fileType;
                        $post['basename']               = $filename;
                        $post['ext']                    = $ext;
                        $post['size']                   = intval(filesize($path));
                        $post['checked']                = $date->format('Y-m-d H:i:s');
                        $post['published']              = 1;
                        $post['featured']               = 0;
                        $post['access']                 = 1;
                        $post['download']               = 1;
                        $post['created_user_id']        = $user->id;
                        $post['created_user_id_alias']  = '';
                        $post['created']                = $date->format('Y-m-d H:i:s');
                        $post['publish_up']             = $date->format('Y-m-d H:i:s');
                        $post['publish_down']           = '0000-00-00 00:00:00';
                        $post['hits']                   = 0;
                        $post['language']               = '*';
                        
                        // Bind it to the table
                        if (!$row->bind( $post ))
                        {
                                return JError::raiseWarning( 500, $row->getError() );
                        }

                        // Store it in the db
                        if (!$row->store())
                        {
                                return JError::raiseError(500, $row->getError() );
                        }
                }
                else
                {
                        return JError::raiseError(500, "COM_HWDMS_ERROR_FAILED_TO_ADD_FILE_TO_DATABASE_FILE_DOES_NOT_EXIST" );
                }
	}
        
        /**
	 * Method to define the local storage path
         * 
	 * @since   0.1
	 **/
        function getLocalStoragePath()
        {
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                if (!defined( 'HWDMS_PATH_MEDIA_FILES' ))
                {
                        if ($config->get('use_default_storage_location') == 0)
                        {
                                define('HWDMS_PATH_MEDIA_FILES', $config->get('storage_location', JPATH_ROOT.'/media/com_hwdmediashare/files'));
                        }
                        else
                        {
                                define('HWDMS_PATH_MEDIA_FILES', JPATH_ROOT.'/media/com_hwdmediashare/files');
                        }
                }
                
		if (!defined( 'HWDMS_URL_MEDIA_FILES' ) && !defined('_JCLI'))
                {
                        // Set default relative url
                        $urelative = 'media/com_hwdmediashare/files';
                    
                        if ($config->get('use_default_storage_location') == 0)
                        {
                                // Lets see if the storage location contains our Joomla root path. If it doens't then 
                                // it may be outside the web root, or somewhere else. In this case we won't be able to 
                                // generate the URL
                                $pos = strpos(HWDMS_PATH_MEDIA_FILES, JPATH_ROOT);
                                if ($pos === false) 
                                {
                                        $urelative = 'media/com_hwdmediashare/files';
                                } 
                                else
                                {
                                        $prelative = str_replace(JPATH_ROOT, "", HWDMS_PATH_MEDIA_FILES);
                                        $parts = explode(DIRECTORY_SEPARATOR, $prelative);
                                        if (is_array($parts)) $parts = array_filter($parts);                                               
                                        $urelative = implode('/', $parts);
                                }                                    
                        }
                        else
                        {
                                $urelative = 'media/com_hwdmediashare/files';
                        }  

                        // If a feed, use absolute urls
                        if (JRequest::getWord('format') == 'feed')
                        {
                                define('HWDMS_URL_MEDIA_FILES', JURI::root().$urelative);
                        }    
                        else
                        {
                                define('HWDMS_URL_MEDIA_FILES', JURI::root( true ).'/'.$urelative);
                        }
                }
        }
        
        /**
	 * Method to get recursive storage folder names
         * 
	 * @since   0.1
	 **/
        function getFolders($key)
        {
                $folder[1] = substr($key, 0, 2);
                $folder[2] = substr($key, 2, 2);
                $folder[3] = substr($key, 4, 2);
                return $folder;
        }
        
        /**
	 * Method to check and create storage folders
         * 
	 * @since   0.1
	 **/
        function setupFolders($folder)
        {
		jimport('joomla.filesystem.folder');
                if (!JFolder::create(HWDMS_PATH_MEDIA_FILES.'/'.$folder[1]))
                {
                        return false;
                }
                if (!JFolder::create(HWDMS_PATH_MEDIA_FILES.'/'.$folder[1].'/'.$folder[2]))
                {
                        return false;
                }
                if (!JFolder::create(HWDMS_PATH_MEDIA_FILES.'/'.$folder[1].'/'.$folder[2].'/'.$folder[3]))
                {
                        return false;
                }
                return true;
        }
        
        /**
	 * Method to get filename from media type
         * 
	 * @since   0.1
	 **/
        function getFilename($key, $fileType)
        {
                switch ($fileType) {
                    case 1:
                        return md5($key . 'original');
                    case 2:
                        return md5($key . 'square');
                    case 3:
                        return md5($key . 'thumbnail');
                    case 4:
                        return md5($key . 'small');
                    case 5:
                        return md5($key . 'medium500');
                    case 6:
                        return md5($key . 'medium640');
                    case 7:
                        return md5($key . 'large');
                    case 8:
                        return md5($key . 'mp3');
                    case 9:
                        return md5($key . 'ogg');
                    case 10:
                        return md5($key . 'customthumbnail');
                    case 11:
                        return md5($key . 'flv240');
                    case 12:
                        return md5($key . 'flv360');
                    case 13:
                        return md5($key . 'flv480');
                    case 14:
                        return md5($key . 'mp4360');
                    case 15:
                        return md5($key . 'mp4480');
                    case 16:
                        return md5($key . 'mp4720');
                    case 17:
                        return md5($key . 'mp41080');
                    case 18:
                        return md5($key . 'webm360');
                    case 19:
                        return md5($key . 'webm480');
                    case 20:
                        return md5($key . 'webm720');
                    case 21:
                        return md5($key . 'webm1080');
                    case 22:
                        return md5($key . 'ogg360');
                    case 23:
                        return md5($key . 'ogg480');
                    case 24:
                        return md5($key . 'ogg720');
                    case 25:
                        return md5($key . 'ogg1080');
                    case 26:
                        return md5($key . 'tmp');
                }
        }
        
        /**
	 * Method to get an extension of a media file from file type
         * 
	 * @since   0.1
	 **/
        function getExtension($media, $fileType)
        {
                switch ($fileType) {
                    case 1:
                        if (!$media->ext_id) return null;
                        $db = JFactory::getDBO();
                        $query = $db->getQuery(true);
                        $query->select('ext');
                        $query->from('#__hwdms_ext');
                        $query->where($db->quoteName('id').' = '.$db->quote($media->ext_id));

                        $db->setQuery($query);
                        return $db->loadResult();
                    case 2:
                    case 3:
                    case 4:
                    case 5:
                    case 6:
                    case 7:
                        return 'jpg';
                    case 8:
                        return 'mp3';
                    case 9:
                        return 'ogg';
                    case 10:
                        if (!$media->thumbnail_ext_id) return null;
                        $db = JFactory::getDBO();
                        $query = $db->getQuery(true);
                        $query->select('ext');
                        $query->from('#__hwdms_ext');
                        $query->where($db->quoteName('id').' = '.$db->quote($media->thumbnail_ext_id));

                        $db->setQuery($query);
                        return $db->loadResult();
                    case 11:
                    case 12:
                    case 13:
                        return 'flv';
                    case 14:
                    case 15:
                    case 16:
                    case 17:
                        return 'mp4';
                    case 18:
                    case 19:
                    case 20:
                    case 21:
                        return 'webm';
                    case 22:
                    case 23:
                    case 24:
                    case 25:
                        return 'ogg';
                    case 26:
                        return 'tmp';
                }
        }
        
        /**
	 * Method to get the path of a media file
         * 
	 * @since   0.1
	 **/
        function getPath($folders, $filename, $ext, $abs=true)
        {
                hwdMediaShareFiles::getLocalStoragePath();
                if ($abs)
                {
                        return HWDMS_PATH_MEDIA_FILES . '/' . $folders[1] . '/' . $folders[2] . '/' . $folders[3] . '/' . $filename . '.' . $ext;
                }
                else
                {
                        return $folders[1] . '/' . $folders[2] . '/' . $folders[3] . '/' . $filename . '.' . $ext;
                }
        }

        /**
	 * Method to get the (public) url of a media file
         * 
	 * @since   0.1
	 **/
        function getUrl($folders, $filename, $ext)
        {
                return HWDMS_URL_MEDIA_FILES . '/' . $folders[1] . '/' . $folders[2] . '/' . $folders[3] . '/' . $filename . '.' . $ext;
        }
        
        /**
	 * Method to get all files associated with a media item
         * 
	 * @since   0.1
	 **/
        function getMediaFiles($item)
        {
		// Return false if nothing to do
                if (empty($item->id) || $item->id == 0) return false;
                
                // Initialise variables.
		$pk = $item->id;

		if ($this->_fileset === null) 
                {
			$this->_fileset = array();
		}

		if (!isset($this->_fileset[$pk])) 
                {
			// Check for remote links first
                        if ($item->type == 7)
                        {
                                $object = new stdClass;
                                $object->row = new stdClass;
                                $object->row->ext = 'remote';
                                $object->row->size = 0;
                                $object->row->file_type = 1;                            
				$this->_fileset[$pk] = $object; 
                        }
                        else
                        {
                                try 
                                {
                                        // Create a new query object.
                                        $db = JFactory::getDBO();
                                        $query = $db->getQuery(true);

                                        // Select the required fields from the table.
                                        $query->select('a.id, a.element_id, a.file_type, a.ext, a.size, a.checked');

                                        // From the albums table
                                        $query->from('#__hwdms_files AS a');

                                        // Join over the asset groups.
                                        $query->select('m.key');
                                        $query->join('LEFT', '#__hwdms_media AS m ON m.id = a.element_id');

                                        $query->where($db->quoteName('a.element_type').' = '.$db->quote(1));
                                        $query->where($db->quoteName('a.element_id').' = '.$db->quote($pk));

                                        $db->setQuery($query);
                                        $rows = $db->loadObjectList();

                                        $this->_fileset[$pk] = $rows;       
                                }
                                catch (JException $e)
                                {
                                        if ($e->getCode() == 404) {
                                                // Need to go thru the error handler to allow Redirect to work.
                                                JError::raiseError(404, $e->getMessage());
                                        }
                                        else {
                                                $this->setError($e);
                                                $this->_fileset[$pk] = false;
                                        }
                                }
                        }
		}

		return $this->_fileset[$pk];
        }
        
        /**
	 * Method to get human readable name of a file type.
         * 
	 * @since   0.1
	 **/
        function getFileType($item)
        {
                switch ($item->file_type) {
                    case 1:
                        return JText::_('COM_HWDMS_FILE_ORIGINAL_MEDIA');
                        break;
                    case 2:
                        return JText::_('COM_HWDMS_FILE_SQUARE_IMAGE');
                        break;
                    case 3:
                        return JText::_('COM_HWDMS_FILE_THUMBNAIL_IMAGE');
                        break;
                    case 4:
                        return JText::_('COM_HWDMS_FILE_SMALL_IMAGE');
                        break;
                    case 5:
                        return JText::_('COM_HWDMS_FILE_MEDIUM1_IMAGE');
                        break;
                    case 6:
                        return JText::_('COM_HWDMS_FILE_MEDIUM2_IMAGE');
                        break;
                    case 7:
                        return JText::_('COM_HWDMS_FILE_LARGE_IMAGE');
                        break;
                    case 8:
                        return JText::_('COM_HWDMS_FILE_MP3_AUDIO');
                        break;
                    case 9:
                        return JText::_('COM_HWDMS_FILE_OGG_AUDIO');
                        break;
                    case 10:
                        return JText::_('COM_HWDMS_FILE_CUSTOM_THUMBNAIL');
                        break;
                    case 11:
                        return JText::_('COM_HWDMS_FILE_FLV_VIDEO_240');
                        break;
                    case 12:
                        return JText::_('COM_HWDMS_FILE_FLV_VIDEO_360');
                        break;
                    case 13:
                        return JText::_('COM_HWDMS_FILE_FLV_VIDEO_480');
                        break;
                    case 14:
                        return JText::_('COM_HWDMS_FILE_MP4_VIDEO_360');
                        break;
                    case 15:
                        return JText::_('COM_HWDMS_FILE_MP4_VIDEO_480');
                        break;
                    case 16:
                        return JText::_('COM_HWDMS_FILE_MP4_VIDEO_720');
                        break;
                    case 17:
                        return JText::_('COM_HWDMS_FILE_MP4_VIDEO_1080');
                        break;
                    case 18:
                        return JText::_('COM_HWDMS_FILE_WEBM_VIDEO_360');
                        break;
                    case 19:
                        return JText::_('COM_HWDMS_FILE_WEBM_VIDEO_480');
                        break;
                    case 20:
                        return JText::_('COM_HWDMS_FILE_WEBM_VIDEO_720');
                        break;
                    case 21:
                        return JText::_('COM_HWDMS_FILE_WEBM_VIDEO_1080');
                        break;
                    case 22:
                        return JText::_('COM_HWDMS_FILE_OGG_VIDEO_360');
                        break;
                    case 23:
                        return JText::_('COM_HWDMS_FILE_OGG_VIDEO_480');
                        break;
                    case 24:
                        return JText::_('COM_HWDMS_FILE_OGG_VIDEO_720');
                        break;
                    case 25:
                        return JText::_('COM_HWDMS_FILE_OGG_VIDEO_1080');
                        break;
                }
        }
        
        /**
	 * Method to delete all files associated with a single media item.
         * 
	 * @since   0.1
	 **/
        function deleteMediaFiles($item)
        {
                // Import Joomla modelform library
                jimport('joomla.application.component.modeladmin');

                hwdMediaShareFactory::load('files');
                hwdMediaShareFiles::getLocalStoragePath();
                $hwdmsFiles = hwdMediaShareFiles::getInstance();
                $files = $hwdmsFiles->getMediaFiles($item);

                $fileArray = array();
                foreach($files as $file)
                {
                        $fileArray[] = (int) $file->id;
                }                        

                $fileModel = JModelAdmin::getInstance('File','hwdMediaShareModel');                        
                if (!$fileModel->delete($fileArray))
                {
                        JFactory::getApplication()->enqueueMessage( $fileModel->getError() );
                }
        }        
}