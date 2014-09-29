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

class hwdMediaShareThumbnails extends JObject
{        
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
	 * Returns the hwdMediaShareThumbnails object, only creating it if it
	 * doesn't already exist.
	 *
	 * @access  public
         * @static
	 * @return  hwdMediaShareThumbnails Object.
	 */ 
	public static function getInstance()
	{
		static $instance;

		if (!isset ($instance))
                {
			$c = 'hwdMediaShareThumbnails';
                        $instance = new $c;
		}

		return $instance;
	}

	/**
	 * Method to get the thumbnail url of a media.
	 *
	 * @access  public
         * @static
         * @param   object   $item         The item object.
         * @param   integer  $elementType  The element type.
	 * @return  string   The URL to deliver the thumbnail.
	 */ 
        public static function thumbnail($item, $elementType = 1)
        {
                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // Load cache object.
                $cache = JFactory::getCache('com_hwdmediashare');
                $cache->setCaching(1);
            
                switch ($elementType)
                {
                        case 1: // Media
                                if ($config->get('caching'))
                                {
                                        return $cache->call(array('hwdMediaShareThumbnails', 'getMediaThumbnail'), $item);
                                }
                                else 
                                {
                                        return hwdMediaShareThumbnails::getMediaThumbnail($item);
                                }
                        break;                    
                        case 2: // Album
                                if ($config->get('caching'))
                                {
                                        return $cache->call(array('hwdMediaShareThumbnails', 'getAlbumThumbnail'), $item);
                                }
                                else 
                                {
                                        return hwdMediaShareThumbnails::getAlbumThumbnail($item);
                                }
                        break;
                        case 3: // Group
                                if ($config->get('caching'))
                                {
                                        return $cache->call(array('hwdMediaShareThumbnails', 'getGroupThumbnail'), $item);
                                }
                                else 
                                {
                                        return hwdMediaShareThumbnails::getGroupThumbnail($item);
                                }
                        break;
                        case 4: // Playlist
                                if ($config->get('caching'))
                                {
                                        return $cache->call(array('hwdMediaShareThumbnails', 'getPlaylistThumbnail'), $item);
                                }
                                else 
                                {
                                        return hwdMediaShareThumbnails::getPlaylistThumbnail($item);
                                }
                        break;
                        case 6: // Category
                                if ($config->get('caching'))
                                {
                                        return $cache->call(array('hwdMediaShareThumbnails', 'getCategoryThumbnail'), $item);
                                }
                                else 
                                {
                                        return hwdMediaShareThumbnails::getCategoryThumbnail($item);
                                }
                        break;
                }
        }

	/**
	 * Method to get the url of an album thumbnail.
	 *
	 * @access  public
         * @static
         * @param   object  $item  The item object.
	 * @return  string  The URL to deliver the thumbnail.
	 */ 
        public static function getAlbumThumbnail($item)
	{
                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                  
                // Check for a custom thumbnail.
                if ($custom = hwdMediaShareThumbnails::getElementThumbnail($item, 2))
                {
                        return $custom;
                }
                
                // Load most recent media and display as thumbnail.
                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                $model = JModelLegacy::getInstance('Media', 'hwdMediaShareModel', array('ignore_request' => true));
                $model->context = 'com_hwdmediashare.album';
                $model->populateState();
                $model->setState('list.ordering', 'a.created');
                $model->setState('list.direction', 'DESC');
                $model->setState('filter.album_id', $item->id);

                if ($media = $model->getItem())
                {
                        return hwdMediaShareThumbnails::getMediaThumbnail($media);
                }
                
                // Fallback to default thumbnail. 
                return JURI::root(true) . '/media/com_hwdmediashare/assets/images/default-image-' . $config->get('list_thumbnail_size') . '.png';
        }
        
	/**
	 * Method to get the url of a category thumbnail.
	 *
	 * @access  public
         * @static
         * @param   object  $item  The item object.
	 * @return  string  The URL to deliver the thumbnail.
	 */ 
        public static function getCategoryThumbnail($item)
	{
                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                  
                // Check for a custom thumbnail.
                $registry = new JRegistry($item->params);
                $image = $registry->get('image');
                if (!empty($image))
                {
                        return $image;
                }

                // Load most recent media and display as thumbnail.
                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                $model = JModelLegacy::getInstance('Media', 'hwdMediaShareModel', array('ignore_request' => true));
                $model->context = 'com_hwdmediashare.cateogry';
                $model->populateState();
                $model->setState('list.ordering', 'a.created');
                $model->setState('list.direction', 'DESC');
                $model->setState('filter.category_id', $item->id);

                if ($media = $model->getItem())
                {
                        return hwdMediaShareThumbnails::getMediaThumbnail($media);
                }
                
                // Fallback to default thumbnail. 
                return JURI::root(true) . '/media/com_hwdmediashare/assets/images/default-image-' . $config->get('list_thumbnail_size') . '.png';
        }
        
