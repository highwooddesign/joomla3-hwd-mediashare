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

class hwdMediaShareRemote extends JObject
{        
	/**
	 * The variable to hold the item details.
         * 
         * @access      public
	 * @var         object
	 */
	public $_item;
        
	/**
	 * The variable to hold the import count.
         * 
         * @access      public
	 * @var         integer
	 */
        public $_count = 0;
        
	/**
	 * The variables to hold the url and host for the import.
         * 
         * @access      public
	 * @var         string
	 */        
	public $_url;
        public $_host;

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
	 * Returns the hwdMediaShareRemote object, only creating it if it
	 * doesn't already exist.
         * 
	 * @access  public
         * @static
	 * @return  hwdMediaShareRemote Object.
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
	 * Method to process a remote media.
         * 
         * @access  public
         * @return  boolean True on success.
	 */
	public function addRemote()
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
                if (!$user->authorise('hwdmediashare.import', 'com_hwdmediashare'))
                {
                        $this->setError(JText::_('COM_HWDMS_ERROR_NOAUTHORISED'));
                        return false;
                }   
                
                // Retrieve filtered jform data.
                hwdMediaShareFactory::load('upload');
                $data = hwdMediaShareUpload::getProcessedUploadData();
                
                // Check if we are processing a single upload, and redefine.
                if (!isset($data['remotes']) && isset($data['remote'])) 
                {
                        $data['remotes'] = $data['remote'];
                }

                // Check if we are processing a single upload, and redefine.
                if (!isset($data['remotes'])) 
                {
                        $this->setError(JText::_('COM_HWDMS_ERROR_NO_REMOTE_HOST'));
                        return false; 
                }
                
                // Get urls to import.
                $urls = explode("\n", $data['remotes']); 
                
                // Loop over urls and add attempt to import.
                foreach($urls as $url)
                {
                        // Reset library data.
                        $this->_url = null;
                        $this->_host = null;

                        // Set current url.
                        $this->_url = trim($url);
                        
                        // Skip empty lines.
                        if (empty($this->_url) || !$utilities->validateUrl($this->_url))
                        {
                                continue;
                        }

                        if (!$this->getHost())
                        {
                                $this->setError(JText::_('COM_HWDMS_ERROR_NO_REMOTE_HOST'));
                                return false; 
                        }

                        $remotePluginClass = $this->getRemotePluginClass($this->_host);
                        $remotePluginPath = $this->getRemotePluginPath($this->_host);

                        // Import HWD remote plugin.
                        JLoader::register($remotePluginClass, $remotePluginPath);
                        if (class_exists($remotePluginClass))
                        {
                                $importer = call_user_func(array($remotePluginClass, 'getInstance'));
                                $importer->_url = $this->_url;
                                $importer->_host = $importer->getHost();
                                $importer->_buffer = $importer->getBuffer($importer->_url);         
                        }
                        else
                        {
                                // If we can't find a suitable plugin, then look for top level domain plugin.
                                $remotePluginClass = $this->getRemotePluginClass($this->getDomain());
                                $remotePluginPath = $this->getRemotePluginPath($this->getDomain());

                                JLoader::register($remotePluginClass, $remotePluginPath);
                                if (class_exists($remotePluginClass))
                                {    
                                        $importer = call_user_func(array($remotePluginClass, 'getInstance'));
                                        $importer->_url = $this->_url;
                                        $importer->_host = $importer->getHost();
                                        $importer->_buffer = $importer->getBuffer($importer->_url); 
                                }
                                else
                                {
                                        $this->setError(JText::_('COM_HWDMS_UNABLE_TO_IMPORT_FROM_'.$this->_host));
                                        $this->setError(JText::sprintf('COM_HWDMS_UNABLE_TO_IMPORT_FROM_X', $this->_host));
                                        return false; 
                                }
                        }
                        
                        // Set approved/pending.
                        (!$app->isAdmin() && $config->get('approve_new_media')) == 1 ? $status = 2 : $status = 1; 
                        $config->get('approve_new_media') == 1 ? $status = 2 : $status = 1; 
       
                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                        $table = JTable::getInstance('Media', 'hwdMediaShareTable');

                        $post = array();
                
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
                                        
                                //$post['id']                   = '';
                                //$post['asset_id']             = '';
                                //$post['ext_id']               = '';
                                $post['media_type']             = $importer->mediaType;
                                //$post['key']                  = '';
                                //$post['title']                = '';
                                //$post['alias']                = '';
                                //$post['description']          = '';
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
                                //$post['ext_id']               = '';
                                $post['media_type']             = $importer->mediaType;
                                //$post['key']                  = '';
                                $post['title']                  = $importer->getTitle();
                                $post['alias']                  = (isset($data['alias']) ? JFilterOutput::stringURLSafe($data['alias']) : JFilterOutput::stringURLSafe($post['title']));
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

                        // Save the data to the database.
                        if (!$table->save($post))
                        {
                                $this->setError($table->getError());
                                return false; 
                        }

                        $properties = $table->getProperties(1);
                        $this->_item = JArrayHelper::toObject($properties, 'JObject');
                        $this->_count++;
                        
                        // Import tags.
                        $tags = (array) $importer->getTags();
                        $joomlaTags = $this->createTags($tags);
                        if (count($joomlaTags))
                        {
                                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                                $tagTable = JTable::getInstance('Media', 'hwdMediaShareTable');
                                $tagTable->load($this->_item->id);                            
                                $tagsObserver = $tagTable->getObserverOfClass('JTableObserverTags');
                                $result = $tagsObserver->setNewTags($joomlaTags, false);
                        }
                }     
                        
