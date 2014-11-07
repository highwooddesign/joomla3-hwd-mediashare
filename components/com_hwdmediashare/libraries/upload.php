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

class hwdMediaShareUpload extends JObject
{
	/**
	 * Holds the new item details.
         * 
         * @access  public
	 * @var     object
	 */
	public $_item;

	/**
	 * Class constructor.
	 *
	 * @access  public
	 * @param   mixed   $properties  Associative array to set the initial properties of the object.
         * @return  void
	 */
	public function __construct($properties = null)
	{
		parent::__construct($properties);
	}

	/**
	 * Returns the hwdMediaShareUpload object, only creating it if it
	 * doesn't already exist.
	 *
	 * @access  public
         * @static
	 * @return  hwdMediaShareUpload Object.
	 */ 
	public static function getInstance()
	{
		static $instance;

		if (!isset ($instance))
                {
			$c = 'hwdMediaShareUpload';
                        $instance = new $c;
		}

		return $instance;
	}

	/**
	 * Method to process a file upload.
         * 
         * @access  public
         * @param   object   $upload  Holds details about the upload field.
         * @return  boolean  True on success.
	 */
	public function process($upload)
	{
                // Initialise variables.            
                $db = JFactory::getDBO();
                $user = JFactory::getUser();
                $app = JFactory::getApplication();
                $date = JFactory::getDate();

                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
 
                // Load HWD utilities.
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();

                // Check authorised.
                if (!$user->authorise('hwdmediashare.upload', 'com_hwdmediashare'))
                {
                        $this->setError(JText::_('COM_HWDMS_ERROR_NOAUTHORISED'));
                        return false;
                }      

                // Retrieve filtered jform data.
                hwdMediaShareFactory::load('upload');
                $data = hwdMediaShareUpload::getProcessedUploadData();

                // Retrieve details of uploaded file, sent from upload form.
                // Check the jform control first, then no control.
                jimport( 'joomla.filesystem.file' );
                $files = $app->input->files->get('jform');
                if (isset($files[$upload->input]))
                {
                        $file = $files[$upload->input];
                }
                else
                {
                        $file = $app->input->files->get($upload->input);    
                }

                $ext = strtolower(JFile::getExt($file['name']));

                // Check if we are replacing an existing item.
                if (isset($data['id']) && $data['id'] > 0 && $app->isAdmin() && $user->authorise('core.edit', 'com_hwdmediashare'))
                {
                        $query = $db->getQuery(true)
                                ->select($db->quoteName('key'))
                                ->from('#__hwdms_media')
                                ->where('id = ' . $db->quote($data['id']));
                        try
                        {                
                                $db->setQuery($query);
                                $key = $db->loadResult();
                        }
                        catch (Exception $e)
                        {
                                $this->setError($e->getMessage());
                                return false;
                        }     
                }
                else
                {
                        if (!$key = $utilities->generateKey(1))
                        {
                                $this->setError($utilities->getError());
                                return false;
                        }                              
                }

                if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name']))
                {
                        $this->setError(JText::_('COM_HWDMS_ERROR_PHP_UPLOAD_ERROR'));
                        return false;
                }
                else
                {
                        hwdMediaShareFactory::load('files');
                        hwdMediaShareFiles::getLocalStoragePath();

                        $folders = hwdMediaShareFiles::getFolders($key);
                        hwdMediaShareFiles::setupFolders($folders);

                        // Get the filename.
                        $filename = hwdMediaShareFiles::getFilename($key, '1');

                        // Get the destination location.
                        $dest = hwdMediaShareFiles::getPath($folders, $filename, $ext);
                        
                        // Check the upload size.
                        $maxUploadFileSize = $config->get('max_upload_filesize', 30) * 1024 * 1024;                       
                        if (filesize($file['tmp_name']) > $maxUploadFileSize)
                        {
                                $this->setError(JText::sprintf('COM_HWDMS_FILE_N_EXCEEDS_THE_MAX_UPLOAD_LIMIT', $file['name']));
                                return false;                            
                        }
 
                        // Get allowed media types.
                        $media_types = array();
                        if ($config->get('enable_audio')) $media_types[] = 1;
                        if ($config->get('enable_documents')) $media_types[] = 2;
                        if ($config->get('enable_images')) $media_types[] = 3;
                        if ($config->get('enable_videos')) $media_types[] = 4;
    
                        // Check if the file has an allowed extension.
                        $query = $db->getQuery(true)
                                ->select('id')
                                ->from('#__hwdms_ext')
                                ->where($db->quoteName('ext') . ' = ' . $db->quote($ext))
                                ->where($db->quoteName('published') . ' = ' . $db->quote(1))
                                ->where($db->quoteName('media_type') . ' IN ('.implode(', ', $media_types).')');
                        $db->setQuery($query);
                        try
                        {
                                $db->execute(); 
                                $ext_id = $db->loadResult();                   
                        }
                        catch (RuntimeException $e)
                        {
                                $this->setError($e->getMessage());
                                return false;                            
                        }

                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                        $table = JTable::getInstance('Media', 'hwdMediaShareTable');
                        
                        if ($ext_id > 0)
                        {
                                // Check if we are replacing an existing item, and need to remove existing media.
                                if (isset($data['id']) && $data['id'] > 0 && $app->isAdmin() && $user->authorise('core.edit', 'com_hwdmediashare'))
                                {
                                        // Attempt to load the existing table row.
                                        $return = $table->load($data['id']);

                                        // Check for a table object error.
                                        if ($return === false && $table->getError())
                                        {
                                                $this->setError($table->getError());
                                                return false;
                                        }

                                        $properties = $table->getProperties(1);
                                        $replace = JArrayHelper::toObject($properties, 'JObject');

                                        // Here, we need to remove all files already associated with this media item.
                                        hwdMediaShareFactory::load('files');
                                        $HWDfiles = hwdMediaShareFiles::getInstance();
                                        $HWDfiles->deleteMediaFiles($replace);
                                }
                                
                                if (!JFile::upload($file['tmp_name'], $dest))
                                {
                                        $this->setError(JText::_('COM_HWDMS_ERROR_FILE_COULD_NOT_BE_COPIED_TO_UPLOAD_DIRECTORY'));
                                        return false;
                                }
                        }
                        else
                        {
                                $this->setError(JText::_('COM_HWDMS_ERROR_EXTENSION_NOT_ALLOWED'));
                                return false;
                        }

                        // Set approved/pending.
                        (!$app->isAdmin() && $config->get('approve_new_media')) == 1 ? $status = 2 : $status = 1; 
                        $config->get('approve_new_media') == 1 ? $status = 2 : $status = 1; 

                        $post = array();

                        // Check if we need to replace an existing media item.
                        if (isset($data['id']) && $data['id'] > 0 && $app->isAdmin() && $user->authorise('core.edit', 'com_hwdmediashare'))
                        {
                                //$post['id']                   = '';
                                //$post['asset_id']             = '';
                                $post['ext_id']                 = $ext_id;
                                $post['media_type']             = '';
                                $post['key']                    = $key;
                                //$post['title']                = '';
                                //$post['alias']                = '';
                                //$post['description']          = '';
                                $post['type']                   = 1; // Local
                                $post['source']                 = '';
                                $post['storage']                = '';
                                //$post['duration']             = '';
                                $post['streamer']               = '';
                                $post['file']                   = '';
                                $post['embed_code']             = '';
                                $post['thumbnail']              = '';
                                //$post['thumbnail_ext_id']     = '';
                                //$post['location']             = '';
                                //$post['viewed']               = '';
                                //$post['private']              = '';
                                //$post['likes']                = '';
                                //$post['dislikes']             = '';
                                //$post['status']               = '';
                                //$post['published']            = '';
                                //$post['featured']             = '';
                                //$post['checked_out']          = '';
                                //$post['checked_out_time']     = '';
                                //$post['access']               = '';
                                //$post['download']             = '';
                                //$post['params']               = '';
                                //$post['ordering']             = '';
                                //$post['created_user_id']      = '';
                                //$post['created_user_id_alias']= '';
                                //$post['created']              = '';
                                //$post['publish_up']           = '';
                                //$post['publish_down']         = '';
                                $post['modified_user_id']       = $user->id;
                                $post['modified']               = $date->toSql();
                                //$post['hits']                 = '';
                                //$post['language']             = '';              
                        }
                        else
                        {
                                //$post['id']                   = '';
                                //$post['asset_id']             = '';
                                $post['ext_id']                 = $ext_id;
                                $post['media_type']             = '';
                                $post['key']                    = $key;
                                $post['title']                  = (isset($data['title']) ? $data['title'] : hwdMediaShareUpload::removeExtension($file['name']));
                                $post['alias']                  = (isset($data['alias']) ? JFilterOutput::stringURLSafe($data['alias']) : JFilterOutput::stringURLSafe($post['title']));
                                $post['description']            = (isset($data['description']) ? $data['description'] : '');
                                $post['type']                   = 1; // Local
                                $post['source']                 = '';
                                $post['storage']                = '';
                                //$post['duration']             = '';
                                $post['streamer']               = '';
                                $post['file']                   = '';
                                $post['embed_code']             = '';
                                //$post['thumbnail']            = '';
                                //$post['thumbnail_ext_id']     = '';
                                //$post['location']             = '';
                                //$post['viewed']               = '';
                                //$post['private']              = '';
                                //$post['likes']                = '';
                                //$post['dislikes']             = '';
                                $post['status']                 = $status;
                                $post['published']              = (isset($data['published']) ? $data['published'] : 1);
                                $post['featured']               = (isset($data['featured']) ? $data['featured'] : 0);
                                //$post['checked_out']          = '';
                                //$post['checked_out_time']     = '';
                                $post['access']                 = (isset($data['access']) ? $data['access'] : 1);
                                //$post['download']             = '';
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
                                $post['language']               = (isset($data['language']) ? $data['language'] : '*');
                        }

                        // Save data to the database.
                        if (!$table->save($post))
                        {
                                $this->setError($table->getError());
                                return false;
                        }  
                        
                        $properties = $table->getProperties(1);
                        $this->_item = JArrayHelper::toObject($properties, 'JObject');

                        hwdMediaShareFactory::load('files');
                        $HWDfiles = hwdMediaShareFiles::getInstance();
                        $HWDfiles->addFile($this->_item, 1);

                        hwdMediaShareUpload::addProcesses($this->_item);
		}

                return true;
        }

