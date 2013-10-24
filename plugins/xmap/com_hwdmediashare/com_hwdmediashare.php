<?php
/**
 * @version             $Id: com_hwdmediashare.php 1670 2013-08-22 15:30:24Z dhorsfall $
 * @copyright           Copyright (C) 2007 - 2009 Joomla! Vargas. All rights reserved.
 * @license             GNU General Public License version 2 or later; see LICENSE.txt
 * @author              Guillermo Vargas (guille@vargas.co.cr)
 */
defined('_JEXEC') or die;

require_once JPATH_SITE . '/components/com_content/helpers/route.php';
require_once JPATH_SITE . '/components/com_content/helpers/query.php';
JLoader::register('hwdMediaShareFactory', JPATH_SITE . '/components/com_hwdmediashare/libraries/factory.php');
JLoader::register('hwdMediaShareHelperRoute', JPATH_SITE . '/components/com_hwdmediashare/helpers/route.php');

/**
 * Handles HWDMediaShare map
 *
 * This plugin is able to expand the categories keeping the right order of the
 * articles acording to the menu settings and the user session data (user state).
 * 
 */
class xmap_com_hwdmediashare
{
    /**
     * This function is called before a menu item is printed. We use it to set the
     * proper uniqueid for the item
     *
     * @param object  Menu item to be "prepared"
     * @param array   The extension params
     *
     * @return void
     * @since  1.2
     */
    static function prepareMenuItem($node, &$params)
    {
        $db = JFactory::getDbo();
        $date =& JFactory::getDate();
        $link_query = parse_url($node->link);
        if (!isset($link_query['query'])) {
            return;
        }

        parse_str(html_entity_decode($link_query['query']), $link_vars);
        $view = JArrayHelper::getValue($link_vars, 'view', '');
        $id = JArrayHelper::getValue($link_vars, 'id', 0);

        switch ($view) {
            case 'media':
                if ($id) {
                    $node->uid = 'com_hwdmediashare.media.' . $id;
                } else {
                    $node->uid = 'com_hwdmediashare.media';
                }
                $node->modified = strtotime($date->format('c'));
                $node->expandible = true;
                break;            
            case 'category':
                if ($id) {
                    $node->uid = 'com_hwdmediashare.category.' . $id;
                } else {
                    $node->uid = 'com_hwdmediashare.category';
                }
                $node->modified = strtotime($date->format('c'));
                $node->expandible = true;
                break;              
        }
    }

    /**
     * Expands a com_hwdmediashare menu item
     *
     * @return void
     * @since  1.0
     */
    static function getTree($xmap, $parent, &$params)
    {
        $db = JFactory::getDBO();
        $app = JFactory::getApplication();
        $user = JFactory::getUser();
        $date =& JFactory::getDate();        
        $result = null;

        $link_query = parse_url($parent->link);
        if (!isset($link_query['query'])) {
            return;
        }

        parse_str(html_entity_decode($link_query['query']), $link_vars);
        $view = JArrayHelper::getValue($link_vars, 'view', '');
        $id = intval(JArrayHelper::getValue($link_vars, 'id', ''));

        /** Parameters Initialitation **/
        // Set expand_categories param
        $expand_categories = JArrayHelper::getValue($params, 'expand_categories', 1);
        $expand_categories = ( $expand_categories == 1
                || ( $expand_categories == 2 && $xmap->view == 'xml')
                || ( $expand_categories == 3 && $xmap->view == 'html')
                || $xmap->view == 'navigator');
        $params['expand_categories'] = $expand_categories;

        // Set expand_archived param
        $include_archived = JArrayHelper::getValue($params, 'include_archived', 2);
        $include_archived = ( $include_archived == 1
                || ( $include_archived == 2 && $xmap->view == 'xml')
                || ( $include_archived == 3 && $xmap->view == 'html')
                || $xmap->view == 'navigator');
        $params['include_archived'] = $include_archived;

        // Set show_unauth param
        $show_unauth = JArrayHelper::getValue($params, 'show_unauth', 1);
        $show_unauth = ( $show_unauth == 1
                || ( $show_unauth == 2 && $xmap->view == 'xml')
                || ( $show_unauth == 3 && $xmap->view == 'html'));
        $params['show_unauth'] = $show_unauth;

        $params['cat_priority'] = $parent->priority;
        $params['cat_changefreq'] = $parent->changefreq;

        $params['media_priority'] = $parent->priority;
        $params['media_changefreq'] = $parent->changefreq;

        $params['max_media'] = intval(JArrayHelper::getValue($params, 'max_media', 9999));
        $params['max_media_age'] = intval(JArrayHelper::getValue($params, 'max_media_age', 9999));

        $params['nullDate'] = $db->Quote($db->getNullDate());

        $params['nowDate'] = $db->Quote($date->toSql());
        $params['groups'] = implode(',', $user->getAuthorisedViewLevels());

        // Define the language filter condition for the query
        $params['language_filter'] = $app->getLanguageFilter();

        switch ($view) {
            case 'media':
                $result = self::includeMedia($xmap, $parent, $id, $params, $parent->id);
                break;          
            case 'category':
                if ($params['expand_categories'] && $id) {
                    $result = self::expandCategory($xmap, $parent, $id, $params, $parent->id);
                }
                break;
            case 'categories':
                if ($params['expand_categories']) {
                    $result = self::expandCategory($xmap, $parent, 1, $params, $parent->id);
                }
                break;
        }
        return $result;
    }
    
