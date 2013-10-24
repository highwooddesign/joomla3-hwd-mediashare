<?php
/**
 * @version    SVN $Id: router.php 1311 2013-03-20 09:37:47Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      07-Mar-2012 10:27:44
 */

defined('_JEXEC') or die;

/**
 * Build the route for the com_hwdmediashare component
 *
 * @param	array	An array of URL arguments
 * @return	array	The URL arguments to use to assemble the subsequent URL.
 * @since	1.5
 */
function hwdMediaShareBuildRoute(&$query)
{
	$segments	= array();

	// Get a menu item based on Itemid or currently active
	$app		= JFactory::getApplication();
	$menu		= $app->getMenu();
	$params		= JComponentHelper::getParams('com_hwdmediashare');
	$advanced	= $params->get('sef_advanced_link', 0);

	// We need a menu item.  Either the one specified in the query, or the current active one if none specified
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
		// We need to have a view in the query or it is an invalid URL
		return $segments;
	}

	// Are we dealing with an item view that is attached to a menu item?
	if (($menuItem instanceof stdClass) && isset($menuItem->query['view']) && isset($menuItem->query['id']) && isset($query['id']))
        {
                if ($menuItem->query['view'] == $query['view'] && $menuItem->query['id'] == intval($query['id']))
                {
                        unset($query['view']);
                        unset($query['id']);
                        return $segments;
                }
	}
        
        // Are we dealing with a list view that is attached to a menu item?
	if (($menuItem instanceof stdClass) && isset($menuItem->query['view']))
        {
                // Are we dealing with an item view that is attached to a menu with the correct view, but wrong item?
                if ($menuItem->query['view'] == $query['view'] && @$menuItem->query['id'] != @intval($query['id']))
                {
                        // We will continue to set the segments and then check for this situation and parse the URL appropriately
                }
                else if ($menuItem->query['view'] == $query['view'])
                {
                        unset($query['view']);
                        return $segments;
                }
	}

        $listViews = array( 'media' , 'categories' , 'albums' , 'groups' , 'playlists', 'users' );
        $itemViews = array( 'mediaitem' , 'category' , 'album' , 'group' , 'playlist', 'user' );

        if(in_array($view, $listViews))
        {
                $segments[] = $view;
		unset($query['view']);
        }
        else if (in_array($view, $itemViews))
	{
                if (!$menuItemGiven) {
			$segments[] = $view;
		}
                else
                {
                        // Are we dealing with an item view that is attached to it's own list view? We won't add the view segment, then check for this situation and parse the URL appropriately
                        if ($menuItem->query['view'] == 'media' && $view == 'mediaitem') {}
                        else if ($menuItem->query['view'] == 'categories' && $view == 'category') {}
                        else if ($menuItem->query['view'] == 'albums' && $view == 'album') {}
                        else if ($menuItem->query['view'] == 'groups' && $view == 'group') {}
                        else if ($menuItem->query['view'] == 'playlists' && $view == 'playlist') {}
                        else if ($menuItem->query['view'] == 'users' && $view == 'user') {}
                        else
                        {
                                parse_str(str_replace('index.php?', '', $menuItem->link), $linkQuery);                        
                                if (@$linkQuery['option'] != 'com_hwdmediashare' || @$linkQuery['view'] != $view)
                                {
                                        $segments[] = $view;
                                } 
                        }
                }
		unset($query['view']);

                $values = array(
                    'mediaitem' => array( 'table' => 'hwdms_media', 'view' => 'mediaitem' ),
                    'category' => array( 'table' => 'categories', 'view' => 'category' ),
                    'album' => array( 'table' => 'hwdms_albums', 'view' => 'album' ),
                    'group' => array( 'table' => 'hwdms_groups', 'view' => 'group' ),
                    'playlist' => array( 'table' => 'hwdms_playlists', 'view' => 'playlist' ),
                    'user' => array( 'table' => 'hwdms_users', 'view' => 'user' )
                );

                if (isset($query['id'])) {
                        // Make sure we have the id and the alias
                        if (strpos($query['id'], ':') === false && !empty($values[$view]['table'])) {
                                $db = JFactory::getDbo();
                                $aquery = $db->setQuery($db->getQuery(true)
                                        ->select('alias')
                                        ->from('#__'.$values[$view]['table'])
                                        ->where('id='.(int)$query['id'])
                                );
                                $alias = $db->loadResult();
                                $query['id'] = (!empty($alias) ? $query['id'].':'.$alias : $query['id']);
                        }
                } else {
                        // We should have id set for this view.  If we don't, it is an error
                        return $segments;
                }

		if (in_array($view, $itemViews)) {
			if ($advanced) {
				list($tmp, $id) = explode(':', $query['id'], 2);
			}
			else {
				$id = $query['id'];
			}
			$segments[] = $id;
		}
		unset($query['id']);
	}

	// If the layout is specified and it is the same as the layout in the menu item, we
	// unset it so it doesn't go into the query string.
	if (isset($query['layout'])) {
		if ($menuItemGiven && isset($menuItem->query['layout'])) {
			if ($query['layout'] == $menuItem->query['layout']) {

				unset($query['layout']);
			}
		}
		else {
			if ($query['layout'] == 'default') {
				unset($query['layout']);
			}
		}
	}

	return $segments;
}

