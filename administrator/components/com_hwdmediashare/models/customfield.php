<?php
/**
 * @version    SVN $Id: customfield.php 285 2012-03-29 21:15:33Z dhorsfall $
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
class hwdMediaShareModelCustomField extends JModelAdmin
{
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
		return JFactory::getUser()->authorise('core.edit', 'com_hwdmediashare.customfield.'.((int) isset($data[$key]) ? $data[$key] : 0)) or parent::allowEdit($data, $key);
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
	public function getTable($type = 'CustomField', $prefix = 'hwdMediaShareTable', $config = array())
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
		$form = $this->loadForm('com_hwdmediashare.customfield', 'customfield', array('control' => 'jform', 'load_data' => $loadData));
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
		return 'administrator/components/com_hwdmediashare/models/forms/customfield.js';
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
		$data = JFactory::getApplication()->getUserState('com_hwdmediashare.edit.customfield.data', array());
		if (empty($data))
		{
			$data = $this->getItem();
		}
		return $data;
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

		if (!isset($value['element_type']) || !isset($value['searchable']) || !isset($value['visible']) || !isset($value['required']))
                {
			$this->setError(JText::_('JGLOBAL_ERROR_INSUFFICIENT_BATCH_INFORMATION'));
                        return false;
		}
                
		// Access checks.
		foreach ($pks as $i => $id)
		{
                        $data = array();
                        $data['id'] = $id;
                        !empty($value['element_type']) ? $data['element_type'] = $value['element_type'] : null;
                        !empty($value['searchable']) ? $data['searchable'] = $value['searchable'] : null;
                        !empty($value['visible']) ? $data['visible'] = $value['visible'] : null;
                        !empty($value['required']) ? $data['required'] = $value['required'] : null;

                        $value['searchable'] == 0 ? $data['searchable'] = 0 : null;
                        $value['visible'] == 0 ? $data['visible'] = 0 : null;
                        $value['required'] == 0 ? $data['required'] = 0 : null;

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
                        
                        // Delete records from field values
                        $queries[] = "
                            DELETE
                                FROM ".$db->quoteName('#__hwdms_fields_values')."
                                WHERE ".$db->quoteName('field_id')." = ".$db->quote($pk)."
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
