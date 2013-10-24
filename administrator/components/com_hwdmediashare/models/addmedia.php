<?php
/**
 * @version    SVN $Id: addmedia.php 1553 2013-06-11 12:22:31Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * hwdMediaShare Model
 */
class hwdMediaShareModelAddMedia extends JModelAdmin
{
	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param	array	$data	An array of input data.
	 * @param	string	$key	The name of the key for the primary key.
	 *
	 * @return	boolean
	 * @since	0.1
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		// Check specific edit permission then general edit permission.
		return JFactory::getUser()->authorise('core.edit', 'com_hwdmediashare.media.'.((int) isset($data[$key]) ? $data[$key] : 0)) or parent::allowEdit($data, $key);
	}
	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	0.1
	 */
	public function getTable($type = 'Media', $prefix = 'hwdMediaShareTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	0.1
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_hwdmediashare.upload', 'upload', array('control' => 'jform', 'load_data' => $loadData));
                if (empty($form))
		{
			return false;
		}
		return $form;
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	0.1
	 */
	public function getStandardExtensions()
	{
		hwdMediaShareFactory::load('upload');
                $standardExtensions = hwdMediaShareUpload::getAllowedExtensions('standard');
                $this->setState('standardExtensions', $standardExtensions);
                return $standardExtensions;
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	0.1
	 */
	public function getLargeExtensions()
	{
		hwdMediaShareFactory::load('upload');
                $largeExtensions = hwdMediaShareUpload::getAllowedExtensions('large');
                $this->setState('largeExtensions', $largeExtensions);
                return $largeExtensions;
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	0.1
	 */
	public function getPlatformExtensions()
	{
		hwdMediaShareFactory::load('upload');
                $platformExtensions = hwdMediaShareUpload::getAllowedExtensions('platform');
                $this->setState('platformExtensions', $platformExtensions);
                return $platformExtensions;
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	0.1
	 */
	public function getFancyUploadScript()
	{
		$app = &JFactory::getApplication();
                $doc = & JFactory::getDocument();
                $standardExtensions = $this->getState('standardExtensions');

                $standardExtensionstring1 = '';
                $standardExtensionstring2 = '';
                if (is_array($standardExtensions))
                {
                        $last_item = end($standardExtensions);
                        foreach($standardExtensions as $item)
                        {
                                if ($item == $last_item)
                                {
                                        $standardExtensionstring1.= "*.$item";
                                        $standardExtensionstring2.= "*.$item";
                                }
                                else
                                {
                                        $standardExtensionstring1.= "*.$item, ";
                                        $standardExtensionstring2.= "*.$item; ";
                                }
                        }
                }
                $typeFilter = "'Media ($standardExtensionstring1)': '$standardExtensionstring2'";

                hwdMediaShareFactory::load('upload');
                $flashUploadUrl = hwdMediaShareUpload::getFlashUploadURI();
                $swfUrl = JURI::root(true).'/media/com_hwdmediashare/assets/swf/Swiff.Uploader.swf';
                $editTask = ($app->isAdmin() ? 'editmedia' : 'mediaform');

                $js = <<<EOD
//<![CDATA[

/**
 * FancyUpload Showcase
 *
 * @license		MIT License
 * @author		Harald Kirschner <mail [at] digitarald [dot] de>
 * @copyright	Authors
 */

window.addEvent('domready', function() { // wait for the content

	// our uploader instance

	var up = new FancyUpload2($('hwd-upload-status'), $('hwd-upload-list'), { // options object
		// we console.log infos, remove that in production!!
		verbose: false,

		url: '$flashUploadUrl',

		// path to the SWF file
		path: '$swfUrl',

		// Actionscript filefilter is causing problems in latest version sof Flash. Possible
                // solution is to split into different types, until researched will just remove (cosmetic) filter. 
                // remove that line to select all files, or edit it, add more items
		// typeFilter: {
		//	'Images (*.jpg, *.jpeg, *.gif, *.png)': '*.jpg; *.jpeg; *.gif; *.png'
		// },
                // typeFilter: {
                //         'Media ($standardExtensionstring1)': '$standardExtensionstring2'
                // },

                data: '',

                timeLimit: 30,

		// this is our browse button, *target* is overlayed with the Flash movie
		target: 'hwd-upload-browse',

		// graceful degradation, onLoad is only called if all went well with Flash
		onLoad: function() {
			$('hwd-upload-status').removeClass('hide'); // we show the actual UI
			$('hwd-upload-fallback').destroy(); // ... and hide the plain form

			// We relay the interactions with the overlayed flash to the link
			this.target.addEvents({
				click: function() {
					return false;
				},
				mouseenter: function() {
					this.addClass('hover');
				},
				mouseleave: function() {
					this.removeClass('hover');
					this.blur();
				},
				mousedown: function() {
					this.focus();
				}
			});

			// Interactions for the 2 other buttons

			$('hwd-upload-clear').addEvent('click', function() {
				up.remove(); // remove all files
				return false;
			});

			$('hwd-upload-upload').addEvent('click', function() {
				up.start(); // start upload
				return false;
			});
		},

		// Edit the following lines, it is your custom event handling
onBeforeStart: function() {
    var listSize = this.fileList.length;
    for (var i=0; i < listSize; i++){
        //alert(JSON.encode($('adminForm').toQueryString().parseQueryString()));

        // Set a flag to avoid leading & in query string.
        var flag = false;
        var adminFormData = '';
/**
 * We originally put the category selection in a select box, but now just a hidden input
 *   
        if ($('adminForm').jform_catid) {
            for (var j=0; j<$('jform_catid').options.length; j++) {
                if ($('jform_catid').options[j].selected) {
                    if ($('jform_catid').options[j].value > 0)
                    {
                        if (flag)
                        {
                            adminFormData+= '&catid=' + $('jform_catid').options[j].value;

                        }
                        else
                        {
                            var flag = true;
                            adminFormData+= 'catid=' + $('jform_catid').options[j].value;
                        }
                    }
                }
            }
        }
 */                   
        if ($('adminForm').jform_catid && $('adminForm').jform_catid.value > 0) {
            if (flag) {
                adminFormData+= '&catid=' + $('adminForm').jform_catid.value;
            } else {
                var flag = true;
                adminFormData+= 'catid=' + $('adminForm').jform_catid.value;
            }
        }   
        if ($('adminForm').jform_album_id && $('adminForm').jform_album_id.value > 0) {
            if (flag) {
                adminFormData+= '&album_id=' + $('adminForm').jform_album_id.value;
            } else {
                var flag = true;
                adminFormData+= 'album_id=' + $('adminForm').jform_album_id.value;
            }
        }
        if ($('adminForm').jform_playlist_id && $('adminForm').jform_playlist_id.value > 0) {
            if (flag) {
                adminFormData+= '&playlist_id=' + $('adminForm').jform_playlist_id.value;
            } else {
                var flag = true;
                adminFormData+= 'playlist_id=' + $('adminForm').jform_playlist_id.value;
            }
        }
        if ($('adminForm').jform_group_id && $('adminForm').jform_group_id.value > 0) {
            if (flag) {
                adminFormData+= '&group_id=' + $('adminForm').jform_group_id.value;
            } else {
                var flag = true;
                adminFormData+= 'group_id=' + $('adminForm').jform_group_id.value;
            }
        }
        if ($('adminForm').jform_user_id && $('adminForm').jform_user_id.value > 0) {
            if (flag) {
                adminFormData+= '&user_id=' + $('adminForm').jform_user_id.value;
            } else {
                var flag = true;
                adminFormData+= 'user_id=' + $('adminForm').jform_user_id.value;
            }
        }
        if (adminFormData) {
            this.fileList[i].setOptions({data: adminFormData.parseQueryString()});
        }
    }
},

		/**
		 * This one was directly in FancyUpload2 before, the event makes it
		 * easier for you, to add your own response handling (you probably want
		 * to send something else than JSON or different items).
		 *
		 * onFileProgress: function(file) {
		 *	alert(file.progress.percentLoaded);
		 * },
                 *
                 */

                /**
		 * Is called when files were not added, "files" is an array of invalid File classes.
		 *
		 * This example creates a list of error elements directly in the file list, which
		 * hide on click.
		 */
		onSelectFail: function(files) {
			files.each(function(file) {
				new Element('li', {'class': 'validation-error',
					html: file.validationErrorMessage || file.validationError,
					title: MooTools.lang.get('FancyUpload', 'removeTitle'),
					events: {
						click: function() {
							this.destroy();
						}
					}
				}).inject(this.list, 'top');
			}, this);
		},

		/**
		 * This one was directly in FancyUpload2 before, the event makes it
		 * easier for you, to add your own response handling (you probably want
		 * to send something else than JSON or different items).
                 * 
                 * In this URL we create a dummy space to prevent Joomla converting to SEF
		 */
		onFileSuccess: function(file, response) {
			var json = new Hash(JSON.decode(response, true) || {});

			if (json.get('status') == '1') {
				file.element.addClass('file-success');
				file.info.set('html', '<strong>Succesfully uploaded</strong> <a href="index'+'.php?option=com_hwdmediashare&task=$editTask.edit&id='+json.get('id')+'" target="_top">Edit</a>');
			} else {
				file.element.addClass('file-failed');
				file.info.set('html', '<strong>An error occured:</strong> ' + (json.get('error') ? (json.get('error') + ' #' + json.get('code')) : response));
			}
		},

		/**
		 * onFail is called when the Flash movie got bashed by some browser plugin
		 * like Adblock or Flashblock.
		 */
		onFail: function(error) {
			switch (error) {
				case 'hidden': // works after enabling the movie and clicking refresh
					alert('To enable the embedded uploader, unblock it in your browser and refresh (see Adblock).');
					break;
				case 'blocked': // This no *full* fail, it works after the user clicks the button
					alert('To enable the embedded uploader, enable the blocked Flash movie (see Flashblock).');
					break;
				case 'empty': // Oh oh, wrong path
                                        // Stop this alert because it commonly loads for people when the page loads slowly
					// alert('A required file was not found, please be patient and we fix this.');
					break;
				case 'flash': // no flash 9+ :(
					alert('To enable the embedded uploader, install the latest Adobe Flash plugin.')
			}
		}

	});

});
//]]>
EOD;

                $doc->addScriptDeclaration( $js );

		return;
	}
	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	0.1
	 */
	public function getUberUploadScript()
	{
                $doc = & JFactory::getDocument();
                $largeExtensions = $this->getState('largeExtensions');

                // Load the parameters.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                $largeExtensionstring = '';
                $largeExtensionReadable = '';
                if (is_array($largeExtensions))
                {
                        $last_item = end($largeExtensions);
                        foreach($largeExtensions as $item)
                        {
                                if ($item == $last_item)
                                {
                                        $largeExtensionstring.= $item;
                                        $largeExtensionReadable.= $item;
                                }
                                else
                                {
                                        $largeExtensionstring.= $item.'|';
                                        $largeExtensionReadable.= $item.', ';
                                }
                        }
                }

//******************************************************************************************************
//   ATTENTION: THIS FILE HEADER MUST REMAIN INTACT. DO NOT DELETE OR MODIFY THIS FILE HEADER.
//
//   Name: ubr_file_upload.php
//   Revision: 1.5
//   Date: 3/2/2008 11:16:38 AM
//   Link: http://uber-uploader.sourceforge.net
//   Initial Developer: Peter Schmandra  http://www.webdice.org
//   Description: Select and submit upload files.
//
//   Licence:
//   The contents of this file are subject to the Mozilla Public
//   License Version 1.1 (the "License"); you may not use this file
//   except in compliance with the License. You may obtain a copy of
//   the License at http://www.mozilla.org/MPL/
//
//   Software distributed under the License is distributed on an "AS
//   IS" basis, WITHOUT WARRANTY OF ANY KIND, either express or
//   implied. See the License for the specific language governing
//   rights and limitations under the License.
//
//******************************************************************************************************

$THIS_VERSION = '1.5';

require_once(JPATH_ROOT.'/components/com_hwdmediashare/libraries/uber/ubr_ini.php');
require_once(JPATH_ROOT.'/components/com_hwdmediashare/libraries/uber/ubr_lib.php');

// Load config file
require $DEFAULT_CONFIG;

//******************************************************************************************************
// The following possible query string formats are assumed
//
// 1. No query string
// 2. ?about=1
//******************************************************************************************************

if($DEBUG_PHP){ phpinfo(); exit(); }
elseif($DEBUG_CONFIG){ hwdvsDebug($_CONFIG['config_file_name'], $_CONFIG); exit(); }
elseif(isset($_GET['about']) && $_GET['about'] == 1){
	kak("<u><b>UBER UPLOADER FILE UPLOAD</b></u><br>UBER UPLOADER VERSION =  " . $UBER_VERSION . "<br>UBR_FILE_UPLOAD = " . $THIS_VERSION . "<br>\n", 1, __LINE__);
}

//******************************************************************************************************
//   Set custom head tags
//******************************************************************************************************

ob_start();
?>
    .bar1 {background-color:#FFFFFF; position:relative; text-align:left; height:24px; width:250px; border:1px solid #505050; border-radius:3px; -moz-border-radius:3px; -webkit-border-radius:3px;}
    .bar2 {background-color:#99CC00; position:relative; text-align:left; height:24px; width:0%; background-image:url('<?php echo JURI::root( true ); ?>/media/com_hwdmediashare/assets/images/ajaxupload/progress-bar/progress.gif');}
<?php
$html = ob_get_contents();
ob_end_clean();

$doc->addStyleDeclaration($html);
$doc->addScript($PATH_TO_JS_SCRIPT);

ob_start();
?>
    var path_to_link_script = "<?php print $PATH_TO_LINK_SCRIPT; ?>";
    var path_to_set_progress_script = "<?php print $PATH_TO_SET_PROGRESS_SCRIPT; ?>";
    var path_to_get_progress_script = "<?php print $PATH_TO_GET_PROGRESS_SCRIPT; ?>";
    var path_to_upload_script = "<?php print $PATH_TO_UPLOAD_SCRIPT; ?>";
    var multi_configs_enabled = <?php print $MULTI_CONFIGS_ENABLED; ?>;
    <?php if($MULTI_CONFIGS_ENABLED){ print "var config_file = \"$config_file\";\n"; } ?>
    var check_allow_extensions_on_client = <?php print $_CONFIG['check_allow_extensions_on_client']; ?>;
    var check_disallow_extensions_on_client = <?php print $_CONFIG['check_disallow_extensions_on_client']; ?>;
    <?php if($_CONFIG['check_allow_extensions_on_client']){ print "var allow_extensions = /" . $_CONFIG['allow_extensions'] . "$/i;\n"; } ?>
    <?php if($_CONFIG['check_disallow_extensions_on_client']){ print "var disallow_extensions = /" . $_CONFIG['disallow_extensions'] . "$/i;\n"; } ?>
    var check_file_name_format = <?php print $_CONFIG['check_file_name_format']; ?>;
    var check_null_file_count = <?php print $_CONFIG['check_null_file_count']; ?>;
    var check_duplicate_file_count = <?php print $_CONFIG['check_duplicate_file_count']; ?>;
    var max_upload_slots = <?php print $_CONFIG['max_upload_slots']; ?>;
    var cedric_progress_bar = <?php print $_CONFIG['cedric_progress_bar']; ?>;
    var progress_bar_width = <?php print $_CONFIG['progress_bar_width']; ?>;
    var show_percent_complete = <?php print $_CONFIG['show_percent_complete']; ?>;
    var show_files_uploaded = <?php print $_CONFIG['show_files_uploaded']; ?>;
    var show_current_position = <?php print $_CONFIG['show_current_position']; ?>;
    var show_elapsed_time = <?php print $_CONFIG['show_elapsed_time']; ?>;
    var show_est_time_left = <?php print $_CONFIG['show_est_time_left']; ?>;
    var show_est_speed = <?php print $_CONFIG['show_est_speed']; ?>;
<?php
$html = ob_get_contents();
ob_end_clean();

$doc->addScriptDeclaration($html);

ob_start();
?>
    <?php if($DEBUG_AJAX){ print "<br><div class=\"debug\" id=\"ubr_debug\"><b>AJAX DEBUG WINDOW</b><br></div><br>\n"; } ?>

    <?php if (JFactory::getApplication()->isAdmin()) : ?>
		<fieldset id="ubr_alert_container" class="adminform" style="display:none">
			<h3 id="ubr_alert"></h3>
		</fieldset>
    <?php else : ?>
		<div id="ubr_alert_container" style="display:none">
			<h3 id="ubr_alert"></h3>
		</div>
    <?php endif; ?>

    <!-- Start Progress Bar -->
    <div id="progress_bar" style="display:none">
    <fieldset class="adminform">
        <div class="bar1" id="upload_status_wrap">
                <div class="bar2" id="upload_status"></div>
        </div>
    </fieldset>
    <?php if($_CONFIG['show_percent_complete'] || $_CONFIG['show_files_uploaded'] || $_CONFIG['show_current_position'] || $_CONFIG['show_elapsed_time'] || $_CONFIG['show_est_time_left'] || $_CONFIG['show_est_speed']){ ?>
    <?php if (JFactory::getApplication()->isAdmin()) : ?>
        <div style="padding:10px;">
    <?php endif; ?>
        <table class="adminlist">
		<tbody>
                <?php if($_CONFIG['show_percent_complete']){ ?>
                <tr>
			<th scope="row">
			  <?php echo JText::_('COM_HWDMS_PERCENT_COMPLETE'); ?>
			</th>
			<td class="center">
			  <span id="percent">0%</span>
			</td>
                </tr>
                <?php } ?>
                <?php if($_CONFIG['show_files_uploaded']){ ?>
                <tr>
			<th scope="row">
			  <?php echo JText::_('COM_HWDMS_FILES_UPLOADED'); ?>
			</th>
			<td class="center">
			  <span id="uploaded_files">0</span> of <span id="total_uploads"></span>
			</td>
                </tr>
                <?php } ?>
                <?php if($_CONFIG['show_current_position']){
                // HWD Modification: changed name of ID to avoid conflicts ?>
                <tr>
			<th scope="row">
			  <?php echo JText::_('COM_HWDMS_CURRENT_POSITION'); ?>
			</th>
			<td class="center">
			  <span id="currentupld">0</span> / <span id="total_kbytes"></span> KBs
			</td>
                </tr>
                <?php } ?>

                <?php if($_CONFIG['show_current_position']){ ?>
                <tr>
			<th scope="row">
			  <?php echo JText::_('COM_HWDMS_ELAPSED_TIME'); ?>
			</th>
			<td class="center">
			  <span id="time">0</span>
			</td>
                </tr>
                <?php } ?>
                <?php if($_CONFIG['show_est_time_left']){ ?>
                <tr>
			<th scope="row">
			  <?php echo JText::_('COM_HWDMS_EST_TIME_LEFT'); ?>
			</th>
			<td class="center">
			  <span id="remain">0</span>
			</td>
                </tr>
                <?php } ?>
                <?php if($_CONFIG['show_est_time_left']){ ?>
                <tr>
			<th scope="row">
			  <?php echo JText::_('COM_HWDMS_EST_SPEED'); ?>
			</th>
			<td class="center">
			  <span id="speed">0</span> KB/s.
			</td>
                </tr>
                <?php } ?>
		</tbody>
	  </table>
    <?php if (JFactory::getApplication()->isAdmin()) : ?>
        </div>
    <?php endif; ?>
    <?php } ?>
    </div>
    <!-- End Progress Bar -->

    <?php if($_CONFIG['embedded_upload_results'] || $_CONFIG['opera_browser'] || $_CONFIG['safari_browser']){ ?>
    <div id="upload_div" style="display:none;"><iframe name="upload_iframe" frameborder="0" width="800" height="200" scrolling="auto"></iframe></div>
    <?php } ?>

    <?php if (JFactory::getApplication()->isAdmin()) : ?>
        <fieldset class="adminform">
            <ul class="panelform">
                <li>
                    <label><?php echo JText::_('COM_HWDMS_SUPPORTED_FORMATS_LABEL'); ?></label>
                    <span class="faux-label" style="clear:none;"><?php echo $largeExtensionReadable; ?></span>
                </li>
            </ul>
        </fieldset>
        <!-- Start Upload Form -->
        <fieldset class="adminform">
            <ul class="panelform">
                <noscript><p><?php echo JText::_('COM_HWDMS_PLEASE_ENABLE_JAVASCRIPT'); ?></p></noscript>
                <div id="upload_slots">
                    <li>
                        <label><?php echo JText::_('COM_HWDMS_UPLOAD_A_FILE'); ?></label>
                        <input type="file" name="upfile_0" <?php if($_CONFIG['multi_upload_slots']){ ?>onChange="addUploadSlot(1)"<?php } ?>  onkeypress="return handleKey(event)" value="">
                    </li>
                </div>
                <li>
                    <label></label>
                    <button type="button" id="upload_button" name="upload_button" value="Upload" onClick="linkUpload();">
                    <?php echo JText::_('COM_HWDMS_UPLOAD') ?>
                    </button>
                </li>
            </ul>
        </fieldset>
    <?php else : ?>
        <fieldset class="adminform">
            <div class="formelm">
                <label><?php echo JText::_('COM_HWDMS_SUPPORTED_FORMATS_LABEL'); ?></label>
                <span><?php echo $largeExtensionReadable; ?></span>
            </div>
        </fieldset>
        <fieldset class="adminform">
            <div id="upload_slots">
                <div class="formelm">
                    <label><?php echo JText::_('COM_HWDMS_UPLOAD_A_FILE'); ?></label>
                    <input type="file" name="upfile_0" <?php if($_CONFIG['multi_upload_slots']){ ?>onChange="addSiteUploadSlot(1)"<?php } ?>  onkeypress="return handleKey(event)" value="">
                </div>
            </div>
            <div class="formelm-buttons">
                <button type="button" id="upload_button" name="upload_button" value="Upload" onClick="linkUpload();">
                <?php echo JText::_('COM_HWDMS_UPLOAD') ?>
                </button>
            </div>
        </fieldset>
    <?php endif; ?>
    <!-- End Upload Form -->
    <div class="clr"></div>
<?php
$html = ob_get_contents();
ob_end_clean();

		return $html;
	}
        
	function getFolderTree($base = null)
	{
                $folder = JRequest::getVar('folder', '', '', 'path');
                $this->setState('folder', $folder);

		// Get some paths from the request
                if (empty($base)) {
			$base = JPATH_SITE.'/media';
		}

                // Define, cleanup and remove trailing slash
                $mediaBase = (defined('DS') ? str_replace(DS, '/', $base) : $base);
                $mediaBase = str_replace('\\', '/', $mediaBase);
                $mediaBase = rtrim($mediaBase, '/');

		// Get the list of folders
                // We don't want to recurse into component media folders. They can contain a lot of subdirectories 
                // and may use too much memory                
		jimport('joomla.filesystem.folder');
		$folders = JFolder::folders($base, '.', 4, true, array('.svn', 'CVS', '.DS_Store', '__MACOSX'), array('^\..*','^com_*','^plg_*'));

		$tree = array();

		foreach ($folders as $folder)
		{
                        // Define, cleanup and remove trailing slash
                        $folder         = (defined('DS') ? str_replace(DS, '/', $folder) : $folder);
                        $folder         = str_replace('\\', '/', $folder);
                        $folder         = rtrim($folder, '/');
			$name		= substr($folder, strrpos($folder, '/') + 1);
			$relative	= str_replace($mediaBase, '', $folder);
                        $relative       = ltrim($relative, '/');
			$absolute	= $folder;
			$path		= explode('/', $relative);
			$node		= (object) array('name' => $name, 'relative' => $relative, 'absolute' => $absolute);

			$tmp = &$tree;
			for ($i=0, $n=count($path); $i<$n; $i++)
			{
				if (!isset($tmp['children'])) {
					$tmp['children'] = array();
				}

				if ($i == $n-1) {
					// We need to place the node
					$tmp['children'][$relative] = array('data' =>$node, 'children' => array());
					break;
				}

				if (array_key_exists($key = implode('/', array_slice($path, 0, $i+1)), $tmp['children'])) {
					$tmp = &$tmp['children'][$key];
				}
			}
		}
		$tree['data'] = (object) array('name' => JText::_('COM_MEDIA_MEDIA'), 'relative' => '', 'absolute' => $base);

		return $tree;
	}
}
