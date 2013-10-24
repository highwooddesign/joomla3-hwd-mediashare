<?php
/**
 * @version    SVN $Id: customfields.php 1135 2013-02-21 11:04:43Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modellist');

/**
 * hwdMediaShare Model
 */
class hwdMediaShareModelCustomFields extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	0.1
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
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
                // Select some fields
		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.id, a.element_type, a.type, a.name, a.ordering, a.published, a.visible, a.required, a.searchable, a.fieldcode, a.tooltip'
			)
		);

                // From the hello table
                $query->from('#__hwdms_fields AS a');

                // Filter by published state
		$element_type = $this->getState('filter.element_type');
		if ($element_type !== '') {
			$query->where('element_type = "'.$element_type.'"');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(name LIKE '.$search.' OR tooltip LIKE '.$search.')');
			}
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

		// Add the list ordering clause.
                $orderCol = $this->state->get('list.ordering');
                if (empty($orderCol))
                {
                        $orderCol = JRequest::getCmd('filter_order', 'ordering');
                        $this->setState('list.ordering', $orderCol);
                }

                $orderDirn = $this->state->get('list.direction');
                if (empty($orderDirn))
                {
                        $orderDirn  = JRequest::getCmd('filter_order_Dir', 'ASC');
                        $this->setState('list.direction', $listOrder);
                }

                $query->order($db->escape($orderCol.' '.$orderDirn));

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
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '', 'string');
                $this->setState('filter.published', $published);
                
		$element_type = $this->getUserStateFromRequest($this->context.'.filter.element_type', 'filter_element_type', '1', 'string');
		$this->setState('filter.element_type', $element_type);

                $listOrder = JRequest::getCmd('filter_order', 'ordering');
                $this->setState('list.ordering', $listOrder);

                $listDirn  = JRequest::getCmd('filter_order_Dir', 'ASC');
                $this->setState('list.direction', $listDirn);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_hwdmediashare');
		$this->setState('params', $params);

		// List state information.
		parent::populateState($listOrder, $listDirn);
	}
	/**
	 * Method to
	 */
	public function getProfileTypes()
	{
		static $types = false;

		if( !$types )
		{
                        $path = JPATH_ROOT . '/components/com_hwdmediashare/libraries/fields/customfields.xml';
                        $parser =& JFactory::getXML($path);                                        
                        $fields	= $parser->fields;
                        
			$data	= array();

			foreach( $fields->children() as $field )
			{
                                $data["$field->type"] = $field->name;
			}
			$types = $data;
		}
               
		return $types;
	}
}
