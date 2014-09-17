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

class hwdMediaShareHelperRoute
{
	/**
	 * An array of associated menu data.
         * 
         * @access      protected
         * @static
	 * @var         array
	 */       
	protected static $lookup;

	/**
	 * Method to route the user on entry into the component.
         * 
         * @access  public
         * @static
         * @return  boolean
	 */
	public static function entry()
	{
		// Initialise variables.
		$app = JFactory::getApplication();      
                $user	= JFactory::getUser();
                
                // Bypass this entry check for specific compound tasks.
                if (($app->input->get('task', '', 'var') == 'addmedia.upload' && $app->input->get('format', '', 'word') == 'raw') ||
                    ($app->input->get('task', '', 'var') == 'uber.link_upload' && $app->input->get('format', '', 'word') == 'raw') ||
                    ($app->input->get('task', '', 'var') == 'maintenance.process' && $app->input->get('format', '', 'word') == 'raw'))
                {
                        return true;
                }

                // Get HWD config.
                $hwdms  = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // Load content router for redirects.
                JLoader::register('ContentHelperRoute', JPATH_ROOT.'/components/com_content/helpers/route.php');

                // Redirect if gallery offline.
                if ($config->get('offline') == 1)
                {
                        $app->enqueueMessage( JText::_( 'COM_HWDMS_ERROR_NOAUTHORISED_OFFLINE' ) ); 
                        $app->redirect($config->get('offline_redirect') > 0 ? ContentHelperRoute::getArticleRoute($config->get('offline_redirect')) : 'index.php');
                }

                // Redirect if not in allowed user group.
                if (!in_array($config->get('default_access'), $user->getAuthorisedViewLevels()))
                {
                        $app->enqueueMessage( JText::_( 'COM_HWDMS_ERROR_NOAUTHORISED_GALLERY' ) ); 
                        $app->redirect($config->get('no_access_redirect') > 0 ? ContentHelperRoute::getArticleRoute($config->get('no_access_redirect')) : 'index.php');
                }
                
                // Check the feature is enabled.
                $enabled = true;
                
                if (!$config->get('enable_categories')    && (in_array($app->input->get('view', '', 'word'), array('categories', 'category', 'categoryform')))) $enabled = false;
                if (!$config->get('enable_albums')        && (in_array($app->input->get('view', '', 'word'), array('albums', 'album', 'albumform')))) $enabled = false;
                if (!$config->get('enable_groups')        && (in_array($app->input->get('view', '', 'word'), array('groups', 'group', 'groupform')))) $enabled = false;
                if (!$config->get('enable_channels')      && (in_array($app->input->get('view', '', 'word'), array('users', 'user', 'userform')))) $enabled = false;
                if (!$config->get('enable_playlists')     && (in_array($app->input->get('view', '', 'word'), array('playlists', 'playlist', 'playlistform')))) $enabled = false;
                
                if (!$enabled)
                {
                        $app->enqueueMessage( JText::_('COM_HWDMS_ERROR_FEATURE_DISABLED') );
                        $app->redirect( $config->get('no_access_redirect') > 0 ? ContentHelperRoute::getArticleRoute($config->get('no_access_redirect')) : hwdMediaShareHelperRoute::getMediaRoute() );
                }
	}
        
	/**
	 * Method to get the url to an album.
         * 
         * @access  public
         * @static
         * @param   integer  $id      The id of the content.
         * @param   array    $params  An array of additional url parameters.
         * @return  string  The url to the content.
	 */
	public static function getAlbumRoute($id, $params = array())
	{
		$needles = array(
			'album'  => array((int) $id),
			'albums'  => null
		);
                
                return self::_buildUrl('index.php?option=com_hwdmediashare&view=album&id=' . $id, $params, $needles);
	}        
        
	/**
	 * Method to get the url to the albums list.
         * 
         * @access  public
         * @static
         * @param   array   $params  An array of additional url parameters.
         * @return  string  The url to the content.
	 */
	public static function getAlbumsRoute($params = array())
	{
		$needles = array(
			'albums'  => null
		);

                return self::_buildUrl('index.php?option=com_hwdmediashare&view=albums', $params, $needles);
	}
        
	/**
	 * Method to get the url to the categories list.
         * 
         * @access  public
         * @static
         * @param   array   $params  An array of additional url parameters.
         * @return  string  The url to the content.
	 */
	public static function getCategoriesRoute($params = array())
	{
		$needles = array(
			'categories'  => null
		);

                return self::_buildUrl('index.php?option=com_hwdmediashare&view=categories', $params, $needles);
	}
        
