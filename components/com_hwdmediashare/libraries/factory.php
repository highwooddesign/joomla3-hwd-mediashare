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

class hwdMediaShareFactory extends JObject
{
	/**
	 * The variable to hold the HWD config.
         * 
         * @access      public
	 * @var         object
	 */
        public $_config;
        
	/**
	 * The variable to hold the Joomla HWD component parameters.
         * 
         * @access      public
	 * @var         object
	 */
        public $_params;

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
	 * Returns the hwdMediaShareFactory object, only creating it if it
	 * doesn't already exist.
	 *
	 * @access  public
         * @static
	 * @return  hwdMediaShareFactory Object.
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
	 * Method to load and register a HWD MediaShare library file.
         * 
         * @access  public
         * @static
         * @param   string  $path   The class name to look for (dot notation).
         * @return  void
	 */
	public static function load($path)
	{
                jimport('joomla.filesystem.file');
                $parts = explode('.', $path);

                $name = array_pop($parts);

                // Check if file exists.
		$path = JPATH_ROOT.'/components/com_hwdmediashare/libraries/'.strtolower(str_replace('.', '/', $path)).'.php';

                if(JFile::exists($path))
		{
			include_once($path);
		}

                $className = 'hwdMediaShare'.$name;
		if(class_exists($className))
                {
                        JLoader::register($className, $path);
		}
	}
        
	/**
	 * Method to allow caller to get a user object while
         * it is not authenticated provided that it has a proper tokenid.
         *
         * @access  public
         * @static
         * @param   string  $tokenId    The token id.
         * @param   integer $userId     The user id.
         * @return  void
	 */
	public static function getUserFromTokenId($tokenId, $userId)
	{
                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                        ->select('COUNT(*)')
                        ->from('#__hwdms_upload_tokens')
                        ->where('token = ' . $db->quote($tokenId))
                        ->where('userid = ' . $db->quote($userId));
                try
                {
                        $db->setQuery($query);
                        $count	= $db->loadResult();
                }
                catch (RuntimeException $e)
                {
                        $this->setError($e->getMessage());
                        return false;                            
                }

                // We assume that the user parsed in correct token and userid. So,
		// we return them the proper user object.
		if ($count > 0)
		{
			$user = JFactory::getUser($userId);
                        return $user;
		}
                
		// If it doesn't bypass our tokens, we assume they are really trying
		// to hack or got in here somehow.
		$user = JFactory::getUser(null);
		return $user;
	}
        
	/**
	 * Method to load HWD MediaShare configuration.
         *
         * @access  public
         * @static
         * @param   object  $params Additional parameters to merge with the configuration.
         * @return  void
	 */
        public function getConfig($params = null)
        {
                // Initialise variables.            
                $app = JFactory::getApplication();
                
                // Check if the config is already loaded.
                if(!$this->_config)
		{
                        jimport('joomla.filesystem.file');
                        $ini	= JPATH_ROOT . '/administrator/components/com_hwdmediashare/config.ini';
                        $data	= JFile::read($ini);

                        // Load default configuration.
                        $this->_config = new JRegistry($data);

                        JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_hwdmediashare/tables');
                        $table = JTable::getInstance('Configuration', 'hwdMediaShareTable');

                        // Attempt to load the row.
                        $return = $table->load('1');

                        // Check for a table object error.
                        if ($return === false && $table->getError())
                        {
                                $this->setError($table->getError());
                                return false;
                        }

                        // Bind the user saved configuration.
                        $this->_config->loadObject(json_decode($table->params));
                        $this->_config->loadObject($params);

                        // Load component parameters and create JRegistry object.
                        $this->_params = JComponentHelper::getParams('com_hwdmediashare');
                        
                        // Load our configuration.
                        $this->_params->merge($this->_config);    
                        
                        // Merge and override with menu and system parameters (if in frontend).
                        if (!defined('_JCLI') && !$app->isAdmin()) $this->_params->merge($app->getParams());
                }

                //$config = JRegistryFormatJSON::stringToObject($this->_config);
                //$config = json_decode($this->_config, true);

                return $this->_params;
        }
        
	/**
	 * Method to get human readable element type.
         *
         * @access  public
         * @static
         * @param   object  $item   The item to check.
         * @return  string  The human readable element type.
	 */
        public static function getElementType($item)
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
}
