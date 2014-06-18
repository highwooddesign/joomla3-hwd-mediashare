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
	 * Method to get a table object, and load it if necessary.
	 *
	 * @access  public
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 * @return  JTable  A JTable object
	 */
	public function getTable($name = 'FileExtension', $prefix = 'hwdMediaShareTable', $config = array())
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
	 * @access  protected
         * @return  mixed       The data for the form.
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
	 * Method to delete one or more records. Overload to check if 
         * gallery still contains media with the extension being deleted.
	 *
         * @access  public
	 * @param   array   $pks    An array of record primary keys.
	 * @return  boolean True if successful, false if an error occurs.
	 */
	public function delete($pks)
	{
		// Initialise variables.
                $user = JFactory::getUser();
		$db = JFactory::getDBO();
        
		// Sanitize the ids.
		$pks = (array) $pks;
		JArrayHelper::toInteger($pks);
                
		// Iterate the items to check permission for delete.
		foreach ($pks as $i => $pk)
		{
			if (!$user->authorise('core.delete', 'com_hwdmediashare'))
			{
				// Prune items that the user can't change.
				unset($pks[$i]);
				JError::raiseNotice(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
			}
                        
                        $query = $db->getQuery(true)
                                ->select('COUNT(*)')
                                ->from('#__hwdms_media')
                                ->where('ext_id = ' . $db->quote($pk));
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
     
                        if ($count > 0)
                        {
				// Prune items that the user can't change.
				unset($pks[$i]);
				JError::raiseNotice(403, JText::_('COM_HWDMS_ERROR_CAN_NOT_REMOVE_EXT_EXISTING_MEDIA_IN_GALLERY'));
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
