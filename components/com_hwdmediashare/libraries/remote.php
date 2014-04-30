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
	 * Library data
	 * @var array
	 */
	public $_item;
	public $_url;
        public $_host;
        public $_count = 0;

    	/**
	 * Constructor override, defines a white list of column filters.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 */
	public function __construct($config = array())
	{
	}

	/**
	 * Returns the hwdMediaShareRemote object, only creating it if it
	 * doesn't already exist.
	 *
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
         * @return	void
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
                                $importer->_url = $url;  
                        }
                        else
                        {
                                // If we can't find a suitable plugin, then look for top level domain plugin
                                $remotePluginClass = $this->getRemotePluginClass($this->getDomain());
                                $remotePluginPath = $this->getRemotePluginPath($this->getDomain());

                                JLoader::register($remotePluginClass, $remotePluginPath);
                                if (class_exists($remotePluginClass))
                                {    
                                        $importer = call_user_func(array($remotePluginClass, 'getInstance'));
                                        $importer->_url = $url;  
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
                                $post['id']                     = $data['id'];
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
                }     
                        
                return true;
        } 
        
        /**
	 * Method to obtain the host from the url.
         * @return	void
	 **/
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
         * @return	void
	 **/
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
         * @return	void
	 **/
	public function getUrl()
	{
                // Initialise variables.
                $app = JFactory::getApplication();    
                
                // Load HWD utilities.
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                
                if (!$this->_url)
                {
                        // Retrieve fitlered jform data
                        $jform = $app->input->getArray(array(
                            'jform' => array(
                                'remote' => 'string'
                            )
                        ));

                        $data = $jform['jform'];
           
                        // Validate url
                        if (!empty($data['remote']) && $utilities->validateUrl($data['remote']))
                        {
                                $this->_url = $data['remote'];
                        }
                }

		return $this->_url;
	}
        
        /**
	 * Method to request the contents of the url
         * @return	void
	 **/
	public function getBuffer($url, $ssl=false)
	{  
                // Check if curl supports ssl connections 
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
                                
                                // Display curl error
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
         * @return	void
	 */
	public function getTitle($buffer)
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
                        $title = JHtmlString::truncate($title, 255);
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
	 * Method to extract the description from the request buffer.
         * @return	void
	 */
	public function getDescription($buffer)
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
                
                // Check standard description meta tag.
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
	 * Method to extract the thumbnail location from the request buffer.
         * @return	void
	 */
	public function getThumbnail($buffer)
	{
                jimport( 'joomla.filter.filterinput' );
                JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');
                
                // We will apply the most strict filter to the variable
                $noHtmlFilter = JFilterInput::getInstance();
          
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
                        
                        if ($utilities->validateUrl($thumbnail))
                        {
                                return $thumbnail;
                        }
                }
                
                return false;
	}
        
	/**
	 * Method to extract the duration from the request buffer.
         * @return	void
	 */
	public function getDuration($buffer)
	{
                jimport( 'joomla.filter.filterinput' );
                JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');
                
                // We will apply the most strict filter to the variable
                $noHtmlFilter = JFilterInput::getInstance();
             
		// Check Open Graph tag
                preg_match('/<meta property="video:duration" content="([^"]+)/', $buffer, $match);
                if (!empty($match[1]))
                {
                        $duration = (int)$match[1];
                }
                
                return false;
	}

	/**
	 * Method to process a remote media.
         * @return	void
	 */
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
	 * Method to construct the plugin class name.
         * @return	void
	 */
	public function getRemotePluginClass($host)
	{
                return 'plgHwdmediashareRemote_'.preg_replace("/[^a-zA-Z0-9\s]/", "", $host);
	}
        
	/**
	 * Method to construct the full path to the plugin file.
         * @return	void
	 */
	public function getRemotePluginPath($host)
	{
                return JPATH_PLUGINS . '/hwdmediashare/remote_'.preg_replace("/[^a-zA-Z0-9\s]/", "", $host).'/remote_'.preg_replace("/[^a-zA-Z0-9\s]/", "", $host).'.php';
	}
        
	/**
	 * Method to import media files from a server directory.
         * @return	void
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
                
                // Load HWD utiltiies
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                
                // Load HWD libraries
                hwdMediaShareFactory::load('upload');
                hwdMediaShareFactory::load('files');

                // Check authorised.
                if (!$user->authorise('hwdmediashare.import', 'com_hwdmediashare'))
                {
                        $this->setError(JText::_('COM_HWDMS_ERROR_NOAUTHORISED'));
                        return false;
                } 
                
                // Get folder to import from request
                $folder = $app->input->get('folder', '', 'path');
		$base = JPATH_SITE.'/media/'.$folder;

                if (!file_exists($base))
                {
                        $this->setError(JText::_('COM_HWDMS_ERROR_NOAUTHORISED'));
                        return false;
                } 
                
		// Get the list of folders
		jimport('joomla.filesystem.folder');
                jimport( 'joomla.filesystem.file' );
		$files = JFolder::files($base, '.', false, true);

		foreach ($files as $file)
		{
                        // Retrieve file details
                        $ext = strtolower(JFile::getExt($file));

                        // Check if the file has an allowed extension
                        $query = $db->getQuery(true)
                                ->select('id')
                                ->from('#__hwdms_ext')
                                ->where($db->quoteName('ext') . ' = ' . $db->quote($ext))
                                ->where($db->quoteName('published') . ' = ' . $db->quote(1));
                        try
                        {
                                $db->setQuery($query);
                                $db->query(); 
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
                            
                                // Define a key so we can copy the file into the storage directory
                                $key = $utilities->generateKey();

                                if ($utilities->keyExists($key))
                                {
                                        $this->setError(JText::_('COM_HWDMS_KEY_EXISTS'));
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

                                        // Set approved/pending
                                        (!$app->isAdmin() && $config->get('approve_new_media')) == 1 ? $status = 2 : $status = 1; 
                                        $config->get('approve_new_media') == 1 ? $status = 2 : $status = 1; 

                                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                                        $table = JTable::getInstance('media', 'hwdMediaShareTable');
 
                                        $post                          = array();
                                        $post['key']                   = $key;
                                        $post['media_type']            = '';
                                        // Check encoding of original filename to prevent problems in the title
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
                                        $post['type']                  = 1; // Local
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

                                        // Save data to the database
                                        if (!$table->save($post))
                                        {
                                                $this->setError($row->getError());
                                                return false;
                                        }                                         
                                }
                                     
                                $properties = $table->getProperties(1);
                                $this->_item = JArrayHelper::toObject($properties, 'JObject');
                                        
                                hwdMediaShareFactory::load('files');
                                hwdMediaShareFiles::add($this->_item, 1);

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
         * @return	void
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
 
                // Load HWD utiltiies.
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
                if ($data['id'] > 0 && $app->isAdmin() && $user->authorise('core.edit', 'com_hwdmediashare'))
                {
                        $post['id']                     = $data['id'];
                        //$post['asset_id']             = '';
                        $post['ext_id']                 = ((isset($data['link_ext']) && $data['link_ext'] > 0) ? $data['link_ext'] : '');
                        $post['media_type']             = $data['link_type'];
                        //$post['key']                  = '';
                        //$post['title']                = '';
                        //$post['alias']                = '';
                        //$post['description']          = '';
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
         * @return	void
	 */
	public function getReadableAllowedRemotes()
	{
                $sites = array();
                
                // Load all HWD remote plugins
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
                        $db->query(); 
                        $rows = $db->loadObjectList();                   
                }
                catch (RuntimeException $e)
                {
                        $this->setError($e->getMessage());
                        return false;                            
                }                

                // Loop all plugins and check if a remote plugin
		for($i = 0; $i < count($rows); $i++)
		{
			$row = $rows[$i];

                        if(substr($row->element, 0, 7) == 'remote_')
			{
                                // Get the supported url from the xml manifest
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
}
                