<?php
/**
 * @version    SVN $Id: editmedia.php 1648 2013-08-16 09:21:26Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * hwdMediaShare Model
 */
class hwdMediaShareModelEditMedia extends JModelAdmin
{
	var $elementType = 1;
        /**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
                if ($item = parent::getItem($pk)) 
                {
                        hwdMediaShareFactory::load('files');
                        $hwdmsFiles = hwdMediaShareFiles::getInstance();
                        $item->mediaitems = $hwdmsFiles->getMediaFiles($item);
                        hwdMediaShareFactory::load('category');
                        $item->catid = hwdMediaShareCategory::getInput($item);
                        hwdMediaShareFactory::load('tags');
                        $item->tags = hwdMediaShareTags::getInput($item);
                        hwdMediaShareFactory::load('customfields');
                        $item->customfields = hwdMediaShareCustomFields::get($item);
                        $item->albumcount = $this->getAlbumCount($item);
                        $item->playlistcount = $this->getPlaylistCount($item);
                        $item->groupcount = $this->getGroupCount($item);
                        $item->linkedmediacount = $this->getLinkedMediaCount($item);
                        $item->linkedpagescount = $this->getLinkedPagesCount($item);
                        $item->responsescount = $this->getResponseCount($item);
                        $item->responds = $this->getResponds($item);
                        $item->customthumbnail = $this->getThumbnail($item);
                        hwdMediaShareFactory::load('media');
                        $item->media_type = hwdMediaShareMedia::loadMediaType($item);

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
		return $item;
	}
        /**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param	array	$data	An array of input data.
	 * @param	string	$key	The name of the key for the primary key.
	 *
	 * @return	boolean
	 * @since	0.1
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		// Check specific edit permission then general edit permission.
		return JFactory::getUser()->authorise('core.edit', 'com_hwdmediashare.media.'.((int) isset($data[$key]) ? $data[$key] : 0)) or parent::allowEdit($data, $key);
	}
	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	0.1
	 */
	public function getTable($type = 'Media', $prefix = 'hwdMediaShareTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	0.1
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
	 * Method to get the script that have to be included on the form
	 *
	 * @return string	Script files
	 */
	public function getAlbumCount($item)
	{            
                $db =& JFactory::getDBO();
                $query = "
                  SELECT COUNT(*)
                    FROM ".$db->quoteName('#__hwdms_album_map')."
                    WHERE ".$db->quoteName('media_id')." = ".$db->quote($item->id).";
                  ";
                $db->setQuery($query);
                return $db->loadResult();
	}
        /**
	 * Method to get the script that have to be included on the form
	 *
	 * @return string	Script files
	 */
	public function getPlaylistCount($item)
	{
                $db =& JFactory::getDBO();
                $query = "
                  SELECT COUNT(*)
                    FROM ".$db->quoteName('#__hwdms_playlist_map')."
                    WHERE ".$db->quoteName('media_id')." = ".$db->quote($item->id).";
                  ";
                $db->setQuery($query);
                return $db->loadResult();
	}
        /**
	 * Method to get the script that have to be included on the form
	 *
	 * @return string	Script files
	 */
	public function getGroupCount($item)
	{
                $db =& JFactory::getDBO();
                $query = "
                  SELECT COUNT(*)
                    FROM ".$db->quoteName('#__hwdms_group_map')."
                    WHERE ".$db->quoteName('media_id')." = ".$db->quote($item->id).";
                  ";
                $db->setQuery($query);
                return $db->loadResult();
	}
        /**
	 * Method to get the script that have to be included on the form
	 *
	 * @return string	Script files
	 */
	public function getLinkedMediaCount($item)
	{
                $db =& JFactory::getDBO();
                $query = "
                  SELECT COUNT(*)
                    FROM ".$db->quoteName('#__hwdms_media_map')."
                    WHERE ".$db->quoteName('media_id_1')." = ".$db->quote($item->id)."
                    OR ".$db->quoteName('media_id_2')." = ".$db->quote($item->id)."
                  ";
                $db->setQuery($query);
                return $db->loadResult();
	}
        /**
	 * Method to get the script that have to be included on the form
	 *
	 * @return string	Script files
	 */
	public function getLinkedPagesCount($item)
	{
                $db =& JFactory::getDBO();
                $query = "
                  SELECT COUNT(*)
                    FROM ".$db->quoteName('#__hwdms_album_map')."
                    WHERE ".$db->quoteName('album_id')." = ".$db->quote($item->id).";
                  ";
                $db->setQuery($query);
                return $db->loadResult();
	}
        /**
	 * Method to get the script that have to be included on the form
	 *
	 * @return string	Script files
	 */
	public function getResponseCount($item)
	{
                $db =& JFactory::getDBO();
                $query = "
                  SELECT COUNT(*)
                    FROM ".$db->quoteName('#__hwdms_response_map')."
                    WHERE ".$db->quoteName('media_id')." = ".$db->quote($item->id).";
                  ";
                $db->setQuery($query);
                return $db->loadResult();
	}
        
        /**
	 * Method to get the script that have to be included on the form
	 *
	 * @return string	Script files
	 */
	public function getResponds($item)
	{
                $db =& JFactory::getDBO();
                $query = "
                  SELECT *
                    FROM ".$db->quoteName('#__hwdms_response_map')."
                    WHERE ".$db->quoteName('response_id')." = ".$db->quote($item->id).";
                  ";
                $db->setQuery($query);
                return $db->loadObjectList();
	}
        
	/**
	 * Method to get the script that have to be included on the form
	 *
	 * @return string	Script files
	 */
	public function getThumbnail($item)
	{
                if (hwdMediaShareFactory::getElementThumbnail($item))
                {
                        return true;
                }
                else
                {
                        return false;
                }
	}
        /**
	 * Method to get the script that have to be included on the form
	 *
	 * @return string	Script files
	 */
	public function getScript()
	{
		return 'administrator/components/com_hwdmediashare/models/forms/media.js';
	}
        /**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	0.1
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_hwdmediashare.edit.media.data', array());
		if (empty($data))
		{
			$data = $this->getItem();
		}
                
                // Tweak the thumbnail data to fit our framework
                $data->thumbnail_remote = $data->thumbnail;
                $data->thumbnail = '';
                
		return $data;
	}
        
	/**
	 * Method to toggle the featured setting of articles.
	 *
	 * @param	array	The ids of the items to toggle.
	 * @param	int		The value to toggle to.
	 *
	 * @return	boolean	True on success.
	 */
	public function approve($pks, $value = 0)
	{
		// Initialise variables.
		$user		= JFactory::getUser();
		$table		= $this->getTable();
		$pks		= (array) $pks;

                $db =& JFactory::getDBO();
                $query = "
                  UPDATE ".$db->quoteName('#__hwdms_media')."
                    SET ".$db->quoteName('status')." = ".$db->quote($value)."
                    WHERE ".$db->quoteName('id')." = ".implode(" OR ".$db->quoteName('id')." = ", $pks)."
                  ";
                $db->setQuery($query);
                
                // Check for a database error.
		if (!$db->query())
                {
			$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_PUBLISH_FAILED', get_class($this), $this->_db->getErrorMsg()));
			$this->setError($e);

			return false;
		}

                // Clear the component's cache
		$this->cleanCache();

                // Trigger onAfterMediaAdd
                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                $table =& JTable::getInstance('Media', 'hwdMediaShareTable');
                hwdMediaShareFactory::load('events');
                $events = hwdMediaShareEvents::getInstance();
		foreach ($pks as $id)
		{
                        $table->load( $id );
                        $properties = $table->getProperties(1);
                        $row = JArrayHelper::toObject($properties, 'JObject');
                        $events->triggerEvent('onAfterMediaAdd', $row); 
		}
                
		return true;
	}
        
	/**
	 * Method to toggle the featured setting of articles.
	 *
	 * @param	array	The ids of the items to toggle.
	 * @param	int		The value to toggle to.
	 *
	 * @return	boolean	True on success.
	 */
	public function feature($pks, $value = 0)
	{
		// Initialise variables.
		$user		= JFactory::getUser();
		$table		= $this->getTable();
		$pks		= (array) $pks;

                $db =& JFactory::getDBO();
                $query = "
                  UPDATE ".$db->quoteName('#__hwdms_media')."
                    SET ".$db->quoteName('featured')." = ".$db->quote($value)."
                    WHERE ".$db->quoteName('id')." = ".implode(" OR ".$db->quoteName('id')." = ", $pks)."
                  ";
                $db->setQuery($query);

                // Check for a database error.
		if (!$db->query())
                {
			$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_PUBLISH_FAILED', get_class($this), $this->_db->getErrorMsg()));
			$this->setError($e);

			return false;
		}

                // Clear the component's cache
		$this->cleanCache();

		return true;

	}

        /**
	 * Method to assign user to a single record
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function batch($pks, $value = array())
	{
		// Initialise variables.
		$user		= JFactory::getUser();
		$table		= $this->getTable();
		$pks		= (array) $pks;

		if (!isset($value['user']) || !isset($value['access']) || !isset($value['language']))
                {
			$this->setError(JText::_('JGLOBAL_ERROR_INSUFFICIENT_BATCH_INFORMATION'));
                        return false;
		}
                
		// Access checks.
		foreach ($pks as $i => $id)
		{
                        $data = array();
                        $data['id'] = $id;
                        !empty($value['user']) ? $data['created_user_id'] = $value['user'] : null;
                        !empty($value['access']) ? $data['access'] = $value['access'] : null;
                        !empty($value['language']) ? $data['language'] = $value['language'] : null;

                        if (!parent::save($data))
                        {
                                $this->setError(JText::_('COM_HWDMS_SAVE_FAILED'));
                                return false;  
                        }
		}

                // Clear the component's cache
		$this->cleanCache();

		return true;
	}

        /**
	 * Method to assign category to a single record
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function assignCategory($id = null)
	{
                if(empty($id))
		{
                        $this->setError(JText::_('COM_HWDMS_INVALID_ID'));
                        return false;
		}

                $categoryId = JRequest::getInt('assign_category_id');              
                if(empty($categoryId) || $categoryId == 0)
		{
                        $this->setError(JText::_('COM_HWDMS_INVALID_CATEGORY'));
                        return false;
		}

                $params = new StdClass;
                $params->elementId = $id;
                $params->elementType = 1;
                $params->categoryId = $categoryId;                
                hwdMediaShareFactory::load('category');
                if (hwdMediaShareCategory::saveIndividual($params))
                {
			return true;
		}
		return false;
	}
        /**
	 * Method to assign category to a single record
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function unassignCategory($id = null)
	{
                if(empty($id))
		{
			JError::raiseError( '500' , JText::_('COM_HWDMS_INVALID_ID') );
		}

                $categoryId = JRequest::getInt('unassign_category_id');
                if(empty($categoryId) || $categoryId == 0)
		{
			JError::raiseError( '500' , JText::_('COM_HWDMS_INVALID_CATEGORY') );
		}

                $db =& JFactory::getDBO();

                $query = "
                      DELETE
                        FROM ".$db->quoteName('#__hwdms_category_map')."
                        WHERE ".$db->quoteName('element_id')." = ".$db->quote($id)."
                        AND ".$db->quoteName('element_type')." = ".$db->quote(1)."
                        AND ".$db->quoteName('category_id')." = ".$db->quote($categoryId)."
                      ";

                $db->setQuery($query);
                if (!$db->query() && $config->getValue( 'debug' ))
                {
                        $app->enqueueMessage(nl2br($db->getErrorMsg()),'error');
                }
                else
                {
                        return true;
                }

		return false;
	}
        /**
	 * Method to assign category to a single record
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function assignAlbum($id = null)
	{
                $db =& JFactory::getDBO();
                $user = & JFactory::getUser();
                $date =& JFactory::getDate();

                if(empty($id))
		{
			JError::raiseError( '500' , JText::_('COM_HWDMS_INVALID_ID') );
		}

                $albumId = JRequest::getInt('assign_album_id');
                if(empty($albumId) || $albumId == 0)
		{
			JError::raiseError( '500' , JText::_('COM_HWDMS_INVALID_ALBUM') );
		}
            
                $query = "
                      SELECT COUNT(*)
                        FROM ".$db->quoteName('#__hwdms_album_map')."
                        WHERE ".$db->quoteName('media_id')." = ".$db->quote($id)."
                        AND ".$db->quoteName('album_id')." = ".$db->quote($albumId)."
                      ";

                $db->setQuery($query);
                $result = $db->loadResult();

                // Loop over categories assigned to elementid
                if($result == 0)
                {
                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                        $row =& JTable::getInstance('LinkedAlbums', 'hwdMediaShareTable');

                        // Create an object to bind to the database
                        $object = new StdClass;
                        $object->id = null;
                        $object->media_id = $id;
                        $object->album_id = $albumId;
                        $object->created_user_id = $user->id;
                        $object->created = $date->format('Y-m-d H:i:s');

                        if (!$row->bind($object))
                        {
                                return JError::raiseWarning( 500, $row->getError() );
                        }

                        if (!$row->store())
                        {
                                JError::raiseError(500, $row->getError() );
                        }
                }
                return true;
	}
        /**
	 * Method to assign category to a single record
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function unassignAlbum($id = null)
	{
                if(empty($id))
		{
			JError::raiseError( '500' , JText::_('COM_HWDMS_INVALID_ID') );
		}

                $albumId = JRequest::getInt('unassign_album_id');
                if(empty($albumId) || $albumId == 0)
		{
			JError::raiseError( '500' , JText::_('COM_HWDMS_INVALID_ALBUM') );
		}

                $db =& JFactory::getDBO();

                $query = "
                      DELETE
                        FROM ".$db->quoteName('#__hwdms_album_map')."
                        WHERE ".$db->quoteName('media_id')." = ".$db->quote($id)."
                        AND ".$db->quoteName('album_id')." = ".$db->quote($albumId)."
                      ";

                $db->setQuery($query);
                if (!$db->query() && $config->getValue( 'debug' ))
                {
                        $app->enqueueMessage(nl2br($db->getErrorMsg()),'error');
                }
                else
                {
                        return true;
                }

                return false;
	}
        /**
	 * Method to assign category to a single record
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function assignPlaylist($id = null)
	{
                if(empty($id))
		{
			JError::raiseError( '500' , JText::_('COM_HWDMS_INVALID_ID') );
		}

                $playlistId = JRequest::getInt('assign_playlist_id');
                if(empty($playlistId) || $playlistId == 0)
		{
			JError::raiseError( '500' , JText::_('COM_HWDMS_INVALID_PLAYLIST') );
		}

                $db =& JFactory::getDBO();

                $query = "
                      SELECT COUNT(*)
                        FROM ".$db->quoteName('#__hwdms_playlist_map')."
                        WHERE ".$db->quoteName('media_id')." = ".$db->quote($id)."
                        AND ".$db->quoteName('playlist_id')." = ".$db->quote($playlistId)."
                      ";

                $db->setQuery($query);
                $result = $db->loadResult();

                // Loop over categories assigned to elementid
                if($result == 0)
                {
                        $user = & JFactory::getUser();
                        $date =& JFactory::getDate();
                        //$table = $this->getTable();
                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                        $table =& JTable::getInstance('LinkedPlaylists', 'hwdMediaShareTable');

                        // Create an object to bind to the database
                        $object = new StdClass;
                        $object->media_id = $id;
                        $object->playlist_id = $playlistId;
                        $object->ordering = 1000;
                        $object->created_user_id = $user->id;
                        $object->created = $date->format('Y-m-d H:i:s');

                        if (!$table->bind($object))
                        {
                                return JError::raiseWarning( 500, $table->getError() );
                        }

                        if (!$table->store())
                        {
                                JError::raiseError(500, $table->getError() );
                        }

                        // Reorder this playlist in integer increments
                        $where = ' playlist_id = '.$playlistId.' ';
                        $table->reorder($where);      
                }
                return true;
	}
        /**
	 * Method to assign category to a single record
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function unassignPlaylist($id = null)
	{
                if(empty($id))
		{
			JError::raiseError( '500' , JText::_('COM_HWDMS_INVALID_ID') );
		}

                $playlistId = JRequest::getInt('unassign_playlist_id');
                if(empty($playlistId) || $playlistId == 0)
		{
			JError::raiseError( '500' , JText::_('COM_HWDMS_INVALID_PLAYLIST') );
		}

                $db =& JFactory::getDBO();

                $query = "
                      DELETE
                        FROM ".$db->quoteName('#__hwdms_playlist_map')."
                        WHERE ".$db->quoteName('media_id')." = ".$db->quote($id)."
                        AND ".$db->quoteName('playlist_id')." = ".$db->quote($playlistId)."
                      ";

                $db->setQuery($query);
                if (!$db->query() && $config->getValue( 'debug' ))
                {
                        $app->enqueueMessage(nl2br($db->getErrorMsg()),'error');
                }
                else
                {
                        return true;
                }

                return false;
	}
        /**
	 * Method to assign category to a single record
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function assignGroup($id = null)
	{
                $db =& JFactory::getDBO();
                $user = & JFactory::getUser();
                $date =& JFactory::getDate();
            
                if(empty($id))
		{
			JError::raiseError( '500' , JText::_('COM_HWDMS_INVALID_ID') );
		}

                $groupId = JRequest::getInt('assign_group_id');
                if(empty($groupId) || $groupId == 0)
		{
			JError::raiseError( '500' , JText::_('COM_HWDMS_INVALID_GROUP') );
		}

                $query = "
                      SELECT COUNT(*)
                        FROM ".$db->quoteName('#__hwdms_group_map')."
                        WHERE ".$db->quoteName('media_id')." = ".$db->quote($id)."
                        AND ".$db->quoteName('group_id')." = ".$db->quote($groupId)."
                      ";

                $db->setQuery($query);
                $result = $db->loadResult();

                // Loop over categories assigned to elementid
                if($result == 0)
                {
                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                        $row =& JTable::getInstance('LinkedGroups', 'hwdMediaShareTable');

                        // Create an object to bind to the database
                        $object = new StdClass;
                        $object->id = null;
                        $object->media_id = $id;
                        $object->group_id = $groupId;
                        $object->created_user_id = $user->id;
                        $object->created = $date->format('Y-m-d H:i:s');

                        if (!$row->bind($object))
                        {
                                return JError::raiseWarning( 500, $row->getError() );
                        }

                        if (!$row->store())
                        {
                                JError::raiseError(500, $row->getError() );
                        }
                }
                
                hwdMediaShareFactory::load('events');
                $events = hwdMediaShareEvents::getInstance();
                $events->triggerEvent('onAfterShareMediaWithGroup', $row);

                return true;
	}
        /**
	 * Method to assign category to a single record
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function unassignGroup($id = null)
	{
                if(empty($id))
		{
			JError::raiseError( '500' , JText::_('COM_HWDMS_INVALID_ID') );
		}

                $groupId = JRequest::getInt('unassign_group_id');
                if(empty($groupId) || $groupId == 0)
		{
			JError::raiseError( '500' , JText::_('COM_HWDMS_INVALID_GROUP') );
		}

                $db =& JFactory::getDBO();

                $query = "
                      DELETE
                        FROM ".$db->quoteName('#__hwdms_group_map')."
                        WHERE ".$db->quoteName('media_id')." = ".$db->quote($id)."
                        AND ".$db->quoteName('group_id')." = ".$db->quote($groupId)."
                      ";

                $db->setQuery($query);
                if (!$db->query() && $config->getValue( 'debug' ))
                {
                        $app->enqueueMessage(nl2br($db->getErrorMsg()),'error');
                }
                else
                {
                        return true;
                }

                return false;
	}
        
        /**
	 * Method to assign category to a single record
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function assignProcess($id = null)
	{
                if(empty($id))
		{
			JError::raiseError( '500' , JText::_('COM_HWDMS_INVALID_ID') );
		}

                $processType = JRequest::getInt('assign_process_type');
                if(empty($processType) || $processType == 0)
		{
			JError::raiseError( '500' , JText::_('COM_HWDMS_INVALID_PROCESS') );
		}
                
                // Load media
                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                $table =& JTable::getInstance('Media', 'hwdMediaShareTable');
                $table->load( $id );

                $properties = $table->getProperties(1);
                $item = JArrayHelper::toObject($properties, 'JObject');

                hwdMediaShareFactory::load('processes');
                hwdMediaShareProcesses::add($item,$processType);

                return true;
	}
        
	/**
	 * Method to save the form data.
	 *
	 * @param	array	The form data.
	 *
	 * @return	boolean	True on success.
	 * @since	0.1
	 */
	public function save($data)
	{                
                $app = JFactory::getApplication();
                $date =& JFactory::getDate();
                $user = JFactory::getUser();
                $isNew = false;

                // Another unexpected fix for Joomla 3.0. If 'tags' is defined in the data which is passed, Joomla will define a 'newTags'
                // variable in the legacy framework for the admin model (don't know why)
                $tags = $data['tags'];
                unset($data['tags']);
                
                // Correctly filter the description text
                require_once JPATH_SITE.'/administrator/components/com_content/helpers/content.php';  
                
                empty($data['id']) ? $data['key'] = hwdMediaShareFactory::generateKey() : null;

                // Alter the title for save as copy
                $data['modified'] = $date->format('Y-m-d H:i:s');
                $data['modified_user_id'] = $user->id;

                // Set created_user_id only only if saving from the administrator, or if savinf new item.
                if (empty($data['id']) || $app->isAdmin())
                {
                        empty($data['created_user_id']) ? $data['created_user_id'] = $user->id : null; 
                        empty($data['created']) ? $data['created'] = $date->format('Y-m-d H:i:s') : null;
                        empty($data['alias']) ? $data['alias'] = JFilterOutput::stringURLSafe($_REQUEST['jform']['title']) : $data['alias'] = JFilterOutput::stringURLSafe($data['alias']);
                }
                        
                empty($data['publish_up']) ? $data['publish_up'] = $date->format('Y-m-d H:i:s') : null;
                empty($data['publish_down']) ? $data['publish_down'] = "0000-00-00 00:00:00" : null;

                // Set the password if one has been submitted, then unset the original input field
                if (!empty($data['params']['password1']))
                {
                        $data['params']['password'] = md5($data['key'] . $data['params']['password1']);
                }
                unset($data['params']['password1']);
                       
                $form = parent::save($data);
		if ($form) 
                {
                        if (empty($data['id'])) 
                        {
                            $isNew = true;  
                        }
                        // Set data to current database object
                        !$app->isAdmin() ? $data['id'] = $this->getState('mediaform.id') : $data['id'] = $this->getState('editmedia.id');

                        $params = new StdClass;
                        $params->elementType = 1;
                        $params->elementId = $data['id'];
                        $params->categoryId = $data['catid'];
                        $params->tags = $tags;
                        $params->key = $data['key'];
                        $params->remove = (isset($data['remove_thumbnail']) ? true : false);
                        $params->thumbnail_remote = $data['thumbnail_remote'];

                        hwdMediaShareFactory::load('category');
                        hwdMediaShareCategory::save($params);

                        hwdMediaShareFactory::load('tags');
                        hwdMediaShareTags::save($params);

                        hwdMediaShareFactory::load('customfields');
                        hwdMediaShareCustomFields::save($params);

                        hwdMediaShareFactory::load('upload');
                        hwdMediaShareUpload::processThumbnail($params);

                        if ($isNew)
                        {
                                hwdMediaShareFactory::load('events');
                                $events = hwdMediaShareEvents::getInstance();
                                $events->triggerEvent( 'onAfterMediaAdd' , $params);
                        }
                        
                        return true;
		}                   
                return false;
	}  
        
        /**
	 * Method to assign user to a single record
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function delete(&$pks)
	{
		$db =& JFactory::getDBO();
                $pks = (array) $pks;
                
		// Iterate the items to delete each one.
		foreach ($pks as $i => $pk)
		{
                        $queries = array();
                        
                        // Delete records from activities
                        $queries[] = "
                            DELETE
                                FROM ".$db->quoteName('#__hwdms_activities')."
                                WHERE ".$db->quoteName('element_type')." = ".$db->quote(1)."
                                AND ".$db->quoteName('element_id')." = ".$db->quote($pk)."
                            ";
                        
                        // Delete records from album map
                        $queries[] = "
                            DELETE
                                FROM ".$db->quoteName('#__hwdms_album_map')."
                                WHERE ".$db->quoteName('media_id')." = ".$db->quote($pk)."
                            ";
                        
                       // Delete records from category map
                        $queries[] = "
                            DELETE
                                FROM ".$db->quoteName('#__hwdms_category_map')."
                                WHERE ".$db->quoteName('element_type')." = ".$db->quote(1)."
                                AND ".$db->quoteName('element_id')." = ".$db->quote($pk)."
                            ";
                        
                        // Delete records from content map
                        $queries[] = "
                            DELETE
                                FROM ".$db->quoteName('#__hwdms_content_map')."
                                WHERE ".$db->quoteName('media_id')." = ".$db->quote($pk)."
                            ";
                        
                        // Delete records from favourites
                        $queries[] = "
                            DELETE
                                FROM ".$db->quoteName('#__hwdms_favourites')."
                                WHERE ".$db->quoteName('element_type')." = ".$db->quote(1)."
                                AND ".$db->quoteName('element_id')." = ".$db->quote($pk)."
                            ";
                        
                        // Delete records from field values
                        $queries[] = "
                            DELETE
                                FROM ".$db->quoteName('#__hwdms_fields_values')."
                                WHERE ".$db->quoteName('element_type')." = ".$db->quote(1)."
                                AND ".$db->quoteName('element_id')." = ".$db->quote($pk)."
                            ";
                        
                        // Delete records from group map
                        $queries[] = "
                            DELETE
                                FROM ".$db->quoteName('#__hwdms_group_map')."
                                WHERE ".$db->quoteName('media_id')." = ".$db->quote($pk)."
                            ";                       
                        
                        // Delete records from likes
                        $queries[] = "
                            DELETE
                                FROM ".$db->quoteName('#__hwdms_likes')."
                                WHERE ".$db->quoteName('element_type')." = ".$db->quote(1)."
                                AND ".$db->quoteName('element_id')." = ".$db->quote($pk)."
                            ";
                        
                        // Delete records from media map
                        $queries[] = "
                            DELETE
                                FROM ".$db->quoteName('#__hwdms_media_map')."
                                WHERE (".$db->quoteName('media_id_1')." = ".$db->quote($pk)." 
                                OR ".$db->quoteName('media_id_2')." = ".$db->quote($pk).")
                            ";   
                        
                        // Delete records from playlist map
                        $queries[] = "
                            DELETE
                                FROM ".$db->quoteName('#__hwdms_playlist_map')."
                                WHERE ".$db->quoteName('media_id')." = ".$db->quote($pk)."
                            ";    
                        
                        // Delete records from processes
                        $queries[] = "
                            DELETE
                                FROM ".$db->quoteName('#__hwdms_processes')."
                                WHERE ".$db->quoteName('media_id')." = ".$db->quote($pk)."
                            ";  
                        
                        // Delete records from reports
                        $queries[] = "
                            DELETE
                                FROM ".$db->quoteName('#__hwdms_reports')."
                                WHERE ".$db->quoteName('element_type')." = ".$db->quote(1)."
                                AND ".$db->quoteName('element_id')." = ".$db->quote($pk)."
                            ";
                        
                        // Delete records from response map
                        $queries[] = "
                            DELETE
                                FROM ".$db->quoteName('#__hwdms_response_map')."
                                WHERE ".$db->quoteName('media_id')." = ".$db->quote($pk)."
                            ";  
                        
                        // Delete records from subscriptions
                        $queries[] = "
                            DELETE
                                FROM ".$db->quoteName('#__hwdms_subscriptions')."
                                WHERE ".$db->quoteName('element_type')." = ".$db->quote(1)."
                                AND ".$db->quoteName('element_id')." = ".$db->quote($pk)."
                            ";
                        
                        // Delete records from tag map
                        $queries[] = "
                            DELETE
                                FROM ".$db->quoteName('#__hwdms_tag_map')."
                                WHERE ".$db->quoteName('element_type')." = ".$db->quote(1)."
                                AND ".$db->quoteName('element_id')." = ".$db->quote($pk)."
                            ";                

                        // Iterate the queries to execute each one.
                        foreach ($queries as $query)
                        {
                                $db->setQuery($query);
                                if (!$db->query())
                                {
                                        $this->setError(nl2br($db->getErrorMsg()));
                                        return false;
                                }
                        }      

                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                        $table =& JTable::getInstance('Media', 'hwdMediaShareTable');
                        $table->load( $pk );

                        $properties = $table->getProperties(1);
                        $item = JArrayHelper::toObject($properties, 'JObject');

                        hwdMediaShareFactory::load('files');
                        hwdMediaShareFiles::getLocalStoragePath();
                        $hwdmsFiles = hwdMediaShareFiles::getInstance();
                        $files = $hwdmsFiles->getMediaFiles($item);

                        $fileArray = array();
                        foreach($files as $file)
                        {
                                $fileArray[] = (int) $file->id;
                        }                        

                        $fileModel = JModelAdmin::getInstance('File','hwdMediaShareModel');                        
                        if (!$fileModel->delete($fileArray))
                        {
                                JFactory::getApplication()->enqueueMessage( $fileModel->getError() );
                        }
                }

                if (!parent::delete($pks))
                {
			return false;
		}
                
		// Clear the component's cache
		$this->cleanCache();

		return true;
	}
}