	/**
	 * Method to get the url of a group thumbnail.
	 *
	 * @access  public
         * @static
         * @param   object  $item  The item object.
	 * @return  string  The URL to deliver the thumbnail.
	 */ 
        public static function getGroupThumbnail($item)
	{
                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                  
                // Check for a custom thumbnail.
                if ($custom = hwdMediaShareThumbnails::getElementThumbnail($item, 3))
                {
                        return $custom;
                }
                
                // Load most recent media and display as thumbnail.
                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                $model = JModelLegacy::getInstance('Media', 'hwdMediaShareModel', array('ignore_request' => true));
                $model->context = 'com_hwdmediashare.group';
                $model->populateState();
                $model->setState('list.ordering', 'a.created');
                $model->setState('list.direction', 'DESC');
                $model->setState('filter.group_id', $item->id);

                if ($media = $model->getItem())
                {
                        return hwdMediaShareThumbnails::getMediaThumbnail($media);
                }
                
                // Fallback to default thumbnail. 
                return JURI::root(true) . '/media/com_hwdmediashare/assets/images/default-image-' . $config->get('list_thumbnail_size') . '.png';
        }
        
	/**
	 * Method to get the url of a media thumbnail.
	 *
	 * @access  public
         * @static
         * @param   object  $item  The item object.
	 * @return  string  The URL to deliver the thumbnail.
	 */ 
        public static function getMediaThumbnail($item)
	{
                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                            
                // Check for a custom thumbnail.
                if ($custom = hwdMediaShareThumbnails::getElementThumbnail($item, 1))
                {
                        return $custom;
                }
            
                // Check for a remote thumbnail.
                if (!empty($item->thumbnail))
                {
                        $item->thumbnail = str_replace("http://i1.ytimg.com", "https://i1.ytimg.com", $item->thumbnail);
                        return $item->thumbnail;
                }    
                
                if (!isset($item->id) || !isset($item->key) || !isset($item->ext_id))
		{
                        return JURI::root(true) . '/media/com_hwdmediashare/assets/images/default-image-' . $config->get('list_thumbnail_size') . '.png';
		}
                
                // If CDN, let the CDN framework return the data.
                if ($item->type == 5 && $item->storage)
		{
                        $pluginClass = 'plgHwdmediashare'.$item->storage;
                        $pluginPath = JPATH_ROOT.'/plugins/hwdmediashare/'.$item->storage.'/'.$item->storage.'.php';
                        if (file_exists($pluginPath))
                        {
                                JLoader::register($pluginClass, $pluginPath);
                                $HWDcdn = call_user_func(array($pluginClass, 'getInstance'));
                                return $HWDcdn->publicUrl($item, $config->get('list_thumbnail_size'));
                        }
                }
                
                hwdMediaShareFactory::load('files');
                hwdMediaShareFiles::getLocalStoragePath();
                $hwdmsFiles = hwdMediaShareFiles::getInstance();
                $files = $hwdmsFiles->getMediaFiles($item);

                $folders = hwdMediaShareFiles::getFolders($item->key);

                // Check for preferred thumbnail.
                $filename = hwdMediaShareFiles::getFilename($item->key, $config->get('list_thumbnail_size'));
                $ext = hwdMediaShareFiles::getExtension($item, $config->get('list_thumbnail_size'));
                $path = hwdMediaShareFiles::getPath($folders, $filename, $ext);
                if (file_exists($path))
                {
                        if ($config->get('protect_media') == 1)
                        {
                                return hwdMediaShareDownloads::protectedUrl($item->id, $config->get('list_thumbnail_size'));
                        }
                        else
                        {
                                return hwdMediaShareFiles::getUrl($folders, $filename, $ext);
                        }
                }
                
                // Check for any other processed images.
                $fileTypes = array(5,6,4,3,7);
                foreach ($fileTypes as $fileType)
                {
                        $folders = hwdMediaShareFiles::getFolders($item->key);
                        $filename = hwdMediaShareFiles::getFilename($item->key, $fileType);
                        $ext = hwdMediaShareFiles::getExtension($item, $fileType);
                        $path = hwdMediaShareFiles::getPath($folders, $filename, $ext);
                        if (file_exists($path))
                        {
                                if ($config->get('protect_media') == 1)
                                {
                                        return hwdMediaShareDownloads::protectedUrl($item->id, $fileType);
                                }
                                else
                                {
                                        return hwdMediaShareFiles::getUrl($folders, $filename, $ext);
                                }
                        }
                }
                     
                // Check if original is an image.
                $ext = hwdMediaShareFiles::getExtension($item, 1);
                hwdMediaShareFactory::load('images');
                if (hwdMediaShareImages::isNativeImage($ext))
                {
                        hwdMediaShareFiles::getLocalStoragePath();
                        $hwdmsFiles = hwdMediaShareFiles::getInstance();
                        $files = $hwdmsFiles->getMediaFiles($item);

                        $folders = hwdMediaShareFiles::getFolders($item->key);
                        $filename = hwdMediaShareFiles::getFilename($item->key, 1);
                        $ext = hwdMediaShareFiles::getExtension($item, 1);

                        $path = hwdMediaShareFiles::getPath($folders, $filename, $ext);
                        if (file_exists($path))
                        {
                                if ($config->get('protect_media') == 1)
                                {
                                        return hwdMediaShareDownloads::protectedUrl($item->id, 1);
                                }
                                else
                                {
                                        return hwdMediaShareFiles::getUrl($folders, $filename, $ext);
                                }
                        }
                }
                
                return JURI::root(true).'/media/com_hwdmediashare/assets/images/default-image-'.$config->get('list_thumbnail_size').'.png';
                                
                //@TODO: Allow custom thumbnails for different file types.
                if ($item->ext == 'pdf') return JURI::root(true).'/media/com_hwdmediashare/assets/images/default-image-pdf.png';
        }

