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

class hwdMediaShareModelCustomField extends JModelAdmin
{
    	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 *
	 * @return  JTable  A JTable object
	 */
	public function getTable($name = 'CustomField', $prefix = 'hwdMediaShareTable', $config = array())
	{
		return JTable::getInstance($name, $prefix, $config);
	}
        
	/**
	 * Abstract method for getting the form from the model.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_hwdmediashare.customfield', 'customfield', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_hwdmediashare.edit.customfield.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

        /**
	 * Method to toggle the searchable status of one or more records.
	 *
	 * @param   array    $pks   An array of record primary keys.
	 * @param   integer  $value The value to toggle to.
	 *
	 * @return  boolean  True on success.
	 */
	public function searchable($pks, $value = 0)
	{
		// Sanitize the ids.
		$pks = (array) $pks;
		JArrayHelper::toInteger($pks);

		if (empty($pks))
		{
			$this->setError(JText::_('COM_HWDMS_NO_ITEM_SELECTED'));
			return false;
		}

		$table = $this->getTable();

		try
		{
			$db = $this->getDbo();
			$query = $db->getQuery(true)
                                    ->update($db->quoteName('#__hwdms_fields'))
                                    ->set('searchable = ' . $db->quote((int) $value))
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
	 * Method to toggle the visibility status of one or more records.
	 *
	 * @param   array    $pks   An array of record primary keys.
	 * @param   integer  $value The value to toggle to.
	 *
	 * @return  boolean  True on success.
	 */
	public function visible($pks, $value = 0)
	{
		// Sanitize the ids.
		$pks = (array) $pks;
		JArrayHelper::toInteger($pks);

		if (empty($pks))
		{
			$this->setError(JText::_('COM_HWDMS_NO_ITEM_SELECTED'));
			return false;
		}

		$table = $this->getTable();

		try
		{
			$db = $this->getDbo();
			$query = $db->getQuery(true)
                                    ->update($db->quoteName('#__hwdms_fields'))
                                    ->set('visible = ' . $db->quote((int) $value))
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
	 * Method to toggle the required status of one or more records.
	 *
	 * @param   array    $pks   An array of record primary keys.
	 * @param   integer  $value The value to toggle to.
	 *
	 * @return  boolean  True on success.
	 */
	public function required($pks, $value = 0)
	{
		// Sanitize the ids.
		$pks = (array) $pks;
		JArrayHelper::toInteger($pks);

		if (empty($pks))
		{
			$this->setError(JText::_('COM_HWDMS_NO_ITEM_SELECTED'));
			return false;
		}

		$table = $this->getTable();

		try
		{
			$db = $this->getDbo();
			$query = $db->getQuery(true)
                                    ->update($db->quoteName('#__hwdms_fields'))
                                    ->set('required = ' . $db->quote((int) $value))
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
	 * Method to delete one or more records. Overload to remove any
         * stored field value data.
	 *
	 * @param   array  $pks  An array of record primary keys.
	 *
	 * @return  boolean  True if successful, false if an error occurs.
	 */
	public function delete($pks)
	{
                if (!parent::delete($pks))
                {
			return false;
		}
                
		$db = JFactory::getDBO();
                $pks = (array) $pks;
                
                // Array holding all queries
                $queries = array();

		// Loop through keys and generate queries to execute.
		foreach ($pks as $i => $pk)
		{
                        // Delete associated field value data
                        $queries[] = $db->getQuery(true)
                                   ->delete('#__hwdms_fields_values')
                                   ->where('field_id = ' . $db->quote($pk));
		}

                // Execute the generated queries.
                foreach ($queries as $query)
                {
                        try
                        {
                                $db->setQuery($query);
                                $db->query();
                        }
                        catch (RuntimeException $e)
                        {
                                $this->setError($e->getMessage());
                                return false;                            
                        }
                }   

		// Clear the component's cache
		$this->cleanCache();

		return true;
	}
}
