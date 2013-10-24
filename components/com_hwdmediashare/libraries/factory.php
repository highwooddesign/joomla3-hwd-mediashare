<?php
/**
 * @version    SVN $Id: factory.php 1545 2013-06-11 10:45:04Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * hwdMediaShare framework factory class
 *
 * @package hwdMediaShare
 * @since   0.1
 */
class hwdMediaShareFactory
{
	/**
	 * Configuration data
	 *
	 * @var object
	 **/
        var $_config;
        var $_params;

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
	 * Returns the hwdMediaShareFactory object, only creating it if it
	 * doesn't already exist.
	 *
	 * @return  hwdMediaShareFactory A hwdMediaShareFactory object.
	 * @since   0.1
	 */
	public static function getInstance()
	{
		static $instance;

		if (!isset ($instance))
                {
			$c = 'hwdMediaShareFactory';
                        $instance = new $c;
		}

		return $instance;
	}

	/**
	 * Method to load and register a hwdMediaShare library file
         * 
         * @since   0.1
	 **/
	public static function load( $filePath )
	{
                jimport('joomla.filesystem.file');
                $parts = explode('.', $filePath);

                $name = array_pop($parts);

                // Test if file really exists before php throws errors.
		$path = JPATH_ROOT.'/components/com_hwdmediashare/libraries/'.strtolower(str_replace('.', '/', $filePath)).'.php';

                if( JFile::exists( $path ) )
		{
			include_once( $path );
		}

                $className = 'hwdMediaShare'.$name;
		if(class_exists($className))
                {
                        JLoader::register($className, $path);
		}
	}
        
	/**
	 * Method to allow caller to get a user object while
         * it is not authenticated provided that it has a proper tokenid
         * 
         * @since   0.1
	 **/
	public function getUserFromTokenId( $tokenId , $userId )
	{
		$db =& JFactory::getDBO();

		$query	= 'SELECT COUNT(*) '
                                . 'FROM ' . $db->quoteName( '#__hwdms_upload_tokens') . ' '
				. 'WHERE ' . $db->quoteName( 'token') . '=' . $db->Quote( $tokenId ) . ' '
				. 'AND ' . $db->quoteName( 'userid') . '=' . $db->Quote( $userId );

		$db->setQuery( $query );

		$count	= $db->loadResult();

                // We assume that the user parsed in correct token and userid. So,
		// we return them the proper user object.

		if ( $count >= 1 )
		{
			$user =& JFactory::getUser( $userId );

                        return $user;
		}

		// If it doesn't bypass our tokens, we assume they are really trying
		// to hack or got in here somehow.

		$user	=& JFactory::getUser( null );

		return $user;
	}
        
	/**
	 * Method to load hwdMediaShare configuraiton
         * 
         * @since   0.1
	 **/
        public function getConfig($params=null)
        {
                jimport( 'joomla.html.parameter' );
                
                // Test if the config is already loaded.
                if( !$this->_config )
		{
                        jimport( 'joomla.filesystem.file');
                        $ini	= JPATH_ROOT.'/administrator/components/com_hwdmediashare/config.ini';
                        $data	= JFile::read($ini);

                        // Load default configuration
                        $this->_config	= new JRegistry( $data );

                        JTable::addIncludePath(JPATH_ROOT.'/administrator/components/com_hwdmediashare/tables');
                        $config = JTable::getInstance('Configuration', 'hwdMediaShareTable');
                        if (method_exists($config, 'load'))
                        {
                                //if (!$config->load('config'))
                                if (!$config->load('1'))
                                {
                                        return JError::raiseWarning( 500, $config->getError() );
                                }
                        }

                        // Bind the user saved configuration.
                        $this->_config->loadObject(json_decode($config->params));
                        $this->_config->loadObject($params);

                        // Load component parameters and create JRegistry object
                        $this->_params = JComponentHelper::getParams('com_hwdmediashare');
                        // Load our configuration
                        $this->_params->merge($this->_config);                        
                        // Merge and override with menu and system parameters (if in site)
                        if (!defined('_JCLI') && !JFactory::getApplication()->isAdmin()) $this->_params->merge( JFactory::getApplication()->getParams() );
                }

                //$config = JRegistryFormatJSON::stringToObject($this->_config);
                //$config = json_decode($this->_config, true);

                return $this->_params;
        }
        
