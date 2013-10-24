<?php
/**
 * @version    SVN $Id: playlist.php 1646 2013-08-16 09:13:42Z dhorsfall $
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
class hwdMediaShareModelPlaylist extends JModelAdmin
{
	var $elementType = 4;
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
                        hwdMediaShareFactory::load('tags');
                        $item->tags = hwdMediaShareTags::getInput($item);
                        hwdMediaShareFactory::load('customfields');
                        $item->customfields = hwdMediaShareCustomFields::get($item);

                        $item->mediacount = $this->getMediaCount($item);
                        $item->thumbnail = $this->getThumbnail($item);
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
		return JFactory::getUser()->authorise('core.edit', 'com_hwdmediashare.playlist.'.((int) isset($data[$key]) ? $data[$key] : 0)) or parent::allowEdit($data, $key);
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
	public function getTable($type = 'Playlist', $prefix = 'hwdMediaShareTable', $config = array())
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
		$form = $this->loadForm('com_hwdmediashare.playlist', 'playlist', array('control' => 'jform', 'load_data' => $loadData));
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
	public function getScript()
	{
		return 'administrator/components/com_hwdmediashare/models/forms/playlist.js';
	}
	/**
	 * Method to get the script that have to be included on the form
	 *
	 * @return string	Script files
	 */
	public function getThumbnail($item)
	{
                // Download links
                return hwdMediaShareFactory::getElementThumbnail($item);
	}
        /**
	 * Method to get the script that have to be included on the form
	 *
	 * @return string	Script files
	 */
	public function getMediaCount($item)
	{
                $db =& JFactory::getDBO();
                $query = "
                  SELECT COUNT(*)
                    FROM ".$db->quoteName('#__hwdms_playlist_map')."
                    WHERE ".$db->quoteName('playlist_id')." = ".$db->quote($item->id).";
                  ";
                $db->setQuery($query);
                return $db->loadResult();
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
		$data = JFactory::getApplication()->getUserState('com_hwdmediashare.edit.playlist.data', array());
		if (empty($data))
		{
			$data = $this->getItem();
		}
		return $data;
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
                
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
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

                if (!$app->isAdmin() && $config->get('approve_new_albums') == 1) 
                { 
                        $data['status'] = 2;
                }
                else
                {
                        $data['status'] = 1;
                }
                
                $form = parent::save($data);
		if ($form) 
                {
                        if (empty($data['id'])) 
                        {
                            $isNew = true;  
                        }
                        // Set data to current database object
                        !$app->isAdmin() ? $data['id'] = $this->getState('playlistform.id') : $data['id'] = $this->getState('playlist.id');

                        $params = new StdClass;
                        $params->elementType = 4;
                        $params->elementId = $data['id'];
                        $params->tags = $tags;
                        $params->key = $data['key'];
                        $params->remove = (isset($data['remove_thumbnail']) ? true : false);
                        
                        hwdMediaShareFactory::load('tags');
                        hwdMediaShareTags::save($params);

                        hwdMediaShareFactory::load('customfields');
                        hwdMediaShareCustomFields::save($params);

                        hwdMediaShareFactory::load('upload');
                        hwdMediaShareUpload::processThumbnail($params);

                        if ($isNew)
                        {
                                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                                $table =& JTable::getInstance('Playlist', 'hwdMediaShareTable');
                                $table->load( $data['id'] );
                                $properties = $table->getProperties(1);
                                $row = JArrayHelper::toObject($properties, 'JObject');
                                
                                hwdMediaShareFactory::load('events');
                                $events = hwdMediaShareEvents::getInstance();
                                $events->triggerEvent('onAfterPlaylistAdd', $row);
                        }
                        
                        return true;
		}                   
                return false;
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
                  UPDATE ".$db->quoteName('#__hwdms_playlists')."
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
                  UPDATE ".$db->quoteName('#__hwdms_playlists')."
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
	 * Method to assign user to a single record
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function delete(&$pks)
	{
                if (!parent::delete($pks))
                {
			return false;
		}

		$db =& JFactory::getDBO();
                $pks = (array) $pks;
                $query = array();
                
		// Iterate the items to delete each one.
		foreach ($pks as $i => $pk)
		{
                        $queries = array();
                        
                        // Delete records from activities
                        $queries[] = "
                            DELETE
                                FROM ".$db->quoteName('#__hwdms_activities')."
                                WHERE ".$db->quoteName('element_type')." = ".$db->quote(4)."
                                AND ".$db->quoteName('element_id')." = ".$db->quote($pk)."
                            ";
                        
                        // Delete records from field values
                        $queries[] = "
                            DELETE
                                FROM ".$db->quoteName('#__hwdms_fields_values')."
                                WHERE ".$db->quoteName('element_type')." = ".$db->quote(4)."
                                AND ".$db->quoteName('element_id')." = ".$db->quote($pk)."
                            ";

                        // Delete records from likes
                        $queries[] = "
                            DELETE
                                FROM ".$db->quoteName('#__hwdms_likes')."
                                WHERE ".$db->quoteName('element_type')." = ".$db->quote(4)."
                                AND ".$db->quoteName('element_id')." = ".$db->quote($pk)."
                            ";
                        
                        // Delete records from playlist map
                        $queries[] = "
                            DELETE
                                FROM ".$db->quoteName('#__hwdms_playlist_map')."
                                WHERE ".$db->quoteName('playlist_id')." = ".$db->quote($pk)."
                            ";
                        // @TODO: Reorder

                        // Delete records from reports
                        $queries[] = "
                            DELETE
                                FROM ".$db->quoteName('#__hwdms_reports')."
                                WHERE ".$db->quoteName('element_type')." = ".$db->quote(3)."
                                AND ".$db->quoteName('element_id')." = ".$db->quote($pk)."
                            ";
                        
                        // Delete records from tag map
                        $queries[] = "
                            DELETE
                                FROM ".$db->quoteName('#__hwdms_tag_map')."
                                WHERE ".$db->quoteName('element_type')." = ".$db->quote(3)."
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
		}

		// Clear the component's cache
		$this->cleanCache();

		return true;
	}
}
