<?php
/**
 * @version    SVN $Id: activities.php 575 2012-10-15 14:43:54Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      20-Jan-2012 09:21:20
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla modelitem library
jimport('joomla.application.component.modellist');

/**
 * hwdMediaShare Model
 */
class hwdMediaShareModelActivities extends JModelList
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	public $_context = 'com_hwdmediashare.activities';

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
                                
                                $items[$i]->verb = $this->getActivityType($items[$i]); 
                                $items[$i]->actor = '<a href="'.JRoute::_(hwdMediaShareHelperRoute::getUserRoute($items[$i]->created_user_id)).'">'.$items[$i]->author.'</a>';                         
                                $items[$i]->object = null;                         
                                $items[$i]->target = null;                         
                                switch ($items[$i]->activity_type) 
                                {
                                    // Comment
                                    case 1:
                                        break;
                                    // New media
                                    case 2: 
                                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');                                       
                                        $table = JTable::getInstance('Media', 'hwdMediaShareTable');
                                        if ($table->load($items[$i]->element_id))
                                        {
                                                    $properties = $table->getProperties(1);
                                                    $media = JArrayHelper::toObject($properties, 'JObject');
                                                    $items[$i]->object = '<a href="'.JRoute::_(hwdMediaShareHelperRoute::getMediaItemRoute($media->id)).'">'.$media->title.'</a>'; 

                                        }
                                        break;
                                    // New album
                                    case 3:
                                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');                                       
                                        $table = JTable::getInstance('Album', 'hwdMediaShareTable');
                                        if ($table->load($items[$i]->element_id))
                                        {
                                                    $properties = $table->getProperties(1);
                                                    $album = JArrayHelper::toObject($properties, 'JObject');
                                                    $items[$i]->object = '<a href="'.JRoute::_(hwdMediaShareHelperRoute::getAlbumRoute($album->id)).'">'.$album->title.'</a>'; 
                                        }
                                        break;  
                                    case 4:// New group
                                    case 7:// Joined group
                                    case 8:// Left group
                                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');                                       
                                        $table = JTable::getInstance('Group', 'hwdMediaShareTable');
                                        if ($table->load($items[$i]->element_id))
                                        {
                                                    $properties = $table->getProperties(1);
                                                    $group = JArrayHelper::toObject($properties, 'JObject');
                                                    $items[$i]->object = '<a href="'.JRoute::_(hwdMediaShareHelperRoute::getGroupRoute($group->id)).'">'.$group->title.'</a>'; 
                                        }
                                        break;
                                    // New playlist
                                    case 5:
                                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');                                       
                                        $table = JTable::getInstance('Playlist', 'hwdMediaShareTable');
                                        if ($table->load($items[$i]->element_id))
                                        {
                                                    $properties = $table->getProperties(1);
                                                    $playlist = JArrayHelper::toObject($properties, 'JObject');
                                                    $items[$i]->object = '<a href="'.JRoute::_(hwdMediaShareHelperRoute::getAlbumRoute($playlist->id)).'">'.$playlist->title.'</a>'; 
                                        }
                                        break;  
                                    case 6: 
                                        break;
        //                            case 7:
        //                                break;
        //                            case 8:
        //                                break;
                                    case 9: // Share media with group
                                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');                                       
                                        $table = JTable::getInstance('Group', 'hwdMediaShareTable');
                                        if ($table->load($items[$i]->element_id))
                                        {
                                                    $properties = $table->getProperties(1);
                                                    $group = JArrayHelper::toObject($properties, 'JObject');
                                                    $items[$i]->object = '<a href="'.JRoute::_(hwdMediaShareHelperRoute::getAlbumRoute($group->id)).'">'.$group->title.'</a>'; 
                                        }  
                                        break;
                                }
                        }
                }
                
                if (count($items) > 0)
                {
                        $this->getChildren($items);
                }
		return $items;
	}
        
	/**
	 * Used to trigger applications
	 * @param	string	eventName
	 * @param	array	params to pass to the function
	 * @param	bool	do we need to use custom user ordering ?
	 *
	 * returns	Array	An array of object that the caller can then manipulate later.
	 **/
	public function getChildren(&$items)
	{
                if (!isset($items) || count($items) == 0)
                {
                        return;
                }

                foreach($items as $item)
                {
                        $this->setState('reply.id', $item->id);
                        $db =& JFactory::getDBO();
 
                        // Can not call getItems() because the query is already defined
                        // $query = $this->getListQuery();
                        $query = hwdMediaShareModelActivities::getListQuery();
                        $db->setQuery($query);
                        $item->children = $db->loadObjectList();

                        //$this->getChildren($item->children);
                        hwdMediaShareModelActivities::getChildren($item->children);
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
		$query->select(
			$this->getState(
				'list.select',
				'a.id, a.activity_type, a.element_type, a.element_id, a.element_type, a.reply_id,' .
                                'a.title, a.description, a.alias, a.checked_out, a.checked_out_time,' .
				'a.created_user_id, a.hits, a.likes, a.dislikes, a.published, a.featured,' .
				'a.status, a.publish_up, a.publish_down, a.ordering, a.created, a.access,'.
				'a.language, a.created_user_id_alias'
			)
		);
                // From the hello table
                $query->from('#__hwdms_activities AS a');

                // Restrict based on access
                $query->where('a.access IN ('.$groups.')');

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

                // Filter by element type.
		$replyId = $this->getState('reply.id');
                if (is_numeric($replyId))
                {
                        $query->where('a.reply_id = ' . $db->quote($replyId));
		}
                
                // Filter by element type.
		if ($elementType = $this->getState('element.type'))
                {
                        $query->where('a.element_type = ' . $db->quote($elementType));
		}
                
                // Filter by user.
		if ($elementId = $this->getState('element.id'))
                {
                        $query->where('a.element_id = ' . $db->quote($elementId));
		}
                
                // Filter by user.
		if ($userId = $this->getState('user.id'))
                {
                        $query->where('a.created_user_id = ' . $db->quote($userId));
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

		// Filter by start and end dates.
		$nullDate = $db->Quote($db->getNullDate());
		$nowDate = $db->Quote(JFactory::getDate()->toSql());

		if ($this->getState('filter.publish_date'))
                {
			$query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')');
			$query->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');
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
				$query->where('(a.title LIKE '.$search.' OR a.alias LIKE '.$search.')');
			}
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

		$listOrder = JRequest::getCmd('filter_order', 'a.created');
                if (!in_array($listOrder, $this->filter_fields))
                {
			$listOrder = 'a.created';
		}
		$this->setState($this->_context.'.list.ordering', $listOrder);              
                
		$listDirn = JRequest::getCmd('filter_order_Dir', 'DESC');
		if (!in_array(strtoupper($listDirn), array('ASC', 'DESC', '')))
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

		// Load the parameters.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                $this->setState('params', $config);

		// Load the filter state.
		$this->setState('reply.id', JRequest::getInt('reply_id'));
		$this->setState('element.type', JRequest::getInt('element_type'));
		$this->setState('element.id', JRequest::getInt('element_id'));
		$this->setState('user.id', JRequest::getInt('user_id'));                
                
		// List state information.
		parent::populateState($listOrder, $listDirn);
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
        
	/**
	 * Method to get human readable activity type
         * 
         * @since   0.1
	 **/
        function getActivityType($item)
        {
                switch ($item->activity_type) {
                    case 1:
                        $return = ($item->reply_id > 0 ? JText::_('COM_HWDMS_X_REPLIED') : JText::_('COM_HWDMS_X_WROTE'));
                        return $return;
                        break;
                    case 2:
                        return JText::_('COM_HWDMS_X_UPLOADED_A_NEW_MEDIA');
                        break;    
                    case 3:
                        return JText::_('COM_HWDMS_X_CREATED_A_NEW_ALBUM');
                        break;   
                    case 4:
                        return JText::_('COM_HWDMS_X_CREATED_A_NEW_GROUP');
                        break; 
                    case 5:
                        return JText::_('COM_HWDMS_X_CREATED_A_NEW_PLAYLIST');
                        break;
                    case 6:
                        return JText::_('COM_HWDMS_X_CREATED_A_NEW_CHANNEL');
                        break; 
                    case 7:
                        return JText::_('COM_HWDMS_X_JOINED_A_GROUP');
                        break; 
                    case 8:
                        return JText::_('COM_HWDMS_X_LEFT_A_GROUP');
                        break;  
                    case 9:
                        return JText::_('COM_HWDMS_X_SHARED_MEDIA_WITH_A_GROUP');
                        break;                    
                }
        }        
}
