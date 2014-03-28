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

class hwdMediaShareModelAlbum extends JModelAdmin
{
	/**
	 * The element type to use with model methods.
	 * @var    integer
	 */
	public $elementType = 2;
        
	/**
	 * Method to get a single record.
	 *
	 * @param   integer	The id of the primary key.
         * 
	 * @return  mixed  Object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
                if ($item = parent::getItem($pk))
                {
                        // Add the tags.
                        $item->tags = new JHelperTags;
                        $item->tags->getTagIds($item->id, 'com_hwdmediashare.album');
                        
                        // Add the custom fields.
                        hwdMediaShareFactory::load('customfields');
                        $cf = hwdMediaShareCustomFields::getInstance();
                        $cf->elementType = 2;
                        $item->customfields = $cf->get($item);
                        
                        // Add the number of media in the album.
                        $item->nummedia = $this->getMediaCount($item);
                        
                        // Add the album thumbnail.
                        $item->thumbnail = $this->getThumbnail($item);
                }

		return $item;
	}

	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 *
	 * @return  JTable  A JTable object
	 */
	public function getTable($name = 'Album', $prefix = 'hwdMediaShareTable', $config = array())
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
		$form = $this->loadForm('com_hwdmediashare.album', 'album', array('control' => 'jform', 'load_data' => $loadData));

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
		$data = JFactory::getApplication()->getUserState('com_hwdmediashare.edit.album.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

        /**
	 * Method to get the thumbnail for the album
	 * @return  void
	 */
	public function getThumbnail($item)
	{
                return hwdMediaShareFactory::getElementThumbnail($item);
	}
        
        /**
	 * Method to count the number of media in the album.
	 * @return  void
	 */
	public function getMediaCount($item)
	{
                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                        ->select('COUNT(*)')
                        ->from('#__hwdms_album_map')
                        ->where('album_id = ' . $db->quote($item->id));
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
                return $count;
	}
        
	/**
	 * Method to toggle the approval status of one or more records.
	 *
	 * @param   array    $pks   An array of record primary keys.
	 * @param   integer  $value The value to toggle to.
	 *
	 * @return  boolean  True on success.
	 */
	public function approve($pks, $value = 0)
	{
		// Initialiase variables.
                $user = JFactory::getUser();
                
		// Sanitize the ids.
		$pks = (array) $pks;
		JArrayHelper::toInteger($pks);

		// Access checks.
		foreach ($pks as $i => $id)
		{
			if (!$user->authorise('core.edit.state', 'com_hwdmediashare.album.'. (int) $id))
			{
				// Prune items that the user can't change.
				unset($pks[$i]);
				JError::raiseNotice(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
			}
		}
                
		if (empty($pks))
		{
			$this->setError(JText::_('JGLOBAL_NO_ITEM_SELECTED'));
			return false;
		}

		try
		{
			$db = $this->getDbo();
			$query = $db->getQuery(true)
                                    ->update($db->quoteName('#__hwdms_albums'))
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

		// Clear the component's cache
		$this->cleanCache();

		return true;
	}
        
	/**
	 * Method to toggle the featured value of one or more records.
	 *
	 * @param   array    $pks   An array of record primary keys.
	 * @param   integer  $value The value to toggle to.
	 *
	 * @return  boolean  True on success.
	 */
	public function feature($pks, $value = 0)
	{
		// Initialiase variables.
                $user = JFactory::getUser();
                            
		// Sanitize the ids.
		$pks = (array) $pks;
		JArrayHelper::toInteger($pks);

		// Access checks.
		foreach ($pks as $i => $id)
		{
			if (!$user->authorise('core.edit.state', 'com_hwdmediashare.album.'. (int) $id))
			{
				// Prune items that the user can't change.
				unset($pks[$i]);
				JError::raiseNotice(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
			}
		}
                
		if (empty($pks))
		{
			$this->setError(JText::_('JGLOBAL_NO_ITEM_SELECTED'));
			return false;
		}

		try
		{
			$db = $this->getDbo();
			$query = $db->getQuery(true)
                                    ->update($db->quoteName('#__hwdms_albums'))
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

		// Clear the component's cache
		$this->cleanCache();

		return true;
	}
        
	/**
	 * Method to delete one or more records. Overload to remove any
         * associated data.
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
                        // Delete records from activities
                        $queries[] = $db->getQuery(true)
                                   ->delete('#__hwdms_activities')
                                   ->where('element_type = ' . $db->quote(2))
                                   ->where('element_id = ' . $db->quote($pk));

                        // Delete records from album map
                        $queries[] = $db->getQuery(true)
                                   ->delete('#__hwdms_album_map')
                                   ->where('album_id = ' . $db->quote($pk));

                        // Delete records from field values
                        $queries[] = $db->getQuery(true)
                                   ->delete('#__hwdms_fields_values')
                                   ->where('element_type = ' . $db->quote(2))
                                   ->where('element_id = ' . $db->quote($pk));

                        // Delete records from likes
                        $queries[] = $db->getQuery(true)
                                   ->delete('#__hwdms_likes')
                                   ->where('element_type = ' . $db->quote(2))
                                   ->where('element_id = ' . $db->quote($pk));

                        // Delete records from reports
                        $queries[] = $db->getQuery(true)
                                   ->delete('#__hwdms_reports')
                                   ->where('element_type = ' . $db->quote(2))
                                   ->where('element_id = ' . $db->quote($pk));    
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
