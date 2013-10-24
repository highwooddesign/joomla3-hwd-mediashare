<?php
/**
 * @version    SVN $Id: groupmedia.php 493 2012-08-28 13:20:17Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      01-Nov-2011 14:19:47
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modellist');

/**
 * hwdMediaShare Model
 */
class hwdMediaShareModelGroupMedia extends JModelList
{
        var $view_list = "groupmedia";
        /**
         * Method to build an SQL query to load the list data.
         *
         * @return      string  An SQL query
         */
        public function getListQuery()
        {
                JLoader::register('hwdMediaShareModelMedia', JPATH_ROOT.'/administrator/components/com_hwdmediashare/models/media.php');
                $query = hwdMediaShareModelMedia::getListQuery();
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

                $listOrder = $this->getUserStateFromRequest($this->context.'.filter_order', 'filter_order', 'a.title');
                $this->setState('list.ordering', $listOrder);

                $listDirn  = $this->getUserStateFromRequest($this->context.'.filter_order_Dir', 'filter_order_Dir', 'ASC');
                $this->setState('list.direction', $listDirn);

                $linked = $this->getUserStateFromRequest($this->context.'.filter.linked', 'filter_linked', null, 'string');
		$this->setState('filter.linked', $linked);

                $group  = JRequest::getInt('group_id','');
                $this->setState('filter.group_id', $group);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_hwdmediashare');
		$this->setState('params', $params);

		// List state information.
		parent::populateState($listOrder, $listDirn);
	}
        /**
         * Method to build an SQL query to load the list data.
         *
         * @return      string  An SQL query
         */
        public function unlink($id, $params)
        {
            $table =& JTable::getInstance('LinkedGroups', 'hwdMediaShareTable');

            $db =& JFactory::getDBO();
            $query = "
              SELECT id
                FROM ".$db->quoteName('#__hwdms_group_map')."
                WHERE ".$db->quoteName('media_id')." = ".$db->quote($id)." AND ".$db->quoteName('group_id')." = ".$db->quote($params->groupId).";
              ";
            $db->setQuery($query);
            $rows = $db->loadObjectList();

		for( $i = 0; $i < count($rows); $i++ )
		{
			$row = $rows[$i];

                        if( !$table->delete( $row->id ) )
			{
				$errors	= true;
			}
		}
		if( $errors )
		{
			$message	= JText::_('COM_HWDMS_ERROR');
		}

            return true;
        }
        /**
         * Method to build an SQL query to load the list data.
         *
         * @return      string  An SQL query
         */
        public function link($id, $params)
        {
            $user = & JFactory::getUser();
            $date =& JFactory::getDate();
            $table =& JTable::getInstance('LinkedGroups', 'hwdMediaShareTable');

            // Create an object to bind to the database
            $object = new StdClass;
            $object->media_id = $id;
            $object->group_id = $params->groupId;
            $object->created = $date->format('Y-m-d H:i:s');

            if (!$table->bind($object))
            {
                    return JError::raiseWarning( 500, $table->getError() );
            }

            if (!$table->store())
            {
                    JError::raiseError(500, $table->getError() );
            }
            return true;
        }
}
