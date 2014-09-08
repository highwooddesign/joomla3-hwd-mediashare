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

class hwdMediaShareModelGroup extends JModelList
{
	/**
	 * Model context string.
         * 
         * @access      public
	 * @var         string
	 */ 
	public $context = 'com_hwdmediashare.group';

	/**
	 * The group data.
         * 
         * @access      protected
	 * @var         object
	 */  
	protected $_group;
        
	/**
	 * The group items.
         * 
         * @access      protected
	 * @var         object
	 */           
	protected $_items;
        
	/**
	 * The group media.
         * 
         * @access      protected
	 * @var         object
	 */   
	protected $_media;
        
	/**
	 * The group members.
         * 
         * @access      protected
	 * @var         object
	 */ 
	protected $_members;
        
	/**
	 * The group activity.
         * 
         * @access      protected
	 * @var         object
	 */ 
	protected $_activities;
        
	/**
	 * The model used for obtaining group items.
         * 
         * @access      protected
	 * @var         object
	 */        
	protected $_model;
        
	/**
	 * The number of media in the group.
         * 
         * @access      protected
	 * @var         integer
	 */
        protected $_numMedia = 0;
        
	/**
	 * The number of members in the group.
         * 
         * @access      protected
	 * @var         integer
	 */
        protected $_numMembers = 0;

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
	 * Method to get a table object, and load it if necessary.
	 *
	 * @access  public
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 * @return  JTable  A JTable object
	 */
	public function getTable($name = 'Group', $prefix = 'hwdMediaShareTable', $config = array())
	{
		return JTable::getInstance($name, $prefix, $config);
	}

