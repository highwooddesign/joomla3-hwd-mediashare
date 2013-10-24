<?php
/**
 * @version    $Id: media.php 1254 2013-03-08 14:31:54Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla plugin library
jimport('joomla.plugin.plugin');

require_once JPATH_SITE.'/components/com_hwdmediashare/helpers/route.php';

/**
 * hwdMediaShare Search plugin
 *
 * @since		0.1
 */
class plgSearchMedia extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @access      protected
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 * @since       1.5
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	 * @return array An array of search areas
	 */
	function onContentSearchAreas()
        {
                JLoader::register('hwdMediaShareFactory', JPATH_ROOT.'/components/com_hwdmediashare/libraries/factory.php');
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

		static $areas = array();

		if($this->params->get('search_media', 1)) $areas['media'] = 'PLG_SEARCH_MEDIA_MEDIA';
		if($this->params->get('search_albums', 1) && $config->get('enable_albums')) $areas['albums'] = 'PLG_SEARCH_MEDIA_ALBUMS';
		if($this->params->get('search_groups', 1) && $config->get('enable_groups')) $areas['groups'] = 'PLG_SEARCH_MEDIA_GROUPS';
		if($this->params->get('search_playlists', 1) && $config->get('enable_playlists')) $areas['playlists'] = 'PLG_SEARCH_MEDIA_PLAYLISTS';

		return $areas;
	}

        /**
	 * hwdMediaShare Search method
	 *
	 * The sql must return the following fields that are used in a common display
	 * routine: href, title, section, created, text, browsernav
	 * @param string Target search string
	 * @param string mathcing option, exact|any|all
	 * @param string ordering option, newest|oldest|popular|alpha|category
	 * @param mixed An array if the search it to be restricted to areas, null if search all
	 */
	function onContentSearch($text, $phrase='', $ordering='', $areas=null)
	{
                $use_caching            = $this->params->def('use_caching', 1);

                if ($use_caching)
                {
                        // Get a reference to the global cache object.
                        $cache = & JFactory::getCache();
                        $cache->setCaching( 1 );

                        // We need to prevent caching when search for unique custom fields, so we set an array holding those values
                        $storeId = plgSearchMedia::getStoreId();

                        // Run the test without caching.
                        // $profiler = new JProfiler();
                        // $rows = plgSearchMedia::onContentSearchCache($text, $phrase, $ordering, $areas);
                        // echo $profiler->mark( ' without caching' );
                        // Run the test with caching.
                        // $profiler = new JProfiler();
                        // $rows  = $cache->call( array( $this, 'onContentSearchCache' ), $text, $phrase, $ordering, $areas );
                        // echo $profiler->mark( ' with caching' );
                        return $cache->call( array( $this, 'onContentSearchCache' ), $text, $phrase, $ordering, $areas, $storeId );
                }
                else
                {
                        return plgSearchMedia::onContentSearchCache($text, $phrase, $ordering, $areas);
                }
        }

        /**
	 * hwdMediaShare Search method
	 *
	 * The sql must return the following fields that are used in a common display
	 * routine: href, title, section, created, text, browsernav
	 * @param string Target search string
	 * @param string mathcing option, exact|any|all
	 * @param string ordering option, newest|oldest|popular|alpha|category
	 * @param mixed An array if the search it to be restricted to areas, null if search all
	 */
	function onContentSearchCache($text, $phrase='', $ordering='', $areas=null, $storeId='')
	{
		$db		= JFactory::getDbo();
		$app	= JFactory::getApplication();
		$user	= JFactory::getUser();
		$groups	= implode(',', $user->getAuthorisedViewLevels());

		$searchText = $text;
                $mediaAreas = $this->onContentSearchAreas();

                if (is_array($areas)) {
			if (!array_intersect($areas, array_keys($mediaAreas))) {
				return array();
			}
		}

		$sContent		= $this->params->get('search_published',	1);
		$sArchived		= $this->params->get('search_archived',		0);

                $limit			= $this->params->def('search_limit',		50);
                $use_caching            = $this->params->def('use_caching',             1);
                $search_title		= $this->params->def('search_title',		1);
                $search_description	= $this->params->def('search_description',	1);
                $search_metadata	= $this->params->def('search_metadata',		0);
                $search_alias		= $this->params->def('search_alias',		0);
                $include_thumbnail      = $this->params->def('include_thumbnail',	1);
                $search_method		= $this->params->def('search_method',		1);
                $relation_method        = $this->params->def('relation_method',		0);

                $state = array();
		if ($sContent) {
			$state[]=1;
		}
		if ($sArchived) {
			$state[]=2;
		}

                // Remove white space from input
		$text = trim($text);

		// If the input was empty then return the array
                if ($text == '' && $storeId == '') {
			return array();
		}

                $values = array(
                    'media' => array( 'table' => 'hwdms_media', 'view' => 'mediaitem' ),
                    'albums' => array( 'table' => 'hwdms_albums', 'view' => 'album' ),
                    'groups' => array( 'table' => 'hwdms_groups', 'view' => 'group' ),
                    'playlists' => array( 'table' => 'hwdms_playlists', 'view' => 'playlist' ),
                    'users' => array( 'table' => 'hwdms_users', 'view' => 'user' )
                );

                $return = array();

                // When searching for related media, use the relation method
                if (JRequest::getWord('option') == 'com_hwdmediashare' && JRequest::getInt('id') > 0)
                {
                    $search_method = $relation_method;
                }

                // Loop all search areas
                foreach($mediaAreas as $searchKey => $searchValue)
                {
                        // Continue loop if we are not searching this area
                        if (is_array($areas)) {
                                if (!in_array($searchKey, $areas)) {
                                        continue;
                                }
                        }

                        $section = JText::_($searchValue);

                        $wheres	= array();
                        switch ($phrase)
                        {       // when searching for exact word or phrase
                                case 'exact':
                                        // if type is LIKE
                                        if ($search_method == 1)
                                        {
                                                $text		= $db->Quote('%'.$db->escape($text, true).'%', false);
                                                $wheres2	= array();
                                                if ($search_title == 1)         $wheres2[]	= 'a.title LIKE '.$text;
                                                if ($search_description == 1)   $wheres2[]	= 'a.description LIKE '.$text;
                                                if ($search_metadata == 1) $wheres2[]	= 'a.params LIKE '.$text;
                                                if ($search_alias == 1) $wheres2[]	= 'a.alias LIKE '.$text;
                                                $where      = '(' . implode(') OR (', $wheres2) . ')';

                                        }
                                        // else type is MATCH
                                        else
                                        {
                                                $text		= $db->Quote($db->escape($text, true).'*', false);
                                                $wheres2	= array();
                                                if ($search_title == 1)         $wheres2[]	= 'a.title ';
                                                if ($search_description == 1)   $wheres2[]	= 'a.description ';
                                                if ($search_metadata == 1) $wheres2[]	= 'a.params ';
                                                if ($search_alias == 1) $wheres2[]	= 'a.alias ';
                                                $where      = 'MATCH(' . implode($wheres2, ',') . ') AGAINST (' .$text. ' IN BOOLEAN MODE)';
                                        }
                                        break;
                                // when searching for all words or any words
                                case 'all':
                                case 'any':
                                default:
                                        // if type is LIKE
                                        if ($search_method == 1)
                                        {
                                            $words = explode(' ', $text);
                                            $wheres = array();
                                            foreach ($words as $word)
                                            {
                                                $word		= $db->Quote('%'.$db->escape($word, true).'%', false);
                                                $wheres2	= array();
                                                if ($search_title == 1)         $wheres2[]	= 'a.title LIKE '.$word;
                                                if ($search_description == 1)   $wheres2[]	= 'a.description LIKE '.$word;
                                                if ($search_metadata == 1) $wheres2[]	= 'a.params LIKE '.$word;
                                                if ($search_alias == 1) $wheres2[]	= 'a.alias LIKE '.$word;
                                                $wheres[]	= implode(' OR ', $wheres2);
                                            }
                                            $where = '(' . implode(($phrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')';
                                        }
                                        // else type is MATCH
                                        else
                                        {
                                            $words = explode(' ', $text);
                                            $wordStarred = array();
                                            foreach ($words as $word)
                                            {
                                                $wordStarred[]		= ($phrase == 'all' ? '+' : '').$db->escape($word, true).'*';

                                            }
                                            $wheres2	= array();
                                            if ($search_title == 1)         $wheres2[]	= 'a.title';
                                            if ($search_description == 1)   $wheres2[]	= 'a.description';
                                            if ($search_metadata == 1) $wheres2[]	= 'a.params ';
                                            if ($search_alias == 1) $wheres2[]	= 'a.alias ';
                                            $where      = 'MATCH(' . implode($wheres2, ', ') . ') AGAINST (' . $db->Quote(implode($wordStarred, ' ')) . ' IN BOOLEAN MODE)';
                                }
                                        break;
                        }

                        switch ($ordering)
                        {
                                case 'oldest':
                                        $order = 'a.created ASC';
                                        break;

                                case 'popular':
                                        $order = 'a.hits DESC';
                                        break;

                                case 'alpha':
                                        $order = 'a.title ASC';
                                        break;

                                case 'newest':
                                default:
                                        $order = 'a.created DESC';
                        }

                        if (!empty($state)) {
                                $query	= $db->getQuery(true);

                                switch ($searchKey)
                                {
                                        // When searching media set select
                                        // Need key, ext_id for thumbnail display
                                        case 'media':
                                                $query->select('a.id, a.title AS title, a.description AS text, a.created AS created, a.ext_id, a.type, a.key, a.thumbnail_ext_id, a.thumbnail, '
                                                                .'CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END as slug, '
                                                                .'CONCAT_WS(" / ", '.$db->Quote($section).', a.title) AS section, "1" AS browsernav');
                                                break;
                                        // when searching albums, groups or playlists set select
                                        case 'albums':
                                        case 'groups':
                                        case 'playlists':
                                                $query->select('a.id, a.title AS title, a.description AS text, a.created AS created, a.thumbnail_ext_id, '
                                                                .'CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END as slug, '
                                                                .'CONCAT_WS(" / ", '.$db->Quote($section).', a.title) AS section, "1" AS browsernav');
                                                break;
                                }


                                // set from to the tables the search is on
                                $query->from('#__'.$values[$searchKey]['table'].' AS a');
                                // set the where section of the query to be the contents of the where variable surrounded by brackets
                                if ($text != '') 
                                {
                                        $query->where('('.$where.')');
                                }
                                $query->where('a.published in ('.implode(',',$state).')');

                                // Restrict based on access
                                $query->where('a.access IN ('.$groups.')');

                                // Restrict based on access
                                $query->where('(a.private = 0 OR (a.private = 1 && a.created_user_id = '.$user->id.'))');

                                // Restrict based on status
                                $query->where('a.status = 1');

                                // If the relation_method is 0 (match search) then do not set any ordering (so that it is ordere by relevance)
                                if (JRequest::getWord('option') == 'com_hwdmediashare' && JRequest::getInt('id') > 0 && $relation_method == 0)
                                {
                                        // Don't define ordering to return results by relavency
                                }
                                // otherwise set the ordering to what it has been selected as
                                else
                                {
                                        $query->order($order);
                                }

                                // Prevent matching current media
                                if (JRequest::getWord('option') == 'com_hwdmediashare' && JRequest::getInt('id') > 0)
                                {
                                        $query->where('id <> ' . JRequest::getInt('id'));
                                }

                                // Add additional filters (only in internal search)
                                if (JRequest::getWord('option') == 'com_hwdmediashare')
                                {
                                        $catid = JRequest::getInt('catid');
                                        if ($searchKey == 'media' && !empty($catid))
                                        {
                                                $query->join('LEFT', '#__hwdms_category_map AS cmap ON cmap.element_id = a.id');

                                                $query->where('cmap.category_id = '.$catid);
                                                $query->where('cmap.element_type = 1');
                                        }

                                        $elementSet = array('media' => 1, 'albums' => 2, 'groups' => 3,'playlists' => 4, 'users' => 5);
                                        hwdMediaShareFactory::load('customfields');
                                        $customfields = hwdMediaShareCustomFields::get(null, $elementSet[$searchKey]);

                                        foreach ($customfields['fields'] as $group => $groupFields)
                                        {
                                                foreach ($groupFields as $field)
                                                {
                                                        $field = JArrayHelper::toObject ( $field );
                                                        if ($field->searchable)
                                                        {
                                                                // Get input for this field
                                                                $term = JRequest::getVar('field'.$field->id);

                                                                // Only search when we have a value to check
                                                                if (empty($term)) continue;

                                                                // Prepare term for search
                                                                $term = $db->Quote('%'.$db->escape($term, true).'%', false);

                                                                // Join over the custom field values
                                                                $query->join('LEFT', '#__hwdms_fields_values AS custom'.$field->id.' ON custom'.$field->id.'.element_id = a.id');
                                                                $query->where('custom'.$field->id.'.element_type = '.$elementSet[$searchKey]);
                                                                $query->where('(custom'.$field->id.'.field_id = '.$field->id.' AND custom'.$field->id.'.value LIKE '.$term.')');
                                                                unset($term);
                                                        }
                                                }
                                        }
                                }

                                // Filter by language
                                if ($app->isSite() && $app->getLanguageFilter())
                                {
                                        $tag = JFactory::getLanguage()->getTag();
                                        $query->where('a.language in (' . $db->Quote($tag) . ',' . $db->Quote('*') . ')');
                                }

                                $db->setQuery($query, 0, $limit);
                                $rows = $db->loadObjectList();

                                JLoader::register('hwdMediaShareFactory', JPATH_ROOT.'/components/com_hwdmediashare/libraries/factory.php');
                                hwdMediaShareFactory::load('downloads');

                                if ($rows)
                                {
                                        foreach($rows as $key => $row)
                                        {
                                                // if include thumbnail is set to yes, add the img to the start of the description
                                                if ($include_thumbnail == 1)
                                                {
                                                    //$rows[$key]->text = "<img src=\"".JRoute::_(hwdMediaShareDownloads::thumbnail($row))."\" border=\"0\" style=\"max-width:150px;\" class=\"\" />".$rows[$key]->text;
                                                }
                                                switch ($searchKey)
                                                {
                                                        case 'media':
                                                                $rows[$key]->href = hwdMediaShareHelperRoute::getMediaItemRoute($row->slug);
                                                                break;

                                                        case 'albums':
                                                                $rows[$key]->href = hwdMediaShareHelperRoute::getAlbumRoute($row->slug);
                                                                break;

                                                        case 'groups':
                                                                $rows[$key]->href = hwdMediaShareHelperRoute::getGroupRoute($row->slug);
                                                                break;

                                                        case 'playlists':
                                                                $rows[$key]->href = hwdMediaShareHelperRoute::getPlaylistRoute($row->slug);
                                                                break;

                                                        case 'users':
                                                                $rows[$key]->href = hwdMediaShareHelperRoute::getUserRoute($row->slug);
                                                                break;
                                                }
                                        }

                                        foreach($rows AS $key => $item)
                                        {
                                                JLoader::register('SearchHelper', JPATH_ROOT.'/administrator/components/com_search/helpers/search.php');
                                                $return[] = $item;
                                                //if (searchHelper::checkNoHTML($item, $searchText, array('url', 'text', 'title')))
                                                //{
                                                        //$return[] = $item;
                                                //}
                                        }
                                }
                        }
                }
		return $return;
	}
        
	/**
	 * @return array An array of search areas
	 */
	function getStoreId($id = '')
        {
                // Set the search area, and default to media
                $area = JRequest::getInt('area', 1);
                hwdMediaShareFactory::load('customfields');
                $customfields = hwdMediaShareCustomFields::get(null, $area);

                foreach ($customfields['fields'] as $group => $groupFields)
                {
                        foreach ($groupFields as $field)
                        {
                                $field = JArrayHelper::toObject ( $field );
                                //$id .= ':'.JRequest::getVar('field'.$field->id);
                                $id   .= JRequest::getVar('field'.$field->id);
                        }
                }

                return $id;
	}
}
