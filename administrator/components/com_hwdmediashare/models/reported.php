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
	 * Class constructor. Defines a white list of column filters.
	 *
	 * @access  public
	 * @param   array   $config  An optional associative array of configuration settings.
         * @return  void
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
         * @access  public
	 * @return  mixed   An integer on success, false on failure.
	 */
	public function getMedia()
	{
                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                        ->select('*')
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
         * @access  public
	 * @return  mixed   An integer on success, false on failure.
	 */
	public function getAlbums()
	{
                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                        ->select('*')
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
         * @access  public
	 * @return  mixed   An integer on success, false on failure.
	 */
	public function getGroups()
	{
                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                        ->select('*')
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
	 * Method to count reported channels.
         * 
         * @access  public
	 * @return  mixed   An integer on success, false on failure.
	 */
	public function getChannels()
	{
                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                        ->select('*')
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
         * @access  public
	 * @return  mixed   An integer on success, false on failure.
	 */
	public function getPlaylists($pk = null)
	{
                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                        ->select('*')
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
	 * @access  public
	 * @return  mixed   An array of data items on success, false on failure.
	 */
	public function getItems()
	{               
		if ($items = parent::getItems())
		{          
                        for ($x = 0, $count = count($items); $x < $count; $x++)
                        {
                                if (empty($items[$x]->author)) $items[$x]->author = JText::_('COM_HWDMS_GUEST');
                                
                                // Get a table instance.
                                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                                switch ($items[$x]->element_type)
                                {
                                        case 1: // Media
                                                $table = JTable::getInstance('Media', 'hwdMediaShareTable');
                                        break;
                                        case 2: // Album
                                                $table = JTable::getInstance('Album', 'hwdMediaShareTable');
                                        break;
                                        case 3: // Group
                                                $table = JTable::getInstance('Group', 'hwdMediaShareTable');
                                        break;
                                        case 4: // Playlist
                                                $table = JTable::getInstance('Playlist', 'hwdMediaShareTable');
                                        break;
                                        case 5: // Channel
                                                $table = JTable::getInstance('Channel', 'hwdMediaShareTable');
                                        break;
                                }

                                // Attempt to load the table row.
                                $return = $table->load($items[$x]->element_id);

                                // Check for a table object error.
                                if ($return === false && $table->getError())
                                {
                                        $this->setError($table->getError());
                                        return false;
                                }

                                $properties = $table->getProperties(1);
                                $item = JArrayHelper::toObject($properties, 'JObject');

                                $items[$x]->title = (isset($item->title) ? $item->title : '');                      
                        }
                }

		return $items;
	}
        
	/**
	 * Method to get the database query.
	 *
	 * @access  protected
	 * @return  JDatabaseQuery  The database query.
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
	 * @access  protected
	 * @param   string     $ordering   An optional ordering field.
	 * @param   string     $direction  An optional direction (asc|desc).
	 * @return  void
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
                        case 'channels':
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
