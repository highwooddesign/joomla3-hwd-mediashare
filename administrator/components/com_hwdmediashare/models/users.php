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
	 * Class constructor. Defines a white list of column filters.
	 *
	 * @access	public
	 * @param       array       $config     An optional associative array of configuration settings.
         * @return      void
	 */    
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'alias', 'a.alias',
				'private', 'a.private',
				'likes', 'a.likes',
				'dislikes', 'a.dislikes',
				'status', 'a.status',   
				'published', 'a.published', 
				'featured', 'a.featured',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'access', 'a.access', 'access_level',
				'ordering', 'a.ordering', 'map.ordering',
				'created_user_id', 'a.created_user_id', 'created_user_id_alias', 'a.created_user_id_alias', 'author',
				'created', 'a.created',
				'publish_up', 'a.publish_up',
				'publish_down', 'a.publish_down',
				'modified_user_id', 'a.modified_user_id',
				'modified', 'a.modified',
				'hits', 'a.hits',
				'language', 'a.language',
                                /** Filter fields for additional joins **/                            
				'report_count', 'a.report_count',
                                'title',
			);
		}

		parent::__construct($config);
	}
        
	/**
	 * Method to get the database query.
	 *
	 * @access  protected
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
				'a.id, a.title, a.description, a.alias, a.checked_out, a.checked_out_time,' .
				'a.created_user_id, a.hits, a.published, a.featured,' .
				'a.status, a.publish_up, a.publish_down, a.ordering, a.created, a.created_user_id_alias, a.access,'.
				'a.language'
			)
		);

                // From the user channel table.
                $query->from('#__hwdms_users AS a');

		// Join over the language.
		$query->select('l.title AS language_title');
		$query->join('LEFT', '`#__languages` AS l ON l.lang_code = a.language');

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

                // Join over the users.
		$query->select('u.name, u.username, u.block, u.activation');
		$query->join('LEFT', '#__users AS u ON u.id=a.id');

                // Join over the users for the title (if empty), with value based on configuration.
                $config->get('author') == 0 ? $query->select('CASE WHEN a.title > ' . $db->quote(' ') . ' THEN a.title ELSE ua.name END AS title') : $query->select('CASE WHEN a.title > ' . $db->quote(' ') . ' THEN a.title ELSE ua.username END AS title');
		$query->join('LEFT', '#__users AS ua ON ua.id=a.id');

		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

		// Filter by access level.
		if ($access = $this->getState('filter.access'))
                {
			$query->where('a.access = '.(int) $access);
		}

		// Filter by published state.
		$published = $this->getState('filter.published');
		if (is_numeric($published))
                {
			$query->where('a.published = '.(int) $published);
		}
                elseif ($published === '')
                {
			$query->where('(a.published IN (0, 1))');
		}

                // Filter by status state.
		$status = $this->getState('filter.status');
                if (is_numeric($status)) 
                {
			if ($status == 3)
                        {
                                $query->select('COUNT(report.element_id) AS report_count');
                                $query->join('LEFT', '`#__hwdms_reports` AS report ON report.element_id = a.id AND report.element_type = 5');
                                $query->where('(a.id = report.element_id OR a.status = 3)');
                                $query->group('report.element_id');
                        }
                        else
                        {
                                $query->where('a.status = '.(int) $status);
                        }                                
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
				$search = $db->quote('%'.$db->escape($search, true).'%');
				$query->where('(a.title LIKE '.$search.' OR a.alias LIKE '.$search.' OR u.username LIKE '.$search.' OR u.name LIKE '.$search.')');
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

   		// Group over the key to prevent duplicates.
                $query->group('a.id');
                
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
	protected function populateState($ordering = null, $direction = null)
	{
		// List state information.
		parent::populateState('a.created', 'desc');
	}
}
