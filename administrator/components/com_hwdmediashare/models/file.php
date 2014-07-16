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

class hwdMediaShareModelFile extends JModelAdmin
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
	public function getTable($name = 'File', $prefix = 'hwdMediaShareTable', $config = array())
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
		$form = $this->loadForm('com_hwdmediashare.file', 'file', array('control' => 'jform', 'load_data' => $loadData));

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
		$data = JFactory::getApplication()->getUserState('com_hwdmediashare.edit.file.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}
        
	/**
	 * Method to delete one or more records. Overload to remove the
         * associated file.
	 *
         * @access  public
	 * @param   array   $pks    An array of record primary keys.
	 * @return  boolean True if successful, false if an error occurs.
	 * @note    $pks is passed by reference only because JModelAdmin parent method does, and we need to keep this declaration compatible.
	 */
	public function delete(&$pks)
	{
		// Initialise variables.
                $user = JFactory::getUser();
		$db = JFactory::getDBO();
                
                // Import Joomla libraries.
                jimport('joomla.filesystem.file');

                // Import HWD libraries.
                hwdMediaShareFactory::load('files');
                        
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
                        
                        $table = JTable::getInstance('File', 'hwdMediaShareTable');
                        $table->load($pk);
                        $properties = $table->getProperties(1);
                        $file = JArrayHelper::toObject($properties, 'JObject');

                        switch ($file->element_type)
                        {
                                case 1:
                                    // Media
                                    $element = JTable::getInstance('Media', 'hwdMediaShareTable');
                                    break;
                                case 2:
                                    // Album
                                    $element = JTable::getInstance('Album', 'hwdMediaShareTable');
                                    break;
                                case 3:
                                    // Group
                                    $element = JTable::getInstance('Group', 'hwdMediaShareTable');
                                    break;
                                case 4:
                                    // Playlist
                                    $element = JTable::getInstance('Playlist', 'hwdMediaShareTable');
                                    break;
                                case 5:
                                    // Channel
                                    $element = JTable::getInstance('UserChannel', 'hwdMediaShareTable');
                                    break;
                                case 6:
                                    // Category
                                    $element = JTable::getInstance('Category', 'hwdMediaShareTable');
                                    break;
                        }
                        
                        if (!is_object($element))
                        {
				// Prune items that we can't load.
				unset($pks[$i]);
                                continue;
                        }
                        
                        $element->load($file->element_id);
                        $properties = $table->getProperties(1);
                        $item = JArrayHelper::toObject($properties, 'JObject');
                        
                        hwdMediaShareFiles::getLocalStoragePath();

                        $foldersSource = hwdMediaShareFiles::getFolders($item->key);
                        $filenameSource = hwdMediaShareFiles::getFilename($item->key, $file->file_type);
                        $extSource = hwdMediaShareFiles::getExtension($item, $file->file_type);
                        $pathSource = hwdMediaShareFiles::getPath($foldersSource, $filenameSource, $extSource);
                        if (JFile::exists($pathSource)) 
                        {
                                if(!JFile::delete($pathSource))
                                {
                                        // Prune items to which we can't remove a media file.
                                        unset($pks[$i]);
                                        JError::raiseNotice(403, JText::_('COM_HWDMS_UNABLE_TO_REMOVE_FILE_FROM_DISK'));
                                }
                        }
                        
		}
                
                if (!parent::delete($pks))
                {
			return false;
		}

		// Clear the component's cache.
		$this->cleanCache();

		return true;
	}
}
