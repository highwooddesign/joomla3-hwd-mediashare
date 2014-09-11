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

class hwdMediaShareUtilities extends JObject
{
	/**
	 * Callback for escaping.
         * 
         * @access      protected
	 * @var         string
	 */
	protected $_escape = 'htmlspecialchars';

	/**
	 * Charset to use in escaping mechanisms; defaults to urf8 (UTF-8)
         * 
         * @access      protected
	 * @var         string
	 */
	protected $_charset = 'UTF-8';

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
	 * Returns the hwdMediaShareUtilities object, only creating it if it
	 * doesn't already exist.
	 *
	 * @access  public
         * @static
	 * @return  hwdMediaShareUtilities Object.
	 */
	public static function getInstance()
	{
		static $instance;

		if (!isset ($instance))
                {
			$c = 'hwdMediaShareUtilities';
                        $instance = new $c;
		}

		return $instance;
	}
        
	/**
	 * Escapes a value for output in a view script.
	 *
	 * If escaping mechanism is either htmlspecialchars or htmlentities, uses
	 * {@link $_encoding} setting.
	 *
         * @access  public
	 * @param   mixed  $var  The output to escape.
	 * @return  mixed  The escaped value.
	 */
	public function escape($var)
	{
		if (in_array($this->_escape, array('htmlspecialchars', 'htmlentities')))
		{
			return call_user_func($this->_escape, $var, ENT_COMPAT, $this->_charset);
		}

		return call_user_func($this->_escape, $var);
	}
        
	/**
	 * Method to validate string as an email.
	 *
         * @access  public
	 * @param   mixed    $string  The input to validate.
	 * @return  boolean  True if validated, false if not.
	 */
	public function validateEmail($string) 
	{
                if (filter_var($string, FILTER_VALIDATE_EMAIL))
                { 
                        return true;
                }

                return false;
	}
        
	/**
	 * Method to validate string as an url.
	 *
         * @access  public
	 * @param   mixed    $string  The input to validate.
	 * @return  boolean  True if validated, false if not.
	 */
        public function validateUrl($string)
        {
                if (filter_var($string, FILTER_VALIDATE_URL))
                { 
                        return true;
                }

                return false;
        }
        
	/**
	 * Method to send an email to all users configured to receive system emails.
	 *
         * @access  public
	 * @param   string   $emailSubject  The subject for the email.
	 * @param   string   $emailBody     The body for the email.
	 * @return  boolean  True if successful, false if not.
	 */
        public function sendSystemEmail($emailSubject, $emailBody)
        {
                // Initialise variables.
                $db = JFactory::getDBO();
                $app = JFactory::getApplication();
                
                // Load HWD config.                
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                // Load all users configured to receive system emails.
                $query = $db->getQuery(true)
                        ->select('name, email, sendEmail, id')
                        ->from('#__users')
                        ->where('sendEmail = ' . $db->quote(1));
                try
                {                
                        $db->setQuery($query);
                        $rows = $db->loadObjectList();
                }
                catch (Exception $e)
                {
                        $this->setError($e->getMessage());
                        return false;
                }

                foreach($rows as $row)
                {
                        $user = JFactory::getUser($id = $row->id);
                        if ($user->authorise('core.create', 'com_users'))
                        {
                                // Send email.
                                $return = JFactory::getMailer()->sendMail($app->getCfg('mailfrom'), $app->getCfg('fromname'), $row->email, $emailSubject, $emailBody, true);

                                // Check for an error.
                                if ($return !== true) 
                                {
                                        $this->setError(JText::_('COM_HWDMS_SEND_MAIL_FAILED'));
                                        return false;
                                }
                        }
                }

                return true;
        }
        
	/**
	 * Method to convert an URL from relative to absolute.
	 *
         * @access  public
	 * @param   string  $url  The URL to process.
	 * @return  string  The processed URL.
	 */
	public function relToAbs($url)
	{ 
                $url = strpos($url,'http') === 0 ? $url : rtrim((isset($_SERVER['HTTPS']) ? 'https://' : 'http://').$_SERVER['SERVER_NAME'], '/') . '/' . ltrim($url, '/');
		return $url;
	}    
        
	/**
	 * Method to print a notice.
	 *
         * @access  public
	 * @param   string  $heading      The notice heading.
	 * @param   string  $description  The notice description.
	 * @param   string  $type         The type of noticed, used as class.
	 * @return  string  The rendered notice.
	 */
	public function printNotice($statement, $description = null, $type = 'info')
	{ 
                ob_start();
                ?>
                <div class="alert alert-<?php echo $type; ?>">
                  <h4 class="alert-heading"><?php echo $statement; ?></h4>
                  <p><?php echo $description; ?></p>
                </div>
                <?php
                $html = ob_get_contents();
                ob_end_clean();
                return $html;
	} 
        