	/**
	 * Method to process an uber upload.
         * 
         * @access  public
         * @return  boolean  True on success.
	 */
	public function uber()
	{
                // Initialise variables.
                $db = JFactory::getDBO();
                $user = JFactory::getUser();
                $app = JFactory::getApplication();
                $date = JFactory::getDate();

                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // Load HWD utilities.
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                
                // Check authorised.
                if (!$user->authorise('hwdmediashare.upload', 'com_hwdmediashare'))
                {
                        $this->setError(JText::_('COM_HWDMS_ERROR_NOAUTHORISED'));
                        return false;
                } 

//******************************************************************************************************
//   ATTENTION: THIS FILE HEADER MUST REMAIN INTACT. DO NOT DELETE OR MODIFY THIS FILE HEADER.
//
//   Name: ubr_finished.php
//   Revision: 1.3
//   Date: 2/18/2008 5:36:57 PM
//   Link: http://uber-uploader.sourceforge.net
//   Initial Developer: Peter Schmandra  http://www.webdice.org
//   Description: Show successful file uploads.
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
//***************************************************************************************************************

//***************************************************************************************************************
// The following possible query string formats are assumed
//
// 1. ?upload_id=upload_id
// 2. ?about=1
//****************************************************************************************************************

$THIS_VERSION = "1.3";                                // Version of this file
$UPLOAD_ID = '';                                      // Initialize upload id

require_once(JPATH_ROOT.'/components/com_hwdmediashare/libraries/uber/ubr_ini.php');
require_once(JPATH_ROOT.'/components/com_hwdmediashare/libraries/uber/ubr_lib.php');
require_once(JPATH_ROOT.'/components/com_hwdmediashare/libraries/uber/ubr_finished_lib.php');

if(preg_match("/^[a-zA-Z0-9]{32}$/", $_GET['upload_id'])){ $UPLOAD_ID = $_GET['upload_id'];}
elseif(isset($_GET['about']) && $_GET['about'] == 1){ kak("<u><b>UBER UPLOADER FINISHED PAGE</b></u><br>UBER UPLOADER VERSION =  <b>" . $UBER_VERSION . "</b><br>UBR_FINISHED = <b>" . $THIS_VERSION . "<b><br>\n", 1 , __LINE__);}
else{kak("ERROR: Invalid parameters passed<br>", 1, __LINE__);}

// Declare local values.
$_XML_DATA = array();                                          // Array of xml data read from the upload_id.redirect file
$_CONFIG_DATA = array();                                       // Array of config data read from the $_XML_DATA array
$_POST_DATA = array();                                         // Array of posted data read from the $_XML_DATA array
$_FILE_DATA = array();                                         // Array of 'FileInfo' objects read from the $_XML_DATA array
$_FILE_DATA_TABLE = '';                                        // String used to store file info results nested between <tr> tags
$_FILE_DATA_EMAIL = '';                                        // String used to store file info results

$xml_parser = new XML_Parser;                                  // XML parser
$xml_parser->setXMLFile($TEMP_DIR, $_REQUEST['upload_id']);    // Set upload_id.redirect file
$xml_parser->setXMLFileDelete($DELETE_REDIRECT_FILE);          // Delete upload_id.redirect file when finished parsing
$xml_parser->parseFeed();                                      // Parse upload_id.redirect file

// Display message if the XML parser encountered an error
if($xml_parser->getError()){ kak($xml_parser->getErrorMsg(), 1, __LINE__); }

$_XML_DATA = $xml_parser->getXMLData();                        // Get xml data from the xml parser
$_CONFIG_DATA = getConfigData($_XML_DATA);                     // Get config data from the xml data
$_POST_DATA  = getPostData($_XML_DATA);                        // Get post data from the xml data
$_FILE_DATA = getFileData($_XML_DATA);                         // Get file data from the xml data

/////////////////////////////////////////////////////////////////////////////////////////////////
// NOTE: You can now access all XML values below this comment. eg.
//   $_XML_DATA['upload_dir']; or $_XML_DATA['link_to_upload'] etc
/////////////////////////////////////////////////////////////////////////////////////////////////
// NOTE: You can now access all config values below this comment. eg.
//   $_CONFIG_DATA['upload_dir']; or $_CONFIG_DATA['link_to_upload'] etc
/////////////////////////////////////////////////////////////////////////////////////////////////
// NOTE: You can now access all post values below this comment. eg.
//   $_POST_DATA['client_id']; or $_POST_DATA['check_box_1_'] etc
/////////////////////////////////////////////////////////////////////////////////////////////////
// NOTE: You can now access all file (slot, name, size, type) info below this comment. eg.
//   $_FILE_DATA[0]->name  or  $_FILE_DATA[0]->getFileInfo('name')
/////////////////////////////////////////////////////////////////////////////////////////////////

                // We want to run this data through the Joomla filters, so we 
                // need to set the $_POST associative array.
                $_REQUEST['id'] = $_POST_DATA['jform_id_'];
                $_REQUEST['title'] = $_POST_DATA['jform_title_'];
                $_REQUEST['alias'] = $_POST_DATA['jform_alias_'];
                $_REQUEST['description'] = $_POST_DATA['jform_description_'];
                $_REQUEST['catid'] = array($_POST_DATA['jform_catid___']);
                $_REQUEST['tags'] = array($_POST_DATA['jform_tags___']);
                $_REQUEST['category_id'] = $_POST_DATA['jform_category_id_'];
                $_REQUEST['album_id'] = $_POST_DATA['jform_album_id_'];
                $_REQUEST['playlist_id'] = $_POST_DATA['jform_playlist_id_'];
                $_REQUEST['group_id'] = $_POST_DATA['jform_group_id_'];

                // Get associations from ubr_upload and assign them to the jform array
                $data = array();
                $data['title'] = $app->input->get('title', '', 'string');
                $data['alias'] = $app->input->get('alias', '', 'string');
                $data['description'] = $app->input->get('description', '', 'safe_html');
                $data['catid'] = $app->input->get('catid', array(), 'array');
                $data['tags'] = $app->input->get('tags', array(), 'array');
                $data['category_id'] = $app->input->get('category_id', '', 'int');
                $data['album_id'] = $app->input->get('album_id', '', 'int');
                $data['playlist_id'] = $app->input->get('playlist_id', '', 'int');
                $data['group_id'] = $app->input->get('group_id', '', 'int');

                $app->input->set('jform', $data);

                foreach($_FILE_DATA as $arrayKey => $slot)
                {
                        // Retrieve file details from uploaded file, sent from upload form.
                        jimport('joomla.filesystem.file');
                        $ext = strtolower(JFile::getExt($slot->name));
                        
			// Check if we need to replace an existing media item.
			if (isset($data['id']) && $data['id'] > 0 && $app->isAdmin() && $user->authorise('core.edit', 'com_hwdmediashare'))
			{
                                $query = $db->getQuery(true)
                                        ->select($db->quoteName('key'))
                                        ->from('#__hwdms_media')
                                        ->where('id = ' . $db->quote($data['id']));
                                try
                                {                
                                        $db->setQuery($query);
                                        $key = $db->loadResult();
                                }
                                catch (Exception $e)
                                {
                                        $this->setError($e->getMessage());
                                        return false;
                                }    
			}
			else
			{
				if (!$key = $utilities->generateKey(1))
				{
					$this->setError($utilities->getError());
					return false;
				}                        
			}
                        
                        // Define the local storage directory location.
                        hwdMediaShareFactory::load('files');
                        hwdMediaShareFiles::getLocalStoragePath();
  
                        // This is the temporary location of the uploaded file.
                        $src = HWDMS_PATH_MEDIA_FILES.'/'.$slot->name;
                        
                        if (!isset($slot->name) || !file_exists($src))
                        {
                                $this->setError(JText::_('COM_HWDMS_ERROR_UPLOAD_ERROR'));
                                return false;
                        }
                        else
                        {
                                hwdMediaShareFactory::load('files');
                                hwdMediaShareFiles::getLocalStoragePath();
                            
                                $folders = hwdMediaShareFiles::getFolders($key);
                                hwdMediaShareFiles::setupFolders($folders);

                                // Get the filename.
                                $filename = hwdMediaShareFiles::getFilename($key, '1');

                                // Get the destination location.
                                $dest = hwdMediaShareFiles::getPath($folders, $filename, $ext);

                                // Check the upload size.
                                $maxUploadFileSize = $config->get('max_upload_filesize', 30) * 1024 * 1024;                       
                                if (filesize($src) > $maxUploadFileSize)
                                {
                                        $this->setError(JText::sprintf('COM_HWDMS_FILE_N_EXCEEDS_THE_MAX_UPLOAD_LIMIT', $file['name']));
                                        return false;                            
                                }
                        
				// Get allowed media types.
				$media_types = array();
				if ($config->get('enable_audio')) $media_types[] = 1;
				if ($config->get('enable_documents')) $media_types[] = 2;
				if ($config->get('enable_images')) $media_types[] = 3;
				if ($config->get('enable_videos')) $media_types[] = 4;

				// Check if the file has an allowed extension.
				$query = $db->getQuery(true)
					->select('id')
					->from('#__hwdms_ext')
					->where($db->quoteName('ext') . ' = ' . $db->quote($ext))
					->where($db->quoteName('published') . ' = ' . $db->quote(1))
					->where($db->quoteName('media_type') . ' IN ('.implode(', ', $media_types).')');
				$db->setQuery($query);
				try
				{
					$db->execute(); 
					$ext_id = $db->loadResult();                   
				}
				catch (RuntimeException $e)
				{
					$this->setError($e->getMessage());
					return false;                            
				}

                                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                                $table = JTable::getInstance('Media', 'hwdMediaShareTable');
                                
                                if ($ext_id > 0)
                                {
					// Check if we need to replace an existing media item.
					if (isset($data['id']) && $data['id'] > 0 && $app->isAdmin() && $user->authorise('core.edit', 'com_hwdmediashare'))
					{
                                                // Attempt to load the existing table row.
                                                $return = $table->load($data['id']);

                                                // Check for a table object error.
                                                if ($return === false && $table->getError())
                                                {
                                                        $this->setError($table->getError());
                                                        return false;
                                                }

                                                $properties = $table->getProperties(1);
                                                $replace = JArrayHelper::toObject($properties, 'JObject');

                                                // Here, we need to remove all files already associated with this media item
                                                hwdMediaShareFactory::load('files');
                                                $HWDfiles = hwdMediaShareFiles::getInstance();
                                                $HWDfiles->deleteMediaFiles($replace);
					}

                                        if (!JFile::move($src, $dest))
                                        {
                                                $this->setError(JText::_('COM_HWDMS_ERROR_FILE_COULD_NOT_BE_COPIED_TO_UPLOAD_DIRECTORY'));
                                                return false;
                                        }
                                }
                                else
                                {
                                        $this->setError(JText::_('COM_HWDMS_ERROR_EXTENSION_NOT_ALLOWED'));
                                        return false;
                                }

                                // Set approved/pending.
                                (!$app->isAdmin() && $config->get('approve_new_media')) == 1 ? $status = 2 : $status = 1; 
                                $config->get('approve_new_media') == 1 ? $status = 2 : $status = 1; 

                                $post = array();

                                // Check if we need to replace an existing media item.
                                if (isset($data['id']) && $data['id'] > 0 && $app->isAdmin() && $user->authorise('core.edit', 'com_hwdmediashare'))
                                {
                                        //$post['id']                   = '';
                                        //$post['asset_id']             = '';
                                        $post['ext_id']                 = $ext_id;
                                        //$post['media_type']           = '';
                                        $post['key']                    = $key;
                                        //$post['title']                = '';
                                        //$post['alias']                = '';
                                        //$post['description']          = '';
                                        $post['type']                   = 1; // Local
                                        $post['source']                 = '';
                                        $post['storage']                = '';
                                        //$post['duration']             = '';
                                        $post['streamer']               = '';
                                        $post['file']                   = '';
                                        $post['embed_code']             = '';
                                        $post['thumbnail']              = '';
                                        //$post['thumbnail_ext_id']     = '';
                                        //$post['location']             = '';
                                        //$post['viewed']               = '';
                                        //$post['private']              = '';
                                        //$post['likes']                = '';
                                        //$post['dislikes']             = '';
                                        //$post['status']               = '';
                                        //$post['published']            = '';
                                        //$post['featured']             = '';
                                        //$post['checked_out']          = '';
                                        //$post['checked_out_time']     = '';
                                        //$post['access']               = '';
                                        //$post['download']             = '';
                                        //$post['params']               = '';
                                        //$post['ordering']             = '';
                                        //$post['created_user_id']      = '';
                                        //$post['created_user_id_alias']= '';
                                        //$post['created']              = '';
                                        //$post['publish_up']           = '';
                                        //$post['publish_down']         = '';
                                        $post['modified_user_id']       = $user->id;
                                        $post['modified']               = $date->toSql();
                                        //$post['hits']                 = '';
                                        //$post['language']             = '';              
                                }
                                else
                                {
                                        //$post['id']                   = '';
                                        //$post['asset_id']             = '';
                                        $post['ext_id']                 = $ext_id;
                                        $post['media_type']             = '';
                                        $post['key']                    = $key;
                                        $post['title']                  = (isset($data['title']) ? $data['title'] : hwdMediaShareUpload::removeExtension($_POST_DATA[$slot->slot]));
                                        $post['alias']                  = (isset($data['alias']) ? JFilterOutput::stringURLSafe($data['alias']) : JFilterOutput::stringURLSafe($post['title']));
                                        $post['description']            = (isset($data['description']) ? $data['description'] : '');
                                        $post['type']                   = 1; // Local
                                        $post['source']                 = '';
                                        $post['storage']                = '';
                                        //$post['duration']             = '';
                                        $post['streamer']               = '';
                                        $post['file']                   = '';
                                        $post['embed_code']             = '';
                                        //$post['thumbnail']            = '';
                                        //$post['thumbnail_ext_id']     = '';
                                        //$post['location']             = '';
                                        //$post['viewed']               = '';
                                        //$post['private']              = '';
                                        //$post['likes']                = '';
                                        //$post['dislikes']             = '';
                                        $post['status']                 = $status;
                                        $post['published']              = (isset($data['published']) ? $data['published'] : 1);
                                        $post['featured']               = (isset($data['featured']) ? $data['featured'] : 0);
                                        //$post['checked_out']          = '';
                                        //$post['checked_out_time']     = '';
                                        $post['access']                 = (isset($data['access']) ? $data['access'] : 1);
                                        $post['download']               = 1;
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
                                        $post['language']               = (isset($data['language']) ? $data['language'] : '*');
                                }

                                // Save data to the database.
                                if (!$table->save($post))
                                {
                                        $this->setError($table->getError());
                                        return false;
                                }  

                                $properties = $table->getProperties(1);
                                $this->_item = JArrayHelper::toObject($properties, 'JObject');

                                hwdMediaShareFactory::load('files');
                                $HWDfiles = hwdMediaShareFiles::getInstance();
                                $HWDfiles->addFile($this->_item, 1);

                                hwdMediaShareUpload::addProcesses($this->_item);  
                        }
                }
                
                return true;
        }

