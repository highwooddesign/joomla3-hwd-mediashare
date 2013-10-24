<?php
/**
 * @version    SVN $Id: files.php 1400 2013-04-30 09:31:12Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Mar-2012 21:27:34
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modellist');

/**
 * hwdMediaShare Model
 */
class hwdMediaShareModelFiles extends JModelList
{
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
                                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                                switch ($items[$i]->element_type)
                                {
                                        case 1:
                                            // Media
                                            $table =& JTable::getInstance('Media', 'hwdMediaShareTable');
                                            break;
                                        case 2:
                                            // Album
                                            $table =& JTable::getInstance('Album', 'hwdMediaShareTable');
                                            break;
                                        case 3:
                                            // Group
                                            $table =& JTable::getInstance('Group', 'hwdMediaShareTable');
                                            break;
                                        case 4:
                                            // Playlist
                                            $table =& JTable::getInstance('Playlist', 'hwdMediaShareTable');
                                            break;
                                        case 5:
                                            // Channel
                                            $table =& JTable::getInstance('UserChannel', 'hwdMediaShareTable');
                                            break;
                                        case 6:
                                            // Category
                                            $table =& JTable::getInstance('Category', 'hwdMediaShareTable');
                                            break;
                                }
                                
                                $table->load( $items[$i]->element_id );
                                $properties = $table->getProperties(1);
                                $row = JArrayHelper::toObject($properties, 'JObject');

                                $items[$i]->title = (isset($row->title) ? $row->title : '');
                                $items[$i]->ext_id = (isset($row->ext_id) ? $row->ext_id : '');
                                $items[$i]->thumbnail_ext_id = (isset($row->thumbnail_ext_id) ? $row->thumbnail_ext_id : '');
                                $items[$i]->key = (isset($row->key) ? $row->key : '');
                                $items[$i]->mediaid = (isset($row->id) ? $row->id : '');
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
				'a.id, a.element_type, a.element_id, a.file_type, a.basename,' .
				'a.ext, a.size, a.checked, a.created, a.hits'
			)
		);

                // From the albums table
                $query->from('#__hwdms_files AS a');

		// Join over the language
		$query->select('m.title, m.ext_id, m.key, m.id AS mediaid');
		$query->join('LEFT', '`#__hwdms_media` AS m ON m.id = a.element_id');
		
                // Join over the asset groups.
		//$query->select('ag.title AS access_level');
		$query->select('ag.title AS download_level');
                $query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.download');
		
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
				$query->where('(m.title LIKE '.$search.' OR a.basename LIKE '.$search.')');
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
