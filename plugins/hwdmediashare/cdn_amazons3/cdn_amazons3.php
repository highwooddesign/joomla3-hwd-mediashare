<?php
/**
 * @version    $Id: cdn_amazons3.php 1534 2013-05-30 10:47:54Z dhorsfall $
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
class plgHwdmediashareCdn_amazons3 extends JObject
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
			$c = 'plgHwdmediashareCdn_amazons3';
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
                JLoader::register('hwdmsS3', JPATH_BASE.'/plugins/hwdmediashare/cdn_amazons3/assets/S3.php');
		$plugin =& JPluginHelper::getPlugin('hwdmediashare', 'cdn_amazons3');

                // Die if plugin not avaliable
                if (!isset($plugin->params)) die('Amazon S3 plugin is not published');

		$params = new JRegistry( $plugin->params );

		// AWS access info
                $awsAccessKey       = $params->get('awsAccessKey', '');
                $awsSecretKey       = $params->get('awsSecretKey', '');
		$bucketName         = $params->get('awsBucket', '');
		$reducedRedundancy  = $params->get('awsRrs' , 0);
		$location           = $params->get('awsRegion', 'us-west-1');

		// marpada-S

                $endpoint = 's3.amazonaws.com';
		switch ($location)
                {
			case "us-west-1":
				$endpoint='s3-us-west-1.amazonaws.com';
				break;
			case "EU":
				$endpoint='s3-eu-west-1.amazonaws.com';
				break;
			case "ap-southeast-1":
				$endpoint="s3-ap-southeast-1.amazonaws.com";
				break;
			case "ap-northeast-1":
				$endpoint="s3-ap-northeast-1.amazonaws.com";
				break;
			default:
				$endpoint='s3.amazonaws.com';
		}

		// Windows curl extension has trouble with SSL connections, so we won't use it
		if (substr(PHP_OS, 0, 3) == "WIN")
                {
			$useSSL= 0;
		}
		else
                {
			$useSSL = 1;
		}

		//marpada-E

		// Check for CURL
		if (!extension_loaded('curl') && !@dl(PHP_SHLIB_SUFFIX == 'so' ? 'curl.so' : 'php_curl.dll'))
			exit("ERROR: CURL extension not loaded");

		// Pointless without your keys!
		if ($awsAccessKey == '' || $awsSecretKey == '')
			exit("ERROR: AWS access information required");

                // Instantiate the class
		$s3 = new hwdmsS3($awsAccessKey, $awsSecretKey, $useSSL, $endpoint);

                if ($reducedRedundancy)
                {
			$storage = hwdmsS3::STORAGE_CLASS_RRS ;
		}
		else
                {
			$storage =  hwdmsS3::STORAGE_CLASS_STANDARD ;
		}

                //Check if bucket exists and if it belongs to the defautt region
		$bucketlocation = $s3->getBucketLocation($bucketName);
		if (($bucketlocation) && ($bucketlocation <> $location ))
                {
			echo "Bucket already exist in " . $bucketlocation . " region";
			$location = $bucketlocation;
			switch ($location)
                        {
                                case "us-west-1":
                                        $s3->setEndpoint('s3-us-west-1.amazonaws.com');
                                        break;
                                case "EU":
                                        $s3->setEndpoint('s3-eu-west-1.amazonaws.com');
                                        break;
                                case "ap-southeast-1":
                                        $s3->setEndpoint('s3-ap-southeast-1.amazonaws.com');
                                        break;
                                case "ap-northeast-1":
                                        $s3->setEndpoint('s3-ap-northeast-1.amazonaws.com');
                                        break;
                                default:
                                        $s3->setEndpoint('s3.amazonaws.com');
			}
                }

		// Create a bucket with public read access
		$s3->putBucket($bucketName, hwdmsS3::ACL_PUBLIC_READ, $location);

                // Get the contents of our bucket
		$cdnContents = $s3->getBucket($bucketName);

                // Get CDN contents
                hwdMediaShareFactory::load('files');
                hwdMediaShareFiles::getLocalStoragePath();

                // Get local queue
                $queued = plgHwdmediashareCdn_amazons3::getLocalQueue();
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
                                echo "[ID:".$media->id."] Media has queued processes which need to be completed or deleted before the transfer of this media item</strong><br />".PHP_EOL ;
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

				if (isset($cdnContents[$relativePath]))
				{
					if ($cdnContents[$relativePath]['size'] == filesize($path) )
                                        {
						echo "[ID:".$media->id."] File [<strong>$relativePath</strong>] already exists in <strong>{$bucketName}</strong><br />".PHP_EOL ;
					}
					else
                                        {
						echo "[ID:".$media->id."] File [<strong>$relativePath</strong>] must be updated <strong>{$bucketName}</strong><br />".PHP_EOL;
						if (@$s3->putObject($s3->inputFile($path,false), $bucketName, $relativePath, hwdmsS3::ACL_PUBLIC_READ,array(),array(),$storage))
						{
							echo "[ID:".$media->id."] S3::putObject(): File copied to {$bucketName}/".$relativePath."</br>" . PHP_EOL;
						}
						else
						{
							echo "[ID:".$media->id."] S3::putObject(): Failed to copy file </br>" . PHP_EOL;
                                                        $errors = true;
                                                        continue;
						}
                                        }
				}
				else
				{
					// Put our file (also with public read access)
					if ($s3->putObject($s3->inputFile($path, false), $bucketName, $relativePath, hwdmsS3::ACL_PUBLIC_READ,array(),array(),$storage))
					{
						echo "[ID:".$media->id."] S3::putObject(): File copied to {$bucketName}/".$relativePath."</br>" . PHP_EOL;
					}
					else
					{
						echo "[ID:".$media->id."] S3::putObject(): Failed to copy file </br>". PHP_EOL;
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
                                $data['storage'] = 'cdn_amazons3';

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
                JLoader::register('hwdmsS3', JPATH_BASE.'/plugins/hwdmediashare/cdn_amazons3/assets/S3.php');
		$plugin =& JPluginHelper::getPlugin('hwdmediashare', 'cdn_amazons3');
		$params = new JRegistry( $plugin->params );

		// AWS access info
                $awsAccessKey       = $params->get('awsAccessKey', '');
                $awsSecretKey       = $params->get('awsSecretKey', '');
		$bucketName         = $params->get('awsBucket', '');
		$reducedRedundancy  = $params->get('reducedRedundancy' , 0);
		$location           = $params->get('location', '');
		$http_cloud         = $params->get('http_cloud', '');
		$http_url           = $params->get('http_url', '');
		$rtmp_cloud         = $params->get('rtmp_cloud', '');
		$rtmp_url           = $params->get('rtmp_url', '');

                if (!empty($bucketAlias))
                {
                        $baseUrl  = $bucketAlias;
                }
                else
                {
                        $baseUrl  = "http://$bucketName.s3.amazonaws.com";
                }

                if ($http_cloud && !empty($http_url))
                {
                        $baseUrl = strpos($http_url,'http') === 0 ? '' : 'http://';
                        $baseUrl.= $http_url;
                        $baseUrl = rtrim($baseUrl, "/");
                }

                // If user has requested that a streaming cloudfront distribution is used, then set the streamer for the item 
                if ($rtmp_cloud && !empty($rtmp_cloud)) $media->streamer = $rtmp_url;

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

                        // If user has requested that a streaming cloudfront distribution is used, and we are serving a Flash compatible file, then set the file for the item 
                        if (in_array($fileType, array(11,12,13,14,15,16,17)) && $rtmp_cloud && !empty($rtmp_cloud)) $media->file = $relativePath;
                        
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

                                        // If user has requested that a streaming cloudfront distribution is used, and we are serving a Flash compatible file, then set the file for the item 
                			if ($rtmp_cloud && !empty($rtmp_cloud)) $media->file = $relativePath;

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

                                        // If user has requested that a streaming cloudfront distribution is used, and we are serving a Flash compatible file, then set the file for the item 
                			if ($rtmp_cloud && !empty($rtmp_cloud)) $media->file = $relativePath;

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

                        if (in_array(1, $available))
                        {
                                $folders = hwdMediaShareFiles::getFolders($media->key);
                                $filename = hwdMediaShareFiles::getFilename($media->key, 1);
                                $ext = hwdMediaShareFiles::getExtension($media, 1);

                                hwdMediaShareFactory::load('images');
                                if (hwdMediaShareImages::isNativeImage($ext))
                                {
                                        $relativePath = hwdMediaShareFiles::getPath($folders, $filename, $ext, false);
                                        return $baseUrl.'/'.$relativePath;
                                }
                        }
                }

                $images = array(2,3,4,5,6,7,10);
                if (in_array($fileType, $images))
                {
                        if ($fileType == 2) $fileType = 3;
                        if ($fileType == 10) $fileType = 4;

                        // Can't find anything suitable so just return default image
                        return JURI::root( true ).'/media/com_hwdmediashare/assets/images/default-image-'.$fileType.'.png';
                }

		return false;
        }
}