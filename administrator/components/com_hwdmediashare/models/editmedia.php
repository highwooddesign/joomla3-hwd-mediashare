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

class hwdMediaShareModelEditMedia extends JModelAdmin
{
	/**
	 * The type alias for this content type.
         * 
         * @access      public
	 * @var         string
	 */  
	public $typeAlias = 'com_hwdmediashare.media';
        
	/**
	 * Method to get a single item.
	 *
         * @access  public
	 * @param   integer     $pk     The id of the primary key.
	 * @return  mixed       Object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
                if ($item = parent::getItem($pk))
                {
                        // Add the tags.
                        $item->tags = new JHelperTags;
                        $item->tags->getTagIds($item->id, 'com_hwdmediashare.media');
                        
                        // Add the custom fields.
                        hwdMediaShareFactory::load('customfields');
                        $HWDcustomfields = hwdMediaShareCustomFields::getInstance();
                        $HWDcustomfields->elementType = 1;
                        $item->customfields = $HWDcustomfields->load($item);
                        
                        // Add the media files.
                        hwdMediaShareFactory::load('files');
                        $HWDfiles = hwdMediaShareFiles::getInstance();
                        $item->mediafiles = $HWDfiles->getMediaFiles($item);
                        
                        // Add the media categories.
                        hwdMediaShareFactory::load('category');
                        $HWDcategory = hwdMediaShareCategory::getInstance();
                        $item->catid = $HWDcategory->getInputValue($item);                      
                        
                        // Add the media type.
                        hwdMediaShareFactory::load('media');
                        $item->media_type = hwdMediaShareMedia::loadMediaType($item);
                        
                        // Add the number of elements associated with this media.
                        $item->numalbums = $this->getAlbumCount($item);
                        $item->numplaylists = $this->getPlaylistCount($item);
                        $item->numgroups = $this->getGroupCount($item);
                        $item->numlinkedmedia = $this->getLinkedMediaCount($item);
                        $item->numlinkedpages = $this->getLinkedPagesCount($item);
                        $item->numresponses = $this->getResponseCount($item);
                        
                        // Add the thumbnail.
                        //$item->thumbnail = $this->getThumbnail($item);
                        $item->customthumbnail = $this->getThumbnail($item);
     
                        // Add the map.
                        if (!empty($item->location))
                        {
                                hwdMediaShareFactory::load('googlemaps.GoogleMap');
                                hwdMediaShareFactory::load('googlemaps.JSMin');
                                hwdMediaShareFactory::load('googlemaps.map');
                                $map = new hwdMediaShareMap();
                                $map->addMarkerByAddress($item->location,$item->title,$item->description);
                                $map->getJavascriptHeader();
                                $map->getJavascriptMap();
                                $map->setWidth('100%');
                                $map->setHeight('280px');
                                $map->setMapType('map');
                                $item->map = $map->getOnLoad().$map->getMap().$map->getSidebar();
                        }
                        else
                        {
                                $item->map = '';
                        }
                }

		return $item;
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
	public function getTable($name = 'Media', $prefix = 'hwdMediaShareTable', $config = array())
	{
		return JTable::getInstance($name, $prefix, $config);
	}
        
	/**
	 * Abstract method for getting the form from the model.
	 *
	 * @access  public
	 * @param   array       $data      Data for the form.
	 * @param   boolean     $loadData  True if the form is to load its own data (default case), false if not.
	 * @return  mixed       A JForm object on success, false on failure
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_hwdmediashare.media', 'media', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @access  protected
         * @return  mixed       The data for the form.
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_hwdmediashare.edit.media.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

                // Tweak the thumbnail data to fit our framework.
                $data->thumbnail_remote = $data->thumbnail;
                $data->thumbnail = '';
                
		return $data;   
	}

        /**
	 * Method to get the thumbnail for the media.
         * 
         * @access  public
         * @param   object  $item   The album object.
	 * @return  mixed   The thumnail location on success, false on failure.
	 */
	public function getThumbnail($item)
	{
                // Load the HWD downloads library.
                hwdMediaShareFactory::load('downloads');
                $HWDdownloads = hwdMediaShareDownloads::getInstance();
                $HWDdownloads->elementType = 1;
                if ($thumbnail = $HWDdownloads->getElementThumbnail($item))
                {
                        return $thumbnail;
                }
                else
                {
                        return false;
                }
	}
        
