<?php
/**
 * @version    SVN $Id: upload.php 1622 2013-08-14 14:01:56Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access'); 

/**
 * hwdMediaShare framework upload class
 *
 * @package hwdMediaShare
 * @since   0.1
 */
class hwdMediaShareUpload extends JObject
{
        /**
	 * @since	0.1
	 */
        public $elementType = 1;
        
        var $_id;
        var $_title;

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
	 * Returns the hwdMediaShareRemote object, only creating it if it
	 * doesn't already exist.
	 *
	 * @return  hwdMediaShareMedia A hwdMediaShareRemote object.
	 * @since   0.1
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
	 * Method to generate a key.
         *
	 * @since   0.1
	 */
        function generateKey()
        {
                mt_srand(microtime(true)*100000 + memory_get_usage(true));
                return md5(uniqid(mt_rand(), true));
        }

	/**
	 * Method to check if a media key exists
         *
	 * @since   0.1
	 */
        function keyExists($key)
        {
                $db =& JFactory::getDBO();
                $app=& JFactory::getApplication();
                $params = &JComponentHelper::getParams( 'com_hwdmediashare' );

                $query = "
                    SELECT count(*)
                    FROM ".$db->quoteName('#__hwdms_media')."
                    WHERE ".$db->quoteName('key')." = ".$db->quote($key).";
                ";

                $db->SetQuery( $query );
                $count = $db->loadResult();

                if (@$params->debug)
                {
                    $app->enqueueMessage(nl2br($db->getErrorMsg()),'error');
                    return;
                }

                $exists = ($count > 0 ? true : false);
                return $exists;
        }

	/**
	 * Method to generate teh destination of an upload.
         *
	 * @since   0.1
	 */
        function dest($folders, $filename)
        {
                return HWDMS_PATH_MEDIA_FILES . '/' . $folders[1] . '/' . $folders[2] . '/' . $folders[3] . '/' . $filename;
        }

	/**
	 * Method to remove an extenstion from a filename
         *
	 * @since   0.1
	 */
        function removeExtension($strName)
        {
             $ext = strrchr($strName, '.');

             if($ext !== false)
             {
                 $strName = substr($strName, 0, -strlen($ext));
             }
             return $strName;
        }

	/**
	 * Method to add a user upload session token to database
         *
	 * @since   0.1
	 */
	public function addUserUploadSession( $token )
	{
                $date =& JFactory::getDate();
                $user = & JFactory::getUser();

                $object = new stdClass;
                $object->userid = $user->id;
                $object->token = $token;
                $object->datetime = $date->format('Y-m-d H:i:s');

                $db =& JFactory::getDBO();
		$db->insertObject( '#__hwdms_upload_tokens' , $object );
	}

	/**
	 * Method to generate the upload uri for FancyUpload2
         *
	 * @since   0.1
	 */
	public function getFlashUploadURI()
	{
                $session	= JFactory::getSession();
                $user = & JFactory::getUser();

                // Generate a session handler for this user.
                $token	= $session->getToken( true );

                $url	= JURI::root(true) . '/index.php?option=com_hwdmediashare&task=addmedia.upload&format=raw';
                $url   .= '&' . $session->getName() . '=' . $session->getId() . '&token=' . $token . '&uploaderid=' . $user->id;

                hwdMediaShareUpload::addUserUploadSession($token);

                return $url;
	}