	/**
	 * Method to process a custom thumbnail upload.
         * 
         * @access  public
         * @param   object   $item  The item which the thumbnail belongs to.
         * @return  boolean  True on success.
	 */
	public function processThumbnail($item)
	{
                // Initialise variables.            
                $db = JFactory::getDBO();
                $user = JFactory::getUser();
                $app = JFactory::getApplication();
                $date = JFactory::getDate();

                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                // Load libraries.
                jimport('joomla.filesystem.file');
                hwdMediaShareFactory::load('files');
                hwdMediaShareFiles::getLocalStoragePath();

                // Check authorised.
                if (!$user->authorise('hwdmediashare.upload', 'com_hwdmediashare'))
                {
                        $this->setError(JText::_('COM_HWDMS_ERROR_NOAUTHORISED'));
                        return false;
                }      

                // Retrieve filtered jform data.
                hwdMediaShareFactory::load('upload');
                $data = hwdMediaShareUpload::getProcessedUploadData();

                // Retrieve details of uploaded file, sent from upload form.
                // Check the jform control first, then no control.
                $files = $app->input->files->get('jform');
                if (isset($files['thumbnail']))
                {
                        $file = $files['thumbnail'];
                }
                else
                {
                        $file = $app->input->files->get('thumbnail');    
                }

                // Remove current thumbnail if requested or new thumbnail is attached to form.
                if (isset($file) && is_uploaded_file($file['tmp_name']) || $data['remove_thumbnail'])
                {
                        $HWDfiles = hwdMediaShareFiles::getInstance();
                        $HWDfiles->elementType = $this->elementType;
                        $HWDfiles->removeFile($item, 10);                     
                }

                // Process thumbnail image.
                if (isset($file) && is_uploaded_file($file['tmp_name']))
                {
                        // Get the extension of the thumbnail image.
                        $ext = strtolower(JFile::getExt($file['name']));

                        // First check if the thumbnail image is an allowed image format.
                        // Check if the file has an allowed extension
                        $query = $db->getQuery(true)
                                ->select('id')
                                ->from('#__hwdms_ext')
                                ->where($db->quoteName('ext') . ' = ' . $db->quote($ext))
                                ->where($db->quoteName('published') . ' = ' . $db->quote(1))
                                ->where($db->quoteName('media_type') . ' = '.$db->quote(3));
                        $db->setQuery($query);
                        try
                        {
                                $db->execute(); 
                                $ext_id = $db->loadResult();                   
                        }
                        catch (RuntimeException $e)
                        {
                                $this->setError($e->getMessage());
                                return false;                            
                        }

                        if ($ext_id > 0)
                        {
                                // Define and setup folders.     
                                $folders = hwdMediaShareFiles::getFolders($item->key);
                                hwdMediaShareFiles::setupFolders($folders);

                                // Get hashed filename.
                                $filename = hwdMediaShareFiles::getFilename($item->key, '10');

                                // Define source and destination.
                                $src = $file['tmp_name'];
                                $dest = hwdMediaShareFiles::getPath($folders, $filename, $ext);

                                if (JFile::upload($src, $dest))
                                {
                                        // Attempt resize on the fly.
                                        hwdMediaShareFactory::load('images');
                                        $HWDimages = hwdMediaShareImages::getInstance();
                                        $HWDimages->resizeImage($dest, 500); 
                                    
                                        // Create an object for updating the record.
                                        $object = new stdClass();
                                        $object->id = $item->id;
                                        $object->thumbnail_ext_id = $ext_id;
                                }
                                else
                                {
                                        $this->setError(JText::_('COM_HWDMS_ERROR_FILE_COULD_NOT_BE_COPIED_TO_UPLOAD_DIRECTORY'));
                                        return false;
                                }
                        }
                        else
                        {
                                $this->setError(JText::_('COM_HWDMS_ERROR_EXTENSION_NOT_ALLOWED'));
                                return false;
                        }
                        
                        // Add file record.
                        $HWDfiles = hwdMediaShareFiles::getInstance();
                        $HWDfiles->elementType = $this->elementType;
                        $HWDfiles->addFile($item, 10);
                }
                elseif (isset($data['thumbnail_remote']) && !empty($data['thumbnail_remote']))
                {
                        // Create an object for updating the record.
                        $object = new stdClass();
                        $object->id = $item->id;
                        $object->thumbnail = $data['thumbnail_remote'];
                }

                if (isset($object) && is_object($object))
                {
                        try
                        {            
                                // Update record in database.
                                switch($this->elementType)
                                {
                                        case 1:
                                                $result = $db->updateObject('#__hwdms_media', $object, 'id');
                                        break;
                                        case 2:
                                                $result = $db->updateObject('#__hwdms_albums', $object, 'id');
                                        break;
                                        case 3:
                                                $result = $db->updateObject('#__hwdms_groups', $object, 'id');
                                        break;
                                        case 4:
                                                $result = $db->updateObject('#__hwdms_playlists', $object, 'id');
                                        break;
                                        case 5:
                                                $result = $db->updateObject('#__hwdms_users', $object, 'id');
                                        break;                            
                                }
                        }
                        catch (Exception $e)
                        {
                                $this->setError($e->getMessage());
                                return false;
                        }
                }
                
                return true;
        }