        /**
	 * Method to count the number of albums associated with a media.
         * 
         * @access  public
         * @param   object  $item   The media object.
	 * @return  mixed   An integer on success, false on failure.
	 */
	public function getAlbumCount($item)
	{
                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                        ->select('COUNT(*)')
                        ->from('#__hwdms_album_map')
                        ->where('media_id = ' . $db->quote($item->id));
                try
                {
                        $db->setQuery($query);
                        $count = $db->loadResult();
                }
                catch (RuntimeException $e)
                {
                        $this->setError($e->getMessage());
                        return false;                            
                }
                return $count;
	}
        
        /**
	 * Method to count the number of playlists associated with a media.
         * 
         * @access  public
         * @param   object  $item   The media object.
	 * @return  mixed   An integer on success, false on failure.
	 */
	public function getPlaylistCount($item)
	{
                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                        ->select('COUNT(*)')
                        ->from('#__hwdms_playlist_map')
                        ->where('media_id = ' . $db->quote($item->id));
                try
                {
                        $db->setQuery($query);
                        $count = $db->loadResult();
                }
                catch (RuntimeException $e)
                {
                        $this->setError($e->getMessage());
                        return false;                            
                }
                return $count;
	}
        
        /**
	 * Method to count the number of groups associated with a media.
         * 
         * @access  public
         * @param   object  $item   The media object.
	 * @return  mixed   An integer on success, false on failure.
	 */
	public function getGroupCount($item)
	{
                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                        ->select('COUNT(*)')
                        ->from('#__hwdms_group_map')
                        ->where('media_id = ' . $db->quote($item->id));
                try
                {
                        $db->setQuery($query);
                        $count = $db->loadResult();
                }
                catch (RuntimeException $e)
                {
                        $this->setError($e->getMessage());
                        return false;                            
                }
                return $count;
	}
        
        /**
	 * Method to count the number of other media associated with a media.
         * 
         * @access  public
         * @param   object  $item   The media object.
	 * @return  mixed   An integer on success, false on failure.
	 */
	public function getLinkedMediaCount($item)
	{
                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                        ->select('COUNT(*)')
                        ->from('#__hwdms_media_map')
                        ->where('(media_id_1 = ' . $db->quote($item->id) . ' OR media_id_2 = ' . $db->quote($item->id) . ')');
                try
                {
                        $db->setQuery($query);
                        $count = $db->loadResult();
                }
                catch (RuntimeException $e)
                {
                        $this->setError($e->getMessage());
                        return false;                            
                }
                return $count;
	}

        /**
	 * Method to count the number of pages associated with a media.
         * 
         * @access  public
         * @param   object  $item   The media object.
	 * @return  mixed   An integer on success, false on failure.
	 */
	public function getLinkedPagesCount($item)
	{
                return 0;
	}
        
        /**
	 * Method to count the number of responses associated with a media.
         * 
         * @access  public
         * @param   object  $item   The media object.
	 * @return  mixed   An integer on success, false on failure.
	 */
	public function getResponseCount($item)
	{
                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                        ->select('COUNT(*)')
                        ->from('#__hwdms_response_map')
                        ->where('media_id = ' . $db->quote($item->id));
                try
                {
                        $db->setQuery($query);
                        $count = $db->loadResult();
                }
                catch (RuntimeException $e)
                {
                        $this->setError($e->getMessage());
                        return false;                            
                }
                return $count;
	}

	/**
	 * Method to toggle the approval status of one or more records.
	 *
         * @access  public
	 * @param   array    $pks   An array of record primary keys.
	 * @param   integer  $value The value to toggle to.
	 * @return  boolean  True on success.
	 */
	public function approve($pks, $value = 0)
	{
		// Initialise variables.
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
			$this->setError(JText::_('JGLOBAL_NO_ITEM_SELECTED'));
			return false;
		}

