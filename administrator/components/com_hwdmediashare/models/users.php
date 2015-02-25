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

class hwdMediaShareModelUsers extends JModelList
{ 
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

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.*'
			)
		);

                // From the users table.
                $query->from('#__users AS a');
                
                // Restrict based on activation access.
                $query->where('a.block = 0');              

                // Group over the key to prevent duplicates.
                $query->group('a.id');
                
		// Filter by search in name or username.
		$search = $this->getState('filter.search');
		if (!empty($search))
                {
			if (stripos($search, 'id:') === 0)
                        {
				$query->where('a.id = '.(int) substr($search, 3));
			}
                        else
                        {
				$search = $db->quote('%'.$db->escape($search, true).'%');
				$query->where('(a.name LIKE '.$search.' OR a.username LIKE '.$search.')');
			}
		}
                
                // Filter by group members (allowing the display of users who have joined specific groups).
                $groupId = $this->getState('filter.group_id');
                if ($groupId > 0)
                {
                        // Join over the group map
                        $query->select('map.id AS mapid, map.group_id, IF(map.group_id = '.$groupId.', true, false) AS connection');
                        $query->join('LEFT', '`#__hwdms_group_members` AS map ON map.member_id = a.id AND map.group_id = '.$groupId);

                        $viewAll = $this->getState('filter.add_to_group') ? true : false;
                        if (!$viewAll)
                        {
                                $query->where('map.group_id = ' . $db->quote($groupId));
                        }
                }             

                // Filter by channel existance.
                $channelFlag = $this->getState('filter.channel');
                if ($channelFlag === false)
                {
                        // Join over the channels
                        $query->join('LEFT', '`#__hwdms_users` AS uc ON uc.id = a.id');
                        $query->where('uc.id IS NULL');
                } 
                
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
		// List state information.
		parent::populateState('a.created', 'desc');
	}
}