	/**
	 * Method to get the url to the a category.
         * 
         * @access  public
         * @static
         * @param   integer  $id      The id of the content.
         * @param   array    $params  An array of additional url parameters.
         * @return  string   The url to the content.
	 */
	public static function getCategoryRoute($id, $params = array())
	{
		$needles = array(
			'category'  => array((int) $id),
			'categories'  => null
		);

                return self::_buildUrl('index.php?option=com_hwdmediashare&view=category&id=' . $id, $params, $needles);
	}
        
	/**
	 * Method to get the url to the a group.
         * 
         * @access  public
         * @static
         * @param   integer  $id      The id of the content.
         * @param   array    $params  An array of additional url parameters.
         * @return  string   The url to the content.
	 */
	public static function getGroupRoute($id, $params = array())
	{
		$needles = array(
			'group'  => array((int) $id),
			'groups'  => null
		);

                return self::_buildUrl('index.php?option=com_hwdmediashare&view=group&id=' . $id, $params, $needles);
	}
        
	/**
	 * Method to get the url to the group list.
         * 
         * @access  public
         * @static
         * @param   array   $params  An array of additional url parameters.
         * @return  string  The url to the content.
	 */
	public static function getGroupsRoute($params = array())
	{
		$needles = array(
			'groups'  => null
		);

                return self::_buildUrl('index.php?option=com_hwdmediashare&view=groups', $params, $needles);
	}
        
	/**
	 * Method to get the url to the media list.
         * 
         * @access  public
         * @static
         * @param   array   $params  An array of additional url parameters.
         * @return  string  The url to the content.
	 */
	public static function getMediaRoute($params=array())
	{
		$needles = array(
			'media'  => null
		);

                return self::_buildUrl('index.php?option=com_hwdmediashare&view=media', $params, $needles);
	}
        
	/**
	 * Method to get the url to the a media item.
         * 
         * @access  public
         * @static
         * @param   integer  $id         The id of the content.
         * @param   array    $params     An array of additional url parameters.
         * @param   boolean  $associate  Add associated data to the url.
         * @return  string   The url to the content.
	 */
	public static function getMediaItemRoute($id, $params=array(), $associate=true)
	{
		// Initialise variables.
		$app = JFactory::getApplication();
                
		$needles = array(
			'mediaitem'  => array((int) $id),
			'media'  => null
		);
                
                $link = self::_buildUrl('index.php?option=com_hwdmediashare&view=mediaitem&id=' . $id, $params, $needles);

                // Get data from the request.
                $option = $app->input->get('option');
                $view = $app->input->get('view');
                $id = $app->input->get('id');
                
                // Associate, in priority: category/playlist/album/group
                if ($associate && $option == 'com_hwdmediashare' && $view == 'category' && $id)
                {
                        $link.= '&category_id='.$id;
                } 
                elseif ($associate && $category_id = $app->input->get('category_id', 0, 'int'))
                {
                        $link.= '&category_id='.$category_id;
                }
                elseif ($associate && $option == 'com_hwdmediashare' && $view == 'playlist' && $id)
                {
                        $link.= '&playlist_id='.$id;
                }
                elseif ($associate && $playlist_id = $app->input->get('playlist_id', 0, 'int'))
                {
                        $link.= '&playlist_id='.$playlist_id;
                }                 
                elseif ($associate && $option == 'com_hwdmediashare' && $view == 'album' && $id)
                {
                        $link.= '&album_id='.$id;
                }
                elseif ($associate && $album_id = $app->input->get('album_id', 0, 'int'))
                {
                        $link.= '&album_id='.$album_id;
                }                
                elseif ($associate && $option == 'com_hwdmediashare' && $view == 'group' && $id)
                {
                        $link.= '&group_id='.$id;
                }  
                elseif ($associate && $group_id = $app->input->get('group_id', 0, 'int'))
                {
                        $link.= '&group_id='.$group_id;
                }  
                
                return $link;
	}

	/**
	 * Method to get the url to the a playlist.
         * 
         * @access  public
         * @static
         * @param   integer  $id      The id of the content.
         * @param   array    $params  An array of additional url parameters.
         * @return  string   The url to the content.
	 */
	public static function getPlaylistRoute($id, $params = array())
	{
		$needles = array(
			'playlist'  => array((int) $id),
			'playlists'  => null
		);
                
                return self::_buildUrl('index.php?option=com_hwdmediashare&view=playlist&id=' . $id, $params, $needles);
	}
        
	/**
	 * Method to get the url to the playlist list.
         * 
         * @access  public
         * @static
         * @param   array   $params  An array of additional url parameters.
         * @return  string  The url to the content.
	 */
	public static function getPlaylistsRoute($params = array())
	{
		$needles = array(
			'playlists'  => null
		);
                
                return self::_buildUrl('index.php?option=com_hwdmediashare&view=playlists', $params, $needles);
	}
        
