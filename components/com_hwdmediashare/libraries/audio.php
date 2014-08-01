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
	 * @param   mixed  $properties  Either and associative array or another
	 *                              object to set the initial properties of the object.
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
         * @param   object  $item   The media item.
         * @return  string  The html to display the document.
	 */
	public static function display($item)
	{
                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // Get HWD utilities.
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                
                $mp3 = hwdMediaShareAudio::getMp3($item);
                $ogg = hwdMediaShareAudio::getOgg($item);

                if ($mp3 && $ogg)
                {
                        $pluginClass = 'plgHwdmediashare'.$config->get('media_player');
                        $pluginPath = JPATH_ROOT.'/plugins/hwdmediashare/'.$config->get('media_player').'/'.$config->get('media_player').'.php';
                        if (file_exists($pluginPath))
                        {
                                JLoader::register($pluginClass, $pluginPath);
                                $HWDplayer = call_user_func(array($pluginClass, 'getInstance'));
                                
                                // Setup parameters for player.
                                $params = new JRegistry(array(
                                    'mp3' => $mp3,
                                    'ogg' => $ogg
                                ));

                                if ($player = $HWDplayer->getAudioPlayer($item, $params))
                                {
                                        return $player;
                                }
                                else
                                {
                                        return $utilities->printNotice($HWDplayer->getError());
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
         * @param   object  $item   The media item.
         * @return  mixed   The mp3 file object, false on fail.
	 */
	public static function getMp3($item)
	{
                // Check for generated mp3.
                $fileType = 8;
 
                hwdMediaShareFactory::load('files');
                $folders = hwdMediaShareFiles::getFolders($item->key);
                $filename = hwdMediaShareFiles::getFilename($item->key, $fileType);
                $ext = hwdMediaShareFiles::getExtension($item, $fileType);
                $path = hwdMediaShareFiles::getPath($folders, $filename, $ext);
       
                if (file_exists($path))
                { 
                        return hwdMediaShareDownloads::url($item, $fileType);
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
         * @param   object  $item   The media item.
         * @return  mixed   The path to the ogg file, false on fail.
	 */
	public static function getOgg($item)
	{
                // Check for generated ogg.
                $fileType = 9;
 
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
         * @param   object  $process    The process item.
         * @param   integer $fileType   The API value for the type of file being generated, used
         *                              in generation of filename. 
         * @return  mixed   The path to the ogg file, false on fail.
	 */
	public function processMp3($process, $fileType)
	{
                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // Import Joomla libraries.
                jimport('joomla.filesystem.file');

                // Import HWD libraries.
                hwdMediaShareFactory::load('files');
                
                // Setup log.
                $log = new StdClass;
                $log->process_id = $process->id;
                $log->input = '';
                $log->output = '';
                $log->status = 3;

                JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_hwdmediashare/tables');
                $table = JTable::getInstance('Media', 'hwdMediaShareTable');
                $table->load($process->media_id);

                $properties = $table->getProperties(1);
                $item = JArrayHelper::toObject($properties, 'JObject');

                hwdMediaShareFiles::getLocalStoragePath();

                $foldersSource = hwdMediaShareFiles::getFolders($item->key);
                $filenameSource = hwdMediaShareFiles::getFilename($item->key, 1);
                $extSource = hwdMediaShareFiles::getExtension($item, 1);

                $pathSource = hwdMediaShareFiles::getPath($foldersSource, $filenameSource, $extSource);

                if (file_exists($pathSource))
                {
                        $foldersDest = hwdMediaShareFiles::getFolders($item->key);
                        $filenameDest = hwdMediaShareFiles::getFilename($item->key, $fileType);
                        $extDest = hwdMediaShareFiles::getExtension($item, $fileType);

                        $pathDest = hwdMediaShareFiles::getPath($foldersDest, $filenameDest, $extDest);

                        try
                        {
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
     
                                $flatoutput = is_array($log->output) ? implode("\n",$log->output) : $log->output;
                                if (empty($flatoutput))
                                {
                                        $log->status = 3;

                                        // Add process log.
                                        hwdMediaShareProcesses::addLog($log);
                                        return $log;
                                }
                                else
                                {
                                        $pos = strpos($flatoutput, "No such file or directory");
                                        if ($pos !== false)
                                        {
                                                $log->status = 3;

                                                // Add process log.
                                                hwdMediaShareProcesses::addLog($log);
                                                return $log;
                                        }
                                }
                        
                                if (file_exists($pathDest) && filesize($pathDest) > 0)
                                {
                                        $log->status = 2;
                                }
                                elseif (file_exists($pathDest) && filesize($pathDest) == 0)
                                {
                                        JFile::delete($pathDest);
                                }
                        }
                        catch(Exception $e)
                        {
                                $log->output = $e->getMessage();
                        }

                        // Add process log.
                        hwdMediaShareProcesses::addLog($log);
                        if (file_exists($pathDest) && filesize($pathDest) > 0)
                        {
                                // Add file to database.
                                hwdMediaShareFactory::load('files');
                                hwdMediaShareFiles::add($item,$fileType);
                                return $log;
                        }

                        $log->output = JText::_('COM_HWDMS_ERROR_DESTINATION_MEDIA_NOT_EXIST');
                }
                else
                {
                        $log->output = JText::_('COM_HWDMS_ERROR_SOURCE_MEDIA_NOT_EXIST');
                }

                // Add process log.
                hwdMediaShareProcesses::addLog($log);
                return $log;                                              
	}
        
        /**
	 * Method to process a media to generate an ogg.
         * 
         * @access  public
         * @param   object  $process    The process item.
         * @param   integer $fileType   The API value for the type of file being generated, used
         *                              in generation of filename. 
         * @return  mixed   The path to the ogg file, false on fail.
	 */
	public function processOgg($process, $fileType)
	{
                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // Import Joomla libraries.
                jimport('joomla.filesystem.file');

                // Import HWD libraries.
                hwdMediaShareFactory::load('files');
                
                // Setup log.
                $log = new StdClass;
                $log->process_id = $process->id;
                $log->input = '';
                $log->output = '';
                $log->status = 3;

                JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_hwdmediashare/tables');
                $table = JTable::getInstance('Media', 'hwdMediaShareTable');
                $table->load($process->media_id);

                $properties = $table->getProperties(1);
                $item = JArrayHelper::toObject($properties, 'JObject');

                hwdMediaShareFactory::load('files');

                hwdMediaShareFiles::getLocalStoragePath();

                $foldersSource = hwdMediaShareFiles::getFolders($item->key);
                $filenameSource = hwdMediaShareFiles::getFilename($item->key, 1);
                $extSource = hwdMediaShareFiles::getExtension($item, 1);

                $pathSource = hwdMediaShareFiles::getPath($foldersSource, $filenameSource, $extSource);

                if (file_exists($pathSource))
                {
                        $foldersDest = hwdMediaShareFiles::getFolders($item->key);
                        $filenameDest = hwdMediaShareFiles::getFilename($item->key, $fileType);
                        $extDest = hwdMediaShareFiles::getExtension($item, $fileType);

                        $pathDest = hwdMediaShareFiles::getPath($foldersDest, $filenameDest, $extDest);

                        try
                        {
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
                                
                                $flatoutput = is_array($log->output) ? implode("\n",$log->output) : $log->output;
                                if (empty($flatoutput))
                                {
                                        $log->status = 3;

                                        // Add process log.
                                        hwdMediaShareProcesses::addLog($log);
                                        return $log;
                                }
                                else
                                {
                                        $pos = strpos($flatoutput, "No such file or directory");
                                        if ($pos !== false)
                                        {
                                                $log->status = 3;

                                                // Add process log.
                                                hwdMediaShareProcesses::addLog($log);
                                                return $log;
                                        }
                                }
                                
                                if (file_exists($pathDest) && filesize($pathDest) > 0)
                                {
                                        $log->status = 2;
                                }
                                elseif (file_exists($pathDest) && filesize($pathDest) == 0)
                                {
                                        JFile::delete($pathDest);
                                }
                        }
                        catch(Exception $e)
                        {
                                $log->output = $e->getMessage();
                        }

                        // Add process log.
                        hwdMediaShareProcesses::addLog($log);
                        if (file_exists($pathDest) && filesize($pathDest) > 0)
                        {
                                // Add file to database.
                                hwdMediaShareFactory::load('files');
                                hwdMediaShareFiles::add($item,$fileType);
                                return $log;
                        }

                        $log->output = JText::_('COM_HWDMS_ERROR_DESTINATION_MEDIA_NOT_EXIST');
                }
                else
                {
                        $log->output = JText::_('COM_HWDMS_ERROR_SOURCE_MEDIA_NOT_EXIST');
                }

                // Add process log.
                hwdMediaShareProcesses::addLog($log);
                return $log;              
	}
        
        /**
	 * Method to extract the metadata from an audio file.
         * 
         * @access  public
         * @param   object  $item   The media item.
         * @return  mixed   An array of metadata, false on fail.
	 */
	public static function getMeta($item)
	{
                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
              
                // Define path to metadata file.
                jimport( 'joomla.filesystem.file');
                $ini = JPATH_CACHE . '/metadata' . $item->id . '.ini';
                
                // If the file does not exist, then attempt to create.
                if (!file_exists($ini))
                {
                        hwdMediaShareFactory::load('files');
                        hwdMediaShareFactory::load('downloads');                
                        $foldersSource = hwdMediaShareFiles::getFolders($item->key);
                        $filenameSource = hwdMediaShareFiles::getFilename($item->key, 1);
                        $extSource = hwdMediaShareFiles::getExtension($item, 1);

                        $pathSource = hwdMediaShareFiles::getPath($foldersSource, $filenameSource, $extSource);

                        // Check the source file exists.
                        if (file_exists($pathSource) && filesize($pathSource) > 0)
                        {
                                try
                                {
                                        // Extract metadata.
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
                                        $log = $e->getMessage();
                                }
                        } 
                }

                // If the file exists, then return the data.
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