	/**
	 * Method to get the url of a playlist thumbnail.
	 *
	 * @access  public
         * @static
         * @param   object  $item  The item object.
	 * @return  string  The URL to deliver the thumbnail.
	 */ 
        public static function getPlaylistThumbnail($item)
	{
                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                  
                // Check for a custom thumbnail.
                if ($custom = hwdMediaShareThumbnails::getElementThumbnail($item, 7))
                {
                        return $custom;
                }
                
                // Load most recent media and display as thumbnail.
                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                $model = JModelLegacy::getInstance('Media', 'hwdMediaShareModel', array('ignore_request' => true));
                $model->context = 'com_hwdmediashare.playlist';
                $model->populateState();
                $model->setState('list.ordering', 'a.created');
                $model->setState('list.direction', 'DESC');
                $model->setState('filter.playlist_id', $item->id);

                if ($media = $model->getItem())
                {
                        return hwdMediaShareThumbnails::getMediaThumbnail($media);
                }
                
                // Fallback to default thumbnail. 
                return JURI::root(true) . '/media/com_hwdmediashare/assets/images/default-image-' . $config->get('list_thumbnail_size') . '.png';
        }
        
	/**
	 * Method to get the channel art url for a channel.
	 *
	 * @access  public
         * @static
	 * @param   object  $item   The channel to display.
	 * @return  string  The url of the channel art.
	 */ 
        public static function getChannelArt($item)
	{
                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
  
                if (empty($item->key))
                {
                        return JURI::root(true).'/media/com_hwdmediashare/assets/images/default-channel.png';
                }
                
                // Check for saved channel art.
                hwdMediaShareFactory::load('files');
                
                $folders = hwdMediaShareFiles::getFolders($item->key);
                $filename = hwdMediaShareFiles::getFilename($item->key, 10);
                $ext = hwdMediaShareFiles::getExtension($item, 10);

                $path = hwdMediaShareFiles::getPath($folders, $filename, $ext);

                if (file_exists($path))
                {
                        return hwdMediaShareFiles::getUrl($folders, $filename, $ext);
                }
                
                return JURI::root(true).'/media/com_hwdmediashare/assets/images/default-channel.png';
        }
        
