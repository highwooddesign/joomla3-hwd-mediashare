<?php
/**
 * @version    SVN $Id: remote.php 1458 2013-04-30 10:50:24Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      18-Feb-2012 12:10:10
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * hwdMediaShare framework upload class
 *
 * @package hwdMediaShare
 * @since   0.1
 */
class hwdMediaShareRemote extends JObject
{        
        /**
	 * @since	0.1
	 */
        public $elementType = 1;
        
        var $_id;
	var $_url;
        var $_host;
        var $_buffer;
        var $_title;
        var $_description;
        var $_source;
        var $_duration;
        var $_count = 0;

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
			$c = 'hwdMediaShareRemote';
                        $instance = new $c;
		}

		return $instance;
	}
        
	/**
	 * Method to process a remote media
         *
	 * @since   0.1
	 */
	public function addRemote()
	{
                $db =& JFactory::getDBO();
                $user = & JFactory::getUser();
                $app = & JFactory::getApplication();
                $date =& JFactory::getDate();

                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                hwdMediaShareFactory::load('upload');
                
                $data = JRequest::getVar('jform', array(), 'post', 'array');
                $urls = explode("\n", $data['remote']);

                // Add remote urls
                foreach($urls as $url)
                {
                        // Reset
                        $this->url = null;
                        $this->_id = null;
                        $this->_url = null;
                        $this->_host = null;
                        $this->_title = null;

                        // Set url
                        $this->url = $url;
                        
                        $error = false;
                        $host = null;

                        $key = hwdMediaShareUpload::generateKey();

                        if (!$this->getHost())
                        {
                                $this->setError(JText::_('COM_HWDMS_ERROR_NO_REMOTE_HOST'));
                                return false; 
                        }

                        if (!$this->getUrl())
                        {
                                $this->setError(JText::_('COM_HWDMS_ERROR_NO_REMOTE_URL'));
                                return false; 
                        }

                        if (hwdMediaShareUpload::keyExists($key))
                        {
                                $this->setError(JText::_('COM_HWDMS_KEY_EXISTS'));
                                return false; 
                        }

                        $remotePluginClass = $this->getRemotePluginClass($this->_host);
                        $remotePluginPath = $this->getRemotePluginPath($this->_host);

                        // Import hwdMediaShare plugins
                        JLoader::register($remotePluginClass, $remotePluginPath);
                        if (class_exists($remotePluginClass))
                        {
                                //$importer = $remotePluginClass::getInstance();
                                $importer = call_user_func(array($remotePluginClass, 'getInstance'));
                                $importer->url = $url;  
                        }
                        else
                        {
                                $this->setError(JText::_('COM_HWDMS_UNABLE_TO_IMPORT_FROM_'.$this->_host));
                                $this->setError(JText::sprintf('COM_HWDMS_UNABLE_TO_IMPORT_FROM_X', $this->_host));
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
                                $post['id']                     = $data['id'];
                                //$post['asset_id']             = '';
                                //$post['ext_id']               = '';
                                $post['media_type']             = $importer->mediaType;
                                //$post['key']                  = '';
                                //$post['title']                = '';
                                //$post['alias']                = '';
                                //$post['description']          = '';
                                $post['type']                   = 2; // Rtmp
                                $post['source']                 = $importer->getSource();
                                $post['storage']                = '';
                                $post['duration']               = $importer->getDuration();
                                $post['streamer']               = '';
                                $post['file']                   = '';
                                $post['embed_code']             = '';
                                $post['thumbnail']              = $importer->getThumbnail();
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
                                //$post['ext_id']               = '';
                                $post['media_type']             = $importer->mediaType;
                                $post['key']                    = $key;
                                $post['title']                  = $importer->getTitle();
                                $post['alias']                  = JFilterOutput::stringURLSafe($post['title']);
                                $post['description']            = $importer->getDescription();
                                $post['type']                   = 2; // Remote
                                $post['source']                 = $importer->getSource();
                                $post['storage']                = '';
                                $post['duration']               = $importer->getDuration();
                                $post['streamer']               = '';
                                $post['file']                   = '';
                                $post['embed_code']             = '';
                                $post['thumbnail']              = $importer->getThumbnail();
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

                        $this->_id = $row->id;
                        $this->_title = $row->title;
                        $this->_count++;

                        hwdMediaShareUpload::assignAssociations($row);

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
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/
	public function getHost()
	{
                if (empty($this->url))
                {
                        $data = JRequest::getVar('jform', array(), 'post', 'array');
                        $this->url = $data['remote']; 
                }

                $pattern = '`.*?((http|https|ftp)://[\w#$&+,\/:;=?@.-]+)[^\w#$&+,\/:;=?@.-]*?`i';
                if (preg_match($pattern, $this->url, $matches)) 
                {
                        $this->_url = $matches[1];
                        $this->_host = parse_url($this->_url, PHP_URL_HOST);
                        $this->_host = preg_replace('#^www\.(.+\.)#i', '$1', $this->_host);
                }

		return $this->_host;
	}
        
        /**
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/
	public function getUrl()
	{
                if (!$this->_url)
                {
                        $data = JRequest::getVar('jform', array(), 'post', 'array');

                        $pattern = '`.*?((http|ftp)://[\w#$&+,\/:;=?@.-]+)[^\w#$&+,\/:;=?@.-]*?`i';
                        if (preg_match($pattern, $data['remote'], $matches)) 
                        {
                                $this->_url = $matches[1];
                                $this->_host = parse_url($this->_url, PHP_URL_HOST);
                                $this->_host = preg_replace('#^www\.(.+\.)#i', '$1', $this->_host);
                        }
                }

		return $this->_url;
	}
        
        /**
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/
	public function getBuffer($url, $ssl=false)
	{  
                // A large number of CURL installations will not support SSL, so switch back to http
                if (!$ssl) $url = str_replace("https", "http", $url);
                
                if ($url)
                {
                        if (function_exists('curl_init'))
                        {
                                $useragent = "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1)";
                                $useragent = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0";

                                $curl_handle = curl_init();
                                curl_setopt($curl_handle, CURLOPT_URL, $url);
                                curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 30);
                                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
                                //curl_setopt($curl_handle, CURLOPT_VERBOSE, 1);
                                //curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, 1);
                                //curl_setopt($curl_handle, CURLOPT_MAXREDIRS, 5);
                                //curl_setopt($curl_handle, CURLOPT_HEADER, 1);
                                curl_setopt($curl_handle, CURLOPT_REFERER, $this->_host);
                                curl_setopt($curl_handle, CURLOPT_USERAGENT, $useragent);
                                $buffer = curl_exec($curl_handle);
                                curl_close($curl_handle);

                                if (!empty($buffer))
                                {
                                        return $buffer;
                                }
                                else
                                {
                                        return false;
                                }
                        }
                }

		return false;
	}
        
        /**
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/
	public function getTitle( $buffer )
	{
                jimport( 'joomla.filter.filterinput' );
                JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');
                
                // We will apply the most strict filter to the variable
                $noHtmlFilter = JFilterInput::getInstance();

                $title = false;
                
		// Check Open Graph tag
                preg_match('/<meta property="og:title" content="([^"]+)/', $buffer, $match);
                if (!empty($match[1]))
                {
                        $title = $match[1];
                        $title = (string)str_replace(array("\r", "\r\n", "\n"), '', $title);
			$title = $noHtmlFilter->clean($title);
                        $title = JHtmlString::truncate($title, 5120);
                        $title = trim($title);
                        
                        if ($title)
                        {
                                return $title;
                        }
                }
                
                // Check standard title tag
                preg_match("/<title>(.*)<\/title>/siU", $buffer, $match);
                if (!empty($match[1]))
                {
                        $title = $match[1];
                        $title = (string)str_replace(array("\r", "\r\n", "\n"), '', $title);
			$title = $noHtmlFilter->clean($title);
                        $title = JHtmlString::truncate($title, 255);
                        $title = trim($title);
                }
                
                return $title;
	}
        
        /**
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/
	public function getDescription( $buffer )
	{
                jimport( 'joomla.filter.filterinput' );
                JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');
                
                // We will apply the most strict filter to the variable
                $noHtmlFilter = JFilterInput::getInstance();

                $description = false;
                                
		// Check Open Graph tag
                preg_match('/<meta property="og:description" content="([^"]+)/', $buffer, $match);
                if (!empty($match[1]))
                {
                        $description = $match[1];
                        $description = (string)str_replace(array("\r", "\r\n", "\n"), '', $description);
			$description = $noHtmlFilter->clean($description);
                        $description = JHtmlString::truncate($description, 5120);
                        $description = trim($description);
                        
                        if ($description)
                        {
                                return $description;
                        }
                }
                
                // Check standard description meta tag
		preg_match('/<meta name="description" content="([^"]+)/', $buffer, $match);
                if (!empty($match[1]))
                {
                        $description = $match[1];
                        $description = (string)str_replace(array("\r", "\r\n", "\n"), '', $description);
			$description = $noHtmlFilter->clean($description);
                        $description = JHtmlString::truncate($description, 5120);
                        $description = trim($description);
                }
                
                return $description;
	}
       
        /**
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/
	public function getThumbnail( $buffer )
	{
                jimport( 'joomla.filter.filterinput' );
                JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');
                
                // We will apply the most strict filter to the variable
                $noHtmlFilter = JFilterInput::getInstance();

                $thumbnail = false;
                                
		// Check Open Graph tag
                preg_match('/<meta property="og:image" content="([^"]+)/', $buffer, $match);

                if (!empty($match[1]))
                {
                        $thumbnail = $match[1];
                        $thumbnail = (string)str_replace(array("\r", "\r\n", "\n"), '', $thumbnail);
			$thumbnail = $noHtmlFilter->clean($thumbnail);
                        $thumbnail = JHtmlString::truncate($thumbnail, 5120);
                        $thumbnail = trim($thumbnail);

                        hwdMediaShareFactory::load('utilities');
                        $utilities = hwdMediaShareUtilities::getInstance();
                        $isValid = $utilities->validateUrl( $thumbnail );
                        
                        if ($isValid)
                        {
                                return $thumbnail;
                        }
                        else
                        {
                                return false;
                        }
                }
                
                return $thumbnail;
	}
        
        /**
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/
	public function getDuration( $buffer )
	{
                jimport( 'joomla.filter.filterinput' );
                JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');
                
                // We will apply the most strict filter to the variable
                $noHtmlFilter = JFilterInput::getInstance();

                $duration = false;
                                
		// Check Open Graph tag
                preg_match('/<meta property="video:duration" content="([^"]+)/', $buffer, $match);
                if (!empty($match[1]))
                {
                        $duration = $match[1];
                        $duration = (int)$duration;
                }
                
                return $duration;
	}

        /**
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/
	public function getSource( )
	{
                jimport( 'joomla.filter.filterinput' );
                JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');
                
                // We will apply the most strict filter to the variable
                $noHtmlFilter = JFilterInput::getInstance();

                $source = $this->_url;
                $source = (string)str_replace(array("\r", "\r\n", "\n"), '', $source);
                $source = $noHtmlFilter->clean($source);
                $source = JHtmlString::truncate($source, 255);
                $source = trim($source);
                
                return $source;
	}
        
        /**
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/
	public function getRemotePluginClass($host)
	{
                return 'plgHwdmediashareRemote_'.preg_replace("/[^a-zA-Z0-9\s]/", "", $host);
	}
        
        /**
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/
	public function getRemotePluginPath($host)
	{
                return JPATH_PLUGINS . '/hwdmediashare/remote_'.preg_replace("/[^a-zA-Z0-9\s]/", "", $host).'/remote_'.preg_replace("/[^a-zA-Z0-9\s]/", "", $host).'.php';
	}
        
	/**
	 * Method to process a remote media
         *
	 * @since   0.1
	 */
	public function addImport()
	{
                $db =& JFactory::getDBO();
                $user = & JFactory::getUser();
                $app = & JFactory::getApplication();
                $date =& JFactory::getDate();

                // Load HWDMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                hwdMediaShareFactory::load('upload');
                hwdMediaShareFactory::load('files');

                $error = false;

                $folder = JRequest::getVar('folder', '', '', 'path');

		// Get some paths from the request
		$base = JPATH_SITE.'/media/'.$folder;

		// Get the list of folders
		jimport('joomla.filesystem.folder');
                jimport( 'joomla.filesystem.file' );
		$files = JFolder::files($base, '.', false, true);
                
		$count = 0;

		foreach ($files as $file)
		{
                        //Import filesystem libraries. Perhaps not necessary, but does not hurt
                        jimport('joomla.filesystem.file');
                        
                        //Retrieve file details
                        $ext = strtolower(JFile::getExt($file));

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
                                //Retrieve file details from uploaded file, sent from upload form
                                $ext = strtolower(JFile::getExt($file));
                                $key = hwdMediaShareUpload::generateKey();

                                if (empty($file) || empty($ext) || !file_exists($file))
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
                                        $src = $file;
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
                                                if ( JFile::copy($src, $dest) )
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

                                        if (hwdMediaShareUpload::keyExists($key))
                                        {
                                                $this->setError(JText::_('COM_HWDMS_KEY_EXISTS'));
                                                return false;
                                        }

                                        // Set approved/pending
                                        (!$app->isAdmin() && $config->get('approve_new_media')) == 1 ? $status = 2 : $status = 1; 
                                        $config->get('approve_new_media') == 1 ? $status = 2 : $status = 1; 

                                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                                        $row =& JTable::getInstance('media', 'hwdMediaShareTable');
 
                                        $post                          = array();
                                        $post['key']                   = $key;
                                        $post['media_type']            = '';
                                        // Check encoding of filename to prevent problems in the title
                                        if(mb_detect_encoding($file, 'iso-8859-1', true))
                                        {
                                                $post['title'] = hwdMediaShareUpload::removeExtension(basename(utf8_encode($file)));
                                        }
                                        else
                                        {
                                                $post['title'] = hwdMediaShareUpload::removeExtension(basename($file));
                                        }
                                        $post['alias']                 = JFilterOutput::stringURLSafe($post['title']);
                                        $post['ext_id']                = $ext_id;
                                        $post['description']           = '';
                                        $post['type']                  = 1;
                                        $post['status']                = $status;
                                        $post['published']             = 1;
                                        $post['featured']              = 0;
                                        $post['access']                = 1;
                                        $post['download']              = 1;
                                        $post['created_user_id']       = $user->id;
                                        $post['created_user_id_alias'] = '';
                                        $post['created']               = $date->format('Y-m-d H:i:s');
                                        $post['publish_up']            = $date->format('Y-m-d H:i:s');
                                        $post['publish_down']          = '0000-00-00 00:00:00';
                                        $post['hits']                  = 0;
                                        $post['language']              = '*';

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

                                hwdMediaShareFactory::load('events');
                                $events = hwdMediaShareEvents::getInstance();
                                $events->triggerEvent('onAfterMediaAdd', $row);

                                $count++;
                        }
		}   
                return true;      
        } 
        
	/**
	 * Method to process a remote media
         *
	 * @since   0.1
	 */
	public function addLink()
	{
                $db =& JFactory::getDBO();
                $user = & JFactory::getUser();
                $app = & JFactory::getApplication();
                $date =& JFactory::getDate();

                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                hwdMediaShareFactory::load('upload');
                hwdMediaShareFactory::load('files');

                //Retrieve file details from uploaded file, sent from upload form
                $data = JRequest::getVar('jform', array(), 'post', 'array');
                $type = $data['link_type'];
                $url = $data['link_url'];
                $title = basename($url);

                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                
		$isValid = $utilities->validateUrl( $url );
                if (!$isValid)
                {
                        $this->setError(JText::_('COM_HWDMS_ERROR_NO_REMOTE_HOST'));
                        return false; 
                }

                $key = hwdMediaShareUpload::generateKey();

                if (hwdMediaShareUpload::keyExists($key))
                {
                        $this->setError(JText::_('COM_HWDMS_KEY_EXISTS'));
                        return false; 
                }

                switch ($type) {
                    case 8:
                        $media_type = 1;
                        break;
                    case 5:
                        $media_type = 3;
                        break;
                    default:
                        $media_type = 4;
                        break;
                }
                
                // Set approved/pending
                (!$app->isAdmin() && $config->get('approve_new_media')) == 1 ? $status = 2 : $status = 1; 
                $config->get('approve_new_media') == 1 ? $status = 2 : $status = 1; 
                
                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                $row =& JTable::getInstance('Media', 'hwdMediaShareTable');

                $post                          = array();
              
                // Check if we need to replace an existing media item
                if ($data['id'] > 0 && $app->isAdmin() && $user->authorise('core.edit', 'com_hwdmediashare'))
                {
                        $post['id']                     = $data['id'];
                        //$post['asset_id']             = '';
                        //$post['ext_id']               = '';
                        $post['media_type']             = $media_type;
                        //$post['key']                  = '';
                        //$post['title']                = '';
                        //$post['alias']                = '';
                        //$post['description']          = '';
                        $post['type']                   = 7; // Remote file
                        $post['source']                 = $url;
                        $post['storage']                = $type;
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
                        //$post['ext_id']               = '';
                        $post['media_type']             = $media_type;
                        $post['key']                    = $key;
                        $post['title']                  = (empty($title) ? 'New media' :$title);
                        $post['alias']                  = JFilterOutput::stringURLSafe($post['title']);
                        //$post['description']          = '';
                        $post['type']                   = 7; // Remote file
                        $post['source']                 = $url;
                        $post['storage']                = $type;
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
                        $post['published']              = 0;
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

                $this->_id = $row->id;
                $this->_title = $row->title;

                hwdMediaShareUpload::assignAssociations($row);

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
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	public function getReadableAllowedRemotes()
	{
                $sites = array();
                
                // Load all hwdMediaShare plugins
                $db =& JFactory::getDBO();
                $query = $db->getQuery(true);
                $query->select('*');
                $query->from('#__extensions AS a');
                $query->where('a.type = '.$db->quote('plugin'));
                $query->where('a.folder = '.$db->quote('hwdmediashare'));
                $db->setQuery($query);
                $rows = $db->loadObjectList();            

                // Loop all plugins and check if a remote plugin
		for( $i = 0; $i < count($rows); $i++ )
		{
			$row = $rows[$i];

                        if( substr($row->element, 0, 7) == 'remote_' )
			{
                                // Get the url
                                $file= JPATH_SITE.'/plugins/hwdmediashare/'.$row->element.'/'.$row->element.'.xml';
                                if (file_exists($file))
                                {
                                        $xml =& JFactory::getXML($file);                                        
                                        if ($xml->url)
                                        {
                                                $sites[] = $xml->url;                                    
                                        }
                                }
			}
		}
                
                $return = '';  
                $last = end($sites);                
                foreach ($sites as $site) {
                    $return.= '<a href="'.$site.'" target="_blank">'.str_replace("http://", "", $site).'</a>';
                    if ($site != $last) 
                    {
                        $return.= ', ';
                    }                    
                }
		return $return;
	}        
}
                