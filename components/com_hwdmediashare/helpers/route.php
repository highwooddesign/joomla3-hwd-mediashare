<?php
/**
 * @version    SVN $Id: route.php 1620 2013-08-14 13:55:49Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      09-Nov-2011 16:42:14
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla component helper library
jimport('joomla.application.component.helper');

/**
 * hwdMediaShare Route Helper
 *
 * @package	hwdMediaShare
 * @since       0.1
 */
abstract class hwdMediaShareHelperRoute
{
	protected static $lookup;

	/**
	 * Route the user on entry
	 */
	public static function entry()
	{
                // Bypass this check if we are processing a Flash upload (need to be able to recreate sessions as guest)
                if (JRequest::getVar('task') == 'addmedia.upload' && JRequest::getWord('format') == 'raw')
                {
                        return true;
                }
                                
                // Get user
                $user	= JFactory::getUser();
                
                // Get HWDMediaShare config
                $hwdms  = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // Load content router for redirects
                JLoader::register('ContentHelperRoute', JPATH_ROOT.'/components/com_content/helpers/route.php');

                if ($config->get('offline') == 1)
                {
                    JFactory::getApplication()->enqueueMessage( JText::_( 'COM_HWDMS_ERROR_NOAUTHORISED_OFFLINE' ) ); 
                    JFactory::getApplication()->redirect( $config->get('offline_redirect') > 0 ? ContentHelperRoute::getArticleRoute($config->get('offline_redirect')) : 'index.php' );
                }

                if (!in_array($config->get('default_access'), $user->getAuthorisedViewLevels()))
                {
                    JFactory::getApplication()->enqueueMessage( JText::_( 'COM_HWDMS_ERROR_NOAUTHORISED_GALLERY' ) ); 
                    JFactory::getApplication()->redirect( $config->get('no_access_redirect') > 0 ? ContentHelperRoute::getArticleRoute($config->get('no_access_redirect')) : 'index.php' );
                }
                
                // Set variables to check for enabled features
                $allow = true;
                
                if (!$config->get('enable_categories') && (JRequest::getVar('view') == 'categories' || JRequest::getVar('view') == 'category' || JRequest::getVar('view') == 'categoryform')) $allow = false;
                if (!$config->get('enable_albums') && (JRequest::getVar('view') == 'albums' || JRequest::getVar('view') == 'album' || JRequest::getVar('view') == 'albumform')) $allow = false;
                if (!$config->get('enable_groups') && (JRequest::getVar('view') == 'groups' || JRequest::getVar('view') == 'group' || JRequest::getVar('view') == 'groupform')) $allow = false;
                if (!$config->get('enable_user_channels') && (JRequest::getVar('view') == 'users' || JRequest::getVar('view') == 'user' || JRequest::getVar('view') == 'userform')) $allow = false;
                if (!$config->get('enable_playlists') && (JRequest::getVar('view') == 'playlists' || JRequest::getVar('view') == 'playlist' || JRequest::getVar('view') == 'playlistform')) $allow = false;
                
                if (!$allow)
                {
                        JFactory::getApplication()->enqueueMessage( JText::_('COM_HWDMS_ERROR_FEATURE_DISABLED') );
                        JFactory::getApplication()->redirect( $config->get('no_access_redirect') > 0 ? ContentHelperRoute::getArticleRoute($config->get('no_access_redirect')) : hwdMediaShareHelperRoute::getMediaRoute() );
                }
	}
        
