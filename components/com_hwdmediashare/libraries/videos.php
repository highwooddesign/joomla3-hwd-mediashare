<?php
/**
 * @version    SVN $Id: videos.php 1288 2013-03-19 11:26:54Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      06-Dec-2011 15:28:32
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * hwdMediaShare framework video class
 *
 * @package hwdMediaShare
 * @since   0.1
 */
abstract class hwdMediaShareVideos
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
	 * Returns the hwdMediaShareVideos object, only creating it if it
	 * doesn't already exist.
	 *
	 * @return  hwdMediaShareVideos A hwdMediaShareVideos object.
	 * @since   0.1
	 */
	public static function getInstance()
	{
		static $instance;

		if (!isset ($instance))
                {
			$c = 'hwdMediaShareVideos';
                        $instance = new $c;
		}

		return $instance;
	}

	/**
	 * Method to render a video
         *
         * @since   0.1
	 **/
	public function get($item)
	{
                // Load hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                $flv = hwdMediaShareDownloads::flvUrl($item);
                $mp4 = hwdMediaShareDownloads::mp4Url($item);
                $webm = hwdMediaShareDownloads::webmUrl($item);
                $ogg = hwdMediaShareDownloads::oggUrl($item);
                $jpg = hwdMediaShareDownloads::jpgUrl($item);
                $hdfile = hwdMediaShareVideos::getMp4($item,720);
                
                // If we now have a streamer and a file definition then CDN has requested we return an RTMP stream
                if (isset($item->streamer) && $item->streamer && isset($item->file) && $item->file)
                {
                        hwdMediaShareFactory::load('rtmp');
                        return hwdMediaShareRtmp::get($item);
                }
                                
                if ($mp4 || $webm || $ogg || $flv)
                {
                        $pluginClass = 'plgHwdmediashare'.$config->get('media_player');
                        $pluginPath = JPATH_ROOT.'/plugins/hwdmediashare/'.$config->get('media_player').'/'.$config->get('media_player').'.php';

                        // Import hwdMediaShare plugins
                        if (file_exists($pluginPath))
                        {
                                JLoader::register($pluginClass, $pluginPath);
                                $player = call_user_func(array($pluginClass, 'getInstance'));
                                $params = new JRegistry('{"mp4":"'.$mp4.'","webm":"'.$webm.'","ogg":"'.$ogg.'","flv":"'.$flv.'","jpg":"'.$jpg.'","hdfile":"'.$hdfile.'"}');
                                return $player->getVideoPlayer($params);
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
	public function getFlv($item, $override = false)
	{
                // Load hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                // Load the mobile framework
                JLoader::register('hwdMediaShareHelperMobile', JPATH_ROOT.'/components/com_hwdmediashare/helpers/mobile.php');
                $mobile = hwdMediaShareHelperMobile::getInstance();

                $quality = $config->get('video_quality');
                if (JRequest::getInt('quality'))
                {
                        $quality = JRequest::getInt('quality');
                }
                if ($override)
                {
                        $quality = $override;
                }
                if ($mobile->_isMobile)
                {
                        $quality = 360;
                }
                
                switch ($quality)
                {
                    default:
                    case 240:
                        $fileTypes = array(11,12,13);
                        break;
                    case 360:
                        $fileTypes = array(12,13,11);
                        break;
                    case 480:
                    case 720:
                    case 1080:
                        $fileTypes = array(13,12,11);
                        break;
                }

                foreach ($fileTypes as $fileType)
                {
                        hwdMediaShareFactory::load('files');
                        $folders = hwdMediaShareFiles::getFolders($item->key);
                        $filename = hwdMediaShareFiles::getFilename($item->key, $fileType);
                        $ext = hwdMediaShareFiles::getExtension($item, $fileType);
                        $path = hwdMediaShareFiles::getPath($folders, $filename, $ext);
                        if (file_exists($path))
                        {
                                return hwdMediaShareDownloads::url($item, $fileType);
                        }
                }

                return false;
	}

        /**
	 * Method to render a video
         *
         * @since   0.1
	 **/
	public function getMp4($item, $override = false)
	{
                // Load hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                // Load the mobile framework
                JLoader::register('hwdMediaShareHelperMobile', JPATH_ROOT.'/components/com_hwdmediashare/helpers/mobile.php');
                $mobile = hwdMediaShareHelperMobile::getInstance();
                
                $quality = $config->get('video_quality');
                if (JRequest::getInt('quality'))
                {
                        $quality = JRequest::getInt('quality');
                }
                if ($override)
                {
                        $quality = $override;
                }
                if ($mobile->_isMobile)
                {
                        $quality = 360;
                }
                
                switch ($quality)
                {
                    default:
                    case 240:
                    case 360:
                        $fileTypes = array(14,15,16,17);
                        break;
                    case 480:
                        $fileTypes = array(15,16,17,14);
                        break;
                    case 720:
                        $fileTypes = array(16,17,15,14);
                        break;
                    case 1080:
                        $fileTypes = array(17,16,15,14);
                        break;
                }

                foreach ($fileTypes as $fileType)
                {
                        hwdMediaShareFactory::load('files');
                        $folders = hwdMediaShareFiles::getFolders($item->key);
                        $filename = hwdMediaShareFiles::getFilename($item->key, $fileType);
                        $ext = hwdMediaShareFiles::getExtension($item, $fileType);
                        $path = hwdMediaShareFiles::getPath($folders, $filename, $ext);
                        if (file_exists($path))
                        {
                                return hwdMediaShareDownloads::url($item, $fileType);
                        }
                }

                return false;
	}

	/**
	 * Method to render a video
         *
         * @since   0.1
	 **/
	public function getWebm($item, $override = false)
	{
                // Load hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                // Load the mobile framework
                JLoader::register('hwdMediaShareHelperMobile', JPATH_ROOT.'/components/com_hwdmediashare/helpers/mobile.php');
                $mobile = hwdMediaShareHelperMobile::getInstance();

                $quality = $config->get('video_quality');
                if (JRequest::getInt('quality'))
                {
                        $quality = JRequest::getInt('quality');
                }
                if ($override)
                {
                        $quality = $override;
                }
                if ($mobile->_isMobile)
                {
                        $quality = 360;
                }
                
                switch ($quality)
                {
                    default:
                    case 240:
                    case 360:
                        $fileTypes = array(18,19,20,21);
                        break;
                    case 480:
                        $fileTypes = array(19,20,21,18);
                        break;
                    case 720:
                        $fileTypes = array(20,21,19,18);
                        break;
                    case 1080:
                        $fileTypes = array(21,20,19,18);
                        break;
                }

                foreach ($fileTypes as $fileType)
                {
                        $folders = hwdMediaShareFiles::getFolders($item->key);
                        $filename = hwdMediaShareFiles::getFilename($item->key, $fileType);
                        $ext = hwdMediaShareFiles::getExtension($item, $fileType);
                        $path = hwdMediaShareFiles::getPath($folders, $filename, $ext);
                        if (file_exists($path))
                        {
                                return hwdMediaShareDownloads::url($item, $fileType);
                        }
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
                // Load hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                // Load the mobile framework
                JLoader::register('hwdMediaShareHelperMobile', JPATH_ROOT.'/components/com_hwdmediashare/helpers/mobile.php');
                $mobile = hwdMediaShareHelperMobile::getInstance();

                $quality = $config->get('video_quality');
                if (JRequest::getInt('quality'))
                {
                        $quality = JRequest::getInt('quality');
                }
                if ($override)
                {
                        $quality = $override;
                }
                if ($mobile->_isMobile)
                {
                        $quality = 360;
                }
                
                switch ($quality)
                {
                    default:
                    case 240:
                    case 360:
                        $fileTypes = array(22,23,24,25);
                        break;
                    case 480:
                        $fileTypes = array(23,24,25,22);
                        break;
                    case 720:
                        $fileTypes = array(24,25,23,22);
                        break;
                    case 1080:
                        $fileTypes = array(25,24,23,22);
                        break;
                }

                foreach ($fileTypes as $fileType)
                {
                        $folders = hwdMediaShareFiles::getFolders($item->key);
                        $filename = hwdMediaShareFiles::getFilename($item->key, $fileType);
                        $ext = hwdMediaShareFiles::getExtension($item, $fileType);
                        $path = hwdMediaShareFiles::getPath($folders, $filename, $ext);
                        if (file_exists($path))
                        {
                                return hwdMediaShareDownloads::url($item, $fileType);
                        }
                }

                return false;
	}

        /**
	 * Method to generate an image
         *
	 * @since   0.1
	 **/
	public function processImage($process, $fileType, $size)
	{
                // Create a new query object.
                $db = JFactory::getDBO();

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
                        // Get information on original
                        if(substr(PHP_OS, 0, 3) == "WIN")
                        {
                                $command = "\"".$config->get('path_ffmpeg', '/usr/bin/ffmpeg')."\" -i $pathSource 2>&1";
                                exec($command, $output);
                        }
                        else
                        {
                                $command = $config->get('path_ffmpeg', '/usr/bin/ffmpeg')." -i $pathSource 2>&1";
                                exec($command, $output);
                        }
                 
                        $flatoutput = is_array($output) ? implode("\n",$output) : $output;
                        if (empty($flatoutput))
                        {
                                $log->status = 3;
                                $log->input = $command;
                                $log->output = $flatoutput;

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
                                        $log->input = $command;
                                        $log->output = $flatoutput;

                                        // Add process log
                                        hwdMediaShareProcesses::addLog($log);
                                        return $log;
                                }
                                
                                $pos = strpos($flatoutput, "not found");
                                if ($pos !== false)
                                {
                                        $log->status = 3;
                                        $log->input = $command;
                                        $log->output = $flatoutput;
                                
                                        // Add process log
                                        hwdMediaShareProcesses::addLog($log);
                                        return $log;
                                }
                             
                                $pos = strpos($flatoutput, "Permission denied");
                                if ($pos !== false)
                                {
                                        $log->status = 3;
                                        $log->input = $command;
                                        $log->output = $flatoutput;
                                
                                        // Add process log
                                        hwdMediaShareProcesses::addLog($log);
                                        return $log;
                                }
                                
                                // Assume successful
                                $log->status = 2;
                                $log->input = $command;
                                $log->output = $flatoutput;

                                // Add process log
                                hwdMediaShareProcesses::addLog($log); 
                                
                                // Reset status
                                $log->status = 3;                                
                        }
                        
                        $ffmpeg_version  = 0;
                        $input_width  = 0;
                        $input_height = 0;
                        $input_bitrate  = 0;
                        $duration = 0;

                        // Get ffmpeg version
                        if ( preg_match( '#FFmpeg version(.*?), Copyright#', implode("\n",$output), $matches ) )
                        {
                                $ffmpeg_version = trim($matches[1]);
                        }
                        elseif ( preg_match( '#ffmpeg version(.*?) Copyright#i', implode("\n",$output), $matches ) )
                        {
                                $ffmpeg_version = trim($matches[1]);
                        }
                        
                        // Get original size
                        if ( preg_match( '/Stream.*Video:.* (\d+)x(\d+).* (\d+\.\d+|\d+) tbr/', implode("\n",$output), $matches ) )
                        {
                                $input_width = $matches[1];
                                $input_height= $matches[2];
                        }
                        elseif ( preg_match( '/Stream.*Video:.* (\d+)x(\d+).* (\d+\.\d+|\d+) tb/', implode("\n",$output), $matches ) )
                        {
                                $input_width = $matches[1];
                                $input_height= $matches[2];
                        }

                        // Get duration
                        if ( preg_match('/Duration: (.*?),/', implode("\n",$output), $matches) )
                        {
                                $duration_string = $matches[1];
                                list($hr,$m,$s) = explode(':', $duration_string);
                                $duration = ( (int)$hr*3600 ) + ( (int)$m*60 ) + (int)$s;
                                $duration = (int) $duration;
                        }
                        
                        if ($input_width == 0 || $input_height == 0)
                        {
                                $log->output = JText::_('COM_HWDMS_ERROR_COULD_NOT_RETRIEVE_SOURCE_DIMENSIONS');
                                $log->status = 3;

                                // Add process log
                                hwdMediaShareProcesses::addLog($log);
                                return $log;
                        }

                        if ($input_height < $size)
                        {
                                $log->output = JText::_('COM_HWDMS_ERROR_ORIGINAL_SMALLER_THAN_DEST');
                                $log->status = 4;

                                // Add process log
                                hwdMediaShareProcesses::addLog($log);
                                return $log;
                        }
                        else
                        {
                                $foldersDest = hwdMediaShareFiles::getFolders($item->key);
                                $filenameDest = hwdMediaShareFiles::getFilename($item->key, $fileType);
                                $extDest = hwdMediaShareFiles::getExtension($item, $fileType);

                                $pathDest = hwdMediaShareFiles::getPath($foldersDest, $filenameDest, $extDest);

                                // Calculate input aspect
                                $input_aspect = $input_width / $input_height;
                                $output_aspect = ($input_aspect > 0 ? $input_aspect : 1.333);

                                // Calculate output sizes
                                $output_width = intval($size*$output_aspect);
                                $output_width % 2 == 1 ? $output_width += 1: false;
                                $output_height= $size;

                                // Calculate padding (for black bar letterboxing/pillarboxing)
                                $input_aspect = $input_width / $input_height;
                                $conv_height = intval ( ($output_width / $input_aspect) );
                                $conv_height % 2 == 1 ? $conv_height -= 1: false;
                                $conv_pad = intval ( ( ($output_height - $conv_height) / 2.0) );
                                $conv_pad % 2 == 1 ? $conv_pad -= 1: false;

                                if($input_aspect < 1.33333333333333)
                                {
                                        $aspect_mode = 'pillarboxing';
                                }
                                else
                                {
                                        $aspect_mode = 'letterboxing';
                                }

                                if ($conv_pad < 0)
                                {
                                        $input_aspect = $input_width / $input_height;
                                        $conv_width = intval ( ($output_height * $input_aspect) );
                                        $conv_width % 2 == 1 ? $conv_width -= 1: false;
                                        $conv_pad = intval ( ( ($output_width - $conv_width) / 2.0) );
                                        $conv_pad % 2 == 1 ? $conv_pad -= 1: false;

                                        $conv_pad = abs($conv_pad);
                                        $pad = " -vf pad=$output_width:$output_height:$conv_pad:0 ";

                                        $wxh = $conv_width .'x'. $output_height;
                                }
                                else
                                {
                                        $wxh = $output_width .'x'. $conv_height;
                                        $pad = " -vf pad=$output_width:$output_height:0:$conv_pad ";
                                }
                                
                                if (version_compare($ffmpeg_version, '0.7.0', '<') || $conv_pad == 0)
                                {
                                        $pad = '';
                                }
                                
                                // Take the screenshot at 4 seconds into the movie unless the duration can be obtained, 
                                // in which case take the screenshot half way through
                                $offset = 4;
                                if ($duration)
                                {
                                        $offset = $duration/2;
                                        $offset = (int) $offset;
                                }

                                try
                                {
                                        if(substr(PHP_OS, 0, 3) == "WIN")
                                        {
                                                $log->input = "\"".$config->get('path_ffmpeg', '/usr/bin/ffmpeg')."\" -y -itsoffset -$offset -i $pathSource -vcodec mjpeg -vframes 1 -an -f rawvideo -s $wxh $pad $pathDest 2>&1";
                                                exec($log->input, $log->output);
                                        }
                                        else
                                        {
                                                $log->input = $config->get('path_ffmpeg', '/usr/bin/ffmpeg')." -y -itsoffset -$offset -i $pathSource -vcodec mjpeg -vframes 1 -an -f rawvideo -s $wxh $pad $pathDest 2>&1";
                                                exec($log->input, $log->output);
                                        }

                                        if (file_exists($pathDest) && filesize($pathDest) > 0)
                                        {
                                                $log->status = 2;
                                        }
                                        else if (file_exists($pathDest) && filesize($pathDest) == 0)
                                        {
                                                jimport( 'joomla.filesystem.file' );
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
	public function processFlv($process, $fileType, $size)
	{
                // Create a new query object.
                $db = JFactory::getDBO();

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
                        // Get information on original
                        if(substr(PHP_OS, 0, 3) == "WIN")
                        {
                                $command = "\"".$config->get('path_ffmpeg', '/usr/bin/ffmpeg')."\" -i $pathSource 2>&1";
                                exec($command, $output);
                        }
                        else
                        {
                                $command = $config->get('path_ffmpeg', '/usr/bin/ffmpeg')." -i $pathSource 2>&1";
                                exec($command, $output);
                        }
                        
                        $flatoutput = is_array($output) ? implode("\n",$output) : $output;
                        if (empty($flatoutput))
                        {
                                $log->status = 3;
                                $log->input = $command;
                                $log->output = $flatoutput;

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
                                        $log->input = $command;
                                        $log->output = $flatoutput;

                                        // Add process log
                                        hwdMediaShareProcesses::addLog($log);
                                        return $log;
                                }
                                
                                $pos = strpos($flatoutput, "not found");
                                if ($pos !== false)
                                {
                                        $log->status = 3;
                                        $log->input = $command;
                                        $log->output = $flatoutput;
                                
                                        // Add process log
                                        hwdMediaShareProcesses::addLog($log);
                                        return $log;
                                }
                             
                                $pos = strpos($flatoutput, "Permission denied");
                                if ($pos !== false)
                                {
                                        $log->status = 3;
                                        $log->input = $command;
                                        $log->output = $flatoutput;
                                
                                        // Add process log
                                        hwdMediaShareProcesses::addLog($log);
                                        return $log;
                                }
                                
                                // Assume successful
                                $log->status = 2;
                                $log->input = $command;
                                $log->output = $flatoutput;

                                // Add process log
                                hwdMediaShareProcesses::addLog($log);
                                
                                // Reset status
                                $log->status = 3;                                
                        }
                    
                        $ffmpeg_version  = 0;
                        $input_width  = 0;
                        $input_height = 0;
                        $input_bitrate  = 0;

                        // Get ffmpeg version
                        if ( preg_match( '#FFmpeg version(.*?), Copyright#', implode("\n",$output), $matches ) )
                        {
                                $ffmpeg_version = trim($matches[1]);
                        }
                        elseif ( preg_match( '#ffmpeg version(.*?) Copyright#i', implode("\n",$output), $matches ) )
                        {
                                $ffmpeg_version = trim($matches[1]);
                        }
                        
                        // Get original size
                        if ( preg_match( '/Stream.*Video:.* (\d+)x(\d+).* (\d+\.\d+|\d+) tbr/', implode("\n",$output), $matches ) )
                        {
                                $input_width = $matches[1];
                                $input_height= $matches[2];
                        }
                        elseif ( preg_match( '/Stream.*Video:.* (\d+)x(\d+).* (\d+\.\d+|\d+) tb/', implode("\n",$output), $matches ) )
                        {
                                $input_width = $matches[1];
                                $input_height= $matches[2];
                        }

                        // Get original bitrate
                        // Outdated pcre (perl-compatible regular expressions) libraries case error:
                        // Compilation failed: unrecognized character
                        // Therefore, surpress error and offer alternative
                        if ( @preg_match('/bitrate:\s(?<bitrate>\d+)\skb\/s/', implode("\n",$output), $matches) )
                        {
                                $input_bitrate = $matches[1];
                        }
                        elseif ( preg_match('/bitrate:\s(.*?)\skb\/s/', implode("\n",$output), $matches ) )
                        {
                                $input_bitrate = $matches[1];
                        }
                        
                        if ($input_width == 0 || $input_height == 0 || $input_bitrate == 0)
                        {
                                $log->output = JText::_('COM_HWDMS_ERROR_COULD_NOT_RETRIEVE_SOURCE_PARAMETERS');
                                $log->status = 3;

                                // Add process log
                                hwdMediaShareProcesses::addLog($log);
                                return $log;
                        }
                                        
                        $bitrate = min($input_bitrate, hwdMediaShareVideos::getVideoBitrate($size));

                        if (($input_height >= $size) || $size == '240')
                        {
                                $foldersDest = hwdMediaShareFiles::getFolders($item->key);
                                $filenameDest = hwdMediaShareFiles::getFilename($item->key, $fileType);
                                $extDest = hwdMediaShareFiles::getExtension($item, $fileType);

                                $pathDest = hwdMediaShareFiles::getPath($foldersDest, $filenameDest, $extDest);

                                // Calculate input aspect
                                $input_aspect = $input_width / $input_height;
                                $output_aspect = ($input_aspect > 0 ? $input_aspect : 1.333);

                                // Calculate output sizes
                                $output_width = intval($size*$output_aspect);
                                $output_width % 2 == 1 ? $output_width += 1: false;
                                $output_height= $size;

                                // Calculate padding (for black bar letterboxing/pillarboxing)
                                $input_aspect = $input_width / $input_height;
                                $conv_height = intval ( ($output_width / $input_aspect) );
                                $conv_height % 2 == 1 ? $conv_height -= 1: false;
                                $conv_pad = intval ( ( ($output_height - $conv_height) / 2.0) );
                                $conv_pad % 2 == 1 ? $conv_pad -= 1: false;

                                if($input_aspect < 1.33333333333333)
                                {
                                        $aspect_mode = 'pillarboxing';
                                }
                                else
                                {
                                        $aspect_mode = 'letterboxing';
                                }

                                if ($conv_pad < 0)
                                {
                                        $input_aspect = $input_width / $input_height;
                                        $conv_width = intval ( ($output_height * $input_aspect) );
                                        $conv_width % 2 == 1 ? $conv_width -= 1: false;
                                        $conv_pad = intval ( ( ($output_width - $conv_width) / 2.0) );
                                        $conv_pad % 2 == 1 ? $conv_pad -= 1: false;

                                        $conv_pad = abs($conv_pad);
                                        $pad = " -vf pad=$output_width:$output_height:$conv_pad:0 ";

                                        $wxh = $conv_width .'x'. $output_height;
                                }
                                else
                                {
                                        $wxh = $output_width .'x'. $conv_height;
                                        $pad = " -vf pad=$output_width:$output_height:0:$conv_pad ";
                                }

                                if (version_compare($ffmpeg_version, '0.7.0', '<') || $conv_pad == 0)
                                {
                                        $pad = '';
                                }
                        
                                try
                                {
	                                if(substr(PHP_OS, 0, 3) == "WIN")
                                        {
                                                $log->input = "\"".$config->get('path_ffmpeg', '/usr/bin/ffmpeg')."\" -y -i $pathSource -ab 128 -ar 22050 -b ".$bitrate."k -s $wxh $pad -g 25 -keyint_min 25 $pathDest 2>&1";
                                                exec($log->input, $log->output);
                                        }
                                        else
                                        {
                                                $log->input = $config->get('path_ffmpeg', '/usr/bin/ffmpeg')." -y -i $pathSource -ab 128 -ar 22050 -b ".$bitrate."k -s $wxh $pad -g 25 -keyint_min 25 $pathDest 2>&1";
                                                exec($log->input, $log->output);
                                        }

                                        if (file_exists($pathDest) && filesize($pathDest) > 0)
                                        {
                                                $log->status = 2;
                                        }
                                        else if (file_exists($pathDest) && filesize($pathDest) == 0)
                                        {
                                                jimport( 'joomla.filesystem.file' );
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
                                        // Add watermark
                                        hwdMediaShareVideos::processWatermark($process, $fileType);

                                        // Add file to database
                                        hwdMediaShareFactory::load('files');
                                        hwdMediaShareFiles::add($item,$fileType);
                                        return $log;
                                }

                                $log->output = JText::_('COM_HWDMS_ERROR_DESTINATION_MEDIA_NOT_EXIST');
                        }
                        else
                        {
                                $log->output = JText::_('COM_HWDMS_ERROR_ORIGINAL_SMALLER_THAN_DEST');
                                $log->status = 4;

                                // Add process log
                                hwdMediaShareProcesses::addLog($log);
                                return $log;
                        }
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
	public function processMp4($process, $fileType, $size)
	{
                // Create a new query object.
                $db = JFactory::getDBO();

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
                        // Get information on original
                        if(substr(PHP_OS, 0, 3) == "WIN")
                        {
                                $command = "\"".$config->get('path_ffmpeg', '/usr/bin/ffmpeg')."\" -i $pathSource 2>&1";
                                exec($command, $output);
                        }
                        else
                        {
                                $command = $config->get('path_ffmpeg', '/usr/bin/ffmpeg')." -i $pathSource 2>&1";
                                exec($command, $output);
                        }

                        $flatoutput = is_array($output) ? implode("\n",$output) : $output;
                        if (empty($flatoutput))
                        {
                                $log->status = 3;
                                $log->input = $command;
                                $log->output = $flatoutput;

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
                                        $log->input = $command;
                                        $log->output = $flatoutput;

                                        // Add process log
                                        hwdMediaShareProcesses::addLog($log);
                                        return $log;
                                }

                                $pos = strpos($flatoutput, "not found");
                                if ($pos !== false)
                                {
                                        $log->status = 3;
                                        $log->input = $command;
                                        $log->output = $flatoutput;
                                
                                        // Add process log
                                        hwdMediaShareProcesses::addLog($log);
                                        return $log;
                                }
                             
                                $pos = strpos($flatoutput, "Permission denied");
                                if ($pos !== false)
                                {
                                        $log->status = 3;
                                        $log->input = $command;
                                        $log->output = $flatoutput;
                                
                                        // Add process log
                                        hwdMediaShareProcesses::addLog($log);
                                        return $log;
                                }
                                
                                // Assume successful
                                $log->status = 2;
                                $log->input = $command;
                                $log->output = $flatoutput;

                                // Add process log
                                hwdMediaShareProcesses::addLog($log);
                                
                                // Reset status
                                $log->status = 3;
                        }
                        
                        $ffmpeg_version  = 0;
                        $input_width  = 0;
                        $input_height = 0;
                        $input_bitrate  = 0;

                        // Get ffmpeg version
                        if ( preg_match( '#FFmpeg version(.*?), Copyright#', implode("\n",$output), $matches ) )
                        {
                                $ffmpeg_version = trim($matches[1]);
                        }
                        elseif ( preg_match( '#ffmpeg version(.*?) Copyright#i', implode("\n",$output), $matches ) )
                        {
                                $ffmpeg_version = trim($matches[1]);
                        }
                        
                        // Get original size
                        if ( preg_match( '/Stream.*Video:.* (\d+)x(\d+).* (\d+\.\d+|\d+) tbr/', implode("\n",$output), $matches ) )
                        {
                                $input_width = $matches[1];
                                $input_height= $matches[2];
                        }
                        elseif ( preg_match( '/Stream.*Video:.* (\d+)x(\d+).* (\d+\.\d+|\d+) tb/', implode("\n",$output), $matches ) )
                        {
                                $input_width = $matches[1];
                                $input_height= $matches[2];
                        }
                        
                        // Get original bitrate
                        // Outdated pcre (perl-compatible regular expressions) libraries case error:
                        // Compilation failed: unrecognized character
                        // Therefore, surpress error and offer alternative
                        if ( @preg_match('/bitrate:\s(?<bitrate>\d+)\skb\/s/', implode("\n",$output), $matches) )
                        {
                                $input_bitrate = $matches[1];
                        }
                        elseif ( preg_match('/bitrate:\s(.*?)\skb\/s/', implode("\n",$output), $matches ) )
                        {
                                $input_bitrate = $matches[1];
                        }
                        
                        if ($input_width == 0 || $input_height == 0 || $input_bitrate == 0)
                        {
                                $log->output = JText::_('COM_HWDMS_ERROR_COULD_NOT_RETRIEVE_SOURCE_PARAMETERS');
                                $log->status = 3;

                                // Add process log
                                hwdMediaShareProcesses::addLog($log);
                                return $log;
                        }

                        $bitrate = min($input_bitrate, hwdMediaShareVideos::getVideoBitrate($size));

                        if (($input_height >= $size) || $size == '360')
                        {
                                $foldersDest = hwdMediaShareFiles::getFolders($item->key);
                                $filenameDest = hwdMediaShareFiles::getFilename($item->key, $fileType);
                                $extDest = hwdMediaShareFiles::getExtension($item, $fileType);

                                $pathDest = hwdMediaShareFiles::getPath($foldersDest, $filenameDest, $extDest);

                                // Calculate input aspect
                                $input_aspect = $input_width / $input_height;
                                $output_aspect = ($input_aspect > 0 ? $input_aspect : 1.333);

                                // Calculate output sizes
                                $output_width = intval($size*$output_aspect);
                                $output_width % 2 == 1 ? $output_width += 1: false;
                                $output_height= $size;

                                // Calculate padding (for black bar letterboxing/pillarboxing)
                                $input_aspect = $input_width / $input_height;
                                $conv_height = intval ( ($output_width / $input_aspect) );
                                $conv_height % 2 == 1 ? $conv_height -= 1: false;
                                $conv_pad = intval ( ( ($output_height - $conv_height) / 2.0) );
                                $conv_pad % 2 == 1 ? $conv_pad -= 1: false;

                                if($input_aspect < 1.33333333333333)
                                {
                                        $aspect_mode = 'pillarboxing';
                                }
                                else
                                {
                                        $aspect_mode = 'letterboxing';
                                }

                                if ($conv_pad < 0)
                                {
                                        $input_aspect = $input_width / $input_height;
                                        $conv_width = intval ( ($output_height * $input_aspect) );
                                        $conv_width % 2 == 1 ? $conv_width -= 1: false;
                                        $conv_pad = intval ( ( ($output_width - $conv_width) / 2.0) );
                                        $conv_pad % 2 == 1 ? $conv_pad -= 1: false;

                                        $conv_pad = abs($conv_pad);
                                        $pad = " -vf pad=$output_width:$output_height:$conv_pad:0 ";

                                        $wxh = $conv_width .'x'. $output_height;
                                }
                                else
                                {
                                        $wxh = $output_width .'x'. $conv_height;
                                        $pad = " -vf pad=$output_width:$output_height:0:$conv_pad ";
                                }

                                if (version_compare($ffmpeg_version, '0.7.0', '<') || $conv_pad == 0)
                                {
                                        $pad = '';
                                }
                                
                                // First attempt (@alduccino commands - CRF with PRESET)
                                try
                                {
                                        // Set parameter values
                                        switch ($size)
                                        {
                                            case '1080':
                                            case '720':
                                                $vbit = 2000;
                                                $min  = 1550;
                                                $max  = 2000;
                                                $buff = 1550;
                                                $crf  = 18;
                                               break;
                                            case '480':
                                            case '360': 
                                            default:                                                
                                                $vbit = 1000;
                                                $min  = 800;
                                                $max  = 1000;
                                                $buff = 800;
                                                $crf  = 18;
                                                break;
                                        }
                                        if(substr(PHP_OS, 0, 3) == "WIN")
                                        {
                                                $log->input = "\"".$config->get('path_ffmpeg', '/usr/bin/ffmpeg')."\" -y -i $pathSource -strict experimental -acodec aac -ac 2 -ab 192k -s $wxh -aspect 16:9 -r 24000/1001 -vcodec libx264 -b:v ".$vbit."k -minrate ".$min."k -maxrate ".$max."k -bufsize ".$buff."K -crf $crf -preset fast -f mp4 -threads 0 $pathDest 2>&1";
                                                exec($log->input, $log->output);
                                        }
                                        else
                                        {
                                                $log->input = $config->get('path_ffmpeg', '/usr/bin/ffmpeg')." -y -i $pathSource -strict experimental -acodec aac -ac 2 -ab 192k -s $wxh -aspect 16:9 -r 24000/1001 -vcodec libx264 -b:v ".$vbit."k -minrate ".$min."k -maxrate ".$max."k -bufsize ".$buff."K -crf $crf -preset fast -f mp4 -threads 0 $pathDest 2>&1";
                                                exec($log->input, $log->output);
                                        }

                                        if (file_exists($pathDest) && filesize($pathDest) > 0)
                                        {
                                                $log->status = 2;
                                        }
                                        else if (file_exists($pathDest) && filesize($pathDest) == 0)
                                        {
                                                jimport( 'joomla.filesystem.file' );
                                                JFile::delete($pathDest);
                                        }

                                        // Add process log
                                        hwdMediaShareProcesses::addLog($log);                                        
                                }
                                catch(Exception $e)
                                {
                                        $output = $e->getMessage();
                                }
        
                                // Second attempt
                                if (!file_exists($pathDest))
                                {
                                        try
                                        {
                                                if(substr(PHP_OS, 0, 3) == "WIN")
                                                {
                                                        $log->input = "\"".$config->get('path_ffmpeg', '/usr/bin/ffmpeg')."\" -y -i $pathSource -strict experimental -acodec aac -ac 2 -ab 160k -s $wxh $pad -vcodec libx264 -vpre ipod640 -b ".$bitrate."k -f mp4 -threads 0 $pathDest 2>&1";
                                                        exec($log->input, $log->output);
                                                }
                                                else
                                                {
                                                        $log->input = $config->get('path_ffmpeg', '/usr/bin/ffmpeg')." -y -i $pathSource -strict experimental -acodec aac -ac 2 -ab 160k -s $wxh $pad -vcodec libx264 -vpre ipod640 -b ".$bitrate."k -f mp4 -threads 0 $pathDest 2>&1";
                                                        exec($log->input, $log->output);
                                                }

                                                if (file_exists($pathDest) && filesize($pathDest) > 0)
                                                {
                                                        $log->status = 2;
                                                }
                                                else if (file_exists($pathDest) && filesize($pathDest) == 0)
                                                {
                                                        jimport( 'joomla.filesystem.file' );
                                                        JFile::delete($pathDest);
                                                }
                                        }
                                        catch(Exception $e)
                                        {
                                                $output = $e->getMessage();
                                        }
                                        
                                        // Add process log
                                        hwdMediaShareProcesses::addLog($log);
                                }                                


                                // Third attempt
                                if (!file_exists($pathDest))
                                {
                                        try
                                        {
                                                $ffpreset_libx264_slow = " -coder 1 -flags +loop -cmp +chroma -partitions +parti8x8+parti4x4+partp8x8+partb8x8 -me_method umh -subq 8 -me_range 16 -g 250 -keyint_min 25 -sc_threshold 40 -i_qfactor 0.71 -b_strategy 2 -qcomp 0.6 -qmin 10 -qmax 51 -qdiff 4 -bf 3 -refs 5 -directpred 3 -trellis 1 -flags2 +bpyramid+mixed_refs+wpred+dct8x8+fastpskip -wpredp 2 -rc_lookahead 50 ";
                                                $ffpreset_libx264_ipod640 = " -coder 0 -bf 0 -refs 1 -flags2 -wpred-dct8x8 -level 30 -maxrate 10000000 -bufsize 10000000 -wpredp 0 ";
                                                if(substr(PHP_OS, 0, 3) == "WIN")
                                                {
                                                        $log->input = "\"".$config->get('path_ffmpeg', '/usr/bin/ffmpeg')."\" -y -i $pathSource -strict experimental -acodec aac -ac 2 -ab 160k -s $wxh $pad -vcodec libx264 $ffpreset_libx264_slow $ffpreset_libx264_ipod640 -b ".$bitrate."k -f mp4 -threads 0 $pathDest 2>&1";
                                                        exec($log->input, $log->output);
                                                }
                                                else
                                                {
                                                        $log->input = $config->get('path_ffmpeg', '/usr/bin/ffmpeg')." -y -i $pathSource -strict experimental -acodec aac -ac 2 -ab 160k -s $wxh $pad -vcodec libx264 $ffpreset_libx264_slow $ffpreset_libx264_ipod640 -b ".$bitrate."k -f mp4 -threads 0 $pathDest 2>&1";
                                                        exec($log->input, $log->output);
                                                }

                                                if (file_exists($pathDest) && filesize($pathDest) > 0)
                                                {
                                                        $log->status = 2;
                                                }
                                                else if (file_exists($pathDest) && filesize($pathDest) == 0)
                                                {
                                                        jimport( 'joomla.filesystem.file' );
                                                        JFile::delete($pathDest);
                                                }
                                        }
                                        catch(Exception $e)
                                        {
                                                $output = $e->getMessage();
                                        }

                                        // Add process log
                                        hwdMediaShareProcesses::addLog($log);
                                }

                                if (file_exists($pathDest) && filesize($pathDest) > 0)
                                {
                                        // Add watermark
                                        hwdMediaShareVideos::processWatermark($process, $fileType);

                                        // Add file to database
                                        hwdMediaShareFactory::load('files');
                                        hwdMediaShareFiles::add($item,$fileType);
                                        return $log;
                                }

                                $log->output = JText::_('COM_HWDMS_ERROR_DESTINATION_MEDIA_NOT_EXIST');
                        }
                        else
                        {
                                $log->output = JText::_('COM_HWDMS_ERROR_ORIGINAL_SMALLER_THAN_DEST');
                                $log->status = 4;

                                // Add process log
                                hwdMediaShareProcesses::addLog($log);
                                return $log;
                        }
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
	public function processWebm($process, $fileType, $size)
	{
                // Create a new query object.
                $db = JFactory::getDBO();

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
                        // Get information on original
                        if(substr(PHP_OS, 0, 3) == "WIN")
                        {
                                $command = "\"".$config->get('path_ffmpeg', '/usr/bin/ffmpeg')."\" -i $pathSource 2>&1";
                                exec($command, $output);
                        }
                        else
                        {
                                $command = $config->get('path_ffmpeg', '/usr/bin/ffmpeg')." -i $pathSource 2>&1";
                                exec($command, $output);
                        }
                        
                        $flatoutput = is_array($output) ? implode("\n",$output) : $output;
                        if (empty($flatoutput))
                        {
                                $log->status = 3;
                                $log->input = $command;
                                $log->output = $flatoutput;

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
                                        $log->input = $command;
                                        $log->output = $flatoutput;

                                        // Add process log
                                        hwdMediaShareProcesses::addLog($log);
                                        return $log;
                                }

                                $pos = strpos($flatoutput, "not found");
                                if ($pos !== false)
                                {
                                        $log->status = 3;
                                        $log->input = $command;
                                        $log->output = $flatoutput;
                                
                                        // Add process log
                                        hwdMediaShareProcesses::addLog($log);
                                        return $log;
                                }
                             
                                $pos = strpos($flatoutput, "Permission denied");
                                if ($pos !== false)
                                {
                                        $log->status = 3;
                                        $log->input = $command;
                                        $log->output = $flatoutput;
                                
                                        // Add process log
                                        hwdMediaShareProcesses::addLog($log);
                                        return $log;
                                }
                                
                                // Assume successful
                                $log->status = 2;
                                $log->input = $command;
                                $log->output = $flatoutput;

                                // Add process log
                                hwdMediaShareProcesses::addLog($log); 
                                
                                // Reset status
                                $log->status = 3;                                
                        }
                        
                        $ffmpeg_version  = 0;
                        $input_width  = 0;
                        $input_height = 0;
                        $input_bitrate  = 0;

                        // Get ffmpeg version
                        if ( preg_match( '#FFmpeg version(.*?), Copyright#', implode("\n",$output), $matches ) )
                        {
                                $ffmpeg_version = trim($matches[1]);
                        }
                        elseif ( preg_match( '#ffmpeg version(.*?) Copyright#i', implode("\n",$output), $matches ) )
                        {
                                $ffmpeg_version = trim($matches[1]);
                        }
                        
                        // Get original size
                        if ( preg_match( '/Stream.*Video:.* (\d+)x(\d+).* (\d+\.\d+|\d+) tbr/', implode("\n",$output), $matches ) )
                        {
                                $input_width = $matches[1];
                                $input_height= $matches[2];
                        }
                        elseif ( preg_match( '/Stream.*Video:.* (\d+)x(\d+).* (\d+\.\d+|\d+) tb/', implode("\n",$output), $matches ) )
                        {
                                $input_width = $matches[1];
                                $input_height= $matches[2];
                        }
                        
                        // Get original bitrate
                        // Outdated pcre (perl-compatible regular expressions) libraries case error:
                        // Compilation failed: unrecognized character
                        // Therefore, surpress error and offer alternative
                        if ( @preg_match('/bitrate:\s(?<bitrate>\d+)\skb\/s/', implode("\n",$output), $matches) )
                        {
                                $input_bitrate = $matches[1];
                        }
                        elseif ( preg_match('/bitrate:\s(.*?)\skb\/s/', implode("\n",$output), $matches ) )
                        {
                                $input_bitrate = $matches[1];
                        }

                        if ($input_width == 0 || $input_height == 0 || $input_bitrate == 0)
                        {
                                $log->output = JText::_('COM_HWDMS_ERROR_COULD_NOT_RETRIEVE_SOURCE_PARAMETERS');
                                $log->status = 3;

                                // Add process log
                                hwdMediaShareProcesses::addLog($log);
                                return $log;
                        }

                        $bitrate = min($input_bitrate, hwdMediaShareVideos::getVideoBitrate($size));

                        if (($input_height >= $size) || $size == '360')
                        {
                                $foldersDest = hwdMediaShareFiles::getFolders($item->key);
                                $filenameDest = hwdMediaShareFiles::getFilename($item->key, $fileType);
                                $extDest = hwdMediaShareFiles::getExtension($item, $fileType);

                                $pathDest = hwdMediaShareFiles::getPath($foldersDest, $filenameDest, $extDest);

                                // Calculate input aspect
                                $input_aspect = $input_width / $input_height;
                                $output_aspect = ($input_aspect > 0 ? $input_aspect : 1.333);

                                // Calculate output sizes
                                $output_width = intval($size*$output_aspect);
                                $output_width % 2 == 1 ? $output_width += 1: false;
                                $output_height= $size;

                                // Calculate padding (for black bar letterboxing/pillarboxing)
                                $input_aspect = $input_width / $input_height;
                                $conv_height = intval ( ($output_width / $input_aspect) );
                                $conv_height % 2 == 1 ? $conv_height -= 1: false;
                                $conv_pad = intval ( ( ($output_height - $conv_height) / 2.0) );
                                $conv_pad % 2 == 1 ? $conv_pad -= 1: false;

                                if($input_aspect < 1.33333333333333)
                                {
                                        $aspect_mode = 'pillarboxing';
                                }
                                else
                                {
                                        $aspect_mode = 'letterboxing';
                                }

                                if ($conv_pad < 0)
                                {
                                        $input_aspect = $input_width / $input_height;
                                        $conv_width = intval ( ($output_height * $input_aspect) );
                                        $conv_width % 2 == 1 ? $conv_width -= 1: false;
                                        $conv_pad = intval ( ( ($output_width - $conv_width) / 2.0) );
                                        $conv_pad % 2 == 1 ? $conv_pad -= 1: false;

                                        $conv_pad = abs($conv_pad);
                                        $pad = " -vf pad=$output_width:$output_height:$conv_pad:0 ";

                                        $wxh = $conv_width .'x'. $output_height;
                                }
                                else
                                {
                                        $wxh = $output_width .'x'. $conv_height;
                                        $pad = " -vf pad=$output_width:$output_height:0:$conv_pad ";
                                }
                                
                                $opt_quality = '-quality good';
                                $opt_speed = '-quality good';
                                $opt_slices = '-slices 4';
                                $opt_arnr = '-arnr_max_frames 7 -arnr_strength 5 -arnr_type 3';
                                if (version_compare($ffmpeg_version, '0.7.0', '<') || $conv_pad == 0)
                                {
                                        $pad = '';
                                        $opt_quality = '';
                                        $opt_speed = '';
                                        $opt_slices = '';
                                        $opt_arnr = '';
                                }
                                if (version_compare($ffmpeg_version, '0.7.0', '<'))
                                {
                                        $opt_quality = '';
                                        $opt_speed = '';
                                        $opt_slices = '';
                                        $opt_arnr = '';
                                }
                                 
                                try
                                {
                                        $ffpreset_libvpx_720p_pass1 = " -vcodec libvpx -g 120 -rc_lookahead 16 $opt_quality $opt_speed -profile 0 -qmax 51 -qmin 11 $opt_slices -vb 2M ";
                                        $ffpreset_libvpx_720p_pass2 = " -vcodec libvpx -g 120 -rc_lookahead 16 $opt_quality $opt_speed -profile 0 -qmax 51 -qmin 11 $opt_slices -vb 2M -maxrate 24M -minrate 100k $opt_arnr ";
                                        if(substr(PHP_OS, 0, 3) == "WIN")
                                        {
                                                $input1 = "\"".$config->get('path_ffmpeg', '/usr/bin/ffmpeg')."\" -y -i $pathSource -s $wxh $pad $ffpreset_libvpx_720p_pass1 -b ".$bitrate."k -pass 1 -an -f webm $pathDest 2>&1";
                                                exec($input1, $output1);
                                                $input2 = "\"".$config->get('path_ffmpeg', '/usr/bin/ffmpeg')."\" -y -i $pathSource -s $wxh $pad $ffpreset_libvpx_720p_pass2 -b ".$bitrate."k -pass 2 -acodec libvorbis -ab 90k -f webm $pathDest 2>&1";
                                                exec($input2, $output2);
                                        }
                                        else
                                        {
                                                $input1 = $config->get('path_ffmpeg', '/usr/bin/ffmpeg')." -y -i $pathSource -s $wxh $pad $ffpreset_libvpx_720p_pass1 -b ".$bitrate."k -pass 1 -an -f webm $pathDest 2>&1";
                                                exec($input1, $output1);
                                                $input2 = $config->get('path_ffmpeg', '/usr/bin/ffmpeg')." -y -i $pathSource -s $wxh $pad $ffpreset_libvpx_720p_pass2 -b ".$bitrate."k -pass 2 -acodec libvorbis -ab 90k -f webm $pathDest 2>&1";
                                                exec($input2, $output2);
                                        }

                                        $log->input = "$input1\n\n$input2";
                                        $log->output = array_merge($output1, $output2);

                                        if (file_exists($pathDest) && filesize($pathDest) > 0)
                                        {
                                                $log->status = 2;
                                        }
                                        else if (file_exists($pathDest) && filesize($pathDest) == 0)
                                        {
                                                jimport( 'joomla.filesystem.file' );
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
                                        // Add watermark
                                        hwdMediaShareVideos::processWatermark($process, $fileType);

                                        // Add file to database
                                        hwdMediaShareFactory::load('files');
                                        hwdMediaShareFiles::add($item,$fileType);
                                        return $log;
                                }

                                $log->output = JText::_('COM_HWDMS_ERROR_DESTINATION_MEDIA_NOT_EXIST');
                        }
                        else
                        {
                                $log->output = JText::_('COM_HWDMS_ERROR_ORIGINAL_SMALLER_THAN_DEST');
                                $log->status = 4;

                                // Add process log
                                hwdMediaShareProcesses::addLog($log);
                                return $log;
                        }
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
	public function processOgg($process, $fileType, $size)
	{
                // Create a new query object.
                $db = JFactory::getDBO();

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
                        // Get information on original
                        if(substr(PHP_OS, 0, 3) == "WIN")
                        {
                                $command = "\"".$config->get('path_ffmpeg', '/usr/bin/ffmpeg')."\" -i $pathSource 2>&1";
                                exec($command, $output);
                        }
                        else
                        {
                                $command = $config->get('path_ffmpeg', '/usr/bin/ffmpeg')." -i $pathSource 2>&1";
                                exec($command, $output);
                        }

                        $flatoutput = is_array($output) ? implode("\n",$output) : $output;
                        if (empty($flatoutput))
                        {
                                $log->status = 3;
                                $log->input = $command;
                                $log->output = $flatoutput;

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
                                        $log->input = $command;
                                        $log->output = $flatoutput;

                                        // Add process log
                                        hwdMediaShareProcesses::addLog($log);
                                        return $log;
                                }                               
                                
                                $pos = strpos($flatoutput, "not found");
                                if ($pos !== false)
                                {
                                        $log->status = 3;
                                        $log->input = $command;
                                        $log->output = $flatoutput;
                                
                                        // Add process log
                                        hwdMediaShareProcesses::addLog($log);
                                        return $log;
                                }
                             
                                $pos = strpos($flatoutput, "Permission denied");
                                if ($pos !== false)
                                {
                                        $log->status = 3;
                                        $log->input = $command;
                                        $log->output = $flatoutput;
                                
                                        // Add process log
                                        hwdMediaShareProcesses::addLog($log);
                                        return $log;
                                }

                                // Assume successful
                                $log->status = 2;
                                $log->input = $command;
                                $log->output = $flatoutput;

                                // Add process log
                                hwdMediaShareProcesses::addLog($log); 
                                
                                // Reset status
                                $log->status = 3;                                
                        }
                        
                        $ffmpeg_version  = 0;
                        $input_width  = 0;
                        $input_height = 0;
                        $input_bitrate  = 0;

                        // Get ffmpeg version
                        if ( preg_match( '#FFmpeg version(.*?), Copyright#', implode("\n",$output), $matches ) )
                        {
                                $ffmpeg_version = trim($matches[1]);
                        }
                        elseif ( preg_match( '#ffmpeg version(.*?) Copyright#i', implode("\n",$output), $matches ) )
                        {
                                $ffmpeg_version = trim($matches[1]);
                        }
                        
                        // Get original size
                        if ( preg_match( '/Stream.*Video:.* (\d+)x(\d+).* (\d+\.\d+|\d+) tbr/', implode("\n",$output), $matches ) )
                        {
                                $input_width = $matches[1];
                                $input_height= $matches[2];
                        }
                        elseif ( preg_match( '/Stream.*Video:.* (\d+)x(\d+).* (\d+\.\d+|\d+) tb/', implode("\n",$output), $matches ) )
                        {
                                $input_width = $matches[1];
                                $input_height= $matches[2];
                        }
                        
                        // Get original bitrate
                        // Outdated pcre (perl-compatible regular expressions) libraries case error:
                        // Compilation failed: unrecognized character
                        // Therefore, surpress error and offer alternative
                        if ( @preg_match('/bitrate:\s(?<bitrate>\d+)\skb\/s/', implode("\n",$output), $matches) )
                        {
                                $input_bitrate = $matches[1];
                        }
                        elseif ( preg_match('/bitrate:\s(.*?)\skb\/s/', implode("\n",$output), $matches ) )
                        {
                                $input_bitrate = $matches[1];
                        }
                        
                        if ($input_width == 0 || $input_height == 0 || $input_bitrate == 0)
                        {
                                $log->output = JText::_('COM_HWDMS_ERROR_COULD_NOT_RETRIEVE_SOURCE_PARAMETERS');
                                $log->status = 3;

                                // Add process log
                                hwdMediaShareProcesses::addLog($log);
                                return $log;
                        }

                        $bitrate = min($input_bitrate, hwdMediaShareVideos::getVideoBitrate($size));

                        if (($input_height >= $size) || $size == '360')
                        {
                                $foldersDest = hwdMediaShareFiles::getFolders($item->key);
                                $filenameDest = hwdMediaShareFiles::getFilename($item->key, $fileType);
                                $extDest = hwdMediaShareFiles::getExtension($item, $fileType);

                                $pathDest = hwdMediaShareFiles::getPath($foldersDest, $filenameDest, $extDest);

                                // Calculate input aspect
                                $input_aspect = $input_width / $input_height;
                                $output_aspect = ($input_aspect > 0 ? $input_aspect : 1.333);

                                // Calculate output sizes
                                $output_width = intval($size*$output_aspect);
                                $output_width % 2 == 1 ? $output_width += 1: false;
                                $output_height= $size;

                                // Calculate padding (for black bar letterboxing/pillarboxing)
                                $input_aspect = $input_width / $input_height;
                                $conv_height = intval ( ($output_width / $input_aspect) );
                                $conv_height % 2 == 1 ? $conv_height -= 1: false;
                                $conv_pad = intval ( ( ($output_height - $conv_height) / 2.0) );
                                $conv_pad % 2 == 1 ? $conv_pad -= 1: false;

                                if($input_aspect < 1.33333333333333)
                                {
                                        $aspect_mode = 'pillarboxing';
                                }
                                else
                                {
                                        $aspect_mode = 'letterboxing';
                                }

                                if ($conv_pad < 0)
                                {
                                        $input_aspect = $input_width / $input_height;
                                        $conv_width = intval ( ($output_height * $input_aspect) );
                                        $conv_width % 2 == 1 ? $conv_width -= 1: false;
                                        $conv_pad = intval ( ( ($output_width - $conv_width) / 2.0) );
                                        $conv_pad % 2 == 1 ? $conv_pad -= 1: false;

                                        $conv_pad = abs($conv_pad);
                                        $pad = " -vf pad=$output_width:$output_height:$conv_pad:0 ";

                                        $wxh = $conv_width .'x'. $output_height;
                                }
                                else
                                {
                                        $wxh = $output_width .'x'. $conv_height;
                                        $pad = " -vf pad=$output_width:$output_height:0:$conv_pad ";
                                }
                                
                                if (version_compare($ffmpeg_version, '0.7.0', '<') || $conv_pad == 0)
                                {
                                        $pad = '';
                                }
                                
                                try
                                {
                                        $ffpreset_libx264_slow = " -coder 1 -flags +loop -cmp +chroma -partitions +parti8x8+parti4x4+partp8x8+partb8x8 -me_method umh -subq 8 -me_range 16 -g 250 -keyint_min 25 -sc_threshold 40 -i_qfactor 0.71 -b_strategy 2 -qcomp 0.6 -qmin 10 -qmax 51 -qdiff 4 -bf 3 -refs 5 -directpred 3 -trellis 1 -flags2 +bpyramid+mixed_refs+wpred+dct8x8+fastpskip -wpredp 2 -rc_lookahead 50 ";
                                        $ffpreset_libx264_ipod640 = " -coder 0 -bf 0 -refs 1 -flags2 -wpred-dct8x8 -level 30 -maxrate 10000000 -bufsize 10000000 -wpredp 0 ";
                                        if(substr(PHP_OS, 0, 3) == "WIN")
                                        {
                                                $log->input = "\"".$config->get('path_ffmpeg', '/usr/bin/ffmpeg')."\" -y -i $pathSource -s $wxh $pad -vcodec libtheora -b ".$bitrate."k -acodec libvorbis $pathDest 2>&1";
                                                exec($log->input, $log->output);
                                        }
                                        else
                                        {
                                                $log->input = $config->get('path_ffmpeg', '/usr/bin/ffmpeg')." -y -i $pathSource -s $wxh $pad -vcodec libtheora -b ".$bitrate."k -acodec libvorbis $pathDest 2>&1";
                                                exec($log->input, $log->output);
                                        }

                                        if (file_exists($pathDest) && filesize($pathDest) > 0)
                                        {
                                                $log->status = 2;
                                        }
                                        else if (file_exists($pathDest) && filesize($pathDest) == 0)
                                        {
                                                jimport( 'joomla.filesystem.file' );
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
                                        // Add watermark
                                        hwdMediaShareVideos::processWatermark($process, $fileType);

                                        // Add file to database
                                        hwdMediaShareFactory::load('files');
                                        hwdMediaShareFiles::add($item,$fileType);
                                        return $log;
                                }

                                $log->output = JText::_('COM_HWDMS_ERROR_DESTINATION_MEDIA_NOT_EXIST');
                        }
                        else
                        {
                                $log->output = JText::_('COM_HWDMS_ERROR_ORIGINAL_SMALLER_THAN_DEST');
                                $log->status = 4;

                                // Add process log
                                hwdMediaShareProcesses::addLog($log);
                                return $log;
                        }
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
	public function injectMetaData($process)
	{
                // Create a new query object.
                $db = JFactory::getDBO();

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
                $hwdmsFiles = hwdMediaShareFiles::getInstance();
                $files = $hwdmsFiles->getMediaFiles($item);

                if (count($files) == 0)
                {
                        // No source files exist
                        $log->output = JText::_('COM_HWDMS_ERROR_NO_SOURCE_MEDIA_EXISTS');

                        // Add process log
                        hwdMediaShareProcesses::addLog($log);
                        return $log;
                }
                
                foreach($files as $file)
                {
                        if (in_array($file->file_type, array(11,12,13)))
                        {
                                $foldersSource = hwdMediaShareFiles::getFolders($item->key);
                                $filenameSource = hwdMediaShareFiles::getFilename($item->key, $file->file_type);
                                $extSource = hwdMediaShareFiles::getExtension($item, $file->file_type);

                                $pathSource = hwdMediaShareFiles::getPath($foldersSource, $filenameSource, $extSource);

                                if (file_exists($pathSource))
                                {
                                        switch ($config->get('metadata_injector'))
                                        {
                                            case 1:
                                                // Flvmdi
                                                if(substr(PHP_OS, 0, 3) == "WIN")
                                                {
                                                        $log->input = "\"".$config->get('path_flvmdi', '/usr/bin/flvmdi')."\" $pathSource 2>&1";
                                                }
                                                else
                                                {
                                                        $log->input = $config->get('path_flvmdi', '/usr/bin/flvmdi')." $pathSource 2>&1";
                                                }
                                                break;
                                            case 2:
                                                // Flvtool2
                                                if(substr(PHP_OS, 0, 3) == "WIN")
                                                {
                                                        $log->input = "\"".$config->get('path_flvtool2', '/usr/bin/flvtool2')."\" -U $pathSource 2>&1";
                                                }
                                                else
                                                {
                                                        $log->input = $config->get('path_flvtool2', '/usr/bin/flvtool2')." -U $pathSource 2>&1";
                                                }
                                                break;
                                            default:
                                                // Yamdi
                                                if(substr(PHP_OS, 0, 3) == "WIN")
                                                {
                                                        $log->input = "\"".$config->get('path_yamdi', '/usr/bin/yamdi')."\" -i $pathSource -s -k -w -o tempfile 2>&1";
                                                }
                                                else
                                                {
                                                        $log->input = $config->get('path_yamdi', '/usr/bin/yamdi')." -i $pathSource -s -k -w -o tempfile 2>&1";
                                                }
                                                break;
                                        }
                                        exec($log->input, $log->output);
                                        
                                        // Check output
                                        if (empty($log->output)) 
                                        {
                                                $log->status = 2;
                                                // Add process log
                                                hwdMediaShareProcesses::addLog($log);
                                                return $log;
                                        }
                                }
                                else
                                {
                                        $log->output = JText::_('COM_HWDMS_ERROR_SOURCE_MEDIA_NOT_EXIST');
                                }

                                // Add process log
                                hwdMediaShareProcesses::addLog($log);
                        }
                }

		return $log;
	}

        /**
	 * Method to generate an image
         *
	 * @since   0.1
	 **/
	public function checkMoovAtoms($process)
	{
                // Create a new query object.
                $db = JFactory::getDBO();

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
                $hwdmsFiles = hwdMediaShareFiles::getInstance();
                $files = $hwdmsFiles->getMediaFiles($item);
            
                if (count($files) == 0)
                {
                        // No source files exist
                        $log->output = JText::_('COM_HWDMS_ERROR_NO_SOURCE_MEDIA_EXISTS');

                        // Add process log
                        hwdMediaShareProcesses::addLog($log);
                        return $log;
                }
                
                foreach($files as $file)
                {
                        if (in_array($file->file_type, array(14,15,16,17)))
                        {
                                $foldersSource = hwdMediaShareFiles::getFolders($item->key);
                                $filenameSource = hwdMediaShareFiles::getFilename($item->key, $file->file_type);
                                $extSource = hwdMediaShareFiles::getExtension($item, $file->file_type);

                                $pathSource = hwdMediaShareFiles::getPath($foldersSource, $filenameSource, $extSource);
                                $pathDest = $pathSource.'.tmp';

                                if (file_exists($pathSource))
                                {
                                        if(substr(PHP_OS, 0, 3) == "WIN")
                                        {
                                                $log->input = "\"".$config->get('path_qt_faststart', '/usr/bin/qt-faststart')."\" $pathSource $pathDest 2>&1";
                                        }
                                        else
                                        {
                                                $log->input = $config->get('path_qt_faststart', '/usr/bin/qt-faststart')." $pathSource $pathDest 2>&1";
                                        }
                                        exec($log->input, $log->output);
                                }
                                else
                                {
                                        $log->output = JText::_('COM_HWDMS_ERROR_SOURCE_MEDIA_NOT_EXIST');
                                }

                                // Check output
                                $stringOutput = is_array($log->output) ? implode("\n",$log->output) : $item->output;
                                if (!empty($stringOutput)) 
                                {
                                        $pos = strpos($stringOutput, "last atom in file was not a moov atom");
                                        if ($pos !== false)
                                        {
                                                $log->status = 4;
                                                // Add process log
                                                hwdMediaShareProcesses::addLog($log);
                                                return $log;
                                        }
                                        
                                        $pos = strpos($stringOutput, "Permission denied");
                                        if ($pos !== false)
                                        {
                                                $log->status = 3;
                                                // Add process log
                                                hwdMediaShareProcesses::addLog($log);
                                                return $log;
                                        }
                                }

                                // Add process log
                                hwdMediaShareProcesses::addLog($log);

                                // @TODO: error reporting
                                if (file_exists($pathDest))
                                {
                                        jimport( 'joomla.filesystem.file' );
                                        
                                        // Remove original MP4 file
                                        JFile::delete($pathSource);
                                        
                                        // Copy temp file
                                        if (JFile::copy($pathDest, $pathSource))
                                        {
                                                $log->status = 2;
                                        }

                                        // Remove temp file
                                        JFile::delete($pathDest);
                                }

                                // Add process log
                                hwdMediaShareProcesses::addLog($log);
                        }
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
	public function getDuration($process)
	{
                // Create a new query object.
                $db = JFactory::getDBO();

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
                        // Get information on original
                        if(substr(PHP_OS, 0, 3) == "WIN")
                        {
                                $command = "\"".$config->get('path_ffmpeg', '/usr/bin/ffmpeg')."\" -i $pathSource 2>&1";
                                exec($command, $output);
                        }
                        else
                        {
                                $command = $config->get('path_ffmpeg', '/usr/bin/ffmpeg')." -i $pathSource 2>&1";
                                exec($command, $output);
                        }

                        $flatoutput = is_array($output) ? implode("\n",$output) : $output;
                        if (empty($flatoutput))
                        {
                                $log->status = 3;
                                $log->input = $command;
                                $log->output = $flatoutput;

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
                                        $log->input = $command;
                                        $log->output = $flatoutput;

                                        // Add process log
                                        hwdMediaShareProcesses::addLog($log);
                                        return $log;
                                }
                                
                                $pos = strpos($flatoutput, "not found");
                                if ($pos !== false)
                                {
                                        $log->status = 3;
                                        $log->input = $command;
                                        $log->output = $flatoutput;
                                
                                        // Add process log
                                        hwdMediaShareProcesses::addLog($log);
                                        return $log;
                                }
                             
                                $pos = strpos($flatoutput, "Permission denied");
                                if ($pos !== false)
                                {
                                        $log->status = 3;
                                        $log->input = $command;
                                        $log->output = $flatoutput;
                                
                                        // Add process log
                                        hwdMediaShareProcesses::addLog($log);
                                        return $log;
                                }

                                // Assume successful
                                $log->status = 2;
                                $log->input = $command;
                                $log->output = $flatoutput;

                                // Add process log
                                hwdMediaShareProcesses::addLog($log); 
                                
                                // Reset status
                                $log->status = 3;                                
                        }
                        
                        preg_match('/Duration: (.*?),/', implode("\n",$output), $matches);
                        $duration_string = $matches[1];

                        list($hr,$m,$s) = explode(':', $duration_string);
                        $duration = ( (int)$hr*3600 ) + ( (int)$m*60 ) + (int)$s;
                        $duration = (int) $duration;

                        if ($duration > 0)
                        {
                                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                                $row =& JTable::getInstance('Media', 'hwdMediaShareTable');

                                // Create an object to bind to the database
                                $data = array();
                                $data['id'] = $item->id;
                                $data['duration'] = $duration;

                                if (!$row->bind($data))
                                {
                                        $log->output = $row->getError();
                                        $log->status = 4;

                                        // Add process log
                                        hwdMediaShareProcesses::addLog($log);
                                        return $log;
                                }

                                if (!$row->store())
                                {
                                        $log->output = $row->getError();
                                        $log->status = 4;

                                        // Add process log
                                        hwdMediaShareProcesses::addLog($log);
                                        return $log;
                                }

                                $log->status = 2;
                        }
                        else
                        {
                                $log->output = JText::_('COM_HWDMS_ERROR_COULD_NOT_FIND_DURATION');
                                $log->status = 4;

                                // Add process log
                                hwdMediaShareProcesses::addLog($log);
                                return $log;
                        }
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
	 * Method to get title of video using Ffmpeg
         *
	 * @since   0.1
	 **/
	public function getTitle($process)
	{
                // Create a new query object.
                $db = JFactory::getDBO();

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

                        // Load data
                        jimport( 'joomla.filesystem.file');
                        $ini	= JPATH_CACHE.'/metadata'.$item->id.'.ini';
                        if (!file_exists($ini)) return $log;
                        $data	= JFile::read($ini);

                        $registry = new JRegistry;
			$registry->loadString($data);
			$meta = $registry->toArray();

                        $_POST['title'] = $meta['title'];
                        $title = JRequest::getVar($title);

                        if (!empty($title))
                        {
                                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                                $row =& JTable::getInstance('Media', 'hwdMediaShareTable');

                                // Create an object to bind to the database
                                $data = array();
                                $data['id'] = $item->id;
                                $data['title'] = $title;

                                if (!$row->bind($data))
                                {
                                        $log->output = $row->getError();
                                        $log->status = 4;

                                        // Add process log
                                        hwdMediaShareProcesses::addLog($log);
                                        return $log;
                                }

                                if (!$row->store())
                                {
                                        $log->output = $row->getError();
                                        $log->status = 4;

                                        // Add process log
                                        hwdMediaShareProcesses::addLog($log);
                                        return $log;
                                }

                                $log->status = 2;
                        }
                        else
                        {
                                $log->output = JText::_('COM_HWDMS_ERROR_COULD_NOT_FIND_TITLE');
                                $log->status = 4;

                                // Add process log
                                hwdMediaShareProcesses::addLog($log);
                                return $log;
                        }
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

        /**
	 * Method to generate an image
         *
	 * @since   0.1
	 **/
	public function processWatermark($process, $fileType)
	{
                // Create a new query object.
                $db = JFactory::getDBO();

                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

		if ($config->get('process_watermark') == 0 || $config->get('watermark_path') == '') return;

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
                $filenameSource = hwdMediaShareFiles::getFilename($item->key, $fileType);
                $extSource = hwdMediaShareFiles::getExtension($item, $fileType);

                $pathSource = hwdMediaShareFiles::getPath($foldersSource, $filenameSource, $extSource);

                if (file_exists($pathSource))
                {
                        $foldersDest = hwdMediaShareFiles::getFolders($item->key);
                        $filenameDest = hwdMediaShareFiles::getFilename($item->key, 26);
                        $extDest = hwdMediaShareFiles::getExtension($item, 26);

                        $pathDest = hwdMediaShareFiles::getPath($foldersDest, $filenameDest, $extDest);

                        $logo = JPATH_SITE.'/'.$config->get('watermark_path');

                        switch ($fileType)
                        {
                            case 11:
                            case 12:
                            case 13:
                                $vcodec = 'libx264';
                                $format = 'flv';
                                break;
                            case 14:
                            case 15:
                            case 16:
                            case 17:
                                $vcodec = 'libx264';
                                $format = 'mp4';
                                break;
                            case 18:
                            case 19:
                            case 20:
                            case 21:
                                $vcodec = 'libvpx';
                                $format = 'webm';
                                break;
                            case 22:
                            case 23:
                            case 24:
                            case 25:
                                $vcodec = 'libtheora';
                                $format = 'ogg';
                                break;
                            default:
                                return false;
                        }

                        switch ($config->get('watermark_position'))
                        {
                            case 1:
                                // Top left
                                $overlay = '10:10';
                                break;
                            case 2:
                                // Top right
                                $overlay = 'W-w-10:10';
                                break;
                            case 4:
                                // Bottom left
                                $overlay = '10:H-h-10';
                                break;
                            default:
                                // Bottom right
                                $overlay = 'W-w-10:H-h-10';
                                break;
                        }

                        try
                        {
                                if(substr(PHP_OS, 0, 3) == "WIN")
                                {
                                        $logo = preg_replace('|^([a-z]{1}):|i', '', $logo); //Strip out windows drive letter if it's there.
                                        $logo = str_replace('\\', '/', $logo); //Windows path sanitisation
                                        //$log->input = "\"".$config->get('path_ffmpeg', '/usr/bin/ffmpeg')."\" -y -b 700k -qscale 0 -b 1500k -i $pathSource -vf \"movie=".$logo." [logo];[in][logo] overlay=".$overlay." [out]\" -vcodec $vcodec -acodec copy -f $format $pathDest 2>&1";
                                        $log->input = "\"".$config->get('path_ffmpeg', '/usr/bin/ffmpeg')."\" -y -i $pathSource -vf \"movie=".$logo." [logo];[in][logo] overlay=".$overlay." [out]\" -vcodec $vcodec -acodec copy -f $format $pathDest 2>&1";
                                        exec($log->input, $log->output);
                                }
                                else
                                {
                                        $log->input = $config->get('path_ffmpeg', '/usr/bin/ffmpeg')." -y -i $pathSource -vf \"movie=".$logo." [logo];[in][logo] overlay=".$overlay." [out]\" -vcodec $vcodec -acodec copy -f $format $pathDest 2>&1";
                                        exec($log->input, $log->output);
                                }

                                jimport( 'joomla.filesystem.file' );
                                if (file_exists($pathDest) && filesize($pathDest) > 0)
                                {
                                        if (JFile::copy($pathDest, $pathSource))
                                        {
                                                JFile::delete($pathDest);
                                        }
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
                }

		return true;
	}
        
        /**
	 * Method to render an image
         *
	 * @since   0.1
	 **/
	public function getVideoBitrate($size)
	{
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                switch ($size)
                {
                    case 240:
                        return $config->get('process_max_vbitrate_240');
                        break;
                    case 360:
                        return $config->get('process_max_vbitrate_360');
                        break;
                    case 480:
                        return $config->get('process_max_vbitrate_480');
                        break;   
                    case 720:
                        return $config->get('process_max_vbitrate_720');
                        break; 
                    case 1080:
                        return $config->get('process_max_vbitrate_1080');
                        break; 
                }
	}
        
        /**
	 * Method to render an image
         *
	 * @since   0.1
	 **/
	public function getAudioBitrate($size)
	{
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                switch ($size)
                {
                    case 240:
                        return $config->get('process_max_abitrate_240');
                        break;
                    case 360:
                        return $config->get('process_max_abitrate_360');
                        break;
                    case 480:
                        return $config->get('process_max_abitrate_480');
                        break;   
                    case 720:
                        return $config->get('process_max_abitrate_720');
                        break; 
                    case 1080:
                        return $config->get('process_max_abitrate_1080');
                        break; 
                }
	}
}
