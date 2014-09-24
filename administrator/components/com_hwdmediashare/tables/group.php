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

class hwdMediaShareTableGroup extends JTable
{
	/**
	 * Class constructor. Overridden to explicitly set the table and key fields.
	 *
	 * @access	public
	 * @param       JDatabaseDriver  $db     JDatabaseDriver object.
         * @return      void
	 */ 
	public function __construct($db)
	{
		parent::__construct('#__hwdms_groups', 'id', $db);
                JObserverMapper::addObserverClassToClass('JTableObserverTags', 'hwdMediaShareTableGroup', array('typeAlias' => 'com_hwdmediashare.group'));
	}

	/**
	 * Method to bind an associative array or object to the JTable instance.
	 *
         * @access  public
	 * @param   mixed   $src     An associative array or object to bind to the JTable instance.
	 * @param   mixed   $ignore  An optional array or space separated list of properties to ignore while binding.
	 * @return  boolean True on success.
	 * @link    http://docs.joomla.org/JTable/bind
	 * @throws  InvalidArgumentException
	 */
	public function bind($src, $ignore = '')
	{
		// Convert the params fields to a string.
                if (isset($src['params']) && is_array($src['params']))
		{
			$registry = new JRegistry;
			$registry->loadArray($src['params']);
			$src['params'] = (string) $registry;
		}
                
                // Bind the rules.
		if (isset($src['rules']) && is_array($src['rules'])) 
                { 
			$rules = new JRules($src['rules']); 
			$this->setRules($rules); 
		}
                
		return parent::bind($src, $ignore);
	}

	/**
	 * Method to store a row in the database from the JTable instance properties.
	 *
         * @access  public
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 * @return  boolean  True on success.
	 * @link    http://docs.joomla.org/JTable/store
	 */
	public function store($updateNulls = false)
	{
		// Initialise variables.
                $app = JFactory::getApplication();
                $date = JFactory::getDate();
                $user = JFactory::getUser();
                $isNew = false;

                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                // Load HWD utilities.
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                
                // Load cache object.
                $cache = JFactory::getCache('com_hwdmediashare');
                
		if ($this->id)
		{
			// Existing item, so set modified details.
			$this->modified		= $date->toSql();
			$this->modified_user_id	= $user->get('id'); 

                        // Only allow users with permission to edit states.
                        if (!$user->authorise('core.edit.state', 'com_hwdmediashare.group.'. (int) $this->id))
                        {
                                unset($this->published);
                                unset($this->status);
                                unset($this->featured);
                                unset($this->access);
                        }
                        
                        // Clean cache.
                        $cache->clean('com_hwdmediashare');             
		}
		else
		{
			// New item.
                        $isNew = true;
                        
                        // Set a unique key.
                        if (empty($this->key))
                        {                            
				if (!$this->key = $utilities->generateKey(3))
				{
					$this->setError($utilities->getError());
					return false;
				} 
                        }

                        // Set default values.
                        if (!isset($this->published)) $this->published = 1;
                        if (!isset($this->featured)) $this->featured = 0;
                        if (!isset($this->access)) $this->access = 1;
                        
                        // Only allow users with permission to edit states.
                        if (!$user->authorise('core.edit.state', 'com_hwdmediashare'))
                        {
                                $this->published = 1;
                                $this->featured = 0;
                                $this->access = 1;
                        } 
                        
                        // Set approval status.
                        $this->status = (!$app->isAdmin() && $config->get('approve_new_groups') == 1) ? 2 : 1;

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

		// Set publish_up to null date if not set.
		if (!$this->publish_up)
		{
			$this->publish_up = $this->_db->getNullDate();
		}

		// Set publish_down to null date if not set.
		if (!$this->publish_down)
		{
			$this->publish_down = $this->_db->getNullDate();
		}

		// Verify that the alias is unique.
		$table = JTable::getInstance('Group', 'hwdMediaShareTable');

		if ($table->load(array('alias' => $this->alias)) && ($table->id != $this->id || $this->id == 0))
		{
                        // Append alias to make unique.
			$this->alias .= JFactory::getDate()->format("Y-m-d-H-i-s");
                    
                        if ($table->load(array('alias' => $this->alias)) && ($table->id != $this->id || $this->id == 0))
                        {
                                $this->setError(JText::_('COM_HWDMS_ERROR_UNIQUE_ALIAS'));
                                return false;
                        }
		}

                $return = parent::store($updateNulls);
		if ($return) 
                {
                        /** Perform a few post-store tasks **/
                        $properties = $this->getProperties(1);
                        $group = JArrayHelper::toObject($properties, 'JObject'); 
                                
                        // Get data from the request.
                        hwdMediaShareFactory::load('upload');
                        $data = hwdMediaShareUpload::getProcessedUploadData(); 
                        
                        // Add custom field data.
                        hwdMediaShareFactory::load('customfields');                
                        $HWDcustomfields = hwdMediaShareCustomFields::getInstance();
                        $HWDcustomfields->elementType = 3;
                        $HWDcustomfields->save($group);
                        
                        // Add thumbnail.
                        hwdMediaShareFactory::load('upload');
                        $object = new StdClass;
                        $object->elementType = 3;
                        $object->elementId = $this->id;
                        $object->remove = (isset($data['remove_thumbnail']) ? true : false);
                        $object->thumbnail_remote = (isset($data['thumbnail_remote']) ? $data['thumbnail_remote'] : null);
                        $HWDupload = hwdMediaShareUpload::getInstance();
                        $HWDupload->processThumbnail($object);

                        // If new and approved then trigger onAfterGroupAdd event.
                        if ($isNew && $this->status == 1)
                        {                                                           
                                hwdMediaShareFactory::load('events');
                                $events = hwdMediaShareEvents::getInstance();
                                $events->triggerEvent('onAfterGroupAdd', $group);
                        }                        
                }        
                
		return $return;
	}
        
	/**
	 * Method to perform sanity checks on the JTable instance properties to ensure
	 * they are safe to store in the database.
	 *
         * @access  public
	 * @return  boolean  True if the instance is sane and able to be stored in the database.
	 * @link    http://docs.joomla.org/JTable/check
	 */
	public function check()
	{
		// Check for valid name.
		if (trim($this->title) == '')
		{
			$this->setError(JText::_('COM_HWDMS_ERROR_SAVE_NO_TITLE'));
			return false;
		}
                
		// Check for valid alias.
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
	 *
	 * @access  protected
	 * @return  string
	 */
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		return 'com_hwdmediashare.group.'.(int) $this->$k;
	}

	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @access  protected
	 * @return  string  The string to use as the title in the asset table.
	 * @link    http://docs.joomla.org/JTable/getAssetTitle
	 */
	protected function _getAssetTitle()
	{
		return $this->title;
	}

	/**
	 * Method to get the parent asset under which to register this one.
	 *
	 * @access  protected
	 * @param   JTable   $table  A JTable object for the asset parent.
	 * @param   integer  $id     Id to look up
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
	 * @access  public
	 * @param   mixed    $pk     An optional primary key value to increment. If not set the instance property value is used.
	 * @param   integer  $value  The value of the property to increment.
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
}