        /**
	 * @param	int	The route of the content item
	 */
	public static function getMediaItemRoute($id, $params=array(), $associate=true)
	{
		$needles = array(
			'mediaitem'  => array((int) $id),
			'media'  => null
		);
                
                $link = 'index.php?option=com_hwdmediashare&view=mediaitem&id='. $id;

                foreach($params as $key => $param)
                {
                        $link.= '&'.$key.'='.$param;
                }
                
                // Get inputs
                $option = JFactory::getApplication()->input->get('option');
                $view = JFactory::getApplication()->input->get('view');
                $id = JFactory::getApplication()->input->get('id');
                if ($associate && $option == 'com_hwdmediashare' && $view == 'category' && $id)
                {
                        $link.= '&category_id='.$id;
                } 
                else if ($associate && $category_id = JRequest::getInt('category_id'))
                {
                        $link.= '&category_id='.$category_id;
                }
                else if ($associate && $option == 'com_hwdmediashare' && $view == 'playlist' && $id)
                {
                        $link.= '&playlist_id='.$id;
                }
                else if ($associate && $playlist_id = JRequest::getInt('playlist_id'))
                {
                        $link.= '&playlist_id='.$playlist_id;
                }                 
                else if ($associate && $option == 'com_hwdmediashare' && $view == 'album' && $id)
                {
                        $link.= '&album_id='.$id;
                }
                else if ($associate && $album_id = JRequest::getInt('album_id'))
                {
                        $link.= '&album_id='.$album_id;
                }                
                else if ($associate && $option == 'com_hwdmediashare' && $view == 'group' && $id)
                {
                        $link.= '&group_id='.$id;
                }  
                else if ($associate && $group_id = JRequest::getInt('group_id'))
                {
                        $link.= '&group_id='.$group_id;
                }  
                
                if ($item = self::_findItem($needles)) 
                {
			$link .= '&Itemid='.$item;
		}
		elseif ($item = self::_findItem()) 
                {
			$link .= '&Itemid='.$item;
		}
                
                return $link;
	}
        
	/**
	 * @param	int	The route of the content item
	 */
	public static function getMediaModalRoute($id)
	{
		$needles = array(
			'mediaitem'  => array((int) $id),
			'media'  => null
		);
                
                $link = 'index.php?option=com_hwdmediashare&view=mediaitem&layout=modal&tmpl=component&mediaitem_size=500&id='. $id;
                
                if ($item = self::_findItem($needles)) 
                {
			$link .= '&Itemid='.$item;
		}
		elseif ($item = self::_findItem()) 
                {
			$link .= '&Itemid='.$item;
		}
                
                return $link;
	}
        
	/**
	 * @param	int	The route of the content item
	 */
	public static function getAlbumRoute($id, $display = null)
	{
		$needles = array(
			'album'  => array((int) $id),
			'albums'  => null
		);
                
                $link = 'index.php?option=com_hwdmediashare&view=album&id='. $id;
                $link.= (!empty($display) ? '&display='.$display : '');

                if ($item = self::_findItem($needles)) 
                {
			$link .= '&Itemid='.$item;
		}
		elseif ($item = self::_findItem()) 
                {
			$link .= '&Itemid='.$item;
		}
                
                return $link;
	}
        
        /**
	 * @param	int	The route of the content item
	 */
	public static function getGroupRoute($id, $display = null)
	{
		$needles = array(
			'group'  => array((int) $id),
			'groups'  => null
		);
                
                $link = 'index.php?option=com_hwdmediashare&view=group&id='. $id;
                $link.= (!empty($display) ? '&display='.$display : '');

                if ($item = self::_findItem($needles)) 
                {
			$link.= '&Itemid='.$item;
		}
		elseif ($item = self::_findItem()) 
                {
			$link.= '&Itemid='.$item;
		}
                
                return $link;
	}
        
        /**
	 * @param	int	The route of the content item
	 */
	public static function getPlaylistRoute($id)
	{
		$needles = array(
			'playlist'  => array((int) $id),
			'playlists'  => null
		);
                
                $link = 'index.php?option=com_hwdmediashare&view=playlist&id='. $id;

                if ($item = self::_findItem($needles)) 
                {
			$link.= '&Itemid='.$item;
		}
		elseif ($item = self::_findItem()) 
                {
			$link.= '&Itemid='.$item;
		}
                
                return $link;
	}
        
        /**
	 * @param	int	The route of the content item
	 */
	public static function getCategoryRoute($id, $display = null)
	{
		$needles = array(
			'category'  => array((int) $id),
			'categories'  => null
		);
                
                $link = 'index.php?option=com_hwdmediashare&view=category&id='. $id;
                $link.= (!empty($display) ? '&display='.$display : '');

                if ($item = self::_findItem($needles)) 
                {
			$link.= '&Itemid='.$item;
		}
		elseif ($item = self::_findItem()) 
                {
			$link.= '&Itemid='.$item;
		}
                
                return $link;
	}
        
