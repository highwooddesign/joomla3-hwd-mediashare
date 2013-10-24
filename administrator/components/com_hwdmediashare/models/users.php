<?php
/**
 * @version    SVN $Id: users.php 277 2012-03-28 10:03:31Z dhorsfall $
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
class hwdMediaShareModelUsers extends JModelList
{
	var $elementType = 5;
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
				'u.id, u.name, u.username, u.block, u.activation'
			)
		);

                // From the Joomla user table
                $query->from('#__users AS u');

                $query->select('u.id, u.name AS editor');

                // Join over the hwdMediaShare user table
                $query->select('a.description, a.alias, a.checked_out, a.checked_out_time,' .
			       'a.hits, a.published, a.featured,' .
			       'a.status, a.publish_up, a.publish_down, a.created, a.access,'.
			       'a.language');

		$query->join('LEFT', '`#__hwdms_users` AS a ON a.id = u.id');

                $query->select('IF(a.id > 0, true, false) AS created');

		// Join over the language
		$query->select('l.title AS language_title');
		$query->join('LEFT', '`#__languages` AS l ON l.lang_code = a.language');

		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

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
			$query->where('(a.published IN (0, 1) OR a.published IS NULL)');
		}
                
                // Filter by status state
		$status = $this->getState('filter.status');
                if (is_numeric($status)) 
                {
			if ($status == 3)
                        {
                                $query->select('COUNT(report.element_id) AS report_count');
                                $query->join('LEFT', '`#__hwdms_reports` AS report ON report.element_id = a.id AND report.element_type = 5');
                                $query->where('a.id = report.element_id');
                                $query->group('report.element_id');
                        }
                        else
                        {
                                $query->where('a.status = '.(int) $status);
                        } 
		}
                
		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search))
                {
			if (stripos($search, 'id:') === 0)
                        {
				$query->where('u.id = '.(int) substr($search, 3));
			}
                        else
                        {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(u.name LIKE '.$search.' OR u.username LIKE '.$search.')');
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
	 * @since	0.1
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$accessId = $this->getUserStateFromRequest($this->context.'.filter.access', 'filter_access', null, 'int');
		$this->setState('filter.access', $accessId);

		$published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '', 'string');
                $this->setState('filter.published', $published);
		
                $status = $this->getUserStateFromRequest($this->context.'.filter.status', 'filter_status', '', 'string');
		$this->setState('filter.status', $status);
                
		$language = $this->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);

                $listOrder = $this->getUserStateFromRequest($this->context.'.filter_order', 'filter_order', 'u.name');
                $this->setState('list.ordering', $listOrder);

                $listDirn  = $this->getUserStateFromRequest($this->context.'.filter_order_Dir', 'filter_order_Dir', 'ASC');
                $this->setState('list.direction', $listDirn);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_hwdmediashare');
		$this->setState('params', $params);

		// List state information.
		parent::populateState($listOrder, $listDirn);
	}
}
