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

class hwdMediaShareModelMedia extends JModelList
{
	/**
	 * Model context string.
	 * @var string
	 */
	public $context = 'com_hwdmediashare.media';

	/**
	 * Model data
	 * @var array
	 */
	protected $_items = null;
        
    	/**
	 * Constructor override, defines a white list of column filters.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'title', 'a.title',
				'viewed', 'a.viewed',                            
				'likes', 'a.likes',
				'dislikes', 'a.dislikes',
				'ordering', 'a.ordering', 'map.ordering', 'pmap.ordering',
				'created_user_id', 'a.created_user_id', 'created_user_id_alias', 'a.created_user_id_alias', 'author',
                                'created', 'a.created',
				'modified', 'a.modified',
				'hits', 'a.hits',
                                'random', 'random',
			);
		}

		parent::__construct($config);
	}
        
	/**
	 * Method to get a list of items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getItems()
	{
		if ($items = parent::getItems())
		{            
                        for ($x = 0, $count = count($items); $x < $count; $x++)
                        {
                                if (empty($items[$x]->author)) $items[$x]->author = JText::_('COM_HWDMS_GUEST');
                                
                                // Load categories for item.
                                hwdMediaShareFactory::load('category');
                                $categoryLib = hwdMediaShareCategory::getInstance();
                                $categoryLib->elementType = 1;
                                $items[$x]->categories = $categoryLib->get($items[$x]);
                        }
                }

		return $items;
	}
        
	/**
	 * Method to return the first in the list of media.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getItem()
	{
		$items = $this->getItems();
                reset($items);
		return current($items);
	}
        
	/**
	 * Method to get the database query.
	 *
	 * @return  JDatabaseQuery  database query
	 */
        public function getListQuery()
        {
		$user	= JFactory::getUser();
		$groups	= implode(',', $user->getAuthorisedViewLevels());

                // Create a new query object.
                $db = JFactory::getDBO();
                $query = $db->getQuery(true);

                // Get HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.id, a.key, a.ext_id, a.title, a.description, a.alias, a.checked_out, a.checked_out_time,' .
				'a.created_user_id, a.thumbnail_ext_id, a.location, a.hits, a.thumbnail, a.type, a.published, a.likes, a.dislikes, a.featured,' .
				'a.status, a.publish_up, a.publish_down, a.duration, a.source, a.ordering, a.created, a.access,'.
				'a.language, a.modified, a.created_user_id_alias'
			)
		);
                
                // From the media table.
                $query->from('#__hwdms_media AS a');

                // Restrict based on access.
		if ($config->get('entice_mode') == 0)
                {
                        $query->where('a.access IN ('.$groups.')');
                }
                
                // Restrict based on privacy access.
                $query->where('(a.private = 0 OR (a.private = 1 && a.created_user_id = '.$user->id.'))');
                
                // Join over the users for the author, with value based on configuration.
                $config->get('author') == 0 ? $query->select('CASE WHEN a.created_user_id_alias > ' . $db->Quote(' ') . ' THEN a.created_user_id_alias ELSE ua.name END AS author') : $query->select('CASE WHEN a.created_user_id_alias > ' . $db->Quote(' ') . ' THEN a.created_user_id_alias ELSE ua.username END AS author');
		$query->join('LEFT', '#__users AS ua ON ua.id=a.created_user_id');
		$query->join('LEFT', '#__users AS uam ON uam.id = a.modified_user_id');

                // Join over the extensions table.
                $query->select('ext.ext');
                $query->select("CASE WHEN a.ext_id > 0 THEN ext.media_type ELSE a.media_type END AS media_type");
                $query->join('LEFT', '#__hwdms_ext AS ext ON ext.id = a.ext_id');
                
		// Filter by published state.
		$published = $this->getState('filter.published');
		if (is_array($published)) 
                {
			JArrayHelper::toInteger($published);
			$published = implode(',', $published);
			if ($published) 
                        {
                                $query->where('a.published IN ('.$published.')');
			}
		}
                elseif (is_numeric($published))
                {
			$query->where('a.published = '.(int) $published);
		}

                // Filter by status state.
		$status = $this->getState('filter.status');
		if (is_array($status)) 
                {
			JArrayHelper::toInteger($status);
			$status = implode(',', $status);
			if ($status) 
                        {
                                $query->where('a.status IN ('.$status.')');
			}
		}
                elseif (is_numeric($status))
                {
			$query->where('a.status = '.(int) $status);
		}

		// Filter by search in title.
		$search = $this->getState('filter.search');
		$searchMethod = $this->getState('filter.search.method');
		if (!empty($search))
                {
			if (stripos($search, 'id:') === 0)
                        {
				$query->where('a.id = '.(int) substr($search, 3));
			}
                        else
                        {
                                if ($searchMethod == "match")
                                {
                                        $query->where('MATCH(a.title, a.description) AGAINST (' . $db->quote($search) . ' IN BOOLEAN MODE)');
                                }
                                else
                                {
                                        $search = $db->Quote('%'.$db->escape($search, true).'%');
                                        $query->where('(a.title LIKE '.$search.' OR a.alias LIKE '.$search.')');
                                }
			}
		}

		// Filter on the language.
		if ($this->getState('filter.language'))
                {
			$query->where('a.language in (' . $db->Quote(JFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')');
		}                

		// Add the list ordering clause.
                $listOrder = $this->state->get('list.ordering');
                $listDirn = $this->state->get('list.direction');
		if (!empty($listOrder) && !empty($listDirn))
		{
                        if ($listOrder == 'random')
                        {
                                $query->order('RAND()');
                        }
                        else
                        {
                                $query->order($db->escape($listOrder.' '.$listDirn));
                        }                    
		}    

		// Filter by a single or multiple categories
		$categoryId = $this->getState('filter.category_id');
                if (is_numeric($categoryId) && $categoryId > 0) 
                {
			$type = $this->getState('filter.category_id.include', true) ? '= ' : '<> ';

			// Add subcategory check
			$includeSubcategories = $this->getState('filter.subcategories', false);
			$categoryEquals = 'cmap.category_id '.$type.(int) $categoryId;

			if ($includeSubcategories) 
                        {
				$levels = (int) $this->getState('filter.max_category_levels', '1');
				// Create a subquery for the subcategory list
				$subQuery = $db->getQuery(true);
				$subQuery->select('sub.id');
				$subQuery->from('#__categories as sub');
				$subQuery->join('INNER', '#__categories as this ON sub.lft > this.lft AND sub.rgt < this.rgt');
				$subQuery->where('this.id = '.(int) $categoryId);
				if ($levels >= 0) 
                                {
					$subQuery->where('sub.level <= this.level + '.$levels);
				}

				// Add the subquery to the main query
				$query->where('('.$categoryEquals.' OR cmap.category_id IN ('.$subQuery->__toString().'))');
			}
			else
                        {
				$query->where($categoryEquals);
			}
                       
                        $query->join('LEFT', '#__hwdms_category_map AS cmap ON cmap.element_id = a.id');
                        $query->join('LEFT', '#__categories AS c ON c.id = cmap.category_id');
                        $query->where('cmap.element_type = 1');
                        $query->where('c.access IN ('.$groups.')');

                        // Filter by published category
                        $cpublished = $this->getState('filter.c.published');
                        if (is_numeric($cpublished)) 
                        {
                                $query->where('c.published = '.(int) $cpublished);
                        }
		}
		elseif (is_array($categoryId) && (count($categoryId) > 0))
                {
			JArrayHelper::toInteger($categoryId);
			$categoryId = implode(',', $categoryId);
			if (!empty($categoryId))
                        {
				$type = $this->getState('filter.category_id.include', true) ? 'IN' : 'NOT IN';
                                $query->where('cmap.category_id '.$type.' ('.$categoryId.')');
                                $query->where('cmap.element_type = 1');

                                $query->join('LEFT', '#__hwdms_category_map AS cmap ON cmap.element_id = a.id');
                                $query->join('LEFT', '#__categories AS c ON c.id = cmap.category_id');
                                
                                $query->where('c.access IN ('.$groups.')');

                                //Filter by published category
                                $cpublished = $this->getState('filter.c.published');
                                if (is_numeric($cpublished))
                                {
                                        $query->where('c.published = '.(int) $cpublished);
                                }
			}
		}
                
		// Filter by author
		$authorId = $this->getState('filter.author_id');
		$authorWhere = '';
		if (is_numeric($authorId)) 
                {
			$type = $this->getState('filter.author_id.include', true) ? '= ' : '<> ';
			$query->where('a.created_user_id '.$type.(int) $authorId);
		}
		elseif (is_array($authorId)) 
                {
			JArrayHelper::toInteger($authorId);
			$authorId = implode(',', $authorId);

			if ($authorId) {
				$type = $this->getState('filter.author_id.include', true) ? 'IN' : 'NOT IN';
				$query->where('a.created_user_id '.$type.' ('.$authorId.')');
			}
		}

		// Filter by start and end dates.
		$nullDate = $db->Quote($db->getNullDate());
		$nowDate = $db->Quote(JFactory::getDate()->toSql());
		if ($this->getState('filter.publish_date'))
                {
			$query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')');
			$query->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');
		}

		// Filter by Date Range or Relative Date
		$dateFiltering = $this->getState('filter.date_filtering', 'off');
		$dateField = $this->getState('filter.date_field', 'a.created');
		switch ($dateFiltering)
		{
			case 'range':
				$startDateRange = $db->Quote($this->getState('filter.start_date_range', $nullDate));
				$endDateRange = $db->Quote($this->getState('filter.end_date_range', $nullDate));
				$query->where('('.$dateField.' >= '.$startDateRange.' AND '.$dateField . ' <= '.$endDateRange.')');
				break;

			case 'relative':
				$relativeDate = (int) $this->getState('filter.relative_date', 0);
				$query->where($dateField.' >= DATE_SUB('.$nowDate.', INTERVAL ' . $relativeDate.' DAY)');
				break;

			case 'off':
			default:
				break;
		}

		// Filter by featured state.
		$featured = $this->getState('filter.featured');
		switch ($featured)
		{
			case 'hide':
				$query->where('a.featured = 0');
				break;

                        case 'only':
				$query->where('a.featured = 1');
				break;
                            
			case 'show':
			default:
				// Normally we do not discriminate
				// between featured/unfeatured items.
				break;
		}                

                // Filter by media type.
		$mediaType = $this->getState('filter.media_type');
		if (is_numeric($mediaType))
                {
                        $query->where('(ext.media_type = '.(int) $mediaType.' OR a.media_type = '.(int) $mediaType.')');
		} 
                
                // Filter by album.
		if ($albumId = $this->getState('filter.album_id'))
                {
                        $query->join('LEFT', '`#__hwdms_album_map` AS amap ON amap.media_id = a.id AND amap.album_id = '.$albumId);
                        $query->where('amap.album_id = ' . $db->quote($albumId));
		}

                // Filter by group.
		if ($groupId = $this->getState('filter.group_id'))
                {
                        $query->join('LEFT', '`#__hwdms_group_map` AS gmap ON gmap.media_id = a.id AND gmap.group_id = '.$groupId);
                        $query->where('gmap.group_id = ' . $db->quote($groupId));
		}

                // Filter by playlist.
		if ($playlistId = $this->getState('filter.playlist_id'))
                {
                        $query->select('pmap.ordering AS playlist_order');
                        $query->join('LEFT', '`#__hwdms_playlist_map` AS pmap ON pmap.media_id = a.id AND pmap.playlist_id = '.$playlistId);
                        $query->where('pmap.playlist_id = ' . $db->quote($playlistId));
		}

                // Filter by user.
		if ($userId = $this->getState('filter.user_id'))
                {
                        $query->where('a.created_user_id = ' . $db->quote($userId));
		}
                
                // Filter by user favourites
		if ($favourtiesId = $this->getState('filter.favourites_id'))
                {
                        $query->join('LEFT', '`#__hwdms_favourites` AS fav ON fav.element_id = a.id');                                           
                        $query->where('fav.user_id = ' . $db->quote($favourtiesId));
		}

                //echo nl2br(str_replace('#__','jos_',$query));
		return $query;
        }
        
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 */
	public function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication();
                $user = JFactory::getUser();
                
		// Load the parameters.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                $this->setState('params', $config);
                
		if ((!$user->authorise('core.edit.state', 'com_hwdmediashare')) && (!$user->authorise('core.edit', 'com_hwdmediashare')))
                {
			// Limit to published for people who can't edit or edit.state.
			$this->setState('filter.published',	1);
			$this->setState('filter.status',	1);

			// Filter by start and end dates.
			$this->setState('filter.publish_date', true);
		}
                else
                {
			// Allow access to unpublished and unapproved items.
			$this->setState('filter.published',	array(0,1));
			$this->setState('filter.status',	array(0,1,2,3));
                }

		$this->setState('filter.language', $app->getLanguageFilter());

		// Load the display state.
		$display = $this->getUserStateFromRequest('media.display', 'display', $config->get('list_default_display', 'details' ), 'word', false);
                if (!in_array(strtolower($display), array('details', 'gallery', 'list'))) $display = 'details';
		$this->setState('media.display', $display);

                $ordering = $config->get('list_order_media', 'a.created DESC');
		$orderingParts = explode(' ', $ordering);

                // Check for list inputs and set default values
                $ordering = $config->get('list_order_media', 'a.created DESC');
                $orderingParts = explode(' ', $ordering); 
                if (!$list = $app->getUserStateFromRequest($this->context . '.list', 'list', array(), 'array'))
                {
                        $list['fullordering'] = $ordering;
                        $list['limit'] = $config->get('list_limit', 6);
                        $app->setUserState($this->context . '.list', $list);
                }
                
		// List state information.
		parent::populateState($orderingParts[0], $orderingParts[1]);
                
                
                
                
                $mediaType = $this->getUserStateFromRequest('filter.mediaType', 'filter_mediaType', $config->get('list_default_media_type', '' ), 'integer', false);
                // If we are viewing a menu item that has a media type filter applied, then we need to show that instead of the user state.
                if ($config->get('list_default_media_type')) $mediaType = $config->get('list_default_media_type');
                $this->setState('filter.mediaType', $mediaType);

		// Load the display state.
		$display = $this->getUserStateFromRequest('media.media-display', 'display', $config->get('list_default_display', 'details' ), 'none', false);
                if (!in_array(strtolower($display), array('details', 'gallery', 'list'))) $display = 'details';
                $this->setState('media.media-display', $display);

                $catids = $config->get('catid');
                $this->setState('filter.category_id.include', (bool) $config->get('category_filtering_type', 1));

		// Category filter
		if ($catids) {                    
			if ($config->get('show_child_category_articles', 0) && (int) $config->get('levels', 0) > 0) {
				// Get an instance of the generic categories model
				$categories = JModelLegacy::getInstance('Categories', 'hwdMediaShareModel', array('ignore_request' => true));
				$categories->setState('params', $appParams);
				$levels = $config->get('levels', 1) ? $config->get('levels', 1) : 9999;
				$categories->setState('filter.get_children', $levels);
				$categories->setState('filter.published', 1);
				$additional_catids = array();

				foreach($catids as $catid)
				{
					$categories->setState('filter.parentId', $catid);
					$recursive = true;
					$items = $categories->getItems($recursive);

					if ($items)
					{
						foreach($items as $category)
						{
							$condition = (($category->level - $categories->getParent()->level) <= $levels);
                                                        if ($condition) {
								$additional_catids[] = $category->id;
							}

						}
					}
				}

				$catids = array_unique(array_merge($catids, $additional_catids));
			}

			$this->setState('filter.category_id', $catids);
		}

		// New Parameters
		$this->setState('filter.featured', $config->get('show_featured', 'show'));
		$this->setState('filter.author_id', $config->get('created_by', ""));
		$this->setState('filter.author_id.include', $config->get('author_filtering_type', 1));
		$this->setState('filter.author_alias', $config->get('created_by_alias', ""));
		$this->setState('filter.author_alias.include', $config->get('author_alias_filtering_type', 1));
		$excluded_articles = $config->get('excluded_articles', '');

		if ($excluded_articles) 
                {
			$excluded_articles = explode("\r\n", $excluded_articles);
			$this->setState('filter.article_id', $excluded_articles);
			$this->setState('filter.article_id.include', false); // Exclude
		}

		$date_filtering = $config->get('date_filtering', 'off');
		if ($date_filtering !== 'off') 
                {
			$this->setState('filter.date_filtering', $date_filtering);
			$this->setState('filter.date_field', $config->get('date_field', 'a.created'));
			$this->setState('filter.start_date_range', $config->get('start_date_range', '1000-01-01 00:00:00'));
			$this->setState('filter.end_date_range', $config->get('end_date_range', '9999-12-31 23:59:59'));
			$this->setState('filter.relative_date', $config->get('relative_date', 30));
		}

                // API filter parameters, not stored in the state
		JRequest::getInt('filter_group_id') > 0 ? $this->setState('filter.group_id', JRequest::getInt('filter_group_id')) : null;
		JRequest::getInt('filter_album_id') > 0 ? $this->setState('filter.album_id', JRequest::getInt('filter_album_id')) : null;
		JRequest::getInt('filter_playlist_id') > 0 ? $this->setState('filter.playlist_id', JRequest::getInt('filter_playlist_id')) : null;
		JRequest::getInt('filter_category_id') > 0 ? $this->setState('filter.category_id', JRequest::getInt('filter_category_id')) : null;
		JRequest::getInt('filter_favourites_id') > 0 ? $this->setState('filter.favourites_id', JRequest::getInt('filter_favourites_id')) : null;
		JRequest::getInt('filter_author_id') > 0 ? $this->setState('filter.author_id', JRequest::getInt('filter_author_id')) : null;
		JRequest::getWord('filter_featured') != '' ? $this->setState('filter.featured', JRequest::getWord('filter_featured')) : null;
		JRequest::getWord('filter_author_filtering_type') != '' ? $this->setState('filter.author_id.include', JRequest::getWord('filter_author_filtering_type')) : null;
		JRequest::getVar('filter_tag') != '' ? $this->setState('filter.tag', JRequest::getVar('filter_tag')) : null;                
	}
}