	/**
	 * Method to get the avatar for a user.
	 *
	 * @access  public
         * @static
	 * @param   object  $user   The user to display.
	 * @return  string  The url of the avatar.
	 */ 
        public static function getAvatar($user)
        {
		// Initialiase variables.
                $db = JFactory::getDBO();
                
                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                if (!$user)
                {
                        return JURI::root(true) . '/media/com_hwdmediashare/assets/images/default-avatar.png';
                }
                elseif ($config->get('community_avatar') == 'cb' && file_exists(JPATH_ROOT.'/components/com_comprofiler'))
                {           
                        $query = $db->getQuery(true)
                                ->select('avatar')
                                ->from('#__comprofiler')
                                ->where('user_id = ' . $db->quote($user->id));
                        try
                        {                
                                $db->setQuery($query);
                                $avatar = $db->loadResult();
                        }
                        catch (Exception $e)
                        {
                                $this->setError($e->getMessage());
                                return false;
                        }                    

                        if (!empty($avatar) && file_exists(JPATH_ROOT.'/images/comprofiler/tn'.$cbAvatar))
                        {
                                return JURI::root(true).'/images/comprofiler/tn'.$cbAvatar;
                        }
                        elseif (!empty($avatar) && file_exists(JPATH_ROOT.'/images/comprofiler/'.$cbAvatar))
                        {
                                return JURI::root(true).'/images/comprofiler/'.$cbAvatar;
                        }                        
                }
                elseif ($config->get('community_avatar') == 'jomsocial' && file_exists(JPATH_ROOT.'/components/com_community'))
                {
                        include_once(JPATH_ROOT.'/components/com_community/libraries/core.php');
                        $JSUser = CFactory::getUser($user->id);
                        return $JSUser->getThumbAvatar();
                }
                elseif ($config->get('community_avatar') == 'easysocial' && file_exists(JPATH_ROOT.'/components/com_easysocial'))
                {
                        require_once(JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/foundry.php');
                        $user = Foundry::user($user->id);
                        return $user->getAvatar();                    
                }              
                elseif ($config->get('community_avatar') == 'gravatar' && isset($user->email))
                {
                        return "http://www.gravatar.com/avatar/".md5(strtolower(trim($user->email)));                    
                }
                elseif ($config->get('community_avatar') == 'jomwall' && file_exists(JPATH_ROOT.'/components/com_awdwall/helpers/user.php'))
                {
                        include_once (JPATH_ROOT.'/components/com_awdwall/helpers/user.php');
                        return AwdwallHelperUser::getBigAvatar51($user->id);	
                }

                return JURI::root(true).'/media/com_hwdmediashare/assets/images/default-avatar.png';
        }        

	/**
	 * Method to get a thumbnail for the video preview.
	 *
	 * @access  public
         * @static
	 * @param   object  $item   The media to display.
	 * @return  string  The url of the video preview image.
	 */ 
        public static function getVideoPreview($item)
	{
                hwdMediaShareFactory::load('files');
                hwdMediaShareFiles::getLocalStoragePath();
                
                // Check for good quality thumbnails.
                $fileTypes = array(7,6,5);
                foreach ($fileTypes as $fileType)
                {
                        $folders = hwdMediaShareFiles::getFolders($item->key);
                        $filename = hwdMediaShareFiles::getFilename($item->key, $fileType);
                        $ext = hwdMediaShareFiles::getExtension($item, $fileType);
                        $path = hwdMediaShareFiles::getPath($folders, $filename, $ext);
                        if (file_exists($path))
                        {
                                if ($config->get('protect_media') == 1)
                                {
                                        return hwdMediaShareDownloads::protectedUrl($item->id, $fileType);
                                }
                                else
                                {
                                        return hwdMediaShareFiles::getUrl($folders, $filename, $ext);
                                }
                        }
                }
                
                // Check for a custom thumbnail.
                if ($custom = hwdMediaShareThumbnails::getElementThumbnail($item))
                {
                        return $custom;
                }
                
                return false;            
        }       
        
	/**
	 * Method to get the url of a custom thumbnail for any element.
	 *
	 * @access  public
         * @static
         * @param   object   $item         The item object.
	 * @param   integer  $elementType  The element type.
	 * @return  mixed    The url of the thumbnail, false on fail.
	 */ 
        public static function getElementThumbnail($item, $elementType = 1)
	{
                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
    
                if (empty($item->key))
                {
                        return false;
                }
                
                hwdMediaShareFactory::load('files');
                hwdMediaShareFiles::getLocalStoragePath();
                
                $folders = hwdMediaShareFiles::getFolders($item->key);
                $filename = hwdMediaShareFiles::getFilename($item->key, 10);
                $ext = hwdMediaShareFiles::getExtension($item, 10);

                $path = hwdMediaShareFiles::getPath($folders, $filename, $ext);

                if (file_exists($path))
                {
                        if ($config->get('protect_media') == 1)
                        {
                                hwdMediaShareFactory::load('downloads');
                                return hwdMediaShareDownloads::protectedUrl($item->id, 10, $elementType);
                        }
                        else
                        {
                                return hwdMediaShareFiles::getUrl($folders, $filename, $ext);
                        }
                }
                
                return false;
	}        
}
