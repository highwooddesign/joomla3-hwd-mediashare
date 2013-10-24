<?php
/**
 * @version    $Id: cdn_universal.php 1301 2013-03-19 11:42:20Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import hwdMediaShare remote library
hwdMediaShareFactory::load('remote');

/**
 * hwdMediaShare framework files class
 *
 * @package hwdMediaShare
 * @since   0.1
 */
class plgHwdmediashareCdn_universal extends JObject
{               
        /**
	 * Constructor
	 *
	 * @access      protected
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 * @since       1.5
	 */
	public function __construct()
	{
	}
        
	/**
	 * Returns the hwdMediaShareFiles object, only creating it if it
	 * doesn't already exist.
	 *
	 * @return  hwdMediaShareFiles A hwdMediaShareFiles object.
	 * @since   0.1
	 */
	public static function getInstance()
	{
		static $instance;

		if (!isset ($instance))
                {
			$c = 'plgHwdmediashareCdn_universal';
                        $instance = new $c;
		}

		return $instance;
	}
        
	/**
	 * Returns the hwdMediaShareFiles object, only creating it if it
	 * doesn't already exist.
	 *
	 * @return  hwdMediaShareFiles A hwdMediaShareFiles object.
	 * @since   0.1
	 */
	public static function maintenance()
	{
		$plugin =& JPluginHelper::getPlugin('hwdmediashare', 'cdn_universal');
                
                // Die if plugin not avaliable
                if (!isset($plugin->params)) die('Universal CDN plugin is not published');
               
		$params = new JRegistry( $plugin->params );
                
		// FTP access info
                $scheme       = $params->get('scheme', 'ftp');
                $host         = $params->get('host', '');
                $port         = $params->get('port', '');
                $user         = $params->get('user' , '');
                $pass         = $params->get('pass', '');
                $path         = $params->get('path', '');
		$location     = $params->get('location', '');

		// Check for CURL
		if (!extension_loaded('curl') && !@dl(PHP_SHLIB_SUFFIX == 'so' ? 'curl.so' : 'php_curl.dll'))
			exit("ERROR: CURL extension not loaded");

		// Pointless without access!
		if ($scheme == '' || $host == '' || $port == '')
			exit("ERROR: Access information required");

                // Instantiate the class
		if (!$conn = plgHwdmediashareCdn_universal::getFtpConnection())
                {
                        echo "User $user cannot log in.<br />".PHP_EOL ;
                        return;
                }
		
                // Get CDN contents
                hwdMediaShareFactory::load('files');
                hwdMediaShareFiles::getLocalStoragePath();
                           
                // Get local queue
                $queued = plgHwdmediashareCdn_universal::getLocalQueue();                  
                echo "About to process ".count($queued)." media items<br />".PHP_EOL ;
                foreach ($queued as $media)
                {
                        $errors = false;

                        // Create a new query object.
                        $db = JFactory::getDBO();

                        // Setup query
                        $query = $db->getQuery(true);

                        // Select the required fields from the table.
                        $query->select('COUNT(*)');
                        $query->from('#__hwdms_processes');
                        $query->where('media_id = '.$media->id);
                        $query->where('(status = 1 || status = 3)');

                        $db->setQuery($query);
                        $queuedProcesses = $db->loadResult();
                        if ($queuedProcesses > 0) 
                        {
                                $errors = true;
                                continue;  
                        }
                        
                        // Get files for local media
                        $hwdmsFiles = hwdMediaShareFiles::getInstance();
                        $files = $hwdmsFiles->getMediaFiles($media);
                        if (count($files) == 0)
                        {
                                echo "[ID:".$media->id."] Media has no files</strong><br />".PHP_EOL ;
                                $errors = true;
                                continue;
                        }
                        foreach ($files as $file)
                        {
                                $folders = hwdMediaShareFiles::getFolders($media->key);
                                $filename = hwdMediaShareFiles::getFilename($media->key, $file->file_type);
                                $ext = hwdMediaShareFiles::getExtension($media, $file->file_type);
                                $relativePath = hwdMediaShareFiles::getPath($folders, $filename, $ext, false);
                                $path = hwdMediaShareFiles::getPath($folders, $filename, $ext);
                                
                                // Check local file exists
                                if (!file_exists($path))
                                {
                                        echo "[ID:".$media->id."] Source File [<strong>$path</strong>] does not exist so skipping</strong><br />".PHP_EOL ;
                                        $errors = true;
                                        continue;
                                }

                                // If more than 500mb, we might struggle to transfer this in the timeout...
                                if (filesize($path) > 524288000)
                                {
                                        echo "[ID:".$media->id."] Source File [<strong>$path</strong>] is larger than 0.5GB so skipping</strong><br />".PHP_EOL ;
                                        $errors = true;
                                        continue;
                                }
                                
                                $remoteSize = ftp_size($conn, $relativePath);                                
				if ($remoteSize > 0)
				{
					if ($remoteSize == filesize($path) ) 
                                        {
						echo "[ID:".$media->id."] File [<strong>$relativePath</strong>] already exists at <strong>$host</strong><br />".PHP_EOL ;
					}
					else 
                                        {
						echo "[ID:".$media->id."] File [<strong>$relativePath</strong>] must be updated at <strong>$host</strong><br />".PHP_EOL;

                                                // Create missing directories
                                                plgHwdmediashareCdn_universal::checkforAndMakeDirs($conn, $relativePath);

                                                // Put our file (also with public read access)
                                                if (ftp_put($conn, $relativePath, $path, FTP_BINARY)) 
                                                {
                                                        echo "[ID:".$media->id."] File copied to $host/".$relativePath."</br>" . PHP_EOL;
                                                } 
                                                else
                                                {
                                                        echo "[ID:".$media->id."] Failed to copy file </br>". PHP_EOL;
                                                        $errors = true;
                                                        continue;
                                                }
                                        }
				}
				else
				{
                                        // Create missing directories
                                        plgHwdmediashareCdn_universal::checkforAndMakeDirs($conn, $relativePath);

					// Put our file (also with public read access)
                                        if (ftp_put($conn, $relativePath, $path, FTP_BINARY)) 
                                        {
						echo "[ID:".$media->id."] File copied to $host/".$relativePath."</br>" . PHP_EOL;
                                        } 
                                        else
                                        {
						echo "[ID:".$media->id."] Failed to copy file </br>". PHP_EOL;
                                                $errors = true;
                                                continue;
                                        }
				}
                        } 

                        // If no errors, and all local files exist on CDN then modify database so media
                        // if switched to CDN, delete all local files
                        if (!$errors)
                        {
				echo "[ID:".$media->id."] Updating database </br>". PHP_EOL;
                                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                                $row =& JTable::getInstance('Media', 'hwdMediaShareTable');

                                // Create an object to bind to the database
                                $data = array();
                                $data['id'] = $media->id;
                                $data['type'] = 5;
                                $data['storage'] = 'cdn_universal';

                                if (!$row->bind($data))
                                {
                                        JError::raiseWarning( 500, $row->getError() );
                                }

                                if (!$row->store())
                                {
                                        JError::raiseError(500, $row->getError() );
                                } 
                                
                                // Now that we have copied all the files for the media, and updated the database,
                                // we can remove all the local media files
				echo "[ID:".$media->id."] Delete local files </br>". PHP_EOL;
                                foreach ($files as $file)
                                {
                                        jimport( 'joomla.filesystem.file' );
                                        
                                        $folders = hwdMediaShareFiles::getFolders($media->key);
                                        $filename = hwdMediaShareFiles::getFilename($media->key, $file->file_type);
                                        $ext = hwdMediaShareFiles::getExtension($media, $file->file_type);
                                        $path = hwdMediaShareFiles::getPath($folders, $filename, $ext);
                                        
                                        if (!JFile::delete($path))
                                        {
                                                JError::raiseWarning( 500, "File delete error" );
                                        }
                                } 
                        }
                }
	}
    
