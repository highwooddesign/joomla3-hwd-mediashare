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

class hwdMediaShareModelExtensions extends JModelList
{
	/**
	 * Class constructor. Defines a white list of column filters.
	 *
	 * @access  public
	 * @param   array   $config  An optional associative array of configuration settings.
         * @return  void
	 */    
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'ext', 'a.ext',
				'media_type', 'a.media_type',
				'published', 'a.published', 
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'access', 'a.access', 'access_level',
				'created_user_id', 'a.created_user_id', 'author',
				'created', 'a.created',
				'publish_up', 'a.publish_up',
				'publish_down', 'a.publish_down',
				'modified_user_id', 'a.modified_user_id',
				'modified', 'a.modified',
			);
		}

		parent::__construct($config);
	}
        
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
				'a.id, a.ext, a.media_type, a.published, a.checked_out, a.checked_out_time, a.access,' .
				'a.created_user_id, a.publish_up, a.publish_down'
			)
		);

                // From the extensions table.
		$query->from('#__hwdms_ext AS a');

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

                // Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

                // Filter by published state.
		$published = $this->getState('filter.published');
		if (is_numeric($published))
                {
			$query->where('a.published = '.(int) $published);
		}
                else
                {
			$query->where('(a.published IN (0, 1))');
		}

                // Filter by access level.
		if ($access = $this->getState('filter.access'))
                {
			$query->where('a.access = '.(int) $access);
		}

		// Filter by media type.
		$mediaType = $this->getState('filter.media_type');
		if (is_numeric($mediaType))
                {
			$query->where('a.media_type = '.(int) $mediaType);
		}                

		// Filter by search in title.
		$search = $this->getState('filter.search');
		if (!empty($search))
                {
			if (stripos($search, 'id:') === 0)
                        {
				$query->where('id = '.(int) substr($search, 3));
			}
                        else
                        {
				$search = $db->quote('%'.$db->escape($search, true).'%');
				$query->where('ext LIKE '.$search);
			}
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
	 * @param   string     $ordering   An optional ordering field.
	 * @param   string     $direction  An optional direction (asc|desc).
	 * @return  void
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// List state information.
		parent::populateState('a.ext', 'asc');
	}
}