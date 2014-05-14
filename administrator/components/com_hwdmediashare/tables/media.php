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

// Import Joomla table library
jimport('joomla.database.table');

class hwdMediaShareTableMedia extends JTable
{
	/**
	 * Constructor.
	 * @return	void
	 */
	function __construct($db)
	{             
		parent::__construct('#__hwdms_media', 'id', $db);
                JObserverMapper::addObserverClassToClass('JTableObserverTags', 'hwdMediaShareTableMedia', array('typeAlias' => 'com_hwdmediashare.media'));                
	}
        
	/**
	 * Overloaded bind function
	 *
	 * @param   array  $array   Named array to bind
	 * @param   mixed  $ignore  An optional array or space separated list of properties to ignore while binding.
	 *
	 * @return  mixed  Null if operation was satisfactory, otherwise returns an error
	 */
	public function bind($array, $ignore = '')
	{
		// Convert the params fields to a string.
                if (isset($array['params']) && is_array($array['params']))
		{
                        // Get the password from the data array and unset
                        $newPassword = $array['params']['password1'];
                        unset($array['params']['password1']);

			$registry = new JRegistry;
			$registry->loadArray($array['params']);

                        // We don't want to loose the existing password! So we check if a new one has been passed, then retain the old one if empty
                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                        $table = JTable::getInstance('Media', 'hwdMediaShareTable');
                        $table->load($array['id']);
                        $properties = $table->getProperties(1);
                        $row = JArrayHelper::toObject($properties, 'JObject');
                        if (empty($newPassword))
                        {                                
                                $password = $row->params->get('password');                                
                                if (!empty($password)) $registry->set('password', $password);
                        }
                        else
                        {
                                $password = md5($row->key . $newPassword);
                                if (!empty($password)) 
                                {
                                        $registry->set('password', $password);
                                        $registry->set('password', $password);
                                }
                        }
                        
			$array['params'] = (string) $registry;
		}
                // Bind the rules. 
		if (isset($array['rules']) && is_array($array['rules'])) 
                { 
			$rules = new JRules($array['rules']); 
			$this->setRules($rules); 
		}
		return parent::bind($array, $ignore);
	}

