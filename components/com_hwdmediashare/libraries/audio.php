<?php
/**
 * @package     Joomla.site
 * @subpackage  Component.hwdmediashare
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class hwdMediaShareAudio extends JObject
{        
	/**
	 * Class constructor.
	 *
	 * @access  public
	 * @param   mixed   $properties  Associative array to set the initial properties of the object.
         * @return  void
	 */
	public function __construct($properties = null)
	{
		parent::__construct($properties);
	}

	/**
	 * Returns the hwdMediaShareAudio object, only creating it if it
	 * doesn't already exist.
	 *
	 * @access  public
         * @static
	 * @return  hwdMediaShareAudio Object.
	 */ 
	public static function getInstance()
	{
		static $instance;

		if (!isset ($instance))
                {
			$c = 'hwdMediaShareAudio';
                        $instance = new $c;
		}

		return $instance;
	}
        
	/**
	 * Method to display an audio track.
         * 
         * @access  public
         * @static
         * @param   object  $item  The media item.
         * @return  string  The html to display the document.
	 */
	public static function display($item)
	{
                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // Load HWD utilities.
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                
                $mp3 = hwdMediaShareAudio::getMp3($item);
                $ogg = hwdMediaShareAudio::getOgg($item);

                if ($mp3)
                {
                        $pluginClass = 'plgHwdmediashare'.$config->get('media_player');
                        $pluginPath = JPATH_ROOT.'/plugins/hwdmediashare/'.$config->get('media_player').'/'.$config->get('media_player').'.php';
                        if (file_exists($pluginPath))
                        {
                                JLoader::register($pluginClass, $pluginPath);
                                $HWDplayer = call_user_func(array($pluginClass, 'getInstance'));
                                
                                // Setup sources for player.
                                $sources = new JRegistry(array(
                                    'mp3' => $mp3,
                                    'ogg' => $ogg
                                ));

                                if ($player = $HWDplayer->getAudioPlayer($item, $sources))
                                {
                                        return $player;
                                }
                                else
                                {
                                        return $utilities->printNotice($HWDplayer->getError(), '', 'info', true);
                                }
                        }
                }

                // Fallback to document display.
                hwdMediaShareFactory::load('documents');
                return hwdMediaShareDocuments::display($item);
	}
        
	/**
	 * Method to check if an mp3 file has been generated and return file data.
         * 
         * @access  public
         * @static
         * @param   object  $item  The media item.
         * @return  mixed   The file object, false on fail.
	 */
	public static function getMp3($item)
	{
                // Check for generated mp3.
                $fileType = 8;
 
                // If CDN, let the CDN framework return the data.
                if ($item->type == 5 && $item->storage)
		{
                        $pluginClass = 'plgHwdmediashare'.$item->storage;
                        $pluginPath = JPATH_ROOT.'/plugins/hwdmediashare/'.$item->storage.'/'.$item->storage.'.php';
                        if (file_exists($pluginPath))
                        {
                                JLoader::register($pluginClass, $pluginPath);
                                $HWDcdn = call_user_func(array($pluginClass, 'getInstance'));
                                return $HWDcdn->publicUrl($item, $fileType);
                        }
                } 
                
                hwdMediaShareFactory::load('files');
                $folders = hwdMediaShareFiles::getFolders($item->key);
                $filename = hwdMediaShareFiles::getFilename($item->key, $fileType);
                $ext = hwdMediaShareFiles::getExtension($item, $fileType);
                $path = hwdMediaShareFiles::getPath($folders, $filename, $ext);
       
                if (file_exists($path))
                {
                        // Create file object.
                        $file = new JObject;
                        $file->local = true;
                        $file->path = $path;
                        $file->url = hwdMediaShareDownloads::url($item, $fileType);
                        $file->size = filesize($path);
                        $file->ext = $ext;
                        $file->type = 'audio/mpeg';
                  
                        return $file;
                }

                // Check if the original is an mp3.
                $fileType = 1;

                hwdMediaShareFactory::load('files');
                $folders = hwdMediaShareFiles::getFolders($item->key);
                $filename = hwdMediaShareFiles::getFilename($item->key, $fileType);
                $ext = hwdMediaShareFiles::getExtension($item, $fileType);
                $path = hwdMediaShareFiles::getPath($folders, $filename, $ext);
 
                if ($ext == 'mp3' && file_exists($path))
                {
                        // Create file object.
                        $file = new JObject;
                        $file->local = true;
                        $file->path = $path;
                        $file->url = hwdMediaShareDownloads::url($item, $fileType);
                        $file->size = filesize($path);
                        $file->ext = $ext;
                        $file->type = 'audio/mpeg';
                  
                        return $file;
                }

                return false;
	} 
        
	/**
	 * Method to check if an ogg file has been generated and return the path.
         * 
         * @access  public
         * @static
         * @param   object  $item  The media item.
         * @return  mixed   The file object, false on fail.
	 */
	public static function getOgg($item)
	{
                // Check for generated ogg.
                $fileType = 9;
 
                // If CDN, let the CDN framework return the data.
                if ($item->type == 5 && $item->storage)
		{
                        $pluginClass = 'plgHwdmediashare'.$item->storage;
                        $pluginPath = JPATH_ROOT.'/plugins/hwdmediashare/'.$item->storage.'/'.$item->storage.'.php';
                        if (file_exists($pluginPath))
                        {
                                JLoader::register($pluginClass, $pluginPath);
                                $HWDcdn = call_user_func(array($pluginClass, 'getInstance'));
                                return $HWDcdn->publicUrl($item, $fileType);
                        }
                } 
                
                hwdMediaShareFactory::load('files');
                $folders = hwdMediaShareFiles::getFolders($item->key);
                $filename = hwdMediaShareFiles::getFilename($item->key, $fileType);
                $ext = hwdMediaShareFiles::getExtension($item, $fileType);
                $path = hwdMediaShareFiles::getPath($folders, $filename, $ext);
                if (file_exists($path))
                {
                        // Create file object.
                        $file = new JObject;
                        $file->local = true;
                        $file->path = $path;
                        $file->url = hwdMediaShareDownloads::url($item, $fileType);
                        $file->size = filesize($path);
                        $file->ext = $ext;
                        $file->type = 'audio/ogg';
                  
                        return $file;
                }
                 
                return false;
	} 
        
        /**
	 * Method to process a media to generate an mp3.
         * 
         * @access  public
         * @param   object   $process   The process item.
         * @param   integer  $fileType  The API value for the type of file being generated, used
         *                              in generation of filename. 
         * @return  object   The log data.
	 */
	public function processMp3($process, $fileType)
	{
                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // Load HWD processes library.
                hwdMediaShareFactory::load('processes');
                $HWDprocesses = hwdMediaShareProcesses::getInstance();
                
                // Load HWD files library.
                hwdMediaShareFactory::load('files');
                $HWDfiles = hwdMediaShareFiles::getInstance();
                                
                // Import Joomla libraries.
                jimport('joomla.filesystem.file');
                
                // Setup log.
                $log = $HWDprocesses->resetLog($process);

                JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_hwdmediashare/tables');
                $table = JTable::getInstance('Media', 'hwdMediaShareTable');
                $table->load($process->media_id);

                $properties = $table->getProperties(1);
                $media = JArrayHelper::toObject($properties, 'JObject');

                hwdMediaShareFiles::getLocalStoragePath();

                $foldersSource = hwdMediaShareFiles::getFolders($media->key);
                $filenameSource = hwdMediaShareFiles::getFilename($media->key, 1);
                $extSource = hwdMediaShareFiles::getExtension($media, 1);

                $pathSource = hwdMediaShareFiles::getPath($foldersSource, $filenameSource, $extSource);

                if (!file_exists($pathSource))
                {
                        // Log fail (no source file).
                        $log->output = JText::_('COM_HWDMS_ERROR_SOURCE_MEDIA_NOT_EXIST');
                        $HWDprocesses->addLog($log);
                        return $log;  
                }
                
                $foldersDest = hwdMediaShareFiles::getFolders($media->key);
                $filenameDest = hwdMediaShareFiles::getFilename($media->key, $fileType);
                $extDest = hwdMediaShareFiles::getExtension($media, $fileType);

                $pathDest = hwdMediaShareFiles::getPath($foldersDest, $filenameDest, $extDest);
                
                // Destination file already exists. We must be re-processing, so delete. 
                if (file_exists($pathDest)) JFile::delete($pathDest);

                try
                {
                        // Check we can use the exec function.
                        if (TRUE !== is_callable('exec'))
                        {
                                throw new Exception(JText::_('COM_HWDMS_ERROR_EXEC_FUNCTION_NOT_CALLABLE'));
                        }

                        // Check we can use the exec function.
                        if (TRUE !== function_exists('exec'))
                        {
                                throw new Exception(JText::_('COM_HWDMS_ERROR_EXEC_FUNCTION_NOT_EXISTS'));
                        } 
                        
                        if(substr(PHP_OS, 0, 3) == "WIN")
                        {
                                $log->input = "\"".$config->get('path_ffmpeg', '/usr/bin/ffmpeg')."\" -y -i $pathSource -f mp3 -acodec libmp3lame -ac 2 -vn $pathDest 2>&1";
                                exec($log->input, $log->output);
                        }
                        else
                        {
                                $log->input = $config->get('path_ffmpeg', '/usr/bin/ffmpeg')." -y -i $pathSource -f mp3 -acodec libmp3lame -ac 2 -vn $pathDest 2>&1";
                                exec($log->input, $log->output);
                        } 

                        $output = is_array($log->output) ? implode("\n", $log->output) : $log->output;
                        if (empty($output))
                        {
                                // Log fail (empty ffmpeg output).
                                $HWDprocesses->addLog($log);
                                return $log;
                        }
                        else
                        {
                                $err1 = strpos($output, "No such file or directory");
                                $err2 = strpos($output, "not found");
                                $err3 = strpos($output, "Permission denied");
                                if ($err1 !== false || $err2 !== false || $err3 !== false)
                                {
                                        // Log fail (ffmpeg not accessible).
                                        $HWDprocesses->addLog($log);
                                        return $log;
                                }
                        }

                        if (file_exists($pathDest) && filesize($pathDest) == 0)
                        {
                                // Log fail (empty output file).
                                JFile::delete($pathDest);
                                $HWDprocesses->addLog($log);
                                return $log;                                        
                        }
                }
                catch(Exception $e)
                {
                        // Log fail (caught error).
                        JFile::delete($pathDest);
                        $log->output = $e->getMessage();
                        $HWDprocesses->addLog($log);
                        return $log;                             
                }

                // SUCCESS!
                if (file_exists($pathDest) && filesize($pathDest) > 0)
                {
                        // Log success.
                        $log->status = 2;
                        $HWDprocesses->addLog($log);
                        // Add file.
                        $HWDfiles->addFile($media, $fileType);
                        return $log;  
                }

                // Log fail (unknown).
                $HWDprocesses->addLog($log);
                return $log;                                              
	}
        
        /**
	 * Method to process a media to generate an ogg.
         * 
         * @access  public
         * @param   object   $process   The process item.
         * @param   integer  $fileType  The API value for the type of file being generated, used
         *                              in generation of filename. 
         * @return  object   The log data.
	 */
	public function processOgg($process, $fileType)
	{
                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // Load HWD processes library.
                hwdMediaShareFactory::load('processes');
                $HWDprocesses = hwdMediaShareProcesses::getInstance();
                
                // Load HWD files library.
                hwdMediaShareFactory::load('files');
                $HWDfiles = hwdMediaShareFiles::getInstance();
                                
                // Import Joomla libraries.
                jimport('joomla.filesystem.file');
                
                // Setup log.
                $log = $HWDprocesses->resetLog($process);

                JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_hwdmediashare/tables');
                $table = JTable::getInstance('Media', 'hwdMediaShareTable');
                $table->load($process->media_id);

                $properties = $table->getProperties(1);
                $media = JArrayHelper::toObject($properties, 'JObject');

                hwdMediaShareFactory::load('files');

                hwdMediaShareFiles::getLocalStoragePath();

                $foldersSource = hwdMediaShareFiles::getFolders($media->key);
                $filenameSource = hwdMediaShareFiles::getFilename($media->key, 1);
                $extSource = hwdMediaShareFiles::getExtension($media, 1);

                $pathSource = hwdMediaShareFiles::getPath($foldersSource, $filenameSource, $extSource);
                
                if (!file_exists($pathSource))
                {
                        // Log fail (no source file).
                        $log->output = JText::_('COM_HWDMS_ERROR_SOURCE_MEDIA_NOT_EXIST');
                        $HWDprocesses->addLog($log);
                        return $log;  
                }

                $foldersDest = hwdMediaShareFiles::getFolders($media->key);
                $filenameDest = hwdMediaShareFiles::getFilename($media->key, $fileType);
                $extDest = hwdMediaShareFiles::getExtension($media, $fileType);

                $pathDest = hwdMediaShareFiles::getPath($foldersDest, $filenameDest, $extDest);

                // Destination file already exists. We must be re-processing, so delete. 
                if (file_exists($pathDest)) JFile::delete($pathDest);
                
                try
                {
                        // Check we can use the exec function.
                        if (TRUE !== is_callable('exec'))
                        {
                                throw new Exception(JText::_('COM_HWDMS_ERROR_EXEC_FUNCTION_NOT_CALLABLE'));
                        }

                        // Check we can use the exec function.
                        if (TRUE !== function_exists('exec'))
                        {
                                throw new Exception(JText::_('COM_HWDMS_ERROR_EXEC_FUNCTION_NOT_EXISTS'));
                        } 
                        
                        if(substr(PHP_OS, 0, 3) == "WIN")
                        {
                                $log->input = "\"".$config->get('path_ffmpeg', '/usr/bin/ffmpeg')."\" -y -i $pathSource -f ogg -acodec libvorbis -ac 2 -vn $pathDest 2>&1";
                                exec($log->input, $log->output);
                        }
                        else
                        {
                                $log->input = $config->get('path_ffmpeg', '/usr/bin/ffmpeg')." -y -i $pathSource -f ogg -acodec libvorbis -ac 2 -vn $pathDest 2>&1";
                                exec($log->input, $log->output);
                        } 

                        $output = is_array($log->output) ? implode("\n", $log->output) : $log->output;
                        if (empty($output))
                        {
                                // Log fail (empty ffmpeg output).
                                $HWDprocesses->addLog($log);
                                return $log;
                        }
                        else
                        {
                                $err1 = strpos($output, "No such file or directory");
                                $err2 = strpos($output, "not found");
                                $err3 = strpos($output, "Permission denied");
                                if ($err1 !== false || $err2 !== false || $err3 !== false)
                                {
                                        // Log fail (ffmpeg not accessible).
                                        $HWDprocesses->addLog($log);
                                        return $log;
                                }
                        }

                        if (file_exists($pathDest) && filesize($pathDest) == 0)
                        {
                                // Log fail (empty output file).
                                JFile::delete($pathDest);
                                $HWDprocesses->addLog($log);
                                return $log;                                        
                        }
                }
                catch(Exception $e)
                {
                        // Log fail (caught error).
                        JFile::delete($pathDest);
                        $log->output = $e->getMessage();
                        $HWDprocesses->addLog($log);
                        return $log;                             
                }

                // SUCCESS!
                if (file_exists($pathDest) && filesize($pathDest) > 0)
                {
                        // Log success.
                        $log->status = 2;
                        $HWDprocesses->addLog($log);
                        // Add file.
                        $HWDfiles->addFile($media, $fileType);
                        return $log;  
                }

                // Log fail (unknown).
                $HWDprocesses->addLog($log);
                return $log;                
	}
        
        /**
	 * Method to extract the metadata from an audio file.
         * 
         * @access  public
         * @param   object  $item  The media item.
         * @return  mixed   An array of metadata, false on fail.
	 */
	public static function getMeta($item)
	{
                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
              
                // Define path to metadata file.
                jimport('joomla.filesystem.file');
                $ini = JPATH_CACHE . '/metadata' . $item->id . '.ini';
                
                // Attempt to create metadata file.
                if (!file_exists($ini))
                {
                        hwdMediaShareFactory::load('files');
                        hwdMediaShareFactory::load('downloads');                
                        $foldersSource = hwdMediaShareFiles::getFolders($item->key);
                        $filenameSource = hwdMediaShareFiles::getFilename($item->key, 1);
                        $extSource = hwdMediaShareFiles::getExtension($item, 1);

                        $pathSource = hwdMediaShareFiles::getPath($foldersSource, $filenameSource, $extSource);

                        if (file_exists($pathSource) && filesize($pathSource) > 0)
                        {
                                try
                                {
                                        if(substr(PHP_OS, 0, 3) == "WIN")
                                        {
                                                $command = "\"".$config->get('path_ffmpeg', '/usr/bin/ffmpeg')."\" -i $pathSource -f ffmetadata ".JPATH_CACHE."/metadata".$item->id.".ini 2>&1";
                                                exec($command, $output);
                                        }
                                        else
                                        {
                                                $command = $config->get('path_ffmpeg', '/usr/bin/ffmpeg')." -i $pathSource -f ffmetadata ".JPATH_CACHE."/metadata".$item->id.".ini 2>&1";
                                                exec($command, $output);
                                        }  
                                }
                                catch(Exception $e)
                                {
                                        // $this->setError($e->getMessage());
                                        return false;
                                }
                        } 
                }

                // Return an array of metadata.
                if (file_exists($ini))
                {
                        $data = JFile::read($ini);
                        $registry = new JRegistry;
			$registry->loadString($data);
			return $registry->toArray();                        
                }
                
                return false;
	}
}
