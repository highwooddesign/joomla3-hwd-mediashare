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
	 * @access	public
         * @param       string      $parent     The class calling this method.
         * @return      void
	 */
	public function install($parent)
	{
		// echo '<p>' . JText::_('COM_HWDMEDIASHARE_INSTALL_TEXT') . '</p>';
                
                // Define language
                $images = 'Recent images';
                $media = 'Recent media';
                $albums = 'Recent albums';
                $groups = 'Recent groups';
                $users = 'Recent channels';
                $playlists = 'Recent playlists';
                
                com_hwdMediaShareInstallerScript::setupModule('mod_media_images', 'media-discover-leading', $images, '{"count":"6","height":"350"}');
                com_hwdMediaShareInstallerScript::setupModule('mod_media_media', 'media-discover-leading', $media, '{"count":"3","columns":"3","header":"2"}');
                com_hwdMediaShareInstallerScript::setupModule('mod_media_albums', 'media-discover-1', $albums, '{"count":"2","columns":"2","header":"2"}');
                com_hwdMediaShareInstallerScript::setupModule('mod_media_groups', 'media-discover-2', $groups, '{"count":"2","columns":"2","header":"2"}');
                com_hwdMediaShareInstallerScript::setupModule('mod_media_channels', 'media-discover-3', $users, '{"count":"2","columns":"2","header":"2"}');
                com_hwdMediaShareInstallerScript::setupModule('mod_media_playlists', 'media-discover-4', $playlists, '{"count":"2","columns":"2","header":"2"}');
	}

	/**
	 * Method to uninstall the component.
	 *
	 * @access	public
         * @param       string      $parent     The class calling this method.
         * @return      void
	 */
	public function uninstall($parent)
	{
                // echo '<p>' . JText::_('COM_HWDMEDIASHARE_UNINSTALL_TEXT') . '</p>';
	}

	/**
	 * Method to update the component.
	 *
	 * @access	public
         * @param       string      $parent     The class calling this method.
         * @return      void
	 */
	public function update($parent)
	{
		// $parent is the class calling this method
		// echo '<p>' . JText::_('COM_HWDMEDIASHARE_UPDATE_TEXT') . '</p>';
	}

	/**
	 * Method to run before an install/update/uninstall method.
	 *
	 * @access	public
         * @param       string      $type       The type of change (install, update or discover_install).
         * @param       string      $parent     The class calling this method.
         * @return      void
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
	 * @access	public
         * @param       string      $type       The type of change (install, update or discover_install).
         * @param       string      $parent     The class calling this method.
         * @return      void
	 */
	public function postflight($type, $parent)
	{
		// $parent is the class calling this method
		// $type is the type of change (install, update or discover_install)
		// echo '<p>' . JText::_('COM_HWDMEDIASHARE_POSTFLIGHT_' . $type . '_TEXT') . '</p>';
                
                // Remove old files.
                com_hwdMediaShareInstallerScript::removeOldFiles();

                // Add content types.
                com_hwdMediaShareInstallerScript::addContentTypes();

                // Update database.
                com_hwdMediaShareInstallerScript::databaseFixes();
                
                // Check if the HWD menu exists.
                if (!com_hwdMediaShareInstallerScript::checkMenuExists())
                {
                        com_hwdMediaShareInstallerScript::writeMenu();
                }
                
                // Fix any broken menu links.
                com_hwdMediaShareInstallerScript::fixBrokenMenuItems();
	}

        /**
	 * Method to check if hwdMediaShare menu exists.
	 *
	 * @access	public
         * @return      void
	 */
	public function checkMenuExists()
	{
                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                        ->select('COUNT(*)')
                        ->from('#__menu_types')
                        ->where('menutype = ' . $db->quote('hwdmediashare'));
                try
                {
                        $db->setQuery($query);
                        return $db->loadResult() > 0;
                }
                catch (RuntimeException $e)
                {
			JError::raiseError(500, $e->getMessage());
			return false;                        
                }
	}
        
        /**
	 * Method to check if a module is installed.
	 *
	 * @access	public
         * @param       string      $module     The name of the module.
         * @param       string      $parent     The class calling this method.
         * @return      void
	 */
	public function checkModuleInstalled($module)
	{
                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                        ->select('COUNT(*)')
                        ->from('#__extensions')
                        ->where('type = ' . $db->quote('module'))
                        ->where('element = ' . $db->quote($module));
                try
                {
                        $db->setQuery($query);
                        return $db->loadResult() > 0;
                }
                catch (RuntimeException $e)
                {
			JError::raiseError(500, $e->getMessage());
			return false;                            
                }
	}

        /**
	 * Method to create hwdMediaShare menu.
	 *
	 * @access	public
         * @return      void
	 */
        public function writeMenu()
        {
                // Initialise variables.
                $db = JFactory::getDBO();
                
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
			JError::raiseError(500, $e->getMessage());
			return false;                            
                }

                // Add default menu items.
                com_hwdMediaShareInstallerScript::addDefaultMenuItems();
                
                return true;
        }
        
        /**
	 * Method to add default hwdMediaShare menu items.
	 *
	 * @access	public
         * @return      void
	 */
        public function addDefaultMenuItems()
        {
                $db		= JFactory::getDBO();
                $file           = JPATH_ROOT.'/administrator/components/com_hwdmediashare/toolbar.xml';
                $xml            = new SimpleXMLElement($file, null, true);

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
                                        JError::raiseError(500, $e->getMessage());
                                        return false;                            
                                }

                                // Insert menu item.
                                try
                                {
                                        $db->insertObject( '#__menu' , $obj );
                                }
                                catch (RuntimeException $e)
                                {
                                        JError::raiseError(500, $e->getMessage());
                                        return false;                            
                                }                       
                        }
                }

                return true;
        }
        
        /**
	 * Method to fix broken menu items and reassociate them with the hwdMediaShare asset.
	 *
	 * @access	public
         * @return      void
	 */
        public function fixBrokenMenuItems()
        {
                // Get HWD component id.
                $component      = JComponentHelper::getComponent('com_hwdmediashare');
                $component_id   = 0;
                if (is_object($component) && isset($component->id))
                {
                        $component_id = $component->id;
                }

                if ($component_id > 0)
                {
                        // Update the existing menu items.
                        $db = JFactory::getDBO();
                        
                        // Fields to update.
                        $fields = array(
                            $db->quoteName('component_id') . ' = ' . $db->quote($component_id)
                        );

                        // Conditions for which records should be updated.
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
                                JError::raiseError(500, $e->getMessage());
                                return false;                            
                        }
                }
                
                return true;
        }
        
        /**
	 * Method to validate the alias, and prevent duplciates.
	 *
	 * @access	public
         * @return      void
	 */
        public function getAlias($alias)
        {
                // Sanitise the alias.
                jimport('joomla.filter.output');
		$alias = JFilterOutput::stringURLSafe($alias);
                
                // Check for duplicates.
                $db = JFactory::getDBO();
                $query = $db->getQuery(true)
                        ->select('COUNT(*)')
                        ->from('#__menu')
                        ->where($db->quoteName('alias') . '=' . $db->quote($alias));
                try
                {
                        $db->setQuery($query);
                        $duplicate = $db->loadResult();
                }
                catch (RuntimeException $e)
                {
                        JError::raiseError(500, $e->getMessage());
                        return false;                            
                }

		if ($duplicate)
                {                       
                        $alias = $alias . '-media';
                        return com_hwdMediaShareInstallerScript::getAlias($alias);     
                }
                else
                {
                        return $alias;                    
                }
        } 
               
        /**
	 * Method to setup modules for the discover page during initial installation.
	 *
	 * @access	public
         * @return      void
	 */
	public function setupModule($module, $position, $title, $params=null)
	{
                if (com_hwdMediaShareInstallerScript::checkModuleInstalled($module))
                {
                        if (!com_hwdMediaShareInstallerScript::checkModuleConfigured($module, $position))
                        {
                                com_hwdMediaShareInstallerScript::configureModule($module, $position, $title, $params);
                        }
                }
                return true;
	}
        
        /**
	 * Method to check if any modules are configured in a specific position.
	 *
	 * @access	public
         * @return      void
	 */
	public function checkModuleConfigured($module, $position)
	{
		$db = JFactory::getDBO();
                $query = $db->getQuery(true)
                        ->select('COUNT(*)')
                        ->from('#__modules')
                        ->where('module = ' . $db->quote($module))
                        ->where('position = ' . $db->quote($position));
                try
                {                
                        $db->setQuery($query);
                        return $db->loadResult() > 0;
                }
                catch (Exception $e)
                {
                        $this->setError($e->getMessage());
                        return false;
                }
	}
        
        /**
	 * Method to add and configure a new module.
	 *
	 * @access	public
         * @return      void
	 */
	public function configureModule($module, $position, $title, $params=null)
	{
                // Initialise variables.
                $db = JFactory::getDBO();

                // Columns and values to insert.
                $columns = array($db->quoteName('title'), $db->quoteName('note'), $db->quoteName('content'), $db->quoteName('ordering'), $db->quoteName('position'), $db->quoteName('checked_out'), $db->quoteName('checked_out_time'), $db->quoteName('publish_up'), $db->quoteName('publish_down'), $db->quoteName('published'), $db->quoteName('module'), $db->quoteName('access'), $db->quoteName('showtitle'), $db->quoteName('params'), $db->quoteName('client_id'), $db->quoteName('language'));
                $values = array($db->quote($title), $db->quote(''), $db->quote(''), $db->quote('0'), $db->quote($position), $db->quote('0'), $db->quote('0000-00-00 00:00:00'), $db->quote('0000-00-00 00:00:00'), $db->quote('0000-00-00 00:00:00'), $db->quote('1'), $db->Quote($module), $db->quote('1'), $db->quote('1'), $db->quote($params), $db->quote('0'), $db->quote('*'));

                $query = $db->getQuery(true)
                        ->insert($db->quoteName('#__modules'))
                        ->columns($db->quoteName($columns))
                        ->values(implode(',', $values));
                try
                {
                        $db->setQuery($query);
                        $db->execute();
                        $id = $db->insertid();
                }
                catch (RuntimeException $e)
                {
			JError::raiseError(500, $e->getMessage());
			return false;                            
                }

                if ($id > 0)
                {
                        // Setup module to display on all pages
                        $columns = array($db->quoteName('moduleid'), $db->quoteName('menuid'));
                        $values = array($db->quote($id), $db->quote('0'));

                        $query = $db->getQuery(true)
                                ->insert($db->quoteName('#__modules_menu'))
                                ->columns($db->quoteName($columns))
                                ->values(implode(',', $values));
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

                return true;
	}
        
        /**
	 * Method to remove any deprecated files from previous installations.
	 *
	 * @access	public
         * @return      void
	 */
	public function removeOldFiles()
	{
                jimport('joomla.filesystem.file');

                $files = array();
                $files[] = JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables/extension.php';
                $files[] = JPATH_SITE.'/components/com_hwdmediashare/libraries/layouts/media_item_details.php';
                $files[] = JPATH_SITE.'/components/com_hwdmediashare/libraries/layouts/media_item_display.php';
       
                foreach($files as $file)
                {
                        if (file_exists($file))
                        {
                                JFile::delete($file);
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
                
                // com_hwdmediashare.user
                $query = $db->getQuery(true)
                        ->select('type_id')
                        ->from('#__content_types')
                        ->where($db->quoteName('type_alias') . ' = ' . $db->quote('com_hwdmediashare.user'));
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
('Media User', 'com_hwdmediashare.user', '{"special":{"dbtable":"#__hwdms_users","key":"id","type":"UserChannel","prefix":"hwdMediaShareTable","config":"array()"},"common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}', '', '{"common":{"core_content_item_id":"id","core_title":"title","core_state":"published","core_alias":"alias","core_created_time":"created","core_modified_time":"modified","core_body":"description", "core_hits":"null","core_publish_up":"publish_up","core_publish_down":"publish_down","core_access":"access", "core_params":"params", "core_featured":"null", "core_metadata":"metadata", "core_language":"language", "core_images":"images", "core_urls":"link", "core_version":"version", "core_ordering":"ordering", "core_metakey":"metakey", "core_metadesc":"metadesc", "core_catid":"catid", "core_xreference":"null", "asset_id":"null"}}', '', '');
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
			JError::raiseError(500, $e->getMessage());
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
                                JError::raiseError(500, $e->getMessage());
                                return false;                            
                        }
                }
        }                                
}
