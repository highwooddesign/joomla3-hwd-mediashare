<?php
/**
 * @package     Joomla.site
 * @subpackage  Plugin.hwdmediashare.platform_kaltura
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class plgHwdmediasharePlatform_kaltura extends JObject
{
	/**
	 * Holds the new item details.
         * 
         * @access      public
	 * @var         object
	 */
	public $_item;
        
	/**
	 * Class constructor.
	 *
	 * @access  public
         * @return  void
	 */
	public function __construct()
	{
		parent::__construct();
	}
        
	/**
	 * Returns the plgHwdmediasharePlatform_kaltura object, only creating it if it
	 * doesn't already exist.
	 *
	 * @access  public
	 * @return  object  The plgHwdmediasharePlatform_kaltura object.
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
	 * Renders the upload form for this media platform.
	 *
	 * @access  public
         * @return  string  The HTML to render the upload form.
	 */
	public function getUploadForm()
	{
                // Load plugin.
		$plugin = JPluginHelper::getPlugin('hwdmediashare', 'platform_kaltura');
		
                // Load the language file.
                $lang = JFactory::getLanguage();
                $lang->load('plg_hwdmediashare_platform_kaltura', JPATH_ADMINISTRATOR, $lang->getTag());

                if (!$plugin)
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_PLATFORM_KALTURA_ERROR_NOT_PUBLISHED'));
                        return false;
                }

                $this->getUploadAssets();

                ob_start();
                ?>
                <div id="kaltura">
                    <div id="flashContainer">
                        <div id="uploader"></div>
                    </div>
                    <div class="btn-group">
                            <input type="button" class="btn" style="margin:0;padding:5px 10px;" value="Browse Files">
                    </div>    
                    <div class="clearfix"></div>
                    <div>
                            <span id="kaltura-progress-title" class="progress-title">Upload Progress</span>
                            <div class="kaltura-progress-bar progress-current">
                                    <div style="width:0;" id="kaltura-progress-active"></div>
                            </div>
                    </div>
                    <div id="kaltura-progress-result"></div>
                </div>
                <?php
                $html = ob_get_contents();
                ob_end_clean();
                return $html; 
	}

	/**
	 * Renders the upload form for this media platform.
	 *
	 * @access  public
         * @return  string  The HTML to render the upload form.
	 */
	public function getSiteUploadForm($displayData)
	{
                // Load plugin.
		$plugin = JPluginHelper::getPlugin('hwdmediashare', 'platform_kaltura');
		
                // Load the language file.
                $lang = JFactory::getLanguage();
                $lang->load('plg_hwdmediashare_platform_kaltura', JPATH_ADMINISTRATOR, $lang->getTag());

                if (!$plugin)
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_PLATFORM_KALTURA_ERROR_NOT_PUBLISHED'));
                        return false;
                }

                $this->getUploadAssets();

                ob_start();
                ?>
                <div id="kaltura">
                    <div id="flashContainer">
                        <div id="uploader"></div>
                    </div>
                    <div class="btn-group">
                            <input type="button" class="btn" style="margin:0;padding:5px 10px;" value="Browse Files">
                    </div>    
                    <div class="clearfix"></div>
                    <div>
                            <span id="kaltura-progress-title" class="progress-title">Upload Progress</span>
                            <div class="kaltura-progress-bar progress-current">
                                    <div style="width:0;" id="kaltura-progress-active"></div>
                            </div>
                    </div>
                    <div id="kaltura-progress-result"></div>
                </div>
                <fieldset>
                  <div class="row-fluid">
                    <div class="span8">
                      <div class="control-group">
                        <div class="control-label hide">
                          <?php echo $displayData->form->getLabel('title'); ?>
                        </div>                  
                        <div class="controls">
                          <?php echo $displayData->form->getInput('title'); ?>
                        </div>
                      </div>        
                      <?php if ($displayData->params->get('enable_categories')): ?>
                      <div class="control-group">
                        <div class="control-label hide">
                          <?php echo $displayData->form->getLabel('catid'); ?>
                        </div>              
                        <div class="controls">
                          <?php echo $displayData->form->getInput('catid'); ?>
                        </div>
                      </div>                          
                      <?php endif; ?>            
                      <?php if ($displayData->params->get('enable_tags')): ?>
                      <div class="control-group">
                        <div class="control-label hide">
                          <?php echo $displayData->form->getLabel('tags'); ?>
                        </div>              
                        <div class="controls">
                          <?php echo $displayData->form->getInput('tags'); ?>
                        </div>
                      </div>    
                      <?php endif; ?>             
                    </div>
                    <div class="span4">
                      <div class="control-group">
                        <div class="controls">
                          <?php echo $displayData->form->getInput('private'); ?>
                        </div>
                      </div>            
                      <div class="btn-toolbar row-fluid">
                        <a title="<?php echo JText::_('COM_HWDMS_ADD_REMOTE_MEDIA'); ?>" href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getUploadRoute(array('method' => 'remote'))); ?>" class="btn span12"><?php echo JText::_('COM_HWDMS_BUTTON_OR_ADD_REMOTE_MEDIA'); ?></a>
                      </div>             
                    </div>
                  </div>  
                  <?php echo $displayData->form->getInput('description'); ?>
                  <div class="well well-small">
                    <h3><?php echo JText::_('COM_HWDMS_HELP_AND_SUGGESTIONS'); ?></h3>
                    <?php if ($displayData->params->get('upload_terms_id')): ?>
                      <p><?php echo JText::sprintf('COM_HWDMS_ACKNOWLEDGE_TERMS_AND_CONDITIONS', '<a href="' . JRoute::_('index.php?option=com_content&view=article&id=' . $displayData->params->get('upload_terms_id') . '&tmpl=component') . '" class="media-popup-iframe-page">' . JText::_('COM_HWDMS_TERMS_AND_CONDITIONS_LINK') . '</a>'); ?></p>      
                    <?php endif; ?>
                  </div> 
                </fieldset> 
                <?php
                $html = ob_get_contents();
                ob_end_clean();
                return $html; 
	}
        
	/**
	 * Renders the upload form for this media platform.
	 *
	 * @access  public
         * @return  string  The HTML to render the upload form.
	 */
	public function getUploadAssets()
	{
		// Initialise variables.
                $document = JFactory::getDocument();
                
                // Load plugin.
		$plugin = JPluginHelper::getPlugin('hwdmediashare', 'platform_kaltura');
		
                // Load the language file.
                $lang = JFactory::getLanguage();
                $lang->load('plg_hwdmediashare_platform_kaltura', JPATH_ADMINISTRATOR, $lang->getTag());

                if (!$plugin)
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_PLATFORM_KALTURA_ERROR_NOT_PUBLISHED'));
                        return false;
                }

                // Load parameters.
                $params = new JRegistry($plugin->params);

                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // Require kaltura API framework.
                require_once(JPATH_ROOT.'/plugins/hwdmediashare/platform_kaltura/assets/KalturaClient.php');

                // Define API constants
                $partnerId      = $params->get('kPartnerId');
                $userSecret     = $params->get('kUserSecret');
                $adminSecret    = $params->get('kAdminSecret');
                $userId         = $params->get('kUserId');
                
                // Construction of Kaltura objects for session initiation.
                $kconfig                = new KalturaConfiguration($partnerId);
                $kconfig->serviceUrl    = $params->get('kServiceUrl', 'http://www.kaltura.com');        
                $client                 = new KalturaClient($kconfig);
                $ks                     = $client->session->start($adminSecret, $userId, KalturaSessionType::ADMIN, $partnerId);

                $flashVars = array();
                $flashVars["uid"]           = $userId;
                $flashVars["partnerId"]     = $partnerId;
                $flashVars["subPId"]        = $partnerId*100;
                $flashVars["entryId"]       = -1;
                $flashVars["ks"]            = $ks;
                $flashVars["maxFileSize"]   = 200;
                $flashVars["maxTotalSize"]  = 5000;
                $flashVars["uiConfId"]      = 7578522; // Kaltura Simple Uploader (KSU) - http://knowledge.kaltura.com/kaltura-simple-uploader-ksu-website-integration-guide
                $flashVars["jsDelegate"]    = "delegate";

                // Load kaltura assets.
                $document->addScript("http://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js");
                $document->addStyleSheet(JURI::root() . "plugins/hwdmediashare/platform_kaltura/assets/kaltura.css");
                $document->addStyleSheet(JURI::root() . "media/com_hwdmediashare/assets/css/fancy.css");

                // Retrieve filtered jform data.
                hwdMediaShareFactory::load('upload');
                $jformdata = hwdMediaShareUpload::getProcessedUploadData();

                ob_start();
                ?>
                        var flashObj;
                        var delegate = {};

                        // KSU handlers.
                        delegate.readyHandler = function()
                        {
                                flashObj = document.getElementById("uploader");
                        }

                        delegate.selectHandler = function()
                        {
                                if(document.getElementsByName('jform[title]')[0].value)
                                {
                                        flashObj.setTitle(document.getElementsByName('jform[title]')[0].value, 0, 1);  
                                }
                                flashObj.upload();
                        }

                        delegate.singleUploadCompleteHandler = function(args)
                        {
                                flashObj.addEntries();
                        }

                        delegate.entriesAddedHandler = function(entries)
                        {
                                <?php if ($config->get('upload_workflow') == 0): ?>
                                        jQuery("#adminForm input[name='task']").val('addmedia.platform');
                                        jQuery('#adminForm').attr('action', '<?php echo JURI::base(); ?>index.php?option=com_hwdmediashare&entryid=' + entries[0].entryId);
                                        jQuery('#adminForm').submit();
                                <?php else: ?>
                                        jQuery("#adminForm input[name='task']").val('addmedia.platform')
                                        var posting = jQuery.post('<?php echo JURI::base(); ?>', 
                                            'option=com_hwdmediashare&format=raw&entryid=' + entries[0].entryId + '&' + jQuery('#adminForm').serialize()
                                        );
                                        posting.done(function(data) {
                                            var result = jQuery.parseJSON(data);
                                            //console.log(result);
                                            if (result.status) {
                                                jQuery('#kaltura-progress-result').empty().css('display', 'block').append('<div class="file file-success"><span class="file-name">' + result.data.name + '</span><span class="file-info"><a class="btn" target="_top" href="index'+'.php?option=com_hwdmediashare&amp;task=editmedia.edit&amp;id=' + result.data.id + '">Edit</a></span></div>');
                                            } else {
                                                jQuery('#kaltura-progress-result').empty().css('display', 'block').append('<div class="file file-failed"><span class="file-name">' + result.data.name + '</span><span class="file-info">Upload Failed</span></div>');
                                            }
                                        });
                                <?php endif; ?>
                        }

                        delegate.progressHandler = function(args)
                        {
                                var percent = parseInt((args[0] / args[1]) * 100);
                                jQuery('#kaltura-progress-active').css('width', percent + '%');
                                jQuery('#kaltura-progress-title').empty().append(args[2].title + ' (' + percent + '%)');
                        }                                                 

                        // JavaScript callback methods to activate Kaltura services via the KSU widget.-->
                        function upload()
                        {
                                flashObj.upload();
                                flashObj.addEntries();
                        }

                        // Locate KSU widget on top of GUI element and Embed Flash Object 
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

                        swfobject.embedSWF("http://www.kaltura.com/kupload/ui_conf_id/<?php echo $flashVars["uiConfId"]; ?>", "uploader", "200", "30", "9.0.0", "expressInstall.swf", flashVars, params, attributes);
                <?php
                $js = ob_get_contents();
                ob_end_clean();
                
                $document->addScriptDeclaration($js);  
        }
        
        /**
	 * Render the HTML to display the media.
	 *
	 * @access  public
	 * @param   object  $item  The media item being displayed.
         * @return  string  The HTML to render the media player.
	 */
	public function display($item)
	{
		// Initialise variables.
                $app = JFactory::getApplication();

                // Load plugin.
		$plugin = JPluginHelper::getPlugin('hwdmediashare', 'platform_kaltura');

                // Load the language file.
                $lang = JFactory::getLanguage();
                $lang->load('plg_hwdmediashare_platform_kaltura', JPATH_ADMINISTRATOR, $lang->getTag());

                if (!$plugin)
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_PLATFORM_KALTURA_ERROR_NOT_PUBLISHED'));
                        return false;
                }

                // Load parameters.
                $params = new JRegistry($plugin->params);

                if (!$params->get('kPartnerId'))
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_PLATFORM_KALTURA_ERROR_NO_PARTNERID'));
                        return false;
                }
                
                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                // Load HWD utilities.
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                
                $this->autoplay = $app->input->get('media_autoplay', $config->get('media_autoplay'), 'integer') == 1 ? '1' : '0';
                $this->width = '100%';
                $this->height = '100%';
                ob_start();
                ?>
                <div class="media-respond" style="max-width:<?php echo $config->get('mediaitem_size', '500'); ?>px;">
                  <div class="media-aspect" data-aspect="<?php echo $config->get('video_aspect', '0.75'); ?>"></div>
                  <div class="media-content">
                    <iframe src="http://www.kaltura.com/p/<?php echo $params->get('kPartnerId'); ?>/sp/<?php echo $params->get('kPartnerId'); ?>00/embedIframeJs/uiconf_id/<?php echo $params->get('kUiConf', '30172991');; ?>/partner_id/<?php echo $params->get('kPartnerId'); ?>?iframeembed=true&playerId={UNIQUE_OBJ_ID}&entry_id=<?php echo $item->source; ?>" width="<?php echo $this->width; ?>" height="<?php echo $this->height; ?>" allowfullscreen webkitallowfullscreen mozAllowFullScreen frameborder="0"></iframe>
                  </div>
                </div>
                <?php
                $html = ob_get_contents();
                ob_end_clean();
                return $html;
        } 
        
        /**
	 * Method to add a platform upload to the gallery.
	 *
	 * @access  public
         * @return  boolean  True on success, false on fail.
	 */
	public function addUpload()
	{
		// Initialise variables.
                $app = JFactory::getApplication();
                $user = JFactory::getUser();
                $date = JFactory::getDate();
                
                // Load plugin.
		$plugin = JPluginHelper::getPlugin('hwdmediashare', 'platform_kaltura');
		
                // Load the language file.
                $lang = JFactory::getLanguage();
                $lang->load('plg_hwdmediashare_platform_kaltura', JPATH_ADMINISTRATOR, $lang->getTag());

                if (!$plugin)
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_PLATFORM_KALTURA_ERROR_NOT_PUBLISHED'));
                        return false;
                }

                // Load parameters.
                $params = new JRegistry($plugin->params);

                // Load HWD config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // Load HWD utilities.
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                
                // Load input variables. 
                $entryId = $app->input->get('entryid');

                if ($entryId) 
                {
                        // Require kaltura API framework.
                        require_once(JPATH_ROOT.'/plugins/hwdmediashare/platform_kaltura/assets/KalturaClient.php');

                        // Define API constants
                        $partnerId              = $params->get('kPartnerId');
                        $userSecret             = $params->get('kUserSecret');
                        $adminSecret            = $params->get('kAdminSecret');
                        $userId                 = $params->get('kUserId');

                        // Construction of Kaltura objects for session initiation
                        $kconfig                = new KalturaConfiguration($partnerId);
                        $kconfig->serviceUrl    = $params->get('kServiceUrl', 'http://www.kaltura.com'); 
                        $client                 = new KalturaClient($kconfig);
                        $ks                     = $client->generateSession($adminSecret, $userId, KalturaSessionType::USER, $partnerId);
                        $client->setKs($ks);
                        $media = $client->media->get($entryId);

                        // Define a key so we can copy the file into the storage directory.
                        if (!$key = $utilities->generateKey(1))
                        {
                                $this->setError($utilities->getError());
                                return false;
                        }  

                        // Set approved/pending.
                        (!$app->isAdmin() && $config->get('approve_new_media')) == 1 ? $status = 2 : $status = 1; 
                        $config->get('approve_new_media') == 1 ? $status = 2 : $status = 1; 
                
                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                        $table = JTable::getInstance('media', 'hwdMediaShareTable');
            
                        $post                           = array();
                        //$post['id']                   = '';
                        //$post['asset_id']             = '';
                        $post['ext_id']                 = '';
                        $post['media_type']             = $this->kalturaMediaType2HWD($media->mediaType); // http://www.kaltura.com/api_v3/testmeDoc/index.php?object=KalturaMediaType
                        $post['key']                    = $key;
                        $post['title']                  = $media->name;
                        $post['alias']                  = JFilterOutput::stringURLSafe($media->name);
                        $post['description']            = $app->input->get('description', '', 'string');
                        $post['type']                   = 6; // Platform
                        $post['source']                 = $entryId;
                        $post['storage']                = 'platform_kaltura';
                        $post['duration']               = $media->duration;
                        $post['streamer']               = '';
                        $post['file']                   = '';
                        $post['embed_code']             = '';
                        $post['thumbnail']              = $media->thumbnailUrl;
                        //$post['thumbnail_ext_id']     = '';
                        //$post['location']             = '';
                        //$post['viewed']               = '';
                        //$post['private']              = '';
                        //$post['likes']                = '';
                        //$post['dislikes']             = '';
                        $post['status']                 = $status;
                        $post['published']              = $app->input->get('published', 1);
                        $post['featured']               = $app->input->get('featured', 0);
                        //$post['checked_out']          = '';
                        //$post['checked_out_time']     = '';
                        $post['access']                 = $app->input->get('access', $config->get('default_access', 1));
                        $post['download']               = $config->get('default_download', 1);
                        //$post['params']               = '';
                        //$post['ordering']             = '';
                        $post['created_user_id']        = $user->id;
                        //$post['created_user_id_alias']= '';
                        $post['created']                = $date->toSql();
                        $post['publish_up']             = $date->toSql();
                        $post['publish_down']           = '0000-00-00 00:00:00';
                        $post['modified_user_id']       = $user->id;
                        $post['modified']               = $date->toSql();
                        $post['hits']                   = 0;
                        $post['language']               = $app->input->get('language', '*');
    
                        // Save data to the database.
                        if (!$table->save($post))
                        {
                                $this->setError($table->getError());
                                return false;
                        }  
                        
                        $properties = $table->getProperties(1);
                        $this->_item = JArrayHelper::toObject($properties, 'JObject');                        
                }
                else
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_PLATFORM_KALTURA_ERROR_ENTRYID_MISSING'));
                        return false;
                }
                
                return true;
        }   

       /**
        * Method to syncronise data from the local gallery to a platform. 
        *
        * @access  public
        * @param   $item    The media item to syncronise.
        * @return  boolean  True on success, false on fail.
        */
	public function syncToPlatform($item)
	{
		// Initialise variables.
                $document = JFactory::getDocument();
                $app = JFactory::getApplication();
                $user = JFactory::getUser();
                $date = JFactory::getDate(); 
                
                // Load plugin.
		$plugin = JPluginHelper::getPlugin('hwdmediashare', 'platform_kaltura');
		
                // Load the language file.
                $lang = JFactory::getLanguage();
                $lang->load('plg_hwdmediashare_platform_kaltura', JPATH_ADMINISTRATOR, $lang->getTag());

                if (!$plugin)
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_PLATFORM_KALTURA_ERROR_NOT_PUBLISHED'));
                        return false;
                }

                // Load parameters.
                $params = new JRegistry($plugin->params);

                // Require kaltura API framework.
                require_once(JPATH_ROOT.'/plugins/hwdmediashare/platform_kaltura/assets/KalturaClient.php');

                // Define API constants
                $partnerId              = $params->get('kPartnerId');
                $userSecret             = $params->get('kUserSecret');
                $adminSecret            = $params->get('kAdminSecret');
                $userId                 = $params->get('kUserId');

                // Construction of Kaltura objects for session initiation
                $kconfig                = new KalturaConfiguration($partnerId);
                $kconfig->serviceUrl    = $params->get('kServiceUrl', 'http://www.kaltura.com'); 
                $client                 = new KalturaClient($kconfig);
                $ks                     = $client->generateSession($adminSecret, $userId, KalturaSessionType::USER, $partnerId);
                $client->setKs($ks);

                $k              = new KalturaMediaEntry();
                $k->name        = $item->title;
                $k->description = $item->description;
                
                if ($client->media->update($item->source, $k))
                {
                        return true;
                }
                else
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_PLATFORM_VZAAR_ERROR_UNABLE_TO_WRITE_VIDEO_DETAILS_TO_VZAAR'));                    
                        return false;
                }
        } 
        
       /**
	 * Method to syncronise data from a platform to the local gallery. 
        *
        * @access  public
        * @param   $item    The media item to syncronise.
        * @return  boolean  True on success, false on fail.
        */
	public function syncFromPlatform($item)
	{
		// Initialise variables.
                $document = JFactory::getDocument();
                $app = JFactory::getApplication();
                $user = JFactory::getUser();
                $date = JFactory::getDate(); 
                
                // Load plugin.
		$plugin = JPluginHelper::getPlugin('hwdmediashare', 'platform_kaltura');
		
                // Load the language file.
                $lang = JFactory::getLanguage();
                $lang->load('plg_hwdmediashare_platform_kaltura', JPATH_ADMINISTRATOR, $lang->getTag());

                if (!$plugin)
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_PLATFORM_KALTURA_ERROR_NOT_PUBLISHED'));
                        return false;
                }

                // Load parameters.
                $params = new JRegistry($plugin->params);

                // Require kaltura API framework.
                require_once(JPATH_ROOT.'/plugins/hwdmediashare/platform_kaltura/assets/KalturaClient.php');

                // Define API constants
                $partnerId              = $params->get('kPartnerId');
                $userSecret             = $params->get('kUserSecret');
                $adminSecret            = $params->get('kAdminSecret');
                $userId                 = $params->get('kUserId');

                // Construction of Kaltura objects for session initiation
                $kconfig                = new KalturaConfiguration($partnerId);
                $kconfig->serviceUrl    = $params->get('kServiceUrl', 'http://www.kaltura.com'); 
                $client                 = new KalturaClient($kconfig);
                $ks                     = $client->generateSession($adminSecret, $userId, KalturaSessionType::USER, $partnerId);
                $client->setKs($ks);
                
                if ($media = $client->media->get($item->source))
                {
                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                        $table = JTable::getInstance('media', 'hwdMediaShareTable');
            
                        $data                     = array();
                        $data['id']               = $item->id;
                        $data['media_type']       = $this->kalturaMediaType2HWD($media->mediaType); // http://www.kaltura.com/api_v3/testmeDoc/index.php?object=KalturaMediaType
                        $data['title']            = $media->name;
                        $data['description']      = $media->description;
                        $data['duration']         = $media->duration;
                        $data['thumbnail']        = $media->thumbnailUrl;
                        $data['modified_user_id'] = $date->format('Y-m-d H:i:s');
                        $data['modified']         = $user->id;

                        // Save data to the database.
                        if (!$table->save($data))
                        {
                                $this->setError($table->getError());
                                return false;
                        }     
                }
                else
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_PLATFORM_VZAAR_ERROR_UNABLE_TO_LOAD_VIDEO_DETAILS_FROM_VZAAR'));                    
                        return false;
                }
                
                return true;
        }  
        
	/**
	 * returns the HWD media type from the Kaltura media type.
	 *
	 * @access  public
         * @return  integer  The HWD media type.
	 */
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
