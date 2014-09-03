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

class hwdMediaShareRouter extends JComponentRouterBase
{
	/**
	 * Build the route for the com_hwdmediashare component.
	 *
         * @access  public
	 * @param   array  &$query  An array of URL arguments
	 * @return  array  The URL arguments to use to assemble the subsequent URL.
	 */
	public function build(&$query)
        {
                // Initialise variables.
                $db = JFactory::getDbo();            
                $segments = array();

                // Get a menu item based on Itemid or currently active.
                $app = JFactory::getApplication();
                $menu = $app->getMenu();
                $params = JComponentHelper::getParams('com_hwdmediashare');
                $advanced = $params->get('sef_advanced_link', 1);

                // We need a menu item.  Either the one specified in the query, or the current active one if none specified.
                if (empty($query['Itemid']))
                {
                        $menuItem = $menu->getActive();
                        $menuItemGiven = false;
                }
                else
                {
                        $menuItem = $menu->getItem($query['Itemid']);
                        $menuItemGiven = true;
                }

                if (isset($query['view']))
                {
                        $view = $query['view'];
                }
                else
                {
                        // We need to have a view in the query or it is an invalid URL.
                        return $segments;
                }

                // Check if this is an item view attached to a menu item.
                if (($menuItem instanceof stdClass) && isset($menuItem->query['view']) && isset($menuItem->query['id']) && isset($query['id']))
                {
                        if ($menuItem->query['view'] == $query['view'] && $menuItem->query['id'] == intval($query['id']))
                        {                            
                                unset($query['view']);
                                unset($query['id']);
                                return $segments;
                        }
                }
 
                // Check if this is a list view that is aattached to a menu item.
                if (($menuItem instanceof stdClass) && isset($menuItem->query['view']) && !isset($menuItem->query['id']))
                {
                        if ($menuItem->query['view'] == $query['view'])
                        {
                                unset($query['view']);
                                return $segments;
                        }
                }

                $listViews = array('albums', 'categories', 'groups', 'media', 'playlists', 'users');
                $itemViews = array('album', 'category', 'group', 'mediaitem', 'playlist', 'user');
                $editViews = array('albumform', 'categoryform', 'groupform', 'mediaform', 'playlistform', 'userform');

                if(in_array($view, $listViews))
                {
                        $segments[] = $this->translate($view);
                        unset($query['view']);
                }
                elseif (in_array($view, $itemViews) || in_array($view, $editViews))
                {                  
                        // Check if this is an item view that is attached to its own list view. We won't add the
                        // view segment, then check for this situation and parse the URL appropriately.
                        if (isset($menuItem->query['view']) && 
                           (($menuItem->query['view'] == 'albums' && $view == 'album')
                         || ($menuItem->query['view'] == 'categories' && $view == 'category')
                         || ($menuItem->query['view'] == 'groups' && $view == 'group')
                         || ($menuItem->query['view'] == 'media' && $view == 'mediaitem')
                         || ($menuItem->query['view'] == 'playlists' && $view == 'playlist')
                         || ($menuItem->query['view'] == 'users' && $view == 'user')))
                        {
                                unset($query['view']);
                        }
                        elseif (isset($menuItem->query['view']) && 
                           (($menuItem->query['view'] == 'albums' && $view == 'albumform')
                         || ($menuItem->query['view'] == 'categories' && $view == 'categoryform')
                         || ($menuItem->query['view'] == 'groups' && $view == 'groupform')
                         || ($menuItem->query['view'] == 'media' && $view == 'mediaform')
                         || ($menuItem->query['view'] == 'playlists' && $view == 'playlistform')
                         || ($menuItem->query['view'] == 'users' && $view == 'userform')))
                        {                            
                                if (isset($query['id']) && $query['id'] > 0)
                                {
                                        $segments[] = $this->translate('edit');
                                }
                                else
                                {
                                        $segments[] = $this->translate('new');
                                }
                                unset($query['view']);
                        }
                        else
                        {
                                if ($view == 'mediaitem' && isset($query['id']))
                                {
                                        unset($query['view']);
                                }
                                $segments[] = $this->translate($view);
                                unset($query['view']);
                        }

                        // Setup an array to validate the alias. 
                        $routing = array(
                            'album' => array('table' => 'hwdms_albums', 'view' => 'album'),
                            'albumform' => array('table' => 'hwdms_albums', 'view' => 'album'),
                            'category' => array('table' => 'categories', 'view' => 'category'),
                            'categoryform' => array('table' => 'categories', 'view' => 'category'),
                            'group' => array('table' => 'hwdms_groups', 'view' => 'group'),
                            'groupform' => array('table' => 'hwdms_groups', 'view' => 'group'),                            
                            'mediaitem' => array('table' => 'hwdms_media', 'view' => 'mediaitem'),
                            'mediaform' => array('table' => 'hwdms_media', 'view' => 'mediaitem'),
                            'playlist' => array('table' => 'hwdms_playlists', 'view' => 'playlist'),
                            'playlistform' => array('table' => 'hwdms_playlists', 'view' => 'playlist'),
                            'user' => array('table' => 'hwdms_users', 'view' => 'user'),
                            'userform' => array('table' => 'hwdms_users', 'view' => 'user')
                        );

                        if (isset($query['id']))
                        {
                                // Make sure we have the id and the alias.
                                if (strpos($query['id'], ':') === false && !empty($routing[$view]['table']))
                                {
                                        $aquery = $db->getQuery(true)
                                                 ->select('alias')
                                                 ->from('#__' . $routing[$view]['table'])
                                                 ->where('id = ' . $db->quote((int) $query['id']));
                                        try
                                        {                
                                                $db->setQuery($aquery);
                                                $alias = $db->loadResult();
                                                $query['id'] = (!empty($alias) ? $query['id'].':'.$alias : (int) $query['id']);
                                        }
                                        catch (Exception $e)
                                        {
                                                $query['id'] = (int) $query['id'];
                                                // echo $e->getMessage();
                                        }     
                                }
                                
                                // Check for advanced link option, this removes the ID from the slug value.
                                if ($advanced && strpos($query['id'], ':') !== false) 
                                {
                                        list($tmp, $id) = explode(':', $query['id'], 2);
                                }
                                else
                                {
                                        $id = $query['id'];
                                }

                                $segments[] = $id;
                                unset($query['id']);
                        } 
                        else
                        {
                                // We should have id set for this view.  If we don't, it is an error.
                                return $segments;
                        }
                }
                else
                {
                        $segments[] = $this->translate($view);
                        unset($query['view']);
                }
                
                // If the layout is specified and it is the same as the layout in the menu item, we
                // unset it so it doesn't go into the query string.
                if (isset($query['layout']))
                {
                        if ($menuItemGiven && isset($menuItem->query['layout']))
                        {
                                if ($query['layout'] == $menuItem->query['layout'])
                                {

                                        unset($query['layout']);
                                }
                        }
                        else
                        {
                                if ($query['layout'] == 'default')
                                {
                                        unset($query['layout']);
                                }
                        }
                }

                $variables = array(
                    'album' => 'album_id',
                    'category' => 'category_id',
                    'group' => 'group_id',
                    'playlist' => 'playlist_id',
                );
              
                foreach($variables as $key => $variable)
                {
                        if(isset($query[$variable]))
                        {
                                $segments[] = $this->translate($key) . ':' . $query[$variable];
                                unset($query[$variable]);
                        }
                }

                return $segments;
        }