	/**
	 * Method to generate the destination of an upload.
         * 
         * @access  public
         * @param   array   $folders   The three element array holding the subdirectories.
         * @param   string  $filename  The basename of the destination file.
         * @return  string  The full server path to the destination.
	 */
        public function dest($folders, $filename)
        {
                return HWDMS_PATH_MEDIA_FILES . '/' . $folders[1] . '/' . $folders[2] . '/' . $folders[3] . '/' . $filename;
        }

	/**
	 * Method to remove an extention from a filename.
         * 
         * @access  public
         * @param   string  $filename  The filename.
         * @return  string  The filename without extension.
	 */
        public function removeExtension($filename)
        {
                $ext = strrchr($filename, '.');
                if($ext !== false)
                {
                       $filename = substr($filename, 0, -strlen($ext));
                }
                return $filename;
        }

	/**
	 * Method to add a user upload session token to database.
         * 
         * @access  public
         * @static
         * @param   string  $token  The session token.
         * @return  void
	 */
        public static function addUserUploadSession($token)
	{
                $date = JFactory::getDate();
                $user = JFactory::getUser();

                $object = new stdClass;
                $object->userid = $user->id;
                $object->token = $token;
                $object->datetime = $date->toSql();

                $db = JFactory::getDBO();
		$db->insertObject('#__hwdms_upload_tokens', $object);
	}

