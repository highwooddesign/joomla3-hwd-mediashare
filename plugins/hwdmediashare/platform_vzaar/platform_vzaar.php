<?php
/**
 * @version    $Id: platform_vzaar.php 1589 2013-06-14 07:48:16Z dhorsfall $
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
class plgHwdmediasharePlatform_vzaar extends JObject
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
			$c = 'plgHwdmediasharePlatform_vzaar';
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
	public static function getUploadForm()
	{
                // Include external scripts and define constants
                require_once(JPATH_ROOT.'/plugins/hwdmediashare/platform_vzaar/assets/Vzaar.php');
                
                JLoader::register('hwdMediaShareHelperNavigation', JPATH_ROOT.'/components/com_hwdmediashare/helpers/navigation.php');
                hwdMediaShareHelperNavigation::setJavascriptVars();

                $plugin =& JPluginHelper::getPlugin('hwdmediashare', 'platform_vzaar');
		$params = new JRegistry( $plugin->params );
                
                // Define constants
                $username       = $params->get('username');
                $token          = $params->get('token');

                $document = JFactory::getDocument();
                $document->addScript("http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js");
                $document->addStyleSheet(JURI::root() . "plugins/hwdmediashare/platform_vzaar/assets/vzaar.css");
                $document->addScript(JURI::root() . "plugins/hwdmediashare/platform_vzaar/assets/swfupload.js");
                $document->addScript(JURI::root() . "plugins/hwdmediashare/platform_vzaar/assets/swfupload.queue.js");
                $document->addScript(JURI::root() . "plugins/hwdmediashare/platform_vzaar/assets/fileprogress.js");
                $document->addScript(JURI::root() . "plugins/hwdmediashare/platform_vzaar/assets/handlers.js");

                Vzaar::$token = $token;
                Vzaar::$secret = $username;
                Vzaar::$enableFlashSupport = true;
                
                ob_start();
                ?>
                <script type="text/javascript">
                    var vzaar_signature = <?php echo(json_encode(Vzaar::getUploadSignature())); ?>;
                    var swfu;
                    var s3Response = {};

                    var j = jQuery.noConflict();

                    j(function(){
                        var settings = {
                            flash_url : "<?php echo JURI::root(); ?>plugins/hwdmediashare/platform_vzaar/assets/swfupload.swf",
                            upload_url: 'http://'+vzaar_signature["vzaar-api"].bucket+'.s3.amazonaws.com/',
                            post_params: {
                                "content-type" : "binary/octet-stream",
                                "acl" : vzaar_signature["vzaar-api"].acl,
                                "policy" :  vzaar_signature["vzaar-api"].policy,
                                "AWSAccessKeyId" :  vzaar_signature["vzaar-api"].accesskeyid,
                                "signature" :  vzaar_signature["vzaar-api"].signature,
                                "success_action_status" : "201",
                                "key" :  vzaar_signature["vzaar-api"].key
                            },
                            // Set the upload success statuses for Chrome issue
                            // http_success : [ 200, 201, 204 ],
                            // Set the upload limit to one because we need to get new vzaar_signature to process multiple uploads
                            // See: https://vzaar.com/help/discussions/questions/2877-multiple-upload-with-swfupload-not-working-properly
                            file_upload_limit: 1,
                            use_query_string: false,
                            file_post_name: 'File',
                            file_size_limit : 0,
                            file_types : "*.*",
                            file_types_description : "All Files",
                            file_upload_limit : 10/*number of files*/,
                            file_queue_limit : 0,
                            custom_settings : {
                                progressTarget : "fsUploadProgress",
                                cancelButtonId : "btnCancel"
                            },
                            debug: false,

                            // Button settings
                            button_width: "150",
                            button_height: "30",
                            button_placeholder_id: "spanButtonPlaceHolder",
                            button_image_url : "<?php echo JURI::root(); ?>/plugins/hwdmediashare/platform_vzaar/assets/button_sprite.png",
                            button_text: '<span class="buttonText">Select Media Files</span>',
                            button_text_style: '.buttonText { text-align:center;font-family:\'Helvetica Neue\',Helvetica,Arial,Verdana,sans-serif;color:#333;font-size:15px;font-weight:bold;text-shadow:0 1px 0 #FF0000; }',
                            button_text_left_padding: 0,
                            button_text_top_padding: 3,

                            // The event handler functions are defined in handlers.js
                            file_queued_handler : fileQueued,
                            file_queue_error_handler : fileQueueError,
                            file_dialog_complete_handler : fileDialogComplete,
                            upload_start_handler : uploadStart,
                            upload_progress_handler : uploadProgress,
                            //upload_error_handler : function uploadError(file, errorCode, message){
                            //    j('#status').html(message);
                            //},
                            upload_error_handler : uploadError,
                            upload_success_handler : function uploadSuccess(file, serverData) {
                                // Added by HWD to switch queue status
                                try {
                                        var progress = new FileProgress(file, this.customSettings.progressTarget);
                                        progress.setComplete();
                                        progress.setStatus("Complete.");
                                        progress.toggleCancel(false);

                                } catch (ex) {
                                        this.debug(ex);
                                }
                                
                                s3Response = j(serverData);

                                if (s3Response.find('key').html()) {
                                    var arrKey = s3Response.find('key').html().split('/');
                                    var guid = arrKey[arrKey.length-2];
                                    
                                    // Calling Process Video service
                                    j.post('<?php echo JURI::root(); ?>index.php?option=com_hwdmediashare&task=addmedia.addCdnUpload&format=raw', {
                                        guid: guid,
                                        filename: file.name,
                                        description: ''
                                    }, function(data){
                                        //j('#status').html(data);
                                    });
                                }
                            },
                            upload_complete_handler : uploadComplete,
                            queue_complete_handler : queueComplete
                        };

                        swfu = new SWFUpload(settings);
                    });
                </script>

                <div id="vzaar">
                    <div class="fieldset flash" id="fsUploadProgress">
                        <span class="legend">Upload Queue</span>
                    </div>
                    <div class="">
                        <span id="spanButtonPlaceHolder"></span>
                    </div>
                    <div id="divStatus">0 Files Uploaded</div>
                    <div class="">
                        <input id="btnCancel" type="button" value="Cancel All Uploads" onclick="swfu.cancelQueue();" disabled="disabled" />
                    </div>
                    <div id="status"></div>
                </div>
                <?php
                $html = ob_get_contents();
                ob_end_clean();
                return $html;  
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
	public function display($item)
	{
                // Load hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();

                $this->width = $utilities->getMediaWidth();
                $this->height = (int) $config->get('mediaitem_height') ? $config->get('mediaitem_height') : $this->width*$config->get('video_aspect',0.75);

                ob_start();
                ?>
                <iframe allowFullScreen allowTransparency="true" class="vzaar-video-player" frameborder="0" width="<?php echo $this->width; ?>" height="<?php echo $this->height; ?>" id="vzvd-<?php echo $item->source; ?>" name="vzvd-<?php echo $item->source; ?>" src="http://view.vzaar.com/<?php echo $item->source; ?>/player" title="Media player" type="text/html"></iframe>
                <?php
                $html = ob_get_contents();
                ob_end_clean();
                return $html;  
        } 
        
        /**
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/
	public function addCdnUpload()
	{
                $user = & JFactory::getUser();
                $date =& JFactory::getDate();
                
                hwdMediaShareFactory::load('upload');
                
                $plugin =& JPluginHelper::getPlugin('hwdmediashare', 'platform_vzaar');
		$params = new JRegistry( $plugin->params );
                
                // Include external scripts and define constants
                require_once(JPATH_ROOT.'/plugins/hwdmediashare/platform_vzaar/assets/Vzaar.php');
                
                // Define constants
                $username       = $params->get('username');
                $token          = $params->get('token');

                $guId           = JRequest::getVar('guid');
                $filename       = JRequest::getVar('filename');

                Vzaar::$token = $token;
                Vzaar::$secret = $username;

                if (isset($guId)) 
                {
                        $media = Vzaar::processVideo($guId, $filename, '', 1);

                        $key = hwdMediaShareUpload::generateKey();

                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                        $row =& JTable::getInstance('Media', 'hwdMediaShareTable');

                        $data                    = array();
                        $data['id'] = 0;
                        $data['asset_id'] = '';
                        $data['ext_id'] = '';
                        $data['media_type'] = 4;
                        $data['key'] = $key;
                        $data['title'] = $filename;
                        $data['alias'] = JFilterOutput::stringURLSafe($filename);
                        $data['description'] = '';
                        $data['type'] = '6';
                        $data['source'] = $media;
                        $data['storage'] = 'platform_vzaar';
                        $data['duration'] = '';
                        $data['streamer'] = '';
                        $data['file'] = '';
                        $data['embed_code'] = '';
                        $data['thumbnail'] = 'http://view.vzaar.com/'.$media.'/image';
                        $data['thumbnail_ext_id'] = '';
                        $data['location'] = '';
                        $data['private'] = '';
                        $data['likes'] = '0';
                        $data['dislikes'] = '0';
                        $data['status'] = '1';
                        $data['published'] = '1';
                        $data['featured'] = '0';
                        $data['checked_out'] = '';
                        $data['checked_out_time'] = '';
                        $data['access'] = '1';
                        $data['download'] = '1';
                        $data['params'] = '';
                        $data['ordering'] = '';
                        $data['created_user_id'] = $user->id;
                        $data['created_user_id_alias'] = '';
                        $data['created'] = $date->format('Y-m-d H:i:s');
                        $data['publish_up'] = $date->format('Y-m-d H:i:s');
                        $data['publish_down'] = "0000-00-00 00:00:00";
                        $data['modified_user_id'] = '';
                        $data['modified'] = '';
                        $data['hits'] = '0';
                        $data['language'] = '*';    

                        // Bind it to the table
                        if (!$row->bind( $data ))
                        {
                                $this->setError($row->getError());
                                return false; 
                        }

                        // Store it in the db
                        if (!$row->store())
                        {
                                $this->setError($row->getError());
                                return false; 
                        }
                }
                else
                {
                        echo('GUID is missing');
                }
        }   

        /**
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/
	public function syncToCdn()
	{
                $user = & JFactory::getUser();
                $date =& JFactory::getDate();
                
                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                $table =& JTable::getInstance('Media', 'hwdMediaShareTable');
                $table->load(JRequest::getVar('id'));
                $properties = $table->getProperties(1);
                $item = JArrayHelper::toObject($properties, 'JObject');
                
                hwdMediaShareFactory::load('upload');
                 
                $plugin =& JPluginHelper::getPlugin('hwdmediashare', 'platform_vzaar');
		$params = new JRegistry( $plugin->params );
                
                // Include external scripts and define constants
                require_once(JPATH_ROOT.'/plugins/hwdmediashare/platform_vzaar/assets/Vzaar.php');
                
                // Define constants
                $username       = $params->get('username');
                $token          = $params->get('token');

                Vzaar::$token = $token;
                Vzaar::$secret = $username;                

                $media = Vzaar::editVideo($item->source, $item->title, $item->description);
        } 
        
        /**
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/
	public function syncFromCdn()
	{
                $user = & JFactory::getUser();
                $date =& JFactory::getDate();
                
                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                $table =& JTable::getInstance('Media', 'hwdMediaShareTable');
                $table->load(JRequest::getVar('id'));
                $properties = $table->getProperties(1);
                $item = JArrayHelper::toObject($properties, 'JObject');
                
                hwdMediaShareFactory::load('upload');
                 
                $plugin =& JPluginHelper::getPlugin('hwdmediashare', 'platform_vzaar');
		$params = new JRegistry( $plugin->params );
                
                // Include external scripts and define constants
                require_once(JPATH_ROOT.'/plugins/hwdmediashare/platform_vzaar/assets/Vzaar.php');
                
                // Define constants
                $username       = $params->get('username');
                $token          = $params->get('token');

                Vzaar::$token = $token;
                Vzaar::$secret = $username;                

                $media = Vzaar::getVideoDetails($item->source, true);

                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                $row =& JTable::getInstance('Media', 'hwdMediaShareTable');

                $data                    = array();
                $data['id'] = $item->id;
                //$data['asset_id'] = '';
                //$data['ext_id'] = '';
                //$data['media_type'] = $this->kalturaMediaType2HWD($media->mediaType); // http://www.kaltura.com/api_v3/testmeDoc/index.php?object=KalturaMediaType
                //$data['key'] = $key;
                $data['title'] = $media->title;
                //$data['alias'] = JFilterOutput::stringURLSafe($media->name);
                $data['description'] = $media->description;
                //$data['type'] = '6';
                //$data['source'] = $row->source;
                //$data['storage'] = '';
                $data['duration'] = $media->duration;
                //$data['streamer'] = '';
                //$data['file'] = '';
                //$data['embed_code'] = '';
                $data['thumbnail'] = 'http://view.vzaar.com/'.$item->source.'/image';
                //$data['thumbnail_ext_id'] = '';
                //$data['location'] = '';
                //$data['private'] = '';
                //$data['likes'] = '0';
                //$data['dislikes'] = '0';
                //$data['status'] = '1';
                //$data['published'] = '1';
                //$data['featured'] = '0';
                //$data['checked_out'] = '';
                //$data['checked_out_time'] = '';
                //$data['access'] = '1';
                //$data['download'] = '1';
                //$data['params'] = '';
                //$data['ordering'] = '';
                //$data['created_user_id'] = $user->id;
                //$data['created_user_id_alias'] = '';
                //$data['created'] = $date->format('Y-m-d H:i:s');
                //$data['publish_up'] = $date->format('Y-m-d H:i:s');
                //$data['publish_down'] = "0000-00-00 00:00:00";
                $data['modified_user_id'] = $date->format('Y-m-d H:i:s');
                $data['modified'] = $user->id;
                //$data['hits'] = '0';
                //$data['language'] = '*';    

                // Bind it to the table
                if (!$row->bind( $data ))
                {
                        $this->setError($row->getError());
                        return false; 
                }

                // Store it in the db
                if (!$row->store())
                {
                        $this->setError($row->getError());
                        return false; 
                }
        }  
}