	/**
	 * Method to get the url to the slideshow.
         * 
         * @access  public
         * @static
         * @param   integer  $id         The id of the content.
         * @param   array    $params     An array of additional url parameters.
         * @param   boolean  $associate  Add associated data to the url.
         * @return  string   The url to the content.
	 */
	public static function getSlideshowRoute($id, $params=array(), $associate=true)
	{
		// Initialise variables.
		$app = JFactory::getApplication();
            
		$needles = array(
			'media'  => null
		);
                
                $link = self::_buildUrl('index.php?option=com_hwdmediashare&view=slideshow', $params, $needles);

                // Associate, in priority: category/playlist/album/group
                if ($associate && $category_id = $app->input->get('category_id', 0, 'int'))
                {
                        $link.= '&category_id='.$category_id;
                }
                elseif ($associate && $playlist_id = $app->input->get('playlist_id', 0, 'int'))
                {
                        $link.= '&playlist_id='.$playlist_id;
                }                 
                elseif ($associate && $album_id = $app->input->get('album_id', 0, 'int'))
                {
                        $link.= '&album_id='.$album_id;
                }                
                elseif ($associate && $group_id = $app->input->get('group_id', 0, 'int'))
                {
                        $link.= '&group_id='.$group_id;
                } 
                
                // Add component tmpl.
                $link.= '&tmpl=component';
                
                // Add return link.
                $link.= '&return=' .  base64_encode(JFactory::getURI()->toString());

                return $link;
	}

	/**
	 * Method to get the url to the a user, based on community options.
         * 
         * @access  public
         * @static
         * @param   integer  $id      The id of the content.
         * @param   array    $params  An array of additional url parameters.
         * @return  string   The url to the content.
	 */
	public static function getUserRoute($id, $params = array())
	{
                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                if ($id == 0)
                {
                        return '#';
                }
                elseif ($config->get('community_link') == 'cb' && file_exists(JPATH_ROOT.'/components/com_comprofiler'))
                {
                        return JRoute::_('index.php?option=com_comprofiler&task=userProfile&user='.$id);
                }
                elseif ($config->get('community_link') == 'jomsocial' && file_exists(JPATH_ROOT.'/components/com_community'))
                {
                        include_once(JPATH_ROOT.'/components/com_community/libraries/core.php');
                        return CRoute::_('index.php?option=com_community&view=profile&userid='.$id);
                }
                elseif ($config->get('community_link') == 'easysocial' && file_exists(JPATH_ROOT.'/components/com_easysocial'))
                {
                        return JRoute::_('index.php?option=com_easysocial&view=profile&id='.$id);
                }                
                elseif ($config->get('community_link') == 'jomwall' && file_exists(JPATH_ROOT.'/components/com_awdwall'))
                {
                        return JRoute::_('index.php?option=com_awdwall&view=awdwall&layout=mywall&wuid='.$id);
                }
                
		$needles = array(
			'channel'  => array((int) $id),
			'channels'  => null
		);

                return self::_buildUrl('index.php?option=com_hwdmediashare&view=channel&id=' . $id, $params, $needles);
        }
        
	/**
	 * Method to get the url to the a channel.
         * 
         * @access  public
         * @static
         * @param   integer  $id      The id of the content.
         * @param   array    $params  An array of additional url parameters.
         * @return  string   The url to the content.
	 */
	public static function getChannelRoute($id, $params = array())
	{
                if ($id == 0)
                {
                        return '#';
                }
                
		$needles = array(
			'channel'  => array((int) $id),
			'channels'  => null
		);

                return self::_buildUrl('index.php?option=com_hwdmediashare&view=channel&id=' . $id, $params, $needles);
        }
        
	/**
	 * Method to get the url to the users list.
         * 
         * @access  public
         * @static
         * @param   array   $params  An array of additional url parameters.
         * @return  string  The url to the content.
	 */
	public static function getChannelsRoute($params = array())
	{
		$needles = array(
			'channels'  => null
		);
                
                return self::_buildUrl('index.php?option=com_hwdmediashare&view=channels', $params, $needles);
	}

	/**
	 * Method to get the url to the account page, 'my media' layout.
         * 
         * @access  public
         * @static
         * @param   array   $params  An array of additional url parameters.
         * @return  string  The url to the content.
	 */
	public static function getMyMediaRoute($params = array())
	{                
		$needles = array(
			'account'  => null
		);
                
                $link = self::_buildUrl('index.php?option=com_hwdmediashare&view=account&layout=media', $params, $needles);
	} 
        
	/**
	 * Method to get the url to the upload page.
         * 
         * @access  public
         * @static
         * @param   array   $params  An array of additional url parameters.
         * @return  string  The url to the content.
	 */
	public static function getUploadRoute($params = array())
	{
		$needles = array(
			'upload'  => null
		);
                
                return self::_buildUrl('index.php?option=com_hwdmediashare&view=upload', $params, $needles);
	}        

