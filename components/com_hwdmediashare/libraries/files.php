<?php
/**
 * @package     Joomla.site
 * @subpackage  Component.hwdmediashare
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class hwdMediaShareFiles extends JObject
{
	/**
	 * The element type to use with this library.
         * 
         * @access      public
	 * @var         string
	 */
	public $elementType = 1;
        
	/**
	 * The set of files for a media.
         * 
         * @access      protected
	 * @var         object
	 */
	protected $_fileset = null;

	/**
	 * Class constructor.
	 *
	 * @access  public
	 * @param   mixed  $properties  Either and associative array or another
	 *                              object to set the initial properties of the object.
         * @return  void
	 */
	public function __construct($properties = null)
	{
		parent::__construct($properties);
	}

	/**
	 * Returns the hwdMediaShareFiles object, only creating it if it
	 * doesn't already exist.
         * 
	 * @access  public
         * @static
	 * @return  hwdMediaShareFiles Object.
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
	 * Method to add a new file entry for a media.
         * 
         * @access  public
         * @param   object  $media          The associated media.
         * @param   integer $filetype       The integer API value for the type of file.
         * @return  boolean True on success.
	 */
	public function addFile($media, $fileType = null)
	{
                // Initialise variables.                        
                $db = JFactory::getDBO();
                $date = JFactory::getDate();
                $user =  JFactory::getUser();

                // Remove previous entries.
                $query = $db->getQuery(true);

                $conditions = array(
                    $db->quoteName('element_type') . ' = ' . $db->quote($this->elementType), 
                    $db->quoteName('element_id') . ' = ' . $db->quote($media->id), 
                    $db->quoteName('file_type') . ' = ' . $db->quote($fileType), 
                );

                $query->delete($db->quoteName('#__hwdms_files'));
                $query->where($conditions);
                try
                {
                        $db->setQuery($query);
                        $result = $db->execute();
                }
                catch (Exception $e)
                {
                        $this->setError($e->getMessage());
                        return false;
                }                

                // Add new entry.
                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                $table = JTable::getInstance('File', 'hwdMediaShareTable');

                $folders = hwdMediaShareFiles::getFolders($media->key);
                $filename = hwdMediaShareFiles::getFilename($media->key, $fileType);
                $ext = hwdMediaShareFiles::getExtension($media, $fileType);
                $path = hwdMediaShareFiles::getPath($folders, $filename, $ext);

                if (file_exists($path))
                {
                        $post                           = array();
                        $post['element_type']           = $this->elementType;
                        $post['element_id']             = $media->id;
                        $post['file_type']              = $fileType;
                        $post['basename']               = $filename;
                        $post['ext']                    = $ext;
                        $post['size']                   = intval(filesize($path));
                        $post['checked']                = $date->toSql();
                        $post['published']              = 1;
                        $post['featured']               = 0;
                        $post['access']                 = 1;
                        $post['download']               = 1;
                        $post['created_user_id']        = $user->id;
                        $post['created_user_id_alias']  = '';
                        $post['created']                = $date->toSql();
                        $post['publish_up']             = $date->toSql();
                        $post['publish_down']           = '0000-00-00 00:00:00';
                        $post['hits']                   = 0;
                        $post['language']               = '*';
                        
                        // Attempt to save the details to the database.
                        if (!$table->save($post))
                        {
                                $this->setError($table->getError());
                                return false;
                        }
                }
                else
                {
                        $this->setError('COM_HWDMS_ERROR_FAILED_TO_ADD_FILE_TO_DATABASE_FILE_DOES_NOT_EXIST');
                        return false;                    
                }
	}
        
	/**
	 * Method to define the local storage path.
         * 
         * @access  public
         * @static
         * @return  void
	 */
        public static function getLocalStoragePath()
        {
                // Initialise variables.            
                $app = JFactory::getApplication();
                
                // Load HWD config.
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
                        // Set default relative url.
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
                        if ($app->input->get('format', '', 'word') == 'feed')
                        {
                                define('HWDMS_URL_MEDIA_FILES', JURI::root().$urelative);
                        }    
                        else
                        {
                                define('HWDMS_URL_MEDIA_FILES', JURI::root(true).'/'.$urelative);
                        }
                }
        }
        
        /**
	 * Method to get recursive storage folder names.
         * 
         * @access  public
         * @static
         * @param   string  $key    The unique key.
         * @return  array   The recursive folder names.
	 */
        public static function getFolders($key)
        {
                $folders[1] = substr($key, 0, 2);
                $folders[2] = substr($key, 2, 2);
                $folders[3] = substr($key, 4, 2);
                return $folders;
        }
        
        /**
	 * Method to check and create storage folders.
         * 
         * @access  public
         * @static
         * @param   array   $folders    The recursive folder names.
         * @return  boolean True on success.
	 */
        public static function setupFolders($folders)
        {
		jimport('joomla.filesystem.folder');
                if (!JFolder::create(HWDMS_PATH_MEDIA_FILES.'/'.$folders[1]))
                {
                        return false;
                }
                if (!JFolder::create(HWDMS_PATH_MEDIA_FILES.'/'.$folders[1].'/'.$folders[2]))
                {
                        return false;
                }
                if (!JFolder::create(HWDMS_PATH_MEDIA_FILES.'/'.$folders[1].'/'.$folders[2].'/'.$folders[3]))
                {
                        return false;
                }
                return true;
        }
        
        /**
	 * Method to get the filename for a media for the specified media type.
         * 
         * @access  public
         * @static
         * @param   string  $key        The unique key.
         * @param   integer $filetype   The integer API value for the type of file.
         * @return  string  The filename.
	 */
        public static function getFilename($key, $fileType)
        {
                switch ($fileType)
                {
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
	 * Method to get an extension of a media file from file type.
         * 
         * @access  public
         * @static
         * @param   object  $media          The associated media.
         * @param   integer $filetype       The integer API value for the type of file.
         * @return  boolean True on success.
	 */
        public static function getExtension($media, $fileType)
        {
                // Initialise variables.                        
                $db = JFactory::getDBO();
                
                switch ($fileType) 
                {
                        case 1:
                                if (!$media->ext_id) return false;
                                
                                $query = $db->getQuery(true)
                                        ->select('ext')
                                        ->from('#__hwdms_ext')
                                        ->where('id = ' . $db->quote($media->ext_id));
                                try
                                {                
                                        $db->setQuery($query);
                                        return $db->loadResult();
                                }
                                catch (Exception $e)
                                {
                                        $this->setError($e->getMessage());
                                        return false;
                                }
                        break;
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
                                if (!$media->thumbnail_ext_id) return false;
                                
                                $query = $db->getQuery(true)
                                        ->select('ext')
                                        ->from('#__hwdms_ext')
                                        ->where('id = ' . $db->quote($media->thumbnail_ext_id));
                                try
                                {                
                                        $db->setQuery($query);
                                        return $db->loadResult();
                                }
                                catch (Exception $e)
                                {
                                        $this->setError($e->getMessage());
                                        return false;
                                }
                        break;
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
	 * Method to get the path of a media file.
         * 
         * @access  public
         * @static
         * @param   array   $folders    The recursive folder names.
         * @param   string  $filename   The name of the file.
         * @param   string  $ext        The extension of the file.
         * @param   boolean $abs        Flag for absolute/relative path.
         * @return  string  The path.
	 */
        public static function getPath($folders, $filename, $ext, $abs = true)
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
	 * Method to get the (public) URL of a media file.
         * 
         * @access  public
         * @static
         * @param   array   $folders    The recursive folder names.
         * @param   string  $filename   The name of the file.
         * @param   string  $ext        The extension of the file.
         * @return  string  The URL.
	 */
        public static function getUrl($folders, $filename, $ext)
        {
                return HWDMS_URL_MEDIA_FILES . '/' . $folders[1] . '/' . $folders[2] . '/' . $folders[3] . '/' . $filename . '.' . $ext;
        }
        
	/**
	 * Method to get all files associated with a media item
         * 
         * @access  public
         * @param   object  $media          The associated media.
         * @param   integer $filetype       The integer API value for the type of file.
         * @return  boolean True on success.
	 */
	public function getMediaFiles($item)
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
                                catch(Exception $e)
                                {
                                        $this->_fileset[$pk] = false;
                                        $this->setError($e->getMessage());
                                        return false;                            
                                }
                        }
		}

		return $this->_fileset[$pk];
        }
        
        /**
	 * Method to get human readable name of a file type.
         * 
         * @access  public
         * @static
         * @param   object  $item   The file item.
         * @return  string  The name of the file type.
	 */
        public static function getFileType($item)
        {
                switch ($item->file_type)
                {
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
         * @access  public
         * @param   object  $media          The media object.
         * @return  boolean True on success.
	 */
	public function deleteMediaFiles($media)
        {
                $files = $this->getMediaFiles($media);

                $fileIds = array();
                foreach($files as $file)
                {
                        $fileIds[] = (int) $file->id;
                }                        

                JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/models');
                $model = JModelLegacy::getInstance('File', 'hwdMediaShareModel', array('ignore_request' => true));
                if (!$model->delete($fileIds))
                {
                        $this->setError($model->getError());
                        return false;
                }
            
                return true;
        } 
        
	/**
	 * Method to check if a file has been generated and return file data.
         * 
         * @access  public
         * @static
         * @param   object  $item      The media item.
         * @param   object  $fileType  The type of file.
         * @return  mixed   The mp3 file object, false on fail.
	 */
	public static function getFileData($item, $fileType)
	{
                hwdMediaShareFactory::load('files');
                hwdMediaShareFactory::load('documents');
                $folders = hwdMediaShareFiles::getFolders($item->key);
                $filename = hwdMediaShareFiles::getFilename($item->key, $fileType);
                $ext = hwdMediaShareFiles::getExtension($item, $fileType);
                $path = hwdMediaShareFiles::getPath($folders, $filename, $ext);
 
                if (file_exists($path))
                {
                        // Create file object.
                        $file = new JObject;
                        $file->local = true;
                        $file->path = $path;
                        $file->url = hwdMediaShareDownloads::url($item, $fileType);
                        $file->size = filesize($path);
                        $file->ext = $ext;
                        $file->type = hwdMediaShareDocuments::getContentType($ext);
                  
                        return $file;
                }

                return false;
	}         
}