	/**
	 * Method to get a single group.
	 *
         * @access  public
	 * @param   integer     $pk     The id of the primary key.
	 * @return  mixed       Object on success, false on failure.
	 */
	public function getGroup($pk = null)
	{
		// Initialise variables.
                $app = JFactory::getApplication();
                
                // Get the filter.group_id value.
		$pk = (int) (!empty($pk)) ? $pk : $this->getState('filter.group_id');

                // Get HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
		// Get a table instance.
		$table = $this->getTable();

		// Attempt to load the table row.
		$return = $table->load($pk);

		// Check for a table object error.
		if ($return === false && $table->getError())
                {
			$this->setError($table->getError());
			return false;
		}
       
                // Check published state and access permissions.
                if ($published = $this->getState('filter.published'))
                {
                        if (is_array($published) && !in_array($table->published, $published)) 
                        {
                                $this->setError(JText::_('COM_HWDMS_ERROR_ITEM_UNPUBLISHED'));
                                return false;
                        }
                        elseif (is_int($published) && $table->published != $published) 
                        {
                                $this->setError(JText::_('COM_HWDMS_ERROR_ITEM_UNPUBLISHED'));
                                return false;
                        }
                }
                                
                // Check approval status and access permissions, but allow users to see own unapproved items.
                $user = JFactory::getUser();
                if ($status = $this->getState('filter.status'))
                {
                        if (is_array($status) && !in_array($table->status, $status) && $table->created_user_id != $user->id) 
                        {
                                $this->setError(JText::_('COM_HWDMS_ERROR_ITEM_UNAPPROVED'));
                                return false;
                        }
                        elseif (is_int($status) && $table->status != $status && $table->created_user_id != $user->id) 
                        {
                                $this->setError(JText::_('COM_HWDMS_ERROR_ITEM_UNAPPROVED'));
                                return false;
                        }
                }

                // Check group access level and access permissions.
                $groups = $user->getAuthorisedViewLevels();
                if (!in_array($table->access, $groups)) 
                {                                    
                        $option = $app->input->get('option');
                        $view = $app->input->get('view');
                        if ($option == 'com_hwdmediashare' && $view == 'group') 
                        {
                                JFactory::getApplication()->enqueueMessage( JText::_( 'COM_HWDMS_ERROR_ITEM_NOAUTHORISED' ) ); 
                                JFactory::getApplication()->redirect( $config->get('no_access_redirect') > 0 ? ContentHelperRoute::getArticleRoute($config->get('no_access_redirect')) : 'index.php' );
                        }
                        
                        $this->setError(JText::_('COM_HWDMS_ERROR_ITEM_NOAUTHORISED'));
                        return false;
                }
                
		$properties = $table->getProperties(1);
		$this->_group = JArrayHelper::toObject($properties, 'JObject');

		// Convert params field to registry.
		if (property_exists($this->_group, 'params'))
		{
			$registry = new JRegistry;
			$registry->loadString($this->_group->params);
			$this->_group->params = $registry;

                        // Check if this group has a custom ordering.
                        if ($ordering = $this->_group->params->get('list_order_media')) 
                        {
                                // Force this new ordering.
                                $orderingParts = explode(' ', $ordering); 
                                $app = JFactory::getApplication();
                                $list = $app->getUserStateFromRequest($this->context . '.list', 'list', array(), 'array');
                                $list['fullordering'] = $ordering;
                                $app->setUserState($this->context . '.list', $list);
                                $this->setState('list.ordering', $orderingParts[0]);
                                $this->setState('list.direction', $orderingParts[1]);     
                        } 
		}

		if ($pk)
		{
                        // Add the tags.
                        $this->_group->tags = new JHelperTags;
                        $this->_group->tags->getItemTags('com_hwdmediashare.group', $this->_group->id);
                        
                        // Add the custom fields.
                        hwdMediaShareFactory::load('customfields');
                        $HWDcustomfields = hwdMediaShareCustomFields::getInstance();
                        $HWDcustomfields->elementType = 3;
                        $this->_group->customfields = $HWDcustomfields->load($this->_group);

                        // Add the number of media and members in the group.
                        $this->_group->nummedia = $this->_numMedia;
                        $this->_group->nummembers = $this->_numMembers;

                        // Add the author.
                        if ($this->_group->created_user_id > 0)
                        {   
                                $user = JFactory::getUser($this->_group->created_user_id);
                                $this->_group->author = (!empty($this->_group->created_user_id_alias) ? $this->_group->created_user_id_alias : ($config->get('author') == 0 ? $user->name : $user->username));
                        }
                        else
                        {
                                $this->_group->author = JText::_('COM_HWDMS_GUEST');
                        }  
                        
                        // Add map.
                        hwdMediaShareFactory::load('googlemaps.GoogleMap');
                        hwdMediaShareFactory::load('googlemaps.JSMin');
                        hwdMediaShareFactory::load('googlemaps.map');
                        $map = new hwdMediaShareMap();
                        $map->addKMLOverlay(JURI::root().'index.php?option=com_hwdmediashare&view=media&format=feed&type=rssgeo&filter_group_id='.$this->_group->id);
                        $map->getJavascriptHeader();
                        $map->getJavascriptMap();
                        $map->setWidth('100%');
                        $map->setHeight('100%');
                        $map->setMapType('map');
                        $this->_group->map = $map->getOnLoad().$map->getMap().$map->getSidebar();
                        $this->_group->map = $map->getOnLoad().$map->getMap();

                        // Add member status.
                        $this->_group->isAdmin = ($this->_group->created_user_id == $user->id);   
                        $this->_group->isMember = $this->isMember($this->_group);
                }

		return $this->_group;
	}

