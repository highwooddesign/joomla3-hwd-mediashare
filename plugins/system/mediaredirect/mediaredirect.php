<?php
/**
 * @version    $Id: mediaredirect.php 1300 2013-03-19 11:41:46Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla plugin library
jimport('joomla.plugin.plugin');

class plgSystemMediaRedirect extends JPlugin
{
	public function onAfterInitialise()
	{
                $db =& JFactory::getDBO();
                $date =& JFactory::getDate();
                $user = JFactory::getUser();
                $app = & JFactory::getApplication();

                // Require hwdMediaShare factory
                JLoader::register('hwdMediaShareFactory', JPATH_ROOT.'/components/com_hwdmediashare/libraries/factory.php');

                $db->setQuery( 'SHOW TABLES' );
                $tables = $db->loadColumn();

                foreach ($tables as $table)
                {
                        if ($table == $app->getCfg( 'dbprefix' ).'hwdms_migrator')
                        {
                                $option = JFactory::getApplication()->input->get('option');
                                $task = JFactory::getApplication()->input->get('task');
                                $id = JFactory::getApplication()->input->get('id');
                                if ($option == 'com_hwdvideoshare' && $task == 'viewvideo' && $id)
                                {
                                        $query = "
                                            SELECT *
                                            FROM ".$db->quoteName('#hwdms_migrator')."
                                            WHERE `element_id` = ".$db->quoteName(intval($id))."
                                            AND `element_type` = 1
                                        ";
                                        $db->setQuery($query);
                                        $redirect = $db->loadResult();

                                        if ($redirect > 0) JFactory::getApplication()->redirect('index.php?option=com_hwdmediashare&view=mediaitem&id='.$redirect);
                                }
                                
                                if ($option == 'com_hwdvideoshare')
                                {
                                        JFactory::getApplication()->redirect('index.php?option=com_hwdmediashare&view=media');
                                }
                        }
                }
	}
}