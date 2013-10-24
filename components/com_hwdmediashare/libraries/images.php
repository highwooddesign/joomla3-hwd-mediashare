<?php
/**
 * @version    SVN $Id: images.php 1592 2013-06-14 13:32:49Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      06-Dec-2011 15:26:06
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * hwdMediaShare framework images class
 *
 * @package hwdMediaShare
 * @since   0.1
 */
abstract class hwdMediaShareImages
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
	 * Returns the hwdMediaShareImages object, only creating it if it
	 * doesn't already exist.
	 *
	 * @return  hwdMediaShareImages A hwdMediaShareImages object.
	 * @since   0.1
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
	 * Method to render an image
         * 
	 * @since   0.1
	 **/
	public function get($item)
	{
                hwdMediaShareFactory::load('files');
                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('utilities');
                $folders = hwdMediaShareFiles::getFolders($item->key);
                $filename = hwdMediaShareFiles::getFilename($item->key, 5);
                $ext = hwdMediaShareFiles::getExtension($item, 5);
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                $utilities = hwdMediaShareUtilities::getInstance();

                if ($config->get('mediaitem_size') <= 100)
                {
                        $fileType = 3;
                }
                elseif ($config->get('mediaitem_size') <= 240)
                {
                        $fileType = 4;
                }
                elseif ($config->get('mediaitem_size') <= 500)
                {
                        $fileType = 5;
                }
                elseif ($config->get('mediaitem_size') <= 640)
                {
                        $fileType = 6;
                }
                else
                {
                        $fileType = 7;
                }

                ob_start(); ?>
                <!--<a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=slideshow&id=' . $item->id . '&format=raw'); ?>" class="pagenav-zoom modal" rel="{handler: 'iframe', size: {x: 840, y: 580}}">-->
                <img src="<?php echo hwdMediaShareDownloads::url($item,$fileType); ?>" border="0" alt="<?php echo $utilities->escape($item->title); ?>" id="media-item-image" style="max-width:<?php echo ($config->get('mediaitem_width') ? $config->get('mediaitem_width') : $config->get('mediaitem_size')); ?>px;max-height:<?php echo ($config->get('mediaitem_height') ? $config->get('mediaitem_height').'px' : 'auto'); ?>;">
                <!--</a>-->
                <?php
                $return = ob_get_contents();
                ob_end_clean();
                return $return;
	}
        
        /**
	 * Method to check is an image can be displayed natively in browsers
         * 
	 * @since   0.1
	 **/
	public function isNativeImage($ext)
	{
                $native = array("jpg", "jpeg", "png", "gif");

                if(in_array(strtolower($ext),$native))
                {
                        return true;
                }

                return false;
	}
        
	/**
	 * Method to render a video
         *
         * @since   0.1
	 **/
	public function getJpg($item)
	{
                // Load hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                $size = $config->get('mediaitem_size');

                if ($size <= 100)
                {
                        $fileType  = 3;
                        $fileTypes = array(3,4,5,6,7);
                }
                else if ($size <= 240)
                {
                        $fileType  = 4;
                        $fileTypes = array(4,5,6,7,3);
                }
                else if ($size <= 500)
                {
                        $fileType  = 5;
                        $fileTypes = array(5,6,7,4,3);
                }
                else if ($size <= 640)
                {
                        $fileType  = 6;
                        $fileTypes = array(6,7,5,4,3);
                }
                else
                {
                        $fileType  = 7;
                        $fileTypes = array(7,6,5,4,3);
                }
                
                // If CDN just let the CDN framework choose the image
                if ($item->type == 5) return hwdMediaShareDownloads::url($item, $fileType);
                
                // If local, loop desired types and select first one which exists
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

                // If no standard images exist then check for a custom thumbnail to use
                if ($custom = hwdMediaShareFactory::getElementThumbnail($item))
                {
                        return $custom;
                }

                return false;
	}
        
        /**
	 * Method to generate an image
         * 
	 * @since   0.1
	 **/
	public function processImage($process, $fileType, $size, $crop=false)
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
	 * Method to render an image
         * 
	 * @since   0.1
	 **/
	public function getMeta($item)
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
}