	/**
	 * Method to get a list of media associated with the group.
	 *
	 * @access  public
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getMedia()
	{
                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                $this->_model = JModelLegacy::getInstance('Media', 'hwdMediaShareModel', array('ignore_request' => true));
                $this->_model->context = 'com_hwdmediashare.group';
                $this->_model->populateState();
                $this->_model->setState('list.ordering', $this->getState('list.ordering'));
                $this->_model->setState('list.direction', $this->getState('list.direction'));                
                $this->_model->setState('filter.group_id', $this->getState('filter.group_id'));

                if ($this->_items = $this->_model->getItems())
                {
                        $this->_numMedia = $this->_model->getTotal();
                }

                return $this->_items; 
	}

	/**
	 * Method to get a list of members associated with the group.
	 *
	 * @access  public
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getMembers()
	{
                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                $this->_model = JModelLegacy::getInstance('Users', 'hwdMediaShareModel', array('ignore_request' => true));
                $this->_model->populateState();
                $this->_model->setState('filter.group_id', $this->getState('filter.group_id'));

                if ($this->_members = $this->_model->getItems())
                {
                        $this->_numMembers = $this->_model->getTotal();
                }

                return $this->_members; 
	}

	/**
	 * Method to get a list of activities associated with this group.
	 *
	 * @access  public
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getActivities()
	{
                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                $this->_model = JModelLegacy::getInstance('Activities', 'hwdMediaShareModel', array('ignore_request' => true));
                $this->_model->populateState();
                $this->_model->setState('group.id', $this->getState('filter.group_id'));
		$this->_model->setState('filter.verb', array(4,7,8,9,14,15));
		$this->_model->setState('filter.action', (int) $this->getState('filter.group_id'));                 
		$this->_model->setState('filter.target', (int) $this->getState('filter.group_id'));                 
		$this->_model->setState('list.ordering', 'a.created');
		$this->_model->setState('list.direction', 'desc');
                
                $this->_activities = $this->_model->getItems();

                return $this->_activities; 
	}
        
	/**
	 * Method to get a JPagination object for the data set.
	 *
         * @access  public
	 * @return  JPagination  A JPagination object for the data set.
	 */
	public function getPagination()
	{
                return $this->_model->getPagination(); 
	}

	/**
	 * Method to get number of media in the group.
	 *
         * @access  public
	 * @return  integer The number of media.
	 */
	public function getNumMedia()
	{
                return (int) $this->_numMedia; 
	}

	/**
	 * Method to get number of members in the group.
	 *
         * @access  public
	 * @return  integer The number of media.
	 */
	public function getNumMembers()
	{
                return (int) $this->_numMembers; 
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
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication();
                $user = JFactory::getUser();
                
		// Load the parameters.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                $this->setState('params', $config);

		// Load state from the request.
		$id = $app->input->getInt('id');
		$this->setState('filter.group_id', $id);

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

                // Only set these states when in the com_hwdmediashare.group context.
                if ($this->context == 'com_hwdmediashare.group')
                {
                        // Load the display state.
                        $this->setState('media.display', 'details');

                        // Check for list inputs and set default values if none exist
                        // This is required as the fullordering input will not take default value unless set
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
                }

                // List state information.
                parent::populateState($ordering, $direction);
	}
        
	/**
	 * Increment the hit counter for the record.
	 *
         * @access  public
	 * @param   integer  $pk  Optional primary key of the record to increment.
	 * @return  boolean  True if successful; false otherwise and internal error set.
	 */
	public function hit($pk = 0)
	{
		$input = JFactory::getApplication()->input;
		$hitcount = $input->getInt('hitcount', 1);

		if ($hitcount)
		{
			$pk = (!empty($pk)) ? $pk : (int) $this->getState('filter.group_id');

			$table = $this->getTable();
			$table->load($pk);
			$table->hit($pk);
		}

		return true;
	}

	/**
	 * Increment the like counter for the record.
	 *
         * @access  public
	 * @param   integer  $pk     Optional primary key of the record to increment.
	 * @param   integer  $value  The value of the property to increment.
	 * @return  boolean  True if successful; false otherwise and internal error set.
	 */
	public function like($pk = 0, $value = 1)
	{            
                $user = JFactory::getUser();
                if (!$user->authorise('hwdmediashare.like', 'com_hwdmediashare'))
                {
			$this->setError(JText::_('COM_HWDMS_ERROR_NOAUTHORISED'));
			return false;
                }
                
                $pk = (!empty($pk)) ? $pk : (int) $this->getState('filter.group_id');

                $table = $this->getTable();
                $table->load($pk);
                $table->like($pk, $value);

                return true;
	}

