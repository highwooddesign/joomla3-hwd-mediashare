<?php
/**
 * @version    SVN $Id: groupmembers.php 493 2012-08-28 13:20:17Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      01-Nov-2011 14:29:37
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modellist');

/**
 * hwdMediaShare Model
 */
class hwdMediaShareModelGroupMembers extends JModelList
{
        /**
         * Method to build an SQL query to load the list data.
         *
         * @return      string  An SQL query
         */
        protected function getListQuery()
        {
                $groupId = JRequest::getInt( 'group_id', '' );
                $viewAll = $this->getState('filter.linked') == "all" ? true:false;

                // Create a new query object.
                $db = JFactory::getDBO();
                $query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'DISTINCT a.id, a.name, a.username'
			)
		);

                // From the albums table
                $query->from('#__users AS a');

                // Join over the language
		$query->select('IF(map.group_id = '.$groupId.', true, false) AS connection');
                $query->join('LEFT', '`#__hwdms_group_members` AS map ON map.member_id = a.id AND map.group_id = '.$groupId);

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
				$query->where('(a.name LIKE '.$search.' OR a.username LIKE '.$search.')');
			}
		}

                if (!$viewAll)
                {
                        $query->where('map.group_id = ' . $db->quote($groupId));
                }

		// Add the list ordering clause.
                $listOrder = $this->state->get('list.ordering');
                $listDirn = $this->state->get('list.direction');

		$query->order($db->escape($listOrder.' '.$listDirn));
                //$query->group('a.id');

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

		$linked = $this->getUserStateFromRequest($this->context.'.filter.linked', 'filter_linked', null, 'string');
		$this->setState('filter.linked', $linked);

                $listOrder = JRequest::getCmd('filter_order', 'name');
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
         * Method to build an SQL query to load the list data.
         *
         * @return      string  An SQL query
         */
        public function unlink($id, $params)
        {
            $table =& JTable::getInstance('GroupMembers', 'hwdMediaShareTable');

            $db =& JFactory::getDBO();
            $query = "
              SELECT id
                FROM ".$db->quoteName('#__hwdms_group_members')."
                WHERE ".$db->quoteName('member_id')." = ".$db->quote($id)." AND ".$db->quoteName('group_id')." = ".$db->quote($params->groupId).";
              ";
            $db->setQuery($query);
            $rows = $db->loadObjectList();

		for( $i = 0; $i < count($rows); $i++ )
		{
			$row = $rows[$i];

                        if( !$table->delete( $row->id ) )
			{
				$errors	= true;
			}
		}
		if( $errors )
		{
			$message	= JText::_('COM_HWDMS_ERROR');
		}

            return true;
        }
        /**
         * Method to build an SQL query to load the list data.
         *
         * @return      string  An SQL query
         */
        public function link($id, $params)
        {
            $date =& JFactory::getDate();
            $table =& JTable::getInstance('GroupMembers', 'hwdMediaShareTable');

            // Create an object to bind to the database
            $object = new StdClass;
            $object->member_id = $id;
            $object->group_id = $params->groupId;
            $object->created = $date->format('Y-m-d H:i:s');

            if (!$table->bind($object))
            {
                    return JError::raiseWarning( 500, $table->getError() );
            }

            if (!$table->store())
            {
                    JError::raiseError(500, $table->getError() );
            }
            return true;
        }
}