	/**
	 * Parse the segments of a URL.
	 *
         * @access  public
	 * @param   array  &$segments  The segments of the URL to parse.
	 * @return  array  The URL attributes to be used by the application.
	 */
	public function parse(&$segments)
	{
                // Initialise variables.
                $db = JFactory::getDbo();            
                $vars = array();

                // Get the active menu item.
                $app = JFactory::getApplication();
                $menu = $app->getMenu();
                $item = $menu->getActive();
                $params = JComponentHelper::getParams('com_hwdmediashare');
                $advanced = $params->get('sef_advanced_link', 1);

                // Count route segments.
                $count = count($segments);

                if (!isset($item))
                {
                        // Standard routing for items. If we don't pick up an Itemid then we get the view from the segments
                        // the first segment is the view and the last segment is the id of the item.
                        $vars['view'] = $this->translate($segments[0], true);
                        
                        // Only define an id value if there is more than one segment.
                        if ($count > 1) $vars['id'] = $segments[$count - 1];
                }
                else
                {
                        $routing = array(
                            'account'       => array(),
                            'album'         => array('table' => 'hwdms_albums', 'listview' => 'albums', 'itemview' => 'album', 'formview' => 'albumform'),
                            'albumform'     => array('table' => 'hwdms_albums', 'listview' => 'albums', 'itemview' => 'album', 'formview' => 'albumform'),
                            'albummedia'    => array(),
                            'albums'        => array('table' => 'hwdms_albums', 'listview' => 'albums', 'itemview' => 'album', 'formview' => 'albumform'),
                            'categories'    => array('table' => 'categories', 'listview' => 'categories', 'itemview' => 'category', 'formview' => 'categoryform'),
                            'category'      => array('table' => 'categories', 'listview' => 'categories', 'itemview' => 'category', 'formview' => 'categoryform'),
                            'categoryform'  => array('table' => 'categories', 'listview' => 'categories', 'itemview' => 'category', 'formview' => 'categoryform'),
                            'discover'      => array(),
                            'group'         => array('table' => 'hwdms_groups', 'listview' => 'groups', 'itemview' => 'group', 'formview' => 'groupform'),
                            'groupform'     => array('table' => 'hwdms_groups', 'listview' => 'groups', 'itemview' => 'group', 'formview' => 'groupform'),
                            'groupmedia'    => array(),
                            'groupmembers'  => array(),
                            'groups'        => array('table' => 'hwdms_groups', 'listview' => 'groups', 'itemview' => 'group', 'formview' => 'groupform'),
                            'media'         => array('table' => 'hwdms_media', 'listview' => 'media', 'itemview' => 'mediaitem', 'formview' => 'mediaform'),
                            'mediaform'     => array('table' => 'hwdms_media', 'listview' => 'media', 'itemview' => 'mediaitem', 'formview' => 'mediaform'),
                            'mediaitem'     => array('table' => 'hwdms_media', 'listview' => 'media', 'itemview' => 'mediaitem', 'formview' => 'mediaform'),
                            'playlist'      => array('table' => 'hwdms_playlists', 'listview' => 'playlists', 'itemview' => 'playlist', 'formview' => 'playlistform'),
                            'playlistform'  => array('table' => 'hwdms_playlists', 'listview' => 'playlists', 'itemview' => 'playlist', 'formview' => 'playlistform'),
                            'playlistmedia' => array(),
                            'playlists'     => array('table' => 'hwdms_playlists', 'listview' => 'playlists', 'itemview' => 'playlist', 'formview' => 'playlistform'),
                            'search'        => array(),
                            'slideshow'     => array(),
                            'upload'        => array(),
                            'user'          => array('table' => 'hwdms_users', 'listview' => 'users', 'itemview' => 'user', 'formview' => 'userform'),
                            'userform'      => array('table' => 'hwdms_users', 'listview' => 'users', 'itemview' => 'user', 'formview' => 'userform'),
                            'users'          => array('table' => 'hwdms_users', 'listview' => 'users', 'itemview' => 'user', 'formview' => 'userform'),                      
                        );

                        if (isset($segments[0]) && isset($segments[1]) && $segments[0] == $this->translate('edit'))
                        {
                                $vars['view'] = $routing[$item->query['view']]['formview'];
                                if ($advanced)
                                {
                                        $alias = JApplication::stringURLSafe($segments[1]);
                                        $aquery = $db->getQuery(true)
                                                 ->select('id')
                                                 ->from('#__' . $routing[$item->query['view']]['table'])
                                                 ->where('alias = ' . $db->quote($alias));
                                        try
                                        {                                        
                                                $db->setQuery($aquery);
                                                $vars['id'] = (int) $db->loadResult();
                                        }
                                        catch (Exception $e)
                                        {
                                                // echo $e->getMessage();
                                                JError::raiseError(404, JText::_('COM_HWDMS_ERROR_ITEM_NOT_FOUND'));
                                                return $vars;                
                                        }
                                }
                                else
                                {
                                        if (strpos($segments[1], ':') === false)
                                        {                            
                                                $vars['id'] = (int) $segments[1]; 
                                        }
                                        else
                                        {
                                                list($id, $alias) = explode(':', $segments[1], 2);
                                                $vars['id'] = (int) $id;
                                        }
                                }                                  
                        }
                        elseif (isset($segments[0]) && $segments[0] == 'new')
                        {
                                $vars['view'] = $routing[$item->query['view']]['formview'];
                        }
                        else
                        {         
                                if (isset($segments[0]) && isset($segments[1]) && isset($routing[$this->translate($segments[0], true)]))
                                {
                                        $alias = JApplication::stringURLSafe($segments[1]); 
                                        $item->query['view'] = $this->translate($segments[0], true);   
                                }
                                else
                                {
                                        $alias = JApplication::stringURLSafe($segments[0]); 
                                }
                                
                                if ($advanced)
                                {
                                        $aquery = $db->getQuery(true)
                                                 ->select('id')
                                                 ->from('#__' . $routing[$item->query['view']]['table'])
                                                 ->where('alias = ' . $db->quote($alias));
                                        try
                                        {     
                                                $db->setQuery($aquery);
                                                $vars['view'] = $routing[$item->query['view']]['itemview'];
                                                $vars['id'] = (int) $db->loadResult();
                                        }
                                        catch (Exception $e)
                                        {
                                                // echo $e->getMessage();
                                                JError::raiseError(404, JText::_('COM_HWDMS_ERROR_ITEM_NOT_FOUND'));
                                                return $vars; 
                                        } 

                                        // Check if ID still hasn't been defined.
                                        if (!isset($vars['id']) || $vars['id'] == 0)
                                        {
                                                // Check if alias could be the ID.
                                                if ((int) $alias == 0)
                                                {
                                                        JError::raiseError(404, JText::_('COM_HWDMS_ERROR_ITEM_NOT_FOUND'));
                                                        return $vars;                                                      
                                                }
                                                else
                                                {
                                                        $vars['id'] = (int) $alias;
                                                }
                                        }
                                }
                                else
                                {
                                        if (strpos($segments[0], ':') === false)
                                        {                            
                                                $vars['view'] = $this->translate($segments[0], true);
                                        }
                                        else
                                        {
                                                list($id, $alias) = explode(':', $segments[0], 2);
                                                $vars['view'] = $routing[$item->query['view']]['itemview'];
                                                $vars['id'] = (int) $id;                                                  
                                        }
                                } 
                        }
                }

                $variables = array(
                    'album' => 'album_id',
                    'category' => 'category_id',
                    'group' => 'group_id',
                    'playlist' => 'playlist_id',
                );
                
                foreach($segments as $segment)
                {
                        if (strpos($segment, ':') !== false)
                        {                            
                                list($key, $value) = explode(':', $segment, 2);
                                if(isset($variables[$key]))
                                {
                                        $vars[$variables[$key]] = $value;                                                  
                                }
                        }
                }

                return $vars;
        }
        
