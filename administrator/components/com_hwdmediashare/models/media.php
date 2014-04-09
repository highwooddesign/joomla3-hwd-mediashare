<?php
/**
 * @package     Joomla.administrator
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
	 * The element type to use with model methods.
	 * @var    integer
	 */    
	public $elementType = 1;

        /**
	 * Constructor override, defines a white list of column filters.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
				'alias', 'a.alias',
				'viewed', 'a.viewed',
				'private', 'a.private',
				'likes', 'a.likes',
				'dislikes', 'a.dislikes',
				'status', 'a.status',   
				'published', 'a.published', 
				'featured', 'a.featured',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'access', 'a.access', 'access_level',
				'ordering', 'a.ordering', 'map.ordering',
				'created_user_id', 'a.created_user_id', 'created_user_id_alias', 'a.created_user_id_alias', 'author',
				'created', 'a.created',
				'publish_up', 'a.publish_up',
				'publish_down', 'a.publish_down',
				'modified_user_id', 'a.modified_user_id',
				'modified', 'a.modified',
				'hits', 'a.hits',
				'language', 'a.language',
                                /** Filter fields for additional joins **/
				'report_count', 'a.report_count',                            
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
		$items = parent::getItems();

                for ($x = 0, $count = count($items); $x < $count; $x++)
                {
                        if (empty($items[$x]->author)) $items[$x]->author = JText::_('COM_HWDMS_GUEST');
                        
                        // Load categories
                        hwdMediaShareFactory::load('category');
                        $HWDcategory = hwdMediaShareCategory::getInstance();
                        $HWDcategory->elementType = 1;
                        $items[$x]->categories = $HWDcategory->get($items[$x]);
                }
                                
		return $items;
	}
        
	/**
	 * Method to get the database query.
	 *
	 * @return  JDatabaseQuery  database query
	 */
        protected function getListQuery()
        {
		// Initialise variables.
		$user = JFactory::getUser();
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
				'a.created_user_id, a.created_user_id_alias, a.thumbnail_ext_id, a.thumbnail, a.duration, a.type, a.hits, a.published, a.featured,' .
				'a.status, a.publish_up, a.publish_down, a.ordering, a.created, a.access,'.
				'a.language'
			)
		);
                
                // From the media table.
                $query->from('#__hwdms_media AS a');

		// Join over the language.
		$query->select('l.title AS language_title');
		$query->join('LEFT', '#__languages AS l ON l.lang_code = a.language');

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

                // Join over the users for the author, with value based on configuration.
                $config->get('author') == 0 ? $query->select('ua.name AS author') : $query->select('ua.username AS author');
		$query->join('LEFT', '#__users AS ua ON ua.id=a.created_user_id');
                
		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

                // Join over the file extensions.
                $query->select("CASE WHEN a.ext_id > 0 THEN ext.media_type ELSE a.media_type END AS media_type");
                $query->select('ext.ext');
		$query->join('LEFT', '#__hwdms_ext AS ext ON ext.id = a.ext_id');

		// Filter by a single or group of categories.
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

                                // Filter by published category
                                $cpublished = $this->getState('filter.c.published');
                                if (is_numeric($cpublished))
                                {
                                        $query->where('c.published = '.(int) $cpublished);
                                }
			}
		}

		// Filter by access level.
		if ($access = $this->getState('filter.access'))
                {
			$query->where('a.access = '.(int) $access);
		}

		// Filter by published state.
		$published = $this->getState('filter.published');
		if (is_numeric($published))
                {
			$query->where('a.published = '.(int) $published);
		} 
                else if ($published === '')
                {
			$query->where('(a.published IN (0, 1))');
		}

                // Filter by status state.
		$status = $this->getState('filter.status');
		if (is_numeric($status))
                {
			if ($status == 3)
                        {
                                $query->select('COUNT(report.element_id) AS report_count');
                                $query->join('LEFT', '#__hwdms_reports AS report ON report.element_id = a.id AND report.element_type = 1');
                                $query->where('(a.id = report.element_id OR a.status = 3)');
                                $query->group('report.element_id');
                        }
                        else
                        {
                                $query->where('a.status = '.(int) $status);
                        }
		}

		// Filter by search in title.
		$search = $this->getState('filter.search');
		if (!empty($search))
                {
			if (stripos($search, 'id:') === 0)
                        {
				$query->where('a.id = '.(int) substr($search, 3));
			} 
                        else
                        {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(a.title LIKE '.$search.' OR a.alias LIKE '.$search.')');
			}
		}

		// Filter on the language.
		if ($language = $this->getState('filter.language'))
                {
			$query->where('a.language = ' . $db->quote($language));
		}

		// Add the list ordering clause.
                $listOrder = $this->state->get('list.ordering');
                $listDirn = $this->state->get('list.direction');

		$query->order($db->escape($listOrder.' '.$listDirn));
                
   		// Group over the media ID to prevent duplicates.
                $query->group('a.id');

                /**
                 * Here we use some additional join and filters
                 * 
                 * 
                 */

                // Filter by media type.
		$mediaType = $this->getState('filter.media_type');
		if (is_numeric($mediaType))
                {
                        $query->where('(ext.media_type = '.(int) $mediaType.' OR a.media_type = '.(int) $mediaType.')');
		} 

                // Filter by album.
                $albumId = $this->getState('filter.album_id');
                if ($albumId > 0)
                {
                        // Join over the album_map
                        $query->select('map.id AS mapid, map.album_id, map.ordering AS mapordering, IF(map.album_id = '.$albumId.', true, false) AS connection');
                        $query->join('LEFT', '#__hwdms_album_map AS map ON map.media_id = a.id');

                        $viewAll = $this->getState('filter.add_to_album') ? true : false;
                        if (!$viewAll)
                        {
                                $query->where('map.album_id = ' . $db->quote($albumId));
                        }
                }

                // Filter by group.
                $groupId = $this->getState('filter.group_id');
                if ($groupId > 0)
                {
                        // Join over the group_map
                        $query->select('map.id AS mapid, map.group_id, map.ordering AS mapordering, IF(map.group_id = '.$groupId.', true, false) AS connection');
                        $query->join('LEFT', '#__hwdms_group_map AS map ON map.media_id = a.id');

                        $viewAll = $this->getState('filter.add_to_group') ? true : false;
                        if (!$viewAll)
                        {
                                $query->where('map.group_id = ' . $db->quote($groupId));
                        }
                }
                
                // Filter by playlist.
                $playlistId = $this->getState('filter.playlist_id');
                if ($playlistId > 0)
                {
                        // Join over the playlist_map
                        $query->select('map.id AS mapid, map.playlist_id, map.ordering AS mapordering, IF(map.playlist_id = '.$playlistId.', true, false) AS connection');
                        $query->join('LEFT', '#__hwdms_playlist_map AS map ON map.media_id = a.id');

                        $viewAll = $this->getState('filter.add_to_playlist') ? true : false;
                        if (!$viewAll)
                        {
                                $query->where('map.playlist_id = ' . $db->quote($playlistId));
                        }
                }

                // Filter by linked media.
		$mediaId = $this->getState('filter.media_id');
                if ($mediaId > 0)
                {
                        $query->where('a.id <> '.$mediaId);
                    
                        // Join over the media_map
                        $query->select('map.id AS mapid, map.media_id_1, map.media_id_2, map.ordering AS mapordering, IF(map.media_id_1 = '.$mediaId.' OR map.media_id_2 = '.$mediaId.', true, false) AS connection');
                        $query->join('LEFT', '#__hwdms_media_map AS map ON map.media_id_1 = a.id OR map.media_id_2 = a.id');

                        $viewAll = $this->getState('filter.add_to_media') ? true : false;
                        if (!$viewAll)
                        {
                                $query->where('(map.media_id_1 = ' . $db->quote($mediaId) . ' OR map.media_id_2 = ' . $db->quote($mediaId) . ')');
                        }
                }
                
                // Filter by linked responses.
                $responseId = $this->getState('filter.response_id');
                if ($responseId > 0)
                {
                        //$query->where('a.id <> '.$responseId);
                    
                        // Join over the response_map
                        $query->select('map.id AS mapid, map.response_id, map.ordering AS mapordering, IF(map.media_id = '.$responseId.', true, false) AS connection');
                        $query->join('LEFT', '#__hwdms_response_map AS map ON map.response_id = a.id');

                        $viewAll = $this->getState('filter.add_responses') ? true : false;
                        if (!$viewAll)
                        {
                                $query->where('map.media_id = ' . $db->quote($responseId));
                        }                    
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
	protected function populateState($ordering = null, $direction = null)
	{
		// List state information.
		parent::populateState('a.created', 'desc');
	}
}