    /**
     * Get all items within a com_hwdmediashare category.
     * Returns an array of all contained items.
     *
     * @param object  $xmap
     * @param object  $parent   the menu item
     * @param int     $catid    the id of the category to be expanded
     * @param array   $params   an assoc array with the params for this plugin on Xmap
     * @param int     $itemid   the itemid to use for this category's children
     */
    static function expandCategory($xmap, $parent, $catid, &$params, $itemid)
    {
        $db = JFactory::getDBO();

        $where = array('a.parent_id = ' . $catid . ' AND a.published = 1 AND a.extension=\'com_hwdmediashare\'');

        if ($params['language_filter'] ) {
            $where[] = 'a.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')';
        }

        if (!$params['show_unauth']) {
            $where[] = 'a.access IN (' . $params['groups'] . ') ';
        }

        $orderby = 'a.lft';
        $query = 'SELECT a.id, a.title, a.alias, a.access, a.path AS route, '
               . 'UNIX_TIMESTAMP(a.created_time) created, UNIX_TIMESTAMP(a.modified_time) modified '
               . 'FROM #__categories AS a '
               . 'WHERE '. implode(' AND ', $where)
               . ( $xmap->view != 'xml' ? "\n ORDER BY " . $orderby . "" : '' );

        $db->setQuery($query);

        $items = $db->loadObjectList();

        if (count($items) > 0) {
            $xmap->changeLevel(1);
            foreach ($items as $item) {
                $node = new stdclass();
                $node->id = $parent->id;
                $node->uid = $parent->uid . 'c' . $item->id;
                $node->browserNav = $parent->browserNav;
                $node->priority = $params['cat_priority'];
                $node->changefreq = $params['cat_changefreq'];
                $node->name = $item->title;
                $node->expandible = true;
                $node->secure = $parent->secure;
                // TODO: Should we include category name or metakey here?
                // $node->keywords = $item->metakey;
                $node->newsItem = 0;

                // For the google news we should use te publication date instead
                // the last modification date. See
                if ($xmap->isNews || !$item->modified)
                    $item->modified = strtotime($item->created);

                $node->slug = $item->route ? ($item->id . ':' . $item->route) : $item->id;
                $node->link = hwdMediaShareHelperRoute::getCategoryRoute($node->slug);
                if (strpos($node->link,'Itemid=')===false) {
                    $node->itemid = $itemid;
                    $node->link .= '&Itemid='.$itemid;
                } else {
                    $node->itemid = preg_replace('/.*Itemid=([0-9]+).*/','$1',$node->link);
                }
                if ($xmap->printNode($node)) {
                    self::expandCategory($xmap, $parent, $item->id, $params, $node->itemid);
                }
            }
            $xmap->changeLevel(-1);
        }