	/**
	 * Method to print a notice in a modal window.
	 *
         * @access  public
	 * @param   string  $heading      The notice heading.
	 * @param   string  $description  The notice description.
	 * @return  string  The rendered notice.
	 */
	public function printModalNotice($heading, $description = null)
	{ 
                ob_start();
                ?>
                <div id="hwd-container" class="hwd-modal"> <a name="top" id="top"></a>
                  <h2 class="media-modal-title"><?php echo JText::_($statement); ?></h2>
                  <p class="media-modal-description"><?php echo JText::_($description); ?></p>
                </div>
                <?php
                $html = ob_get_contents();
                ob_end_clean();
                print $html;
	}           

	/**
	 * Method to generate a unique key.
	 *
         * @access  public
	 * @return  string  The key.
	 */
        public function generateKey($element = 1)
        {
                // Initialise variables.
                $db = JFactory::getDBO();
                $app = JFactory::getApplication();
            
                // Generate key.
                mt_srand(microtime(true)*100000 + memory_get_usage(true));
                $key = md5(uniqid(mt_rand(), true));
                
                // Define value array.
                $values = array(1 => 'media', 2 => 'album', 3 => 'group', 4 => 'playlist', 5 => 'channel');

                // Check if key exists.
                $query = $db->getQuery(true)
                        ->select('1')
                        ->from('#__hwdms_' . $values[$element])
                        ->where($db->quoteName('key') . ' = ' . $db->quote($key));
                try
                {
                        $db->setQuery($query);
                        $exists = $db->loadResult();                        
                }
                catch (RuntimeException $e)
                {
                        $this->setError($e->getMessage());
                        return false;                            
                }
                
                if ($exists) 
                {
                        $this->setError(JText::_('COM_HWDMS_KEY_EXISTS'));
                        return false;   
                }
                
                return $key;
        }

	/**
	 * Method to automatically generate a channel.
	 *
         * @access  public
	 * @param   integer  $key  The primary channel key (the user ID).
	 * @return  boolean  True on success, false on fail.
	 */
	public function autoCreateChannel($pk)
	{
                // Initialiase variables.
                $app = JFactory::getApplication();
                $user = JFactory::getUser();
                $db = JFactory::getDBO();
                $date = JFactory::getDate();
                
		// Sanitize the id.
		$pk = (int) $pk;

                // Get HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // Load HWD utilities.
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                
                if (empty(JFactory::getUser($pk)->id))
                {
			$this->setError(JText::_('COM_HWDMS_ERROR_NOAUTHORISED'));
			return false;
                }

                $query = $db->getQuery(true)
                        ->select('1')
                        ->from('#__hwdms_users')
                        ->where('id = ' . $db->quote($pk));
                try
                {                
                        $db->setQuery($query);
                        $db->execute(); 
                        $exists = $db->loadResult();
                }
                catch (Exception $e)
                {
                        $this->setError($e->getMessage());
                        return false;
                }

                if(!$exists)
                {
                        if (!$key = $utilities->generateKey(1))
                        {
                                $this->setError($utilities->getError());
                                return false;
                        }  
                        
                        $title = $config->get('author') == 0 ? JFactory::getUser($pk)->name : JFactory::getUser($pk)->username; 
                        $alias = JApplication::stringURLSafe($title); 
                        $status = (!$app->isAdmin() && $config->get('approve_new_channels') == 1) ? 2 : 1;
                        
                        // Insert columns.
                        $columns = array('id', 'key', 'title', 'alias', 'status', 'published', 'access', 'created_user_id', 'created', 'language');

                        // Insert values.
                        $values = array($pk, $key, $title, $alias, $status, 1, 1, $user->id, $date->toSql(), '*');

                        // Prepare the insert query.
                        $query = $db->getQuery(true)
                                ->insert($db->quoteName('#__hwdms_users'))
                                ->columns($db->quoteName($columns))
                                ->values(implode(',', $db->quote($values)));
                        try
                        {                
                                $db->setQuery($query);
                                $db->execute();
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
	 * Method to get a human readable status.
	 *
         * @access  public
	 * @param   object  $item  The item to process.
	 * @return  string  The human readable string for the item status.
	 */
	public function getReadableStatus($item)
	{
                switch ($item->status)
                {
                        case 0:
                                return JText::_('COM_HWDMS_UNAPPROVED');
                        break;
                        case 1:
                                return JText::_('COM_HWDMS_APPROVED');
                        break;
                        case 2:
                                return JText::_('COM_HWDMS_PENDING');
                        break;
                        case 3:
                                return JText::_('COM_HWDMS_REPORTED');
                        break;
                }
	}   

	/**
	 * Method to get a human readable element type.
	 *
         * @access  public
	 * @param   object  $item  The item to process.
	 * @return  string  The human readable string for the item element type.
	 */
        public static function getElementType($item)
        {
                switch ($item->element_type)
                {
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
                                return JText::_('COM_HWDMS_CHANNEL');
                        break;
                }
        }         
}