        /**
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/
	public function getLocalQueue()
	{
                // Create a new query object.
                $db = JFactory::getDBO();
                    
                // Setup query
                $query = $db->getQuery(true);

                // Select the required fields from the table.
                $query->select('a.*');
                $query->from('#__hwdms_media AS a');
                $query->where('a.type = 1');
                $query->where('a.status = 1');
                $query->order('a.created ASC');
                
                $db->setQuery($query);
                return $db->loadObjectList();
        }   

        /**
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/
	public function getCdnLocation()
	{
        }   
        
        /**
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/
	public function createCdnLocation()
	{
        } 
        
        /**
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/
	public function getCdnContents()
	{
        } 
        
        /**
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/
	public function putFile()
	{
        } 
        
        /**
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/
	public function publicUrl($media, $fileType=1)
	{
                $plugin =& JPluginHelper::getPlugin('hwdmediashare', 'cdn_universal');

                // Die if plugin not avaliable
                if (!isset($plugin->params)) die('Universal CDN plugin is not published');

                $params = new JRegistry( $plugin->params );

                // FTP access info
                $scheme       = $params->get('scheme', 'ftp');
                $host         = $params->get('host', '');
                $port         = $params->get('port', '');
                $user         = $params->get('user' , '');
                $pass         = $params->get('pass', '');
                $path         = $params->get('path', '');
		$location     = $params->get('location', '');

                $baseUrl      = rtrim($location, "/");
                
                // Import hwdMediaShare files library
                hwdMediaShareFactory::load('files');
                hwdMediaShareFiles::getLocalStoragePath();
                $hwdmsFiles = hwdMediaShareFiles::getInstance();
                $files = $hwdmsFiles->getMediaFiles($media);
		$available = array();
                foreach ($files as $file)
                {
                        $available[] = $file->file_type;
                }
                
                if (in_array($fileType, $available))
                {
                        $folders = hwdMediaShareFiles::getFolders($media->key);
                        $filename = hwdMediaShareFiles::getFilename($media->key, $fileType);
                        $ext = hwdMediaShareFiles::getExtension($media, $fileType);

                        $relativePath = hwdMediaShareFiles::getPath($folders, $filename, $ext, false);

                        return $baseUrl.'/'.$relativePath;
                }

                $flvs = array(11,12,13);
                if (in_array($fileType, $flvs))
                {
                        foreach ($flvs as $flv)
                        {
                                if (in_array($flv, $available))
                                {
                                        $folders = hwdMediaShareFiles::getFolders($media->key);
                                        $filename = hwdMediaShareFiles::getFilename($media->key, $flv);
                                        $ext = hwdMediaShareFiles::getExtension($media, $flv);

                                        $relativePath = hwdMediaShareFiles::getPath($folders, $filename, $ext, false);

                                        return $baseUrl.'/'.$relativePath;
                                }
                        }
                        return false;
                }
                
                $mp4s = array(14,15,16,17);
                if (in_array($fileType, $mp4s))
                {
                        foreach ($mp4s as $mp4)
                        {
                                if (in_array($mp4, $available))
                                {
                                        $folders = hwdMediaShareFiles::getFolders($media->key);
                                        $filename = hwdMediaShareFiles::getFilename($media->key, $mp4);
                                        $ext = hwdMediaShareFiles::getExtension($media, $mp4);

                                        $relativePath = hwdMediaShareFiles::getPath($folders, $filename, $ext, false);

                                        return $baseUrl.'/'.$relativePath;
                                }
                        }
                        return false;
                }

                $webms = array(18,19,20,21);
                if (in_array($fileType, $webms))
                {
                        foreach ($webms as $webm)
                        {
                                if (in_array($webm, $available))
                                {
                                        $folders = hwdMediaShareFiles::getFolders($media->key);
                                        $filename = hwdMediaShareFiles::getFilename($media->key, $webm);
                                        $ext = hwdMediaShareFiles::getExtension($media, $webm);

                                        $relativePath = hwdMediaShareFiles::getPath($folders, $filename, $ext, false);

                                        return $baseUrl.'/'.$relativePath;
                                }
                        }
                        return false;
                }

                $oggs = array(22,23,24,25);
                if (in_array($fileType, $oggs))
                {
                        foreach ($oggs as $ogg)
                        {
                                if (in_array($ogg, $available))
                                {
                                        $folders = hwdMediaShareFiles::getFolders($media->key);
                                        $filename = hwdMediaShareFiles::getFilename($media->key, $ogg);
                                        $ext = hwdMediaShareFiles::getExtension($media, $ogg);

                                        $relativePath = hwdMediaShareFiles::getPath($folders, $filename, $ext, false);

                                        return $baseUrl.'/'.$relativePath;
                                }
                        }
                        return false;
                }

                // We search for images, starting with the best quality, and finishing with the lowest,
                // Then move to a custom thumbnail
                $images = array(7,6,5,4,3,10);
                if (in_array($fileType, $images))
                {
                        foreach ($images as $image)
                        {
                                if (in_array($image, $available))
                                {
                                        $folders = hwdMediaShareFiles::getFolders($media->key);
                                        $filename = hwdMediaShareFiles::getFilename($media->key, $image);
                                        $ext = hwdMediaShareFiles::getExtension($media, $image);

                                        $relativePath = hwdMediaShareFiles::getPath($folders, $filename, $ext, false);

                                        return $baseUrl.'/'.$relativePath;
                                }
                        }
                        // We don't return false here to prevent blank thumbnail images, we want the default image
                }

                $images = array(2,3,4,5,6,7);
                if (in_array($fileType, $images))
                {
                        // Can't find anything suitable so just return default image
                        return JURI::root( true ).'/media/com_hwdmediashare/assets/images/default-image-'.$fileType.'.png';
                }

                return JURI::root( true ).'/media/com_hwdmediashare/assets/images/default-image-4.png';
                
		return false;
        }  
        
        function getFtpConnection()
        {
                $plugin =& JPluginHelper::getPlugin('hwdmediashare', 'cdn_universal');

                // Die if plugin not avaliable
                if (!isset($plugin->params)) die('Universal CDN plugin is not published');

                $params = new JRegistry( $plugin->params );

                // FTP access info
                $scheme       = $params->get('scheme', 'ftp');
                $host         = $params->get('host', '');
                $port         = $params->get('port', '');
                $user         = $params->get('user' , '');
                $pass         = $params->get('pass', '');
                $path         = $params->get('path', '');
		$location     = $params->get('location', '');

                // Set up a connection
                $conn = ftp_connect($host . $path);

                // Login
                if (ftp_login($conn, $user, $pass))
                {
                        // Change the dir
                        ftp_chdir($conn, $path);

                        // Return the resource
                        return $conn;
                }

                // Or retun null
                return null;
        }  
        
        function checkForAndMakeDirs($connection, $file)
        {
                $origin = ftp_pwd($connection);
                $parts = explode("/", dirname($file));

                foreach ($parts as $curDir) 
                {
                    // Attempt to change directory, suppress errors
                    if (@ftp_chdir($connection, $curDir) === false)
                    {
                        ftp_mkdir($connection, $curDir); //directory doesn't exist - so make it
                        ftp_chdir($connection, $curDir); //go into the new directory
                    }
                }

                // Go back to the origin directory
                ftp_chdir($connection, $origin);
        }        
}