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

class hwdMediaShareModelSubscriptions extends JModelList
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
				'title', 'a.title',
				'user_id', 'a.user_id',
				'created', 'a.created',
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
                
		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.id, a.element_type, a.element_id, a.created, a.user_id'
			)
		);
                
                // From the subscriptions table.
                $query->from('#__hwdms_subscriptions AS a');

                // Join over the users for the author, with value based on configuration.
                $query->select('ua.name, ua.username');
		$query->join('LEFT', '#__users AS ua ON ua.id=a.user_id');

		// Filter by published state.
		$element_type = $this->getState('filter.element_type');
		if (is_numeric($element_type) && $element_type)
                {
			$query->where('a.element_type = '.(int) $element_type);
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
				$query->where('(u.name LIKE '.$search.' OR u.username LIKE '.$search.')');
			}
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');

		$query->order($db->escape($orderCol.' '.$orderDirn));

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

		$elementType = $this->getUserStateFromRequest($this->context.'.filter.element_type', 'filter_element_type', '0', 'int');
		$this->setState('filter.element_type', $elementType);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_hwdmediashare');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.created', 'desc');
	}
}
