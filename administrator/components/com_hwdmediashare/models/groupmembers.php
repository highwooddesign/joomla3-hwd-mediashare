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

class hwdMediaShareModelGroupMembers extends JModelList
{
        protected $model;

    	/**
	 * Constructor override, defines a white list of column filters.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'name', 'u.name',
				'username', 'u.username',
				'registerDate', 'u.registerDate',
			);
		}

		parent::__construct($config);
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
	public function getTable($name = 'GroupMembers', $prefix = 'hwdMediaShareTable', $config = array())
	{
		return JTable::getInstance($name, $prefix, $config);
	}
        
	/**
	 * Method to get a list of items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getItems()
	{
                $jinput = JFactory::getApplication()->input;

                JModelLegacy::addIncludePath(JPATH_ROOT.'/administrator/components/com_hwdmediashare/models');
                $this->model = JModelLegacy::getInstance('Users', 'hwdMediaShareModel', array('ignore_request' => true));
                $this->model->populateState();
                $this->model->setState('filter.add_to_group', $jinput->get('add', '0', 'int'));
                $this->model->setState('filter.group_id',  $jinput->get('group_id', '', 'int'));
                $this->model->setState('list.ordering', 'u.registerDate');
                $this->model->setState('list.direction', 'ASC');
                
                return $this->model->getItems(); 
	}

	/**
	 * Method to get a JPagination object for the data set.
	 *
	 * @return  JPagination  A JPagination object for the data set.
	 */
	public function getPagination()
	{
                return $this->model->getPagination(); 
	}
        
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
                $jinput = JFactory::getApplication()->input;
 
                $this->setState('filter.add_to_group', $jinput->get('add', '0', 'int'));
                $this->setState('filter.group_id', $jinput->get('group_id', '', 'int'));

		// List state information.
		parent::populateState('u.registerDate', 'asc');
	}
        
	/**
	 * Method to unlink one or more users from a group.
	 *
	 * @param   array    $pks      A list of the primary keys to change.
	 * @param   integer  $groupId  The value of the media key to associate with.
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
                                ->from('#__hwdms_group_members')
                                ->where('member_id = ' . $db->quote($pk))
                                ->where('group_id = ' . $db->quote($groupId)); 
                                    
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
                                        if ($utilities->authoriseGroupAction('leave', $groupId, $pk))
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
		}

		// Clear the component's cache
		$this->cleanCache();

		return true;
        }

	/**
	 * Method to link one or more users to a group.
	 *
	 * @param   array    $pks      A list of the primary keys to change.
	 * @param   integer  $groupId  The value of the media key to associate with.
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

                        if (!$utilities->authoriseGroupAction('join', $groupId, $pk))
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
                        $object->member_id = (int) $pk;
                        $object->approved = (int) 1;
                        $object->created = $date->format('Y-m-d H:i:s');
 
                        // Attempt to change the state of the records.
                        if (!$table->save($object))
                        {
                                $this->setError($table->getError());
                                return false;
                        }
		}

		// Clear the component's cache
		$this->cleanCache();

		return true;
	}
}
