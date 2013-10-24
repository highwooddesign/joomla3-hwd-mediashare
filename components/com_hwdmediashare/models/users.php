<?php
/**
 * @version    SVN $Id: users.php 1343 2013-03-20 11:01:00Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      16-Nov-2011 19:39:33
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla modelitem library
jimport('joomla.application.component.modellist');

/**
 * hwdMediaShare Model
 */
class hwdMediaShareModelUsers extends JModelList
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	public $_context = 'com_hwdmediashare.users';

	/**
	 * The category context (allows other extensions to derived from this model).
	 *
	 * @var		string
	 */
	protected $_extension = 'com_hwdmediashare';

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
				'title', 'title',
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

                // Get hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

		// Join over the users for the author and modified_by names.
                // Select DISTINCT to void duplicates when joining over subscription lists.
                $query->select('DISTINCT u.id');
                if ($config->get('author') == 1)
                {
                    $query->select("u.username AS title");
                }
                else
                {
                    $query->select("u.name AS title");
                }
                // We select the email so that gravatar will work correctly.
                $query->select("u.email AS email");

                $query->from('#__users AS u');

		// Select the required fields from the hwdMediaShare user table.
                $query->select('a.thumbnail_ext_id, a.key, a.description, a.alias, a.checked_out, a.checked_out_time,' .
			       'a.hits, a.published, a.likes, a.dislikes, a.featured,' .
			       'a.status, a.publish_up, a.publish_down, a.created, a.access,'.
			       'a.language');

                $query->select("CASE WHEN a.published > ' ' THEN a.published ELSE 1 END AS published");

                $query->join('LEFT', '`#__hwdms_users` AS a ON a.id = u.id');
                
                // Restrict based on access
                $query->where('a.access IN ('.$groups.')');
                
                // Restrict based on access
                $query->where('a.private = 0');
                
		// Join over the users for the author and modified_by names.
		if ($config->get('author') == 0)
                {
                    $query->select("CASE WHEN a.created_user_id_alias > ' ' THEN a.created_user_id_alias ELSE ua.username END AS author");
                }
                else
                {
                    $query->select("CASE WHEN a.created_user_id_alias > ' ' THEN a.created_user_id_alias ELSE ua.name END AS author");
                }
		$query->join('LEFT', '#__users AS ua ON ua.id = a.created_user_id');
		$query->join('LEFT', '#__users AS uam ON uam.id = a.modified_user_id');
                
                // Filter by groups.
		if ($groupId = $this->getState('filter.group_id'))
                {
                        $query->join('LEFT', '`#__hwdms_group_members` AS map ON map.member_id = a.id');
                        $query->where('map.group_id = ' . $db->quote($groupId));                        
		}
                
                // Filter by subscribers.
		if ($subscribersId = $this->getState('filter.subscribers_id'))
                {
                        $query->join('LEFT', '`#__hwdms_subscriptions` AS map ON map.user_id = a.id');
                        $query->where('map.element_type = 5');
                        $query->where('map.element_id = ' . $db->quote($subscribersId));                        
		}
                
                // Filter by subscriptions.
		if ($subscriptionsId = $this->getState('filter.subscriptions_id'))
                {
                        // If we are filtering by subscriptionsId then we are trying to get the user who has been 
                        // subscribed too, so we match the element_id with the id.
                        $query->join('LEFT', '`#__hwdms_subscriptions` AS map ON map.element_id = a.id');
                        $query->where('map.element_type = 5');
                        $query->where('map.user_id = ' . $db->quote($subscriptionsId));                        
		}                
                
		// Filter by state
		$published = $this->getState('filter.published');
		if (is_numeric($published))
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

		if (is_string($authorAlias)) {
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
				$query->where('(u.username LIKE '.$search.' OR u.name LIKE '.$search.' OR a.alias LIKE '.$search.')');
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
                
		// Add the list ordering clause.
		$orderCol	= $this->state->get($this->_context.'.list.ordering');
		$orderDirn	= $this->state->get($this->_context.'.list.direction');

		if (!empty($orderDirn) && !empty($orderDirn))
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
                
                // Here we check if we should list all users regardless of whether they have HWD channels or not, but we
                // prevent that from working when we are building users lists for specific elements, such as group members
                // or user subscribers.
                if ($config->get('channel_auto_create') == 1 && (!$subscribersId && !$subscriptionsId && !$groupId))
                {
                        // $query->where('NOT EXISTS (SELECT 1 FROM #__hwdms_users WHERE #__hwdms_users.id = u.id)', 'OR');
                        $query->where('1 = 1 OR NOT EXISTS (SELECT 1 FROM #__hwdms_users WHERE #__hwdms_users.id = u.id)', 'OR');
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
	protected function populateState()
	{
		// Initialise variables.
		$app	= JFactory::getApplication();

		// Load the parameters.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                $this->setState('params', $config);
                
                // Check if the ordering field is in the white list, otherwise use a default value.
                $listOrder = $app->getUserStateFromRequest($this->_context.'.list.ordering', 'filter_order', JRequest::getCmd('filter_order', $config->get('list_order_channel', 'a.created')));
                if (!in_array($listOrder, $this->filter_fields))
                {
                        $listOrder = 'a.created';
                }
                $this->setState($this->_context.'.list.ordering', $listOrder);                
                
		$listDirn = JRequest::getCmd('filter_order_Dir', 'DESC');
                if (in_array(strtolower($listOrder), array('title', 'author')))
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
		$this->setState('filter.search', $search);

                $mediaType = $this->getUserStateFromRequest($this->context.'.filter.mediaType', 'filter_mediaType', $config->get('list_default_media_type', '' ), 'integer', false);
                // If we are viewing a menu item that has a media type filter applied, then we need to show that instead of the user state.
                if ($config->get('list_default_media_type')) $mediaType = $config->get('list_default_media_type');
                $this->setState('filter.mediaType', $mediaType);

                // Load the parameters.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                $this->setState('params', $config);

		// Load the display state.
		$display = $this->getUserStateFromRequest('media.display', 'display', $config->get('list_default_display', 'details' ), 'none', false);
                if (!in_array(strtolower($display), array('details', 'list'))) $display = 'details';
		$this->setState('media.display', $display);
                
		// List state information.
		parent::populateState($listOrder, $listDirn);
                
		// Set HWD listing states
                $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $config->get('list_limit', $app->getCfg('list_limit') )); // Get global list limit from request, with default value read from HWD configuration and fallback to global Joomla value
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