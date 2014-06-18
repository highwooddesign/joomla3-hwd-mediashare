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

class hwdMediaShareModelUser extends JModelAdmin
{        
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
                        $item->tags->getTagIds($item->id, 'com_hwdmediashare.user');
                        
                        // Add the custom fields.
                        hwdMediaShareFactory::load('customfields');
                        $HWDcustomfields = hwdMediaShareCustomFields::getInstance();
                        $HWDcustomfields->elementType = 5;
                        $item->customfields = $HWDcustomfields->get($item);

                        // Add the user thumbnail (avatar).
                        $item->thumbnail = $this->getThumbnail($item);

                        // Get HWD config.
                        $hwdms = hwdMediaShareFactory::getInstance();
                        $config = $hwdms->getConfig();

                        // Set title with value based on configuration.
                        $item->title = $config->get('author') == 0 ? JFactory::getUser($item->id)->name : JFactory::getUser($item->id)->username;
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
	public function getTable($name = 'UserChannel', $prefix = 'hwdMediaShareTable', $config = array())
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
		$form = $this->loadForm('com_hwdmediashare.user', 'user', array('control' => 'jform', 'load_data' => $loadData));

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
		$data = JFactory::getApplication()->getUserState('com_hwdmediashare.edit.user.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}
        
        /**
	 * Method to get the thumbnail for the user.
         * 
         * @access  public
         * @param   object  $item   The user object.
	 * @return  mixed   The thumnail location on success, false on failure.
	 */
	public function getThumbnail($item)
	{
                // Load the HWD downloads library.
                hwdMediaShareFactory::load('downloads');
                $HWDdownloads = hwdMediaShareDownloads::getInstance();
                $HWDdownloads->elementType = 5;
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
	 * Method to toggle the approval status of one or more records.
	 *
         * @access  public
	 * @param   array    $pks   An array of record primary keys.
	 * @param   integer  $value The value to toggle to.
	 * @return  boolean  True on success.
	 */
	public function approve($pks, $value = 0)
	{
		// Sanitize the ids.
		$pks = (array) $pks;
		JArrayHelper::toInteger($pks);

		if (empty($pks))
		{
			$this->setError(JText::_('JGLOBAL_NO_ITEM_SELECTED'));
			return false;
		}

		$table = $this->getTable();

		try
		{
			$db = $this->getDbo();
			$query = $db->getQuery(true)
                                    ->update($db->quoteName('#__hwdms_users'))
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
		// Sanitize the ids.
		$pks = (array) $pks;
		JArrayHelper::toInteger($pks);

		if (empty($pks))
		{
			$this->setError(JText::_('JGLOBAL_NO_ITEM_SELECTED'));
			return false;
		}

		$table = $this->getTable();

		try
		{
			$db = $this->getDbo();
			$query = $db->getQuery(true)
                                    ->update($db->quoteName('#__hwdms_users'))
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
	 * Method to delete one or more records. Overload to remove any
         * associated data.
	 *
         * @access  public
	 * @param   array   $pks    An array of record primary keys.
	 * @return  boolean True if successful, false if an error occurs.
	 * @note    $pks is passed by reference only because JModelAdmin parent method does, and we need to keep this declaration compatible.
	 */
	public function delete($pks)
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
                        // Delete records from activities
                        $queries[] = $db->getQuery(true)
                                        ->delete('#__hwdms_activities')
                                        ->where('element_type = ' . $db->quote(5))
                                        ->where('element_id = ' . $db->quote($pk));

                        // Delete records from field values
                        $queries[] = $db->getQuery(true)
                                        ->delete('#__hwdms_fields_values')
                                        ->where('element_type = ' . $db->quote(5))
                                        ->where('element_id = ' . $db->quote($pk));

                        // Delete records from likes
                        $queries[] = $db->getQuery(true)
                                        ->delete('#__hwdms_likes')
                                        ->where('element_type = ' . $db->quote(5))
                                        ->where('element_id = ' . $db->quote($pk));

                        // Delete records from reports
                        $queries[] = $db->getQuery(true)
                                        ->delete('#__hwdms_reports')
                                        ->where('element_type = ' . $db->quote(5))
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

		// Clear the component's cache.
		$this->cleanCache();

		return true;
	}        
}