	/**
	 * Method to get the url to the search page.
         * 
         * @access  public
         * @static
         * @return  string  The url to the content.
	 */
	public static function getSearchRoute($params=array())
	{
		$needles = array(
			'search'  => null
		);

                return self::_buildUrl('index.php?option=com_hwdmediashare&view=search', $params, $needles);
	}

	/**
	 * Method to build an url based on parmaeters.
         * 
         * @access  protected
         * @static
         * @param   string  $link     The current link.
         * @param   array   $params   An array of additional url parameters.
         * @param   array   $needles  An array of needles used to located an appropriate menu link.
         * @return  string  The completed url.
	 */
	protected static function _buildUrl($link, $params = array(), $needles = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication();
                
                foreach($params as $key => $param)
                {
                        $link.= '&'.$key.'='.$param;
                }
                
                if ($item = self::_findItem($needles)) 
                {
			$link.= '&Itemid='.$item;
		}
		elseif ($item = self::_findItem()) 
                {
			$link.= '&Itemid='.$item;
		}
                
                $link .= $app->input->get('tmpl', '', 'word') == 'component' ? '&tmpl=component' : '';
                
                return $link;
        }
        
	/**
	 * Method to locate menu items based on view names.
         * 
         * @access  protected
         * @static
         * @param   array   $needles    An array of needles used to located an appropriate menu link.
         * @return  mixed   The ID of a menu link on success, or false on fail.
	 */  
	protected static function _findItem($needles = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication();
		$menus = $app->getMenu('site');

                // Get HWD config.
                JLoader::register('hwdMediaShareFactory', JPATH_ROOT.'/components/com_hwdmediashare/libraries/factory.php');
                $hwdms  = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                // Check for menu binding configuration values for media types.
		if ($needles)
		{
                        foreach ($needles as $view => $ids)
                        {
                                if ($view == 'mediaitem' && is_array($ids))
                                {                                    
                                        // Get a table instance.
                                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                                        $table = JTable::getInstance('Media', 'hwdMediaShareTable');
                                        
                                        // Attempt to load the table row.
                                        $return = $table->load(reset($ids));

                                        // Check for a table object error.
                                        if ($return === false && $table->getError())
                                        {
                                                return null;
                                        }

                                        $properties = $table->getProperties(1);
                                        $item = JArrayHelper::toObject($properties, 'JObject');
                                        
                                        // Get the categories.
                                        hwdMediaShareFactory::load('category');
                                        $cat = hwdMediaShareCategory::getInstance();
                                        $categories = $cat->load($item);

                                        // If media is assigned only to one category, then search for associated menu link.
                                        if (count($categories) == 1)
                                        {
                                                $needles = array(
                                                        'category'  => array(reset($categories)->id)
                                                );
                
                                                if ($category = self::_findItem($needles)) 
                                                {
                                                        return $category;
                                                }                                            
                                        }
                                        
                                        // Get the media type.
                                        hwdMediaShareFactory::load('media');
                                        $type = hwdMediaShareMedia::loadMediaType($item);

                                        if ($config->get('menu_bind_'.$view.$type) > 1) return $config->get('menu_bind_'.$view.$type);
                                }
                        }  
                }

		// Prepare the reverse lookup array.
		if (self::$lookup === null)
		{
			self::$lookup = array();

			$component	= JComponentHelper::getComponent('com_hwdmediashare');
			$items		= $menus->getItems('component_id', $component->id);
			foreach ($items as $item)
			{
				if (isset($item->query) && isset($item->query['view']))
				{
					$view = $item->query['view'];
                                       
					if (isset($item->query['id'])) 
                                        {                                         
                                                if (!isset(self::$lookup[$view])) 
                                                {
                                                        self::$lookup[$view] = array();
                                                } 						
						self::$lookup[$view][$item->query['id']] = $item->id;
					}
                                        else
                                        {
                                                self::$lookup[$view] = $item->id;
                                        }
				}
			}
		}

		if ($needles)
		{
			foreach ($needles as $view => $ids)
			{
				if (isset(self::$lookup[$view]))
				{
                                        if (is_array(self::$lookup[$view]))
                                        {
                                                foreach($ids as $id)
                                                {
                                                        if (isset(self::$lookup[$view][(int)$id])) 
                                                        {
                                                                return self::$lookup[$view][(int)$id];
                                                        }
                                                }
                                        }
                                        else
                                        {
                                                if (isset(self::$lookup[$view]))
                                                {
                                                        return self::$lookup[$view];
                                                }                                            
                                        }
				}
			}
		}
		else
		{
			$active = $menus->getActive();
			if ($active && $active->component == 'com_hwdmediashare') 
                        {
				return $active->id;
			}
		}

		return null;
	}
}