        // Include category media items
        $params['element_id'] = 6;
        self::includeMedia($xmap, $parent, $catid, $params, $itemid);
        return true;
    }

    /**
     * Get all content items within a content category.
     * Returns an array of all contained content items.
     *
     * @since 2.0
     */
    static function includeMedia($xmap, $parent, $eid, &$params, $Itemid)
    {
        $db = JFactory::getDBO();

        // We do not do ordering for XML sitemap.
        if ($xmap->view != 'xml') {
            $orderby = self::buildContentOrderBy($parent->params,$parent->id,$Itemid);
            //$orderby = !empty($menuparams['orderby']) ? $menuparams['orderby'] : (!empty($menuparams['orderby_sec']) ? $menuparams['orderby_sec'] : 'rdate' );
            //$orderby = self::orderby_sec($orderby);
        }

        jimport( 'joomla.application.component.model' );
        //JModel::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
        //$model =& JModel::getInstance('Media', 'hwdMediaShareModel', array('ignore_request' => true));
        $version = new JVersion();
        ($version->RELEASE >= 3.0 ? JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models') : JModel::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models'));
        $model = ($version->RELEASE >= 3.0 ? JModelLegacy::getInstance('Media', 'hwdMediaShareModel', array('ignore_request' => true)) : JModel::getInstance('Media', 'hwdMediaShareModel', array('ignore_request' => true)));

        // Get hwdMediaShare config
        $hwdms = hwdMediaShareFactory::getInstance();
        $config = $hwdms->getConfig();

        // Set application parameters in model
        $app = JFactory::getApplication();
        $appParams = $app->getParams();
        $model->setState('params', $appParams);

        // Set list filters based on parameters
        $model->setState('list.limit', $params['max_media']);
        $model->setState('list.start', 0); 

        // Set other filters
        if (isset($params['element_id']))
        {
            if ($params['element_id'] == 6)
            {
                $model->setState('filter.category_id', $eid);
                $model->setState('category.id', $eid);
            }
        }
 
        // Ordering
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

        // Filter by language
        $model->setState('filter.language', $app->getLanguageFilter());

        if ($items = $model->getItems())
        {
                for ($i=0, $n=count($items); $i < $n; $i++)
                {
                }
        }
                
        if (count($items) > 0) {
            $xmap->changeLevel(1);
            foreach ($items as $item) {
                $node = new stdclass();
                $node->id = $parent->id;
                $node->uid = $parent->uid . 'a' . $item->id;
                $node->browserNav = $parent->browserNav;
                $node->priority = $params['media_priority'];
                $node->changefreq = $params['media_changefreq'];
                $node->name = $item->title;
                $node->modified = strtotime($item->modified);
                $node->expandible = false;
                $node->secure = $parent->secure;
                // TODO: Should we include category name or metakey here?
                // $node->keywords = $item->metakey;
                $node->newsItem = 1;
                $node->language = $item->language;

                // For the google news we should use te publication date instead
                // the last modification date. See
                if ($xmap->isNews || !$node->modified)
                    $node->modified = strtotime($item->created);

                $node->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;
                $node->link = hwdMediaShareHelperRoute::getMediaItemRoute($node->slug);

                // Add images to the article
                $text = @$item->introtext . @$item->fulltext;

                // Need to get thumbnail
                // $node->images = XmapHelper::getImages($text,$params['max_images']);

                if ($xmap->printNode($node) && $node->expandible) {
                    $xmap->changeLevel(1);
                    $i=0;
                    foreach ($subnodes as $subnode) {
                        $i++;
                        //var_dump($subnodes);
                        $subnode->id = $parent->id;
                        $subnode->uid = $parent->uid.'p'.$i;
                        $subnode->browserNav = $parent->browserNav;
                        $subnode->priority = $params['media_priority'];
                        $subnode->changefreq = $params['media_changefreq'];
                        $subnode->secure = $parent->secure;
                        $xmap->printNode($subnode);
                    }
                    $xmap->changeLevel(-1);
                }
            }
            $xmap->changeLevel(-1);
        }
        return true;
    }

    /**
     * Generates the order by part of the query according to the
     * menu/component/user settings. It checks if the current user
     * has already changed the article's ordering column in the frontend
     *
     * @param JRegistry $params
     * @param int $parentId
     * @param int $itemid
     * @return string
     */
    static function buildContentOrderBy(&$params,$parentId,$itemid)
    {
        $app    = JFactory::getApplication('site');

        // Case when the child gets a different menu itemid than it's parent
        if ($parentId != $itemid) {
            $menu = $app->getMenu();
            $item = $menu->getItem($itemid);
            $menuParams = clone($params);
            $itemParams = new JRegistry($item->params);
            $menuParams->merge($itemParams);
        } else {
            $menuParams =& $params;
        }

        $filter_order = $app->getUserStateFromRequest('com_content.category.list.' . $itemid . '.filter_order', 'filter_order', '', 'string');
        $filter_order_Dir = $app->getUserStateFromRequest('com_content.category.list.' . $itemid . '.filter_order_Dir', 'filter_order_Dir', '', 'cmd');
        $orderby = ' ';

        if ($filter_order && $filter_order_Dir) {
            $orderby .= $filter_order . ' ' . $filter_order_Dir . ', ';
        }

        $articleOrderby     = $menuParams->get('orderby_sec', 'rdate');
        $articleOrderDate   = $menuParams->get('order_date');
        //$categoryOrderby  = $menuParams->def('orderby_pri', '');
        $secondary      = ContentHelperQuery::orderbySecondary($articleOrderby, $articleOrderDate) . ', ';
        //$primary      = ContentHelperQuery::orderbyPrimary($categoryOrderby);

        //$orderby .= $primary . ' ' . $secondary . ' a.created ';
        $orderby .=  $secondary . ' a.created ';

        return $orderby;
    }
}