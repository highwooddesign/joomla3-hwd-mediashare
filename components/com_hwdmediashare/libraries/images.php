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

class hwdMediaShareImages extends JObject
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
	 * Returns the hwdMediaShareImages object, only creating it if it
	 * doesn't already exist.
	 *
	 * @access  public
         * @static
	 * @return  hwdMediaShareImages Object.
	 */ 
	public static function getInstance()
	{
		static $instance;

		if (!isset ($instance))
                {
			$c = 'hwdMediaShareImages';
                        $instance = new $c;
		}

		return $instance;
	}
        
	/**
	 * Method to display an image.
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
                
                // Load HWD utilities.
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                
                if ($image = hwdMediaShareImages::getJpg($item))
                {
                        ob_start(); ?>
                        <img src="<?php echo $image->url; ?>" border="0" alt="<?php echo $utilities->escape($item->title); ?>" id="media-item-image" style="width:100%;max-width:<?php echo ($config->get('mediaitem_width') ? $config->get('mediaitem_width') : $config->get('mediaitem_size')); ?>px;max-height:<?php echo ($config->get('mediaitem_height') ? $config->get('mediaitem_height').'px' : 'auto'); ?>;">
                        <?php
                        $return = ob_get_contents();
                        ob_end_clean();
                        
                        return $return;
                }

                return $utilities->printNotice(JText::_('COM_HWDMS_MSG_FAILED_LOAD_IMAGE'));               
	}
        
	/**
	 * Method to check if a jpg file has been generated and return file data.
         * 
         * @access  public
         * @static
         * @param   object  $item   The media item.
         * @return  mixed   The file object, false on fail.
	 */
	public static function getJpg($item)
	{
                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                // Get maximum media size.
                $size = $config->get('mediaitem_size');

                // Select most appropriate file based on the maximum size.
                if ($size <= 100)
                {
                        $fileType  = 3;
                        $fileTypes = array(3,4,5,6,7);
                }
                elseif ($size <= 240)
                {
                        $fileType  = 4;
                        $fileTypes = array(4,5,6,7,3);
                }
                elseif ($size <= 500)
                {
                        $fileType  = 5;
                        $fileTypes = array(5,6,7,4,3);
                }
                elseif ($size <= 640)
                {
                        $fileType  = 6;
                        $fileTypes = array(6,7,5,4,3);
                }
                else
                {
                        $fileType  = 7;
                        $fileTypes = array(7,6,5,4,3);
                }
                
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

                // Loop through local files and select the first one which exists.
                foreach ($fileTypes as $fileType)
                {
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
                                $file->type = 'image/jpeg';

                                return $file;
                        }
                }

                // Check original for a native image format.
                $folders = hwdMediaShareFiles::getFolders($item->key);
                $filename = hwdMediaShareFiles::getFilename($item->key, 1);
                $ext = hwdMediaShareFiles::getExtension($item, 1);
                $path = hwdMediaShareFiles::getPath($folders, $filename, $ext);
                if (file_exists($path) && hwdMediaShareImages::isNativeImage($ext))
                {
                        // Create file object.
                        $file = new JObject;
                        $file->local = true;
                        $file->path = $path;
                        $file->url = hwdMediaShareDownloads::url($item, 1);
                        $file->size = filesize($path);
                        $file->ext = $ext;
                        $file->type = hwdMediaShareDocuments::getContentType($ext);
                        
                        return $file;
                }

                return false;
	}

	/**
	 * Method to check is an image can be displayed natively in browsers
         * 
         * @access  public
         * @static
         * @param   string  $ext    The ext of the image.
         * @return  boolean True if native, false if not.
	 */
	public static function isNativeImage($ext)
	{
                $native = array('jpg', 'jpeg', 'png', 'gif');

                if (in_array(strtolower($ext),$native))
                {
                        return true;
                }

                return false;
	}
        
        /**
	 * Method to process a media to generate an image.
         * 
         * @access  public
         * @param   object  $process    The process item.
         * @param   integer $fileType   The API value for the type of file being generated, used
         *                              in generation of filename. 
         * @param   integer $size       The size of the image.
         * @param   integer $crop       The flag to crop the image during processing.
         * @return  object  The log data.
	 */
	public function processImage($process, $fileType, $size, $crop = false)
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
                        list($width, $height) = getimagesize($pathSource); 

                        if (max($width, $height) < $size)
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

                                // Try ImageMagick PHP extension
                                try
                                {
                                        $log->input = "Imagemagick::cropThumbnailImage()\n";

                                        // Let's check whether we can perform the magick.
                                        if (TRUE !== extension_loaded('imagick'))
                                        {
                                            throw new Exception(JText::_('COM_HWDMS_ERROR_IMAGICK_EXTENSION_NOT_LOADED'));
                                        }

                                        // This check is an alternative to the previous one.
                                        // Use the one that suits you better.
                                        if (TRUE !== class_exists('Imagick'))
                                        {
                                            throw new Exception(JText::_('COM_HWDMS_ERROR_IMAGICK_CLASS_NOT_EXIST'));
                                        } 

                                        // New imagick object
                                        $im = new imagick( $pathSource );

                                        // Convert to jpg
                                        $im->setImageColorspace(imagick::COLORSPACE_RGB);
                                        $im->setCompression(Imagick::COMPRESSION_JPEG);
                                        $im->setCompressionQuality(60);
                                        $im->setImageFormat('jpeg');

                                        // Resize
                                        if ($crop)
                                        {
                                                $im->cropThumbnailImage($size, $size);
                                        }
                                        else
                                        {
                                                $im->resizeImage($size, $size, imagick::FILTER_LANCZOS, 1, true);
                                        }

                                        // Write image on server
                                        $im->writeImage($pathDest);

                                        $im->clear();
                                        $im->destroy();

                                        if (file_exists($pathDest) && filesize($pathDest) > 0 && (filemtime($pathDest) > time()-60*5))
                                        {
                                                $log->status = 2;
                                        }
                                }
                                catch(Exception $e)
                                {
                                        $log->output = $e->getMessage();
                                }

                                // Add process log
                                hwdMediaShareProcesses::addLog($log);
                                if (file_exists($pathDest) && filesize($pathDest) > 0 && (filemtime($pathDest) > time()-60*5))
                                {
                                        // Add watermark
                                        hwdMediaShareImages::processWatermark($process, $fileType);
                                                                        
                                        // Add file to database
                                        hwdMediaShareFactory::load('files');
                                        hwdMediaShareFiles::add($item,$fileType);
                                        return $log;
                                }

                                // Try ImageMagick command line
                                try
                                {
                                        // Let's check whether we can perform the magick.
                                        if (TRUE !== is_callable('exec'))
                                        {
                                            throw new Exception(JText::_('COM_HWDMS_ERROR_EXEC_FUNCTION_NOT_CALLABLE'));
                                        }

                                        // This check is an alternative to the previous one.
                                        // Use the one that suits you better.
                                        if (TRUE !== function_exists('exec'))
                                        {
                                            throw new Exception(JText::_('COM_HWDMS_ERROR_EXEC_FUNCTION_NOT_EXISTS'));
                                        } 

                                        $wxh = $size.'x'.$size;
                                        // Resize
                                        if(substr(PHP_OS, 0, 3) == "WIN")
                                        {
                                                if ($crop)
                                                {
                                                        $log->input = "\"".$config->get('path_imagemagick', '/usr/bin/convert')."\" -verbose -thumbnail 75x75^ -gravity center -extent 75x75 $pathSource $pathDest 2>&1";
                                                }
                                                else
                                                {
                                                        $log->input = "\"".$config->get('path_imagemagick', '/usr/bin/convert')."\" -verbose -resize $wxh $pathSource $pathDest 2>&1";
                                                }
                                                exec($log->input, $log->output);
                                        }
                                        else
                                        {
                                                if ($crop)
                                                {
                                                        $log->input = $config->get('path_imagemagick', '/usr/bin/convert')." -verbose -thumbnail 75x75^ -gravity center -extent 75x75 $pathSource $pathDest 2>&1";
                                                }
                                                else
                                                {
                                                        $log->input = $config->get('path_imagemagick', '/usr/bin/convert')." -verbose -resize $wxh $pathSource $pathDest 2>&1";
                                                }
                                                exec($log->input, $log->output);
                                        }
                                        
                                        if (file_exists($pathDest) && filesize($pathDest) > 0 && (filemtime($pathDest) > time()-60*5))
                                        {
                                                $log->status = 2;
                                        }
                                }
                                catch(Exception $e)
                                {
                                        $log->output = $e->getMessage();
                                }       
        
                                // Add process log
                                hwdMediaShareProcesses::addLog($log);
                                if (file_exists($pathDest) && filesize($pathDest) > 0 && (filemtime($pathDest) > time()-60*5))
                                {
                                        // Add watermark
                                        hwdMediaShareImages::processWatermark($process, $fileType);
                                    
                                        // Add file to database
                                        hwdMediaShareFactory::load('files');
                                        hwdMediaShareFiles::add($item,$fileType);
                                        return $log;
                                }
                                
                                // As of IM v6.3.8-3 the special resize option flag '^' was added to make this easier.
				// Before IM v6.3.8-3 when this special flag was added, you would have needed some very complex trickiness to achieve the same result.
				// See Resizing to Fill a Given Space for details: http://www.imagemagick.org/Usage/resize/#space_fill
                                // Try older ImageMagick command line
                                try
                                {
                                        // Let's check whether we can perform the magick.
                                        if (TRUE !== is_callable('exec'))
                                        {
                                            throw new Exception(JText::_('COM_HWDMS_ERROR_EXEC_FUNCTION_NOT_CALLABLE'));
                                        }

                                        // This check is an alternative to the previous one.
                                        // Use the one that suits you better.
                                        if (TRUE !== function_exists('exec'))
                                        {
                                            throw new Exception(JText::_('COM_HWDMS_ERROR_EXEC_FUNCTION_NOT_EXISTS'));
                                        }

                                        $wxh = $size.'x'.$size;
                                        // Resize
                                        if(substr(PHP_OS, 0, 3) == "WIN")
                                        {
                                                if ($crop)
                                                {
                                                        $log->input = "\"".$config->get('path_imagemagick', '/usr/bin/convert')."\" -verbose -thumbnail 75x75 -gravity center -extent 75x75 $pathSource $pathDest 2>&1";
                                                }
                                                else
                                                {
                                                        $log->input = "\"".$config->get('path_imagemagick', '/usr/bin/convert')."\" -verbose -resize $wxh $pathSource $pathDest 2>&1";
                                                }
                                                exec($log->input, $log->output);
                                        }
                                        else
                                        {
                                                if ($crop)
                                                {
                                                        $log->input = $config->get('path_imagemagick', '/usr/bin/convert')." -verbose $pathSource -resize x140 -resize '140x<' -resize 50% -gravity center  -crop 75x75+0+0 +repage $pathDest 2>&1";
                                                }
                                                else
                                                {
                                                        $log->input = $config->get('path_imagemagick', '/usr/bin/convert')." -resize $wxh $pathSource $pathDest 2>&1";
                                                }
                                                exec($log->input, $log->output);
                                        }

                                        if (file_exists($pathDest) && filesize($pathDest) > 0)
                                        {
                                                $log->status = 2;
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
                                        hwdMediaShareImages::processWatermark($process, $fileType);
                                    
                                        // Add file to database
                                        hwdMediaShareFactory::load('files');
                                        hwdMediaShareFiles::add($item,$fileType);
                                        return $log;
                                }
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
	 * Method to process an image and include a watermark.
         * 
         * @access  public
         * @param   object  $process    The process item.
         * @param   integer $fileType   The API value for the type of file being generated, used
         *                              in generation of filename. 
         * @return  object  The log data.
	 */
	public function processWatermark($process, $fileType)
	{
                // Create a new query object.
                $db = JFactory::getDBO();

                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

		if ($config->get('process_watermark') == 0 || $config->get('watermark_path') == '') return;

                // Only process watermark if the image being generated is larger than 500 pixels
                if (!in_array($fileType, array(5,6,7))) return;
                
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

                        switch ($config->get('watermark_position'))
                        {
                            case 1:
                                // Top left
                                $gravity = 'northwest';
                                break;
                            case 2:
                                // Top right
                                $gravity = 'northeast';
                                break;
                            case 4:
                                // Bottom left
                                $gravity = 'southwest';
                                break;
                            default:
                                // Bottom right
                                $gravity = 'southeast';
                                break;
                        }

                        try
                        {
                                if(substr(PHP_OS, 0, 3) == "WIN")
                                {
                                        $logo = preg_replace('|^([a-z]{1}):|i', '', $logo); //Strip out windows drive letter if it's there.
                                        $logo = str_replace('\\', '/', $logo); //Windows path sanitisation
                                        $log->input = "\"".$config->get('path_composite', 'C:\Program Files (x86)\ImageMagick-6.7.9-Q16\composite.exe')."\" -verbose -dissolve 25% -gravity ".$gravity." ".$logo." ".$pathSource." ".$pathDest." 2>&1";
                                        exec($log->input, $log->output);
                                }
                                else
                                {
                                        $log->input = $config->get('path_composite', '/usr/bin/composite')." -verbose -watermark 50% -gravity ".$gravity." ".$logo." ".$pathSource." ".$pathDest." 2>&1";
                                        exec($log->input, $log->output);
                                }

                                jimport( 'joomla.filesystem.file' );
                                if (file_exists($pathDest) && filesize($pathDest) > 0)
                                {
                                        if (JFile::copy($pathDest, $pathSource))
                                        {
                                                // The watermarked image has been copied successfuly to replace the original, so
                                                // delete the temporary file and mark as successull
                                                JFile::delete($pathDest);
                                                $log->status = 2;
                                        }
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

                        // Add process log
                        hwdMediaShareProcesses::addLog($log);
                }

		return true;
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
                hwdMediaShareFactory::load('files');
                hwdMediaShareFactory::load('downloads');                
                $foldersSource = hwdMediaShareFiles::getFolders($item->key);
                $filenameSource = hwdMediaShareFiles::getFilename($item->key, 1);
                $extSource = hwdMediaShareFiles::getExtension($item, 1);

                $pathSource = hwdMediaShareFiles::getPath($foldersSource, $filenameSource, $extSource);

                // Check if the variable is set and if the file itself exists before continuing
                if (file_exists($pathSource) && filesize($pathSource) > 0)
                {
                        // Try ImageMagick PHP extension
                        try
                        {
                                // Let's check whether we can load the exif.
                                if (TRUE !== function_exists('read_exif_data'))
                                {
                                    throw new Exception(JText::_('COM_HWDMS_ERROR_EXIF_FUNCTION_NOT_EXIST'));
                                } 
                                        
                                 // There are 2 arrays which contains the information we are after, so it's easier to state them both
                                $exif_ifd0 = read_exif_data($pathSource ,'IFD0' ,0);      
                                $exif_exif = read_exif_data($pathSource ,'EXIF' ,0);
                        }
                        catch(Exception $e)
                        {
                                //$this->setError($e->getMessage());
                                return false;
                        }   

                        //error control
                        $notFound = JText::_('COM_HWDMS_UNAVAILABLE');

                        // Make
                        if (@array_key_exists('Make', $exif_ifd0)) {
                            $camMake = $exif_ifd0['Make'];
                        } else { $camMake = $notFound; }

                        // Model
                        if (@array_key_exists('Model', $exif_ifd0)) {
                            $camModel = $exif_ifd0['Model'];
                        } else { $camModel = $notFound; }

                        // Exposure
                        if (@array_key_exists('ExposureTime', $exif_ifd0)) {
                            $camExposure = $exif_ifd0['ExposureTime'];
                        } else { $camExposure = $notFound; }

                        // Aperture
                        if (@array_key_exists('ApertureFNumber', $exif_ifd0['COMPUTED'])) {
                            $camAperture = $exif_ifd0['COMPUTED']['ApertureFNumber'];
                        } else { $camAperture = $notFound; }

                        // Date
                        if (@array_key_exists('DateTime', $exif_ifd0)) {
                            $camDate = $exif_ifd0['DateTime'];
                        } else { $camDate = $notFound; }

                        // ISO
                        if (@array_key_exists('ISOSpeedRatings',$exif_exif)) {
                            $camIso = $exif_exif['ISOSpeedRatings'];
                        } else { $camIso = $notFound; }

                        $return = array();
                        $return['make'] = $camMake;
                        $return['model'] = $camModel;
                        $return['exposure'] = $camExposure;
                        $return['aperture'] = $camAperture;
                        $return['date'] = $camDate;
                        $return['iso'] = $camIso;
                        return $return;
                } 

                return false;
	}
}