/**
 * Parse the segments of a URL.
 *
 * @param	array	The segments of the URL to parse.
 *
 * @return	array	The URL attributes to be used by the application.
 * @since	1.5
 */
function hwdMediaShareParseRoute($segments)
{
	$vars = array();

	//Get the active menu item.
	$app	= JFactory::getApplication();
	$menu	= $app->getMenu();
	$item	= $menu->getActive();
	$params = JComponentHelper::getParams('com_hwdmediashare');
	$advanced = $params->get('sef_advanced_link', 0);
	$db = JFactory::getDBO();

	// Count route segments
	$count = count($segments);

	// Standard routing for items. If we don't pick up an Itemid then we get the view from the segments
	// the first segment is the view and the last segment is the id of the item.
	if (!isset($item))
        {
		$vars['view']               = $segments[0];
		// Only define an id value if there is more than one segment
                if ($count > 1) $vars['id'] = $segments[$count - 1];
               
		return $vars;
	}

	// If there is only one segment, then it points to either 1) an item view 2) an item view bound to a different item 3) an item view bound to it's own list view
        // We test it first to see if the id and alias match the expected item
	if ($count == 1)
        {
                $values = array(
                        'media' => array( 'table' => 'hwdms_media', 'view' => 'mediaitem' ),
                        'mediaitem' => array( 'table' => 'hwdms_media', 'view' => 'mediaitem' ),
                        'categories' => array( 'table' => 'categories', 'view' => 'category' ),
                        'category' => array( 'table' => 'categories', 'view' => 'category' ),
                        'albums' => array( 'table' => 'hwdms_albums', 'view' => 'album' ),
                        'album' => array( 'table' => 'hwdms_albums', 'view' => 'album' ),
                        'groups' => array( 'table' => 'hwdms_groups', 'view' => 'group' ),
                        'group' => array( 'table' => 'hwdms_groups', 'view' => 'group' ),
                        'playlists' => array( 'table' => 'hwdms_playlists', 'view' => 'playlist' ),
                        'playlist' => array( 'table' => 'hwdms_playlists', 'view' => 'playlist' ),
                        'users' => array( 'table' => 'hwdms_users', 'view' => 'user' ),
                        'user' => array( 'table' => 'hwdms_users', 'view' => 'user' )
                );

                if (strpos($segments[0], ':') === false)
                {
                        if (isset($values[$segments[0]]['view']))
                        {
                                $vars['view'] = $segments[0];
                        }
                        else
                        {
                                $id = (int)$segments[0];
                                $vars['view'] = $values[$item->query['view']]['view'];
                                $vars['id'] = (int)$id; 
                        }

                        return $vars;
                }
                else
                {
                        list($id, $alias) = explode(':', $segments[0], 2);

                        if (empty($values[$item->query['view']]['table'])) return $vars;

                        $query = 'SELECT alias FROM #__'.$values[$item->query['view']]['table'].' WHERE id = '.(int)$id;
                        $db->setQuery($query);
                        $row = $db->loadObject();

                        if ($row) {
                                if ($row->alias == $alias) {
                                        $vars['view'] = $values[$item->query['view']]['view'];
                                        $vars['id'] = (int)$id;

                                        return $vars;
                                }
                        }
                }
	}

	// If there was more than one segment, then we can determine where the URL points to
	// because the first segment will have the target category id prepended to it.  If the
	// last segment has a number prepended, it is an article, otherwise, it is a category.
	if (!$advanced) {
                $id = (int)$segments[$count - 1];

		if ($id > 0) {
                        $vars['view'] = $segments[0];
			$vars['id'] = $id;
		} else {
                        $vars['view'] = $segments[0];
		}

		return $vars;
	}

	foreach($segments as $segment)
	{
		$segment = str_replace(':', '-', $segment);
	}

	return $vars;
}
