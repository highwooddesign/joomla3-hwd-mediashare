<?php
/**
 * @version    SVN $Id: script.php 550 2012-10-05 12:30:04Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Script file of hwdMediaShare component
 */
class com_hwdMediaShareInstallerScript
{
        /**
	 * Stored error
	 */
        protected $error;
        
        /**
	 * Method to install the component
	 *
	 * @return void
	 */
	function install($parent)
	{
		// $parent is the class calling this method
		echo '<p>' . JText::_('COM_HWDMEDIASHARE_INSTALL_TEXT') . '</p>';
                
                // Define language
                $images = JText::_('COM_HWDMS_MOD_RECENT_IMAGES');
                $media = JText::_('COM_HWDMS_MOD_RECENT_MEDIA');
                $albums = JText::_('COM_HWDMS_MOD_RECENT_ALBUMS');
                $groups = JText::_('COM_HWDMS_MOD_RECENT_GROUPS');
                $users = JText::_('COM_HWDMS_MOD_RECENT_CHANNELS');
                $playlists = JText::_('COM_HWDMS_MOD_RECENT_PLAYLISTS');
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
	 * Method to uninstall the component
	 *
	 * @return void
	 */
	function uninstall($parent)
	{
                $confirm = JRequest::getInt('removehwd');
                if ($confirm == 1)
                {
                        // $parent is the class calling this method
                        echo '<p>' . JText::_('COM_HWDMEDIASHARE_UNINSTALL_TEXT') . '</p>';
                }
                else
                {
                        jimport( 'joomla.application.component.helper' );
                        $params = JComponentHelper::getComponent('com_hwdmediashare');
                        ?>
                        <form name="uninstall" action="<?php echo JURI::root( true ); ?>/administrator/index.php" method="post">
                                <input type="hidden" name="option" value="com_installer" />
                                <input type="hidden" name="task" value="manage.remove" />
                                <input type="hidden" name="cid[]" value="<?php echo $params->id; ?>" />
                                <input type="hidden" name="<?php echo JSession::getFormToken(); ?>" value="1" />
                                <input type="hidden" name="removehwd" value="1" />
                        </form>
                        <script language="JavaScript">
                        <!--
                        function confirm_uninstall()
                        {
                                var action= confirm("<?php echo JText::_('COM_HWDMEDIASHARE_UNINSTALL_CONFIRMATION'); ?>\n\n<?php echo JText::_('COM_HWDMEDIASHARE_UNINSTALL_SUPPORT'); ?>");
                                if (action== true)
                                {
                                        document.uninstall.submit();
                                }
                                else
                                {
                                        window.location="<?php echo JURI::root( true ); ?>/administrator/index.php?option=com_installer&view=manage";
                                }
                        }
                        confirm_uninstall();
                        //-->
                        </script>
                        <?php
                        jexit();
                }
	}

	/**
	 * Method to update the component
	 *
	 * @return void
	 */
	function update($parent)
	{
		// $parent is the class calling this method
		echo '<p>' . JText::_('COM_HWDMEDIASHARE_UPDATE_TEXT') . '</p>';
	}

	/**
	 * Method to run before an install/update/uninstall method
	 *
	 * @return void
	 */
	function preflight($type, $parent)
	{
		// $parent is the class calling this method
		// $type is the type of change (install, update or discover_install)
		echo '<p>' . JText::_('COM_HWDMEDIASHARE_PREFLIGHT_' . $type . '_TEXT') . '</p>';
	}

	/**
	 * Method to run after an install/update/uninstall method
	 *
	 * @return void
	 */
	function postflight($type, $parent)
	{
		// $parent is the class calling this method
		// $type is the type of change (install, update or discover_install)
		echo '<p>' . JText::_('COM_HWDMEDIASHARE_POSTFLIGHT_' . $type . '_TEXT') . '</p>';
                
                if (!com_hwdMediaShareInstallerScript::checkMenuExists())
                {
                        com_hwdMediaShareInstallerScript::writeMenu();
                }
                com_hwdMediaShareInstallerScript::fixBrokenMenuItems();
  
                // Remove 'extension' JTable file
                if (file_exists(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables/extension.php'))
                {
                        jimport('joomla.filesystem.file');
                        JFile::delete(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables/extension.php');
                }
                
                if (!$this->error)
                {
                        $parent->getParent()->setRedirectURL('index.php?option=com_hwdmediashare');
                }
	}

        /**
	 * Method to check if hwdMediaShare menu exists
	 *
	 * @return boolean
	 */
	function checkMenuExists()
	{
		$db = JFactory::getDBO();
		$query	= 'SELECT COUNT(*)
                             FROM ' . $db->quoteName( '#__menu_types' ) . '
                             WHERE '. $db->quoteName( 'menutype' ) . '=' . $db->Quote( 'hwdmediashare' );
		$db->setQuery( $query );

		return $db->loadResult() > 0;
	}
        
        /**
	 * Method to check if hwdMediaShare menu exists
	 *
	 * @return boolean
	 */
	function checkModuleInstalled($module)
	{
		$db = JFactory::getDBO();
		$query	= 'SELECT COUNT(*)
                             FROM ' . $db->quoteName( '#__extensions' ) . '
                             WHERE '. $db->quoteName( 'type' ) . '=' . $db->Quote( 'module' ) . '
                             AND '. $db->quoteName( 'element' ) . '=' . $db->Quote( $module );
		$db->setQuery( $query );

		return $db->loadResult() > 0;
	}

        /**
	 * Method to create hwdMediaShare menu
	 *
	 * @return boolean
	 */
        function writeMenu()
        {
                $db = JFactory::getDBO();
                $result = new stdClass();
                $status = true;
                $errorCode ='';
                
                // Define language
                $title = JText::_('COM_HWDMS_MENU_TITLE');
                $desc = JText::_('COM_HWDMS_MENU_DESC');
                // Define language
                $title = 'HWDMediaShare Menu';
                $desc = 'A menu for the HWDMediaShare component';
                
                // Write hwdMediaShare menu
                $query = 'INSERT INTO ' . $db->quoteName( '#__menu_types' ) . ' (' . $db->quoteName('menutype') .',' . $db->quoteName('title') .',' . $db->quoteName('description') .')
                          VALUES ( ' . $db->Quote( 'hwdmediashare' ) . ',' . $db->Quote( $title ) . ',' . $db->Quote( $desc ) . ')';
                $db->setQuery( $query );
                $db->Query();
                $menuId = $db->insertid();
                if (!$menuId = $db->insertid())
                {
                        $this->error = JText::_('COM_HWDMS_ERROR_FAILED_WRITE_JMENU');
                        return false;
                }

                // Create default menu items because the hwdMediaShare menu doesn't exist.
                $status = com_hwdMediaShareInstallerScript::addDefaultMenuItems();
                if(!$status)
                {
                        $this->error = JText::_('COM_HWDMEDIASHARE_ERROR_FAILED_WRITE_JMENU_ITEMS');
                        return false;
                }
                return true;
        }
        
        /**
	 * Method to add default hwdMediaShare menu items
	 *
	 * @return boolean
	 */
        function addDefaultMenuItems()
        {
                $db		= JFactory::getDBO();
                $file           = JPATH_ROOT.'/administrator/components/com_hwdmediashare/toolbar.xml';

                // Get Joomla version
                $version = new JVersion();
                ($version->RELEASE >= 3.0 ? JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models') : JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models'));
                $model = ($version->RELEASE >= 3.0 ? JModelLegacy::getInstance('Media', 'hwdMediaShareModel', array('ignore_request' => true)) : JModelLegacy::getInstance('Media', 'hwdMediaShareModel', array('ignore_request' => true)));

                
                // Joomla 1.5
                //$parser =& JFactory::getXMLParser('Simple');
                //$parser->loadFile( $file );
                //$items = $parser->document->getElementByPath( 'items' );
                
                // Joomla 2.5
                if ($version->RELEASE < 3.0)
                {
                    $xml =& JFactory::getXML($file);
                    if ($xml) 
                    {
                        $items = $xml->items;
                        foreach( $items->children() as $item )
                        {
                            $obj		= new stdClass();

                            // Retrieve the menu name
                            // $element	= $item->getElementByPath( 'name' );
                            $element	= $item->name;
                            $obj->title	= !empty( $element ) ? $element->data() : '';

                            // @TODO: check for existing aliases
                            // Retrieve the menu alias
                            // $element	= $item->getElementByPath( 'alias' );
                            $element	= $item->alias;
                            $obj->alias	= !empty( $element ) ? com_hwdMediaShareInstallerScript::getAlias($element->data()) : '';
                            $obj->path	= $obj->alias;

                            // Retrieve the menu link
                            // $element	= $item->getElementByPath( 'link' );
                            $element	= $item->link;
                            $obj->link	= !empty( $element ) ? $element->data() : '';

                            $obj->menutype	= 'hwdmediashare';
                            $obj->type	= 'component';
                            $obj->published	= 1;
                            $obj->parent_id	= 1;
                            $obj->level	= 1;
                            $obj->ordering	= $i;
                            $obj->access	= 1;

                            // $childs	= $item->getElementByPath( 'childs' );
                            $childs         = $item->childs;

                            $obj->language	= '*';

                            $query 	= 'SELECT ' . $db->quoteName( 'rgt' ) . ' '
                                            . 'FROM ' . $db->quoteName( '#__menu' ) . ' '
                                            . 'ORDER BY ' . $db->quoteName( 'rgt' ) . ' DESC LIMIT 1';
                            $db->setQuery( $query );
                            $obj->lft 	= $db->loadResult() + 1;
                            $totalchild     = $childs ? count($childs->children()) : 0;
                            $obj->rgt	= $obj->lft + $totalchild * 2 + 1;

                            $db->insertObject( '#__menu' , $obj );
                            if ($db->getErrorNum())
                            {
                                    $this->error = $db->getErrorNum();
                                    return false;
                            }
                            $parentId	= $db->insertid();

                            $i++;                            
                        }
                    }
                } 
                // Joomla 3.0
                else
                {
                    // Joomla 3.0
                    $xml = new SimpleXMLElement($file,null,true);
                   
                    if ($xml) 
                    {
                        $items = $xml->items;
                        foreach( $items->children() as $item )
                        {
                            $obj		= new stdClass();

                            // Retrieve the menu name
                            $obj->title	= !empty( $item->name ) ? "$item->name" : '';

                            // Retrieve the menu alias
                            //$obj->alias	= !empty( $item->alias ) ? com_hwdMediaShareInstallerScript::getAlias("$item->alias") : '';
                            $obj->alias	= !empty( $item->alias ) ? "$item->alias" : '';
                            $obj->path	= $obj->alias;

                            // Retrieve the menu link
                            $obj->link	= !empty( $item->link ) ? "$item->link" : '';

                            $obj->menutype	= 'hwdmediashare';
                            $obj->type	= 'component';
                            $obj->published	= 1;
                            $obj->parent_id	= 1;
                            $obj->level	= 1;
                            $obj->access	= 1;

                            $childs         = $item->childs;

                            $obj->language	= '*';

                            $query 	= 'SELECT ' . $db->quoteName( 'rgt' ) . ' '
                                            . 'FROM ' . $db->quoteName( '#__menu' ) . ' '
                                            . 'ORDER BY ' . $db->quoteName( 'rgt' ) . ' DESC LIMIT 1';
                            $db->setQuery( $query );
                            $obj->lft 	= $db->loadResult() + 1;
                            $totalchild     = $childs ? count($childs->children()) : 0;
                            $obj->rgt	= $obj->lft + $totalchild * 2 + 1;
                            
                            $db->insertObject( '#__menu' , $obj );
                            if ($db->getErrorNum())
                            {
                                    $this->error = $db->getErrorNum();
                                    return false;
                            }
                            $parentId	= $db->insertid();

                            $i++;                            
                        }
                    }
                }
                
                return true;
        }
        
        /**
	 * Method to fix any broken menu items and associate them with the hwdMediaShare asset
	 *
	 * @return boolean
	 */
        function fixBrokenMenuItems()
        {
                // Get new component id.
                $component      = JComponentHelper::getComponent('com_hwdmediashare');
                $component_id   = 0;
                if (is_object($component) && isset($component->id))
                {
                        $component_id = $component->id;
                }

                if ($component_id > 0)
                {
                        // Update the existing menu items.
                        $db 	= JFactory::getDBO();

                        $query 	= 'UPDATE ' . $db->quoteName( '#__menu' ) . ' '
                                . 'SET '.$db->quoteName('component_id').'=' . $db->Quote( $component_id ) . ' '
                                . 'WHERE ' . $db->quoteName('link') .' LIKE ' . $db->Quote('%option=com_hwdmediashare%');

                        $db->setQuery( $query );
                        $db->query();

                        if($db->getErrorNum())
                        {
                                $this->error = $db->getErrorNum();
                                return false;
                        }
                }
                return true;
        }
        
        /**
	 * Method to check for valid aliases (no duplicates)
	 *
	 * @return boolean
	 */
        function getAlias($alias)
        {
                // Sanitise the alias
                jimport( 'joomla.filter.output' );
		$alias = JFilterOutput::stringURLSafe($alias);
                
                // Check for duplicates
                $db = JFactory::getDBO();
		$query	= 'SELECT COUNT(*)
                             FROM ' . $db->quoteName( '#__menu' ) . '
                             WHERE '. $db->quoteName( 'alias' ) . '=' . $db->Quote( $alias );
		$db->setQuery( $query );

		if ($db->loadResult() > 0)
                {                       
                        // If duplicate, append with additioanl text
                        // @TODO: add recursive check
                        $alias = $alias.'-media';
                        return com_hwdMediaShareInstallerScript::getAlias($alias);     
                }
                else
                {
                        // Otherwise return sanitised alias
                        return $alias;                    
                }
        } 
               
        /**
	 * Method to check if hwdMediaShare menu exists
	 *
	 * @return boolean
	 */
	function setupModule($module, $position, $title, $params=null)
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
	 * Method to check if hwdMediaShare menu exists
	 *
	 * @return boolean
	 */
	function checkModuleConfigured($module, $position)
	{
		$db = JFactory::getDBO();
		$query	= 'SELECT COUNT(*)
                             FROM ' . $db->quoteName( '#__modules' ) . '
                             WHERE '. $db->quoteName( 'module' ) . '=' . $db->Quote( $module ) . '
                             AND '. $db->quoteName( 'position' ) . '=' . $db->Quote( $position );
		$db->setQuery( $query );

		return $db->loadResult() > 0;
	}
        
        /**
	 * Method to check if hwdMediaShare menu exists
	 *
	 * @return boolean
	 */
	function configureModule($module, $position, $title, $params=null)
	{
		$db = JFactory::getDBO();
		$query	= 'INSERT INTO ' . $db->quoteName( '#__modules' ) . ' ('. $db->quoteName( 'title' ) . ', ' . $db->quoteName( 'note' ) . ', ' . $db->quoteName( 'content' ) . ', ' . $db->quoteName( 'ordering' ) . ', ' . $db->quoteName( 'position' ) . ', ' . $db->quoteName( 'checked_out' ) . ', ' . $db->quoteName( 'checked_out_time' ) . ', ' . $db->quoteName( 'publish_up' ) . ', ' . $db->quoteName( 'publish_down' ) . ', ' . $db->quoteName( 'published' ) . ', ' . $db->quoteName( 'module' ) . ', ' . $db->quoteName( 'access' ) . ', ' . $db->quoteName( 'showtitle' ) . ', ' . $db->quoteName( 'params' ) . ', ' . $db->quoteName( 'client_id' ) . ', ' . $db->quoteName( 'language' ) . ') VALUES
                           (' . $db->Quote( $title ) . ', ' . $db->Quote( '' ) . ', ' . $db->Quote( '' ) . ', ' . $db->Quote( '0' ) . ', ' . $db->Quote( $position ) . ', ' . $db->Quote( '0' ) . ', ' . $db->Quote( '0000-00-00 00:00:00' ) . ', ' . $db->Quote( '0000-00-00 00:00:00' ) . ', ' . $db->Quote( '0000-00-00 00:00:00' ) . ', ' . $db->Quote( '1' ) . ', ' . $db->Quote($module ) . ', ' . $db->Quote( '1' ) . ', ' . $db->Quote( '1' ) . ', ' . $db->Quote( $params ) . ', ' . $db->Quote( '0' ) . ', ' . $db->Quote( '*' ) . ');';

                $db->setQuery( $query );
		
                if ($db->query())
                {
                        // Setup module to display on all pages
                        $id = $db->insertid();
                        if ($id > 0)
                        {
                                $query	= 'INSERT INTO ' . $db->quoteName( '#__modules_menu' ) . ' ('. $db->quoteName( 'moduleid' ) . ', ' . $db->quoteName( 'menuid' ) . ') VALUES
                                        (' . $db->Quote( $id ) . ', ' . $db->Quote( '0' ) . ');';

                                $db->setQuery( $query );
                                $db->query();
                                
                                if($db->getErrorNum())
                                {
                                        $this->error = $db->getErrorNum();
                                        return false;
                                }
                        }         
                }

                return true;
	}
}
