<?php
/**
 * @version    SVN $Id: playlist.php 496 2012-08-29 13:26:32Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      06-Dec-2011 09:54:14
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

// Import the list field type
jimport('joomla.form.helper');
jimport('joomla.form.formfield');
JFormHelper::loadFieldClass('list');

/**
 * Playlist field class
 */
class JFormFieldPlaylist extends JFormFieldList
{
        /**
 	 * field type
 	 * @var string
 	 */
 	protected $type = 'Playlist';

        /**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 */
	protected function getOptions()
	{
                // Create a new query object.
                $db = JFactory::getDBO();

                $user	= JFactory::getUser();
                $groups	= implode(',', $user->getAuthorisedViewLevels());

                // Create a new query object.
                $db = JFactory::getDBO();
                $query = $db->getQuery(true);

                // Get hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                // Select the required fields from the table.
                $query->select(
                                'a.id, a.title'
                );

                // From the hello table
                $query->from('#__hwdms_playlists AS a');

                // Restrict based on access
                $query->where('a.access IN ('.$groups.')');

                // Filter by user.
                $query->where('a.created_user_id = ' . $db->quote($user->id));

                // Filter by state
                $published = 1;
                if (is_numeric($published))
                {
                        $query->where('a.published = '.(int) $published);
                }

                // Filter by status
                $status = 1;
                if (is_numeric($status))
                {
                        //$query->where('a.status = '.(int) $status);
                }

                // Filter by start and end dates.
                $nullDate = $db->Quote($db->getNullDate());
                $nowDate = $db->Quote(JFactory::getDate()->toSql());

                $query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')');
                $query->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');

                // Add the list ordering clause.
                $query->order($db->escape('a.title ASC'));

                $db->setQuery($query);
                $rows = $db->loadObjectList();

                // Initialise variables.
		$options = array();

                $options[] = JHtml::_('select.option', '', JText::_('COM_HWDMS_LIST_SELECT_PLAYLIST'));

                foreach ($rows as $id => &$item) :
                        $options[] = JHtml::_('select.option', $item->id, addslashes($item->title));
                endforeach;

		return $options;
	}
}