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

class hwdMediaShareModelLinkedMedia extends JModelList
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
				'title', 'a.title',
				'created', 'a.created',
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
	public function getTable($name = 'LinkedMedia', $prefix = 'hwdMediaShareTable', $config = array())
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
                $this->model = JModelLegacy::getInstance('Media', 'hwdMediaShareModel', array('ignore_request' => true));
                $this->model->populateState();
                $this->model->setState('filter.add_to_media', $jinput->get('add', '0', 'int'));
                $this->model->setState('filter.media_id',  $jinput->get('media_id', '', 'int'));
                $this->model->setState('list.ordering', 'a.title');
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
 
                $this->setState('filter.add_to_media', $jinput->get('add', '0', 'int'));
                $this->setState('filter.media_id', $jinput->get('media_id', '', 'int'));

		// List state information.
		parent::populateState('a.title', 'ASC');
	}

	/**
	 * Method to unlink one or more media from another media.
	 *
	 * @param   array    $pks      A list of the primary keys to change.
	 * @param   integer  $mediaId  The value of the media key to associate with.
	 *
	 * @return  boolean  True on success.
	 */
        public function unlink($pks, $mediaId = null)
        {
		// Initialiase variables.
                $db = JFactory::getDbo();

                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                
		$table = $this->getTable();    
                
		// Sanitize the ids.
		$pks = (array) $pks;
		JArrayHelper::toInteger($pks);

		// Iterate through the items to process each one.
		foreach ($pks as $i => $pk)
		{
                        $query = $db->getQuery(true)
                                ->select('id')
                                ->from('#__hwdms_media_map')
                                ->where('((media_id_1 = ' . $db->quote($pk) . ' AND media_id_2 = ' . $db->quote($mediaId) . ') OR (media_id_1 = ' . $db->quote($mediaId) . ' AND media_id_2 = ' . $db->quote($pk) . '))');
                        
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
                                        if ($utilities->authoriseMediaAction('unlink', $pk, $mediaId))
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
	 * Method to link one or more media to another media.
	 *
	 * @param   array    $pks      A list of the primary keys to change.
	 * @param   integer  $mediaId  The value of the media key to associate with.
	 *
	 * @return  boolean  True on success.
	 */
	public function link($pks, $mediaId = null)
	{
		// Initialiase variables.
                $db = JFactory::getDbo();
		$user = JFactory::getUser();
                $date = JFactory::getDate();                

                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                
		$table = $this->getTable();    

		// Sanitize the ids.
		$pks = (array) $pks;
		JArrayHelper::toInteger($pks);

		// Iterate through the items to process each one.
		foreach ($pks as $i => $pk)
		{
			$table->reset();

                        if (!$utilities->authoriseMediaAction('link', $pk, $mediaId))
                        {
                                // Prune items that you can't change.
                                unset($pks[$i]);
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
                        
                        // Check if association already exists
                        $db = JFactory::getDbo();
                        $query = $db->getQuery(true)->select('id')->from('#__hwdms_media_map')
                                 ->where('((' . $db->quoteName('media_id_1') . ' = ' . $db->quote($pk) . ' AND ' . $db->quoteName('media_id_2') . ' = ' . $db->quote($mediaId) . ') OR '
                                        . '(' . $db->quoteName('media_id_1') . ' = ' . $db->quote($mediaId) . ' AND ' . $db->quoteName('media_id_2') . ' = ' . $db->quote($pk) . '))');
                        $db->setQuery($query);
                        $exists = $db->loadResult();

                        // Create an object to bind to the database
                        if (!$exists)
                        {
                                $object = new StdClass;
                                $object->id = '';
                                $object->media_id_1 = (int) $pk;
                                $object->media_id_2 = (int) $mediaId;
                                $object->created_user_id = (int) $user->id;
                                $object->created = $date->format('Y-m-d H:i:s');

                                // Attempt to save the data.
                                if (!$table->save($object))
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
}
