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
	 * Holds the HWD config.
         * 
         * @access  public
	 * @var     object
	 */
        public $_config;
        
	/**
	 * Holds the HWD component parameters.
         * 
         * @access  public
	 * @var     object
	 */
        public $_params;

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
         * @param   string  $path  The class name to look for (dot notation).
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
	 * Method to load HWD MediaShare configuration.
         *
         * @access  public
         * @static
         * @param   object   $params  Additional parameters to merge with the configuration.
         * @param   boolean  $reset   Flag to reset the config to the default values.
         * @return  void
	 */
        public function getConfig($params = null, $reset = false)
        {
                // Initialise variables.            
                $app = JFactory::getApplication();
                
                // Get the frontend application parameters.
                $appParams = $app->isSite() && !defined('_JCLI') ? $app->getParams('com_hwdmediashare') : null;
                
                // Check if the config is already loaded, or if we're resetting.
                if(!$this->_config || $reset)
		{
                        jimport('joomla.filesystem.file');
                        $ini	= JPATH_ROOT . '/administrator/components/com_hwdmediashare/config.ini';
                        $data	= JFile::read($ini);

                        // Load default configuration.
                        $this->_config = new JRegistry($data);

                        JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_hwdmediashare/tables');
                        $table = JTable::getInstance('Configuration', 'hwdMediaShareTable');

                        // Attempt to load the default configuration row.
                        $return = $table->load('1');

                        // Check for a table object error.
                        if ($return === false && $table->getError())
                        {
                                $this->setError($table->getError());
                                return false;
                        }

                        // Load and manipulate the saved configuration.
                        $config = json_decode($table->params);                       
                        if (isset($config->upload_limits))
                        {
                                $config->upload_limits = json_encode($config->upload_limits);
                        }
                        
                        /**
                         * Here we define the HWD configuration registry, with the fallback
                         * and override as follows:
                         * 
                         * 1) Start with the global user saved configuration.
                         * 2) Merge the application parameters (menu parameters)
                         * 3) Merge the passed parameters (item parameters).
                         */
                        
                        // Load the user saved configuration.
                        $this->_config->loadObject($config);

                        // Merge the application parameters.
                        if (is_object($appParams))
                        {
                                $this->_config->merge($appParams, true);
                        }
                        
                        // Merge the passed parameters.
                        if (is_object($params))
                        {
                                $this->_config->merge($params, true);
                        }

                        // Load component parameters and create JRegistry object (this is really a 
                        // dummy registry as no fields are defined in the component config.xml).
                        $this->_params = JComponentHelper::getParams('com_hwdmediashare');

                        // Load our configuration.
                        $this->_params->merge($this->_config, true); 
                        
                        // Merge the 'debug' parameter from the Global Configuration.
                        $this->_params->set('debug', $app->getCfg('debug'));                           
                }

                //$config = JRegistryFormatJSON::stringToObject($this->_config);
                //$config = json_decode($this->_config, true);

                return $this->_params;
        }

	/**
	 * Method to allow caller to get a user object while
         * it is not authenticated provided that it has a proper tokenid.
         *
         * @access  public
         * @static
         * @param   string   $tokenId  The token id.
         * @param   integer  $userId   The user id.
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
}
