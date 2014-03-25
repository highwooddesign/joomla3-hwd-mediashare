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

class hwdMediaShareModelGroupMediaItem extends JModelAdmin
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
	public function getTable($name = 'LinkedGroups', $prefix = 'hwdMediaShareTable', $config = array())
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
		$form = $this->loadForm('com_hwdmediashare.group', 'group', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to unlink one or more media items with a group.
	 *
	 * @param   array    $pks         A list of the primary keys to change.
	 * @param   integer  $groupId  The value of the group key to associate with.
	 *
	 * @return  boolean  True on success.
	 */
        public function unlink($pks, $groupId = null)
        {
		// Initialiase variables.
                $db = JFactory::getDbo();
		$table = $this->getTable();    
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                
		// Sanitize the ids.
		$pks = (array) $pks;
		JArrayHelper::toInteger($pks);

		// Iterate through the items to process each one.
		foreach ($pks as $i => $pk)
		{
                        $query = $db->getQuery(true)
                                ->select('id')
                                ->from('#__hwdms_group_map')
                                ->where('group_id = ' . $db->quote($groupId))
                                ->where('media_id = ' . $db->quote($pk));

                        $db->setQuery($query);
                        try
                        {
                                $rows = $db->loadColumn();
                        }
                        catch (RuntimeException $e)
                        {
                                $this->setError($e->getMessage());
                                return false;                            
                        }

                        // Iterate the items to delete each one.
                        foreach ($rows as $x => $row)
                        {
                                if ($table->load($row))
                                {
                                        if ($utilities->authoriseGroupAction('unlink', $groupId, $pk))
                                        {
                                                if (!$table->delete($row))
                                                {
                                                        $this->setError($table->getError());
                                                        return false;
                                                }
                                        }
                                        else
                                        {
                                                // Prune items that you can't change.
                                                unset($rows[$x]);
                                                $error = $this->getError();

                                                if ($error)
                                                {
                                                        JLog::add($error, JLog::WARNING, 'jerror');
                                                        return false;
                                                }
                                                else
                                                {
                                                        JLog::add(JText::_('COM_HWDMS_ERROR_ACTION_NOT_PERMITTED'), JLog::WARNING, 'jerror');
                                                        return false;
                                                }
                                        }
                                }
                                else
                                {
                                        $this->setError($table->getError());
                                        return false;
                                }
                        }
                        
                        // Reorder this group
                        $table->reorder(' group_id = '.$pk.' ');
		}

		// Clear the component's cache
		$this->cleanCache();
             
		return true;
        }

	/**
	 * Method to link one or more media items with a group.
	 *
	 * @param   array    $pks         A list of the primary keys to change.
	 * @param   integer  $groupId  The value of the group key to associate with.
	 *
	 * @return  boolean  True on success.
	 */
	public function link($pks, $groupId = null)
	{
		// Initialiase variables.
                $db = JFactory::getDbo();
		$user = JFactory::getUser();
                $date = JFactory::getDate();                
		$table = $this->getTable();    
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                
		// Sanitize the ids.
		$pks = (array) $pks;
		JArrayHelper::toInteger($pks);

		// Iterate through the items to process each one.
		foreach ($pks as $i => $pk)
		{
			$table->reset();

                        if (!$utilities->authoriseGroupAction('link', $groupId, $pk))
                        {
                                // Prune items that you can't change.
                                unset($pks[$x]);
                                $error = $this->getError();

                                if ($error)
                                {
                                        JLog::add($error, JLog::WARNING, 'jerror');
                                        return false;
                                }
                                else
                                {
                                        JLog::add(JText::_('COM_HWDMS_ERROR_ACTION_NOT_PERMITTED'), JLog::WARNING, 'jerror');
                                        return false;
                                }
                        }
                        
                        // Create an object to bind to the database
                        $object = new StdClass;
                        $object->group_id = (int) $groupId;
                        $object->media_id = (int) $pk;
                        $object->created_user_id = (int) $user->id;
                        $object->created = $date->format('Y-m-d H:i:s');

                        // Attempt to change the state of the records.
                        if (!$table->save($object))
                        {
                                $this->setError($table->getError());
                                return false;
                        }

                        // Reorder this group
                        $table->reorder(' group_id = '.$groupId.' ');                        
		}

		// Clear the component's cache
		$this->cleanCache();

                return true;
	}
       
	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param	object	A record object.
	 *
	 * @return	array	An array of conditions to add to add to ordering queries.
	 */
	protected function getReorderConditions($table)
	{
		$condition = array();
		$condition[] = 'group_id = '.(int) $table->group_id;
                return $condition;
	}      
}
