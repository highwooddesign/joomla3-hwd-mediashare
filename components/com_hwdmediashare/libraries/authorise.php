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

class hwdMediaShareAuthorise extends JObject
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
	 * Returns the hwdMediaShareAuthorise object, only creating it if it
	 * doesn't already exist.
	 *
	 * @access  public
         * @static
	 * @return  hwdMediaShareAuthorise Object.
	 */ 
	public static function getInstance()
	{
		static $instance;

		if (!isset ($instance))
                {
			$c = 'hwdMediaShareAuthorise';
                        $instance = new $c;
		}

		return $instance;
	}

	/**
	 * Method to authorise a user for an album action.
	 *
         * @access  public
	 * @param   string      $action     The name of the action to check for permission (link/unlink).
	 * @param   integer     $album_id   The id of the album.
	 * @param   integer     $media_id   The id of the media.
	 * @return  boolean     True if authorised
	 */
	public function authoriseAlbumAction($action, $album_id = null, $media_id = null)
	{ 
                // Initialise variables.
		$user = JFactory::getUser();

                // Load album.
                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                $table = JTable::getInstance('Album', 'hwdMediaShareTable');
                $table->load($album_id);
                $properties = $table->getProperties(1);
                $album = JArrayHelper::toObject($properties, 'JObject');
                       
                // Load media.
                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                $table = JTable::getInstance('Media', 'hwdMediaShareTable');
                $table->load($media_id);
                $properties = $table->getProperties(1);
                $media = JArrayHelper::toObject($properties, 'JObject');
                                
                switch ($action) 
                {
                        case 'link':
                        case 'unlink':
                                // Allow if user owns the album and the media.
                                if ($album->created_user_id == $user->id && $media->created_user_id == $user->id)
                                {
                                        return true;
                                }
                                
                                // Allow if user has global manage permissions.
                                if (!$user->authorise('core.edit.state', 'com_hwdmediashare'))
                                {
                                        return true;
                                }
                        break;    
                }

                return false;
	}   
        
	/**
	 * Method to authorise a user for a group action.
	 *
         * @access  public
	 * @param   string      $action     The name of the action to check for permission (link/unlink).
	 * @param   integer     $group_id   The id of the album.
	 * @param   integer     $media_id   The id of the media.
	 * @return  boolean     True if authorised
	 */
	public function authoriseGroupAction($action, $group_id = null, $media_id = null)
	{ 
                // Initialise variables.
		$user = JFactory::getUser();

                // Load group.
                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                $table = JTable::getInstance('Group', 'hwdMediaShareTable');
                $table->load($group_id);
                $properties = $table->getProperties(1);
                $group = JArrayHelper::toObject($properties, 'JObject');
                                  
                switch ($action) 
                {
                        case 'link':
                        case 'unlink':
                                // Allow if user owns the album and the media.
                                if ($group->created_user_id == $user->id)
                                {
                                        return true;
                                }
                                
                                // Allow if user has global manage permissions.
                                if (!$user->authorise('core.edit.state', 'com_hwdmediashare'))
                                {
                                        return true;
                                }
                        break;    
                }

                return false;
	} 
        
	/**
	 * Method to authorise a user for a playlist action.
	 *
         * @access  public
	 * @param   string      $action         The name of the action to check for permission (link/unlink).
	 * @param   integer     $playlist_id    The id of the album.
	 * @param   integer     $media_id       The id of the media.
	 * @return  boolean     True if authorised
	 */
	public function authorisePlaylistAction($action, $playlist_id = null, $media_id = null)
	{ 
                // Initialise variables.
		$user = JFactory::getUser();

                // Load group.
                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                $table = JTable::getInstance('Playlist', 'hwdMediaShareTable');
                $table->load($playlist_id);
                $properties = $table->getProperties(1);
                $playlist = JArrayHelper::toObject($properties, 'JObject');
                                  
                switch ($action) 
                {
                        case 'link':
                        case 'unlink':
                                // Allow if user owns the album and the media.
                                if ($playlist->created_user_id == $user->id)
                                {
                                        return true;
                                }
                                
                                // Allow if user has global manage permissions.
                                if (!$user->authorise('core.edit.state', 'com_hwdmediashare'))
                                {
                                        return true;
                                }
                        break;    
                }

                return false;
	}      
        
	/**
	 * Method to authorise a user for a media action.
	 *
         * @access  public
	 * @param   string      $action     The name of the action to check for permission.
	 * @param   integer     $assetid    The id of the category.
	 * @param   integer     $targetid   The id of the target (action specific).
	 * @return  boolean     True if authorised
	 */
	public function authoriseMediaAction($action, $assetid = null, $targetid)
	{ 
		$user = JFactory::getUser();

                if ($user->authorise('core.edit.state', 'com_hwdmediashare'))
                {
                        return true;
                }

                return false;
	}   
        
	/**
	 * Method to authorise a user for a category action.
	 *
         * @access  public
	 * @param   string      $action     The name of the action to check for permission.
	 * @param   integer     $assetid    The id of the category.
	 * @param   integer     $targetid   The id of the target (action specific).
	 * @return  boolean     True if authorised
	 */
	public function authoriseCategoryAction($action, $assetid = null, $targetid)
	{ 
		$user = JFactory::getUser();

                if ($user->authorise('core.edit.state', 'com_hwdmediashare'))
                {
                        return true;
                }

                return false;
	}        
}