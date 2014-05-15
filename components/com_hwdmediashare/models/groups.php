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

class hwdMediaShareModelGroups extends JModelList
{
	/**
	 * Model context string.
	 * @var string
	 */
	public $context = 'com_hwdmediashare.groups';

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
                        }
                }

		return $items;
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
				'a.id, a.key, a.thumbnail_ext_id, a.title, a.description, a.alias, a.checked_out, a.checked_out_time,' .
				'a.created_user_id, a.hits, a.likes, a.dislikes, a.published, a.featured,' .
				'a.status, a.publish_up, a.publish_down, a.ordering, a.created, a.access,'.
				'a.language, a.created_user_id_alias'
			)
		);
                
                // From the groups table.
                $query->from('#__hwdms_groups AS a');

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

                // Filter by media.
		$mediaId = $this->getState('filter.media.id');
                if (is_numeric($mediaId))
                {
                        $query->join('LEFT', '#__hwdms_group_map AS gmap ON gmap.group_id = a.id');
                        $query->where('gmap.media_id = ' . $db->quote($mediaId));
		}
                
                // Filter by membership.
                $memberId = $this->getState('filter.member_id');
                if (is_numeric($memberId))
                {
                        $query->join('LEFT', '#__hwdms_group_members AS gmem ON gmem.group_id = a.id');
                        $query->where('gmem.member_id = ' . $db->quote($memberId));
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

                // Only set these states when in the com_hwdmediashare.media context.
                if ($this->context == 'com_hwdmediashare.groups')
                {                   
                        // Load the display state.
                        $display = $this->getUserStateFromRequest('media.display_groups', 'display', $config->get('list_default_display', 'details' ), 'word', false);
                        if (!in_array(strtolower($display), array('details', 'list'))) $display = 'details';
                        $this->setState('media.display_groups', $display);

                        // Load the featured state.
                        $featured = $this->getUserStateFromRequest('groups.show_featured', 'show_featured', $config->get('show_featured', 'show' ), 'word', false);
                        if (!in_array(strtolower($featured), array('show', 'hide', 'only'))) $display = 'show';
                        $this->setState('groups.show_featured', $featured);
                        $this->setState('filter.featured', $featured);

                        // Check for list inputs and set default values
                        $orderingFull = $config->get('list_order_group', 'a.created DESC');
                        $orderingParts = explode(' ', $orderingFull); 
                        $ordering = $orderingParts[0];
                        $direction = $orderingParts[1];                           
                        if (!$list = $app->getUserStateFromRequest($this->context . '.list', 'list', array(), 'array'))
                        {
                                $list['fullordering'] = $orderingFull;
                                $list['limit'] = $config->get('list_limit', 6);
                                $app->setUserState($this->context . '.list', $list);
                        }
                }
                
                // List state information.
                parent::populateState($ordering, $direction); 
	}
}
