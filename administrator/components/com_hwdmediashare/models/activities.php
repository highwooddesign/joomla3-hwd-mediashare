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

class hwdMediaShareModelActivities extends JModelList
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
				'activity_type', 'a.activity_type',
				'created', 'a.created',
				'id', 'a.id',
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
                                if (empty($items[$x]->author)) $items[$x]->author = JText::_('COM_HWDMS_GUEST');
                        }
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
				'a.id, a.activity_type, a.element_type, a.element_id, a.reply_id,' .
                                'a.target_element_id, a.target_element_id, a.description, a.created_user_id, a.created'
			)
		);

                // From the activities table.
		$query->from($db->quoteName('#__hwdms_activities') . ' AS a');

                // Join over the users for the author, with value based on configuration.
                $config->get('author') == 0 ? $query->select('ua.name AS author') : $query->select('ua.username AS author');
		$query->join('LEFT', '#__users AS ua ON ua.id=a.created_user_id');
                                
		// Add the list ordering clause.
                $listOrder = $this->state->get('list.ordering');
                $listDirn = $this->state->get('list.direction');

		$query->order($db->escape($listOrder.' '.$listDirn));

   		// Group over the ID to prevent duplicates.
                $query->group('a.id');
                
		// Filter by activity type
                $activityType = $this->getState('filter.activity_type');
		if (is_numeric($activityType))
                {
			$query->where('a.activity_type = '.(int) $activityType);
		}
                
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
}
