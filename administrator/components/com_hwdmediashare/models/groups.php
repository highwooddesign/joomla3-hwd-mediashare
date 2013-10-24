<?php
/**
 * @version    SVN $Id: groups.php 1401 2013-04-30 09:31:26Z dhorsfall $
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
class hwdMediaShareModelGroups extends JModelList
{
	var $elementType = 3;
        /**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getItems($pk = null)
	{
                if ($items = parent::getItems($pk))
                {
                        for ($i=0, $n=count($items); $i < $n; $i++)
                        {
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
				'a.id, a.title, a.description, a.alias, a.checked_out, a.checked_out_time,' .
				'a.created_user_id, a.hits, a.published, a.featured,' .
				'a.status, a.publish_up, a.publish_down, a.ordering, a.created, a.access,'.
				'a.language'
			)
		);

                // From the albums table
                $query->from('#__hwdms_groups AS a');

		// Join over the language
		$query->select('l.title AS language_title');
		$query->join('LEFT', '`#__languages` AS l ON l.lang_code = a.language');

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

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
			$query->where('(a.published IN (0, 1))');
		}

                // Filter by status state
		$status = $this->getState('filter.status');
                if (is_numeric($status)) 
                {
			if ($status == 3)
                        {
                                $query->select('COUNT(report.element_id) AS report_count');
                                $query->join('LEFT', '`#__hwdms_reports` AS report ON report.element_id = a.id AND report.element_type = 3');
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
				$query->where('a.id = '.(int) substr($search, 3));
			}
                        else
                        {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(a.title LIKE '.$search.' OR a.alias LIKE '.$search.')');
			}
		}

		// Filter on the language.
		if ($language = $this->getState('filter.language'))
                {
			$query->where('a.language = ' . $db->quote($language));
		}

		// Add the list ordering clause.
                $listOrder = $this->state->get('list.ordering');
                $listDirn = $this->state->get('list.direction');

		$query->order($db->escape($listOrder.' '.$listDirn));

                // Additional filters
		$mediaId = $this->getState('filter.media_id');
                if ($mediaId > 0)
                {
                        // Join over the language
                    $query->select('IF(map.media_id = '.$mediaId.', true, false) AS connection');
                    $query->join('LEFT', '`#__hwdms_group_map` AS map ON map.group_id = a.id AND map.media_id = '.$mediaId);

                        $viewAll = $this->getState('filter.linked') == "all" ? true:false;
                        if (!$viewAll)
                        {
                                $query->where('map.media_id = ' . $db->quote($mediaId));
                        }
                }

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

                // Passing additional parameters to prevent resetting the page
                $listOrder = $this->getUserStateFromRequest($this->context.'.filter_order', 'filter_order', 'a.created', null, false);
                $this->setState('list.ordering', $listOrder);

                // Passing additional parameters to prevent resetting the page
                $listDirn  = $this->getUserStateFromRequest($this->context.'.filter_order_Dir', 'filter_order_Dir', 'DESC', null, false);
                $this->setState('list.direction', $listDirn);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_hwdmediashare');
		$this->setState('params', $params);

		// List state information.
		parent::populateState($listOrder, $listDirn);
	}
}
