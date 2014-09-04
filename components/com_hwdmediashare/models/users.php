<?php
/**
 * @package     Joomla.site
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
	 * Model context string.
         * 
         * @access      public
	 * @var         string
	 */   
	public $context = 'com_hwdmediashare.users';
                
	/**
	 * Method to get the database query.
	 *
	 * @access  protected
	 * @return  JDatabaseQuery  database query
	 */
        protected function getListQuery()
        {
                // Initialise variables.
		$user	= JFactory::getUser();
		$groups	= implode(',', $user->getAuthorisedViewLevels());

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
				'a.*'
			)
		);

                // From the users table.
                $query->from('#__users AS a');
                
                // Restrict based on activation access.
                $query->where('a.block = 0');              

                // Group over the key to prevent duplicates.
                $query->group('a.id');
                
                // Filter by subscribers (allowing the display of users who have subscribed to something).
		$subscribersId = $this->getState('filter.subscribers_id');
		if (is_numeric($subscribersId)) 
                {
                        $query->join('LEFT', '#__hwdms_subscriptions AS map ON map.user_id = a.id');
                        $query->where('map.element_type = 5');
                        $query->where('map.element_id = ' . $db->quote($subscribersId));                        
		}

                // Filter by subscriptions (allowing the display of what a user has subscribed to).
		$subscriptionsId = $this->getState('filter.subscriptions_id');
		if (is_numeric($subscriptionsId)) 
                {
                        $query->join('LEFT', '#__hwdms_subscriptions AS map ON map.element_id = a.id');
                        $query->where('map.element_type = 5');
                        $query->where('map.user_id = ' . $db->quote($subscriptionsId));                        
		}                    
                
                // Filter by group members (allowing the display of users who have joined specific groups).
		$groupId = $this->getState('filter.group_id');
		if (is_numeric($groupId)) 
                {
                        $query->join('LEFT', '#__hwdms_group_members AS map ON map.member_id = a.id');
                        $query->where('map.group_id = ' . $db->quote($groupId)); 
                        $query->order('map.created DESC');
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
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 * @return  void
	 */
	public function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication();
                $user = JFactory::getUser();
                
		// Load the parameters.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                $this->setState('params', $config);

		// List state information.
		parent::populateState($ordering, $direction); 
	}
}
