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

class hwdMediaShareModelReported extends JModelList
{
    	/**
	 * Constructor override, defines a white list of column filters.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'element_type', 'a.element_type',
				'element_id', 'a.element_id',
				'user_id', 'a.user_id',
				'report_id', 'a.report_id',
				'description', 'a.description',
				'created', 'a.created',
			);
		}

		parent::__construct($config);
	}

        /**
	 * Method to count reported media.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getMedia()
	{
                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                        ->select('1')
                        ->from('#__hwdms_reports')
                        ->where('element_type = ' . $db->quote(1))
                        ->group('element_id');
                try
                {
                        $db->setQuery($query);
                        $db->execute(); 
                        $count = $db->getNumRows();                        
                }
                catch (RuntimeException $e)
                {
                        $this->setError($e->getMessage());
                        return false;                            
                }
                return $count;
	}
        
        /**
	 * Method to count reported albums.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getAlbums()
	{
                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                        ->select('1')
                        ->from('#__hwdms_reports')
                        ->where('element_type = ' . $db->quote(2))
                        ->group('element_id');
                try
                {
                        $db->setQuery($query);
                        $db->execute(); 
                        $count = $db->getNumRows();                        
                }
                catch (RuntimeException $e)
                {
                        $this->setError($e->getMessage());
                        return false;                            
                }
                return $count;
	}
        
        /**
	 * Method to count reported groups.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getGroups()
	{
                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                        ->select('1')
                        ->from('#__hwdms_reports')
                        ->where('element_type = ' . $db->quote(3))
                        ->group('element_id');
                try
                {
                        $db->setQuery($query);
                        $db->execute(); 
                        $count = $db->getNumRows();                        
                }
                catch (RuntimeException $e)
                {
                        $this->setError($e->getMessage());
                        return false;                            
                }
                return $count;
	}
        
        /**
	 * Method to count reported users.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getUsers()
	{
                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                        ->select('1')
                        ->from('#__hwdms_reports')
                        ->where('element_type = ' . $db->quote(5))
                        ->group('element_id');
                try
                {
                        $db->setQuery($query);
                        $db->execute(); 
                        $count = $db->getNumRows();                        
                }
                catch (RuntimeException $e)
                {
                        $this->setError($e->getMessage());
                        return false;                            
                }
                return $count;
	}  
        
        /**
	 * Method to count reported playlists.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getPlaylists($pk = null)
	{
                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                        ->select('1')
                        ->from('#__hwdms_reports')
                        ->where('element_type = ' . $db->quote(4))
                        ->group('element_id');
                try
                {
                        $db->setQuery($query);
                        $db->execute(); 
                        $count = $db->getNumRows();                        
                }
                catch (RuntimeException $e)
                {
                        $this->setError($e->getMessage());
                        return false;                            
                }
                return $count;
	}
                
	/**
	 * Method to get a list of items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getItems()
	{                
		$items = parent::getItems();

                for ($x = 0, $count = count($items); $x < $count; $x++)
                {
                        if (empty($items[$x]->author)) $items[$x]->author = JText::_('COM_HWDMS_GUEST');
                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                        switch ($items[$x]->element_type)
                        {
                                case 1:
                                    // Media
                                    $table = JTable::getInstance('Media', 'hwdMediaShareTable');
                                    break;
                                case 2:
                                    // Album
                                    $table = JTable::getInstance('Album', 'hwdMediaShareTable');
                                    break;
                                case 3:
                                    // Group
                                    $table = JTable::getInstance('Group', 'hwdMediaShareTable');
                                    break;
                                case 4:
                                    // Playlist
                                    $table = JTable::getInstance('Playlist', 'hwdMediaShareTable');
                                    break;
                                case 5:
                                    // Channel
                                    $table = JTable::getInstance('UserChannel', 'hwdMediaShareTable');
                                    break;
                        }

                        $table->load($items[$x]->element_id);
                        $properties = $table->getProperties(1);
                        $row = JArrayHelper::toObject($properties, 'JObject');

                        $items[$x]->title = (isset($row->title) ? $row->title : '');                      
                }

		return $items;
	}
        
	/**
	 * Method to get the database query.
	 *
	 * @return  JDatabaseQuery  database query
	 */
        protected function getListQuery()
        {
                // Create a new query object.
                $db = JFactory::getDBO();
                $query = $db->getQuery(true);

                // Get HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.id, a.element_type, a.element_id, a.user_id, a.report_id, a.description, a.created'
			)
		);
                
                // From the reports table
                $query->from('#__hwdms_reports AS a');
                
                // Join over the users for the author, with value based on configuration.
                $config->get('author') == 0 ? $query->select('ua.name AS author') : $query->select('ua.username AS author');
		$query->join('LEFT', '#__users AS ua ON ua.id=a.user_id');
                                
		// Filter by element_type.
		if ($elementType = $this->getState('filter.element_type'))
                {
			$query->where('a.element_type = '.(int) $elementType);
		}
                
		// Filter by element_id.
		if ($elementId = $this->getState('filter.element_id'))
                {
			$query->where('a.element_id = '.(int) $elementId);
		}

		// Add the list ordering clause.
                $listOrder = $this->state->get('list.ordering');
                $listDirn = $this->state->get('list.direction');

		$query->order($db->escape($listOrder.' '.$listDirn));

		//echo nl2br(str_replace('#__','jos_',$query));
		return $query;
        }
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	0.1
	 */
	protected function populateState($ordering = null, $direction = null)
	{
                // Initialise variables.
                $app = JFactory::getApplication();

                $layout = $app->input->get('layout', '', 'word');
                
                switch ($layout) 
                {
                    case 'media':
                        $this->setState('filter.element_type', 1);
                        $this->setState('filter.element_id', $app->input->get('id', '', 'int'));
                        break;                    
                    case 'albums':
                        $this->setState('filter.element_type', 2);
                        $this->setState('filter.element_id', $app->input->get('id', '', 'int'));
                        break;    
                    case 'groups':
                        $this->setState('filter.element_type', 3);
                        $this->setState('filter.element_id', $app->input->get('id', '', 'int'));
                        break;   
                    case 'playlists':
                        $this->setState('filter.element_type', 4);
                        $this->setState('filter.element_id', $app->input->get('id', '', 'int'));
                        break;                      
                    case 'users':
                        $this->setState('filter.element_type', 5);
                        $this->setState('filter.element_id', $app->input->get('id', '', 'int'));
                        break; 
                }

		// Load the parameters.
		$params = JComponentHelper::getParams('com_hwdmediashare');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.created', 'desc');
	}    
}