	/**
	 * Method to generate the upload uri for FancyUpload2.
         * 
         * @access  public
         * @static
         * @return  string  The upload uri.
	 */
        public static function getFlashUploadURI()
	{
                $session = JFactory::getSession();
                $user = JFactory::getUser();

                // Generate a session handler for this user.
                $token	= $session->getToken(true);

                $url	= JURI::root(true) . '/index.php?option=com_hwdmediashare&task=addmedia.upload&format=raw';
                $url   .= '&' . $session->getName() . '=' . $session->getId() . '&token=' . $token . '&uploaderid=' . $user->id;

                hwdMediaShareUpload::addUserUploadSession($token);

                return $url;
	}

        /**
	 * Method to get maximum upload size (in MB) for specified upload method.
         * 
         * @access  public
         * @static
         * @param   string   $method  The upload method (standard|large|platform).
         * @return  integer  The maximum upload size (in MB)
	 */
        public static function getMaximumUploadSize($method = null)
	{
                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                $media_types = array();

                switch ($method) 
                {
                        case 'standard':                        
                                $maxUpload = (int)$config->get('max_upload_filesize', 30);        
                                $maxPhpUpload = min((int)ini_get('post_max_size'), (int)ini_get('upload_max_filesize'));
                                $max = min($maxUpload, $maxPhpUpload);
                                return $max;
                        break;
                        case 'large':   
                        default:
                                $maxUpload = (int)$config->get('max_upload_filesize', 30);        
                                $maxPerlUpload = 2000;
                                $max = min($maxUpload, $maxPerlUpload);
                                return $max;
                        break;
                }      
        }
        
