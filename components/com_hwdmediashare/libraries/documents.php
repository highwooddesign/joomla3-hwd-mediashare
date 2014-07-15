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

class hwdMediaShareDocuments extends JObject
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
	 * Returns the hwdMediaShareDocuments object, only creating it if it
	 * doesn't already exist.
	 *
	 * @access  public
         * @static
	 * @return  hwdMediaShareDocuments A hwdMediaShareDocuments object.
	 */
	public static function getInstance()
	{
		static $instance;

		if (!isset ($instance))
                {
			$c = 'hwdMediaShareDocuments';
                        $instance = new $c;
		}

		return $instance;
	}
        
	/**
	 * Method to display a document.
         * 
         * @access  public
         * @static
         * @param   object  $item   The media item.
         * @return  string  The html to display the document.
	 */
	public static function display($item)
	{
                // Initialise variables.            
                $app = JFactory::getApplication();
                
                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // Get HWD utilities.
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                
                // Load some libraries.
                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('utilities');
                hwdMediaShareFactory::load('files');

                $width = $config->get('mediaitem_size', '500');
                $height = (int) $config->get('mediaitem_height') ? $config->get('mediaitem_height') : $width*$config->get('video_aspect',0.75);
                $autoplayNumerical = ($app->input->get('media_autoplay', $config->get('media_autoplay'), 'integer') == 1 ? '1' : '0');
                $autoplayBoolean = ($app->input->get('media_autoplay', $config->get('media_autoplay'), 'integer') == 1 ? 'true' : 'false');
                
                // Process a linked file.
                if($item->type == 7)
                {
                        // Get extension of linked file.
                        $item->ext = strtolower(JFile::getExt($item->source));

                        // Check this extension is what we expect.
                        switch ($item->media_type)
                        {
                            case 1: // Audio
                                if (!in_array($item->ext, array('mp3'))) $item->ext = 'mp3';
                                break;
                            case 2: // Document
                                break;
                            case 3: // Image
                                if (!in_array($item->ext, array('jpeg','jpg','png','gif'))) $item->ext = 'jpg';
                                break;
                            case 4: // Video
                                if (!in_array($item->ext, array('flv','mp4'))) $item->ext = 'flv';
                                break;
                        }

                        // Define the URL to the media file
                        $url = $item->source;
                } 
                else 
                {
                        if(!isset($item->ext))
                        {
                                $item->ext = hwdMediaShareFiles::getExtension($item, 1);
                        }
                        $url = hwdMediaShareDownloads::url($item, 1);
                }

                // Check for cloudfront services.
                if (strpos($url, '.cloudfront.net') !== false) 
                {
                        hwdMediaShareFactory::load('aws.cloudfront');
                        $player = call_user_func(array('hwdMediaShareCloudfront', 'getInstance'));
                        $item->file = basename($url);
                        $signed = urldecode($player->update_stream_name($item));
                        $url = str_replace($item->file, $signed, $url);
                }
                
                switch ($item->ext) 
                {
                    case 'pdf':
                        ob_start();
                        ?>
                        <embed width="100%" height="450" src="<?php echo $url; ?>" type="application/pdf"></embed>	
                        <?php
                        $html = ob_get_contents();
                        ob_end_clean();
                        return $html;
                        break;
                    
                    case 'mpg':
                    case 'mpeg':
                    case 'avi':
                    case 'wmv':
                        ob_start();
                        ?>
                        <object id="player" width="<?php echo $width; ?>" height="<?php echo $height; ?>" classid="CLSID:22D6F312-B0F6-11D0-94AB-0080C74C7E95" type="application/x-oleobject">
                        <param name="filename" value="<?php echo $url ?>">
                        <param name="autostart" value="<?php echo $autoplayBoolean; ?>">
                        <param name="showcontrols" value="true">
                        <param name="showstatusbar" value="false">
                        <param name="showdisplay" value="false">
                        <embed type="application/x-mplayer2" pluginspage="http://www.microsoft.com/Windows/MediaPlayer/" src="<?php echo $url ?>" name="player" width="<?php echo $width; ?>" height="<?php echo $height; ?>" showcontrols="1" showstatusbar="0" showdisplay="0" autostart="<?php echo $autoplayNumerical; ?>"> </embed>
                        </object>
                        <?php
                        $html = ob_get_contents();
                        ob_end_clean();
                        return $html;
                        break;

                    case 'wma':
                        ob_start();
                        ?>
                        <object id="player" width="<?php echo $width; ?>" height="46" classid="CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95" codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab# Version=5,1,52,701" standby="Loading Microsoft Windows Media Player..." type="application/x-oleobject">
                        <param name="fileName" value="<?php echo $url ?>">
                        <param name="animationatStart" value="true">
                        <param name="transparentatStart" value="true">
                        <param name="autoStart" value="<?php echo $autoplayBoolean; ?>">
                        <param name="showControls" value="true">
                        <param name="Volume" value="-300">
                        <embed type="application/x-mplayer2" pluginspage="http://www.microsoft.com/Windows/MediaPlayer/" src="<?php echo $url ?>" name="player" width="<?php echo $width; ?>" height="46" autostart=<?php echo $autoplayNumerical; ?> showcontrols=1 volume=-300> </embed>
                        </object>
                        <?php
                        $html = ob_get_contents();
                        ob_end_clean();
                        return $html;
                        break;
                    
                    case 'divx':  
                        ob_start();
                        ?>
                        <object classid="clsid:67DABFBF-D0AB-41fa-9C46-CC0F21721616" width="<?php echo $width; ?>" height="<?php echo $height; ?>" codebase="http://go.divx.com/plugin/DivXBrowserPlugin.cab">
                        <param name="custommode" value="none" />
                        <param name="autoPlay" value="<?php echo $autoplayBoolean; ?>" />
                        <param name="src" value="<?php echo $url; ?>" />
                        <embed type="video/divx" src="<?php echo $url; ?>" custommode="none" width="<?php echo $width; ?>" height="<?php echo $height; ?>" autoPlay="<?php echo $autoplayBoolean; ?>"  pluginspage="http://go.divx.com/plugin/download/">
                        </embed>
                        </object>
                        <?php
                        $html = ob_get_contents();
                        ob_end_clean();
                        return $html;
                        break;

                    case 'mov':                    
                        ob_start();
                        ?>
                        <object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" width="<?php echo $width; ?>" height="<?php echo $height; ?>" codebase="http://www.apple.com/qtactivex/qtplugin.cab">
                        <param name="src" value="<?php echo $url; ?>">
                        <param name="autoplay" value="<?php echo $autoplayBoolean; ?>">
                        <param name="scale" value="aspect">
                        <embed src="<?php echo $url; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" autoplay="<?php echo $autoplayBoolean; ?>" scale="aspect"
                        bgcolor="#000000" pluginspage="http://www.apple.com/quicktime/download/">
                        </embed>
                        </object>
                        <?php
                        $html = ob_get_contents();
                        ob_end_clean();
                        return $html;
                        break;
                    
                    case 'flv':                    
                    case 'f4v':                    
                        $pluginClass = 'plgHwdmediashare'.$config->get('media_player');
                        $pluginPath = JPATH_ROOT.'/plugins/hwdmediashare/'.$config->get('media_player').'/'.$config->get('media_player').'.php';

                        // Import hwdMediaShare plugins
                        if (file_exists($pluginPath))
                        {
				$jpg = hwdMediaShareDownloads::jpgUrl($item);
                                JLoader::register($pluginClass, $pluginPath);
                                $player = call_user_func(array($pluginClass, 'getInstance'));
                                $params = new JRegistry('{"flv":"'.$url.'","jpg":"'.$jpg.'"}');
                                return $player->getVideoPlayer($params);
                        }
                        break;
                        
                    case 'mp4':                    
                    case 'm4v':                    
                        $pluginClass = 'plgHwdmediashare'.$config->get('media_player');
                        $pluginPath = JPATH_ROOT.'/plugins/hwdmediashare/'.$config->get('media_player').'/'.$config->get('media_player').'.php';

                        // Import hwdMediaShare plugins
                        if (file_exists($pluginPath))
                        {
 				$jpg = hwdMediaShareDownloads::jpgUrl($item);
                                JLoader::register($pluginClass, $pluginPath);
                                $player = call_user_func(array($pluginClass, 'getInstance'));
                                $params = new JRegistry('{"mp4":"'.$url.'","jpg":"'.$jpg.'"}');
                                return $player->getVideoPlayer($params);
                        }
                        break;
                        
                    case 'mp3':                    
                        $pluginClass = 'plgHwdmediashare'.$config->get('media_player');
                        $pluginPath = JPATH_ROOT.'/plugins/hwdmediashare/'.$config->get('media_player').'/'.$config->get('media_player').'.php';

                        // Import hwdMediaShare plugins
                        if (file_exists($pluginPath))
                        {
 				$jpg = hwdMediaShareDownloads::jpgUrl($item);
                                JLoader::register($pluginClass, $pluginPath);
                                $player = call_user_func(array($pluginClass, 'getInstance'));
                                $params = new JRegistry('{"mp3":"'.$url.'","jpg":"'.$jpg.'"}');
                                return $player->getAudioPlayer($params);
                        }
                        break; 
                        
                    case 'doc':                    
                    case 'docx':                    
                    case 'ppt':  
                    case 'pptx':  
                    case 'pub':  
                    case 'xls':  
                    case 'xlsx':
                        hwdMediaShareFactory::load('utilities');
                        $utilities = hwdMediaShareUtilities::getInstance();
                        ob_start();
                        ?>
                        <iframe src="http://docs.google.com/gview?url=<?php echo urlencode($utilities->relToAbs($url)); ?>&embedded=true" style="width:100%; height:450px;" frameborder="0"></iframe>
                        <?php
                        $html = ob_get_contents();
                        ob_end_clean();
                        return $html;
                        break;
                        
                    case 'jpeg':
                    case 'jpg':
                    case 'gif':
                    case 'png':
                        ob_start();
                        ?>
                        <img src="<?php echo $url; ?>" border="0" alt="<?php echo $utilities->escape($item->title); ?>" id="media-item-image" style="max-width:<?php echo ($config->get('mediaitem_width') ? $config->get('mediaitem_width') : $config->get('mediaitem_size')); ?>px;max-height:<?php echo ($config->get('mediaitem_height') ? $config->get('mediaitem_height').'px' : '100%'); ?>;">
                        <?php
                        $html = ob_get_contents();
                        ob_end_clean();
                        return $html;
                        break;
                    
                    //@TODO: Works need to be done on the sizing of the SWF display, and parameter values. 
                    case 'swf':
                        ob_start();
                        ?>                     
                        <object id="player" width="<?php echo $width; ?>" height="<?php echo $height; ?>" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0">
                        <param name="filename" value="<?php echo $url ?>">
                        <param name="autostart" value="<?php echo $autoplayBoolean; ?>">
                        <param name="showcontrols" value="true">
                        <param name="showstatusbar" value="false">
                        <param name="showdisplay" value="false">
                        <embed type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" src="<?php echo $url ?>" name="player" width="<?php echo $width; ?>" height="<?php echo $height; ?>" showcontrols="1" showstatusbar="0" showdisplay="0" autostart="<?php echo $autoplayNumerical; ?>"> </embed>
                        </object>
                        <?php
                        $html = ob_get_contents();
                        ob_end_clean();
                        return $html;
                        break;
                    
                    default:
                       return '<img src="'.hwdMediaShareDownloads::thumbnail($item).'" border="0" alt="'.$utilities->escape($item->title).'" id="media-item-image">';
                       break;
                }
	}
}
