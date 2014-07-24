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

class hwdMediaShareModelAlbum extends JModelList
{
	/**
	 * Model context string.
         * 
         * @access      public
	 * @var         string
	 */   
	public $context = 'com_hwdmediashare.album';

	/**
	 * The album data.
         * 
         * @access      protected
	 * @var         object
	 */  
	protected $_album;
        
	/**
	 * The media model used for obtaining album items.
         * 
         * @access      protected
	 * @var         object
	 */        
	protected $_model;
        
	/**
	 * The number of media in the album.
         * 
         * @access      protected
	 * @var         integer
	 */
        protected $_numMedia = 0;
        
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
	public function getTable($name = 'Album', $prefix = 'hwdMediaShareTable', $config = array())
	{
		return JTable::getInstance($name, $prefix, $config);
	}
        
	/**
	 * Method to get a single album.
	 *
         * @access  public
	 * @param   integer     $pk     The id of the primary key.
	 * @return  mixed       Object on success, false on failure.
	 */
	public function getAlbum($pk = null)
	{
		// Initialise variables.
                $app = JFactory::getApplication();
                
                // Get the filter.album_id value.
		$pk = (int) (!empty($pk)) ? $pk : $this->getState('filter.album_id');

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
                        if ($option == 'com_hwdmediashare' && $view == 'album') 
                        {
                                $app->enqueueMessage( JText::_( 'COM_HWDMS_ERROR_ITEM_NOAUTHORISED' ) ); 
                                $app->getApplication()->redirect( $config->get('no_access_redirect') > 0 ? ContentHelperRoute::getArticleRoute($config->get('no_access_redirect')) : 'index.php' );
                        }
                        
                        $this->setError(JText::_('COM_HWDMS_ERROR_ITEM_NOAUTHORISED'));
                        return false;
                }
                
		$properties = $table->getProperties(1);
		$this->_album = JArrayHelper::toObject($properties, 'JObject');
              
		// Convert params field to registry.
		if (property_exists($this->_album, 'params'))
		{
			$registry = new JRegistry;
			$registry->loadString($this->_album->params);
			$this->_album->params = $registry;

                        // Check if this album has a custom ordering.
                        if ($ordering = $this->_album->params->get('list_order_media')) 
                        {
                                // Force this new ordering
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
                        $this->_album->tags = new JHelperTags;
                        $this->_album->tags->getItemTags('com_hwdmediashare.album', $this->_album->id);
                        
                        // Add the custom fields.
                        hwdMediaShareFactory::load('customfields');
                        $HWDcustomfields = hwdMediaShareCustomFields::getInstance();
                        $HWDcustomfields->elementType = 2;
                        $this->_album->customfields = $HWDcustomfields->load($this->_album);

                        // Add the number of media in the album.
                        $this->_album->nummedia = $this->_numMedia;

                        // Add the author.
                        if ($this->_album->created_user_id > 0)
                        {   
                                $user = JFactory::getUser($this->_album->created_user_id);
                                $this->_album->author = (!empty($this->_album->created_user_id_alias) ? $this->_album->created_user_id_alias : ($config->get('author') == 0 ? $user->name : $user->username));
                        }
                        else
                        {
                                $this->_album->author = JText::_('COM_HWDMS_GUEST');
                        }
		}

		return $this->_album;
	}

	/**
	 * Method to get a list of items.
	 *
	 * @access  public
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getItems()
	{
                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                $this->_model = JModelLegacy::getInstance('Media', 'hwdMediaShareModel', array('ignore_request' => true));
                $this->_model->context = 'com_hwdmediashare.album';
                $this->_model->populateState();
                $this->_model->setState('list.ordering', $this->getState('list.ordering'));
                $this->_model->setState('list.direction', $this->getState('list.direction'));
                $this->_model->setState('filter.album_id', $this->getState('filter.album_id'));

                if ($this->_items = $this->_model->getItems())
                {
                        $this->_numMedia = $this->_model->getTotal();
                }

                return $this->_items; 
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
	 * Method to number of media in the album.
	 *
         * @access  public
	 * @return  integer The number of media.
	 */
	public function getNumMedia()
	{
                return (int) $this->_numMedia; 
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
		$this->setState('filter.album_id', $id);

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
                
                // Only set these states when in the com_hwdmediashare.album context.
                if ($this->context == 'com_hwdmediashare.album')
                {             
                        // Load the display state.
                        $display = $this->getUserStateFromRequest('media.display', 'display', $config->get('list_default_display', 'details' ), 'word', false);
                        if (!in_array(strtolower($display), array('details', 'gallery', 'list'))) $display = 'details';
                        $this->setState('media.display', $display);

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
			$pk = (!empty($pk)) ? $pk : (int) $this->getState('filter.album_id');

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
                
                $pk = (!empty($pk)) ? $pk : (int) $this->getState('filter.album_id');

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
			if (!$user->authorise('core.edit.state', 'com_hwdmediashare.album.'. (int) $id))
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
                                    ->update($db->quoteName('#__hwdms_albums'))
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
	 * Method to report an album.
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
                $object->element_type = 2;
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
}