        /**
	 * Method to get all allowed extensions.
         * 
         * @access  public
         * @static
         * @return  array  An array of allowed file extensions.
	 */
        public static function getAllowedExtensions()
	{
                // Initialise variables.
                $db = JFactory::getDBO();

                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // Define array of allowed media types.
                $media_types = array();
                            
                if ($config->get('enable_audio')) $media_types[] = 1;
                if ($config->get('enable_documents')) $media_types[] = 2;
                if ($config->get('enable_images')) $media_types[] = 3;
                if ($config->get('enable_videos')) $media_types[] = 4;

		if (count($media_types))
                {
                        $query = $db->getQuery(true)
                                ->select('ext')
                                ->from('#__hwdms_ext')
                                ->where('media_type IN (' . implode(',', $media_types) . ')')
                                ->where('published = ' . $db->quote(1));
                        try
                        {                
                                $db->setQuery($query);
                                $rows = $db->loadColumn();
                        }
                        catch (Exception $e)
                        {
                                $this->setError($e->getMessage());
                                return false;
                        }

                        return $rows;
                }
                
                return false;
	}

	/**
	 * Method to associate the upload with tags and other elements.
         * 
         * @access  public
         * @static
         * @param   object  $media  The media object.
         * @return  void
	 */
	public static function assignAssociations($media)
	{
                // Retrieve filtered jform data.
                hwdMediaShareFactory::load('upload');
                $data = hwdMediaShareUpload::getProcessedUploadData();

                // Assign tags.
                if (isset($data['tags']))
                {
                        $tags = (array) $data['tags'];

                        // Sanitise tag array.
                        jimport('joomla.utilities.arrayhelper');
			JArrayHelper::toInteger($tags);

                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                        $table = JTable::getInstance('Media', 'hwdMediaShareTable');
                        $table->reset();
                        $table->load($media->id);

                        $tagsObserver = $table->getObserverOfClass('JTableObserverTags');
                        $result = $tagsObserver->setNewTags($tags, false);

                        if (!$result)
                        {
                                $this->setError($table->getError());
                                return false;
                        }
                }
                
                // Assign album.
                if (isset($data['album_id']))
                {
                        if ($data['album_id'] > 0)
                        {
                                $pks = array((int) $media->id);
                                $albumId = (int) $data['album_id'];
                                JModelLegacy::addIncludePath(JPATH_ROOT.'/administrator/components/com_hwdmediashare/models');
                                $model = JModelLegacy::getInstance('AlbumMediaItem', 'hwdMediaShareModel', array('ignore_request' => true));
                                $model->link($pks, $albumId);
                        }
                }
                
                // Assign category.               
                if (isset($data['category_id']))
                {
                        if ($data['category_id'] > 0)
                        {            
                                $catid = array((int) $data['category_id']);

                                hwdMediaShareFactory::load('category');
                                $HWDcategory = hwdMediaShareCategory::getInstance();
                                $HWDcategory->save($catid, $media->id);
                        }
                }
                
                // Assign group.
                if (isset($data['group_id']))
                {
                        if ($data['group_id'] > 0)
                        {
                                $pks = array((int) $media->id);
                                $groupId = (int) $data['group_id'];
                                JModelLegacy::addIncludePath(JPATH_ROOT.'/administrator/components/com_hwdmediashare/models');
                                $model = JModelLegacy::getInstance('GroupMediaItem', 'hwdMediaShareModel', array('ignore_request' => true));
                                $model->link($pks, $groupId);
                        }
                }           
                
                // Assign playlist.
                if (isset($data['playlist_id']))
                {
                        if ($data['playlist_id'] > 0)
                        {
                                $pks = array((int) $media->id);
                                $playlistId = (int) $data['playlist_id'];
                                JModelLegacy::addIncludePath(JPATH_ROOT.'/administrator/components/com_hwdmediashare/models');
                                $model = JModelLegacy::getInstance('PlaylistMediaItem', 'hwdMediaShareModel', array('ignore_request' => true));
                                $model->link($pks, $playlistId);
                        }
                }
	}

