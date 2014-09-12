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
         * 
         * @access      public
	 * @var         string
	 */   
	public $context = 'com_hwdmediashare.media';
        
	/**
	 * Class constructor. Defines a white list of column filters.
	 *
	 * @access	public
	 * @param       array       $config     An optional associative array of configuration settings.
         * @return      void
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
	 * @access  public
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getItems()
	{
		if ($items = parent::getItems())
		{            
                        for ($x = 0, $count = count($items); $x < $count; $x++)
                        {
                                if (empty($items[$x]->author)) $items[$x]->author = JText::_('COM_HWDMS_GUEST');
                                
                                // Load categories.
                                hwdMediaShareFactory::load('category');
                                $HWDcategory = hwdMediaShareCategory::getInstance();
                                $items[$x]->categories = $HWDcategory->load($items[$x]);
                        }
                }

		return $items;
	}
        
	/**
	 * Method to return the first item in the list of media.
	 *
         * @access  public
	 * @return  mixed  A media item on success, false on failure.
	 */
	public function getItem()
	{
		if ($items = $this->getItems())
		{            
                        reset($items);
                        return current($items);
                }   
                
                return false;
	}
        
	/**
	 * Method to get the database query.
	 *
	 * @access  protected
	 * @return  JDatabaseQuery  database query
	 */
        public function getListQuery()
        {
                // Initialise variables.
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
				'a.id, a.ext_id, a.key, a.title, a.alias, a.description, a.type, a.source, a.storage, a.duration,' .
                                'a.thumbnail, a.thumbnail_ext_id, a.location, a.likes, a.dislikes, a.status, a.published, a.featured,' .
                                'a.checked_out, a.checked_out_time, a.access, a.ordering, a.created_user_id, a.created_user_id_alias,' .
                                'a.created, a.publish_up, a.publish_down, a.modified, a.hits, a.language'
			)
		);
                
                // From the media table.
                $query->from('#__hwdms_media AS a');

                // Restrict based on access.
		if ($config->get('entice_mode') == 0)
                {
                        $query->where('a.access IN ('.$groups.')');
                }
                
                // Restrict based on privacy (listed/unlisted) access.
                $query->where('(a.private = 0 OR (a.private = 1 && a.created_user_id = '.$user->id.'))');
                
                // Join over the users for the author, with value based on configuration.
                $config->get('author') == 0 ? $query->select('CASE WHEN a.created_user_id_alias > ' . $db->quote(' ') . ' THEN a.created_user_id_alias ELSE ua.name END AS author') : $query->select('CASE WHEN a.created_user_id_alias > ' . $db->quote(' ') . ' THEN a.created_user_id_alias ELSE ua.username END AS author');
		$query->join('LEFT', '#__users AS ua ON ua.id=a.created_user_id');

                // Join over the extensions table.
                $query->select('ext.ext');
                $query->select('CASE WHEN a.ext_id > 0 THEN ext.media_type ELSE a.media_type END AS media_type');
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
                                if ($searchMethod == 'match')
                                {
                                        $query->where('MATCH(a.title, a.description) AGAINST (' . $db->quote($search) . ' IN BOOLEAN MODE)');
                                }
                                else
                                {
                                        $search = $db->quote('%'.$db->escape($search, true).'%');
                                        $query->where('(a.title LIKE '.$search.' OR a.alias LIKE '.$search.')');
                                }
			}
		}

		// Filter on the language.
		if ($this->getState('filter.language'))
                {
			$query->where('a.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
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

		// Filter by a single or multiple categories.
		$categoryId = $this->getState('filter.category_id');
                if (is_numeric($categoryId) && $categoryId > 0) 
                {
			$type = $this->getState('filter.category_id.include', true) ? '= ' : '<> ';

			// Add subcategory check.
			$includeSubcategories = $this->getState('filter.subcategories', false);
			$categoryEquals = 'cmap.category_id '.$type.(int) $categoryId;

			if ($includeSubcategories) 
                        {
				$levels = (int) $this->getState('filter.max_category_levels', '1');
                                
				// Create a subquery for the subcategory list.
				$subQuery = $db->getQuery(true);
				$subQuery->select('sub.id');
				$subQuery->from('#__categories as sub');
				$subQuery->join('INNER', '#__categories as this ON sub.lft > this.lft AND sub.rgt < this.rgt');
				$subQuery->where('this.id = '.(int) $categoryId);
				if ($levels >= 0) 
                                {
					$subQuery->where('sub.level <= this.level + '.$levels);
				}

				// Add the subquery to the main query.
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

                        // Filter by published category.
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

                                // Filter by published category.
                                $cpublished = $this->getState('filter.c.published');
                                if (is_numeric($cpublished))
                                {
                                        $query->where('c.published = '.(int) $cpublished);
                                }
			}
		}
                
		// Filter by author.
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

		// Filter by published start and end dates.
		$nullDate = $db->quote($db->getNullDate());
		$nowDate = $db->quote(JFactory::getDate()->toSql());
		if ($this->getState('filter.publish_date'))
                {
			$query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')');
			$query->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');
		}

		// Filter by date range states.
		$dateFiltering = $this->getState('filter.date_filtering', 'off');
		$dateField = $this->getState('filter.date_field', 'a.created');
		switch ($dateFiltering)
		{
			case 'range':
				$startDateRange = $db->quote($this->getState('filter.start_date_range', $nullDate));
				$endDateRange = $db->quote($this->getState('filter.end_date_range', $nullDate));
				$query->where('('.$dateField.' >= '.$startDateRange.' AND '.$dateField . ' <= '.$endDateRange.')');
			break;
			case 'relative':
				$relativeDate = (int) $this->getState('filter.relative_date', 0);
				$query->where($dateField.' >= DATE_SUB('.$nowDate.', INTERVAL ' . $relativeDate.' DAY)');
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
		}                

                // Filter by media type.
		$mediaType = $this->getState('filter.media_type');
		if (is_numeric($mediaType))
                {
                        $query->where('(ext.media_type = '.(int) $mediaType.' OR a.media_type = '.(int) $mediaType.')');
		} 
                
                // Filter by album (allowing the display of media from a specific album).
		if ($albumId = $this->getState('filter.album_id'))
                {
                        $query->join('LEFT', '`#__hwdms_album_map` AS amap ON amap.media_id = a.id AND amap.album_id = '.$albumId);
                        $query->where('amap.album_id = ' . $db->quote($albumId));
		}

                // Filter by group (allowing the display of media from a specific group).
		if ($groupId = $this->getState('filter.group_id'))
                {
                        $query->join('LEFT', '`#__hwdms_group_map` AS gmap ON gmap.media_id = a.id AND gmap.group_id = '.$groupId);
                        $query->where('gmap.group_id = ' . $db->quote($groupId));
		}

                // Filter by playlist (allowing the display of media from a specific playlist).
		if ($playlistId = $this->getState('filter.playlist_id'))
                {
                        $query->select('pmap.ordering AS playlist_order');
                        $query->join('LEFT', '`#__hwdms_playlist_map` AS pmap ON pmap.media_id = a.id AND pmap.playlist_id = '.$playlistId);
                        $query->where('pmap.playlist_id = ' . $db->quote($playlistId));
		}
                
                // Filter by user favourites (allowing the display of media favourited by a user).
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
	 * @access  protected
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
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

                // Only set these states when in the com_hwdmediashare.media context.
                if ($this->context == 'com_hwdmediashare.media')
                {
                        // Load the display state.
                        $display = $this->getUserStateFromRequest('media.display', 'display', $config->get('list_default_display', 'details'), 'word', false);
                        if (!in_array(strtolower($display), array('details', 'gallery', 'list'))) $display = 'details';
                        $this->setState('media.display', $display);

                        // Load the featured state.
                        $featured = $this->getUserStateFromRequest('media.show_featured', 'show_featured', $config->get('show_featured', 'show'), 'word', false);
                        if (!in_array(strtolower($featured), array('show', 'hide', 'only'))) $display = 'show';
                        $this->setState('media.show_featured', $featured);
                        $this->setState('filter.featured', $featured);

                        // Check for list inputs and set default values.
                        $orderingFull = $config->get('list_order_media', 'a.created DESC');
                        $orderingParts = explode(' ', $orderingFull);
                        $ordering = $orderingParts[0];
                        $direction = $orderingParts[1];
                        if (!$list = $app->getUserStateFromRequest($this->context . '.list', 'list', array(), 'array'))
                        {
                                $list['fullordering'] = $orderingFull;
                                $list['limit'] = $config->get('list_limit', 6);
                                $app->setUserState($this->context . '.list', $list);
                        }

                        // Load the media type state from the menu item parameters.
                        if ($config->get('list_default_media_type')) 
                        {
                                $mediaType = $config->get('list_default_media_type');
                                $this->setState('filter.media_type', $mediaType);                            
                        }
                        
                        // Load the category filter state.
                        $catids = $config->get('catid');
                        if (is_array($catids)) $catids = array_filter($catids); // Remove empty array elements.
                        if ($catids) 
                        {     
                                $this->setState('filter.category_id.include', (bool) $config->get('category_filtering_type', 1));
                            
                                if ($config->get('show_child_category_articles', 0) && (int) $config->get('levels', 0) > 0)
                                {
                                        // Get an instance of the categories model.
                                        $categories = JModelLegacy::getInstance('Categories', 'hwdMediaShareModel', array('ignore_request' => true));
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
                                                                if ($condition) 
                                                                {
                                                                        $additional_catids[] = $category->id;
                                                                }
                                                        }
                                                }
                                        }

                                        $catids = array_unique(array_merge($catids, $additional_catids));
                                }

                                $this->setState('filter.category_id', $catids);
                        }
                        
                        /**
                         * When populating the state from the 'com_hwdmediashare.media' context we set the states
                         * above from the request. That data is coming from the config, menu parameters or the user
                         * who is viewing. We directly set other states from the request allowing various useful
                         * filters to be applied using the HWD API.
                         */ 
                        if ($app->input->get('filter_group_id', '', 'integer') > 0)         $this->setState('filter.group_id', $app->input->get('filter_group_id', '', 'integer'));
                        if ($app->input->get('filter_album_id', '', 'integer') > 0)         $this->setState('filter.album_id', $app->input->get('filter_album_id', '', 'integer'));
                        if ($app->input->get('filter_playlist_id', '', 'integer') > 0)      $this->setState('filter.playlist_id', $app->input->get('filter_playlist_id', '', 'integer'));
                        if ($app->input->get('filter_category_id', '', 'integer') > 0)      $this->setState('filter.category_id', $app->input->get('filter_category_id', '', 'integer'));
                        if ($app->input->get('filter_favourites_id', '', 'integer') > 0)    $this->setState('filter.favourites_id', $app->input->get('filter_favourites_id', '', 'integer'));
                        if ($app->input->get('filter_author_id', '', 'integer') > 0)        $this->setState('filter.author_id', $app->input->get('filter_author_id', '', 'integer'));
                        if ($app->input->get('filter_media_type', '', 'integer') > 0)       $this->setState('filter.media_type', $app->input->get('filter_media_type', '', 'integer'));
                }    
                
                // List state information.
                parent::populateState($ordering, $direction);                
	}
}
