<?php
/**
 * @package     Joomla.administrator
 * @subpackage  Component.hwdmigrator
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class hwdMigratorModelMigrate extends JModelAdmin
{
	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	0.1
	 */
	public function getForm($data = array(), $loadData = true)
	{
		parent::getForm($data, $loadData);
	}

        /**
	 * Method to process an embed code import
         *
	 * @since   0.1
	 */
	public function videoItems()
	{
                $db = JFactory::getDBO();
                $date = JFactory::getDate();
                $user = JFactory::getUser();
                $app = JFactory::getApplication();

                // Require hwdMediaShare factory
                JLoader::register('hwdMediaShareFactory', JPATH_ROOT.'/components/com_hwdmediashare/libraries/factory.php');

                $db->setQuery( 'SHOW TABLES' );
                $tables = $db->loadColumn();

                foreach ($tables as $table)
                {
                        if ($table == $app->getCfg( 'dbprefix' ).'hwdvidsvideos')
                        {
                                $query = "
                                    SELECT *
                                    FROM ".$db->quoteName('#__hwdvidsvideos')."
                                ";
                                $db->setQuery($query);
                                $items = $db->loadObjectList();
                        }
                }

                if (!isset($items) || count($items) == 0)
                {
                        return true;
                }
                else
                {
                        //First check if jpg is allowed for later
                        $db = JFactory::getDBO();
                        $query = $db->getQuery(true);
                        $query->select('id');
                        $query->from('#__hwdms_ext');
                        $query->where($db->quoteName('ext').' = '.$db->quote('jpg'));
                        $db->setQuery($query);
                        $jpg_ext_id = $db->loadResult();
                    
			foreach ($items as $item)
                        {
                                $import = false;

				$query = "
                                    SELECT *
                                    FROM ".$db->quoteName('#__hwdms_migrator')."
                                    WHERE ".$db->quoteName('element_type')." = ".$db->quote('1')."
                                    AND ".$db->quoteName('element_id')." = ".$db->quote($item->id)."
                                ";
                                $db->setQuery($query);
                                $record = $db->loadObject();

                                if (!isset($record->status))
                                {
                                        $query = "INSERT INTO ".$db->quoteName('#__hwdms_migrator')." (".$db->quoteName('element_type').",".$db->quoteName('element_id').")
                                                  VALUES (".$db->quote('1').",".$db->quote($item->id).")";
                                        $db->setQuery($query);
                                        $db->query();
                                        $record_id = $db->insertid();

                                        $import = true;
                                }
                                elseif ($record->status == 0)
                                {
                                        $record_id = $record->id;

                                        $import = true;
                                }
                                else
                                {
                                        //echo "already imported $item->id<br>";
                                }

                                $data = array();
                                $ext_id = '';
                                $duration = '';

                                switch ($item->video_type)
                                {
                                    case 'youtube.com':
                                    case 'youtu.be':
                                    	$vId = explode(",", $item->video_id);
                                   	$item->video_id = $vId[0];
                                        $data['media_type'] = '4';
                                        $data['type'] = '2';
                                        $data['source'] = 'http://www.youtube.com/watch?v='.$item->video_id;
                                        $data['thumbnail'] = 'http://i1.ytimg.com/vi/'.$item->video_id.'/hqdefault.jpg';
                                        break;
                                    case 'vimeo.com':
                                    	$vId = explode(",", $item->video_id);
                                   	$item->video_id = $vId[0];
                                        $data['media_type'] = '4';
                                        $data['type'] = '2';
                                        $data['source'] = 'http://vimeo.com/'.$item->video_id;
                                        JLoader::register('plgHwdmediashareRemote_vimeocom', JPATH_PLUGINS.'/hwdmediashare/remote_vimeocom/remote_vimeocom.php');
                                        if (class_exists('plgHwdmediashareRemote_vimeocom'))
                                        {
                                                $importer = call_user_func(array('plgHwdmediashareRemote_vimeocom', 'getInstance'));
                                                $importer->_url = $data['source'];
                                                $data['thumbnail'] = $importer->getThumbnail();
                                        }
                                        break;
                                    case 'dailymotion.com':
                                    	$vId = explode(",", $item->video_id);
                                   	$item->video_id = $vId[0];
                                        $data['media_type'] = '4';
                                        $data['type'] = '2';
                                        $data['source'] = 'http://www.dailymotion.com/video/'.$item->video_id;
                                        JLoader::register('plgHwdmediashareRemote_dailymotioncom', JPATH_PLUGINS.'/hwdmediashare/remote_dailymotioncom/remote_dailymotioncom.php');
                                        if (class_exists('plgHwdmediashareRemote_dailymotioncom'))
                                        {
                                                $importer = call_user_func(array('plgHwdmediashareRemote_dailymotioncom', 'getInstance'));
                                                $importer->_url = $data['source'];
                                                $data['thumbnail'] = $importer->getThumbnail();
                                        }
                                        break;
                                    case 'blip.tv':
                                        $vId = explode(",", $item->video_id);
                                        $item->video_id = preg_replace("/[^a-zA-Z0-9]/", "", $vId[0]);
                                        $url = 'http://blip.tv/file/'.$item->video_id;

                                        // This URL is out of date, so follow redirects to get the new URL
                                        // Thanks: http://codeaid.net/php/get-the-last-effective-url-from-a-series-of-redirects-for-the-given-url
                                        $curl = curl_init($url);
                                        curl_setopt_array($curl, array(
                                            CURLOPT_RETURNTRANSFER  => true,
                                            CURLOPT_FOLLOWLOCATION  => true,
                                        ));

                                        // Execute the request
                                        $result = curl_exec($curl);

                                        // Fail if the request was not successful
                                        if ($result === false) {
                                            curl_close($curl);
                                            return null;
                                        }

                                        // Extract the target url
                                        $redirectUrl = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
                                        curl_close($curl);

                                        $data['media_type'] = '4';
                                        $data['type'] = '2';
                                        $data['source'] = $redirectUrl;
                                        JLoader::register('plgHwdmediashareRemote_bliptv', JPATH_PLUGINS.'/hwdmediashare/remote_bliptv/remote_bliptv.php');
                                        if (class_exists('plgHwdmediashareRemote_bliptv'))
                                        {
                                                $importer = call_user_func(array('plgHwdmediashareRemote_bliptv', 'getInstance'));
                                                $importer->_url = $data['source'];
                                                $data['thumbnail'] = $importer->getThumbnail();
                                        }
                                        break;
                                    case 'veoh.com':
                                    	$vId = explode(",", $item->video_id);
                                   	$item->video_id = $vId[0];
                                        $data['media_type'] = '4';
                                        $data['type'] = '2';
                                        $data['source'] = 'http://www.veoh.com/watch/'.$item->video_id;
                                        JLoader::register('plgHwdmediashareRemote_veohcom', JPATH_PLUGINS.'/hwdmediashare/remote_veohcom/remote_veohcom.php');
                                        if (class_exists('plgHwdmediashareRemote_veohcom'))
                                        {
                                                $importer = call_user_func(array('plgHwdmediashareRemote_veohcom', 'getInstance'));
                                                $importer->_url = $data['source'];
                                                $data['thumbnail'] = $importer->getThumbnail();
                                        }
                                        break;                                        
                                    case 'remote':
                                    	$vId = explode(",", $item->video_id);
                                        $data['media_type'] = '4';
                                        $data['type'] = '7';
                                        if (isset($vId[0]) && !empty($vId[0]))
                                        {
                                                $pos = strpos($vId[0], "http://");
                                                if ($pos === false)
                                                {
                                                        continue;
                                                }
                                                else
                                                {
                                                        $data['source'] = @$vId[0];
                                                }
                                        }
                                        if (isset($vId[1]) && !empty($vId[1]))
                                        {
                                                $pos = strpos($vId[1], "http://");
                                                if ($pos === false)
                                                {
                                                        continue;
                                                }
                                                else
                                                {
                                                        $data['thumbnail'] = @$vId[1];
                                                }
                                        }
                                        break;
                                    case 'local':
                                    case 'mp4':
                                        $data['media_type'] = '';
                                        $data['type'] = '1';
                                        $data['source'] = '';
                                        // Get source path
                                        if (file_exists(JPATH_SITE.'/hwdvideos/uploads/'.$item->video_id.'.mp4'))
                                        {
                                                $source = JPATH_SITE.'/hwdvideos/uploads/'.$item->video_id.'.mp4';
                                                $ext = 'mp4';
                                        }
                                        elseif (file_exists(JPATH_SITE.'/hwdvideos/uploads/'.$item->video_id.'.flv'))
                                        {
                                                $source = JPATH_SITE.'/hwdvideos/uploads/'.$item->video_id.'.flv';
                                                $ext = 'flv';
                                        }
                                        else
                                        {
                                                // Skip file if source doesn't exist
                                                continue;
                                        }
                                        //First check for allowed extension
                                        $db = JFactory::getDBO();
                                        $query = $db->getQuery(true);
                                        $query->select('id');
                                        $query->from('#__hwdms_ext');
                                        $query->where($db->quoteName('ext').' = '.$db->quote($ext));
                                        $db->setQuery($query);
                                        $ext_id = $db->loadResult();
                                        if ( $ext_id == 0 )
                                        {
                                                continue;
                                       	}
                                        // Get duration
                                        $duration = (int) $this->time2seconds($item->video_length);                                       
                                        break;
                                    case 'seyret':
                                        $data['media_type'] = '';
                                        $data['type'] = '';
                                        $data['source'] = '';

                                    	// Get seyret information
                                        $vId = explode(",", $item->video_id);

                                        if ($vId[0] == 'youtube.com')
                                        {
                                                $item->video_id = $vId[0];
                                                $data['media_type'] = '4';
                                                $data['type'] = '2';
                                                $data['source'] = 'http://www.youtube.com/watch?v='.$vId[1];
                                                $data['thumbnail'] = 'http://i1.ytimg.com/vi/'.$vId[1].'/hqdefault.jpg';
                                                $import = true;
                                                break;
                                        }
                                        elseif ($vId[0] == 'local')
                                        {
                                                // Try to convert to a path
                                                $path = str_replace($_SERVER["SERVER_NAME"], JPATH_SITE, $vId[1]);
                                        }
                                        $import = false;
                                        break;
                                    default:
                                        $import = false;
                                }

                                if ($import)
                                {
                                        // Require hwdMediaShare factory
                                        JLoader::register('hwdMediaShareFactory', JPATH_ROOT.'/components/com_hwdmediashare/libraries/factory.php');

                                        JTable::addIncludePath(JPATH_SITE.'/administrator/components/com_hwdmediashare/tables');
                                        $hwdms_media = JTable::getInstance('Media', 'hwdMediaShareTable');

                                        $data['id'] = $item->id;                                        
                                        //$data['id'] = 0;
                                        $data['key'] = hwdMediaShareFactory::generateKey();
                                        $data['asset_id'] = '';
                                        $data['ext_id'] = $ext_id;
                                        $data['title'] = $item->title;
                                        $data['alias'] = $this->getAlias(JFilterOutput::stringURLSafe($item->title), 'hwdms_media', 'media-');
                                        $data['description'] = $item->description;
                                        $data['storage'] = '';
                                        $data['duration'] = $duration;
                                        $data['streamer'] = '';
                                        $data['file'] = '';
                                        $data['embed_code'] = '';
                                        $data['thumbnail_ext_id'] = '';
                                        $data['location'] = '';
                                        $data['private'] = '';
                                        $data['likes'] = '0';
                                        $data['dislikes'] = '0';
                                        $data['status'] = '1';
                                        $data['published'] = (int) $item->published;
                                        $data['featured'] = (int) $item->featured;
                                        $data['checked_out'] = '';
                                        $data['checked_out_time'] = '';
                                        $data['access'] = '1';
                                        $data['download'] = '1';
                                        $data['params'] = '';
                                        $data['ordering'] = (int) $item->ordering;
                                        $data['created_user_id'] = (int) $item->user_id;
                                        $data['created_user_id_alias'] = '';
                                        $data['created'] = $item->date_uploaded;
                                        $data['publish_up'] = $item->date_uploaded;
                                        $data['publish_down'] = "0000-00-00 00:00:00";
                                        $data['modified_user_id'] = $user->id;
                                        $data['modified'] = $date->format('Y-m-d H:i:s');
                                        $data['hits'] = (int) $item->number_of_views;
                                        $data['language'] = '*';
                                        
// Replace the above bind and store methods with the following to force the same ID during migration
// Create and populate an object
$profile = JArrayHelper::toObject($data);
if ($profile->id == 0)
{
	continue;
}
else
{
	if ($hwdms_media->load($profile->id))
	{
		continue;
	}
}

// Insert the object into the user profile table.
$result = JFactory::getDbo()->insertObject('#__hwdms_media', $profile);
if (!$result)
{
	continue;
}
if (!$hwdms_media->load($profile->id))
{
	continue;
}

                                        if ($item->video_type == "local" || $item->video_type == "mp4")
                                        {
                                                if (empty($hwdms_media->ext_id) || $hwdms_media->ext_id == 0)
                                                {
                                                        // For some reason the extension ID is empty so we need to skip this entry
                                                        $hwdms_media->delete($hwdms_media->id);
                                                        continue;  
                                                }
                                            
                                                // Get destination path
                                                hwdMediaShareFactory::load('files');
                                                hwdMediaShareFiles::getLocalStoragePath();

                                                //Import filesystem libraries. Perhaps not necessary, but does not hurt
                                                jimport('joomla.filesystem.file');

                                                $folders = hwdMediaShareFiles::getFolders($hwdms_media->key);
                                                hwdMediaShareFiles::setupFolders($folders);

                                                //Clean up filename to get rid of strange characters like spaces etc
                                                $filename = hwdMediaShareFiles::getFilename($hwdms_media->key, '1');

                                                $dest = hwdMediaShareFiles::getPath($folders, $filename, $ext);
                                                if (JFile::copy($source, $dest))
                                                {
                                                        if (file_exists($dest))
                                                        {
                                                                    hwdMediaShareFactory::load('files');
                                                                    hwdMediaShareFiles::add($hwdms_media, 1);
                                                        }
                                                        else
                                                        {
                                                                    // Couldn't copy the file so we need to skip this item, not logging it
                                                                    // in the migrator and removing the listing from HWDMediaShare.
                                                                    $hwdms_media->delete($hwdms_media->id);
                                                                    continue;
                                                        }
                                                }
                                                else
                                                {
                                                        // Couldn't copy the file so we need to skip this item, not logging it
                                                        // in the migrator and removing the listing from HWDMediaShare.
                                                        $hwdms_media->delete($hwdms_media->id);
                                                        continue;
                                                }
                                                // Get smaller thumbnail
                                                if (file_exists(JPATH_SITE.'/hwdvideos/thumbs/'.$item->video_id.'.jpg'))
                                                {
                                                        $ext = 'jpg';
                                                        $filename = hwdMediaShareFiles::getFilename($hwdms_media->key, '3');
                                                        $source = JPATH_SITE.'/hwdvideos/thumbs/'.$item->video_id.'.jpg';
                                                        $dest = hwdMediaShareFiles::getPath($folders, $filename, $ext);
                                                        if (JFile::copy($source, $dest))
                                                        {
                                                                hwdMediaShareFactory::load('files');
                                                                hwdMediaShareFiles::add($hwdms_media,'3');
                                                        }
                                                }
                                                // Get larger thumbnail
                                                if (file_exists(JPATH_SITE.'/hwdvideos/thumbs/l_'.$item->video_id.'.jpg'))
                                                {
                                                        $ext = 'jpg';
                                                        $filename = hwdMediaShareFiles::getFilename($hwdms_media->key, '5');
                                                        $source = JPATH_SITE.'/hwdvideos/thumbs/l_'.$item->video_id.'.jpg';
                                                        $dest = hwdMediaShareFiles::getPath($folders, $filename, $ext);
                                                        if (JFile::copy($source, $dest))
                                                        {
                                                                hwdMediaShareFactory::load('files');
                                                                hwdMediaShareFiles::add($hwdms_media,'5');
                                                        }
                                                }
                                        }
                                        else
                                        {
                                                // Get destination path
                                                hwdMediaShareFactory::load('files');
                                                hwdMediaShareFiles::getLocalStoragePath();

                                                //Import filesystem libraries. Perhaps not necessary, but does not hurt
                                                jimport('joomla.filesystem.file');

                                                $folders = hwdMediaShareFiles::getFolders($hwdms_media->key);
                                                hwdMediaShareFiles::setupFolders($folders);

                                                // Get custom thumbnail
                                                $source = '';
                                                $dest = '';
                                                if (file_exists(JPATH_SITE.'/hwdvideos/thumbs/l_tp-'.$item->id.'.jpg') && $jpg_ext_id > 0)
                                                {
                                                        $ext = 'jpg';
                                                        $filename = hwdMediaShareFiles::getFilename($hwdms_media->key, '10');
                                                        $source = JPATH_SITE.'/hwdvideos/thumbs/l_tp-'.$item->id.'.jpg';
                                                        $dest = hwdMediaShareFiles::getPath($folders, $filename, $ext);
                                                        JFile::copy($source, $dest);
                                                }
                                                elseif (file_exists(JPATH_SITE.'/hwdvideos/thumbs/tp-'.$item->id.'.jpg') && $jpg_ext_id > 0)
                                                {
                                                        $ext = 'jpg';
                                                        $filename = hwdMediaShareFiles::getFilename($hwdms_media->key, '10');
                                                        $source = JPATH_SITE.'/hwdvideos/thumbs/tp-'.$item->id.'.jpg';
                                                        $dest = hwdMediaShareFiles::getPath($folders, $filename, $ext);
                                                        JFile::copy($source, $dest);
                                                }
                                                
                                                if (!empty($dest) && file_exists($dest) && $jpg_ext_id > 0 && $hwdms_media->id > 0)
                                                {
                                                        // Define new custom thumbnail data
                                                        $data = array();
                                                        $data['id'] = $hwdms_media->id;
                                                        $data['thumbnail_ext_id'] = $jpg_ext_id;
                                                        // Bind the data.
                                                        if (!$hwdms_media->bind($data)) {
                                                                $this->setError($hwdms_media->getError());
                                                                return false;
                                                        }
                                                        // Store the data.
                                                        if (!$hwdms_media->store()) {
                                                                $this->setError($hwdms_media->getError());
                                                                return false;
                                                        }                                                    
                                                        hwdMediaShareFactory::load('files');
                                                        hwdMediaShareFiles::add($hwdms_media,'10');
                                                }                                                        
                                        }
                                        
                                        JTable::addIncludePath(JPATH_SITE.'/administrator/components/com_hwdmigrator/tables');
                                        $migrator_table = JTable::getInstance('Migrator', 'hwdMigratorTable');

                                        $migrated = array();
                                        ($record_id > 0 ? $migrated['id'] = $record_id : null);
                                        $migrated['element_type'] = 1;
                                        $migrated['element_id'] = $item->id;
                                        $migrated['migration_id'] = $hwdms_media->id;
                                        $migrated['status'] = 1;

                                        // Bind the data.
                                        if (!$migrator_table->bind($migrated)) {
                                                $this->setError($migrator_table->getError());
                                                return false;
                                        }
                                        // Store the data.
                                        if (!$migrator_table->store()) {
                                                $this->setError($migrator_table->getError());
                                                return false;
                                        }
                                        
                                        // If you are running out of disk space during the migration, then you may want to remove the old HWDVideoShare 
                                        // files after each successful import, by using the following code
                                        if ($item->video_type == "local" || $item->video_type == "mp4")
                                        {
                                                if (file_exists($source))
                                                {
                                                            //JFile::delete($source);
                                                }
                                        }                                        
                                }
                        }
                }
                return true;
        }

        /**
	 * Method to process an embed code import
         *
	 * @since   0.1
	 */
	public function videoCategories()
	{
                $db = JFactory::getDBO();
                $app = JFactory::getApplication();

                $db->setQuery( 'SHOW TABLES' );
                $tables = $db->loadColumn();

                foreach ($tables as $table)
                {
                        if ($table == $app->getCfg( 'dbprefix' ).'hwdvidscategories')
                        {
                                $query = "
                                    SELECT *
                                    FROM ".$db->quoteName('#__hwdvidscategories')."
                                ";
                                $db->setQuery($query);
                                $categories = $db->loadObjectList();
                        }
                }

                if (!isset($categories)|| count($categories) == 0)
                {
                        return true;
                }
                else
                {
			foreach ($categories as $category)
                        {
                                $import = false;

				$query = "
                                    SELECT *
                                    FROM ".$db->quoteName('#__hwdms_migrator')."
                                    WHERE ".$db->quoteName('element_type')." = ".$db->quote('2')."
                                    AND ".$db->quoteName('element_id')." = ".$db->quote($category->id)."
                                ";
                                $db->setQuery($query);
                                $record = $db->loadObject();

                                if (!isset($record->status))
                                {
                                        $query = "INSERT INTO ".$db->quoteName('#__hwdms_migrator')." (".$db->quoteName('element_type').",".$db->quoteName('element_id').")
                                                VALUES (".$db->quote('2').",".$db->quote($category->id).")";
                                        $db->setQuery($query);
                                        $db->query();
                                        $record_id = $db->insertid();

                                        $import = true;
                                }
                                elseif ($record->status == 0)
                                {
                                        $record_id = $record->id;

                                        $import = true;
                                }
                                else
                                {
                                        //echo "already imported $category->id<br>";
                                }

                                if ($import)
                                {
                                        JTable::addIncludePath(JPATH_SITE.'/libraries/joomla/database/table');

                                        // Setup sample nature category
                                        $jcat = JTable::getInstance('Category', 'JTable');

                                        $parent_id = $this->getParentId($category, 2);
                                        $level = $this->getLevel($category, 'hwdvidscategories');

                                        if (!$parent_id)
                                        {
                                                // If the parent category does not exist yet, then skip
                                                //echo "no parent id $category->id<br>";
                                                continue;
                                        }

                                        if (!$level)
                                        {
                                                // If the level is not calculated, then skip
                                                //echo "no level $category->id<br>";
                                                continue;
                                        }

                                        $data = array();
                                        $data['id'] = 0;
                                        $data['parent_id'] = $parent_id;
                                        $data['level'] = $level;
                                        $data['extension'] = 'com_hwdmediashare';
                                        $data['title'] = $category->category_name;
                                        $data['alias'] = $this->getAlias(JFilterOutput::stringURLSafe($category->category_name), 'categories', 'video-');
                                        $data['description'] = $category->category_description;
                                        $data['published'] = 1;
                                        $data['access'] = 1;
                                        $data['created_user_id'] = 0;
                                        $data['language'] = '*';

                                        $jcat->setLocation($data['parent_id'], 'last-child');

                                        // Bind the data.
                                        if (!$jcat->save($data)) {
                                                $this->setError($jcat->getError());
                                                return false;
                                        }

                                        JTable::addIncludePath(JPATH_SITE.'/administrator/components/com_hwdmigrator/tables');
                                        $migrator_table = JTable::getInstance('Migrator', 'hwdMigratorTable');

                                        $migrated = array();
                                        ($record_id > 0 ? $migrated['id'] = $record_id : null);
                                        $migrated['element_type'] = 2;
                                        $migrated['element_id'] = $category->id;
                                        $migrated['migration_id'] = $jcat->id;
                                        $migrated['status'] = 1;

                                        // Bind the data.
                                        if (!$migrator_table->save($migrated)) {
                                                $this->setError($migrator_table->getError());
                                                return false;
                                        }
                                }
                        }
                }
                return true;
        }

        /**
	 * Method to process an embed code import
         *
	 * @since   0.1
	 */
	public function videoGroups()
	{
                $db = JFactory::getDBO();
                $date = JFactory::getDate();
                $user = JFactory::getUser();
                $app = JFactory::getApplication();

                $db->setQuery( 'SHOW TABLES' );
                $tables = $db->loadColumn();

                foreach ($tables as $table)
                {
                        if ($table == $app->getCfg( 'dbprefix' ).'hwdvidsgroups')
                        {
                                $query = "
                                    SELECT *
                                    FROM ".$db->quoteName('#__hwdvidsgroups')."
                                ";
                                $db->setQuery($query);
                                $items = $db->loadObjectList();
                        }
                }

                if (!isset($items) || count($items) == 0)
                {
                        return true;
                }
                else
                {
			foreach ($items as $item)
                        {
                                $import = false;

				$query = "
                                    SELECT *
                                    FROM ".$db->quoteName('#__hwdms_migrator')."
                                    WHERE ".$db->quoteName('element_type')." = ".$db->quote('3')."
                                    AND ".$db->quoteName('element_id')." = ".$db->quote($item->id)."
                                ";
                                $db->setQuery($query);
                                $record = $db->loadObject();

                                if (!isset($record->status))
                                {
                                        $query = "INSERT INTO ".$db->quoteName('#__hwdms_migrator')." (".$db->quoteName('element_type').",".$db->quoteName('element_id').")
                                                  VALUES (".$db->quote('3').",".$db->quote($item->id).")";
                                        $db->setQuery($query);
                                        $db->query();
                                        $record_id = $db->insertid();

                                        $import = true;
                                }
                                elseif ($record->status == 0)
                                {
                                        $record_id = $record->id;

                                        $import = true;
                                }
                                else
                                {
                                        //echo "already imported $item->id<br>";
                                }

                                $data = array();

                                if ($import)
                                {
                                        // Require hwdMediaShare factory
                                        JLoader::register('hwdMediaShareFactory', JPATH_ROOT.'/components/com_hwdmediashare/libraries/factory.php');

                                        JTable::addIncludePath(JPATH_SITE.'/administrator/components/com_hwdmediashare/tables');
                                        $hwdms_item = JTable::getInstance('Group', 'hwdMediaShareTable');

                                        $data['id'] = 0;
                                        //$data['asset_id']
                                        //$data['thumbnail_ext_id']
                                        $data['key'] = hwdMediaShareFactory::generateKey();
                                        $data['title'] = $item->group_name;
                                        $data['alias'] = $this->getAlias(JFilterOutput::stringURLSafe($item->group_name), 'hwdms_groups', 'media-');
                                        $data['description'] = $item->group_description;
                                        $data['private'] = 0;
                                        $data['likes'] = 0;
                                        $data['dislikes'] = 0;
                                        $data['status'] = 1;
                                        $data['published'] = 1;
                                        $data['featured'] = (int) $item->featured;
                                        //$data['checked_out']
                                        //$data['checked_out_time']
                                        $data['access'] = 1;
                                        //$data['params']
                                        $data['ordering'] = (int) $item->ordering;
                                        $data['created_user_id'] = (int) $item->adminid;
                                        //$data['created_user_id_alias']
                                        $data['created'] = $item->date;
                                        $data['publish_up'] = "0000-00-00 00:00:00";
                                        //$data['publish_down']
                                        $data['modified_user_id'] = $user->id;
                                        $data['modified'] = $date->format('Y-m-d H:i:s');
                                        //$data['hits']
                                        $data['language'] = '*';

                                        // Bind the data.
                                        if (!$hwdms_item->bind($data)) {
                                                $this->setError($hwdms_item->getError());
                                                return false;
                                        }
                                        // Store the data.
                                        if (!$hwdms_item->store()) {
                                                $this->setError($hwdms_item->getError());
                                                return false;
                                        }

                                        JTable::addIncludePath(JPATH_SITE.'/administrator/components/com_hwdmigrator/tables');
                                        $migrator_table = JTable::getInstance('Migrator', 'hwdMigratorTable');

                                        $migrated = array();
                                        ($record_id > 0 ? $migrated['id'] = $record_id : null);
                                        $migrated['element_type'] = 3;
                                        $migrated['element_id'] = $item->id;
                                        $migrated['migration_id'] = $hwdms_item->id;
                                        $migrated['status'] = 1;

                                        // Bind the data.
                                        if (!$migrator_table->bind($migrated)) {
                                                $this->setError($migrator_table->getError());
                                                return false;
                                        }
                                        // Store the data.
                                        if (!$migrator_table->store()) {
                                                $this->setError($migrator_table->getError());
                                                return false;
                                        }
                                }
                        }
                }
                return true;
        }

        /**
	 * Method to process an embed code import
         *
	 * @since   0.1
	 */
	public function videoPlaylists()
	{
                $db = JFactory::getDBO();
                $date = JFactory::getDate();
                $user = JFactory::getUser();
                $app = JFactory::getApplication();

                $db->setQuery( 'SHOW TABLES' );
                $tables = $db->loadColumn();

                foreach ($tables as $table)
                {
                        if ($table == $app->getCfg( 'dbprefix' ).'hwdvidsplaylists')
                        {
                                $query = "
                                    SELECT *
                                    FROM ".$db->quoteName('#__hwdvidsplaylists')."
                                ";
                                $db->setQuery($query);
                                $items = $db->loadObjectList();
                        }
                }

                if (!isset($items) || count($items) == 0)
                {
                        return true;
                }
                else
                {
			foreach ($items as $item)
                        {
                                $import = false;

				$query = "
                                    SELECT *
                                    FROM ".$db->quoteName('#__hwdms_migrator')."
                                    WHERE ".$db->quoteName('element_type')." = ".$db->quote('4')."
                                    AND ".$db->quoteName('element_id')." = ".$db->quote($item->id)."
                                ";
                                $db->setQuery($query);
                                $record = $db->loadObject();

                                if (!isset($record->status))
                                {
                                        $query = "INSERT INTO ".$db->quoteName('#__hwdms_migrator')." (".$db->quoteName('element_type').",".$db->quoteName('element_id').")
                                                  VALUES (".$db->quote('4').",".$db->quote($item->id).")";
                                        $db->setQuery($query);
                                        $db->query();
                                        $record_id = $db->insertid();

                                        $import = true;
                                }
                                elseif ($record->status == 0)
                                {
                                        $record_id = $record->id;

                                        $import = true;
                                }
                                else
                                {
                                        //echo "already imported $item->id<br>";
                                }

                                $data = array();

                                if ($import)
                                {
                                        // Require hwdMediaShare factory
                                        JLoader::register('hwdMediaShareFactory', JPATH_ROOT.'/components/com_hwdmediashare/libraries/factory.php');

                                        JTable::addIncludePath(JPATH_SITE.'/administrator/components/com_hwdmediashare/tables');
                                        $hwdms_item = JTable::getInstance('Playlist', 'hwdMediaShareTable');

                                        $data['id'] = 0;
                                        //$data['asset_id']
                                        //$data['thumbnail_ext_id']
                                        $data['key'] = hwdMediaShareFactory::generateKey();
                                        $data['title'] = $item->playlist_name;
                                        $data['alias'] = $this->getAlias(JFilterOutput::stringURLSafe($item->playlist_name), 'hwdms_playlists', 'media-');
                                        $data['description'] = $item->playlist_description;
                                        $data['private'] = 0;
                                        $data['likes'] = 0;
                                        $data['dislikes'] = 0;
                                        $data['status'] = 1;
                                        $data['published'] = (int) $item->published;
                                        $data['featured'] = (int) $item->featured;
                                        //$data['checked_out']
                                        //$data['checked_out_time']
                                        $data['access'] = 1;
                                        //$data['params']
                                        $data['ordering'] = 0;
                                        $data['created_user_id'] = (int) $item->user_id;
                                        //$data['created_user_id_alias']
                                        $data['created'] = $item->date_created;
                                        $data['publish_up'] = "0000-00-00 00:00:00";
                                        //$data['publish_down']
                                        $data['modified_user_id'] = $user->id;
                                        $data['modified'] = $date->format('Y-m-d H:i:s');
                                        //$data['hits']
                                        $data['language'] = '*';

                                        // Bind the data.
                                        if (!$hwdms_item->bind($data)) {
                                                $this->setError($hwdms_item->getError());
                                                return false;
                                        }
                                        // Store the data.
                                        if (!$hwdms_item->store()) {
                                                $this->setError($hwdms_item->getError());
                                                return false;
                                        }

                                        JTable::addIncludePath(JPATH_SITE.'/administrator/components/com_hwdmigrator/tables');
                                        $migrator_table = JTable::getInstance('Migrator', 'hwdMigratorTable');

                                        $migrated = array();
                                        ($record_id > 0 ? $migrated['id'] = $record_id : null);
                                        $migrated['element_type'] = 4;
                                        $migrated['element_id'] = $item->id;
                                        $migrated['migration_id'] = $hwdms_item->id;
                                        $migrated['status'] = 1;

                                        // Bind the data.
                                        if (!$migrator_table->bind($migrated)) {
                                                $this->setError($migrator_table->getError());
                                                return false;
                                        }
                                        // Store the data.
                                        if (!$migrator_table->store()) {
                                                $this->setError($migrator_table->getError());
                                                return false;
                                        }
                                }
                        }
                }
                return true;
        }

        /**
	 * Method to process an embed code import
         *
	 * @since   0.1
	 */
	public function photoItems()
	{
                $db = JFactory::getDBO();
                $date = JFactory::getDate();
                $user = JFactory::getUser();
                $app = JFactory::getApplication();

                $db->setQuery( 'SHOW TABLES' );
                $tables = $db->loadColumn();

                foreach ($tables as $table)
                {
                        if ($table == $app->getCfg( 'dbprefix' ).'hwdpsphotos')
                        {
                                $query = "
                                    SELECT *
                                    FROM ".$db->quoteName('#__hwdpsphotos')."
                                ";
                                $db->setQuery($query);
                                $items = $db->loadObjectList();
                        }
                }

                if (!isset($items) || count($items) == 0)
                {
                        return true;
                }
                else
                {
			foreach ($items as $item)
                        {
                                $import = false;

				$query = "
                                    SELECT *
                                    FROM ".$db->quoteName('#__hwdms_migrator')."
                                    WHERE ".$db->quoteName('element_type')." = ".$db->quote('5')."
                                    AND ".$db->quoteName('element_id')." = ".$db->quote($item->id)."
                                ";
                                $db->setQuery($query);
                                $record = $db->loadObject();

                                if (!isset($record->status))
                                {
                                        $query = "INSERT INTO ".$db->quoteName('#__hwdms_migrator')." (".$db->quoteName('element_type').",".$db->quoteName('element_id').")
                                                  VALUES (".$db->quote('5').",".$db->quote($item->id).")";
                                        $db->setQuery($query);
                                        $db->query();
                                        $record_id = $db->insertid();

                                        $import = true;
                                }
                                elseif ($record->status == 0)
                                {
                                        $record_id = $record->id;

                                        $import = true;
                                }
                                else
                                {
                                        //echo "already imported $item->id<br>";
                                }

                                $data = array();

                                if ($import)
                                {
                                        // Get source path
                                        $source = JPATH_SITE.'/hwdphotos/originals/'.$item->user_id.'/'.$item->photo_id.'.'.$item->original_type;
                                        if (!file_exists($source))
                                        {
                                                // Skip file if source doesn;t exist
                                                continue;
                                        }

					// Require hwdMediaShare factory
                                        JLoader::register('hwdMediaShareFactory', JPATH_ROOT.'/components/com_hwdmediashare/libraries/factory.php');

                                        JTable::addIncludePath(JPATH_SITE.'/administrator/components/com_hwdmediashare/tables');
                                        $hwdms_media = JTable::getInstance('Media', 'hwdMediaShareTable');

                                        //First check for allowed extension
                                        $db = JFactory::getDBO();
                                        $query = $db->getQuery(true);
                                        $query->select('id');
                                        $query->from('#__hwdms_ext');
                                        $query->where($db->quoteName('ext').' = '.$db->quote($item->original_type));

                                        $db->setQuery($query);
                                        $ext_id = $db->loadResult();
                                        if ( $ext_id > 0 )
                                        {
                                                $data['id'] = 0;
                                                $data['asset_id'] = '';
                                                $data['ext_id'] = (int) $ext_id;
                                                $data['media_type'] = '';
                                                $data['key'] = hwdMediaShareFactory::generateKey();
                                                $data['title'] = (empty($item->title) ? 'Image' : $item->title);
                                                $data['alias'] = $this->getAlias(JFilterOutput::stringURLSafe($item->title), 'hwdms_media', 'media');
                                                $data['description'] = $item->caption;
                                                $data['type'] = '1';
                                                $data['source'] = '';
                                                $data['storage'] = '';
                                                $data['duration'] = '';
                                                $data['streamer'] = '';
                                                $data['file'] = '';
                                                $data['embed_code'] = '';
                                                $data['thumbnail'] = '';
                                                $data['thumbnail_ext_id'] = '';
                                                $data['location'] = '';
                                                $data['private'] = '';
                                                $data['likes'] = '0';
                                                $data['dislikes'] = '0';
                                                $data['status'] = '1';
                                                $data['published'] = (int) $item->published;
                                                $data['featured'] = (int) $item->featured;
                                                $data['checked_out'] = '';
                                                $data['checked_out_time'] = '';
                                                $data['access'] = '1';
                                                $data['download'] = '1';
                                                $data['params'] = '';
                                                $data['ordering'] = (int) $item->ordering;
                                                $data['created_user_id'] = (int) $item->user_id;
                                                $data['created_user_id_alias'] = '';
                                                $data['created'] = $item->date_uploaded;
                                                $data['publish_up'] = $item->date_uploaded;
                                                $data['publish_down'] = "0000-00-00 00:00:00";
                                                $data['modified_user_id'] = $user->id;
                                                $data['modified'] = $date->format('Y-m-d H:i:s');
                                                $data['hits'] = (int) $item->number_of_views;
                                                $data['language'] = '*';

                                                // Bind the data.
                                                if (!$hwdms_media->bind($data)) {
                                                        $this->setError($hwdms_media->getError());
                                                        return false;
                                                }
                                                // Store the data.
                                                if (!$hwdms_media->store()) {
                                                        $this->setError($hwdms_media->getError());
                                                        return false;
                                                }

                                                // Get destination path
                                                hwdMediaShareFactory::load('files');
                                                hwdMediaShareFiles::getLocalStoragePath();

                                                //Import filesystem libraries. Perhaps not necessary, but does not hurt
                                                jimport('joomla.filesystem.file');

                                                $folders = hwdMediaShareFiles::getFolders($hwdms_media->key);
                                                hwdMediaShareFiles::setupFolders($folders);

                                                //Clean up filename to get rid of strange characters like spaces etc
                                                $filename = hwdMediaShareFiles::getFilename($hwdms_media->key, '1');

                                                $dest = hwdMediaShareFiles::getPath($folders, $filename, $item->original_type);
                                                if (JFile::copy($source, $dest))
                                                {
                                                        hwdMediaShareFactory::load('files');
                                                        hwdMediaShareFiles::add($hwdms_media,'1');
                                                }
                                                else
                                                {
                                                        continue;
                                                }

                                                JTable::addIncludePath(JPATH_SITE.'/administrator/components/com_hwdmigrator/tables');
                                                $migrator_table = JTable::getInstance('Migrator', 'hwdMigratorTable');

                                                $migrated = array();
                                                ($record_id > 0 ? $migrated['id'] = $record_id : null);
                                                $migrated['element_type'] = 5;
                                                $migrated['element_id'] = $item->id;
                                                $migrated['migration_id'] = $hwdms_media->id;
                                                $migrated['status'] = 1;

                                                // Bind the data.
                                                if (!$migrator_table->bind($migrated)) {
                                                        $this->setError($migrator_table->getError());
                                                        return false;
                                                }
                                                // Store the data.
                                                if (!$migrator_table->store()) {
                                                        $this->setError($migrator_table->getError());
                                                        return false;
                                                }
                                        }
                                        else
                                        {
                                                continue;
                                        }
                                }
                        }
                }
                return true;
        }

        /**
	 * Method to process an embed code import
         *
	 * @since   0.1
	 */
	public function photoCategories()
	{
                $db = JFactory::getDBO();
                $app = JFactory::getApplication();

                $db->setQuery( 'SHOW TABLES' );
                $tables = $db->loadColumn();

                foreach ($tables as $table)
                {
                        if ($table == $app->getCfg( 'dbprefix' ).'hwdpscategories')
                        {
                                $query = "
                                    SELECT *
                                    FROM ".$db->quoteName('#__hwdpscategories')."
                                ";
                                $db->setQuery($query);
                                $categories = $db->loadObjectList();
                        }
                }

                if (!isset($categories)|| count($categories) == 0)
                {
                        return true;
                }
                else
                {
			foreach ($categories as $category)
                        {
                                $import = false;

				$query = "
                                    SELECT *
                                    FROM ".$db->quoteName('#__hwdms_migrator')."
                                    WHERE ".$db->quoteName('element_type')." = ".$db->quote('6')."
                                    AND ".$db->quoteName('element_id')." = ".$db->quote($category->id)."
                                ";
                                $db->setQuery($query);
                                $record = $db->loadObject();

                                if (!isset($record->status))
                                {
                                        $query = "INSERT INTO ".$db->quoteName('#__hwdms_migrator')." (".$db->quoteName('element_type').",".$db->quoteName('element_id').")
                                                VALUES (".$db->quote('6').",".$db->quote($category->id).")";
                                        $db->setQuery($query);
                                        $db->query();
                                        $record_id = $db->insertid();

                                        $import = true;
                                }
                                elseif ($record->status == 0)
                                {
                                        $record_id = $record->id;

                                        $import = true;
                                }
                                else
                                {
                                        //echo "already imported $category->id<br>";
                                }

                                if ($import)
                                {
                                        JTable::addIncludePath(JPATH_SITE.'/libraries/joomla/database/table');

                                        // Setup sample nature category
                                        $jcat = JTable::getInstance('Category', 'JTable');

                                        $parent_id = $this->getParentId($category, 6);
                                        $level = $this->getLevel($category, 'hwdpscategories');

                                        if (!$parent_id)
                                        {
                                                // If the parent category does not exist yet, then skip
                                                //echo "no parent id $category->id<br>";
                                                continue;
                                        }

                                        if (!$level)
                                        {
                                                // If the level is not calculated, then skip
                                                //echo "no level $category->id<br>";
                                                continue;
                                        }

                                        $data = array();
                                        $data['id'] = 0;
                                        $data['parent_id'] = $parent_id;
                                        $data['level'] = $level;
                                        $data['extension'] = 'com_hwdmediashare';
                                        $data['title'] = $category->category_name;
                                        $data['alias'] = $this->getAlias(JFilterOutput::stringURLSafe($category->category_name), 'categories', 'photo-');
                                        $data['description'] = $category->category_description;
                                        $data['published'] = 1;
                                        $data['access'] = 1;
                                        $data['created_user_id'] = 0;
                                        $data['language'] = '*';

                                        $jcat->setLocation($data['parent_id'], 'last-child');

                                        // Bind the data.
                                        if (!$jcat->bind($data)) {
                                                $this->setError($jcat->getError());
                                                return false;
                                        }
                                        // Store the data.
                                        if (!$jcat->store()) {
                                                $this->setError($jcat->getError());
                                                return false;
                                        }

                                        JTable::addIncludePath(JPATH_SITE.'/administrator/components/com_hwdmigrator/tables');
                                        $migrator_table = JTable::getInstance('Migrator', 'hwdMigratorTable');

                                        $migrated = array();
                                        ($record_id > 0 ? $migrated['id'] = $record_id : null);
                                        $migrated['element_type'] = 6;
                                        $migrated['element_id'] = $category->id;
                                        $migrated['migration_id'] = $jcat->id;
                                        $migrated['status'] = 1;

                                        // Bind the data.
                                        if (!$migrator_table->bind($migrated)) {
                                                $this->setError($migrator_table->getError());
                                                return false;
                                        }
                                        // Store the data.
                                        if (!$migrator_table->store()) {
                                                $this->setError($migrator_table->getError());
                                                return false;
                                        }
                                }
                        }
                }
                return true;
        }

        /**
	 * Method to process an embed code import
         *
	 * @since   0.1
	 */
	public function photoGroups()
	{
                $db = JFactory::getDBO();
                $date = JFactory::getDate();
                $user = JFactory::getUser();
                $app = JFactory::getApplication();

                $db->setQuery( 'SHOW TABLES' );
                $tables = $db->loadColumn();

                foreach ($tables as $table)
                {
                        if ($table == $app->getCfg( 'dbprefix' ).'hwdpsgroups')
                        {
                                $query = "
                                    SELECT *
                                    FROM ".$db->quoteName('#__hwdpsgroups')."
                                ";
                                $db->setQuery($query);
                                $items = $db->loadObjectList();
                        }
                }

                if (!isset($items) || count($items) == 0)
                {
                        return true;
                }
                else
                {
			foreach ($items as $item)
                        {
                                $import = false;

				$query = "
                                    SELECT *
                                    FROM ".$db->quoteName('#__hwdms_migrator')."
                                    WHERE ".$db->quoteName('element_type')." = ".$db->quote('7')."
                                    AND ".$db->quoteName('element_id')." = ".$db->quote($item->id)."
                                ";
                                $db->setQuery($query);
                                $record = $db->loadObject();

                                if (!isset($record->status))
                                {
                                        $query = "INSERT INTO ".$db->quoteName('#__hwdms_migrator')." (".$db->quoteName('element_type').",".$db->quoteName('element_id').")
                                                  VALUES (".$db->quote('7').",".$db->quote($item->id).")";
                                        $db->setQuery($query);
                                        $db->query();
                                        $record_id = $db->insertid();

                                        $import = true;
                                }
                                elseif ($record->status == 0)
                                {
                                        $record_id = $record->id;

                                        $import = true;
                                }
                                else
                                {
                                        //echo "already imported $item->id<br>";
                                }

                                $data = array();

                                if ($import)
                                {
                                        // Require hwdMediaShare factory
                                        JLoader::register('hwdMediaShareFactory', JPATH_ROOT.'/components/com_hwdmediashare/libraries/factory.php');

                                        JTable::addIncludePath(JPATH_SITE.'/administrator/components/com_hwdmediashare/tables');
                                        $hwdms_item = JTable::getInstance('Group', 'hwdMediaShareTable');

                                        $data['id'] = 0;
                                        //$data['asset_id']
                                        //$data['thumbnail_ext_id']
                                        $data['key'] = hwdMediaShareFactory::generateKey();
                                        $data['title'] = $item->group_name;
                                        $data['alias'] = $this->getAlias(JFilterOutput::stringURLSafe($item->group_name), 'hwdms_groups', 'media-');
                                        $data['description'] = $item->group_description;
                                        $data['private'] = 0;
                                        $data['likes'] = 0;
                                        $data['dislikes'] = 0;
                                        $data['status'] = 1;
                                        $data['published'] = 1;
                                        $data['featured'] = (int) $item->featured;
                                        //$data['checked_out']
                                        //$data['checked_out_time']
                                        $data['access'] = 1;
                                        //$data['params']
                                        $data['ordering'] = (int) $item->ordering;
                                        $data['created_user_id'] = (int) $item->adminid;
                                        //$data['created_user_id_alias']
                                        $data['created'] = $item->date;
                                        $data['publish_up'] = "0000-00-00 00:00:00";
                                        //$data['publish_down']
                                        $data['modified_user_id'] = $user->id;
                                        $data['modified'] = $date->format('Y-m-d H:i:s');
                                        //$data['hits']
                                        $data['language'] = '*';

                                        // Bind the data.
                                        if (!$hwdms_item->bind($data)) {
                                                $this->setError($hwdms_item->getError());
                                                return false;
                                        }
                                        // Store the data.
                                        if (!$hwdms_item->store()) {
                                                $this->setError($hwdms_item->getError());
                                                return false;
                                        }

                                        JTable::addIncludePath(JPATH_SITE.'/administrator/components/com_hwdmigrator/tables');
                                        $migrator_table = JTable::getInstance('Migrator', 'hwdMigratorTable');

                                        $migrated = array();
                                        ($record_id > 0 ? $migrated['id'] = $record_id : null);
                                        $migrated['element_type'] = 7;
                                        $migrated['element_id'] = $item->id;
                                        $migrated['migration_id'] = $hwdms_item->id;
                                        $migrated['status'] = 1;

                                        // Bind the data.
                                        if (!$migrator_table->bind($migrated)) {
                                                $this->setError($migrator_table->getError());
                                                return false;
                                        }
                                        // Store the data.
                                        if (!$migrator_table->store()) {
                                                $this->setError($migrator_table->getError());
                                                return false;
                                        }
                                }
                        }
                }
                return true;
        }

        /**
	 * Method to process an embed code import
         *
	 * @since   0.1
	 */
	public function photoAlbums()
	{
                $db = JFactory::getDBO();
                $date = JFactory::getDate();
                $user = JFactory::getUser();
                $app = JFactory::getApplication();

                $db->setQuery( 'SHOW TABLES' );
                $tables = $db->loadColumn();

                foreach ($tables as $table)
                {
                        if ($table == $app->getCfg( 'dbprefix' ).'hwdpsalbums')
                        {
                                $query = "
                                    SELECT *
                                    FROM ".$db->quoteName('#__hwdpsalbums')."
                                ";
                                $db->setQuery($query);
                                $items = $db->loadObjectList();
                        }
                }

                if (!isset($items) || count($items) == 0)
                {
                        return true;
                }
                else
                {
			foreach ($items as $item)
                        {
                                $import = false;

				$query = "
                                    SELECT *
                                    FROM ".$db->quoteName('#__hwdms_migrator')."
                                    WHERE ".$db->quoteName('element_type')." = ".$db->quote('8')."
                                    AND ".$db->quoteName('element_id')." = ".$db->quote($item->id)."
                                ";
                                $db->setQuery($query);
                                $record = $db->loadObject();

                                if (!isset($record->status))
                                {
                                        $query = "INSERT INTO ".$db->quoteName('#__hwdms_migrator')." (".$db->quoteName('element_type').",".$db->quoteName('element_id').")
                                                  VALUES (".$db->quote('8').",".$db->quote($item->id).")";
                                        $db->setQuery($query);
                                        $db->query();
                                        $record_id = $db->insertid();

                                        $import = true;
                                }
                                elseif ($record->status == 0)
                                {
                                        $record_id = $record->id;

                                        $import = true;
                                }
                                else
                                {
                                        //echo "already imported $item->id<br>";
                                }

                                $data = array();

                                if ($import)
                                {
                                        // Require hwdMediaShare factory
                                        JLoader::register('hwdMediaShareFactory', JPATH_ROOT.'/components/com_hwdmediashare/libraries/factory.php');

                                        JTable::addIncludePath(JPATH_SITE.'/administrator/components/com_hwdmediashare/tables');
                                        $hwdms_item = JTable::getInstance('Album', 'hwdMediaShareTable');

                                        $data['id'] = 0;
                                        //$data['asset_id']
                                        //$data['thumbnail_ext_id']
                                        $data['key'] = hwdMediaShareFactory::generateKey();
                                        $data['title'] = $item->title;
                                        $data['alias'] = $this->getAlias(JFilterOutput::stringURLSafe($item->title), 'hwdms_albums', 'media-');
                                        $data['description'] = $item->description;
                                        $data['private'] = 0;
                                        $data['likes'] = 0;
                                        $data['dislikes'] = 0;
                                        $data['status'] = 1;
                                        $data['published'] = (int) $item->published;
                                        $data['featured'] = (int) $item->featured;
                                        //$data['checked_out']
                                        //$data['checked_out_time']
                                        $data['access'] = 1;
                                        //$data['params']
                                        $data['ordering'] = (int) $item->ordering;
                                        $data['created_user_id'] = (int) $item->user_id;
                                        //$data['created_user_id_alias']
                                        $data['created'] = $item->date_created;
                                        $data['publish_up'] = "0000-00-00 00:00:00";
                                        //$data['publish_down']
                                        $data['modified_user_id'] = $user->id;
                                        $data['modified'] = $date->format('Y-m-d H:i:s');
                                        //$data['hits']
                                        $data['language'] = '*';

                                        // Bind the data.
                                        if (!$hwdms_item->bind($data)) {
                                                $this->setError($hwdms_item->getError());
                                                return false;
                                        }
                                        // Store the data.
                                        if (!$hwdms_item->store()) {
                                                $this->setError($hwdms_item->getError());
                                                return false;
                                        }

                                        JTable::addIncludePath(JPATH_SITE.'/administrator/components/com_hwdmigrator/tables');
                                        $migrator_table = JTable::getInstance('Migrator', 'hwdMigratorTable');

                                        $migrated = array();
                                        ($record_id > 0 ? $migrated['id'] = $record_id : null);
                                        $migrated['element_type'] = 8;
                                        $migrated['element_id'] = $item->id;
                                        $migrated['migration_id'] = $hwdms_item->id;
                                        $migrated['status'] = 1;

                                        // Bind the data.
                                        if (!$migrator_table->bind($migrated)) {
                                                $this->setError($migrator_table->getError());
                                                return false;
                                        }
                                        // Store the data.
                                        if (!$migrator_table->store()) {
                                                $this->setError($migrator_table->getError());
                                                return false;
                                        }
                                }
                        }
                }
                return true;
        }

        /**
	 * Method to process an embed code import
         *
	 * @since   0.1
	 */
	public function matchVideoCategories()
	{
                $db = JFactory::getDBO();
                $date = JFactory::getDate();
                $user = JFactory::getUser();
                $app = JFactory::getApplication();

                // Require hwdMediaShare factory
                JLoader::register('hwdMediaShareFactory', JPATH_ROOT.'/components/com_hwdmediashare/libraries/factory.php');

                $query = "
                    SELECT *
                    FROM ".$db->quoteName('#__hwdms_migrator')."
                    WHERE ".$db->quoteName('element_type')." = ".$db->quote('1')."
                ";
                $db->setQuery($query);
                $items = $db->loadObjectList();

                if (!isset($items) || count($items) == 0)
                {
                        return true;
                }
                else
                {
                        foreach ($items as $item)
                        {
                                $categoryId = array();

                                $import = false;

                                $query = "
                                    SELECT category_id
                                    FROM ".$db->quoteName('#__hwdvidsvideos')."
                                    WHERE ".$db->quoteName('id')." = ".$db->quote($item->element_id)."
                                ";
                                $db->setQuery($query);
                                $categoryId[] = $db->loadResult();

                                $query = "
                                    SELECT categoryid
                                    FROM ".$db->quoteName('#__hwdvidsvideo_category')."
                                    WHERE ".$db->quoteName('videoid')." = ".$db->quote($item->element_id)."
                                ";
                                $db->setQuery($query);
                                $multipleCategories = $db->loadObjectList();

                                foreach ($multipleCategories as $multipleCategory)
                                {
                                	$categoryId[] = $multipleCategory->categoryid;
                                }

                                foreach ($categoryId as $cId)
                                {
                                        if ($cId > 0)
                                        {
                                                $query = "
                                                    SELECT migration_id
                                                    FROM ".$db->quoteName('#__hwdms_migrator')."
                                                    WHERE ".$db->quoteName('element_type')." = ".$db->quote('2')."
                                                    AND ".$db->quoteName('element_id')." = ".$db->quote($cId)."
                                                ";
                                                $db->setQuery($query);
                                                $migratedCategoryId = $db->loadResult();

                                                if ($migratedCategoryId > 0)
                                                {
                                                        $params = new StdClass;
                                                        $params->elementId = $item->migration_id;
                                                        $params->elementType = 1;
                                                        $params->categoryId = $migratedCategoryId;

                                                        hwdMediaShareFactory::load('category');
                                                        hwdMediaShareCategory::saveIndividual($params);
                                                }
                                        }
                                }
                        }
                }  
                return true;
        }

        /**
	 * Method to process an embed code import
         *
	 * @since   0.1
	 */
	public function matchVideoTags()
	{
                $db = JFactory::getDBO();
                $date = JFactory::getDate();
                $user = JFactory::getUser();
                $app = JFactory::getApplication();
                
                // Set for Tag library
                $this->elementType = 1;
                
                // Require hwdMediaShare factory
                JLoader::register('hwdMediaShareFactory', JPATH_ROOT.'/components/com_hwdmediashare/libraries/factory.php');

                $query = "
                    SELECT *
                    FROM ".$db->quoteName('#__hwdms_migrator')."
                    WHERE ".$db->quoteName('element_type')." = ".$db->quote('1')."
                ";
                $db->setQuery($query);
                $items = $db->loadObjectList();

                if (!isset($items) || count($items) == 0)
                {
                        return true;
                }
                else
                {
                        foreach ($items as $item)
                        {
                                $import = false;

                                $query = "
                                    SELECT tags
                                    FROM ".$db->quoteName('#__hwdvidsvideos')."
                                    WHERE ".$db->quoteName('id')." = ".$db->quote($item->element_id)."
                                ";
                                $db->setQuery($query);
                                $tagString = $db->loadResult();

                                if (!empty($tagString))
                                {
                                        $params = new StdClass;
                                        $params->elementType = 1;
                                        $params->elementId = $item->migration_id;
                                        $params->tags = $tagString;

                                        hwdMediaShareFactory::load('tags');
                                        hwdMediaShareTags::save($params);
                                }
                        }
                }
                return true;
        }
        
        /**
	 * Method to process an embed code import
         *
	 * @since   0.1
	 */
	public function matchVideoGroups()
	{
                $db = JFactory::getDBO();
                $date = JFactory::getDate();
                $user = JFactory::getUser();
                $app = JFactory::getApplication();

                // Require hwdMediaShare factory
                JLoader::register('hwdMediaShareFactory', JPATH_ROOT.'/components/com_hwdmediashare/libraries/factory.php');

                $query = "
                    SELECT *
                    FROM ".$db->quoteName('#__hwdms_migrator')."
                    WHERE ".$db->quoteName('element_type')." = ".$db->quote('3')."
                ";
                $db->setQuery($query);
                $items = $db->loadObjectList();

                if (!isset($items) || count($items) == 0)
                {
                        return true;
                }
                else
                {
                        foreach ($items as $item)
                        {
                                $import = false;

                                $query = "
                                    SELECT *
                                    FROM ".$db->quoteName('#__hwdvidsgroup_videos')."
                                    WHERE ".$db->quoteName('groupid')." = ".$db->quote($item->element_id)."
                                ";
                                $db->setQuery($query);
                                $groupMaps = $db->loadObjectList();

                                foreach ($groupMaps as $groupMap)
                                {
                                        $query = "
                                                SELECT migration_id
                                                FROM ".$db->quoteName('#__hwdms_migrator')."
                                                WHERE ".$db->quoteName('element_type')." = ".$db->quote('1')."
                                                AND ".$db->quoteName('element_id')." = ".$db->quote($groupMap->videoid)."
                                        ";
                                        $db->setQuery($query);
                                        $migratedMediaId = $db->loadResult();

					// Check if map already exists
                                        $query = "
                                                SELECT count(*)
                                                FROM ".$db->quoteName('#__hwdms_group_map')."
                                                WHERE ".$db->quoteName('media_id')." = ".$db->quote($migratedMediaId)."
                                                AND ".$db->quoteName('group_id')." = ".$db->quote($item->migration_id)."
                                        ";
                                        $db->setQuery($query);
                                        $mapExists = $db->loadResult();

                                        if ($mapExists == 0)
                                        {
                                                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                                                $table = JTable::getInstance('LinkedGroups', 'hwdMediaShareTable');

                                                // Create an object to bind to the database
                                                $object = new StdClass;
                                                $object->media_id = $migratedMediaId;
                                                $object->group_id = $item->migration_id;
                                                $object->created = $groupMap->date;

                                                if (!$table->bind($object))
                                                {
                                                        return JError::raiseWarning( 500, $table->getError() );
                                                }

                                                if (!$table->store())
                                                {
                                                        JError::raiseError(500, $table->getError() );
                                                }
                                        }

                                }
                        }
                }
                return true;
        }

        /**
	 * Method to process an embed code import
         *
	 * @since   0.1
	 */
	public function matchVideoPlaylists()
	{
                $db = JFactory::getDBO();
                $date = JFactory::getDate();
                $user = JFactory::getUser();
                $app = JFactory::getApplication();

                // Require hwdMediaShare factory
                JLoader::register('hwdMediaShareFactory', JPATH_ROOT.'/components/com_hwdmediashare/libraries/factory.php');

                $query = "
                    SELECT *
                    FROM ".$db->quoteName('#__hwdms_migrator')."
                    WHERE ".$db->quoteName('element_type')." = ".$db->quote('4')."
                ";
                $db->setQuery($query);
                $items = $db->loadObjectList();

                if (!isset($items) || count($items) == 0)
                {
                        return true;
                }
                else
                {
                        foreach ($items as $item)
                        {
                        }
                }
                return true;
        }
        
        /**
	 * Method to process an embed code import
         *
	 * @since   0.1
	 */
	public function matchPhotoCategories()
	{
                $db = JFactory::getDBO();
                $date = JFactory::getDate();
                $user = JFactory::getUser();
                $app = JFactory::getApplication();
                
                // Require hwdMediaShare factory
                JLoader::register('hwdMediaShareFactory', JPATH_ROOT.'/components/com_hwdmediashare/libraries/factory.php');

                $query = "
                    SELECT *
                    FROM ".$db->quoteName('#__hwdms_migrator')."
                    WHERE ".$db->quoteName('element_type')." = ".$db->quote('5')."
                ";
                $db->setQuery($query);
                $items = $db->loadObjectList();

                if (!isset($items) || count($items) == 0)
                {
                        return true;
                }
                else
                {
                        foreach ($items as $item)
                        {
                                $import = false;

                                $query = "
                                    SELECT category_id
                                    FROM ".$db->quoteName('#__hwdpsphotos')."
                                    WHERE ".$db->quoteName('id')." = ".$db->quote($item->element_id)."
                                ";
                                $db->setQuery($query);
                                $categoryId = $db->loadResult();

                                if ($categoryId > 0)
                                {
                                        $query = "
                                                SELECT migration_id
                                                FROM ".$db->quoteName('#__hwdms_migrator')."
                                                WHERE ".$db->quoteName('element_type')." = ".$db->quote('6')."
                                                AND ".$db->quoteName('element_id')." = ".$db->quote($categoryId)."
                                        ";
                                        $db->setQuery($query);
                                        $migratedCategoryId = $db->loadResult();

                                        if ($migratedCategoryId > 0)
                                        {
                                                $params = new StdClass;
                                                $params->elementId = $item->migration_id;
                                                $params->elementType = 1;
                                                $params->categoryId = $migratedCategoryId;

                                                hwdMediaShareFactory::load('category');
                                                hwdMediaShareCategory::saveIndividual($params);
                                        }
                                }
                        }
                }  
                return true;
        }

        /**
	 * Method to process an embed code import
         *
	 * @since   0.1
	 */
	public function matchPhotoTags()
	{
                $db = JFactory::getDBO();
                $date = JFactory::getDate();
                $user = JFactory::getUser();
                $app = JFactory::getApplication();
                
                // Set for Tag library
                $this->elementType = 1;
                
                // Require hwdMediaShare factory
                JLoader::register('hwdMediaShareFactory', JPATH_ROOT.'/components/com_hwdmediashare/libraries/factory.php');

                $query = "
                    SELECT *
                    FROM ".$db->quoteName('#__hwdms_migrator')."
                    WHERE ".$db->quoteName('element_type')." = ".$db->quote('1')."
                ";
                $db->setQuery($query);
                $items = $db->loadObjectList();

                if (!isset($items) || count($items) == 0)
                {
                        return true;
                }
                else
                {
                        foreach ($items as $item)
                        {
                                $import = false;

                                $query = "
                                    SELECT tags
                                    FROM ".$db->quoteName('#__hwdpsphotos')."
                                    WHERE ".$db->quoteName('id')." = ".$db->quote($item->element_id)."
                                ";
                                $db->setQuery($query);
                                $tagString = $db->loadResult();

                                if (!empty($tagString))
                                {
                                        $params = new StdClass;
                                        $params->elementType = 1;
                                        $params->elementId = $item->migration_id;
                                        $params->tags = $tagString;

                                        hwdMediaShareFactory::load('tags');
                                        hwdMediaShareTags::save($params);
                                }
                        }
                }
                return true;
        }
                
        /**
	 * Method to process an embed code import
         *
	 * @since   0.1
	 */
	public function matchPhotoGroups()
	{
                $db = JFactory::getDBO();
                $date = JFactory::getDate();
                $user = JFactory::getUser();
                $app = JFactory::getApplication();

                // Require hwdMediaShare factory
                JLoader::register('hwdMediaShareFactory', JPATH_ROOT.'/components/com_hwdmediashare/libraries/factory.php');

                $query = "
                    SELECT *
                    FROM ".$db->quoteName('#__hwdms_migrator')."
                    WHERE ".$db->quoteName('element_type')." = ".$db->quote('7')."
                ";
                $db->setQuery($query);
                $items = $db->loadObjectList();

                if (!isset($items) || count($items) == 0)
                {
                        return true;
                }
                else
                {
                        foreach ($items as $item)
                        {
                                $import = false;

                                $query = "
                                    SELECT *
                                    FROM ".$db->quoteName('#__hwdpsgroup_photos')."
                                    WHERE ".$db->quoteName('groupid')." = ".$db->quote($item->element_id)."
                                ";
                                $db->setQuery($query);
                                $groupMaps = $db->loadObjectList();

                                foreach ($groupMaps as $groupMap)
                                {
                                        $query = "
                                                SELECT migration_id
                                                FROM ".$db->quoteName('#__hwdms_migrator')."
                                                WHERE ".$db->quoteName('element_type')." = ".$db->quote('5')."
                                                AND ".$db->quoteName('element_id')." = ".$db->quote($groupMap->photoid)."
                                        ";
                                        $db->setQuery($query);
                                        $migratedMediaId = $db->loadResult();

					// Check if map already exists
                                        $query = "
                                                SELECT count(*)
                                                FROM ".$db->quoteName('#__hwdms_group_map')."
                                                WHERE ".$db->quoteName('media_id')." = ".$db->quote($migratedMediaId)."
                                                AND ".$db->quoteName('group_id')." = ".$db->quote($item->migration_id)."
                                        ";
                                        $db->setQuery($query);
                                        $mapExists = $db->loadResult();

                                        if ($mapExists == 0)
                                        {
                                                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                                                $table = JTable::getInstance('LinkedGroups', 'hwdMediaShareTable');

                                                // Create an object to bind to the database
                                                $object = new StdClass;
                                                $object->media_id = $migratedMediaId;
                                                $object->group_id = $item->migration_id;
                                                $object->created = $groupMap->date;

                                                if (!$table->bind($object))
                                                {
                                                        return JError::raiseWarning( 500, $table->getError() );
                                                }

                                                if (!$table->store())
                                                {
                                                        JError::raiseError(500, $table->getError() );
                                                }
                                        }

                                }
                        }
                }
                return true;
        }

        /**
	 * Method to process an embed code import
         *
	 * @since   0.1
	 */
	public function matchPhotoAlbums()
	{
                $db = JFactory::getDBO();
                $date = JFactory::getDate();
                $user = JFactory::getUser();
                $app = JFactory::getApplication();
                
                // Require hwdMediaShare factory
                JLoader::register('hwdMediaShareFactory', JPATH_ROOT.'/components/com_hwdmediashare/libraries/factory.php');

                $query = "
                    SELECT *
                    FROM ".$db->quoteName('#__hwdms_migrator')."
                    WHERE ".$db->quoteName('element_type')." = ".$db->quote('5')."
                ";
                $db->setQuery($query);
                $items = $db->loadObjectList();

                if (!isset($items) || count($items) == 0)
                {
                        return true;
                }
                else
                {
                        foreach ($items as $item)
                        {
                                $query = "
                                    SELECT album_id
                                    FROM ".$db->quoteName('#__hwdpsphotos')."
                                    WHERE ".$db->quoteName('id')." = ".$db->quote($item->element_id)."
                                ";
                                $db->setQuery($query);
                                $albumId = $db->loadResult();

                                if ($albumId > 0)
                                {
                                        $query = "
                                                SELECT migration_id
                                                FROM ".$db->quoteName('#__hwdms_migrator')."
                                                WHERE ".$db->quoteName('element_type')." = ".$db->quote('8')."
                                                AND ".$db->quoteName('element_id')." = ".$db->quote($albumId)."
                                        ";
                                        $db->setQuery($query);
                                        $migratedAlbumId = $db->loadResult();

                                        if ($migratedAlbumId > 0)
                                        {
                                                // Check if map already exists
                                                $query = "
                                                        SELECT count(*)
                                                        FROM ".$db->quoteName('#__hwdms_album_map')."
                                                        WHERE ".$db->quoteName('media_id')." = ".$db->quote($item->migration_id)."
                                                        AND ".$db->quoteName('album_id')." = ".$db->quote($migratedAlbumId)."
                                                ";
                                                $db->setQuery($query);
                                                $mapExists = $db->loadResult();

                                                if ($mapExists == 0)
                                                {
                                                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                                                        $table = JTable::getInstance('LinkedAlbums', 'hwdMediaShareTable');

                                                        // Create an object to bind to the database
                                                        $object = new StdClass;
                                                        $object->album_id = $migratedAlbumId;
                                                        $object->media_id = $item->migration_id;
                                                        $object->created_user_id = $user->id;
                                                        $object->created = $date->format('Y-m-d H:i:s');

                                                        if (!$table->bind($object))
                                                        {
                                                                return JError::raiseWarning( 500, $table->getError() );
                                                        }

                                                        if (!$table->store())
                                                        {
                                                                JError::raiseError(500, $table->getError() );
                                                        }
                                                }
                                        }
                                }
                        }
                }  
                return true;
        }
        
        /**
	 * Method to process an embed code import
         *
	 * @since   0.1
	 */
	public function getParentId($category, $etype=2)
	{
                if ($category->parent == "0")
                {
                        return "1";
                }

                $db = JFactory::getDBO();
                $query = "
                    SELECT *
                    FROM ".$db->quoteName('#__hwdms_migrator')."
                ";
                $db->setQuery($query);
                $tests = $db->loadObjectList();

                $query = "
                    SELECT migration_id
                    FROM ".$db->quoteName('#__hwdms_migrator')."
                    WHERE ".$db->quoteName('element_type')." = ".$db->quote($etype)."
                    AND ".$db->quoteName('element_id')." = ".$db->quote($category->parent)."
                    AND ".$db->quoteName('status')." = ".$db->quote('1')."
                ";
                $db->setQuery($query);
                $parent_id = $db->loadResult();
                $parent_id = (int) $parent_id;
                if ($parent_id > 1)
                {
                        return $parent_id;
                }
                return false;
        }

        /**
	 * Method to process an embed code import
         *
	 * @since   0.1
	 */
	public function getLevel($category, $ctable='hwdvidscategories')
	{
                $level = 1;
                if ($category->parent == "0")
                {
                        return $level;
                }

                $db = JFactory::getDBO();
                $app = JFactory::getApplication();

                $db->setQuery( 'SHOW TABLES' );
                $tables = $db->loadColumn();

                foreach ($tables as $table)
                {
                        if ($table == $app->getCfg( 'dbprefix' ).$ctable)
                        {
                                $query = "
                                    SELECT *
                                    FROM ".$db->quoteName('#__'.$ctable)."
                                    WHERE ".$db->quoteName('id')." = ".$db->quote($category->parent)."
                                ";
                                $db->setQuery($query);
                                $row1 = $db->loadObject();

                                if ($row1->parent == "0")
                                {
                                        $level++;
                                        return $level;
                                }
                                else
                                {
                                        $query = "
                                            SELECT *
                                            FROM ".$db->quoteName('#__'.$ctable)."
                                            WHERE ".$db->quoteName('id')." = ".$db->quote($row1->parent)."
                                        ";
                                        $db->setQuery($query);
                                        $row2 = $db->loadObject();

                                        if ($row2->parent == "0")
                                        {
                                                $level++;
                                                return $level;
                                        }
                                        else
                                        {
                                                return false;
                                        }
                                }
                        }
                }
                return false;
        }

        /**
	 * Method to check for valid aliases (no duplicates)
	 *
	 * @return boolean
	 */
        function getAlias($alias, $table, $prep='media')
        {
                $prep = $prep.rand(1,9);
                
                // Sanitise the alias
                jimport( 'joomla.filter.output' );
		$alias = JFilterOutput::stringURLSafe($alias);

                // Check for duplicates
                $db = JFactory::getDBO();
		$query	= 'SELECT COUNT(*)
                             FROM ' . $db->quoteName( '#__'.$table ) . '
                             WHERE '. $db->quoteName( 'alias' ) . '=' . $db->Quote( $alias );
		$db->setQuery( $query );

		if ($db->loadResult() > 0)
                {
                        // If duplicate, append with additioanl text
                        // @TODO: add recursive check
                        $alias = $prep.'-'.$alias;
                        return $this->getAlias($alias, $table, $prep);
                }
                else
                {
                        // Otherwise return sanitised alias
                        return $alias;
                }
        }
        
        /**
	 * Method to check for valid aliases (no duplicates)
	 *
	 * @return boolean
	 */
	function time2seconds($time='00:00:00')
        {
            list($hours, $mins, $secs) = explode(':', $time);
            return ($hours * 3600 ) + ($mins * 60 ) + $secs;
        }
}