	/**
	 * Method to add relavent processes for uploads.
         * 
         * @access  public
         * @static
         * @param   object  $media  The media object.
         * @return  void.
	 */
	public static function addProcesses($media)
	{
                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // Check processing is enabled.
                if ($config->get('process') == 0) return true;
                        
                $processes = array();

                // Load media library.
                hwdMediaShareFactory::load('media');

                // Load processes library.
                hwdMediaShareFactory::load('processes');
                $HWDprocesses = hwdMediaShareProcesses::getInstance();
                                
                // Get the media type.
                $type = hwdMediaShareMedia::loadMediaType($media);
                
                if ($type == 1)
                {
                        // Audio
                        $config->get('process_jpeg_75') == 1 ? $processes[] = $HWDprocesses->addProcess($media,'1') : null;
                        $config->get('process_jpeg_100') == 1 ? $processes[] = $HWDprocesses->addProcess($media,'2') : null;
                        $config->get('process_jpeg_240') == 1 ? $processes[] = $HWDprocesses->addProcess($media,'3') : null;
                        $config->get('process_jpeg_500') == 1 ? $processes[] = $HWDprocesses->addProcess($media,'4') : null;
                        $config->get('process_jpeg_640') == 1 ? $processes[] = $HWDprocesses->addProcess($media,'5') : null;
                        $config->get('process_jpeg_1024') == 1 ? $processes[] = $HWDprocesses->addProcess($media,'6') : null;

                        $config->get('process_audio_mp3') == 1 ? $processes[] = $HWDprocesses->addProcess($media,'7') : null;
                        $config->get('process_audio_ogg') == 1 ? $processes[] = $HWDprocesses->addProcess($media,'8') : null;

                        $processes[] = $HWDprocesses->addProcess($media,'22');
                        $processes[] = $HWDprocesses->addProcess($media,'23');
                }
                elseif ($type == 2)
                {
                        // Document
                        $config->get('process_jpeg_75') == 1 ? $processes[] = $HWDprocesses->addProcess($media,'1') : null;
                        $config->get('process_jpeg_100') == 1 ? $processes[] = $HWDprocesses->addProcess($media,'2') : null;
                        $config->get('process_jpeg_240') == 1 ? $processes[] = $HWDprocesses->addProcess($media,'3') : null;
                        $config->get('process_jpeg_500') == 1 ? $processes[] = $HWDprocesses->addProcess($media,'4') : null;
                }
                elseif ($type == 3)
                {
                        // Image
                        $config->get('process_jpeg_75') == 1 ? $processes[] = $HWDprocesses->addProcess($media,'1') : null;
                        $config->get('process_jpeg_100') == 1 ? $processes[] = $HWDprocesses->addProcess($media,'2') : null;
                        $config->get('process_jpeg_240') == 1 ? $processes[] = $HWDprocesses->addProcess($media,'3') : null;
                        $config->get('process_jpeg_500') == 1 ? $processes[] = $HWDprocesses->addProcess($media,'4') : null;
                        $config->get('process_jpeg_640') == 1 ? $processes[] = $HWDprocesses->addProcess($media,'5') : null;
                        $config->get('process_jpeg_1024') == 1 ? $processes[] = $HWDprocesses->addProcess($media,'6') : null;
                }
                elseif ($type == 4)
                {
                        // Video
                        $config->get('process_jpeg_75') == 1 ? $processes[] = $HWDprocesses->addProcess($media,'1') : null;
                        $config->get('process_jpeg_100') == 1 ? $processes[] = $HWDprocesses->addProcess($media,'2') : null;
                        $config->get('process_jpeg_240') == 1 ? $processes[] = $HWDprocesses->addProcess($media,'3') : null;
                        $config->get('process_jpeg_500') == 1 ? $processes[] = $HWDprocesses->addProcess($media,'4') : null;
                        $config->get('process_jpeg_640') == 1 ? $processes[] = $HWDprocesses->addProcess($media,'5') : null;
                        $config->get('process_jpeg_1024') == 1 ? $processes[] = $HWDprocesses->addProcess($media,'6') : null;

                        $config->get('process_flv_240') == 1 ? $processes[] = $HWDprocesses->addProcess($media,'9') : null;
                        $config->get('process_flv_360') == 1 ? $processes[] = $HWDprocesses->addProcess($media,'10') : null;
                        $config->get('process_flv_480') == 1 ? $processes[] = $HWDprocesses->addProcess($media,'11') : null;
                        $config->get('process_mp4_360') == 1 ? $processes[] = $HWDprocesses->addProcess($media,'12') : null;
                        $config->get('process_mp4_480') == 1 ? $processes[] = $HWDprocesses->addProcess($media,'13') : null;
                        $config->get('process_mp4_720') == 1 ? $processes[] = $HWDprocesses->addProcess($media,'14') : null;
                        $config->get('process_mp4_1080') == 1 ? $processes[] = $HWDprocesses->addProcess($media,'15') : null;
                        $config->get('process_webm_360') == 1 ? $processes[] = $HWDprocesses->addProcess($media,'16') : null;
                        $config->get('process_webm_480') == 1 ? $processes[] = $HWDprocesses->addProcess($media,'17') : null;
                        $config->get('process_webm_720') == 1 ? $processes[] = $HWDprocesses->addProcess($media,'18') : null;
                        $config->get('process_webm_1080') == 1 ? $processes[] = $HWDprocesses->addProcess($media,'19') : null;
                        $config->get('process_ogg_360') == 1 ? $processes[] = $HWDprocesses->addProcess($media,'24') : null;
                        $config->get('process_ogg_480') == 1 ? $processes[] = $HWDprocesses->addProcess($media,'25') : null;
                        $config->get('process_ogg_720') == 1 ? $processes[] = $HWDprocesses->addProcess($media,'26') : null;
                        $config->get('process_ogg_1080') == 1 ? $processes[] = $HWDprocesses->addProcess($media,'27') : null;

                        // $processes[] = $HWDprocesses->addProcess($media,'20'); // Inject metadata is now integrated into parent process.
                        // $processes[] = $HWDprocesses->addProcess($media,'21'); // Move moov atom is now integrated into parent process.
                        $processes[] = $HWDprocesses->addProcess($media,'22');  
                }
                
                $cli = JPATH_SITE.'/administrator/components/com_hwdmediashare/cli.php';

                if ($config->get('process_auto') == 1)
                {
                        if(substr(PHP_OS, 0, 3) != "WIN") 
                        {
                                // Surpress the error if exec() is disabled. This is better than checking
                                // if the function is avaliable because there are multiple ways disable the 
                                // function and checking the status can cause errors itself.
                                // @exec("env -i ".$config->get('path_php')." $cli process ".implode(" ", $processes)." &>/dev/null &");
                                @exec("env -i ".$config->get('path_php')." $cli process ".implode(" ", $processes)." > /dev/null 2>&1  &");
                        } 
                        else 
                        {
                                @exec($config->get('path_php')." $cli process ".implode(" ", $processes)." NUL");
                        }
                }
	}

