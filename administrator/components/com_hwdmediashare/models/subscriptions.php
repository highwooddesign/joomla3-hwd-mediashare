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

class hwdMediaShareModelSubscriptions extends JModelList
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
				'element_type', 'a.element_type',
				'element_id', 'a.element_id',
				'user_id', 'a.user_id',
				'created', 'a.created',
                                /** Filter fields for additional joins **/
				'title', 'm.title',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to get a list of items.
	 *
	 * @access  public
	 * @return  mixed   An array of data items on success, false on failure.
	 */
	public function getItems()
	{
		if ($items = parent::getItems())
		{            
                        for ($x = 0, $count = count($items); $x < $count; $x++)
                        {
                                // Get a table instance.
                                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                                switch ($items[$x]->element_type)
                                {
                                        case 1: // Media
                                                $table = JTable::getInstance('Media', 'hwdMediaShareTable');
                                        break;
                                        case 2: // Album
                                                $table = JTable::getInstance('Album', 'hwdMediaShareTable');
                                        break;
                                        case 3: // Group
                                                $table = JTable::getInstance('Group', 'hwdMediaShareTable');
                                        break;
                                        case 4: // Playlist
                                                $table = JTable::getInstance('Playlist', 'hwdMediaShareTable');
                                        break;
                                        case 5: // Channel
                                                $table = JTable::getInstance('Channel', 'hwdMediaShareTable');
                                        break;
                                        case 6: // Category
                                                $table = JTable::getInstance('Category', 'hwdMediaShareTable');
                                        break;
                                }

                                // Attempt to load the table row.
                                $return = $table->load($items[$x]->element_id);

                                // Check for a table object error.
                                if ($return === false && $table->getError())
                                {
                                        $this->setError($table->getError());
                                        return false;
                                }
                
                                $properties = $table->getProperties(1);
                                $item = JArrayHelper::toObject($properties, 'JObject');

                                $items[$x]->element = $item;
                        }
                }

		return $items;
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
				'a.id, a.element_type, a.element_id, a.user_id, a.created'
			)
		);
                
                // From the subscriptions table.
                $query->from('#__hwdms_subscriptions AS a');

                // Join over the users for the author, with value based on configuration.
                $query->select('ua.name, ua.username');
		$query->join('LEFT', '#__users AS ua ON ua.id=a.user_id');

		// Filter by published state.
		$element_type = $this->getState('filter.element_type');
		if (is_numeric($element_type))
                {
			$query->where('a.element_type = '.(int) $element_type);
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
				$query->where('(u.name LIKE '.$search.' OR u.username LIKE '.$search.')');
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
		parent::populateState('a.created', 'desc');
	}
}
