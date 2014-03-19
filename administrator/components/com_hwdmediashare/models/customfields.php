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

class hwdMediaShareModelCustomFields extends JModelList
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
				'type', 'a.type',
 				'ordering', 'a.ordering', 'map.ordering',
				'published', 'a.published', 
				'name', 'a.name',
				'visible', 'a.visible',
				'required', 'a.required',
				'searchable', 'a.searchable',
				'fieldcode', 'a.fieldcode',
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
		$items = parent::getItems();

                for ($x = 0, $count = count($items); $x < $count; $x++)
                {
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
				'a.id, a.element_type, a.type, a.name, a.ordering, a.published,' .
				'a.visible, a.required, a.searchable, a.fieldcode, a.tooltip'
			)
		);

                // From the fields table
                $query->from('#__hwdms_fields AS a');

		// Filter by published state.
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
                
		// Filter by search in title.
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
				$query->where('(a.name LIKE '.$search.' OR a.tooltip LIKE '.$search.')');
			}
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
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '', 'string');
                $this->setState('filter.published', $published);
                
		$elementType = $this->getUserStateFromRequest($this->context.'.filter.element_type', 'filter_element_type', '1', 'string');
		$this->setState('filter.element_type', $elementType);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_hwdmediashare');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.created', 'desc');
	}
}
