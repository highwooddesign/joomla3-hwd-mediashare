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

class hwdMediaShareModelMediaItem extends JModelItem
{
    	/**
	 * Model context string.
	 * @var string
	 */
	public $context = 'com_hwdmediashare.media';

	/**
	 * Model data
	 * @var array
	 */
	protected $_media = null;
	protected $_model = null;
        protected $_likes = null;
        protected $_dislikes = null;
        
	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 *
	 * @return  JTable  A JTable object
	 */
	public function getTable($name = 'Media', $prefix = 'hwdMediaShareTable', $config = array())
	{
		return JTable::getInstance($name, $prefix, $config);
	}
        
	/**
	 * Method to get a single media item.
	 *
	 * @param   integer	The id of the primary key.
         * 
	 * @return  mixed  Object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		// Initialise variables.
                $app = JFactory::getApplication();
                $option = $app->input->get('option');
                $view = $app->input->get('view');
                $task = $app->input->get('task'); 
                
                // Get the media.id value.
                $pk = (int) (!empty($pk)) ? $pk : $this->getState('media.id');

                // Get HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
		// Get a row instance.
		$table = $this->getTable();

		// Attempt to load the row.
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
                        if ($option == 'com_hwdmediashare' && $view == 'mediaitem' && $task != 'embed') 
                        {
                                JFactory::getApplication()->enqueueMessage( JText::_( 'COM_HWDMS_ERROR_ITEM_NOAUTHORISED' ) ); 
                                JFactory::getApplication()->redirect( $config->get('no_access_redirect') > 0 ? ContentHelperRoute::getArticleRoute($config->get('no_access_redirect')) : 'index.php' );
                        }
                        
                        $this->setError(JText::_('COM_HWDMS_ERROR_ITEM_NOAUTHORISED'));
                        return false;
                }
                
		$properties = $table->getProperties(1);
		$this->_media = JArrayHelper::toObject($properties, 'JObject');

		// Convert params field to registry.
		if (property_exists($this->_media, 'params'))
		{
			$registry = new JRegistry;
			$registry->loadString($this->_media->params);
			$this->_media->params = $registry;    
		}

		// Check if only author can view.
                if ($this->_media->params->get('author_only') == 1 && $user != $this->_media->created_user_id)
                {
                        $this->setError(JText::_('COM_HWDMS_ERROR_ONLY_AUTHOR_ACCESS'));
                        return false;
                }

		// Check for age restriction.
                if ($this->_media->params->get('age_restriction') == 1)
                {        
                        $dob = $app->getUserState('media.dob');
                        if (!$dob)
                        {                           
                                if ($option == 'com_hwdmediashare' && $view == 'mediaitem' && $task != 'embed') 
                                {                                   
                                        $this->_media->agerestricted = true;
                                        return $this->_media;
                                }
                                else
                                {
                                        $this->setError(JText::_('COM_HWDMS_ERROR_AGE_RESTRICTED'));
                                }                            
                        }
                        else
                        {
                                $born = strtotime($dob);
                                $required = strtotime("- ".$this->_media->params->get('age')." year", time());
                                if ($born > $required)
                                {
                                        if ($option == 'com_hwdmediashare' && $view == 'mediaitem' && $task != 'embed') 
                                        {          
                                                $app->enqueueMessage(JText::_('COM_HWDMS_NOTICE_TOO_YOUNG_TO_VIEW'));
                                                $this->_media->agerestricted = true;
                                                return $this->_media;
                                        }
                                        else
                                        {
                                                $this->setError(JText::_('COM_HWDMS_NOTICE_TOO_YOUNG_TO_VIEW'));
                                        } 
                                }                            
                        }
                }
                
		// Check for password protection.
                if ($this->_media->params->get('password_protect') == 1)
                {
                        $pw = $app->getUserState('media.media-password-'.$this->_media->id );
                        if ($pw != $this->_media->params->get('password'))
                        {
                                if ($option == 'com_hwdmediashare' && $view == 'mediaitem' && $task != 'embed') 
                                {          
                                        if ($pw) $app->enqueueMessage( JText::_('COM_HWDMS_ERROR_PASSWORD_INCORRECT') );
                                        $this->_media->passwordprotected = true;
                                        return $this->_media;
                                }
                                else
                                {
                                        $this->setError(JText::_('COM_HWDMS_ERROR_PASSWORD_PROTECTED'));
                                }                             
                        }
                }
                
		if ($pk)
		{
                        // Add the tags.
                        $this->_media->tags = new JHelperTags;
                        $this->_media->tags->getItemTags('com_hwdmediashare.media', $this->_media->id);

                        // Add the custom fields.
                        hwdMediaShareFactory::load('customfields');
                        $cf = hwdMediaShareCustomFields::getInstance();
                        $cf->elementType = 1;
                        $this->_media->customfields = $cf->get($this->_media);
                        
                        // Add the categories.
                        hwdMediaShareFactory::load('category');
                        $cat = hwdMediaShareCategory::getInstance();
                        $cat->elementType = 1;
                        $this->_media->categories = $cat->get($this->_media);

                        // Add the media type.
                        hwdMediaShareFactory::load('media');
                        $this->_media->media_type = hwdMediaShareMedia::loadMediaType($this->_media);
                        
                        // Add the author.
                        if ($this->_media->created_user_id > 0)
                        {   
                                $user = JFactory::getUser($this->_media->created_user_id);
                                $this->_media->author = (!empty($this->_media->created_user_id_alias) ? $this->_media->created_user_id_alias : ($config->get('author') == 0 ? $user->name : $user->username));
                        }
                        else
                        {
                                $this->_media->author = JText::_('COM_HWDMS_GUEST');
                        }

                        // Add subscription status.
                        hwdMediaShareFactory::load('subscriptions');
                        $HWDsubscriptions = hwdMediaShareSubscriptions::getInstance();
                        $HWDsubscriptions->elementType = 5;
                        $this->_media->isSubscribed = $HWDsubscriptions->isSubscribed($this->_media->created_user_id);

                        // Add favourite status.
                        hwdMediaShareFactory::load('favourites');
                        $HWDfavourites = hwdMediaShareFavourites::getInstance();
                        $HWDfavourites->elementType = 1;
                        $this->_media->isFavourite = $HWDfavourites->isFavourite($this->_media->id);

                        // Add map assets.
                        hwdMediaShareFactory::load('googlemaps.GoogleMap');
                        hwdMediaShareFactory::load('googlemaps.JSMin');
                        hwdMediaShareFactory::load('googlemaps.map');
                        $map = new hwdMediaShareMap();
                        $map->addMarkerByAddress($this->_media->location, $this->_media->title, $this->_media->description);
                        $map->getJavascriptHeader();
                        $map->getJavascriptMap();
                        $map->setWidth('100%');
                        $this->_media->map = $map->getOnLoad().$map->getMap().$map->getSidebar();
                        $this->_media->map = $map->getOnLoad().$map->getMap();
                        
                        // Add page navigation.
                        $this->_media->navigation = hwdMediaShareHelperNavigation::pageNavigation($this->_media, $this->_media->params);
                
                        //$this->_media->linkedalbums = $this->getLinkedAlbums(); 
                        //$this->_media->linkedgroups = $this->getLinkedGroups();                          
                        //$this->_media->linkedplaylists = $this->getLinkedPlaylists();                          
                        //$this->_media->linkedmedia = $this->getLinkedMedia();                          
                        //$this->_media->linkedpages = $this->getLinkedPages();     
		}

		return $this->_media;
	}
        
	/**
	 * Method to get a list of activities associated with this media item.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getActivities()
	{
                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                $this->_model = JModelLegacy::getInstance('Activities', 'hwdMediaShareModel', array('ignore_request' => true));
                $this->_model->populateState();
                $this->_model->setState('media.id', $this->getState('filter.media.id'));
		$this->_model->setState('filter.verb', array(2,9,10,11));
		$this->_model->setState('filter.action', (int) $this->getState('media.id'));                 
		$this->_model->setState('filter.target', (int) $this->getState('media.id'));                 
		$this->_model->setState('list.ordering', 'a.created');
		$this->_model->setState('list.direction', 'desc');
                
                $this->_activities = $this->_model->getItems();

                return $this->_activities; 
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

		// Load state from the request.
		$id = $app->input->getInt('id');
		$this->setState('media.id', $id);

		$return = $app->input->get('return', null, 'base64');
		$this->setState('return_page', base64_decode($return));

		$this->setState('layout', $app->input->getString('layout'));                

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
                
		parent::populateState();  
	}
        
	/**
	 * Increment the hit counter for the record.
	 *
	 * @param   integer  $pk  Optional primary key of the record to increment.
	 *
	 * @return  boolean  True if successful; false otherwise and internal error set.
	 */
	public function hit($pk = 0)
	{
		$input = JFactory::getApplication()->input;
		$hitcount = $input->getInt('hitcount', 1);

		if ($hitcount)
		{
			$pk = (!empty($pk)) ? $pk : (int) $this->getState('media.id');

			$table = $this->getTable();
			$table->load($pk);
			$table->hit($pk);
			$table->view($pk);                        
		}

		return true;
	}

