<?php
/**
 * @version    SVN $Id: extension.php 493 2012-08-28 13:20:17Z dhorsfall $
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
class hwdMediaShareModelExtension extends JModelAdmin
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
		return JFactory::getUser()->authorise('core.edit', 'com_hwdmediashare.extension.'.((int) isset($data[$key]) ? $data[$key] : 0)) or parent::allowEdit($data, $key);
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
	public function getTable($type = 'FileExtension', $prefix = 'hwdMediaShareTable', $config = array())
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
		$form = $this->loadForm('com_hwdmediashare.extension', 'extension', array('control' => 'jform', 'load_data' => $loadData));
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
		return 'administrator/components/com_hwdmediashare/models/forms/extension.js';
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
		$data = JFactory::getApplication()->getUserState('com_hwdmediashare.edit.extension.data', array());
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
		// Initialise variables.
                $date =& JFactory::getDate();
                $user = & JFactory::getUser();
                $data = JRequest::getVar('jform', array(), 'post', 'array');

                // Alter the title for save as copy
                $data['modified'] = $date->format('Y-m-d H:i:s');
                $data['modified_user_id'] = $user->id;

                empty($data['created_user_id']) ? $data['created_user_id'] = $user->id : null;
                empty($data['created']) ? $data['created'] = $date->format('Y-m-d H:i:s') : null;
                empty($data['publish_up']) ? $data['publish_up'] = $date->format('Y-m-d H:i:s') : null;
                empty($data['publish_down']) ? $data['publish_down'] = "0000-00-00 00:00:00" : null;

		if (parent::save($data))
                {
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
	public function batch($pks, $value = array())
	{
		// Initialise variables.
		$user		= JFactory::getUser();
		$table		= $this->getTable();
		$pks		= (array) $pks;

		if (!isset($value['user']) || !isset($value['access']))
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
		$db =& JFactory::getDBO();
                $pks = (array) $pks;
                
		// Iterate the items to check for contents
		foreach ($pks as $i => $pk)
		{
                        $query = $db->getQuery(true);
                        $query->select('COUNT(*)');
                        $query->from('#__hwdms_media AS a');
                        $query->where('a.ext_id = '.$pk);
                        $db->setQuery($query);
                        $count = $db->loadResult();      
                        if ($count > 0)
                        {
                                $this->setError(JText::_('COM_HWDMS_ERROR_CAN_NOT_REMOVE_EXT_EXISTING_MEDIA_IN_GALLERY'));
                                return false;
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