	/**
	 * Method to process a file upload
         *
	 * @since   0.1
	 */
	public function process( $upload )
	{
                $db =& JFactory::getDBO();
                $user = & JFactory::getUser();
                $app = & JFactory::getApplication();
                $date =& JFactory::getDate();

                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                hwdMediaShareFactory::load('files');

                $error = false;

                $data = JRequest::getVar('jform', array(), 'post', 'array');
                
                jimport( 'joomla.filesystem.file' );
                //Retrieve file details from uploaded file, sent from upload form
                $file = JRequest::getVar($upload->input, null, 'files', 'array');
                $ext = strtolower(JFile::getExt($file['name']));
                
                // Check if we need to replace an existing media item
                if (isset($data['id']) && $data['id'] > 0 && $app->isAdmin() && $user->authorise('core.edit', 'com_hwdmediashare'))
                {
                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                        $table =& JTable::getInstance('Media', 'hwdMediaShareTable');
                        $table->load( $data['id'] );
                        $properties = $table->getProperties(1);
                        $replace = JArrayHelper::toObject($properties, 'JObject');
                        $key = $replace->key;      
                }
                else
                {
                        $key = hwdMediaShareUpload::generateKey();
                        if (hwdMediaShareUpload::keyExists($key))
                        {
                                $this->setError(JText::_('COM_HWDMS_KEY_EXISTS'));
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
                        hwdMediaShareFiles::getLocalStoragePath();

                        //Import filesystem libraries. Perhaps not necessary, but does not hurt
                        jimport('joomla.filesystem.file');

                        $folders = hwdMediaShareFiles::getFolders($key);
                        hwdMediaShareFiles::setupFolders($folders);

                        //Clean up filename to get rid of strange characters like spaces etc
                        $filename = hwdMediaShareFiles::getFilename($key, '1');

                        //Set up the source and destination of the file
                        $src = $file['tmp_name'];
                        $dest = hwdMediaShareFiles::getPath($folders, $filename, $ext);

                        // Get allowed media types
                        $media_types = array();
                        if ($config->get('enable_audio')) $media_types[] = 1;
                        if ($config->get('enable_documents')) $media_types[] = 2;
                        if ($config->get('enable_images')) $media_types[] = 3;
                        if ($config->get('enable_videos')) $media_types[] = 4;
    
                        //First check if the file has the right extension, we need jpg only
                        $db = JFactory::getDBO();
                        $query = $db->getQuery(true);
                        $query->select('id');
                        $query->from('#__hwdms_ext');
                        $query->where($db->quoteName('ext').' = '.$db->quote($ext));
                        $query->where($db->quoteName('media_type').' IN ('.implode(', ', $media_types).')');

                        $db->setQuery($query);
                        $ext_id = $db->loadResult();
                        if ( $ext_id > 0 )
                        {
                                // Check if we need to replace an existing media item
                                if (isset($data['id']) && $data['id'] > 0 && $app->isAdmin() && $user->authorise('core.edit', 'com_hwdmediashare'))
                                {
                                        // Here, we need to remove all files already associated with this media item
                                        hwdMediaShareFactory::load('files');
                                        hwdMediaShareFiles::deleteMediaFiles($replace);
                                }
                                
                                if ( JFile::upload($src, $dest) )
                                {
                                        //Redirect to a page of your choice
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

                        // Set approved/pending
                        (!$app->isAdmin() && $config->get('approve_new_media')) == 1 ? $status = 2 : $status = 1; 
                        $config->get('approve_new_media') == 1 ? $status = 2 : $status = 1; 
            
                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                        $row =& JTable::getInstance('Media', 'hwdMediaShareTable');

                        $post                          = array();

                        // Check if we need to replace an existing media item
                        if (isset($data['id']) && $data['id'] > 0 && $app->isAdmin() && $user->authorise('core.edit', 'com_hwdmediashare'))
                        {
                                // Here, we need to remove all files already associated with this media item
                                hwdMediaShareFactory::load('files');
                                hwdMediaShareFiles::deleteMediaFiles($replace);

                                // Now we setup a new array to bind to this item
                                $post['id']                     = $data['id'];
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
                                $post['modified']               = $date->format('Y-m-d H:i:s');
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
                                $post['title']                  = hwdMediaShareUpload::removeExtension($file['name']);
                                $post['alias']                  = JFilterOutput::stringURLSafe($post['title']);
                                //$post['description']          = '';
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
                                $post['published']              = 1;
                                $post['featured']               = 0;
                                //$post['checked_out']          = '';
                                //$post['checked_out_time']     = '';
                                $post['access']                 = 1;
                                //$post['download']             = '';
                                //$post['params']               = '';
                                //$post['ordering']             = '';
                                $post['created_user_id']        = $user->id;
                                //$post['created_user_id_alias']= '';
                                $post['created']                = $date->format('Y-m-d H:i:s');
                                $post['publish_up']             = $date->format('Y-m-d H:i:s');
                                $post['publish_down']           = '0000-00-00 00:00:00';
                                $post['modified_user_id']       = $user->id;
                                $post['modified']               = $date->format('Y-m-d H:i:s');
                                $post['hits']                   = 0;
                                $post['language']               = '*';
                        }

                        // Bind it to the table
                        if (!$row->bind( $post ))
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

                $this->_id = $row->id;
                $this->_title = $row->title;

                hwdMediaShareUpload::assignAssociations($row);

                hwdMediaShareFactory::load('files');
                hwdMediaShareFiles::add($row,'1');

                hwdMediaShareUpload::addProcesses($row);

                // Trigger onAfterMediaAdd
                if ($config->get('approve_new_media') == 0)
                {
                        hwdMediaShareFactory::load('events');
                        $events = hwdMediaShareEvents::getInstance();
                        $events->triggerEvent('onAfterMediaAdd', $row); 
                }

                // Send system notifications
                if ($config->get('notify_new_media') == 1) 
                {
                        if($row->status == 2){
                                ob_start();
                                require(JPATH_SITE . '/components/com_hwdmediashare/libraries/emails/newmedia_pending.php');
                                $body = ob_get_contents();
                                ob_end_clean();
                        }
                        else{
                                ob_start();
                                require(JPATH_SITE . '/components/com_hwdmediashare/libraries/emails/newmedia.php');
                                $body = ob_get_contents();
                                ob_end_clean();
                        }                        
                        hwdMediaShareFactory::load('utilities');
                        $utilities = hwdMediaShareUtilities::getInstance();
                        $utilities->sendSystemEmail(JText::_('COM_HWDMS_EMAIL_SUBJECT_NEW_MEDIA'), $body);
                } 

                return true;
        }

	/**
	 * Method to process a cusotm thumbnail upload
         *
	 * @since   0.1
	 */
	public function processThumbnail( $params )
	{
                // Initialise variables
                $db =& JFactory::getDBO();
                $user = & JFactory::getUser();
                $app = & JFactory::getApplication();
                $input = 'thumbnail';
                $error = false;

                // Load libraries
                hwdMediaShareFactory::load('files');

                // Load the current element
                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                switch ($params->elementType) {
                    case 1:
                        $table =& JTable::getInstance('Media', 'hwdMediaShareTable');
                        break;
                    case 2:
                        $table =& JTable::getInstance('Album', 'hwdMediaShareTable');
                        break;
                    case 3:
                        $table =& JTable::getInstance('Group', 'hwdMediaShareTable');
                        break;
                    case 4:
                        $table =& JTable::getInstance('Playlist', 'hwdMediaShareTable');
                        break;
                    case 5:
                        $table =& JTable::getInstance('UserChannel', 'hwdMediaShareTable');
                        break;
                    default:
                        $table =& JTable::getInstance('Media', 'hwdMediaShareTable');
                        break;
                }
                $table->load( $params->elementId );

                $properties = $table->getProperties(1);
                $row = JArrayHelper::toObject($properties, 'JObject');

                //Retrieve file details from uploaded file, sent from upload form
                jimport( 'joomla.filesystem.file' );
                $file = JRequest::getVar('jform', null, 'files', 'array');
                $ext = strtolower(JFile::getExt($file['name'][$input]));

                // Remove current thumbnail if requested or new thumbnail is attached to form
                if (is_uploaded_file($file['tmp_name'][$input]) || $params->remove)
                {                    
                        $_folders = hwdMediaShareFiles::getFolders($row->key);
                        $_filename = hwdMediaShareFiles::getFilename($row->key, '10');
                        $_ext = hwdMediaShareFiles::getExtension($row, 10);
                        $_path = hwdMediaShareFiles::getPath($_folders, $_filename, $_ext);
                        if (file_exists($_path))
                        {
                                jimport( 'joomla.filesystem.file' );
                                JFile::delete($_path);
                        }
                }

                // If no file attached then return
                if (is_uploaded_file($file['tmp_name'][$input]) && !empty($ext))
                {
                        hwdMediaShareFiles::getLocalStoragePath();

                        //Import filesystem libraries. Perhaps not necessary, but does not hurt
                        jimport('joomla.filesystem.file');

                        $folders = hwdMediaShareFiles::getFolders($row->key);
                        hwdMediaShareFiles::setupFolders($folders);

                        //Clean up filename to get rid of strange characters like spaces etc
                        $filename = hwdMediaShareFiles::getFilename($row->key, '10');

                        //Set up the source and destination of the file
                        $src = $file['tmp_name'][$input];
                        $dest = hwdMediaShareFiles::getPath($folders, $filename, $ext);

                        //First check if the file has the right extension, we need jpg only
                        $db = JFactory::getDBO();
                        $query = $db->getQuery(true);
                        $query->select('id');
                        $query->from('#__hwdms_ext');
                        $query->where($db->quoteName('ext').' = '.$db->quote($ext));

                        $db->setQuery($query);
                        $ext_id = $db->loadResult();
                        if ( $ext_id > 0 )
                        {
                                if ( JFile::upload($src, $dest) )
                                {
                                        //Redirect to a page of your choice
                                        $data = array();
                                        $data['id'] = $row->id;
                                        $data['thumbnail_ext_id'] = $ext_id;

                                        if (!$table->bind( $data )) {
                                                return JError::raiseWarning( 500, $row->getError() );
                                        }
                                        if (!$table->store()) {
                                                JError::raiseError(500, $row->getError() );
                                        }
                                }
                                else
                                {
                                        // Upload failed
                                        return false;
                                }
                        }
                        else
                        {
                                // Extension not allowed
                                return false;
                        }

                        hwdMediaShareFactory::load('files');
                        hwdMediaShareFiles::add($table, 10, $params->elementType);
                }
                elseif (isset($params->thumbnail_remote) && !empty($params->thumbnail_remote))
                {
                        //Redirect to a page of your choice
                        $data = array();
                        $data['id'] = $row->id;
                        $data['thumbnail'] = $params->thumbnail_remote;

                        if (!$table->bind( $data )) {
                                return JError::raiseWarning( 500, $row->getError() );
                        }
                        if (!$table->store()) {
                                JError::raiseError(500, $row->getError() );
                        }
                }

                return true;
        }

        /**
	 * Method to get all allowed extensions
         *
	 * @since   0.1
	 */
	public function getAllowedExtensions($method=null)
	{
                // Load hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                $media_types = array();
       
                switch ($method) 
                {
                    case 'standard':                        
                        if ($config->get('audio_uploads') == 0 || $config->get('audio_uploads') == 2 && $config->get('enable_audio')) $media_types[] = 1;
                        if ($config->get('document_uploads') == 0 || $config->get('document_uploads') == 2 && $config->get('enable_documents')) $media_types[] = 2;
                        if ($config->get('image_uploads') == 0 || $config->get('image_uploads') == 2 && $config->get('enable_images')) $media_types[] = 3;
                        if ($config->get('video_uploads') == 0 || $config->get('video_uploads') == 2 && $config->get('enable_videos')) $media_types[] = 4;
                        break;
                    case 'large':
                        if ($config->get('audio_uploads') == 1 || $config->get('audio_uploads') == 2 && $config->get('enable_audio')) $media_types[] = 1;
                        if ($config->get('document_uploads') == 1 || $config->get('document_uploads') == 2 && $config->get('enable_documents')) $media_types[] = 2;
                        if ($config->get('image_uploads') == 1 || $config->get('image_uploads') == 2 && $config->get('enable_images')) $media_types[] = 3;
                        if ($config->get('video_uploads') == 1 || $config->get('video_uploads') == 2 && $config->get('enable_videos')) $media_types[] = 4;
                        break;
                    case 'platform':
                        if ($config->get('audio_uploads') == 3 && $config->get('enable_audio')) $media_types[] = 1;
                        if ($config->get('document_uploads') == 3 && $config->get('enable_documents')) $media_types[] = 2;
                        if ($config->get('image_uploads') == 3 && $config->get('enable_images')) $media_types[] = 3;
                        if ($config->get('video_uploads') == 3 && $config->get('enable_videos')) $media_types[] = 4;
                        break;
                    default:
                        $media_types = array(1,2,3,4);
                        break;
                }                
                
		if (count($media_types) > 0)
                {
                        $db =& JFactory::getDBO();
                        $query = "
                        SELECT ".$db->quoteName('ext')."
                            FROM ".$db->quoteName('#__hwdms_ext')."
                            WHERE ".$db->quoteName('media_type')." IN (" . implode(',', $media_types) . ")
                            AND ".$db->quoteName('published')." = ".$db->quote('1')."
                        ";  

                        $db->setQuery($query);
                        $row = $db->loadColumn();

                        return $row;
                }
                return false;
	}

	/**
	 * Method to process an uber upload
         *
	 * @since   0.1
	 */
	public function uber( )
	{
                $db =& JFactory::getDBO();
                $user = & JFactory::getUser();
                $app = & JFactory::getApplication();
                $date =& JFactory::getDate();
                // Load hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                hwdMediaShareFactory::load('files');
                hwdMediaShareFiles::getLocalStoragePath();

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

if($PHP_ERROR_REPORTING){ error_reporting(E_ALL); }

if(preg_match("/^[a-zA-Z0-9]{32}$/", $_GET['upload_id'])){ $UPLOAD_ID = $_GET['upload_id']; }
elseif(isset($_GET['about']) && $_GET['about'] == 1){ kak("<u><b>UBER UPLOADER FINISHED PAGE</b></u><br>UBER UPLOADER VERSION =  <b>" . $UBER_VERSION . "</b><br>UBR_FINISHED = <b>" . $THIS_VERSION . "<b><br>\n", 1 , __LINE__); }
else{ kak("ERROR: Invalid parameters passed<br>", 1, __LINE__); }

//Declare local values
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

// Output XML DATA, CONFIG DATA, POST DATA, FILE DATA to screen and exit if DEBUG_ENABLED.
if($DEBUG_FINISHED){
	debug("<br><u>XML DATA</u>", $_XML_DATA);
	debug("<u>CONFIG DATA</u>", $_CONFIG_DATA);
	debug("<u>POST DATA</u>", $_POST_DATA);
	debug("<u>FILE DATA</u><br>", $_FILE_DATA);
	exit;
}

//Create file upload table
$_FILE_DATA_TABLE = getFileDataTable($_FILE_DATA, $_CONFIG_DATA);

// Create and send email
if($_CONFIG_DATA['send_email_on_upload']){ emailUploadResults($_FILE_DATA, $_CONFIG_DATA, $_POST_DATA); }

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

                // Get associations from ubr_upload and assign them to the jform array
                $data = array();
                if (isset($_POST_DATA['jform_catid']))			$data['catid'] = intval($_POST_DATA['jform_catid']);
                if (isset($_POST_DATA['jform_album_id']))		$data['album_id'] = intval($_POST_DATA['jform_album_id']);
                if (isset($_POST_DATA['jform_playlist_id']))            $data['playlist_id'] = intval($_POST_DATA['jform_playlist_id']);
                if (isset($_POST_DATA['jform_group_id']))		$data['group_id'] = intval($_POST_DATA['jform_group_id']);
                if (isset($_POST_DATA['jform_id']))                     $data['id'] = intval($_POST_DATA['jform_id']);
                if (isset($_POST_DATA['redirect']))                     JRequest::setVar('redirect', $_POST_DATA['redirect']);
                JRequest::setVar('jform', $data);

                foreach($_FILE_DATA as $arrayKey => $slot)
                {
                        hwdMediaShareFactory::load('files');

                        $tmp_name = HWDMS_PATH_MEDIA_FILES.'/'.$slot->name;
                        if (!isset($slot->name) || !file_exists(HWDMS_PATH_MEDIA_FILES.'/'.$slot->name))
                        {
                                $this->setError(JText::_('COM_HWDMS_ERROR_UPLOAD_ERROR'));
                                return false;
                        }

                        $error = false;

                        // Retrieve file details from uploaded file, sent from upload form
                        jimport( 'joomla.filesystem.file' );
                        $ext = strtolower(JFile::getExt($slot->name));
                        
                        // Check if we need to replace an existing media item
                        if (isset($data['id']) && $data['id'] > 0 && $app->isAdmin() && $user->authorise('core.edit', 'com_hwdmediashare'))
                        {
                                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                                $table =& JTable::getInstance('Media', 'hwdMediaShareTable');
                                $table->load( $data['id'] );
                                $properties = $table->getProperties(1);
                                $replace = JArrayHelper::toObject($properties, 'JObject');
                                $key = $replace->key;      
                        }
                        else
                        {
                                $key = hwdMediaShareUpload::generateKey();
                                if (hwdMediaShareUpload::keyExists($key))
                                {
                                        $this->setError(JText::_('COM_HWDMS_KEY_EXISTS'));
                                        return false;
                                }                        
                        }

                        // Import filesystem libraries. Perhaps not necessary, but does not hurt
                        jimport('joomla.filesystem.file');

                        $folders = hwdMediaShareFiles::getFolders($key);
                        hwdMediaShareFiles::setupFolders($folders);

                        // Get filename for original media
                        $filename = hwdMediaShareFiles::getFilename($key, '1');

                        // Set up the source and destination of the file
                        $src = $tmp_name;
                        $dest = hwdMediaShareFiles::getPath($folders, $filename, $ext);
                        
                        // Get allowed media types
                        $media_types = array();
                        if ($config->get('enable_audio')) $media_types[] = 1;
                        if ($config->get('enable_documents')) $media_types[] = 2;
                        if ($config->get('enable_images')) $media_types[] = 3;
                        if ($config->get('enable_videos')) $media_types[] = 4;
    
                        //First check if the file has the right extension, we need jpg only
                        $db = JFactory::getDBO();
                        $query = $db->getQuery(true);
                        $query->select('id');
                        $query->from('#__hwdms_ext');
                        $query->where($db->quoteName('ext').' = '.$db->quote($ext));
                        $query->where($db->quoteName('media_type').' IN ('.implode(', ', $media_types).')');

                        $db->setQuery($query);
                        $ext_id = $db->loadResult();
                        if ( $ext_id > 0 )
                        {
                                // Check if we need to replace an existing media item
                                if (isset($data['id']) && $data['id'] > 0 && $app->isAdmin() && $user->authorise('core.edit', 'com_hwdmediashare'))
                                {
                                        // Here, we need to remove all files already associated with this media item
                                        hwdMediaShareFactory::load('files');
                                        hwdMediaShareFiles::deleteMediaFiles($replace);
                                }
                                
                                if ( JFile::move($src, $dest) )
                                {
                                        //Redirect to a page of your choice
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

                        if (!$error)
                        {
                                // Set approved/pending
                                (!$app->isAdmin() && $config->get('approve_new_media')) == 1 ? $status = 2 : $status = 1; 
                                $config->get('approve_new_media') == 1 ? $status = 2 : $status = 1; 

                                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                                $row =& JTable::getInstance('media', 'hwdMediaShareTable');

                                $post                          = array();

                                // Check if we need to replace an existing media item
                                if (isset($data['id']) && $data['id'] > 0 && $app->isAdmin() && $user->authorise('core.edit', 'com_hwdmediashare'))
                                {
                                        // Here, we need to remove all files already associated with this media item
                                        hwdMediaShareFactory::load('files');
                                        hwdMediaShareFiles::deleteMediaFiles($replace);

                                        // Now we setup a new array to bind to this item
                                        $post['id']                     = $data['id'];
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
                                        $post['modified']               = $date->format('Y-m-d H:i:s');
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
                                        $post['title']                  = hwdMediaShareUpload::removeExtension($_POST_DATA[$slot->slot]);
                                        $post['alias']                  = JFilterOutput::stringURLSafe($post['title']);
                                        //$post['description']          = '';
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
                                        $post['published']              = 1;
                                        $post['featured']               = 0;
                                        //$post['checked_out']          = '';
                                        //$post['checked_out_time']     = '';
                                        $post['access']                 = 1;
                                        $post['download']               = 1;
                                        //$post['params']               = '';
                                        //$post['ordering']             = '';
                                        $post['created_user_id']        = $user->id;
                                        //$post['created_user_id_alias']= '';
                                        $post['created']                = $date->format('Y-m-d H:i:s');
                                        $post['publish_up']             = $date->format('Y-m-d H:i:s');
                                        $post['publish_down']           = '0000-00-00 00:00:00';
                                        $post['modified_user_id']       = $user->id;
                                        $post['modified']               = $date->format('Y-m-d H:i:s');
                                        $post['hits']                   = 0;
                                        $post['language']               = '*';
                                }

                                // Bind it to the table
                                if (!$row->bind( $post ))
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

                        $this->_id = $row->id;
                        $this->_title = $row->title;

                        hwdMediaShareUpload::assignAssociations($row);

                        hwdMediaShareFactory::load('files');
                        hwdMediaShareFiles::add($row,'1');

                        hwdMediaShareUpload::addProcesses($row);

                        // Trigger onAfterMediaAdd
                        if ($config->get('approve_new_media') == 0)
                        {
                                hwdMediaShareFactory::load('events');
                                $events = hwdMediaShareEvents::getInstance();
                                $events->triggerEvent('onAfterMediaAdd', $row); 
                        }

                        // Send system notifications
                        if ($config->get('notify_new_media') == 1) 
                        {
                                if($row->status == 2){
                                        ob_start();
                                        require(JPATH_SITE . '/components/com_hwdmediashare/libraries/emails/newmedia_pending.php');
                                        $body = ob_get_contents();
                                        ob_end_clean();
                                }
                                else{
                                        ob_start();
                                        require(JPATH_SITE . '/components/com_hwdmediashare/libraries/emails/newmedia.php');
                                        $body = ob_get_contents();
                                        ob_end_clean();
                                }
                                hwdMediaShareFactory::load('utilities');
                                $utilities = hwdMediaShareUtilities::getInstance();
                                $utilities->sendSystemEmail(JText::_('COM_HWDMS_EMAIL_SUBJECT_NEW_MEDIA'), $body);
                        }   
                }
                return true;
        }

	/**
	 * Method to associate the upload with elements
         *
	 * @since   0.1
	 */
	public function assignAssociations($row)
	{
                //// @TODO: Validation and playlist section
                $data = JRequest::getVar('jform', array(), 'request', 'array');
                if (isset($data['catid']))
                {
                        if (is_array($data['catid']))
                        {
                                $params = new StdClass;
                                $params->elementId = $row->id;
                                $params->categoryId = $data['catid'];
                                hwdMediaShareFactory::load('category');
                                hwdMediaShareCategory::save($params);
                                unset($params);
                        }
                        else
                        {
                                $cid = (int) $data['catid'];                                
                                $params = new StdClass;
                                $params->elementId = $row->id;
                                $params->categoryId = array($cid);
                                hwdMediaShareFactory::load('category');
                                hwdMediaShareCategory::save($params);
                                unset($params);
                        }
                }
                if (isset($data['album_id']))
                {
                        if ($data['album_id'] > 0)
                        {                      
                                $params = new StdClass;
                                $params->albumId = (int) $data['album_id'];
                                $controller =& JControllerLegacy::getInstance('hwdMediaShareController');
                                $model = $controller->getModel('albumMedia');
                                $model->link( $row->id, $params );
                        }
                }
                if (isset($data['playlist_id']))
                {
                        if ($data['playlist_id'] > 0)
                        {
                                $params = new StdClass;
                                $params->playlistId = (int) $data['playlist_id'];
                                $controller =& JControllerLegacy::getInstance('hwdMediaShareController');
                                $model = $controller->getModel('playlistMedia');
                                $model->link( $row->id, $params );
                        }
                }
                if (isset($data['group_id']))
                {
                        if ($data['group_id'] > 0)
                        {
                                $params = new StdClass;
                                $params->groupId = (int) $data['group_id'];
                                $controller =& JControllerLegacy::getInstance('hwdMediaShareController');
                                $model = $controller->getModel('groupMedia');
                                $model->link( $row->id, $params );
                        }
                }
                return;
	}

	/**
	 * Method to add relavent processes for uploads
         *
	 * @since   0.1
	 */
	public function addProcesses($item)
	{
                // Load hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // Check we are meant to be processing
                if ($config->get('process') == 0) return true;
                        
                $processes = array();

                hwdMediaShareFactory::load('processes');
                hwdMediaShareFactory::load('media');

                $type = hwdMediaShareMedia::loadMediaType($item);
                if ($type == 1)
                {
                        // Audio
                        $config->get('process_jpeg_75') == 1 ? $processes[] = hwdMediaShareProcesses::add($item,'1') : null;
                        $config->get('process_jpeg_100') == 1 ? $processes[] = hwdMediaShareProcesses::add($item,'2') : null;
                        $config->get('process_jpeg_240') == 1 ? $processes[] = hwdMediaShareProcesses::add($item,'3') : null;
                        $config->get('process_jpeg_500') == 1 ? $processes[] = hwdMediaShareProcesses::add($item,'4') : null;
                        $config->get('process_jpeg_640') == 1 ? $processes[] = hwdMediaShareProcesses::add($item,'5') : null;
                        $config->get('process_jpeg_1024') == 1 ? $processes[] = hwdMediaShareProcesses::add($item,'6') : null;

                        $config->get('process_audio_mp3') == 1 ? $processes[] = hwdMediaShareProcesses::add($item,'7') : null;
                        $config->get('process_audio_ogg') == 1 ? $processes[] = hwdMediaShareProcesses::add($item,'8') : null;

                        $processes[] = hwdMediaShareProcesses::add($item,'22');
                        $processes[] = hwdMediaShareProcesses::add($item,'23');
                }
                else if ($type == 2)
                {
                        // Document
                        $config->get('process_jpeg_75') == 1 ? $processes[] = hwdMediaShareProcesses::add($item,'1') : null;
                        $config->get('process_jpeg_100') == 1 ? $processes[] = hwdMediaShareProcesses::add($item,'2') : null;
                        $config->get('process_jpeg_240') == 1 ? $processes[] = hwdMediaShareProcesses::add($item,'3') : null;
                        $config->get('process_jpeg_500') == 1 ? $processes[] = hwdMediaShareProcesses::add($item,'4') : null;
                }
                else if ($type == 3)
                {
                        // Image
                        $config->get('process_jpeg_75') == 1 ? $processes[] = hwdMediaShareProcesses::add($item,'1') : null;
                        $config->get('process_jpeg_100') == 1 ? $processes[] = hwdMediaShareProcesses::add($item,'2') : null;
                        $config->get('process_jpeg_240') == 1 ? $processes[] = hwdMediaShareProcesses::add($item,'3') : null;
                        $config->get('process_jpeg_500') == 1 ? $processes[] = hwdMediaShareProcesses::add($item,'4') : null;
                        $config->get('process_jpeg_640') == 1 ? $processes[] = hwdMediaShareProcesses::add($item,'5') : null;
                        $config->get('process_jpeg_1024') == 1 ? $processes[] = hwdMediaShareProcesses::add($item,'6') : null;
                }
                else if ($type == 4)
                {
                        // Video
                        $config->get('process_jpeg_75') == 1 ? $processes[] = hwdMediaShareProcesses::add($item,'1') : null;
                        $config->get('process_jpeg_100') == 1 ? $processes[] = hwdMediaShareProcesses::add($item,'2') : null;
                        $config->get('process_jpeg_240') == 1 ? $processes[] = hwdMediaShareProcesses::add($item,'3') : null;
                        $config->get('process_jpeg_500') == 1 ? $processes[] = hwdMediaShareProcesses::add($item,'4') : null;
                        $config->get('process_jpeg_640') == 1 ? $processes[] = hwdMediaShareProcesses::add($item,'5') : null;
                        $config->get('process_jpeg_1024') == 1 ? $processes[] = hwdMediaShareProcesses::add($item,'6') : null;

                        $config->get('process_flv_240') == 1 ? $processes[] = hwdMediaShareProcesses::add($item,'9') : null;
                        $config->get('process_flv_360') == 1 ? $processes[] = hwdMediaShareProcesses::add($item,'10') : null;
                        $config->get('process_flv_480') == 1 ? $processes[] = hwdMediaShareProcesses::add($item,'11') : null;
                        $config->get('process_mp4_360') == 1 ? $processes[] = hwdMediaShareProcesses::add($item,'12') : null;
                        $config->get('process_mp4_480') == 1 ? $processes[] = hwdMediaShareProcesses::add($item,'13') : null;
                        $config->get('process_mp4_720') == 1 ? $processes[] = hwdMediaShareProcesses::add($item,'14') : null;
                        $config->get('process_mp4_1080') == 1 ? $processes[] = hwdMediaShareProcesses::add($item,'15') : null;
                        $config->get('process_webm_360') == 1 ? $processes[] = hwdMediaShareProcesses::add($item,'16') : null;
                        $config->get('process_webm_480') == 1 ? $processes[] = hwdMediaShareProcesses::add($item,'17') : null;
                        $config->get('process_webm_720') == 1 ? $processes[] = hwdMediaShareProcesses::add($item,'18') : null;
                        $config->get('process_webm_1080') == 1 ? $processes[] = hwdMediaShareProcesses::add($item,'19') : null;
                        $config->get('process_ogg_360') == 1 ? $processes[] = hwdMediaShareProcesses::add($item,'24') : null;
                        $config->get('process_ogg_480') == 1 ? $processes[] = hwdMediaShareProcesses::add($item,'25') : null;
                        $config->get('process_ogg_720') == 1 ? $processes[] = hwdMediaShareProcesses::add($item,'26') : null;
                        $config->get('process_ogg_1080') == 1 ? $processes[] = hwdMediaShareProcesses::add($item,'27') : null;

                        $processes[] = hwdMediaShareProcesses::add($item,'20');
                        $processes[] = hwdMediaShareProcesses::add($item,'21');
                        $processes[] = hwdMediaShareProcesses::add($item,'22');  
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
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	public function getReadableAllowedMediaTypes($method=null)
	{
                // Load hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                $db =& JFactory::getDBO();

                $media_types = array();
                hwdMediaShareFactory::load('media');
                        
                switch ($method) 
                {
                    case 'standard':                        
                        if ($config->get('audio_uploads') == 0 || $config->get('audio_uploads') == 2 && $config->get('enable_audio')) $media_types[] = 1;
                        if ($config->get('document_uploads') == 0 || $config->get('document_uploads') == 2 && $config->get('enable_documents')) $media_types[] = 2;
                        if ($config->get('image_uploads') == 0 || $config->get('image_uploads') == 2 && $config->get('enable_images')) $media_types[] = 3;
                        if ($config->get('video_uploads') == 0 || $config->get('video_uploads') == 2 && $config->get('enable_videos')) $media_types[] = 4;
                        break;
                    case 'large':
                        if ($config->get('audio_uploads') == 1 || $config->get('audio_uploads') == 2 && $config->get('enable_audio')) $media_types[] = 1;
                        if ($config->get('document_uploads') == 1 || $config->get('document_uploads') == 2 && $config->get('enable_documents')) $media_types[] = 2;
                        if ($config->get('image_uploads') == 1 || $config->get('image_uploads') == 2 && $config->get('enable_images')) $media_types[] = 3;
                        if ($config->get('video_uploads') == 1 || $config->get('video_uploads') == 2 && $config->get('enable_videos')) $media_types[] = 4;
                        break;
                    case 'platform':
                        if ($config->get('audio_uploads') == 3 && $config->get('enable_audio')) $media_types[] = 1;
                        if ($config->get('document_uploads') == 3 && $config->get('enable_documents')) $media_types[] = 2;
                        if ($config->get('image_uploads') == 3 && $config->get('enable_images')) $media_types[] = 3;
                        if ($config->get('video_uploads') == 3 && $config->get('enable_videos')) $media_types[] = 4;
                        break;
                    default:
                        $media_types = array(1,2,3,4);
                        break;
                }  
                
                $retval = "";
                $first = reset($media_types);
                $last = end($media_types);
                foreach($media_types as $media_type)
                {
                        switch ($media_type) {
                            case 1:
                                $query = "
                                SELECT count(*)
                                    FROM ".$db->quoteName('#__hwdms_ext')."
                                    WHERE ".$db->quoteName('media_type')." = ".$db->quote('1')."
                                    AND ".$db->quoteName('published')." = ".$db->quote('1')."
                                ";  
                                $db->setQuery($query);
                                if ($db->loadResult())
                                {
                                        $retval.= JText::_('COM_HWDMS_AUDIO');
                                        if ($media_type != $last) 
                                        {
                                            $retval.= ', ';
                                        }
                                }
                                break;
                            case 2:
                                $query = "
                                SELECT count(*)
                                    FROM ".$db->quoteName('#__hwdms_ext')."
                                    WHERE ".$db->quoteName('media_type')." = ".$db->quote('2')."
                                    AND ".$db->quoteName('published')." = ".$db->quote('1')."
                                ";  
                                $db->setQuery($query);
                                if ($db->loadResult())
                                {
                                        $retval.= JText::_('COM_HWDMS_DOCUMENTS');
                                        if ($media_type != $last) 
                                        {
                                            $retval.= ', ';
                                        }
                                }
                                break;
                            case 3:
                                $query = "
                                SELECT count(*)
                                    FROM ".$db->quoteName('#__hwdms_ext')."
                                    WHERE ".$db->quoteName('media_type')." = ".$db->quote('3')."
                                    AND ".$db->quoteName('published')." = ".$db->quote('1')."
                                ";  
                                $db->setQuery($query);
                                if ($db->loadResult())
                                {
                                        $retval.= JText::_('COM_HWDMS_IMAGES');
                                        if ($media_type != $last) 
                                        {
                                            $retval.= ', ';
                                        }
                                }
                                break;
                            case 4:
                                $query = "
                                SELECT count(*)
                                    FROM ".$db->quoteName('#__hwdms_ext')."
                                    WHERE ".$db->quoteName('media_type')." = ".$db->quote('4')."
                                    AND ".$db->quoteName('published')." = ".$db->quote('1')."
                                ";  
                                $db->setQuery($query);                               
                                if ($db->loadResult())
                                {
                                        $retval.= JText::_('COM_HWDMS_VIDEOS');
                                        if ($media_type != $last) 
                                        {
                                            $retval.= ', ';
                                        }
                                }
                                break;
                        }

                }
                return $retval;
	}

	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	public function getReadableAllowedExtensions($extensions)
	{
                $retval = "";
                $last = end($extensions);
                foreach($extensions as $extension)
                {
                    if ($extension == $last) 
                    {
                        $retval.= $extension;
                    }
                    else
                    {
                        $retval.= $extension.', ';
                    }
                }
                return $retval;
	}
}
