<?php
/**
 * @package     Joomla.site
 * @subpackage  Plugin.system.mediaredirect
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class plgSystemMediaRedirect extends JPlugin
{
	/**
	 * Check for a necessary redirection after Joomla initialises.
	 *
	 * @access  public
	 * @return  void
	 */    
	public function onAfterInitialise()
	{                
                // Initialise variables.
                $db = JFactory::getDBO();
                $date = JFactory::getDate();
                $user = JFactory::getUser();
                $app = JFactory::getApplication();

		// No redirect for admin.
		if ($app->isAdmin())
		{
			return false;
		}

                // Get entry point.
                $option = $app->input->get('option');
                $task = $app->input->get('task');
                $id = $app->input->get('id', 0, 'integer');

                // Only redirect com_hwdvideoshare option.
                if ($option != 'com_hwdvideoshare')
                {
                        return false;
                }
                        
                // Register HWD.
                JLoader::register('hwdMediaShareFactory', JPATH_ROOT.'/components/com_hwdmediashare/libraries/factory.php');

                $db->setQuery('SHOW TABLES');
                $tables = $db->loadColumn();
                
                if (in_array($app->getCfg( 'dbprefix' ).'hwdms_migrator', $tables)) 
                {
                        if ($task == 'viewvideo' && $id)
                        {
                                $query = $db->getQuery(true)
                                        ->select('*')
                                        ->from('#__hwdms_migrator')
                                        ->where('element_id = ' . $db->quote($id))
                                        ->where('element_type = ' . $db->quote(1));
                                try
                                {
                                        $db->setQuery($query);
                                        $redirect = $db->loadResult();                  
                                }
                                catch (RuntimeException $e)
                                {
                                        $this->setError($e->getMessage());
                                        return false;                            
                                }

                                if ($redirect > 0) $app->redirect('index.php?option=com_hwdmediashare&view=mediaitem&id='.$redirect);
                        }

                        $app->redirect('index.php?option=com_hwdmediashare&view=media');
                }
	}
}