	/**
	 * Increment the like counter for the record.
	 *
	 * @param   integer  $pk     Optional primary key of the record to increment.
	 * @param   integer  $value  The value of the property to increment.
         * 
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
                
                $pk = (!empty($pk)) ? $pk : (int) $this->getState('media.id');

                $table = $this->getTable();
                $table->load($pk);
                $table->like($pk, $value);

                return true;
	}

	/**
	 * Method to change the published state of one or more records.
	 *
	 * @param   array    $pks    A list of the primary keys to change.
	 * @param   integer  $value  The value of the published state.
	 *
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
			if (!$user->authorise('core.edit.state', 'com_hwdmediashare.media.'. (int) $id))
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
                                    ->update($db->quoteName('#__hwdms_media'))
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

		// Clear the component's cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Method to report an object
	 * @return  void
	 */
	public function report()
	{
		// Initialiase variables.
		$user = JFactory::getUser();
                $date = JFactory::getDate();                
		$input = JFactory::getApplication()->input;

                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                
		$table = $this->getTable('Report', 'hwdMediaShareTable');    

                if (!$user->authorise('hwdmediashare.report', 'com_hwdmediashare'))
                {
                        $this->setError(JText::_('COM_HWDMS_ERROR_NOAUTHORISED'));
                        return false;                    
                }
                                        
                // Create an object to bind to the database.
                $object = new StdClass;
                $object->element_type = 1;
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
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getLinkedAlbums($pk = null)
	{
                // Create a new query object.
                $db = JFactory::getDBO();

                JLoader::register('hwdMediaShareModelAlbums', JPATH_ROOT.'/components/com_hwdmediashare/models/albums.php');
                $query = hwdMediaShareModelAlbums::getListQuery();
                // Limit this query
                //$query.= ' LIMIT 0, '.$this->getState('list.limit');                
                $query.= ' LIMIT 0, 5';                
 
                $db->setQuery($query);
                $rows = $db->loadObjectList();

                if ($db->getErrorMsg())
                {
                        $this->setError($db->getErrorMsg());
                        return false;
                }
                else
                {
			return $rows;
                }
	}
        /**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getLinkedGroups($pk = null)
	{
                // Create a new query object.
                $db = JFactory::getDBO();

                JLoader::register('hwdMediaShareModelGroups', JPATH_ROOT.'/components/com_hwdmediashare/models/groups.php');
                $query = hwdMediaShareModelGroups::getListQuery();
                // Limit this query
                //$query.= ' LIMIT 0, '.$this->getState('list.limit');                
                $query.= ' LIMIT 0, 5';  
                
                $db->setQuery($query);
                $rows = $db->loadObjectList();

                if ($db->getErrorMsg())
                {
                        $this->setError($db->getErrorMsg());
                        return false;
                }
                else
                {
			return $rows;
                }
	}
        /**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getLinkedPlaylists($pk = null)
	{
                // Create a new query object.
                $db = JFactory::getDBO();

                JLoader::register('hwdMediaShareModelPlaylists', JPATH_ROOT.'/components/com_hwdmediashare/models/playlists.php');
                $query = hwdMediaShareModelPlaylists::getListQuery();
                // Limit this query
                //$query.= ' LIMIT 0, '.$this->getState('list.limit');                
                $query.= ' LIMIT 0, 5';  
                
                $db->setQuery($query);
                $rows = $db->loadObjectList();

                if ($db->getErrorMsg())
                {
                        $this->setError($db->getErrorMsg());
                        return false;
                }
                else
                {
			return $rows;
                }
	}
        
        /**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getLinkedMedia($pk = null)
	{
	}
        
        
        /**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getLinkedPages($pk = null)
	{
	}
        
        /**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function password($pk = null)
	{
                $data = JRequest::getVar('jform', array(), 'post', 'array');

                $app = JFactory::getApplication();
                
                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                $table =& JTable::getInstance('Media', 'hwdMediaShareTable');
                $table->load($app->input->get('id', '', 'integer'));
                $properties = $table->getProperties(1);
                $item = JArrayHelper::toObject($properties, 'JObject');

                if (empty($data['password']))
                {
                        $this->setError(JText::_('COM_HWDMS_ERROR_NO_PASSWORD_PROVIDED'));
                        return false; 
                }

                $pw = md5($item->key . $data['password']);               

                JFactory::getApplication()->setUserState( "media.media-password-$item->id", $pw );
                
                return true; 
	}
        
        /**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function dob($pk = null)
	{               
                $data = JRequest::getVar('jform', array(), 'post', 'array');

                if (empty($data['dob']))
                {
                        $this->setError(JText::_('COM_HWDMS_NO_DOB_PROVIDED'));
                        return false; 
                }
                                
                JFactory::getApplication()->setUserState( "media.dob", $data['dob'] );
                
                return true;
	}
        
        /**
	 * Increment the hit counter for the media.
	 *
	 * @param	int		Optional primary key of the article to increment.
	 *
	 * @return	boolean	True if successful; false otherwise and internal error set.
	 */
	public function link()
	{
                $app = JFactory::getApplication();

                $array = JRequest::get( 'post' );
                $user = JFactory::getUser();

                // Base this model on the backend version.
                JLoader::register('hwdMediaShareModelEditMedia', JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/models/editmedia.php');
                $model          = JModelLegacy::getInstance('EditMedia', 'hwdMediaShareModel');                       
                $id             = JRequest::getInt('id');
                $album_id       = JRequest::getInt('album_id');
                $category_id    = JRequest::getInt('category_id');
                $group_id       = JRequest::getInt('group_id');
                $playlist_id    = JRequest::getInt('playlist_id');

                if ($album_id > 0)
                {
                        JRequest::setVar('assign_album_id', $album_id);
                        if( !$model->assignAlbum( $id ) )
                        {
                                JError::raiseWarning(500, $model->getError());
                        }
                }
                
                if ($category_id > 0)
                {
                        JRequest::setVar('assign_category_id', $category_id);
                        if( !$model->assignCategory( $id ) )
                        {
                                JError::raiseWarning(500, $model->getError());
                        }
                }
                
                if ($group_id > 0)
                {
                        JRequest::setVar('assign_group_id', $group_id);
                        if( !$model->assignGroup( $id ) )
                        {
                                JError::raiseWarning(500, $model->getError());
                        }
                }
                
                if ($playlist_id > 0)
                {
                        JRequest::setVar('assign_playlist_id', $playlist_id);
                        if( !$model->assignPlaylist( $id ) )
                        {
                                JError::raiseWarning(500, $model->getError());
                        }
                }     
                
                JFactory::getApplication()->enqueueMessage( JText::_('COM_HWDMS_NOTICE_SUCCESSFULLY_ADDED_MEDIA_TO_ELEMENTS') );
                JFactory::getApplication()->redirect('index.php?option=com_hwdmediashare&task=mediaform.link&id='.$id.'&tmpl=component&Itemid='.JRequest::getInt('Itemid'));
                return true;
	} 
}
