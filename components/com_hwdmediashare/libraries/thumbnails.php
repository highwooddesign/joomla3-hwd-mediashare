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
                        return JURI::root( true ).'/media/com_hwdmediashare/assets/images/default-channel.png';
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
                
                return JURI::root( true ).'/media/com_hwdmediashare/assets/images/default-channel.png';
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
                        return JURI::root( true ) . '/media/com_hwdmediashare/assets/images/default-avatar.png';
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
                                return JURI::root( true ).'/images/comprofiler/tn'.$cbAvatar;
                        }
                        elseif (!empty($avatar) && file_exists(JPATH_ROOT.'/images/comprofiler/'.$cbAvatar))
                        {
                                return JURI::root( true ).'/images/comprofiler/'.$cbAvatar;
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

                return JURI::root( true ).'/media/com_hwdmediashare/assets/images/default-avatar.png';
        }        

	/**
	 * Method to get the channel art url for a channel.
	 *
	 * @access  public
         * @static
	 * @param   object  $item   The media to display.
	 * @return  string  The url of the video preview image.
	 */ 
        public static function getVideoPreview($item)
	{
        }       
}
