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

class com_hwdMediaShareInstallerScript
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
		// echo '<p>' . JText::_('COM_HWDMEDIASHARE_INSTALL_TEXT') . '</p>';
	}

	/**
	 * Method to uninstall the component.
	 *
	 * @access  public
         * @param   string  $parent  The class calling this method.
         * @return  void
	 */
	public function uninstall($parent)
	{
                // echo '<p>' . JText::_('COM_HWDMEDIASHARE_UNINSTALL_TEXT') . '</p>';
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
		// echo '<p>' . JText::_('COM_HWDMEDIASHARE_UPDATE_TEXT') . '</p>';

		// Initialise variables.
                $app = JFactory::getApplication();
                
                // Update database.
                $this->databaseFixes();
                
                $app->enqueueMessage(JText::_('COM_HWDMEDIASHARE_MESSAGE_RECOMMEND_MAINTENANCE'));
	}

	/**
	 * Method to run before an install/update/uninstall method.
	 *
	 * @access  public
         * @param   string  $type    The type of change (install, update or discover_install).
         * @param   string  $parent  The class calling this method.
         * @return  void
	 */
	public function preflight($type, $parent)
	{
		// echo '<p>' . JText::_('COM_HWDMEDIASHARE_PREFLIGHT_' . $type . '_TEXT') . '</p>';

		// Initialise variables.
                $app = JFactory::getApplication();
                
                // Get Joomla version.
                $version = new JVersion();

                // Check Joomla compatibility.
                if ($version->RELEASE < 3.0)
                {
			$app->enqueueMessage(JText::_('COM_HWDMEDIASHARE_MESSAGE_NOT_COMPATIBLE'));
			$app->redirect('index.php?option=com_installer');
                }
        }

	/**
	 * Method to run after an install/update/uninstall method.
	 *
	 * @access  public
         * @param   string  $type    The type of change (install, update or discover_install).
         * @param   string  $parent  The class calling this method.
         * @return  void
	 */
	public function postflight($type, $parent)
	{
		// echo '<p>' . JText::_('COM_HWDMEDIASHARE_POSTFLIGHT_' . $type . '_TEXT') . '</p>';

                // Add content types.
                $this->addContentTypes();

                // Remove unwanted legacy files.
                $this->removeLegacyFiles();
                
                // Check if the HWD menu exists.
                if (!$this->checkMenuExists())
                {
                        $this->writeMenu();
                }
                
                // Fix any broken menu links.
                $this->fixBrokenMenuItems();
	}

        /**
	 * Method to check if the HWD menu exists.
	 *
	 * @access  public
         * @return  void
	 */
	public function checkMenuExists()
	{
        	// Initialise variables.
                $app = JFactory::getApplication();
                $db = JFactory::getDbo();
                
                $query = $db->getQuery(true)
                        ->select('1')
                        ->from('#__menu_types')
                        ->where('menutype = ' . $db->quote('hwdmediashare'));
                try
                {
                        $db->setQuery($query);
                        return $db->loadResult();
                }
                catch (RuntimeException $e)
                {
			$app->enqueueMessage($e->getMessage());
			return false;                        
                }
	}
        
        /**
	 * Method to create the HWD menu.
	 *
	 * @access  public
         * @return  void
	 */
        public function writeMenu()
        {
        	// Initialise variables.
                $app = JFactory::getApplication();
                $db = JFactory::getDbo();
                
                // Define the menu strings.
                $title = 'HWDMediaShare Menu';
                $desc = 'The HWDMediaShare gallery menu';
                
                // Columns and values to insert.
                $columns = array('menutype', 'title', 'description');
                $values = array($db->quote('hwdmediashare'), $db->quote($title), $db->quote($desc));

                $query = $db->getQuery(true)
                        ->insert($db->quoteName('#__menu_types'))
                        ->columns($db->quoteName($columns))
                        ->values(implode(',', $values));
                try
                {
                        $db->setQuery($query);
                        $db->execute();
                }
                catch (RuntimeException $e)
                {
			$app->enqueueMessage($e->getMessage());
			return false;                           
                }

                // Add default menu items.
                $this->addDefaultMenuItems();
                
                return true;
        }
        
        /**
	 * Method to add default HWD menu items.
	 *
	 * @access  public
         * @return  void
	 */
        public function addDefaultMenuItems()
        {
        	// Initialise variables.
                $app = JFactory::getApplication();
                $db = JFactory::getDbo();
                $file = JPATH_ROOT . '/administrator/components/com_hwdmediashare/toolbar.xml';
                $xml = new SimpleXMLElement($file, null, true);

                if ($xml) 
                {
                        $items = $xml->items;
                        foreach($items->children() as $item)
                        {
                                $obj		= new stdClass();
                                $obj->title	= empty($item->name) ? "" : "$item->name";
                                $obj->alias	= empty($item->alias) ? "" : com_hwdMediaShareInstallerScript::getAlias($item->alias);
                                $obj->path	= $obj->alias;
                                $obj->link	= empty($item->link) ? "" : "$item->link";
                                $obj->menutype	= 'hwdmediashare';
                                $obj->type	= 'component';
                                $obj->published	= 1;
                                $obj->parent_id	= 1;
                                $obj->level	= 1;
                                $obj->access	= 1;
                                $obj->language	= '*';

                                // Set the menu position in the nested tree.
                                $query = $db->getQuery(true)
                                        ->select('rgt')
                                        ->from('#__menu')
                                        ->order('rgt DESC');
                                try
                                {
                                        $db->setQuery($query);
                                        
                                        $childs         = $item->childs;
                                        $totalchild     = $childs ? count($childs->children()) : 0;
                                        $obj->lft       = $db->loadResult() + 1;
                                        $obj->rgt       = $obj->lft + $totalchild * 2 + 1;                                        
                                }
                                catch (RuntimeException $e)
                                {
                                        $app->enqueueMessage($e->getMessage());
                                        return false;                           
                                }

                                // Insert menu item.
                                try
                                {
                                        $db->insertObject( '#__menu' , $obj );
                                }
                                catch (RuntimeException $e)
                                {
                                        $app->enqueueMessage($e->getMessage());
                                        return false;                             
                                }                       
                        }
                }

                return true;
        }
        
        /**
	 * Method to fix broken menu items and reassociate them with the HWD asset.
	 *
	 * @access  public
         * @return  void
	 */
        public function fixBrokenMenuItems()
        {
        	// Initialise variables.
                $app = JFactory::getApplication();
                $db = JFactory::getDbo();
                
                // Get HWD component id.
                $component      = JComponentHelper::getComponent('com_hwdmediashare');
                $component_id   = 0;
                
                if (is_object($component) && isset($component->id))
                {
                        $component_id = $component->id;
                }

                if ($component_id > 0)
                {
                        // Re-associate HWD menu items with component ID.
                        $fields = array(
                            $db->quoteName('component_id') . ' = ' . $db->quote($component_id)
                        );

                        $conditions = array(
                            $db->quoteName('link') . ' LIKE ' . $db->Quote('%option=com_hwdmediashare%')
                        );

                        $query = $db->getQuery(true)
                                    ->update($db->quoteName('#__menu'))->set($fields)->where($conditions);
                        try
                        {
                                $db->setQuery($query);
                                $db->execute();
                        }
                        catch (RuntimeException $e)
                        {
                                $app->enqueueMessage($e->getMessage());
                                return false;                             
                        }
                        
                        // Update 'users' view menu links to 'channels'.
                        $fields = array(
                            $db->quoteName('title') . ' = ' . $db->quote('Channels'),
                            $db->quoteName('alias') . ' = ' . $db->quote('channels'),
                            $db->quoteName('path') . ' = ' . $db->quote('channels'),
                            $db->quoteName('link') . ' = ' . $db->quote('index.php?option=com_hwdmediashare&view=channels'),
                        );

                        $conditions = array(
                            $db->quoteName('link') . ' LIKE ' . $db->Quote('%option=com_hwdmediashare&view=users%')
                        );

                        $query = $db->getQuery(true)
                                    ->update($db->quoteName('#__menu'))->set($fields)->where($conditions);
                        try
                        {
                                $db->setQuery($query);
                                $db->execute();
                        }
                        catch (RuntimeException $e)
                        {
                                $app->enqueueMessage($e->getMessage());
                                return false;                             
                        }
                }
                
                return true;
        }
        
        /**
	 * Method to validate the alias, and prevent duplciates.
	 *
	 * @access  public
         * @return  void
	 */
        public function getAlias($alias)
        {
        	// Initialise variables.
                $app = JFactory::getApplication();
                $db = JFactory::getDbo();
                
                // Sanitise the alias.
		$alias = JFilterOutput::stringURLSafe($alias);
                
                // Check for duplicates.
                $db = JFactory::getDBO();
                $query = $db->getQuery(true)
                        ->select('1')
                        ->from('#__menu')
                        ->where($db->quoteName('alias') . '=' . $db->quote($alias));
                try
                {
                        $db->setQuery($query);
                        $duplicate = $db->loadResult();
                }
                catch (RuntimeException $e)
                {
                        $app->enqueueMessage($e->getMessage());
                        return false;                           
                }

		if ($duplicate)
                {                       
                        $alias = $alias . '-media';
                        return $this->getAlias($alias);     
                }
                else
                {
                        return $alias;                    
                }
        }  
        
        /**
	 * Method to remove any deprecated files from previous installations.
	 *
	 * @access  public
         * @return  void
	 */
	public function removeLegacyFiles()
	{
                $files = array();
                $folders = array();
                
                // Administrator/components/com_hwdmediashare
                $files[]   = JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/controllers/activities.php';
                $files[]   = JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/controllers/activity.php';
                $files[]   = JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/controllers/reported.php';
                $files[]   = JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/controllers/tag.php';
                $files[]   = JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/controllers/tags.php';
                $files[]   = JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/controllers/user.php';
                $files[]   = JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/controllers/users.php';                
                $files[]   = JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/helpers/html/hwdadminusers.php'; 
                $files[]   = JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/models/fields/article.php';                
                $files[]   = JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/models/fields/editorhwd.php';                
                $files[]   = JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/models/fields/userchannel.php';                
                $files[]   = JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/models/fields/userchannelfullordering.php';                
                $files[]   = JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/models/forms/activity.js';                
                $files[]   = JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/models/forms/activity.xml';                
                $files[]   = JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/models/forms/album.js';                
                $files[]   = JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/models/forms/configuration.js';                
                $files[]   = JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/models/forms/filter_users.xml';                
                $files[]   = JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/models/forms/group.js';                
                $files[]   = JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/models/forms/playlist.js';                
                $files[]   = JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/models/forms/tag.js';                
                $files[]   = JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/models/forms/tag.xml';                
                $files[]   = JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/models/forms/user.js';                
                $files[]   = JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/models/forms/user.xml';  
                $files[]   = JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/models/activity.php';  
                $files[]   = JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/models/tag.php';  
                $files[]   = JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/models/tags.php';  
                $files[]   = JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/models/user.php';  
                $files[]   = JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables/extension.php';
                $files[]   = JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables/tag.php';  
                $files[]   = JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables/tagmap.php';  
                $files[]   = JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables/userchannel.php';  
                $folders[] = JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/views/activity';
                $files[]   = JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/views/addmedia/tmpl/form.php';  
                $files[]   = JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/views/addmedia/tmpl/remote.php';  
                $folders[] = JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/views/tag';
                $folders[] = JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/views/tags';
                $folders[] = JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/views/user';
                $folders[] = JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/views/users';
                $files[]   = JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/com_hwdmediashare.xml';

                // Components/com_hwdmediashare
                $files[]   = JPATH_SITE.'/components/com_hwdmediashare/controllers/activities.php';
                $files[]   = JPATH_SITE.'/components/com_hwdmediashare/controllers/activity.php';
                $files[]   = JPATH_SITE.'/components/com_hwdmediashare/controllers/activityform.php';
                $files[]   = JPATH_SITE.'/components/com_hwdmediashare/controllers/album.php';
                $files[]   = JPATH_SITE.'/components/com_hwdmediashare/controllers/category.php';
                $files[]   = JPATH_SITE.'/components/com_hwdmediashare/controllers/group.php';
                $files[]   = JPATH_SITE.'/components/com_hwdmediashare/controllers/mediaitem.php';
                $files[]   = JPATH_SITE.'/components/com_hwdmediashare/controllers/playlist.php';
                $files[]   = JPATH_SITE.'/components/com_hwdmediashare/controllers/user.php';
                $files[]   = JPATH_SITE.'/components/com_hwdmediashare/controllers/userform.php';
                $files[]   = JPATH_SITE.'/components/com_hwdmediashare/controllers/users.php';
                $files[]   = JPATH_SITE.'/components/com_hwdmediashare/helpers/dropdown.php';
                $files[]   = JPATH_SITE.'/components/com_hwdmediashare/helpers/icon.php';
                $files[]   = JPATH_SITE.'/components/com_hwdmediashare/helpers/mobile.php';
                $files[]   = JPATH_SITE.'/components/com_hwdmediashare/libraries/fields/label.php';
                $files[]   = JPATH_SITE.'/components/com_hwdmediashare/libraries/fields/label.xml';
                $files[]   = JPATH_SITE.'/components/com_hwdmediashare/libraries/fields/list.php';
                $files[]   = JPATH_SITE.'/components/com_hwdmediashare/libraries/fields/list.xml';
                $files[]   = JPATH_SITE.'/components/com_hwdmediashare/libraries/fields/singleselect.php';
                $files[]   = JPATH_SITE.'/components/com_hwdmediashare/libraries/fields/singleselect.xml';
                $files[]   = JPATH_SITE.'/components/com_hwdmediashare/libraries/layouts/media_item_details.php';
                $files[]   = JPATH_SITE.'/components/com_hwdmediashare/libraries/layouts/media_item_display.php';                
                $files[]   = JPATH_SITE.'/components/com_hwdmediashare/libraries/layouts/users_details.php';
                $folders[] = JPATH_SITE.'/components/com_hwdmediashare/libraries/opengraph';
                $folders[] = JPATH_SITE.'/components/com_hwdmediashare/libraries/recaptcha';
                $files[]   = JPATH_SITE.'/components/com_hwdmediashare/libraries/form.php';
                $files[]   = JPATH_SITE.'/components/com_hwdmediashare/libraries/maintenance.php';
                $files[]   = JPATH_SITE.'/components/com_hwdmediashare/libraries/reports.php';
                $files[]   = JPATH_SITE.'/components/com_hwdmediashare/libraries/tags.php';
                $files[]   = JPATH_SITE.'/components/com_hwdmediashare/models/fields/editorhwd.php';
                $files[]   = JPATH_SITE.'/components/com_hwdmediashare/models/fields/element.php';
                $files[]   = JPATH_SITE.'/components/com_hwdmediashare/models/forms/activity.xml';
                $files[]   = JPATH_SITE.'/components/com_hwdmediashare/models/forms/comment.xml';
                $files[]   = JPATH_SITE.'/components/com_hwdmediashare/models/forms/filter.xml';
                $files[]   = JPATH_SITE.'/components/com_hwdmediashare/models/forms/link.xml';
                $files[]   = JPATH_SITE.'/components/com_hwdmediashare/models/forms/user.xml';
                $files[]   = JPATH_SITE.'/components/com_hwdmediashare/models/activity.php';
                $files[]   = JPATH_SITE.'/components/com_hwdmediashare/models/activityform.php';
                $files[]   = JPATH_SITE.'/components/com_hwdmediashare/models/tags.php';
                $files[]   = JPATH_SITE.'/components/com_hwdmediashare/models/tags.php';
                $files[]   = JPATH_SITE.'/components/com_hwdmediashare/models/user.php';
                $files[]   = JPATH_SITE.'/components/com_hwdmediashare/models/userform.php';
                $folders[] = JPATH_SITE.'/components/com_hwdmediashare/views/activities';
                $folders[] = JPATH_SITE.'/components/com_hwdmediashare/views/activity';
                $folders[] = JPATH_SITE.'/components/com_hwdmediashare/views/activityform';
                $folders[] = JPATH_SITE.'/components/com_hwdmediashare/views/user';
                $folders[] = JPATH_SITE.'/components/com_hwdmediashare/views/userform';
                $folders[] = JPATH_SITE.'/components/com_hwdmediashare/views/users';

                // Media/com_hwdmediashare
                $folders[] = JPATH_SITE.'/media/com_hwdmediashare/assets/images/admin';        
                $files[]   = JPATH_SITE.'/media/com_hwdmediashare/assets/images/ajaxupload/failed.png';
                $files[]   = JPATH_SITE.'/media/com_hwdmediashare/assets/images/ajaxupload/file.png';
                $files[]   = JPATH_SITE.'/media/com_hwdmediashare/assets/images/ajaxupload/success.png';
                $files[]   = JPATH_SITE.'/media/com_hwdmediashare/assets/images/ajaxupload/uploading.png';
                $folders[] = JPATH_SITE.'/media/com_hwdmediashare/assets/images/icons/16';
                $folders[] = JPATH_SITE.'/media/com_hwdmediashare/assets/images/icons/32';              
                $folders[] = JPATH_SITE.'/media/com_hwdmediashare/assets/images/icons/48';
                $files[]   = JPATH_SITE.'/media/com_hwdmediashare/assets/images/ajax-loader-slideshow.gif';                

                // Modules
                $files[]   = JPATH_SITE.'/modules/mod_media_images/css/slideshow.css';
                $folders[] = JPATH_SITE.'/modules/mod_media_images/images';              
                $folders[] = JPATH_SITE.'/modules/mod_media_images/js';              
                $files[]   = JPATH_SITE.'/modules/mod_media_media/tmpl/compact-horizontal.php';
                $files[]   = JPATH_SITE.'/modules/mod_media_media/tmpl/compact-vertical.php';
                $files[]   = JPATH_SITE.'/modules/mod_media_videos/css/sidebar-simple.css';
                $files[]   = JPATH_SITE.'/modules/mod_media_videos/tmpl/compact.php';
                $files[]   = JPATH_SITE.'/modules/mod_media_videos/tmpl/sidebar-simple.php';

                // Plugins
                $folders[] = JPATH_SITE.'/plugins/community/media/assets';              

                foreach ($files as $file)
		{
			if (JFile::exists($file))
			{
                                if (!JFile::delete($file))
                                {
                                        $app->enqueueMessage(JText::sprintf('FILES_JOOMLA_ERROR_FILE_FOLDER', $file));
                                }
			}
		}
                
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
        
        /**
	 * Method to add new content types to Joomla only when they have not alreayd been added.
	 *
	 * @access	public
         * @return      void
	 */
	public function addContentTypes()
	{
                $db = JFactory::getDbo();
                        
                // com_hwdmediashare.media
                $query = $db->getQuery(true)
                        ->select('type_id')
                        ->from('#__content_types')
                        ->where($db->quoteName('type_alias') . ' = ' . $db->quote('com_hwdmediashare.media'));
                try
                {
                        $db->setQuery($query);
                        $typeMedia = $db->loadResult();                        
                }
                catch (RuntimeException $e)
                {
			JError::raiseError(500, $e->getMessage());
			return false;                          
                }
                
                if (!$typeMedia)
                {
$query = <<<SQL
INSERT INTO `#__content_types` (`type_title`, `type_alias`, `table`, `rules`, `field_mappings`, `router`, `content_history_options`) VALUES
('Media', 'com_hwdmediashare.media', '{"special":{"dbtable":"#__hwdms_media","key":"id","type":"Media","prefix":"hwdMediaShareTable","config":"array()"},"common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}', '', '{"common":{"core_content_item_id":"id","core_title":"title","core_state":"published","core_alias":"alias","core_created_time":"created","core_modified_time":"modified","core_body":"description", "core_hits":"null","core_publish_up":"publish_up","core_publish_down":"publish_down","core_access":"access", "core_params":"params", "core_featured":"null", "core_metadata":"metadata", "core_language":"language", "core_images":"images", "core_urls":"link", "core_version":"version", "core_ordering":"ordering", "core_metakey":"metakey", "core_metadesc":"metadesc", "core_catid":"catid", "core_xreference":"null", "asset_id":"null"}}', '', '');
SQL;
                        try
                        {
                                $db->setQuery($query);
                                $db->execute();
                        }
                        catch (RuntimeException $e)
                        {
                                JError::raiseError(500, $e->getMessage());
                                return false;  
                        }
                }

                // com_hwdmediashare.album
                $query = $db->getQuery(true)
                        ->select('type_id')
                        ->from('#__content_types')
                        ->where($db->quoteName('type_alias') . ' = ' . $db->quote('com_hwdmediashare.album'));
                try
                {
                        $db->setQuery($query);
                        $typeMedia = $db->loadResult();                        
                }
                catch (RuntimeException $e)
                {
			JError::raiseError(500, $e->getMessage());
			return false;                          
                }
                
                if (!$typeMedia)
                {
$query = <<<SQL
INSERT INTO `#__content_types` (`type_title`, `type_alias`, `table`, `rules`, `field_mappings`, `router`, `content_history_options`) VALUES
('Media Album', 'com_hwdmediashare.album', '{"special":{"dbtable":"#__hwdms_albums","key":"id","type":"Album","prefix":"hwdMediaShareTable","config":"array()"},"common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}', '', '{"common":{"core_content_item_id":"id","core_title":"title","core_state":"published","core_alias":"alias","core_created_time":"created","core_modified_time":"modified","core_body":"description", "core_hits":"null","core_publish_up":"publish_up","core_publish_down":"publish_down","core_access":"access", "core_params":"params", "core_featured":"null", "core_metadata":"metadata", "core_language":"language", "core_images":"images", "core_urls":"link", "core_version":"version", "core_ordering":"ordering", "core_metakey":"metakey", "core_metadesc":"metadesc", "core_catid":"catid", "core_xreference":"null", "asset_id":"null"}}', '', '');
SQL;
                        try
                        {
                                $db->setQuery($query);
                                $db->execute();
                        }
                        catch (RuntimeException $e)
                        {
                                JError::raiseError(500, $e->getMessage());
                                return false;  
                        }
                }
                
                // com_hwdmediashare.category
                $query = $db->getQuery(true)
                        ->select('type_id')
                        ->from('#__content_types')
                        ->where($db->quoteName('type_alias') . ' = ' . $db->quote('com_hwdmediashare.category'));
                try
                {
                        $db->setQuery($query);
                        $typeMedia = $db->loadResult();                        
                }
                catch (RuntimeException $e)
                {
			JError::raiseError(500, $e->getMessage());
			return false;                          
                }
                
                if (!$typeMedia)
                {
$query = <<<SQL
INSERT INTO `#__content_types` (`type_title`, `type_alias`, `table`, `rules`, `field_mappings`, `router`, `content_history_options`) VALUES
('Media Category', 'com_hwdmediashare.category', '{"special":{"dbtable":"#__categories","key":"id","type":"Category","prefix":"JTable","config":"array()"},"common":{"dbtable":"#__core_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}', '', '{"common":{"core_content_item_id":"id","core_title":"title","core_state":"published","core_alias":"alias","core_created_time":"created_time","core_modified_time":"modified_time","core_body":"description", "core_hits":"hits","core_publish_up":"null","core_publish_down":"null","core_access":"access", "core_params":"params", "core_featured":"null", "core_metadata":"metadata", "core_language":"language", "core_images":"null", "core_urls":"null", "core_version":"version", "core_ordering":"null", "core_metakey":"metakey", "core_metadesc":"metadesc", "core_catid":"parent_id", "core_xreference":"null", "asset_id":"asset_id"}, "special": {"parent_id":"parent_id","lft":"lft","rgt":"rgt","level":"level","path":"path","extension":"extension","note":"note"}}', 'ContentHelperRoute::getCategoryRoute', '');
SQL;
                        try
                        {
                                $db->setQuery($query);
                                $db->execute();
                        }
                        catch (RuntimeException $e)
                        {
                                JError::raiseError(500, $e->getMessage());
                                return false;  
                        }
                }
                
                // com_hwdmediashare.group
                $query = $db->getQuery(true)
                        ->select('type_id')
                        ->from('#__content_types')
                        ->where($db->quoteName('type_alias') . ' = ' . $db->quote('com_hwdmediashare.group'));
                try
                {
                        $db->setQuery($query);
                        $typeMedia = $db->loadResult();                        
                }
                catch (RuntimeException $e)
                {
			JError::raiseError(500, $e->getMessage());
			return false;                         
                }
                
                if (!$typeMedia)
                {
$query = <<<SQL
INSERT INTO `#__content_types` (`type_title`, `type_alias`, `table`, `rules`, `field_mappings`, `router`, `content_history_options`) VALUES
('Media Group', 'com_hwdmediashare.group', '{"special":{"dbtable":"#__hwdms_groups","key":"id","type":"Group","prefix":"hwdMediaShareTable","config":"array()"},"common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}', '', '{"common":{"core_content_item_id":"id","core_title":"title","core_state":"published","core_alias":"alias","core_created_time":"created","core_modified_time":"modified","core_body":"description", "core_hits":"null","core_publish_up":"publish_up","core_publish_down":"publish_down","core_access":"access", "core_params":"params", "core_featured":"null", "core_metadata":"metadata", "core_language":"language", "core_images":"images", "core_urls":"link", "core_version":"version", "core_ordering":"ordering", "core_metakey":"metakey", "core_metadesc":"metadesc", "core_catid":"catid", "core_xreference":"null", "asset_id":"null"}}', '', '');
SQL;
                        try
                        {
                                $db->setQuery($query);
                                $db->execute();
                        }
                        catch (RuntimeException $e)
                        {
                                JError::raiseError(500, $e->getMessage());
                                return false;  
                        }
                }
                
                // com_hwdmediashare.playlist
                $query = $db->getQuery(true)
                        ->select('type_id')
                        ->from('#__content_types')
                        ->where($db->quoteName('type_alias') . ' = ' . $db->quote('com_hwdmediashare.playlist'));
                try
                {
                        $db->setQuery($query);
                        $typeMedia = $db->loadResult();                        
                }
                catch (RuntimeException $e)
                {
			JError::raiseError(500, $e->getMessage());
			return false;                          
                }
                
                if (!$typeMedia)
                {
$query = <<<SQL
INSERT INTO `#__content_types` (`type_title`, `type_alias`, `table`, `rules`, `field_mappings`, `router`, `content_history_options`) VALUES
('Media Playlist', 'com_hwdmediashare.playlist', '{"special":{"dbtable":"#__hwdms_playlists","key":"id","type":"Playlist","prefix":"hwdMediaShareTable","config":"array()"},"common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}', '', '{"common":{"core_content_item_id":"id","core_title":"title","core_state":"published","core_alias":"alias","core_created_time":"created","core_modified_time":"modified","core_body":"description", "core_hits":"null","core_publish_up":"publish_up","core_publish_down":"publish_down","core_access":"access", "core_params":"params", "core_featured":"null", "core_metadata":"metadata", "core_language":"language", "core_images":"images", "core_urls":"link", "core_version":"version", "core_ordering":"ordering", "core_metakey":"metakey", "core_metadesc":"metadesc", "core_catid":"catid", "core_xreference":"null", "asset_id":"null"}}', '', '');
SQL;
                        try
                        {
                                $db->setQuery($query);
                                $db->execute();
                        }
                        catch (RuntimeException $e)
                        {
                                JError::raiseError(500, $e->getMessage());
                                return false;  
                        }
                }
                
                // com_hwdmediashare.channel
                $query = $db->getQuery(true)
                        ->select('type_id')
                        ->from('#__content_types')
                        ->where($db->quoteName('type_alias') . ' = ' . $db->quote('com_hwdmediashare.channel'));
                try
                {
                        $db->setQuery($query);
                        $typeMedia = $db->loadResult();                        
                }
                catch (RuntimeException $e)
                {
			JError::raiseError(500, $e->getMessage());
			return false;                       
                }
                
                if (!$typeMedia)
                {
$query = <<<SQL
INSERT INTO `#__content_types` (`type_title`, `type_alias`, `table`, `rules`, `field_mappings`, `router`, `content_history_options`) VALUES
('Media User', 'com_hwdmediashare.channel', '{"special":{"dbtable":"#__hwdms_users","key":"id","type":"Channel","prefix":"hwdMediaShareTable","config":"array()"},"common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}', '', '{"common":{"core_content_item_id":"id","core_title":"title","core_state":"published","core_alias":"alias","core_created_time":"created","core_modified_time":"modified","core_body":"description", "core_hits":"null","core_publish_up":"publish_up","core_publish_down":"publish_down","core_access":"access", "core_params":"params", "core_featured":"null", "core_metadata":"metadata", "core_language":"language", "core_images":"images", "core_urls":"link", "core_version":"version", "core_ordering":"ordering", "core_metakey":"metakey", "core_metadesc":"metadesc", "core_catid":"catid", "core_xreference":"null", "asset_id":"null"}}', '', '');
SQL;
                        try
                        {
                                $db->setQuery($query);
                                $db->execute();
                        }
                        catch (RuntimeException $e)
                        {
                                JError::raiseError(500, $e->getMessage());
                                return false;  
                        }
                }
	}
        
        /**
	 * Method to check for and fix potential core HWD database problems.
	 *
	 * @access	public
         * @return      void
	 */
	function databaseFixes()
	{
                $db = JFactory::getDbo();

                //
                // Check activity table has been created.
                //
$query = <<<SQL
CREATE TABLE IF NOT EXISTS `#__hwdms_activity` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `actor` int(11) unsigned NOT NULL,
  `action` int(11) unsigned NOT NULL,
  `target` int(11) unsigned NOT NULL,
  `verb` int(11) unsigned NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `params` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `actor` (`actor`),
  KEY `action` (`action`),
  KEY `target` (`target`),
  KEY `verb` (`verb`),
  KEY `created` (`created`),
  KEY `access` (`access`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=30 ;
SQL;
                try
                {
                        $db->setQuery($query);
                        $db->execute();
                }
                catch (RuntimeException $e)
                {
                        $app->enqueueMessage($e->getMessage());
                        return false;    
                }

                //
                // Check config table.
                //
                $columns = $db->getTableColumns('#__hwdms_config'); 

                // Array holding all queries
                $queries = array();

                // Alter params column.
                $queries[] = 'ALTER TABLE ' . $db->quoteName('#__hwdms_config') . ' CHANGE ' . $db->quoteName('params') . ' ' . $db->quoteName('params') . ' text;';

                // Add id column.
                if (!isset($columns['id']))       $queries[] = 'ALTER TABLE ' . $db->quoteName('#__hwdms_config') . ' ADD COLUMN ' . $db->quoteName('id') . ' int(11) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (id);';

                // Add asset_id column.
                if (!isset($columns['asset_id'])) $queries[] = 'ALTER TABLE ' . $db->quoteName('#__hwdms_config') . ' ADD COLUMN ' . $db->quoteName('asset_id') . ' int(11) unsigned NOT NULL DEFAULT ' . $db->quote('0') . ' AFTER ' . $db->quoteName('id') . ';';

                // Execute the generated queries.
                foreach ($queries as $query)
                {
                        try
                        {
                                $db->setQuery($query);
                                $db->execute();
                        }
                        catch (RuntimeException $e)
                        {
                                $app->enqueueMessage($e->getMessage());
                                return false;                             
                        }
                }
        }                                
}