                return true;
        } 
        
        /**
	 * Method to obtain the host from the url.
         * 
         * @access  public
         * @return  string  The host of the import url.
	 */
	public function getHost()
	{
                $pattern = "/(http|https)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
                if (preg_match($pattern, $this->_url, $matches)) 
                {
                        $this->_host = parse_url($this->_url, PHP_URL_HOST);
                        $this->_host = preg_replace('#^www\.(.+\.)#i', '$1', $this->_host);
                }

		return $this->_host;
	}
        
        /**
	 * Method to obtain the top level domain from the url.
         * 
         * @access  public
         * @return  string  The domain of the import url.
	 */
	public function getDomain()
	{
                $host = parse_url($this->_url, PHP_URL_HOST);
                if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $host, $regs))
                {
                        return $regs['domain'];
                }
                return false;
	} 
        
        /**
	 * Method to obtain the url from the request.
         * 
         * @access  public
         * @return  string  The host of the import url.
	 */
	public function getUrl()
	{
                // Initialise variables.
                $app = JFactory::getApplication();    
                
                // Load HWD utilities.
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                
                if (!$this->_url)
                {
                        // Retrieve filtered jform data.
                        $jform = $app->input->getArray(array(
                            'jform' => array(
                                'remote' => 'string'
                            )
                        ));

                        $data = $jform['jform'];
           
                        // Validate url.
                        if (!empty($data['remote']) && $utilities->validateUrl($data['remote']))
                        {
                                $this->_url = $data['remote'];
                        }
                }

		return $this->_url;
	}
        
        /**
	 * Method to request the contents of the url.
         * 
         * @access  public
         * @param   string  $url    The url to request.
         * @param   boolean $ssl    Request secure connection.
         * @return  string  The buffer.
	 */
	public function getBuffer($url, $ssl = true)
	{  
                // Check if curl supports ssl connections.
                $version = curl_version();
                $ssl_supported = ($version['features'] && CURL_VERSION_SSL);
                if (!$ssl || !$ssl_supported) $url = str_replace("https", "http", $url);

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
                                curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
                                curl_setopt($curl_handle, CURLOPT_REFERER, $this->_host);
                                curl_setopt($curl_handle, CURLOPT_USERAGENT, $useragent);
                                $buffer = curl_exec($curl_handle);
                                curl_close($curl_handle);
                                
                                // Display curl error.
                                if ($buffer === false) 
                                {
                                        $this->setError('Curl error #'.curl_errno($curl_handle).': ' . curl_error($curl_handle));
                                        return false;
                                }
                                
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
	 * Method to extract the title from the request buffer.
         * 
         * @access  public
         * @param   string  $buffer The buffer of the remote source.
         * @return  string  The title of the remote media.
	 */
	public function getTitle($buffer = null)
	{
                if (empty($buffer)) $buffer = $this->getBuffer($this->_url);

		// Check OpenGraph tag.
                preg_match('/<meta property="og:title" content="([^"]+)/', $buffer, $match);
                if (!empty($match[1]))
                {
                        if ($title = $this->clean($match[1], 255))
                        {
                                return $title;                             
                        }
                }
                
		// Check title tag.
                preg_match('/<meta name="title" content="([^"]+)/', $buffer, $match);               
                if (!empty($match[1]))
                {
                        if ($title = $this->clean($match[1], 255))
                        {
                                return $title;                             
                        }
                }
                
                // Check standard title tag.
                preg_match("/<title>(.*)<\/title>/siU", $buffer, $match);
                if (!empty($match[1]))
                {
                        if ($title = $this->clean($match[1], 255))
                        {
                                return $title;                             
                        }
                }
                
                return false;
	}
        
	/**
	 * Method to extract the description from the request buffer.
         * 
         * @access  public
         * @param   string  $buffer The buffer of the remote source.
         * @return  string  The description of the remote media.
	 */
	public function getDescription($buffer = null)
	{
                if (empty($buffer)) $buffer = $this->getBuffer($this->_url);
                              
		// Check OpenGraph tag.
                preg_match('/<meta property="og:description" content="([^"]+)/', $buffer, $match);
                if (!empty($match[1]))
                {
                        if ($description = $this->clean($match[1]))
                        {
                                return $description;                             
                        }
                }
                
                // Check standard description meta tag.
		preg_match('/<meta name="description" content="([^"]+)/', $buffer, $match);
                if (!empty($match[1]))
                {
                        if ($description = $this->clean($match[1]))
                        {
                                return $description;                             
                        }
                }
                
                return false;
	}
       
	/**
	 * Method to extract the thumbnail location from the request buffer.
         * 
         * @access  public
         * @param   string  $buffer The buffer of the remote source.
         * @return  string  The thumbnail of the remote media.
	 */
	public function getThumbnail($buffer = null)
	{
                if (empty($buffer)) $buffer = $this->getBuffer($this->_url);

                // Load HWD utilities.
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                
		// Check OpenGraph tag.
                preg_match('/<meta property="og:image" content="([^"]+)/', $buffer, $match);
                if (!empty($match[1]))
                {
                        if ($thumbnail = $this->clean($match[1], 255))
                        {
                                if ($utilities->validateUrl($thumbnail))
                                {
                                        return $thumbnail; 
                                }                            
                        }
                }
                
                return false;
	}
        
	/**
	 * Method to extract the tags from the request buffer.
         * 
         * @access  public
         * @param   string  $buffer The buffer of the remote source.
         * @return  array   An array of tags.
	 */
	public function getTags($buffer = null)
	{
                if (empty($buffer)) $buffer = $this->getBuffer($this->_url);
                
                // We will filter input from the buffer.
                $noHtmlFilter = JFilterInput::getInstance();

                $tags = array();

                // Check standard keyword meta tag.
		preg_match('/<meta name="keywords" content="([^"]+)/', $buffer, $match);
                if (!empty($match[1]))
                {
                        $tagString = $match[1];
                        $tagArray = explode(",", $tagString);
                        
                        foreach($tagArray as $tagElement)
                        {
                                if ($tag = $this->clean($tagElement))
                                {
                                        $tags[] = $tag;     
                                }                               
                        }
                }
                else
                {
                        preg_match_all('/<meta property="video:tag" content="([^"]+)/', $buffer, $match);
                        if (!empty($match[1]) && is_array($match[1]))
                        {
                                foreach ($match[1] as $tagElement)
                                {
                                        if ($tag = $this->clean($tagElement))
                                        {
                                                $tags[] = $tag;     
                                        } 
                                }
                        }
                }
                      
                return $tags;
	}
        
	/**
	 * Method to extract the duration from the request buffer.
         * 
         * @access  public
         * @param   string  $buffer The buffer of the remote source.
         * @return  integer The duration of the remote media.
	 */
	public function getDuration($buffer = null)
	{             
                if (empty($buffer)) $buffer = $this->getBuffer($this->_url);
            
		// Check OpenGraph tag.
                preg_match('/<meta property="video:duration" content="([^"]+)/', $buffer, $match);
                if (!empty($match[1]))
                {
                        return (int)$match[1];
                }

                return false;
	}

	/**
	 * Method to process the source of the remote media.
         * 
         * @access  public
         * @return  string  The source of the remote media.
	 */
	public function getSource()
	{
                // We will filter input from the buffer.
                $noHtmlFilter = JFilterInput::getInstance();

                $source = $this->_url;
                $source = (string)str_replace(array("\r", "\r\n", "\n"), '', $source);
                $source = $noHtmlFilter->clean($source);
                $source = JHtml::_('string.truncate', $source, 255);
                $source = trim($source);

                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                        
                if ($utilities->validateUrl($source)) return $source;
	}
        
	/**
	 * Method to construct the plugin class name.
         * 
         * @access  public
         * @static
         * @param   string  $host   The host of the remote url.
         * @return  string  The plugin class name.
	 */
	public static function getRemotePluginClass($host)
	{
                return 'plgHwdmediashareRemote_'.preg_replace("/[^a-zA-Z0-9\s]/", "", $host);
	}
        
	/**
	 * Method to construct the full path to the plugin file.
         * 
         * @access  public
         * @static
         * @param   string  $host   The host of the remote url.
         * @return  string  The plugin file location.
	 */
	public static function getRemotePluginPath($host)
	{
                return JPATH_PLUGINS . '/hwdmediashare/remote_'.preg_replace("/[^a-zA-Z0-9\s]/", "", $host).'/remote_'.preg_replace("/[^a-zA-Z0-9\s]/", "", $host).'.php';
	}
        
	/**
	 * Method to import media files from a server directory.
         * 
         * @access  public
         * @return  boolean True on success.
	 */
	public function addImport()
	{
                // Initialise variables.            
                $db = JFactory::getDBO();
                $user = JFactory::getUser();
                $app = JFactory::getApplication();
                $date = JFactory::getDate();

                // Load HWD config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // Load HWD utilities.
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                
                // Load HWD libraries.
                hwdMediaShareFactory::load('upload');
                hwdMediaShareFactory::load('files');

                // Check authorised.
                if (!$user->authorise('hwdmediashare.import', 'com_hwdmediashare'))
                {
                        $this->setError(JText::_('COM_HWDMS_ERROR_NOAUTHORISED'));
                        return false;
                } 
                
                // Get folder to import from request.
                $folder = $app->input->get('folder', '', 'path');
		$base = JPATH_SITE.'/media/'.$folder;

                if (!file_exists($base))
                {
                        $this->setError(JText::_('COM_HWDMS_ERROR_NOAUTHORISED'));
                        return false;
                } 
                
		// Get the list of folders.
		jimport('joomla.filesystem.folder');
                jimport( 'joomla.filesystem.file' );
		$files = JFolder::files($base, '.', false, true);

		foreach ($files as $file)
		{
                        // Retrieve file details.
                        $ext = strtolower(JFile::getExt($file));

                        // Check if the file has an allowed extension.
                        $query = $db->getQuery(true)
                                ->select('id')
                                ->from('#__hwdms_ext')
                                ->where($db->quoteName('ext') . ' = ' . $db->quote($ext))
                                ->where($db->quoteName('published') . ' = ' . $db->quote(1));
                        try
                        {
                                $db->setQuery($query);
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
                                $maxUploadFileSize = $config->get('max_upload_filesize', 30) * 1024 * 1024;                       
                                if (filesize($file) > $maxUploadFileSize)
                                {
                                        $app->enqueueMessage(JText::sprintf('COM_HWDMS_FILE_N_EXCEEDS_THE_MAX_UPLOAD_LIMIT', basename($file)));
                                        continue; // We just want to skip files that can't be imported.
                                }
                            
                                // Define a key so we can copy the file into the storage directory.
                                if (!$key = $utilities->generateKey(1))
                                {
                                        $this->setError($utilities->getError());
                                        return false;
                                }  
                                        
                                if (empty($file) || empty($ext) || !file_exists($file))
                                {
                                        $this->setError(JText::_('COM_HWDMS_ERROR_PHP_UPLOAD_ERROR'));
                                        return false;
                                }
                                else
                                {
                                        hwdMediaShareFiles::getLocalStoragePath();

                                        $folders = hwdMediaShareFiles::getFolders($key);
                                        hwdMediaShareFiles::setupFolders($folders);

                                        // Get the filename.
                                        $filename = hwdMediaShareFiles::getFilename($key, 1);

                                        // Get the destination location, and copy the uploaded file.
                                        $dest = hwdMediaShareFiles::getPath($folders, $filename, $ext);
                                        if (!JFile::copy($file, $dest))
                                        {
                                                $this->setError(JText::_('COM_HWDMS_ERROR_FILE_COULD_NOT_BE_COPIED_TO_UPLOAD_DIRECTORY'));
                                                return false;
                                        }

                                        // Set approved/pending.
                                        (!$app->isAdmin() && $config->get('approve_new_media')) == 1 ? $status = 2 : $status = 1; 
                                        $config->get('approve_new_media') == 1 ? $status = 2 : $status = 1; 

                                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                                        $table = JTable::getInstance('media', 'hwdMediaShareTable');
 
                                        $post                          = array();
                                        $post['key']                   = $key;
                                        $post['media_type']            = '';
                                        // Check encoding of original filename to prevent problems in the title.
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
                                        $post['type']                  = 1; // Local.
                                        $post['status']                = $status;
                                        $post['published']             = 1;
                                        $post['featured']              = 0;
                                        $post['access']                = 1;
                                        $post['download']              = 1;
                                        $post['created_user_id']       = $user->id;
                                        $post['created_user_id_alias'] = '';
                                        $post['created']               = $date->toSql();
                                        $post['publish_up']            = $date->toSql();
                                        $post['publish_down']          = '0000-00-00 00:00:00';
                                        $post['hits']                  = 0;
                                        $post['language']              = '*';

                                        // Save data to the database.
                                        if (!$table->save($post))
                                        {
                                                $this->setError($row->getError());
                                                return false;
                                        }                                         
                                }
                                     
                                $properties = $table->getProperties(1);
                                $this->_item = JArrayHelper::toObject($properties, 'JObject');
                                        
                                hwdMediaShareFactory::load('files');
                                $HWDfiles = hwdMediaShareFiles::getInstance();
                                $HWDfiles->addFile($this->_item, 1);

                                hwdMediaShareUpload::addProcesses($this->_item);

                                $this->_count++; 
                        }
                        else
                        {
                                continue; // We just want to skip files that can't be imported.
                        }                        
		}   
                return true;      
        } 
        
	/**
	 * Method to process a remote link.
         * 
         * @access  public
         * @return  boolean True on success.
	 */
	public function addLink()
	{
                // Initialise variables.            
                $db = JFactory::getDBO();
                $user = JFactory::getUser();
                $app = JFactory::getApplication();
                $date = JFactory::getDate();
                $jinput = JFactory::getApplication()->input;
                
                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
 
                // Load HWD utilities.
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                
                // Load HWD libraries.
                hwdMediaShareFactory::load('upload');
                hwdMediaShareFactory::load('files');
                
                // Check authorised.
                if (!$user->authorise('hwdmediashare.import', 'com_hwdmediashare'))
                {
                        $this->setError(JText::_('COM_HWDMS_ERROR_NOAUTHORISED'));
                        return false;
                }   
                
                // Retrieve filtered jform data.
                hwdMediaShareFactory::load('upload');
                $data = hwdMediaShareUpload::getProcessedUploadData();
                
                if (!$utilities->validateUrl($data['link_url']))
                {
                        $this->setError(JText::_('COM_HWDMS_ERROR_NO_REMOTE_HOST'));
                        return false; 
                }
                
                // Set approved/pending.
                (!$app->isAdmin() && $config->get('approve_new_media')) == 1 ? $status = 2 : $status = 1; 
                $config->get('approve_new_media') == 1 ? $status = 2 : $status = 1; 
                
                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                $table = JTable::getInstance('Media', 'hwdMediaShareTable');

                $post = array();
              
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

                        //$post['id']                   = '';
                        //$post['asset_id']             = '';
                        $post['ext_id']                 = ((isset($data['link_ext']) && $data['link_ext'] > 0) ? $data['link_ext'] : '');
                        $post['media_type']             = $data['link_type'];
                        //$post['key']                  = '';
                        //$post['title']                = '';
                        //$post['alias']                = '';
                        //$post['description']          = '';
                        $post['type']                   = 7; // Remote file.
                        $post['source']                 = $data['link_url'];
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
                        $post['ext_id']                 = ((isset($data['link_ext']) && $data['link_ext'] > 0) ? $data['link_ext'] : '');
                        $post['media_type']             = $data['link_type'];
                        //$post['key']                  = '';
                        $post['title']                  = (isset($data['title']) ? $data['title'] : basename($data['link_url']));
                        $post['alias']                  = (isset($data['alias']) ? JFilterOutput::stringURLSafe($data['alias']) : JFilterOutput::stringURLSafe($post['title']));
                        $post['description']            = (isset($data['description']) ? $data['description'] : '');
                        $post['type']                   = 7; // Remote file
                        $post['source']                 = $data['link_url'];
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
                        $post['language']               = (isset($jform['language']) ? $jform['language'] : '*');                              
                }

                // Save the data to the database.
                if (!$table->save($post))
                {
                        $this->setError($table->getError());
                        return false; 
                }

                $properties = $table->getProperties(1);
                $this->_item = JArrayHelper::toObject($properties, 'JObject');

                return true;
        } 
        
	/**
	 * Method to render a list of allowed remote websites.
         * 
         * @access  public
         * @static
         * @return  boolean True on success.
	 */
	public static function getReadableAllowedRemotes()
	{
                $sites = array();
                
                // Load all HWD remote plugins.
                $db = JFactory::getDBO();
                $query = $db->getQuery(true)
                        ->select('*')
                        ->from('#__extensions')
                        ->where('type = '.$db->quote('plugin'))
                        ->where('folder = '.$db->quote('hwdmediashare'))
                        ->where('enabled = '.$db->quote(1));
                try
                {
                        $db->setQuery($query);
                        $db->execute(); 
                        $rows = $db->loadObjectList();                   
                }
                catch (RuntimeException $e)
                {
                        $this->setError($e->getMessage());
                        return false;                            
                }                

                // Loop all plugins and check if a remote plugin.
		for($i = 0; $i < count($rows); $i++)
		{
			$row = $rows[$i];

                        if(substr($row->element, 0, 7) == 'remote_')
			{
                                // Get the supported url from the xml manifest.
                                $file= JPATH_SITE.'/plugins/hwdmediashare/'.$row->element.'/'.$row->element.'.xml';
                                if (file_exists($file))
                                {
                                        $xml = JFactory::getXML($file);                                        
                                        if ($xml->url)
                                        {
                                                $sites[] = $xml->url;                                    
                                        }
                                }
			}
		}
                
                $return = array();  
                foreach ($sites as $site) 
                {
                        $return[] = '<a href="'.$site.'" target="_blank">'.str_replace("http://", "", $site).'</a>';
                }
                
		return implode(", ", $return);
	}
        
	/**
	 * Method to load and create any new tags.
         * 
         * @access  public
	 * @param   array  $tags  Tags text array.
	 * @return  mixed  If successful, Joomla tag array. Otherwise false.
	 */
	public function createTags($tags = array())
	{
                array_walk($tags, function(&$value, $key) { $value = '#new#' . $value; });
                $tagsHelper = new JHelperTags;
                $tagsHelper->createTagsFromField($tags);                                
                return $tagsHelper->tags;
	}
        
	/**
	 * Method to clean strings extracted from buffers.
         * 
         * @access  public
	 * @param   string  $string  The string to clean.Tags text array.
	 * @return  mixed   If successful then the cleaned string. Otherwise false.
	 */
	public function clean($string, $truncate = 5120)
	{
                // We will filter input from the buffer.
                $noHtmlFilter = JFilterInput::getInstance();

                $string = (string) str_replace(array("\r", "\r\n", "\n"), '', $string);
                $string = $noHtmlFilter->clean($string);
                $string = JHtml::_('string.truncate', $string, $truncate);
                $string = trim($string);
                                
                if (!empty($string))
                {
                        return $string;
                }
                
                return false;
	}
}
                