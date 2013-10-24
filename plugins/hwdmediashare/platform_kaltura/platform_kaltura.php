<?php
/**
 * @version    $Id: platform_kaltura.php 1549 2013-06-11 10:50:37Z dhorsfall $
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
class plgHwdmediasharePlatform_kaltura extends JObject
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
			$c = 'plgHwdmediasharePlatform_kaltura';
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
                require_once(JPATH_ROOT.'/plugins/hwdmediashare/platform_kaltura/assets/KalturaClient.php');

                JLoader::register('hwdMediaShareHelperNavigation', JPATH_ROOT.'/components/com_hwdmediashare/helpers/navigation.php');
                hwdMediaShareHelperNavigation::setJavascriptVars();

                $plugin =& JPluginHelper::getPlugin('hwdmediashare', 'platform_kaltura');
		$params = new JRegistry( $plugin->params );

                // Define constants
                $partnerId      = $params->get('kPartnerId');
                $userSecret     = $params->get('kUserSecret');
                $adminSecret    = $params->get('kAdminSecret');
                $userId         = "ANONYMOUS";
                $userId         = $params->get('kUserId');

                $isAdmin        = true;
		$sessionType = ($isAdmin)? KalturaSessionType::ADMIN : KalturaSessionType::USER;

                // Construction of Kaltura objects for session initiation
                $config         = new KalturaConfiguration($partnerId);
                $client         = new KalturaClient($config);
                $ks             = $client->session->start($adminSecret, $partnerId, $sessionType);

                $flashVars = array();
                $flashVars["uid"]           = $userId;
                $flashVars["partnerId"]     = $partnerId;
                $flashVars["subPId"]        = $partnerId*100;
                $flashVars["entryId"]       = -1;
                $flashVars["ks"]            = $ks;
                $flashVars["maxFileSize"]   = 200;
                $flashVars["maxTotalSize"]  = 5000;
                $flashVars["uiConfId"]      = (isset($_GET['uiconf']))? $_GET['uiconf']: 7578522;
                $flashVars["jsDelegate"]    = "delegate";

                $document = JFactory::getDocument();
                $document->addScript("http://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js");
                $document->addStyleSheet(JURI::root() . "media/com_hwdmediashare/assets/css/fancy.css");
                $document->addStyleDeclaration('
#flashContainer{ position:relative; }
object, embed{ position:absolute; top:0; left:0; z-index:999;}
'
                        );

                ob_start();
                ?>
<style>
#kaltura-progress {margin-top:20px;}
#kaltura-progress-graphic {margin-top:10px;}
</style>
                <script type="text/javascript">
                var flashObj;
                var delegate = {};
                var mediaTypeInput;

                //KSU handlers
                delegate.readyHandler = function()
                {
                        flashObj = document.getElementById("uploader");
                }

                delegate.selectHandler = function()
                {
                        flashObj.upload();
                        //console.log("selectHandler()");
                        //console.log(flashObj.getTotalSize());
                }

                function setMediaType()
                {
                        var mediaType = document.getElementById("mediaTypeInput").value; alert(mediaType);
                        //console.log(mediaType);
                        flashObj.setMediaType(mediaType);
                }

                function setRequest(entryId)
                {
                        var targetUrl = hwdms_live_site;
                        var myRequest = new Request({
                            url: targetUrl
                        }).send('option=com_hwdmediashare&task=addmedia.addCdnUpload&id='+entryId+'&format=raw');
                }

                delegate.singleUploadCompleteHandler = function(args)
                {
                        flashObj.addEntries();
                        //console.log("singleUploadCompleteHandler", args[0].title);
                }

                delegate.allUploadsCompleteHandler = function()
                {
                        //console.log("allUploadsCompleteHandler");
                }

                delegate.entriesAddedHandler = function(entries)
                {
                        //alert(entries.length);
                        var entry = entries[0];
                        //alert(entry.entryId);
                        //document.getElementById('entryid').value = entry.entryId
                        //console.log(entries);
                        setRequest(entry.entryId);
                        var textLink = '<strong>Upload Successful</strong> (<a href="index.php?option=com_hwdmediashare&view=media">Edit</a>)';
                	document.getElementById("kaltura-progress-text").setAttribute("style", "display:block;");
                        document.getElementById('kaltura-progress-text').set('html', textLink);
                	document.getElementById("kaltura-progress-graphic").setAttribute("style", "visibility:hidden;");
                        document.getElementById('kaltura-progress-graphic').set('src', '<?php echo JURI::root(true); ?>/media/com_hwdmediashare/assets/images/ajaxupload/success.png').setStyle('background', 'none');
                }

                delegate.progressHandler = function(args)
                {
                        var percent = parseInt((args[0] / args[1]) * 100);
                        var offset = parseInt(percent * 2.5);
                        var pos = 400 - offset;
                        document.getElementById('kaltura-progress').setStyle('display', 'block');
                        document.getElementById('kaltura-progress-title').set('html', args[2].title);
                	document.getElementById("kaltura-progress-text").setAttribute("style", "display:inline;");
                        document.getElementById('kaltura-progress-text').set('html', '(' + percent + '%)');
                	document.getElementById("kaltura-progress-graphic").setAttribute("style", "visibility:visible;");
                        document.getElementById('kaltura-progress-graphic').set('src', '<?php echo JURI::root(true); ?>/media/com_hwdmediashare/assets/images/ajaxupload/progress-bar/bar.gif').setStyles({
    'background': 'url(<?php echo JURI::root(true); ?>/media/com_hwdmediashare/assets/images/ajaxupload/progress-bar/progress.gif) no-repeat scroll 50% 0 transparent',
    'margin-right': '0.5em',
    'vertical-align': 'middle',
    'background-position': '-' + pos + 'px 0px'
});
                        //console.log(args[2].title + ": " + args[0] + " / " + args[1]);
                }

                delegate.uiConfErrorHandler = function()
                {
                        //console.log("ui conf loading error");
                }

                <!--- JavaScript callback methods to activate Kaltura services via the KSU widget.-->
                function upload()
                {
                        flashObj.upload();
                        flashObj.addEntries();
                }
                </script>
                <?php if (JFactory::getApplication()->isAdmin()) : ?>
                <fieldset class="adminform">
                    <ul class="panelform">
                        <li>
                            <label><?php echo JText::_('COM_HWDMS_SUPPORTED_FORMATS_LABEL'); ?></label>
                            <span class="faux-label" style="clear:none;"><?php echo @$platformExtensionReadable; ?></span>
                        </li>
                    </ul>
                </fieldset>
                <fieldset class="adminform">
                    <ul class="panelform">
                        <li>
                            <div id="flashContainer">
                                <div id="uploader"></div>
                                <script language="JavaScript" type="text/javascript">
                                var params = {
                                        allowScriptAccess: "always",
                                        allowNetworking: "all",
                                        wmode: "transparent",
                                };
                                var attributes  = {
                                        id: "uploader",
                                        name: "uploader",
                                        style: "margin-left:0px;margin-top:0px;"
                                };
                                // set flashVar object
                                var flashVars = <?php echo json_encode($flashVars); ?>;
                                    <!--embed flash object-->
                                swfobject.embedSWF("http://www.kaltura.com/kupload/ui_conf_id/<?php echo $flashVars["uiConfId"]; ?>", "uploader", "200", "30", "9.0.0", "expressInstall.swf", flashVars, params,attributes);
                                </script>
                            </div>
                            <input type="button" style="margin:0;padding:5px 10px;" value="Browse & Select">
                        </li>
                        <div class="clr"></div>
                        <li>
                            <div>
                                <div id="kaltura-progress" style="display:none;">
                                    <span id="kaltura-progress-title" class="progress-title"></span>
                                    <span id="kaltura-progress-text" class="progress-text"></span>
                                    <div class="clr"></div>
                                    <img id="kaltura-progress-graphic" class="progress" src="<?php echo JURI::root(true); ?>/media/com_hwdmediashare/assets/images/ajaxupload/progress-bar/bar.gif" style="background-position: -400px 0px;" title="0%">
                                </div>
                            </div>
                        </li>
                    </ul>
                </fieldset>
                <?php else : ?>
                <fieldset class="adminform">
                    <div class="formelm">
                    <label><?php echo JText::_('COM_HWDMS_SUPPORTED_FORMATS_LABEL'); ?></label>
                    <span><?php echo @$platformExtensionReadable; ?></span>
                    </div>
                </fieldset>
                <fieldset class="adminform">
                    <div class="formelm">
                    <div id="flashContainer">
                        <div id="uploader"></div>
                        <script language="JavaScript" type="text/javascript">
                        var params = {
                                allowScriptAccess: "always",
                                allowNetworking: "all",
                                wmode: "transparent",
                        };
                        var attributes  = {
                                id: "uploader",
                                name: "uploader",
                                style: "margin-left:0px;margin-top:0px;"
                        };
                        // set flashVar object
                        var flashVars = <?php echo json_encode($flashVars); ?>;
                        <!--embed flash object-->
                        swfobject.embedSWF("http://www.kaltura.com/kupload/ui_conf_id/<?php echo $flashVars["uiConfId"]; ?>", "uploader", "200", "30", "9.0.0", "expressInstall.swf", flashVars, params,attributes);
                        </script>
                    </div>
                    <input type="button" style="margin:0;padding:0;" value="Browse & Select">
                    </div>
                    <div class="formelm">
                    <span id="kaltura-progress-title" class="progress-title"></span>
                    <span id="kaltura-progress-text" class="progress-text"></span>
                    </div>
                    <div class="formelm">
                    <div id="kaltura-progress" style="display:none;">
                        <img id="kaltura-progress-graphic" class="progress" src="<?php echo JURI::root(true); ?>/media/com_hwdmediashare/assets/images/ajaxupload/progress-bar/bar.gif" style="background-position: -400px 0px;" title="0%">
                    </div>
                    </div>
                </fieldset>
                <?php endif; ?>
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
	public function getLocalQueue()
	{
                 // Create a new query object.
                $db = JFactory::getDBO();

                // Setup query
                $query = $db->getQuery(true);

                // Select the required fields from the table.
                $query->select('a.*');

                $query->from('#__hwdms_media AS a');
                $query->join('LEFT', '`#__hwdms_processes` AS p ON p.media_id = a.id');

                $query->where('a.type = 1');
                $query->where('a.status = 1');
                $query->where('(p.status = 2 || p.status = 4)');

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
	public function display($item)
	{
                // Load hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

		$plugin =& JPluginHelper::getPlugin('hwdmediashare', 'platform_kaltura');
		$params = new JRegistry( @$plugin->params );

                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();

                $width = $utilities->getMediaWidth();
                $height = (int) $config->get('mediaitem_height') ? $config->get('mediaitem_height') : $width*$config->get('video_aspect',0.75);
                $partnerId = $params->get('kPartnerId');
                $uiConfId = $params->get('uiConfId', '7752572');
                $entryId = $item->source;

                ob_start();
                ?>
<div id="hwd-kaltura-player" style="width:<?php echo $width; ?>px;height:<?php echo $height; ?>px;"></div>
<script src="http://cdnapi.kaltura.com/p/<?php echo $partnerId; ?>/sp/<?php echo $partnerId; ?>00/embedIframeJs/uiconf_id/<?php echo $uiConfId; ?>/partner_id/<?php echo $partnerId; ?>"></script>
<script>
      kWidget.embed({
         'targetId': 'hwd-kaltura-player',
         'wid': '_<?php echo $partnerId; ?>',
         'uiconf_id' : '<?php echo $uiConfId; ?>',
         'entry_id' : '<?php echo $entryId; ?>',
         'flashvars':{ // flashvars allows you to set runtime uiVar configuration overrides.
              'autoPlay': false
         },
         'params':{ // params allows you to set flash embed params such as wmode, allowFullScreen etc
              'wmode': 'transparent'
         },
         readyCallback: function( playerId ){
              console.log( 'Player:' + playerId + ' is ready ');
         }
 });
</script>
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

                $plugin =& JPluginHelper::getPlugin('hwdmediashare', 'platform_kaltura');
		$params = new JRegistry( $plugin->params );

                // Include external scripts and define constants
                require_once(JPATH_ROOT.'/plugins/hwdmediashare/platform_kaltura/assets/KalturaClient.php');

                // Define constants
                $partnerId      = $params->get('kPartnerId');
                $userSecret     = $params->get('kUserSecret');
                $adminSecret    = $params->get('kAdminSecret');
                $userId         = "ANONYMOUS";
                $userId         = $params->get('kUserId');

                $entryId        = JRequest::getVar('id');

                $isAdmin        = true;
		$sessionType = ($isAdmin)? KalturaSessionType::ADMIN : KalturaSessionType::USER;

                $kConfig        = new KalturaConfiguration($partnerId);
		$kConfig->serviceUrl = "http://www.kaltura.com";
		$client = new KalturaClient($kConfig);

		try
		{
			$ks = $client->generateSession($adminSecret, $userId, $sessionType, $partnerId);
			$client->setKs($ks);
		}
		catch(Exception $ex)
		{
			die("could not start session - check configurations in KalturaTestConfiguration class");
		}

                $media = $client->media->get($entryId);

                $key = hwdMediaShareUpload::generateKey();

                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                $row =& JTable::getInstance('Media', 'hwdMediaShareTable');

                $data                    = array();
                $data['id'] = 0;
                $data['asset_id'] = '';
                $data['ext_id'] = '';
                $data['media_type'] = $this->kalturaMediaType2HWD($media->mediaType); // http://www.kaltura.com/api_v3/testmeDoc/index.php?object=KalturaMediaType
                $data['key'] = $key;
                $data['title'] = $media->name;
                $data['alias'] = JFilterOutput::stringURLSafe($media->name);
                $data['description'] = '';
                $data['type'] = '6';
                $data['source'] = JRequest::getVar('id');
                $data['storage'] = '';
                $data['duration'] = $media->duration;
                $data['streamer'] = '';
                $data['file'] = '';
                $data['embed_code'] = '';
                $data['thumbnail'] = $media->thumbnailUrl;
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

                $plugin =& JPluginHelper::getPlugin('hwdmediashare', 'platform_kaltura');
		$params = new JRegistry( $plugin->params );

                // Include external scripts and define constants
                require_once(JPATH_ROOT.'/plugins/hwdmediashare/platform_kaltura/assets/KalturaClient.php');

                // Define constants
                $partnerId      = $params->get('kPartnerId');
                $adminSecret    = $params->get('kAdminSecret');
                $userId         = $params->get('kUserId');

                $isAdmin        = true;
		$sessionType    = ($isAdmin)? KalturaSessionType::ADMIN : KalturaSessionType::USER;

                $kConfig        = new KalturaConfiguration($partnerId);
		$kConfig->serviceUrl = "http://www.kaltura.com";
		$client = new KalturaClient($kConfig);

		try
		{
			$ks = $client->generateSession($adminSecret, $userId, $sessionType, $partnerId);
			$client->setKs($ks);
		}
		catch(Exception $ex)
		{
			die("could not start session - check configurations in KalturaTestConfiguration class");
		}

                $k = new KalturaMediaEntry();

                $k->name=$item->title;
                $k->description=$item->description;

                $client->media->update($item->source, $k);
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

                $plugin =& JPluginHelper::getPlugin('hwdmediashare', 'platform_kaltura');
		$params = new JRegistry( $plugin->params );

                // Include external scripts and define constants
                require_once(JPATH_ROOT.'/plugins/hwdmediashare/platform_kaltura/assets/KalturaClient.php');

                // Define constants
                $partnerId      = $params->get('kPartnerId');
                $adminSecret    = $params->get('kAdminSecret');
                $userId         = $params->get('kUserId');

                $isAdmin        = true;
		$sessionType    = ($isAdmin)? KalturaSessionType::ADMIN : KalturaSessionType::USER;

                $kConfig        = new KalturaConfiguration($partnerId);
		$kConfig->serviceUrl = "http://www.kaltura.com";
		$client = new KalturaClient($kConfig);

		try
		{
			$ks = $client->generateSession($adminSecret, $userId, $sessionType, $partnerId);
			$client->setKs($ks);
		}
		catch(Exception $ex)
		{
			die("could not start session - check configurations in KalturaTestConfiguration class");
		}

                $media = $client->media->get($item->source);

                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                $row =& JTable::getInstance('Media', 'hwdMediaShareTable');

                $data                    = array();
                $data['id'] = $item->id;
                //$data['asset_id'] = '';
                //$data['ext_id'] = '';
                $data['media_type'] = $this->kalturaMediaType2HWD($media->mediaType); // http://www.kaltura.com/api_v3/testmeDoc/index.php?object=KalturaMediaType
                //$data['key'] = $key;
                $data['title'] = $media->name;
                //$data['alias'] = JFilterOutput::stringURLSafe($media->name);
                $data['description'] = $media->description;
                //$data['type'] = '6';
                //$data['source'] = $row->source;
                //$data['storage'] = '';
                $data['duration'] = $media->duration;
                //$data['streamer'] = '';
                //$data['file'] = '';
                //$data['embed_code'] = '';
                $data['thumbnail'] = $media->thumbnailUrl;
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

        /**
	 * Method to add a file to the database
         *
	 * @since   0.1
	 **/
	public function kalturaMediaType2HWD($type)
	{
                switch ($type)
                {
                    case 5:
                        return 1;
                        break;
                    case 2:
                        return 3;
                        break;
                    case 1:
                        return 4;
                        break;
                }
        }
}