	/**
	 * Method to change the published state of one or more records.
	 *
         * @access  public
	 * @param   array    $pks    A list of the primary keys to change.
	 * @param   integer  $value  The value of the published state.
	 * @return  boolean  True on success.
	 */
	public function publish($pks, $value = 0)
	{
		// Initialiase variables.
                $user = JFactory::getUser();
                
		// Sanitize the ids.
		$pks = (array) $pks;
		JArrayHelper::toInteger($pks);

		// Access checks.
		foreach ($pks as $i => $id)
		{
			if (!$user->authorise('core.edit.state', 'com_hwdmediashare.group.'. (int) $id))
			{
				// Prune items that the user can't change.
				unset($pks[$i]);
				JError::raiseNotice(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
			}
		}
                
		if (empty($pks))
		{
			$this->setError(JText::_('COM_HWDMS_NO_ITEM_SELECTED'));
			return false;
		}

		try
		{
			$db = $this->getDbo();
			$query = $db->getQuery(true)
                                    ->update($db->quoteName('#__hwdms_groups'))
                                    ->set('published = ' . $db->quote((int) $value))
                                    ->where('id IN (' . implode(',', $pks) . ')');
                        $db->setQuery($query);
			$db->execute();
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());
			return false;
		}

		// Clear the component's cache.
		$this->cleanCache();

		return true;
	}

	/**
	 * Method to report a group.
         * 
         * @access  public
	 * @return  boolean True on success, false on failure.
	 */
	public function report()
	{
		// Initialiase variables.
		$user = JFactory::getUser();
                $date = JFactory::getDate();                
		$input = JFactory::getApplication()->input;

                // Load HWD utilities.
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                
                // Load HWD report table.
		$table = $this->getTable('Report', 'hwdMediaShareTable');    

                if (!$user->authorise('hwdmediashare.report', 'com_hwdmediashare'))
                {
                        $this->setError(JText::_('COM_HWDMS_ERROR_NOAUTHORISED'));
                        return false;                    
                }
                                        
                // Create an object to bind to the database.
                $object = new StdClass;
                $object->element_type = 3;
                $object->element_id = $input->get('id', 0, 'int');
                $object->user_id = $user->id;
                $object->report_id = $input->get('report_id', 0, 'int');
                $object->description = $input->get('description', '', 'string');
                $object->created = $date->toSql();
                
                // Attempt to save the report details to the database.
                if (!$table->save($object))
                {
                        $this->setError($table->getError());
                        return false;
                }

		return true;
	} 
        
	/**
	 * Method to add a member to a group.
         * 
         * @access  public
	 * @param   array    $pks    A list of the primary keys.
	 * @return  boolean  True on success.
	 */
	public function join($pks)
	{
		// Initialiase variables.
                $user = JFactory::getUser();
                $db = JFactory::getDBO();
                $date = JFactory::getDate();
                
		// Sanitize the ids.
		$pks = (array) $pks;
		JArrayHelper::toInteger($pks);
                
                if (empty($user->id))
                {
			$this->setError(JText::_('COM_HWDMS_ERROR_NOAUTHORISED'));
			return false;
                }
                
		foreach ($pks as $i => $pk)
		{
                        if (empty($pk))
                        {
                                $this->setError(JText::_('COM_HWDMS_NO_ITEM_SELECTED'));
                                return false;
                        }

                        $query = $db->getQuery(true)
                                ->select('COUNT(*)')
                                ->from('#__hwdms_group_members')
                                ->where('group_id = ' . $db->quote($pk))
                                ->where('member_id = ' . $db->quote($user->id))
                                ->group('member_id');                        
                        try
                        {                
                                $db->setQuery($query);
                                $member = $db->loadResult();
                        }
                        catch (Exception $e)
                        {
                                $this->setError($e->getMessage());
                                return false;
                        }

                        if(!$member)
                        {
                                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                                $table = JTable::getInstance('GroupMembers', 'hwdMediaShareTable');    

                                // Create an object to bind to the database.
                                $object = new StdClass;
                                $object->group_id = $pk;
                                $object->member_id = $user->id;
                                $object->approved = 1;
                                $object->created = $date->toSql();

                                // Attempt to change the state of the records.
                                if (!$table->save($object))
                                {
                                        $this->setError($table->getError());
                                        return false;
                                }
                        }
                        
                        // Get a table instance.
                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                        $table = JTable::getInstance('Group', 'hwdMediaShareTable');

                        // Attempt to load the table row.
                        $return = $table->load($pk);

                        // Check for a table object error.
                        if ($return === false && $table->getError())
                        {
                                $this->setError($table->getError());
                                return false;
                        }
                
                        $properties = $table->getProperties(1);
                        $item = JArrayHelper::toObject($properties, 'JObject');
                        
                        // Load HWD library.
                        hwdMediaShareFactory::load('events');
                        $HWDevents = hwdMediaShareEvents::getInstance();
                        
                        // Trigger onAfterJoinGroup event.                        
                        $HWDevents->triggerEvent('onAfterJoinGroup', $item);
		}

		return true;
	}

