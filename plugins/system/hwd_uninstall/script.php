<?php
/**
 * @package     Joomla.site
 * @subpackage  Plugin.system.hwd_uninstall
 *
 * @copyright   Copyright (C) 2015 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class plgSystemHWD_UninstallInstallerScript
{       
        /**
	 * Method to install the component.
	 *
	 * @access  public
         * @param   string  $parent  The class calling this method.
         * @return  void
	 */
	public function install($parent)
	{
                $this->hwdUninstall();
	}

	/**
	 * Method to update the component.
	 *
	 * @access  public
         * @param   string  $parent  The class calling this method.
         * @return  void
	 */
	public function update($parent)
	{
                $this->hwdUninstall();
	}
        
	/**
	 * Method to manually remove broken HWD installation.
	 *
	 * @access  public
         * @return  boolean
	 */
	public function hwdUninstall()
	{
                // Initialise variables.
                $db = JFactory::getDBO(); 
                $app = JFactory::getApplication();
                
		echo '<h3>Manual HWDMediaShare Uninstallation</h3>';
		echo '<p class="alert alert-info">This plugin will cleanly remove a broken HWDMediaShare installation.</p>';
     
$query = "DELETE FROM `#__menu` WHERE `link` LIKE '%hwdmediashare%' AND `client_id` = 1";
$db->setQuery($query);
if ($db->query())
{
        echo '<p>Successfully removed menu entries.</p>';
}
else
{
        $app->enqueueMessage($db->getErrorMsg());
}

$query = "DELETE FROM `#__extensions` WHERE `name` LIKE '%com_hwdmediashare%'";
$db->setQuery($query);
if ($db->query())
{
        echo '<p>Successfully removed extension entries.</p>';
}
else
{
        $app->enqueueMessage($db->getErrorMsg());
}

$query = "DELETE FROM `#__assets` WHERE `name` LIKE '%hwdmediashare%'";
$db->setQuery($query);
if ($db->query())
{
        echo '<p>Successfully removed asset entries.</p>';
}
else
{
        $app->enqueueMessage($db->getErrorMsg());
}

$query = "DROP TABLE IF EXISTS `#__hwdms_activities`;";
$db->setQuery($query);
$db->query();

$query = "DROP TABLE IF EXISTS `#__hwdms_albums`;";
$db->setQuery($query);
$db->query();

$query = "DROP TABLE IF EXISTS `#__hwdms_album_map`;";
$db->setQuery($query);
$db->query();

$query = "DROP TABLE IF EXISTS `#__hwdms_category_map`;";
$db->setQuery($query);
$db->query();

$query = "DROP TABLE IF EXISTS `#__hwdms_config`;";
$db->setQuery($query);
$db->query();

$query = "DROP TABLE IF EXISTS `#__hwdms_content_map`;";
$db->setQuery($query);
$db->query();

$query = "DROP TABLE IF EXISTS `#__hwdms_ext`;";
$db->setQuery($query);
$db->query();

$query = "DROP TABLE IF EXISTS `#__hwdms_favourites`;";
$db->setQuery($query);
$db->query();

$query = "DROP TABLE IF EXISTS `#__hwdms_fields`;";
$db->setQuery($query);
$db->query();

$query = "DROP TABLE IF EXISTS `#__hwdms_fields_values`;";
$db->setQuery($query);
$db->query();

$query = "DROP TABLE IF EXISTS `#__hwdms_files`;";
$db->setQuery($query);
$db->query();

$query = "DROP TABLE IF EXISTS `#__hwdms_groups`;";
$db->setQuery($query);
$db->query();

$query = "DROP TABLE IF EXISTS `#__hwdms_group_invite`;";
$db->setQuery($query);
$db->query();

$query = "DROP TABLE IF EXISTS `#__hwdms_group_map`;";
$db->setQuery($query);
$db->query();

$query = "DROP TABLE IF EXISTS `#__hwdms_group_members`;";
$db->setQuery($query);
$db->query();

$query = "DROP TABLE IF EXISTS `#__hwdms_likes`;";
$db->setQuery($query);
$db->query();

$query = "DROP TABLE IF EXISTS `#__hwdms_media`;";
$db->setQuery($query);
$db->query();

$query = "DROP TABLE IF EXISTS `#__hwdms_media_map`;";
$db->setQuery($query);
$db->query();

$query = "DROP TABLE IF EXISTS `#__hwdms_playlists`;";
$db->setQuery($query);
$db->query();

$query = "DROP TABLE IF EXISTS `#__hwdms_playlist_map`;";
$db->setQuery($query);
$db->query();

$query = "DROP TABLE IF EXISTS `#__hwdms_processes`;";
$db->setQuery($query);
$db->query();

$query = "DROP TABLE IF EXISTS `#__hwdms_process_log`;";
$db->setQuery($query);
$db->query();

$query = "DROP TABLE IF EXISTS `#__hwdms_reports`;";
$db->setQuery($query);
$db->query();

$query = "DROP TABLE IF EXISTS `#__hwdms_response_map`;";
$db->setQuery($query);
$db->query();

$query = "DROP TABLE IF EXISTS `#__hwdms_subscriptions`;";
$db->setQuery($query);
$db->query();

$query = "DROP TABLE IF EXISTS `#__hwdms_tags`;";
$db->setQuery($query);
$db->query();

$query = "DROP TABLE IF EXISTS `#__hwdms_tag_map`;";
$db->setQuery($query);
$db->query();

$query = "DROP TABLE IF EXISTS `#__hwdms_upload_tokens`;";
$db->setQuery($query);
$db->query();

$query = "DROP TABLE IF EXISTS `#__hwdms_users`;";
$db->setQuery($query);
$db->query();

                // Remove core HWDMediaShare folders.
                $folders = array();
                $folders[] = JPATH_ADMINISTRATOR.'/components/com_hwdmediashare';       
                $folders[] = JPATH_SITE.'/components/com_hwdmediashare';             
                $folders[] = JPATH_SITE.'/media/com_hwdmediashare';            

		foreach ($folders as $folder)
		{
			if (JFolder::exists($folder))
			{
                                if (!JFolder::delete($folder))
                                {
                                        $app->enqueueMessage(JText::sprintf('FILES_JOOMLA_ERROR_FILE_FOLDER', $folder));
                                }
			}
		}
                
                return true;
	}                              
}