        /**
	 * @param	int	The route of the content item
	 */
	public static function getUserRoute($id, $display = null)
	{
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                if ($id == 0)
                {
                        return '#top';
                }
                else if ($config->get('community_link') == 'cb')
                {
                        return JRoute::_('index.php?option=com_comprofiler&task=userProfile&user='.$id);
                }
                else if ($config->get('community_link') == 'jomsocial' && file_exists(JPATH_ROOT.'/components/com_community/libraries/core.php'))
                {
                        include_once(JPATH_ROOT.'/components/com_community/libraries/core.php');
                        return CRoute::_('index.php?option=com_community&view=profile&userid='.$id);
                }
                else if ($config->get('community_link') == 'jomwall')
                {
                        return JRoute::_('index.php?option=com_awdwall&view=awdwall&layout=mywall&wuid='.$id);
                }
                
		$needles = array(
			'user'  => array((int) $id),
			'users'  => null
		);
                
                $link = 'index.php?option=com_hwdmediashare&view=user&id='. $id;
                $link.= (!empty($display) ? '&display='.$display : '');

                if ($item = self::_findItem($needles)) 
                {
			$link.= '&Itemid='.$item;
		}
		elseif ($item = self::_findItem()) 
                {
			$link.= '&Itemid='.$item;
		}
                
                return $link;
        }
        
        /**
	 * @param	int	The route of the content item
	 */
	public static function getCategoriesRoute($display = null)
	{
		$needles = array(
			'categories'  => null
		);
                
                $link = 'index.php?option=com_hwdmediashare&view=categories';
                $link.= (!empty($display) ? '&display='.$display : '');

                if ($item = self::_findItem($needles)) 
                {
			$link.= '&Itemid='.$item;
		}
		elseif ($item = self::_findItem()) 
                {
			$link.= '&Itemid='.$item;
		}
                
                return $link;
	}
        
        /**
	 * @param	int	The route of the content item
	 */
	public static function getMediaRoute($params=array())
	{
		$needles = array(
			'media'  => null
		);
                
                $link = 'index.php?option=com_hwdmediashare&view=media';
                
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
                
                return $link;
	}
        
	/**
	 * @param	int	The route of the content item
	 */
	public static function getSlideshowRoute($params=array())
	{
		$needles = array(
			'media'  => null
		);
                
                $link = 'index.php?option=com_hwdmediashare&view=slideshow';
                
                foreach($params as $key => $param)
                {
                        $link.= '&'.$key.'='.$param;
                }
                
                // Add tmpl=component
                $link.= '&tmpl=component';
                // Add return link
                $link.= '&return=' .  base64_encode(JFactory::getURI()->toString());
                
                if ($item = self::_findItem($needles)) 
                {
			$link .= '&Itemid='.$item;
		}
		elseif ($item = self::_findItem()) 
                {
			$link .= '&Itemid='.$item;
		}
                
                return $link;
	}
        
        /**
	 * @param	int	The route of the content item
	 */
	public static function getAlbumsRoute($display = null)
	{
		$needles = array(
			'albums'  => null
		);
                
                $link = 'index.php?option=com_hwdmediashare&view=albums';
                $link.= (!empty($display) ? '&display='.$display : '');

                if ($item = self::_findItem($needles)) 
                {
			$link.= '&Itemid='.$item;
		}
		elseif ($item = self::_findItem()) 
                {
			$link.= '&Itemid='.$item;
		}
                
                return $link;
	}
        
        /**
	 * @param	int	The route of the content item
	 */
	public static function getGroupsRoute($display = null)
	{
		$needles = array(
			'groups'  => null
		);
                
                $link = 'index.php?option=com_hwdmediashare&view=groups';
                $link.= (!empty($display) ? '&display='.$display : '');

                if ($item = self::_findItem($needles)) 
                {
			$link.= '&Itemid='.$item;
		}
		elseif ($item = self::_findItem()) 
                {
			$link.= '&Itemid='.$item;
		}
                
                return $link;
	}
        
        /**
	 * @param	int	The route of the content item
	 */
	public static function getPlaylistsRoute($display = null)
	{
		$needles = array(
			'playlists'  => null
		);
                
                $link = 'index.php?option=com_hwdmediashare&view=playlists';
                $link.= (!empty($display) ? '&display='.$display : '');

                if ($item = self::_findItem($needles)) 
                {
			$link.= '&Itemid='.$item;
		}
		elseif ($item = self::_findItem()) 
                {
			$link.= '&Itemid='.$item;
		}
                
                return $link;
	}
        
        /**
	 * @param	int	The route of the content item
	 */
	public static function getUsersRoute($display = null)
	{
		$needles = array(
			'users'  => null
		);
                
                $link = 'index.php?option=com_hwdmediashare&view=users';
                $link.= (!empty($display) ? '&display='.$display : '');

                if ($item = self::_findItem($needles)) 
                {
			$link.= '&Itemid='.$item;
		}
		elseif ($item = self::_findItem()) 
                {
			$link.= '&Itemid='.$item;
		}
                
                return $link;
	}

