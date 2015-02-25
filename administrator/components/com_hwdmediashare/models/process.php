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

class hwdMediaShareModelProcess extends JModelAdmin
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
	public function getTable($name = 'Process', $prefix = 'hwdMediaShareTable', $config = array())
	{
		return JTable::getInstance($name, $prefix, $config);
	}
        
	/**
	 * Abstract method for getting the form from the model.
	 *
	 * @access  public
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 * @return  mixed    A JForm object on success, false on failure
	 */
	public function getForm($data = array(), $loadData = true)
	{
	}

	/**
	 * Method to get a single item.
	 *
         * @access  public
	 * @param   integer  $pk  The id of the primary key.
	 * @return  mixed    Object on success, false on failure.
	 */
        public function getItem($pk = null)
        {
                // Initialise variables.
                $app = JFactory::getApplication();
                
                $processId = $app->input->get('id');

		try
		{
			$db = $this->getDbo();
                        $query = $db->getQuery(true)
                                ->select('a.id, a.process_type, a.attempts, a.checked_out, a.checked_out_time, m.title, ext.media_type')
                                ->from('#__hwdms_processes AS a')
                                ->join('LEFT', '`#__hwdms_media` AS m ON m.id = a.media_id')
                                ->join('LEFT', '`#__hwdms_ext` AS ext ON ext.id = m.ext_id')
                                ->where('a.id = ' . $db->quote((int) $processId));
			$db->setQuery($query);
			$item = $db->loadObject();
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());
			return false;
		}

                // Check process exists.
                if ($item == false)
                {
                        $this->setError(JText::_('COM_HWDMS_ERROR_PROCESS_NOT_FOUND'));
                        return false;
                }
                        
                return $item;
        }

	/**
	 * Method to get a list of items.
	 *
	 * @access  public
	 * @return  mixed   An array of data items on success, false on failure.
	 */
        public function getItems()
        {
                // Initialise variables.
                $app = JFactory::getApplication();
                
                $processId = $app->input->get('id');

		try
		{
			$db = $this->getDbo();
                        $query = $db->getQuery(true)
                                ->select('id, input, output, status, created')
                                ->from('#__hwdms_process_log')
                                ->where('process_id = ' . $db->quote((int) $processId))
                                ->order('created DESC');
			$db->setQuery($query);
			$items = $db->loadObjectList();
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());
			return false;
		}

                return $items;
        }
        
	/**
	 * Method to reset the number of attempts on a process.
	 *
         * @access  public
	 * @param   array    $pks  An array of record primary keys.
	 * @param   boolean  $all  True if all processes should be reset regardless of their status.
	 * @return  boolean  True on success.
	 */
	public function reset($pks, $all)
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
                                    ->update($db->quoteName('#__hwdms_processes'))
                                    ->set('attempts = ' . $db->quote(0) . ', status = ' . $db->quote(1))
                                    ->where('id IN (' . implode(',', $pks) . ')');
                        
                        // Only reset queued processes when specified.
                        if (!$all) $query->where('status = ' . $db->quote(1));
                   
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
         * associated process log entries.
	 *
         * @access  public
	 * @param   array    $pks  An array of record primary keys.
	 * @return  boolean  True if successful, false if an error occurs.
	 * @note    $pks is passed by reference only because JModelAdmin parent method does, and we need to keep this declaration compatible.
	 */
	public function delete(&$pks)
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
                        // Delete associated field value data.
                        $queries[] = $db->getQuery(true)
                                        ->delete('#__hwdms_process_log')
                                        ->where('process_id = ' . $db->quote($pk));
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
