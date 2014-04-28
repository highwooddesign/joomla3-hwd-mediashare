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

class hwdMediaShareModelFiles extends JModelList
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
				'file_type', 'a.file_type',                            
				'basename', 'a.basename',                            
				'ext', 'a.ext',                            
				'size', 'a.size',                            
				'checked', 'a.checked',                            
				'status', 'a.status',   
				'published', 'a.published', 
				'featured', 'a.featured',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'access', 'a.access', 'access_level',
				'download', 'a.download', 'download_level',
				'ordering', 'a.ordering', 'map.ordering',
				'created_user_id', 'a.created_user_id', 'created_user_id_alias', 'a.created_user_id_alias', 'author',
				'created', 'a.created',
				'publish_up', 'a.publish_up',
				'publish_down', 'a.publish_down',
				'modified_user_id', 'a.modified_user_id',
				'modified', 'a.modified',
				'hits', 'a.hits',
				'language', 'a.language',
			);
		}

		parent::__construct($config);
	}
        
	/**
	 * Method to get a list of items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getItems()
	{
		if ($items = parent::getItems())
		{            
                        for ($x = 0, $count = count($items); $x < $count; $x++)
                        {
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
                                        case 6:
                                            // Category
                                            $table = JTable::getInstance('Category', 'hwdMediaShareTable');
                                            break;
                                }

                                $table->load($items[$x]->element_id);
                                $properties = $table->getProperties(1);
                                $row = JArrayHelper::toObject($properties, 'JObject');

                                $items[$x]->title = (isset($row->title) ? $row->title : '');
                                $items[$x]->ext_id = (isset($row->ext_id) ? $row->ext_id : '');
                                $items[$x]->thumbnail_ext_id = (isset($row->thumbnail_ext_id) ? $row->thumbnail_ext_id : '');
                                $items[$x]->key = (isset($row->key) ? $row->key : '');
                        }
                }

		return $items;
	}

        /**
         * Method to build an SQL query to load the list data.
         *
         * @return      string  An SQL query
         */
        protected function getListQuery()
        {
                // Create a new query object.
                $db = JFactory::getDBO();
                $query = $db->getQuery(true);
                
		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.id, a.element_type, a.element_id, a.file_type, a.basename,' .
				'a.ext, a.size, a.checked, a.created, a.hits'
			)
		);

                // From the files table
                $query->from('#__hwdms_files AS a');

		// Join over the asset groups (for access).
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

		// Join over the asset groups (for download).
		$query->select('dg.title AS download_level');
		$query->join('LEFT', '#__viewlevels AS dg ON dg.id = a.download');

                // Filter by access level.
		if ($access = $this->getState('filter.access'))
                {
			$query->where('a.access = '.(int) $access);
		}
                
		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published))
                {
			$query->where('a.published = '.(int) $published);
		}
                else if ($published === '')
                {
			$query->where('(a.published IN (0, 1))');
		}
                
		// Filter by element type.
		$elementType = $this->getState('filter.element_type');
		if (is_numeric($elementType))
                {
			$query->where('a.element_type = '.(int) $elementType);
		} 
                
		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search))
                {
			if (stripos($search, 'id:') === 0)
                        {
				$query->where('a.id = '.(int) substr($search, 3));
			}
                        else
                        {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(a.basename LIKE '.$search.')');
			}
		}
                
		// Add the list ordering clause.
                $listOrder = $this->state->get('list.ordering');
                $listDirn = $this->state->get('list.direction');

		$query->order($db->escape($listOrder.' '.$listDirn));

   		// Group over the ID to prevent duplicates.
                $query->group('a.id');
                
		//echo nl2br(str_replace('#__','jos_',$query));
		return $query;
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
		// List state information.
		parent::populateState('a.created', 'desc');
	}

	/**
	 * Get the batch form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 */
	public function getBatchForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_hwdmediashare.batch', 'batch', array('control' => 'batch', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		return $form;
	}         
}