        /**
	 * @param	int	The route of the content item
	 */
	public static function getMyMediaRoute()
	{
		$needles = array(
			'account'  => null
		);
                
                $link = 'index.php?option=com_hwdmediashare&view=account&layout=media';
                
                $tmpl = JRequest::getWord( 'tmpl', '' );
                $template = ($tmpl == 'component' ? '&tmpl=component' : '');
                $link.= $template;
                
                if ($item = self::_findItem($needles)) 
                {
			$link.= '&Itemid='.$item;
		}
		elseif ($item = self::_findItem()) 
                {
			$link.= '&Itemid='.$item;
		}
                
                return $link;
	} 
        
        /**
	 * @param	int	The route of the content item
	 */
	public static function getUploadRoute()
	{
		$needles = array(
			'upload'  => null
		);
                
                $link = 'index.php?option=com_hwdmediashare&view=upload';
                
                $tmpl = JRequest::getWord( 'tmpl', '' );
                $template = ($tmpl == 'component' ? '&tmpl=component' : '');
                $link.= $template;
                
                if ($item = self::_findItem($needles)) 
                {
			$link.= '&Itemid='.$item;
		}
		elseif ($item = self::_findItem()) 
                {
			$link.= '&Itemid='.$item;
		}
                
                return $link;
	}        

        /**
	 * @param	int	The route of the content item
	 */
	public static function getSearchRoute($params=array())
	{
		$needles = array(
			'search'  => null
		);
                
                $link = 'index.php?option=com_hwdmediashare&view=search';
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
                
                return $link;
	}
        
        /**
	 * @param	int	The route of the content item
	 */
	public static function getSelfRoute($display = null)
	{
		// @TODO: Review reason the id isn't in 3.0 when it is in 2.5
		// Joomla 3.0 may not have these set in JRequest so we set them 
		if (JRequest::getCmd('view'))   JRequest::setVar('view', JRequest::getCmd('view'));
		if (JRequest::getInt('id'))     JRequest::setVar('id', JRequest::getInt('id'));
                
                $query_string = http_build_query(JRequest::get( 'get' ));
                $query = array();
                if (isset($query_string))
                {
                        parse_str($query_string, $vars);
                        foreach ($vars as $i => $var)
                        {
                                if ($i != 'details')
                                {
                                        $query[] = $i . '=' . $var;
                                }                                
                        }
                }
                return 'index.php?' . implode('&', $query) . '&display='. $display;
	}
        
	protected static function _findItem($needles = null)
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu('site');
                
                // Temp fix @TODO: figure this out
                // The JLoader was added as this was missing when called from some plugins/modules, but then started to generate problems
                // in JomSocial 2.6.2
                //JLoader::register('hwdMediaShareFactory', JPATH_ROOT.'/components/com_hwdmediashare/libraries/factory.php');
                require_once( JPATH_ROOT.'/components/com_hwdmediashare/libraries/factory.php' );

                // Serioulsy?! What is this JomSocial?
                JTable::addIncludePath(JPATH_ROOT.'/components/com_community/tables');

                // Get HWDMediaShare config
                $hwdms  = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                // Check if a menu has been assigned for the needle in the HWD config
		if ($needles)
		{
                        foreach ($needles as $view => $ids)
                        {
                                if ($view == 'mediaitem' && isset($ids[0]))
                                {
                                        //@TODO: Check impact on performace
                                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                                        $table = JTable::getInstance('Media', 'hwdMediaShareTable');
                                        $table->load($ids[0]);
                                        $properties = $table->getProperties(1);
                                        $object = JArrayHelper::toObject($properties, 'JObject');
                                        hwdMediaShareFactory::load('media');
                                        $type = hwdMediaShareMedia::loadMediaType($object);
                                        $view.= $type;                                
                                }

                                if ($config->get('menu_bind_'.$view) > 1) return $config->get('menu_bind_'.$view);
                                // Only search leading needle
                                break;
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
                                                // Possible extension to firstly check if active is a valid link
                                                // if ($active && $active->component == 'com_hwdmediashare' && $active->query['view'] == $view)
                                                // {
                                                //        return $active->id;
                                                // }
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