	/**
	 * Overloaded load function
	 *
	 * @param       int     $pk     primary key
	 * @param       boolean $reset  reset data
         * 
	 * @return      boolean
	 */
	public function load($pk = null, $reset = true) 
	{
		if (parent::load($pk, $reset)) 
		{
                        // Convert the params string to an array.
                        if (property_exists($this, 'params'))
                        {
                                $registry = new JRegistry;
                                $registry->loadString($this->params);
                                $this->params = $registry;
                        }                    
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Overload store method
	 *
	 * @param   boolean   $updateNulls   Toggle whether null values should be updated.
         * 
	 * @return  boolean   True on success, false on failure.
	 */
	public function store($updateNulls = false)
	{
		// Initialise variables.
                $app = JFactory::getApplication();
                $date = JFactory::getDate();
                $user = JFactory::getUser();
                $isNew = false;

                // Load HWD config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
		if ($this->id)
		{
			// Existing item, so set modified details.
			$this->modified		= $date->toSql();
			$this->modified_user_id	= $user->get('id');

                        // Only allow users with permission to edit states.
                        if (!$user->authorise('core.edit.state', 'com_hwdmediashare.album.'. (int) $this->id))
                        {
                                unset($this->published);
                                unset($this->status);
                                unset($this->featured);
                                unset($this->access);
                        }                          
		}
		else
		{
			// New item
                        $isNew = true;
                        
                        // Set a unique key
                        if (empty($this->key))
                        {
                                hwdMediaShareFactory::load('utilities');
                                $this->key = hwdMediaShareUtilities::generateKey();
                                if (hwdMediaShareUtilities::keyExists($this->key))
                                {
                                        $this->setError(JText::_('COM_HWDMS_KEY_EXISTS'));
                                        return false;
                                }
                        }

                        // Only allow users with permission to edit states.
                        if (!$user->authorise('core.edit.state', 'com_hwdmediashare'))
                        {
                                $this->published = 1;
                                $this->featured = 1;
                                $this->access = 1;
                        } 

                        // Set approval status
                        $this->status = (!$app->isAdmin() && $config->get('approve_new_playlists') == 1) ? 2 : 1;

                        // The created and created_by fields can be set by the user,
			// so we don't touch either of these if they are set.
			if (!(int) $this->created)
			{
				$this->created = $date->toSql();
			}
			if (empty($this->created_user_id))
			{
				$this->created_user_id = $user->get('id');
			}                      
		}

		// Set publish_up to null date if not set
		if (!$this->publish_up)
		{
			$this->publish_up = $this->_db->getNullDate();
		}

		// Set publish_down to null date if not set
		if (!$this->publish_down)
		{
			$this->publish_down = $this->_db->getNullDate();
		}

		// Verify that the alias is unique
		$table = JTable::getInstance('Playlist', 'hwdMediaShareTable');

		if ($table->load(array('alias' => $this->alias)) && ($table->id != $this->id || $this->id == 0))
		{
			$this->setError(JText::_('COM_HWDMS_ERROR_UNIQUE_ALIAS'));
			return false;
		}

                $return = parent::store($updateNulls);
		if ($return) 
                {
                        // Perform a few post-store tasks

                        // Get the upload data
                        hwdMediaShareFactory::load('upload');
                        $data = hwdMediaShareUpload::getProcessedUploadData();                
                        
                        // Add category data
                        if (isset($data['catid']))
                        {
                                hwdMediaShareFactory::load('category');
                                $catid = (array) $data['catid'];
                                // Make sure the item ids are integers
                                jimport('joomla.utilities.arrayhelper');
                                JArrayHelper::toInteger($catid);
                                $object = new StdClass;
                                $object->elementId = $this->id;
                                $object->categoryId = $catid;
                                $HWDcategory = hwdMediaShareCategory::getInstance();
                                $HWDcategory->elementType = 1;
                                $HWDcategory->save($object);
                        }

                        // Add custom field data
                        hwdMediaShareFactory::load('customfields');                
                        $object = new StdClass;
                        $object->elementId = $this->id;
                        $HWDcustomfields = hwdMediaShareCustomFields::getInstance();
                        $HWDcustomfields->elementType = 1;
                        $HWDcustomfields->save($object);

                        // Add thumbnail
                        hwdMediaShareFactory::load('upload');
                        $object = new StdClass;
                        $object->elementType = 1;
                        $object->elementId = $this->id;
                        $object->remove = (isset($data['remove_thumbnail']) ? true : false);
                        $object->thumbnail_remote = (isset($data['thumbnail_remote']) ? $data['thumbnail_remote'] : null);
                        $HWDupload = hwdMediaShareUpload::getInstance();
                        $HWDupload->processThumbnail($object);
                        
                        // If new and approved, trigger event
                        if ($isNew && $this->status == 1)
                        {                            
                                $properties = $this->getProperties(1);
                                $row = JArrayHelper::toObject($properties, 'JObject');                                
                                hwdMediaShareFactory::load('events');
                                $events = hwdMediaShareEvents::getInstance();
                                $events->triggerEvent('onAfterMediaAdd', $row);
                                
                                hwdMediaShareUpload::assignAssociations($row);
                        }    

                        // Send system notifications
                        if ($isNew && $config->get('notify_new_media') == 1) 
                        {
                                if($row->status == 2){
                                        ob_start();
                                        require(JPATH_SITE . '/components/com_hwdmediashare/libraries/emails/newmedia_pending.php');
                                        $body = ob_get_contents();
                                        ob_end_clean();
                                }
                                else{
                                        ob_start();
                                        require(JPATH_SITE . '/components/com_hwdmediashare/libraries/emails/newmedia.php');
                                        $body = ob_get_contents();
                                        ob_end_clean();
                                }
                                hwdMediaShareFactory::load('utilities');
                                $utilities = hwdMediaShareUtilities::getInstance();
                                $utilities->sendSystemEmail(JText::_('COM_HWDMS_EMAIL_SUBJECT_NEW_MEDIA'), $body);
                        }  
                }        
                
		return $return;
	}
        
	/**
	 * Overloaded check method to ensure data integrity.
	 *
	 * @return  boolean  True on success.
	 */
	public function check()
	{
 		// Check for valid name
		if (trim($this->title) == '')
		{
			$this->setError(JText::_('COM_HWDMS_ERROR_SAVE_NO_TITLE'));
			return false;
		}
                
		// Check for valid alias
		if (empty($this->alias))
		{
			$this->alias = $this->title;
		}
		$this->alias = JApplication::stringURLSafe($this->alias);
		if (trim(str_replace('-', '', $this->alias)) == '')
		{
			$this->alias = JFactory::getDate()->format("Y-m-d-H-i-s");
		}

		// Check the publish down date is not earlier than publish up.
		if ($this->publish_down > $this->_db->getNullDate() && $this->publish_down < $this->publish_up)
		{
			$this->setError(JText::_('JGLOBAL_START_PUBLISH_AFTER_FINISH'));
			return false;
		}

		return true;
	}
        
	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form `table_name.id`
	 * where id is the value of the primary key of the table.
	 *
	 * @return	string
	 */
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		return 'com_hwdmediashare.media.'.(int) $this->$k;
	}

	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return	string
	 */
	protected function _getAssetTitle()
	{
		return $this->title;
	}

	/**
	 * Get the parent asset id for the record
	 *
	 * @return	int
	 */
	protected function _getAssetParentId(JTable $table = null, $id = null)
	{
		$asset = JTable::getInstance('Asset');
		$asset->loadByName('com_hwdmediashare');
		return $asset->id;
	}
        
	/**
	 * Method to increment the likes/dislikes for a row if the necessary property/field exists.
	 *
	 * @param   mixed    $pk     An optional primary key value to increment. If not set the instance property value is used.
	 * @param   integer  $value  The value of the property to increment.
         * 
	 * @return  boolean  True on success.
	 */
	public function like($pk = null, $value = 1)
	{
		$values	= array(1 => 'likes', 0 => 'dislikes');
		$property = JArrayHelper::getValue($values, $value, 'like', 'word');

		// If there is no hits field, just return true.
		if (!property_exists($this, $property))
		{
			return true;
		}

		if (is_null($pk))
		{
			$pk = array();

			foreach ($this->_tbl_keys AS $key)
			{
				$pk[$key] = $this->$key;
			}
		}
		elseif (!is_array($pk))
		{
			$pk = array($this->_tbl_key => $pk);
		}

		foreach ($this->_tbl_keys AS $key)
		{
			$pk[$key] = is_null($pk[$key]) ? $this->$key : $pk[$key];

			if ($pk[$key] === null)
			{
				throw new UnexpectedValueException('Null primary key not allowed.');
			}
		}

		// Check the row in by primary key.
		$query = $this->_db->getQuery(true)
			->update($this->_tbl)
			->set($this->_db->quoteName($property) . ' = (' . $this->_db->quoteName($property) . ' + 1)');
		$this->appendPrimaryKeys($query, $pk);
		$this->_db->setQuery($query);
		$this->_db->execute();

		// Set table values in the object.
		$this->hits++;

		return true;
	} 

	/**
	 * Method to update the viewed date for a row if the necessary property/field exists.
	 *
	 * @param   mixed    $pk     An optional primary key value to increment. If not set the instance property value is used.
	 * @param   integer  $value  The value of the property to increment.
         * 
	 * @return  boolean  True on success.
	 */
	public function view($pk = null)
	{
                // Initialise variables.
                $date = JFactory::getDate();

		// The property we are updating.
                $property = 'viewed';

		// If there is no hits field, just return true.
		if (!property_exists($this, $property))
		{
			return true;
		}

		if (is_null($pk))
		{
			$pk = array();

			foreach ($this->_tbl_keys AS $key)
			{
				$pk[$key] = $this->$key;
			}
		}
		elseif (!is_array($pk))
		{
			$pk = array($this->_tbl_key => $pk);
		}

		foreach ($this->_tbl_keys AS $key)
		{
			$pk[$key] = is_null($pk[$key]) ? $this->$key : $pk[$key];

			if ($pk[$key] === null)
			{
				throw new UnexpectedValueException('Null primary key not allowed.');
			}
		}

		// Check the row in by primary key.
		$query = $this->_db->getQuery(true)
			->update($this->_tbl)
			->set($this->_db->quoteName($property) . ' = ' . $this->_db->quote($date->toSql()));
		$this->appendPrimaryKeys($query, $pk);
		$this->_db->setQuery($query);
		$this->_db->execute();

		// Set table values in the object.
		$this->hits++;

		return true;
	}         
}