	/**
	 * Method to remove a member to a group.
         * 
         * @access  public
	 * @param   array    $pks    A list of the primary keys.
	 * @return  boolean  True on success.
	 */
	public function leave($pks)
	{
		// Initialiase variables.
                $user = JFactory::getUser();
                $db = JFactory::getDBO();

		// Sanitize the ids.
		$pks = (array) $pks;
		JArrayHelper::toInteger($pks);

                if (empty($user->id))
                {
			$this->setError(JText::_('COM_HWDMS_ERROR_NOAUTHORISED'));
			return false;
                }
                
		foreach ($pks as $i => $pk)
		{
                        if (empty($pk))
                        {
                                $this->setError(JText::_('COM_HWDMS_NO_ITEM_SELECTED'));
                                return false;
                        }

                        try
                        {
                                $query = $db->getQuery(true);

                                $conditions = array(
                                    $db->quoteName('group_id') . ' = ' . $db->quote($pk), 
                                    $db->quoteName('member_id') . ' = ' . $db->quote($user->id)
                                );

                                $query->delete($db->quoteName('#__hwdms_group_members'));
                                $query->where($conditions);

                                $db->setQuery($query);

                                $result = $db->execute();
                        }
                        catch (Exception $e)
                        {
                                $this->setError($e->getMessage());
                                return false;
                        }
                        
                        // Get a table instance.
                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                        $table = JTable::getInstance('Group', 'hwdMediaShareTable');

                        // Attempt to load the table row.
                        $return = $table->load($pk);

                        // Check for a table object error.
                        if ($return === false && $table->getError())
                        {
                                $this->setError($table->getError());
                                return false;
                        }
                
                        $properties = $table->getProperties(1);
                        $item = JArrayHelper::toObject($properties, 'JObject');
                        
                        // Load HWD library.
                        hwdMediaShareFactory::load('events');
                        $HWDevents = hwdMediaShareEvents::getInstance();
                        
                        // Trigger onAfterLeaveGroup event.                        
                        $HWDevents->triggerEvent('onAfterLeaveGroup', $item);
		}

		return true; 
	}

	/**
	 * Method to check if a user is a member of a group.
         * 
         * @access  public
	 * @param   array    $group     The group primary key to check.
	 * @return  boolean  True on success.
	 */
	public function isMember($group)
	{            
                // Initialise variables.
                $db = JFactory::getDBO();
                $user = JFactory::getUser();

                $query = $db->getQuery(true)
                        ->select('COUNT(*)')
                        ->from('#__hwdms_group_members')
                        ->where('group_id = ' . $db->quote($group->id))
                        ->where('member_id = ' . $db->quote($user->id))
                        ->group('member_id');                
                try
                {                
                        $db->setQuery($query);
                        $member = $db->loadResult();
                }
                catch (Exception $e)
                {
                        $this->setError($e->getMessage());
                        return false;
                }

                if ($member)
                {
                        return true;
                }
                else
                {
                        return false;
                }
	}
}

