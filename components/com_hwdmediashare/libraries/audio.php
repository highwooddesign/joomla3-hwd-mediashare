<?php
/**
 * @version    SVN $Id: audio.php 1508 2013-05-13 13:35:49Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      19-Jan-2012 15:23:50
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * hwdMediaShare framework audio class
 *
 * @package hwdMediaShare
 * @since   0.1
 */
abstract class hwdMediaShareAudio
{
	/**
	 * Class constructor.
	 *
	 * @param   array  $config  A configuration array including optional elements.
	 *
	 * @since   0.1
	 */
	public function __construct($config = array())
	{
	}

	/**
	 * Returns the hwdMediaShareAudio object, only creating it if it
	 * doesn't already exist.
	 *
	 * @return  hwdMediaShareAudio A hwdMediaShareAudio object.
	 * @since   0.1
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
	 * Method to render an audio
         * 
         * @since   0.1
	 **/
	public function get($item)
	{
                // Load hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                $mp3 = hwdMediaShareAudio::getMp3($item);
                $ogg = hwdMediaShareAudio::getOgg($item);
                $jpg = hwdMediaShareDownloads::jpgUrl($item);

                if ($mp3 && $ogg)
                {
                        $pluginClass = 'plgHwdmediashare'.$config->get('media_player');
                        $pluginPath = JPATH_ROOT.'/plugins/hwdmediashare/'.$config->get('media_player').'/'.$config->get('media_player').'.php';

                        // Import hwdMediaShare plugins
                        if (file_exists($pluginPath))
                        {                            
                                JLoader::register($pluginClass, $pluginPath);
                                $player = call_user_func(array($pluginClass, 'getInstance'));
                                $params = new JRegistry('{"mp3":"'.$mp3.'","ogg":"'.$ogg.'","jpg":"'.$jpg.'"}');
                                return $player->getAudioPlayer($params);
                        }
                }

                // Default to document
                hwdMediaShareFactory::load('documents');
                return hwdMediaShareDocuments::get($item);
	}
	/**
	 * Method to render a video
         * 
         * @since   0.1
	 **/
	public function getMp3($item, $override = false)
	{
                // Check for generated MP3
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

                // Check for original MP3
                $fileType = 1;

                hwdMediaShareFactory::load('files');
                $folders = hwdMediaShareFiles::getFolders($item->key);
                $filename = hwdMediaShareFiles::getFilename($item->key, $fileType);
                $ext = hwdMediaShareFiles::getExtension($item, $fileType);
                $path = hwdMediaShareFiles::getPath($folders, $filename, $ext);
 
                if ($ext == 'mp3' && file_exists($path))
                {
                        return hwdMediaShareDownloads::url($item, $fileType);
                }

                return false;
	} 
        
	/**
	 * Method to render a video
         * 
         * @since   0.1
	 **/
	public function getOgg($item, $override = false)
	{
                $fileType = 9;
 
                hwdMediaShareFactory::load('files');
                $folders = hwdMediaShareFiles::getFolders($item->key);
                $filename = hwdMediaShareFiles::getFilename($item->key, $fileType);
                $ext = hwdMediaShareFiles::getExtension($item, $fileType);
                $path = hwdMediaShareFiles::getPath($folders, $filename, $ext);
                if (file_exists($path))
                {
                        return hwdMediaShareDownloads::url($item, $fileType);
                }
                 
                return false;
	} 
        
        /**
	 * Method to generate an image
         * 
	 * @since   0.1
	 **/
	public function processMp3($process, $fileType)
	{
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // Setup log
                $log = new StdClass;
                $log->process_id = $process->id;
                $log->input = '';
                $log->output = '';
                $log->status = 3;

                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                $table =& JTable::getInstance('Media', 'hwdMediaShareTable');
                $table->load( $process->media_id );

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

                                        // Add process log
                                        hwdMediaShareProcesses::addLog($log);
                                        return $log;
                                }
                                else
                                {
                                        $pos = strpos($flatoutput, "No such file or directory");
                                        if ($pos !== false)
                                        {
                                                $log->status = 3;

                                                // Add process log
                                                hwdMediaShareProcesses::addLog($log);
                                                return $log;
                                        }
                                }
                        
                                jimport( 'joomla.filesystem.file' );
                                if (file_exists($pathDest) && filesize($pathDest) > 0)
                                {
                                        $log->status = 2;
                                }
                                else if (file_exists($pathDest) && filesize($pathDest) == 0)
                                {
                                        JFile::delete($pathDest);
                                }
                        }
                        catch(Exception $e)
                        {
                                $log->output = $e->getMessage();
                        }

                        // Add process log
                        hwdMediaShareProcesses::addLog($log);
                        if (file_exists($pathDest) && filesize($pathDest) > 0)
                        {
                                // Add file to database
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

                // Add process log
                hwdMediaShareProcesses::addLog($log);
                return $log;                                              
	}
        
        /**
	 * Method to generate an image
         * 
	 * @since   0.1
	 **/
	public function processOgg($process, $fileType)
	{
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // Setup log
                $log = new StdClass;
                $log->process_id = $process->id;
                $log->input = '';
                $log->output = '';
                $log->status = 3;

                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                $table =& JTable::getInstance('Media', 'hwdMediaShareTable');
                $table->load( $process->media_id );

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

                                        // Add process log
                                        hwdMediaShareProcesses::addLog($log);
                                        return $log;
                                }
                                else
                                {
                                        $pos = strpos($flatoutput, "No such file or directory");
                                        if ($pos !== false)
                                        {
                                                $log->status = 3;

                                                // Add process log
                                                hwdMediaShareProcesses::addLog($log);
                                                return $log;
                                        }
                                }
                                
                                jimport( 'joomla.filesystem.file' );
                                if (file_exists($pathDest) && filesize($pathDest) > 0)
                                {
                                        $log->status = 2;
                                }
                                else if (file_exists($pathDest) && filesize($pathDest) == 0)
                                {
                                        JFile::delete($pathDest);
                                }
                        }
                        catch(Exception $e)
                        {
                                $log->output = $e->getMessage();
                        }

                        // Add process log
                        hwdMediaShareProcesses::addLog($log);
                        if (file_exists($pathDest) && filesize($pathDest) > 0)
                        {
                                // Add file to database
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

                // Add process log
                hwdMediaShareProcesses::addLog($log);
                return $log;              
	}
        
        /**
	 * Method to render an image
         * 
	 * @since   0.1
	 **/
	public function getMeta($item)
	{
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                hwdMediaShareFactory::load('files');
                hwdMediaShareFactory::load('downloads');                
                $foldersSource = hwdMediaShareFiles::getFolders($item->key);
                $filenameSource = hwdMediaShareFiles::getFilename($item->key, 1);
                $extSource = hwdMediaShareFiles::getExtension($item, 1);

                $pathSource = hwdMediaShareFiles::getPath($foldersSource, $filenameSource, $extSource);

                // Check if the variable is set and if the file itself exists before continuing
                if (file_exists($pathSource) && filesize($pathSource) > 0)
                {
                        try
                        {
                                // Get information on original
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
                                $log->output = $e->getMessage();
                        }
                        
                        // Load data
                        jimport( 'joomla.filesystem.file');
                        $ini	= JPATH_CACHE.'/metadata'.$item->id.'.ini';
                        $data	= JFile::read($ini);
                        
                        $registry = new JRegistry;
			$registry->loadString($data);
			return $registry->toArray();
                } 

                return false;
	}
}
