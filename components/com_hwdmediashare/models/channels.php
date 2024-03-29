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

class hwdMediaShareModelChannels extends JModelList
{
	/**
	 * Model context string.
         * 
         * @access      public
	 * @var         string
	 */ 
	public $context = 'com_hwdmediashare.channels';

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
                                // Unset this item if no title.
                                if (empty($items[$x]->title)) unset($items[$x]);
                                
                                // Add the number of media and subscribers to the channel.
                                $items[$x]->nummedia = $this->getNumMedia($items[$x]->id);
                                $items[$x]->numsubscribers = $this->getNumSubscribers($items[$x]->id);
                        }
                }

		return $items;
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
				'a.id, a.thumbnail_ext_id, a.key, a.title, a.alias, a.description, a.likes, a.dislikes, a.status,' .
                                'a.published, a.featured, a.checked_out, a.checked_out_time, a.access, a.ordering, a.created_user_id,' . 
                                'a.created_user_id_alias, a.created, a.publish_up, a.publish_down, a.hits, a.language'
			)
		);

                // From the channels table.
                $query->from('#__hwdms_users AS a');

                // Restrict based on access.
		if ($config->get('entice_mode') == 0)
                {
                        $query->where('a.access IN ('.$groups.')');
                }
                
                // Restrict based on privacy (listed/unlisted) access.
                $query->where('(a.private = 0 OR (a.private = 1 && a.id = '.$user->id.'))');
                
                // Join over the users table.
		$query->select('u.name, u.username, u.block, u.activation');
		$query->join('LEFT', '#__users AS u ON u.id=a.id');

                // Join over the users for the title (if empty), with value based on configuration.
                $config->get('author') == 0 ? $query->select('CASE WHEN a.title > ' . $db->quote(' ') . ' THEN a.title ELSE ua.name END AS title') : $query->select('CASE WHEN a.title > ' . $db->quote(' ') . ' THEN a.title ELSE ua.username END AS title');
		$query->join('LEFT', '#__users AS ua ON ua.id=a.id');
                
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
				$search = $db->quote('%'.$db->escape($search, true).'%');
				$query->where('(a.title LIKE '.$search.' OR a.alias LIKE '.$search.')');
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

                // Group over the key to prevent duplicates.
                $query->group('a.id');

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
                if ($this->context == 'com_hwdmediashare.channels')
                {                   
                        // Load the display state.
                        $this->setState('media.display_channels', 'details');

                        // Load the featured state.
                        $featured = $this->getUserStateFromRequest('channels.show_featured', 'show_featured', $config->get('show_featured', 'show'), 'word', false);
                        if (!in_array(strtolower($featured), array('show', 'hide', 'only'))) $display = 'show';
                        $this->setState('channels.show_featured', $featured);
                        $this->setState('filter.featured', $featured);

                        // Check for list inputs and set default values
                        $orderingFull = $config->get('list_order_channel', 'a.created DESC');
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
        
	/**
	 * Method to get a list of media associated with the channel.
	 *
	 * @access  public
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getNumMedia($channelId)
	{
                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                $this->_model = JModelLegacy::getInstance('Media', 'hwdMediaShareModel', array('ignore_request' => true));
                $this->_model->context = 'com_hwdmediashare.channels';
                $this->_model->populateState();
                $this->_model->setState('filter.author_id', $channelId);

                return $this->_model->getTotal();
	}   
        
	/**
	 * Method to get a list of members associated with the channel.
	 *
	 * @access  public
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getNumSubscribers($channelId)
	{
                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                $this->_model = JModelLegacy::getInstance('Users', 'hwdMediaShareModel', array('ignore_request' => true));
                $this->_model->context = 'com_hwdmediashare.channels';
                $this->_model->populateState();
                $this->_model->setState('filter.subscribers_id', $channelId);

                return $this->_model->getTotal();
	}         
}
