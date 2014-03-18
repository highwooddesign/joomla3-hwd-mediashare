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

class hwdMediaShareModelExtension extends JModelAdmin
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
	public function getTable($name = 'FileExtension', $prefix = 'hwdMediaShareTable', $config = array())
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
		$form = $this->loadForm('com_hwdmediashare.extension', 'extension', array('control' => 'jform', 'load_data' => $loadData));

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
	 * @param	array   $data   The form data.
	 *
	 * @return	boolean	True on success.
	 */
	public function save($data)
	{
		// Initialise variables.
                $date = JFactory::getDate();
                $user = JFactory::getUser();

                // Set the modified details
                $data['modified'] = $date->format('Y-m-d H:i:s');
                $data['modified_user_id'] = $user->id;
                
                // Populate empty data
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
	 * Method to delete one or more records. Overload to check if 
         * gallery still contains media with the extension being deleted.
	 *
	 * @param   array  $pks  An array of record primary keys.
	 *
	 * @return  boolean  True if successful, false if an error occurs.
	 */
	public function delete($pks)
	{
		$db = JFactory::getDBO();
                $pks = (array) $pks;
                
		// Iterate the items to check for contents
		foreach ($pks as $i => $pk)
		{
                        $query = $db->getQuery(true)
                                ->select('COUNT(*)')
                                ->from('#__hwdms_media')
                                ->where('ext_id = ' . $db->quote($pk));
                        $db->setQuery($query);
                        try
                        {
                                $count = $db->loadResult();
                        }
                        catch (RuntimeException $e)
                        {
                                $this->setError($e->getMessage());
                                return false;                            
                        }
     
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
