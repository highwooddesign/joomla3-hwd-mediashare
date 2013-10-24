<?php
/**
 * @version    SVN $Id: extensions.php 277 2012-03-28 10:03:31Z dhorsfall $
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
class hwdMediaShareModelExtensions extends JModelList
{
	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	0.1
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.id,a.ext,a.media_type,a.published,' .
				'a.created_user_id,a.publish_up,a.checked_out,a.checked_out_time,' .
				'a.publish_down,a.access'
			)
		);

                // Select the required fields from the table.
		$query->select('a.id,a.ext,a.media_type,a.published,a.created_user_id,a.publish_up,a.publish_down,a.access');

		$query->from('#__hwdms_ext AS a');

                // Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

                // Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

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

                // Filter by access level.
		if ($access = $this->getState('filter.access'))
                {
			$query->where('a.access = '.(int) $access);
		}

		// Filter by media type
		$mediaType = $this->getState('filter.mediaType');
		if ($mediaType !== '')
                {
			$query->where('media_type = "'.$mediaType.'"');
		}
		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search))
                {
			if (stripos($search, 'id:') === 0)
                        {
				$query->where('id = '.(int) substr($search, 3));
			}
                        else
                            {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('ext LIKE '.$search);
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

		$mediaType = $this->getUserStateFromRequest($this->context.'.filter.mediaType', 'filter_mediaType', '', 'string');
		$this->setState('filter.mediaType', $mediaType);

                $listOrder = JRequest::getCmd('filter_order', 'ext');
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
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 * @return	string		A store id.
	 * @since	0.1
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id.= ':' . $this->getState('filter.search');
		$id.= ':' . $this->getState('filter.media');

		return parent::getStoreId($id);
	}


}