	/**
	 * Method to get data from the jform.
         * 
         * @access  public
         * @static
         * @return  array  An array of filtered jform data.
	 */
	public static function getProcessedUploadData()
	{
                // Initialise variables.
                $app = JFactory::getApplication();

                // Retrieve filtered jform data.
                $jform = $app->input->getArray(array(
                    'jform' => array(
                        'id' => 'int',
                        'title' => 'string',
                        'alias' => 'string',
                        'description' => 'safe_html',
                        'catid' => 'array',
                        'tags' => 'array',
                        'published' => 'int',
                        'featured' => 'int',
                        'access' => 'int',
                        'language' => 'string',
                        // Other associations
                        'album_id' => 'int',
                        'category_id' => 'int',
                        'group_id' => 'int',
                        'playlist_id' => 'int',
                        // Thumbnail data
                        'remove_thumbnail' => 'int',
                        'thumbnail_remote' => 'string',
                        // Remote media
                        'remotes' => 'string',
                        'remote' => 'string',
                        // Remote file
                        'link_url' => 'url',
                        // Embed code
                        'embed_code' => 'raw',
                        // RTMP
                        'media_type' => 'int',
                        'streamer' => 'string',
                        'file' => 'string',
                    )
                ));

                $data = $jform['jform'];
                
                if (empty($data['id']))                                         unset($data['id']);
                if (empty($data['title']))                                      unset($data['title']);
                if (empty($data['alias']))                                      unset($data['alias']);
                if (empty($data['description']))                                unset($data['description']);
                if (count($data['catid']) < 1)                                  unset($data['catid']);
                if (count($data['tags']) < 1)                                   unset($data['tags']);
                if (empty($data['description']))                                unset($data['description']);
                if (empty($data['published']))                                  unset($data['published']);
                if (empty($data['featured']))                                   unset($data['featured']);
                if (empty($data['access']))                                     unset($data['access']);
                if (empty($data['language']))                                   unset($data['language']);

                if (empty($data['album_id']))                                   unset($data['album_id']);
                if (empty($data['category_id']))                                unset($data['category_id']);
                if (empty($data['group_id']))                                   unset($data['group_id']);
                if (empty($data['playlist_id']))                                unset($data['playlist_id']);
                                
                // if (empty($data['remove_thumbnail']))                        unset($data['remove_thumbnail']);  // We always want this defined.
                if (empty($data['thumbnail_remote']))                           unset($data['thumbnail_remote']);
                
                if (empty($data['remotes']))                                    unset($data['remotes']);
                if (empty($data['remote']))                                     unset($data['remote']);
                if (empty($data['link_url']))                                   unset($data['link_url']);
                if (empty($data['embed_code']))                                 unset($data['embed_code']);
                if (empty($data['media_type']))                                 unset($data['media_type']);
                if (empty($data['streamer']))                                   unset($data['streamer']);
                if (empty($data['file']))                                       unset($data['file']);

                return $data;    
        }
     
	/**
	 * Method to check upload limits for user.
         * 
         * @access  public
         * @static
         * @return  boolean  True if under limit, false if over limit.
	 */
	public static function checkLimits()
	{
                // Initialise variables.
                $app = JFactory::getApplication();
                $db = JFactory::getDbo();
                $user = JFactory::getUser();
                $user_limit = 0;
                
                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                if ($config->get('enable_limits'))
                {
                        // Get limits array.
                        $limits = json_decode($config->get('upload_limits'), true);
                
                        // Define the value array.
                        $values = array(1 => 'space', 2 => 'number');
                        
                        // Calculate the maximum limit for the user.
                        foreach ($user->groups as $key => $group)
                        {
                                $group_limit = $limits[$values[$config->get('enable_limits')]][$key];
                                if ($group_limit > 0)
                                {
                                        $user_limit = $group_limit > $user_limit ? $group_limit : $user_limit;
                                }
                        }
                        
                        if ($user_limit > 0)
                        {
                                if ($config->get('enable_limits') == 1)
                                {
                                        // Limit by disk space.
                                        $query = $db->getQuery(true)
                                                ->select('SUM(size)')
                                                ->from('#__hwdms_files')
                                                ->where('created_user_id = ' . $user->id)
                                                ->where('file_type = 1');
                                        try
                                        {
                                                $db->setQuery($query);
                                                $bytes = $db->loadResult();
                                        }
                                        catch (RuntimeException $e)
                                        {
                                                $this->setError($e->getMessage());
                                                return false;                            
                                        }
                                        
                                        $mbytes = round($bytes / 1048576);
                                        if ($mbytes > $user_limit)
                                        {
                                                $app->enqueueMessage(JText::sprintf('COM_HWDMS_WARNING_OVER_LIMIT_DISK_SPACE', $user_limit, $mbytes));
                                                return false;                                            
                                        }
                                }
                                elseif ($config->get('enable_limits') == 2)
                                {
                                        // Limit by number of uploads.
                                        $query = $db->getQuery(true)
                                                ->select('COUNT(*)')
                                                ->from('#__hwdms_media')
                                                ->where('created_user_id = ' . $user->id);
                                        try
                                        {
                                                $db->setQuery($query);
                                                $count = $db->loadResult();
                                        }
                                        catch (RuntimeException $e)
                                        {
                                                $this->setError($e->getMessage());
                                                return false;                            
                                        }
                                        
                                        if ($count > $user_limit)
                                        {
                                                $app->enqueueMessage(JText::sprintf('COM_HWDMS_WARNING_OVER_LIMIT_NUMBER_OF_UPLOADS', $user_limit, $count));
                                                return false;                                            
                                        }
                                }
                        }
                }
                    
                return true;
        }        
}
