<?php
/**
 * @package     Joomla.administrator
 * @subpackage  Component.hwdmediashare
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class hwdMediaShareModelAddMedia extends JModelAdmin
{
	/**
	 * Method to get a table object, and load it if necessary.
	 *
	 * @access  public
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 * @return  JTable  A JTable object
	 */
	public function getTable($name = 'Media', $prefix = 'hwdMediaShareTable', $config = array())
	{
		return JTable::getInstance($name, $prefix, $config);
	}
        
	/**
	 * Abstract method for getting the form from the model.
	 *
	 * @access  public
	 * @param   array       $data      Data for the form.
	 * @param   boolean     $loadData  True if the form is to load its own data (default case), false if not.
	 * @return  mixed       A JForm object on success, false on failure
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
	 * Method to get the data that should be injected in the form.
	 *
	 * @access  protected
         * @return  mixed       The data for the form.
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_hwdmediashare.edit.upload.data', array());

		if (empty($data))
		{
                        // Retrieve filtered jform data.
                        hwdMediaShareFactory::load('upload');
                        $data = hwdMediaShareUpload::getProcessedUploadData();                        
		}

		return $data;
	}

	/**
	 * Method to get a list of extensions allowed for local uploads, 
         * and also set state to access later.
	 *
	 * @access  public
         * @return  object  The object of extensions.
	 */
	public function getLocalExtensions()
	{
		hwdMediaShareFactory::load('upload');
                $extensions = hwdMediaShareUpload::getAllowedExtensions();
                $this->setState('allowedExtensions', $extensions);
                return $extensions;
	}

	/**
	 * Method to check if we should show the platform upload tool.
	 *
	 * @access  public
         * @return  boolean True if show, false if hide.
	 */
	public function getPlatformStatus()
	{
                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                            
                if ($config->get('audio_uploads') == 3 && $config->get('enable_audio')) return true;
                if ($config->get('document_uploads') == 3 && $config->get('enable_documents')) return true;
                if ($config->get('image_uploads') == 3 && $config->get('enable_images')) return true;
                if ($config->get('video_uploads') == 3 && $config->get('enable_videos')) return true;
                
                return false;
	}

	/**
	 * Method to load <head> assets for the fancy upload script.
	 *
	 * @access  public
         * @return  void
	 */
	public function getFancyUploadScript()
	{
                // Initialise variables.
		$app = JFactory::getApplication();
                $doc = JFactory::getDocument();
                
                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                // Load HWD libraries.
                hwdMediaShareFactory::load('upload');
                
                JHtml::_('behavior.framework');
                $document = JFactory::getDocument();
                $document->addScript(JURI::root(true) . "/media/com_hwdmediashare/assets/javascript/Swiff.Uploader.js");
                $document->addScript(JURI::root(true) . "/media/com_hwdmediashare/assets/javascript/Fx.ProgressBar.js");
                $document->addScript(JURI::root(true) . "/media/com_hwdmediashare/assets/javascript/FancyUpload2.js");
                $document->addStyleSheet(JURI::root(true) . "/media/com_hwdmediashare/assets/css/fancy.css");

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

                $flashUploadUrl = hwdMediaShareUpload::getFlashUploadURI();
                $swfUrl = JURI::root(true) . '/media/com_hwdmediashare/assets/swf/Swiff.Uploader.swf';
                $editTask = ($app->isAdmin() ? 'editmedia' : 'mediaform');
                $verbose = ($config->get('debug') ? 'true' : 'false');
                
                // Check if we need to limit to a single upload and redirect on success
                if ($config->get('upload_workflow', 1) == 0)
                {
                        $limitFiles = "1";
                        //$windowLocation = "window.location = 'index'+'.php?option=com_hwdmediashare&task=$editTask.edit&id='+json.get('id');"; 
                        $windowLocation = ($app->isAdmin() ? "window.location = 'index'+'.php?option=com_hwdmediashare&task=$editTask.edit&id='+json.get('id');" : "window.location = 'index'+'.php?option=com_hwdmediashare&view=mediaitem&id='+json.get('id');");
                }
                else
                {
                        $limitFiles = "1000";
                        $windowLocation = null;
                }
                
                $js = <<<EOD
//<![CDATA[
/**
 * FancyUpload2
 *
 * @license		MIT License
 * @author		Harald Kirschner <mail [at] digitarald [dot] de>
 */
window.addEvent('domready', function() { 
	var up = new FancyUpload2($('hwd-upload-status'), $('hwd-upload-list'), {
		// We console.log infos, remove that in production!!
		verbose: $verbose,

		url: '$flashUploadUrl',

		// path to the SWF file
		path: '$swfUrl',

		// Actionscript filefilter is causing problems in latest version of Flash. Possible
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
                limitFiles: $limitFiles,
                        
		// this is our browse button, *target* is overlayed with the Flash movie
		target: 'hwd-upload-browse',

		// graceful degradation, onLoad is only called if all went well with Flash
		onLoad: function() {
                        if ($('hwd-upload-status'))   $('hwd-upload-status').removeClass('hide');  // Display FancyUpload2 UI
                        if ($('hwd-upload-buttons'))  $('hwd-upload-buttons').removeClass('hide'); // Display FancyUpload2 UI
                        if ($('hwd-upload-fallback')) $('hwd-upload-fallback').destroy();          // Hide Fallback Form

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
                                                
                        $('hwd-upload-upload').removeAttribute('onclick');
		},

		// Edit the following lines, it is your custom event handling
                onBeforeStart: function() {
                    var listSize = this.fileList.length;
                    for (var i=0; i < listSize; i++){
                        // Pass the jform with the upload request
                        //alert(JSON.encode($('adminForm').toQueryString().parseQueryString()));
                        this.fileList[i].setOptions({data: $('adminForm').toQueryString()});
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
				$windowLocation
                                file.element.addClass('file-success');
				file.info.set('html', '<a href="index'+'.php?option=com_hwdmediashare&task=$editTask.edit&id='+json.get('id')+'" target="_top" class="btn">Edit</a>');
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
	}
        
	/**
	 * Method to load <head> assets and includes for the uber upload script.
	 *
	 * @access  public
         * @return  void
	 */
	public function getUberUploadScript()
	{
                // Initialise variables.
		$app = JFactory::getApplication();
                $doc = JFactory::getDocument();
                
                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                // Load HWD libraries.
                hwdMediaShareFactory::load('upload');

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

                // Load config file.
                require $DEFAULT_CONFIG;

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
                $js = ob_get_contents();
                ob_end_clean();

                $doc->addScriptDeclaration($js);
	}
        
	/**
	 * Method to get a folder tree.
	 *
	 * @access  public
         * @return  string  The folder tree structure.
	 */
	public function getFolderTree($base = null)
	{
                // Initialise variables.
		$app = JFactory::getApplication();
                
                // Load Joomla libraries.
                jimport('joomla.filesystem.folder');

                $folder = $app->input->get('folder', '', '', 'path');
                $this->setState('folder', $folder);

		// Get some paths from the request.
                if (empty($base))
                {
			$base = JPATH_SITE.'/media';
		}

                // Define, cleanup and remove trailing slash.
                $mediaBase = (defined('DS') ? str_replace(DS, '/', $base) : $base);
                $mediaBase = str_replace('\\', '/', $mediaBase);
                $mediaBase = rtrim($mediaBase, '/');

		// Get the list of folders (we don't want to recurse into component media 
                // folders. They can contain a lot of subdirectories and may use too much memory.
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
				if (!isset($tmp['children'])) 
                                {
					$tmp['children'] = array();
				}

				if ($i == $n-1) 
                                {
					// We need to place the node
					$tmp['children'][$relative] = array('data' =>$node, 'children' => array());
					break;
				}

				if (array_key_exists($key = implode('/', array_slice($path, 0, $i+1)), $tmp['children'])) 
                                {
					$tmp = &$tmp['children'][$key];
				}
			}
		}
                
		$tree['data'] = (object) array('name' => JText::_('COM_MEDIA_MEDIA'), 'relative' => '', 'absolute' => $base);

		return $tree;
	}
}