	/**
	 * Method to get human readable element type
         * 
         * @since   0.1
	 **/
        function getElementType($item)
        {
                switch ($item->element_type) {
                    case 1:
                        return JText::_('COM_HWDMS_MEDIA');
                        break;
                    case 2:
                        return JText::_('COM_HWDMS_ALBUM');
                        break;
                    case 3:
                        return JText::_('COM_HWDMS_GROUP');
                        break;
                    case 4:
                        return JText::_('COM_HWDMS_PLAYLIST');
                        break;
                    case 5:
                        return JText::_('COM_HWDMS_USER_CHANNEL');
                        break;
                }
        }
        
	/**
	 * Method to generate a key
         * 
         * @since   0.1
	 **/
        function generateKey()
        {
                mt_srand(microtime(true)*100000 + memory_get_usage(true));
                return md5(uniqid(mt_rand(), true));
        }
        
	/**
	 * Method to get url of custom thumbnail for item
         * 
         * @since   0.1
	 **/
	public function getElementThumbnail($item, $elemmentId=1)
	{
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                if ($elemmentId == 6)
                {
                        $params = new JRegistry( $item->params );
                        $image = $params->get('image');
                        if (!empty($image))
                        {
                                return $image;
                        }
                        else
                        {
                                return false;
                        }
                }
                        
                if (empty($item->key))
                {
                        return false;
                }
                
                hwdMediaShareFactory::load('files');
                hwdMediaShareFiles::getLocalStoragePath();
                
                $folders = hwdMediaShareFiles::getFolders($item->key);
                $filename = hwdMediaShareFiles::getFilename($item->key, 10);
                $ext = hwdMediaShareFiles::getExtension($item, 10);

                $path = hwdMediaShareFiles::getPath($folders, $filename, $ext);

                if (file_exists($path))
                {
                        if ($config->get('protect_media') == 1)
                        {
                                hwdMediaShareFactory::load('downloads');
                                return hwdMediaShareDownloads::protectedUrl($item->id, 10, $elemmentId);
                        }
                        else
                        {
                                return hwdMediaShareFiles::getUrl($folders, $filename, $ext);
                        }
                }
                else
                {
                        return false;
                }
	}
        
        /**
	 * Method to assign user to a single record
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function autoCreateChannel($id)
	{
		// Initialise variables.
		$user		= JFactory::getUser($id);
                $db             =& JFactory::getDBO();
		$date           =& JFactory::getDate();
                $id		= (int) $id;

                if ($id > 0 && $user->id)
                {
                        $query = "SELECT id FROM `#__hwdms_users` WHERE id = $id";
                        $db->setQuery($query);
                        $userExists = $db->loadResult();

                        if (!$userExists)
                        {
                                $app = JFactory::getApplication();
                                $hwdms = hwdMediaShareFactory::getInstance();
                                $config = $hwdms->getConfig();

                                // if (!$app->isAdmin() && $config->get('approve_new_user_channels') == 1) 
                                if ($config->get('approve_new_user_channels') == 1) 
                                { 
                                        $status = 2;
                                }
                                else
                                {
                                        $status = 1;
                                }

                                hwdMediaShareFactory::load('upload');
                                $key = hwdMediaShareUpload::generateKey();
                
                                $query = 'INSERT INTO `#__hwdms_users` (`id`) VALUES ('.$id.')';
                                $db->setQuery($query);
                                $db->query();
                                
                                $row =& JTable::getInstance('UserChannel', 'hwdMediaShareTable');

                                // Create an array to bind to the database
                                $post                          = array();
                                $post['id']                    = $id;
                                $post['key']                   = $key;
                                $post['published']             = 1;
                                $post['status']                = $status;
                                $post['access']                = 1;
                                $post['created']               = $date->format('Y-m-d H:i:s');

                                if (!$row->bind($post))
                                {
                                        return JError::raiseWarning( 500, $row->getError() );
                                }

                                if (!$row->store())
                                {
                                        JError::raiseError(500, $row->getError() );
                                }
                        } 
                }

		return true;
	}        
}