		try
		{
			$db = $this->getDbo();
			$query = $db->getQuery(true)
                                    ->update($db->quoteName('#__hwdms_media'))
                                    ->set('status = ' . $db->quote((int) $value))
                                    ->where('id IN (' . implode(',', $pks) . ')');
			$db->setQuery($query);
			$db->execute();
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());
			return false;
		}

		// Get a table instance.
                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                $table = JTable::getInstance('Media', 'hwdMediaShareTable');

                // Load HWD library.
                hwdMediaShareFactory::load('events');
                $HWDevents = hwdMediaShareEvents::getInstance();
                
		foreach ($pks as $pk)
		{
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
                        
                        // Trigger onAfterMediaAdd event.
                        $HWDevents->triggerEvent('onAfterMediaAdd', $item); 
		}
                
		// Clear the component's cache.
		$this->cleanCache();

		return true;
	}
        
	/**
	 * Method to toggle the featured value of one or more records.
	 *
         * @access  public
	 * @param   array    $pks   An array of record primary keys.
	 * @param   integer  $value The value to toggle to.
	 * @return  boolean  True on success.
	 */
	public function feature($pks, $value = 0)
	{
		// Initialise variables.
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
			$this->setError(JText::_('JGLOBAL_NO_ITEM_SELECTED'));
			return false;
		}

		try
		{
			$db = $this->getDbo();
			$query = $db->getQuery(true)
                                    ->update($db->quoteName('#__hwdms_media'))
                                    ->set('featured = ' . $db->quote((int) $value))
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
	 * Method to link one or more media items with a category.
	 *
         * @access  public
	 * @param   array    $pks         A list of the primary keys to change.
	 * @param   integer  $categoryId  The value of the category key to associate with.
	 * @return  boolean  True on success.
	 */
	public function assignCategory($pks, $categoryId = null)
	{
                // Load HWD authorise library.
                hwdMediaShareFactory::load('authorise');
                $HWDauthorise = hwdMediaShareAuthorise::getInstance();
                
                // Load HWD category library.
		hwdMediaShareFactory::load('category');
                $HWDcategory = hwdMediaShareCategory::getInstance();

		// Sanitize the ids.
		$pks = (array) $pks;
		JArrayHelper::toInteger($pks);

		// Iterate through the items to process each one.
		foreach ($pks as $i => $pk)
		{
                        if (!$HWDauthorise->authoriseCategoryAction('link', $categoryId, $pk))
                        {
                                // Prune items that you can't change.
                                unset($pks[$i]);
                                $error = $this->getError();

                                if ($error)
                                {
                                        JLog::add($error, JLog::WARNING, 'jerror');
                                        return false;
                                }
                                else
                                {
                                        JLog::add(JText::_('COM_HWDMS_ERROR_ACTION_NOT_PERMITTED'), JLog::WARNING, 'jerror');
                                        return false;
                                }
                        }
                        
                        // Create an object to bind to the database.
                        $object = new StdClass;
                        $object->categoryId = (int) $categoryId;
                        $object->elementId = (int) $pk;
                        $object->elementType = 1;

                        // Attempt to associate this category with the media.
                        if (!$HWDcategory->saveIndividual($categoryId, $pk))
                        {
                                $this->setError($HWDcategory->getError());
                                return false;
                        }                      
		}

		// Clear the component's cache.
		$this->cleanCache();

                return true;
	}
        
	/**
	 * Method to unlink one or more media items with a category.
	 *
         * @access  public
	 * @param   array    $pks         A list of the primary keys to change.
	 * @param   integer  $categoryId  The value of the category key to associate with.
	 * @return  boolean  True on success.
	 */
	public function unassignCategory($pks, $categoryId = null)
	{
		// Initialise variables.
                $db = JFactory::getDbo();

                // Load HWD authorise library.
                hwdMediaShareFactory::load('authorise');
                $HWDauthorise = hwdMediaShareAuthorise::getInstance();
                
		$table = $this->getTable('CategoryMap', 'hwdMediaShareTable');    

                // Sanitize the ids.
		$pks = (array) $pks;
		JArrayHelper::toInteger($pks);

		// Iterate through the items to process each one.
		foreach ($pks as $i => $pk)
		{
                        $query = $db->getQuery(true)
                                ->select('id')
                                ->from('#__hwdms_category_map')
                                ->where('element_type = ' . $db->quote(1))
                                ->where('element_id = ' . $db->quote($pk))
                                ->where('category_id = ' . $db->quote($categoryId));

                        $db->setQuery($query);
                        try
                        {
                                $rows = $db->loadColumn();
                        }
                        catch (RuntimeException $e)
                        {
                                $this->setError($e->getMessage());
                                return false;                            
                        }

                        // Iterate the items to delete each one.
                        foreach ($rows as $x => $row)
                        {
                                if ($table->load($row))
                                {
                                        if ($HWDauthorise->authoriseCategoryAction('unlink', $categoryId, $pk))
                                        {
                                                if (!$table->delete($row))
                                                {
                                                        $this->setError($table->getError());
                                                        return false;
                                                }
                                        }
                                        else
                                        {
                                                // Prune items that you can't change.
                                                unset($rows[$x]);
                                                $error = $this->getError();

                                                if ($error)
                                                {
                                                        JLog::add($error, JLog::WARNING, 'jerror');
                                                        return false;
                                                }
                                                else
                                                {
                                                        JLog::add(JText::_('COM_HWDMS_ERROR_ACTION_NOT_PERMITTED'), JLog::WARNING, 'jerror');
                                                        return false;
                                                }
                                        }
                                }
                                else
                                {
                                        $this->setError($table->getError());
                                        return false;
                                }
                        }
		}

		// Clear the component's cache.
		$this->cleanCache();
             
		return true;
	}

        /**
	 * Method to add a process for an array of media items.
	 *
         * @access  public
	 * @param   array    $pks       A list of the primary keys to change.
	 * @param   integer  $processId The value of the process key to associate with.
	 * @return  boolean  True on success.
	 */
	public function assignProcess($pks, $processId = null)
	{
		// Initialise variables.
                $user = JFactory::getUser();

                // Load HWD processes library.
		hwdMediaShareFactory::load('processes');
                $HWDprocesses = hwdMediaShareProcesses::getInstance();

		// Sanitize the ids.
		$pks = (array) $pks;
		JArrayHelper::toInteger($pks);

		// Iterate through the items to process each one.
		foreach ($pks as $i => $pk)
		{
                        if (!$user->authorise('core.edit', 'com_hwdmediashare.media.'.$pk))
                        {
                                // Prune items that you can't change.
                                unset($pks[$i]);
                                $error = $this->getError();

                                if ($error)
                                {
                                        JLog::add($error, JLog::WARNING, 'jerror');
                                        return false;
                                }
                                else
                                {
                                        JLog::add(JText::_('COM_HWDMS_ERROR_ACTION_NOT_PERMITTED'), JLog::WARNING, 'jerror');
                                        return false;
                                }
                        }
                        
                        // Get a table instance.
                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                        $table = JTable::getInstance('Media', 'hwdMediaShareTable');
                        
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

                        // Attempt to add the process to the media.
                        if (!$HWDprocesses->add($item, $processId))
                        {
                                $this->setError($HWDprocesses->getError());
                                return false;
                        }                      
		}

		// Clear the component's cache.
		$this->cleanCache();

                return true;
	}
        
	/**
	 * Method to perform batch operations on an item or a set of items.
	 *
	 * @access  public
	 * @param   array   $commands  An array of commands to perform.
	 * @param   array   $pks       An array of item ids.
	 * @param   array   $contexts  An array of item contexts.
	 * @return  boolean Returns true on success, false on failure.
	 */
	public function batch($commands, $pks, $contexts)
	{           
                $done1 = false;
                $done2 = false;
                
                if (parent::batch($commands, $pks, $contexts))
                {
			$done1 = true;
		}

		// Sanitize ids.
		$pks = array_unique($pks);
		JArrayHelper::toInteger($pks);

		// Remove any values of zero.
		if (array_search(0, $pks, true))
		{
			unset($pks[array_search(0, $pks, true)]);
		}

		if (empty($pks))
		{
			$this->setError(JText::_('JGLOBAL_NO_ITEM_SELECTED'));
			return false;
		}                

		if (is_numeric($commands['assignprocess']) && $commands['assignprocess'] > 0)
		{
			$value = (int) $commands['assignprocess'];
                        
                        if (!$this->assignProcess($pks, $value))
			{
				return false;
			}

			$done2 = true;
		}                

		if (is_numeric($commands['assigncategory']) && $commands['assigncategory'] > 0)
		{
			$value = (int) $commands['assigncategory'];
                        
                        if (!$this->assignCategory($pks, $value))
			{
				return false;
			}

			$done2 = true;
		} 
 
		if (is_numeric($commands['unassigncategory']) && $commands['unassigncategory'] > 0)
		{
			$value = (int) $commands['unassigncategory'];
                        
                        if (!$this->unassignCategory($pks, $value))
			{
				return false;
			}

			$done2 = true;
		} 

		if (is_numeric($commands['assignalbum']) && $commands['assignalbum'] > 0)
		{
			$value = (int) $commands['assignalbum'];
                        
                        $modelFile = JModelAdmin::getInstance('albumMediaItem', 'hwdMediaShareModel'); 
                        if (!$modelFile->link($pks, $value))
			{
				return false;
			}

			$done2 = true;
		} 
 
		if (is_numeric($commands['unassignalbum']) && $commands['unassignalbum'] > 0)
		{
			$value = (int) $commands['unassignalbum'];
                        
                        $modelFile = JModelAdmin::getInstance('albumMediaItem', 'hwdMediaShareModel'); 
                        if (!$modelFile->unlink($pks, $value))
			{
				return false;
			}

			$done2 = true;
		}                 

		if (is_numeric($commands['assignplaylist']) && $commands['assignplaylist'] > 0)
		{
			$value = (int) $commands['assignplaylist'];
                        
                        $modelFile = JModelAdmin::getInstance('playlistMediaItem', 'hwdMediaShareModel'); 
                        if (!$modelFile->link($pks, $value))
			{
				return false;
			}

			$done2 = true;
		} 
 
		if (is_numeric($commands['unassignplaylist']) && $commands['unassignplaylist'] > 0)
		{
			$value = (int) $commands['unassignplaylist'];
                        
                        $modelFile = JModelAdmin::getInstance('playlistMediaItem', 'hwdMediaShareModel'); 
                        if (!$modelFile->unlink($pks, $value))
			{
				return false;
			}

			$done2 = true;
		} 

		if (is_numeric($commands['assigngroup']) && $commands['assigngroup'] > 0)
		{
			$value = (int) $commands['assigngroup'];
                        
                        $modelFile = JModelAdmin::getInstance('groupMediaItem', 'hwdMediaShareModel'); 
                        if (!$modelFile->link($pks, $value))
			{
				return false;
			}

			$done2 = true;
		} 
 
		if (is_numeric($commands['unassigngroup']) && $commands['unassigngroup'] > 0)
		{
			$value = (int) $commands['unassigngroup'];
                        
                        $modelFile = JModelAdmin::getInstance('groupMediaItem', 'hwdMediaShareModel'); 
                        if (!$modelFile->unlink($pks, $value))
			{
				return false;
			}

			$done2 = true;
		} 

		if (!$done1 && !$done2)
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_INSUFFICIENT_BATCH_INFORMATION'));
			return false;
		}

		// Clear the component's cache.
		$this->cleanCache();

		return true;                
	}

	/**
	 * Method to delete one or more records. Overload to remove any
         * associated data.
	 *
         * @access  public
	 * @param   array   $pks    An array of record primary keys.
	 * @return  boolean True if successful, false if an error occurs.
	 * @note    $pks is passed by reference only because JModelAdmin parent method does, and we need to keep this declaration compatible.
	 */
	public function delete(&$pks)
	{
                if (!parent::delete($pks))
                {
			return false;
		}
                
		$db = JFactory::getDBO();
                $pks = (array) $pks;
                
                // Array holding all queries.
                $queries = array();

		// Loop through keys and generate queries to execute.
		foreach ($pks as $i => $pk)
		{
                        // Delete records from activities.
                        $queries[] = $db->getQuery(true)
                                        ->delete('#__hwdms_activities')
                                        ->where('element_type = ' . $db->quote(1))
                                        ->where('element_id = ' . $db->quote($pk));

                        // Delete records from album map.
                        $queries[] = $db->getQuery(true)
                                        ->delete('#__hwdms_album_map')
                                        ->where('media_id = ' . $db->quote($pk));

                       // Delete records from category map.
                        $queries[] = $db->getQuery(true)
                                        ->delete('#__hwdms_category_map')
                                        ->where('element_type = ' . $db->quote(1))
                                        ->where('element_id = ' . $db->quote($pk));

                        // Delete records from content map.
                        $queries[] = $db->getQuery(true)
                                        ->delete('#__hwdms_content_map')
                                        ->where('media_id = ' . $db->quote($pk));

                        // Delete records from favourites.
                        $queries[] = $db->getQuery(true)
                                        ->delete('#__hwdms_favourites')
                                        ->where('element_type = ' . $db->quote(1))
                                        ->where('element_id = ' . $db->quote($pk));
                        
                        // Delete records from field values.
                        $queries[] = $db->getQuery(true)
                                        ->delete('#__hwdms_fields_values')
                                        ->where('element_type = ' . $db->quote(1))
                                        ->where('element_id = ' . $db->quote($pk));

                        // Delete records from group map.
                        $queries[] = $db->getQuery(true)
                                        ->delete('#__hwdms_group_map')
                                        ->where('media_id = ' . $db->quote($pk));

                        // Delete records from likes.
                        $queries[] = $db->getQuery(true)
                                        ->delete('#__hwdms_likes')
                                        ->where('element_type = ' . $db->quote(1))
                                        ->where('element_id = ' . $db->quote($pk));

                        // Delete records from media map.
                        $queries[] = $db->getQuery(true)
                                        ->delete('#__hwdms_media_map')
                                        ->where('(media_id_1 = ' . $db->quote(1) . ' OR media_id_2 = ' . $db->quote($pk) . ')');

                        // Delete records from playlist map.
                        $queries[] = $db->getQuery(true)
                                        ->delete('#__hwdms_playlist_map')
                                        ->where('media_id = ' . $db->quote($pk));

                        // Delete records from processes.
                        $queries[] = $db->getQuery(true)
                                        ->delete('#__hwdms_processes')
                                        ->where('media_id = ' . $db->quote($pk));

                        // Delete records from reports.
                        $queries[] = $db->getQuery(true)
                                        ->delete('#__hwdms_reports')
                                        ->where('element_type = ' . $db->quote(1))
                                        ->where('element_id = ' . $db->quote($pk));

                        // Delete records from response map.
                        $queries[] = $db->getQuery(true)
                                        ->delete('#__hwdms_processes')
                                        ->where('media_id = ' . $db->quote($pk));

                        // Delete records from reports.
                        $queries[] = $db->getQuery(true)
                                        ->delete('#__hwdms_subscriptions')
                                        ->where('element_type = ' . $db->quote(1))
                                        ->where('element_id = ' . $db->quote($pk)); 
		}

                // Execute the generated queries.
                foreach ($queries as $query)
                {
                        try
                        {
                                $db->setQuery($query);
                                $db->execute();
                        }
                        catch (RuntimeException $e)
                        {
                                $this->setError($e->getMessage());
                                return false;                            
                        }
                }   
                
		// Get a table instance.
                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                $table = JTable::getInstance('Media', 'hwdMediaShareTable');

                // Load file model.
                $modelFile = JModelAdmin::getInstance('File','hwdMediaShareModel'); 
                
                // Load HWD files library.
                hwdMediaShareFactory::load('files');
                hwdMediaShareFiles::getLocalStoragePath();
                $HWDfiles = hwdMediaShareFiles::getInstance();
                        
		// Loop through keys and remove media files.
		foreach ($pks as $i => $pk)
		{             
                        // Attempt to load the table row.
                        if ($table->load($pk))
                        {
                                $properties = $table->getProperties(1);
                                $item = JArrayHelper::toObject($properties, 'JObject');

                                $files = $HWDfiles->getMediaFiles($item);

                                $fileArray = array();
                                foreach($files as $file)
                                {
                                        $fileArray[] = (int) $file->id;
                                }                        

                                if (!$modelFile->delete($fileArray))
                                {
                                        $this->setError($modelFile->getError());
                                }
                        }
                }

		// Clear the component's cache.
		$this->cleanCache();

		return true;
	}
}