	/**
	 * Translates a term for use with SEF.
	 *
         * @access  public
	 * @param   array    $string   The string to translate.
	 * @param   boolean  $inverse  Inverse trace.
	 * @return  array    The translated string.
	 */
	public function translate($string, $inverse = false)
	{
                // Translate URL strings here.
                $segments = array(
                //  Original           Translated
                    'account'       => 'account',
                    'album'         => 'album',
                    'albumform'     => 'albumform',
                    'albummedia'    => 'albummedia',
                    'albums'        => 'albums',
                    'categories'    => 'categories',
                    'category'      => 'category',
                    'categoryform'  => 'categoryform',
                    'discover'      => 'discover',
                    'group'         => 'group',
                    'groupform'     => 'groupform',
                    'groupmedia'    => 'groupmedia',
                    'groupmembers'  => 'groupmembers',
                    'groups'        => 'groups',
                    'media'         => 'media',
                    'mediaform'     => 'mediaform',
                    'mediaitem'     => 'view',
                    'playlist'      => 'playlist',
                    'playlistform'  => 'playlistform',
                    'playlistmedia' => 'playlistmedia',
                    'playlists'     => 'playlists',
                    'search'        => 'search',
                    'slideshow'     => 'slideshow',
                    'upload'        => 'upload',
                    'user'          => 'user',
                    'userform'      => 'userform',
                    'user'          => 'user',                   
                );

                if (!$inverse && isset($segments[$string]))
                {
                        return JApplication::stringURLSafe($segments[$string]);
                }
                elseif ($inverse && in_array($string, $segments))
                {
                        if ($key = array_search($string, $segments))
                        {
                                return JApplication::stringURLSafe($key);
                        }
                }
                
                return $string;
        }        
}