<?php
/**
 * @version    SVN $Id: media.php 1402 2013-04-30 09:31:35Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modellist');

/**
 * hwdMediaShare Model
 */
class hwdMediaShareModelMedia extends JModelList
{
	/**
	 * Element type
	 */ 
        var $elementType = 1;
        
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	0.1
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
				'alias', 'a.alias',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'catid', 'a.catid', 'category_title',
				'state', 'a.state',
				'access', 'a.access', 'access_level',
				'created', 'a.created',
				'created_by', 'a.created_by',
				'ordering', 'a.ordering',
				'featured', 'a.featured',
				'language', 'a.language',
				'hits', 'a.hits',
				'publish_up', 'a.publish_up',
				'publish_down', 'a.publish_down',
			);
		}

		parent::__construct($config);
	}

        /**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getItems($pk = null)
	{
                if ($items = parent::getItems($pk))
                {
                        for ($i=0, $n=count($items); $i < $n; $i++)
                        {
                                if (empty($items[$i]->author))
                                {
                                        $items[$i]->author = JText::_('COM_HWDMS_GUEST');
                                }                            
                                hwdMediaShareFactory::load('category');
                                $items[$i]->categories = hwdMediaShareCategory::get($items[$i]);
                        }
                }
		return $items;
	}
        /**
         * Method to build an SQL query to load the list data.
         *
         * @return      string  An SQL query
         */
        protected function getListQuery()
        {
		$user	= JFactory::getUser();
		$groups	= implode(',', $user->getAuthorisedViewLevels());

                // Create a new query object.
                $db = JFactory::getDBO();
                $query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.id, a.key, a.ext_id, a.title, a.description, a.alias, a.checked_out, a.checked_out_time,' .
				'a.created_user_id, a.thumbnail_ext_id, a.thumbnail, a.duration, a.type, a.hits, a.published, a.featured,' .
				'a.status, a.publish_up, a.publish_down, a.ordering, a.created, a.access,'.
				'a.language'
			)
		);
                // From the hello table
                $query->from('#__hwdms_media AS a');

		// Join over the language
		$query->select('l.title AS language_title');
		$query->join('LEFT', '`#__languages` AS l ON l.lang_code = a.language');

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

                // Join over the asset groups.
		$query->select('CASE WHEN a.ext_id > 0 THEN ext.media_type ELSE a.media_type END AS media_type');
		$query->join('LEFT', '#__hwdms_ext AS ext ON ext.id = a.ext_id');

		// Filter by a single or group of categories
		$categoryId = $this->getState('filter.category_id');

                // Filter by a single or group of categories
                if (is_numeric($categoryId) && $categoryId > 0) 
                {
			$type = $this->getState('filter.category_id.include', true) ? '= ' : '<> ';

			// Add subcategory check
			$includeSubcategories = $this->getState('filter.subcategories', false);
			$categoryEquals = 'cmap.category_id '.$type.(int) $categoryId;

			if ($includeSubcategories) {
				$levels = (int) $this->getState('filter.max_category_levels', '1');
				// Create a subquery for the subcategory list
				$subQuery = $db->getQuery(true);
				$subQuery->select('sub.id');
				$subQuery->from('#__categories as sub');
				$subQuery->join('INNER', '#__categories as this ON sub.lft > this.lft AND sub.rgt < this.rgt');
				$subQuery->where('this.id = '.(int) $categoryId);
				if ($levels >= 0) {
					$subQuery->where('sub.level <= this.level + '.$levels);
				}

				// Add the subquery to the main query
				$query->where('('.$categoryEquals.' OR cmap.category_id IN ('.$subQuery->__toString().'))');
			}
			else {
				$query->where($categoryEquals);
			}
                        $query->where('cmap.element_type = 1');
                        
                        $query->join('LEFT', '#__hwdms_category_map AS cmap ON cmap.element_id = a.id');
                        $query->join('LEFT', '#__categories AS c ON c.id = cmap.category_id');

                        $query->where('c.access IN ('.$groups.')');

                        //Filter by published category
                        $cpublished = $this->getState('filter.c.published');
                        if (is_numeric($cpublished)) {
                                $query->where('c.published = '.(int) $cpublished);
                        }
		}
		elseif (is_array($categoryId) && (count($categoryId) > 0)) {
			JArrayHelper::toInteger($categoryId);
			$categoryId = implode(',', $categoryId);
			if (!empty($categoryId)) {
				$type = $this->getState('filter.category_id.include', true) ? 'IN' : 'NOT IN';
                                $query->where('cmap.category_id '.$type.' ('.$categoryId.')');
                                $query->where('cmap.element_type = 1');

                                $query->join('LEFT', '#__hwdms_category_map AS cmap ON cmap.element_id = a.id');
                                $query->join('LEFT', '#__categories AS c ON c.id = cmap.category_id');
                                
                                $query->where('c.access IN ('.$groups.')');

                                //Filter by published category
                                $cpublished = $this->getState('filter.c.published');
                                if (is_numeric($cpublished)) {
                                        $query->where('c.published = '.(int) $cpublished);
                                }
			}
		}

		// Filter by access level.
		if ($access = $this->getState('filter.access'))
                {
			$query->where('a.access = '.(int) $access);
		}

		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('a.published = '.(int) $published);
		} else if ($published === '') {
			$query->where('(a.published IN (0, 1))');
		}

                // Filter by status state
		$status = $this->getState('filter.status');
		if (is_numeric($status))
                {
			if ($status == 3)
                        {
                                $query->select('COUNT(report.element_id) AS report_count');
                                $query->join('LEFT', '`#__hwdms_reports` AS report ON report.element_id = a.id AND report.element_type = 1');
                                $query->where('a.id = report.element_id');
                                $query->group('report.element_id');
                        }
                        else
                        {
                                $query->where('a.status = '.(int) $status);
                        }
		}

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search))
                {
			if (stripos($search, 'id:') === 0)
                        {
				$query->where('a.id = '.(int) substr($search, 3));
			} else {
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
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');

		$query->order($db->escape($orderCol.' '.$orderDirn));
                $query->group('a.id');

                // Additional filters
		$albumId = $this->getState('filter.album_id');
                if ($albumId > 0)
                {
                        // Join over the language
                        $query->select('IF(map.album_id = '.$albumId.', true, false) AS connection');
                        $query->join('LEFT', '`#__hwdms_album_map` AS map ON map.media_id = a.id AND map.album_id = '.$albumId);

                        $viewAll = $this->getState('filter.linked') == "all" ? true:false;
                        if (!$viewAll)
                        {
                                $query->where('map.album_id = ' . $db->quote($albumId));
                        }
                }

                $groupId = $this->getState('filter.group_id');
                if ($groupId > 0)
                {
                        // Join over the language
                        $query->select('IF(map.group_id = '.$groupId.', true, false) AS connection');
                        $query->join('LEFT', '`#__hwdms_group_map` AS map ON map.media_id = a.id AND map.group_id = '.$groupId);

                        $viewAll = $this->getState('filter.linked') == "all" ? true:false;
                        if (!$viewAll)
                        {
                                $query->where('map.group_id = ' . $db->quote($groupId));
                        }
                }

                $playlistId = $this->getState('filter.playlist_id');
                if ($playlistId > 0)
                {
                        // Join over the language
                        $query->select('map.id AS mapid, map.playlist_id, map.ordering, IF(map.playlist_id = '.$playlistId.', true, false) AS connection');
                        $query->join('LEFT', '`#__hwdms_playlist_map` AS map ON map.media_id = a.id AND map.playlist_id = '.$playlistId);

                        $viewAll = $this->getState('filter.linked') == "all" ? true:false;
                        if (!$viewAll)
                        {
                                $query->where('map.playlist_id = ' . $db->quote($playlistId));
                        }
                }

		$mediaId = $this->getState('filter.media_id');
                if ($mediaId > 0)
                {
                        $query->where('a.id <> '.$mediaId);

                        // Join over the language
                        $query->select('IF(map.media_id_1 = '.$mediaId.' OR map.media_id_2 = '.$mediaId.', true, false) AS connection');
                        $query->join('LEFT', '`#__hwdms_media_map` AS map ON map.media_id_1 = a.id OR map.media_id_2 = a.id');

                        $viewAll = $this->getState('filter.linked') == "all" ? true:false;
                        if (!$viewAll)
                        {
                                $query->where('map.media_id_1 = ' . $db->quote($mediaId) . ' OR map.media_id_2 = ' . $db->quote($mediaId));
                        }
                }

                $responseId = $this->getState('filter.response_id');
                if ($responseId > 0)
                {
                        // Join over the language
                        $query->select('IF(map.media_id = '.$responseId.', true, false) AS connection');
                        $query->join('LEFT', '`#__hwdms_response_map` AS map ON map.response_id = a.id AND map.media_id = '.$responseId);

                        $viewAll = $this->getState('filter.linked') == "all" ? true:false;
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
	 * @since	0.1
	 */
	protected function populateState($ordering = null, $direction = null)
	{          
		// Initialise variables.
		$app = JFactory::getApplication('administrator');
 
		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$accessId = $this->getUserStateFromRequest($this->context.'.filter.access', 'filter_access', null, 'int');
		$this->setState('filter.access', $accessId);

		$published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '', 'string');
		$this->setState('filter.published', $published);

                $status = $this->getUserStateFromRequest($this->context.'.filter.status', 'filter_status', '', 'string');
		$this->setState('filter.status', $status);
 
		$categoryId = $this->getUserStateFromRequest($this->context.'.filter.category_id', 'filter_category_id', '');
		$this->setState('filter.category_id', $categoryId);

		$language = $this->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);

                // Passing additional parameters to prevent resetting the page
                $listOrder = $this->getUserStateFromRequest($this->context.'.filter_order', 'filter_order', 'a.created', null, false);
                $this->setState('list.ordering', $listOrder);

                // Passing additional parameters to prevent resetting the page
                $listDirn  = $this->getUserStateFromRequest($this->context.'.filter_order_Dir', 'filter_order_Dir', 'DESC', null, false);
                $this->setState('list.direction', $listDirn);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_hwdmediashare');
		$this->setState('params', $params);

		// List state information.
		parent::populateState($listOrder, $listDirn);
	}
}
