<?php
/**
 * @version    SVN $Id: media.php 929 2013-01-16 11:31:34Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      09-Nov-2011 14:05:28
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla modelitem library
jimport('joomla.application.component.modellist');

/**
 * hwdMediaShare Model
 */
class hwdMediaShareModelMedia extends JModelList
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	public $_context = 'com_hwdmediashare.media';

	/**
	 * The category context (allows other extensions to derived from this model).
	 *
	 * @var		string
	 */
	protected $_extension = 'com_hwdmediashare';

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
				'created', 'a.created',
				'hits', 'a.hits',
				'title', 'a.title',
				'likes', 'a.likes',
				'dislikes', 'a.dislikes',
				'modified', 'a.modified',
				'viewed', 'a.viewed',
				'title', 'a.title',
                                'author', 'author',
                                'created', 'a.created',
                                'ordering', 'a.ordering',
                                'random', 'random',
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
        public function getListQuery()
        {
		$user	= JFactory::getUser();
		$groups	= implode(',', $user->getAuthorisedViewLevels());

                // Create a new query object.
                $db = JFactory::getDBO();
                $query = $db->getQuery(true);

                // Get hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

		// Select the required fields from the table.
                // Modified for xmap
		$query->select(
			$this->getState(
				'list.select',
				'a.id, a.key, a.ext_id, a.title, a.description, a.alias, a.checked_out, a.checked_out_time,' .
				'a.created_user_id, a.thumbnail_ext_id, a.location, a.hits, a.thumbnail, a.type, a.published, a.likes, a.dislikes, a.featured,' .
				'a.status, a.publish_up, a.publish_down, a.duration, a.source, a.ordering, a.created, a.access,'.
				'a.language, a.modified, a.created_user_id_alias'
			)
		);
                // From the hello table
                $query->from('#__hwdms_media AS a');

                // Restrict based on access
		if ($config->get('entice_mode') == 0)
                {
                        $query->where('a.access IN ('.$groups.')');
                }

                // Restrict based on access
                $query->where('(a.private = 0 OR (a.private = 1 && a.created_user_id = '.$user->id.'))');

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

		// Join over the users for the author and modified_by names.
		if ($config->get('author') == 0)
                {
                    $query->select("CASE WHEN a.created_user_id_alias > ' ' THEN a.created_user_id_alias ELSE ua.name END AS author");
                }
                else
                {
                    $query->select("CASE WHEN a.created_user_id_alias > ' ' THEN a.created_user_id_alias ELSE ua.username END AS author");
                }
		$query->join('LEFT', '#__users AS ua ON ua.id = a.created_user_id');
		$query->join('LEFT', '#__users AS uam ON uam.id = a.modified_user_id');

		// Filter by state
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
                else if (is_numeric($published))
                {
			$query->where('a.published = '.(int) $published);
		}
                
                // Filter by status
		$status = $this->getState('filter.status');
		if (is_numeric($status))
                {
			$query->where('a.status = '.(int) $status);
		}
                
		// Filter by author
		$authorId = $this->getState('filter.author_id');
		$authorWhere = '';

		if (is_numeric($authorId)) {
			$type = $this->getState('filter.author_id.include', true) ? '= ' : '<> ';
			$authorWhere = 'a.created_user_id '.$type.(int) $authorId;
		}
		elseif (is_array($authorId)) {
			JArrayHelper::toInteger($authorId);
			$authorId = implode(',', $authorId);

			if ($authorId) {
				$type = $this->getState('filter.author_id.include', true) ? 'IN' : 'NOT IN';
				$authorWhere = 'a.created_user_id '.$type.' ('.$authorId.')';
			}
		}

		// Filter by author alias
		$authorAlias = $this->getState('filter.author_alias');
		$authorAliasWhere = '';

		// Needed to add an empty check to avoid empty strings
                if (is_string($authorAlias) && !empty($authorAlias)) {
			$type = $this->getState('filter.author_alias.include', true) ? '= ' : '<> ';
			$authorAliasWhere = 'a.created_user_id_alias '.$type.$db->Quote($authorAlias);
		}
		elseif (is_array($authorAlias)) {
			$first = current($authorAlias);

			if (!empty($first)) {
				JArrayHelper::toString($authorAlias);

				foreach ($authorAlias as $key => $alias)
				{
					$authorAlias[$key] = $db->Quote($alias);
				}

				$authorAlias = implode(',', $authorAlias);

				if ($authorAlias) {
					$type = $this->getState('filter.author_alias.include', true) ? 'IN' : 'NOT IN';
					$authorAliasWhere = 'a.created_user_id_alias '.$type.' ('.$authorAlias .')';
				}
			}
		}

		if (!empty($authorWhere) && !empty($authorAliasWhere)) {
			$query->where('('.$authorWhere.' OR '.$authorAliasWhere.')');
		}
		elseif (empty($authorWhere) && empty($authorAliasWhere)) {
			// If both are empty we don't want to add to the query
		}
		else {
			// One of these is empty, the other is not so we just add both
			$query->where($authorWhere.$authorAliasWhere);
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
				$query->where('('.$dateField.' >= '.$startDateRange.' AND '.$dateField .
					' <= '.$endDateRange.')');
				break;

			case 'relative':
				$relativeDate = (int) $this->getState('filter.relative_date', 0);
				$query->where($dateField.' >= DATE_SUB('.$nowDate.', INTERVAL ' .
					$relativeDate.' DAY)');
				break;

			case 'off':
			default:
				break;
		}
                
		// Filter by language
		if ($this->getState('filter.language'))
                {
			$query->where('a.language in (' . $db->Quote(JFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')');
		}

                // Join over the extensions table.
                $query->select("CASE WHEN a.ext_id > 0 THEN ext.media_type ELSE a.media_type END AS media_type");
                $query->select('ext.ext');
		$query->join('LEFT', '#__hwdms_ext AS ext ON ext.id = a.ext_id');

                // Filter by media type
		$mediaType = $this->getState('filter.mediaType');
		if (!empty($mediaType))
                {
                        $query->where('(ext.media_type = '.$mediaType.' OR a.media_type = '.$mediaType.')');
		}

		// Filter by search in title
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
                
		// Filter by featured state
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

                // Filter by tag
		if ($tag = $this->getState('filter.tag'))
                {
                        $query->join('LEFT', '`#__hwdms_tag_map` AS tmap ON tmap.element_id = a.id');                                           
                        $query->join('LEFT', '`#__hwdms_tags` AS tags ON tags.id = tmap.tag_id');                                           
                        $query->where('tags.tag = ' . $db->quote($tag));
		}
                
		// Add the list ordering clause.
		$orderCol	= $this->state->get($this->_context.'.list.ordering');
		$orderDirn	= $this->state->get($this->_context.'.list.direction');

		if (!empty($orderCol) && !empty($orderDirn))
		{
                        if ($orderCol == 'random')
                        {
                                $query->order('RAND()');
                        }
                        else
                        {
                                $query->order($db->escape($orderCol.' '.$orderDirn));
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
		$app = JFactory::getApplication();
		$appParams = $app->getParams();
                
		// Load the parameters.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                $this->setState('params', $config);
                
                // Check if the ordering field is in the white list, otherwise use a default value.
                $listOrder = $app->getUserStateFromRequest($this->_context.'.list.ordering', 'filter_order', JRequest::getCmd('filter_order', $config->get('list_order_media', 'a.created')));
                if (!in_array($listOrder, $this->filter_fields))
                {
                        $listOrder = 'a.created';
                }
                $this->setState($this->_context.'.list.ordering', $listOrder);                
                
		$listDirn = JRequest::getCmd('filter_order_Dir', 'DESC');
                if (in_array(strtolower($listOrder), array('a.title', 'author', 'a.ordering')))
                {
                        $listDirn = 'ASC';
                }
                else if (!in_array(strtoupper($listDirn), array('ASC', 'DESC', '')))
                {
                        $listDirn = 'DESC';
		}
                $this->setState($this->_context.'.list.direction', $listDirn);

		$user = JFactory::getUser();
		if ((!$user->authorise('core.edit.state', 'com_hwdmediashare')) &&  (!$user->authorise('core.edit', 'com_hwdmediashare')))
                {
			// Limit to published for people who can't edit or edit.state.
			$this->setState('filter.published',	1);
			$this->setState('filter.status',	1);

			// Filter by start and end dates.
			$this->setState('filter.publish_date', true);
		}
                else
                {
			// Limit to published for people who can't edit or edit.state.
			$this->setState('filter.published',	array(0,1));
			$this->setState('filter.status',	1);
                }

		$this->setState('filter.language',$app->getLanguageFilter());

		// Load the filter state.
		$search = $this->getUserStateFromRequest('filter.search', 'filter_search');
		//$search = $this->getUserStateFromRequest('filter.search', 'filter_search', '', 'none', false);
		$this->setState('filter.search', $search);

                //@TODO: This code will prevent issue with the pagination when a search filter has been applied
                //$search = JRequest::getString('filter_search');
                //if (empty($search))
                //{
                //        $search = JFactory::getApplication()->getUserState('filter.search');                        
                //}                
		//$this->setState('filter.search', $search);

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

                // List state information.
		parent::populateState($listOrder, $listDirn);
                
		// Set HWD listing (pagination) states
                $limit = $app->getUserStateFromRequest($this->context . '.list.limit', 'limit', $config->get('list_limit', $app->getCfg('list_limit') )); // Get global list limit from request, with default value read from HWD configuration and fallback to global Joomla value
		$this->setState('list.limit', $limit);

		if (JRequest::getVar('limitstart', 0, '', 'int') == 0) JRequest::setVar('limitstart', 0); // We want to go to page one, unless a different page has been specifically selected
                $value = $app->getUserStateFromRequest($this->context . '.limitstart', 'limitstart', 0);
         
		$limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
		$this->setState('list.start', $limitstart);
	}

      	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 *
	 * @return	string		A store id.
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.extension');
		$id	.= ':'.$this->getState('filter.published');
		$id	.= ':'.$this->getState('filter.access');
		$id	.= ':'.$this->getState('filter.parentId');

		return parent::getStoreId($id);
        }
}
