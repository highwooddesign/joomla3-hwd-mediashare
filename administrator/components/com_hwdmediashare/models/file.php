<?php
/**
 * @version    SVN $Id: file.php 751 2012-10-31 17:13:13Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      16-Mar-2012 18:17:41
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * hwdMediaShare Model
 */
class hwdMediaShareModelFile extends JModelAdmin
{
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
                }

		return $item;
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
	public function getTable($type = 'File', $prefix = 'hwdMediaShareTable', $config = array())
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
		$form = $this->loadForm('com_hwdmediashare.file', 'file', array('control' => 'jform', 'load_data' => $loadData));
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
		return 'administrator/components/com_hwdmediashare/models/forms/file.js';
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
		$data = JFactory::getApplication()->getUserState('com_hwdmediashare.edit.file.data', array());
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
                
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                empty($data['id']) ? $data['key'] = hwdMediaShareFactory::generateKey() : null;

                // Alter the title for save as copy
                $data['modified'] = $date->format('Y-m-d H:i:s');
                $data['modified_user_id'] = $user->id;

                empty($data['created_user_id']) ? $data['created_user_id'] = $user->id : null;
                empty($data['created']) ? $data['created'] = $date->format('Y-m-d H:i:s') : null;
                empty($data['publish_up']) ? $data['publish_up'] = $date->format('Y-m-d H:i:s') : null;
                empty($data['publish_down']) ? $data['publish_down'] = "0000-00-00 00:00:00" : null;
                empty($data['alias']) ? $data['alias'] = JFilterOutput::stringURLSafe($_REQUEST['jform']['title']) : $data['alias'] = JFilterOutput::stringURLSafe($data['alias']);

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
                        $params->tags = $data['tags'];
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
                // Delete test file is already exists
                jimport( 'joomla.filesystem.file' );

		// Iterate the items to delete each one.
		foreach ($pks as $i => $pk)
		{
                        $table =& JTable::getInstance('File', 'hwdMediaShareTable');
                        $table->load( $pk );
                        $properties = $table->getProperties(1);
                        $file = JArrayHelper::toObject($properties, 'JObject');

                        switch ($file->element_type)
                        {
                                case 1:
                                    // Media
                                    $table =& JTable::getInstance('Media', 'hwdMediaShareTable');
                                    break;
                                case 2:
                                    // Album
                                    $table =& JTable::getInstance('Album', 'hwdMediaShareTable');
                                    break;
                                case 3:
                                    // Group
                                    $table =& JTable::getInstance('Group', 'hwdMediaShareTable');
                                    break;
                                case 4:
                                    // Playlist
                                    $table =& JTable::getInstance('Playlist', 'hwdMediaShareTable');
                                    break;
                                case 5:
                                    // Channel
                                    $table =& JTable::getInstance('UserChannel', 'hwdMediaShareTable');
                                    break;
                                case 6:
                                    // Category
                                    $table =& JTable::getInstance('Category', 'hwdMediaShareTable');
                                    break;
                        }
                        
                        if (!is_object($table)) {
                            continue;
                        }
                        
                        $table->load( $file->element_id );
                        $properties = $table->getProperties(1);
                        $item = JArrayHelper::toObject($properties, 'JObject');
                        
                        hwdMediaShareFactory::load('files');
                        hwdMediaShareFiles::getLocalStoragePath();

                        $foldersSource = hwdMediaShareFiles::getFolders($item->key);
                        $filenameSource = hwdMediaShareFiles::getFilename($item->key, $file->file_type);
                        $extSource = hwdMediaShareFiles::getExtension($item, $file->file_type);

                        $pathSource = hwdMediaShareFiles::getPath($foldersSource, $filenameSource, $extSource);
                                
                        jimport( 'joomla.filesystem.file' );
                        if (JFile::exists($pathSource)) JFile::delete($pathSource);
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
