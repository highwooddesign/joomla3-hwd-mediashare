<?php
/**
 * @version    SVN $Id: utilities.php 1623 2013-08-14 14:03:42Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      20-Mar-2012 10:29:20
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * hwdMediaShare framework upload class
 *
 * @package hwdMediaShare
 * @since   0.1
 */
class hwdMediaShareUtilities extends JObject
{
	/**
	 * Callback for escaping.
	 *
	 * @var string
	 */
	protected $escape = 'htmlspecialchars';

	/**
	 * Callback for escaping.
	 *
	 * @var string
	 * @deprecated use $escape declare as private
	 */
	protected $_escape = 'htmlspecialchars';

	/**
	 * Charset to use in escaping mechanisms; defaults to urf8 (UTF-8)
	 *
	 * @var string
	 */
	protected $charset = 'UTF-8';

	/**
	 * Charset to use in escaping mechanisms; defaults to urf8 (UTF-8)
	 *
	 * @var string
	 * @deprecated use $charset declare as private
	 */
	protected $_charset = 'UTF-8';

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
	 * @param   mixed  $var  The output to escape.
	 *
	 * @return  mixed  The escaped value.
	 *
	 * @since   11.1
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
	 * Method to validate an email
	 *
	 * @since   0.1
	 */
	public function validateEmail($data, $strict = false) 
	{
		$regex = $strict ? '/^([.0-9a-z_-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,4})$/i' : '/^([*+!.&#$¦\'\\%\/0-9a-z^_`{}=?~:-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,4})$/i'; 
		
		if(preg_match($regex, JString::trim($data), $matches))
		{
			return array($matches[1], $matches[2]); 
		}
		else
		{ 
			return false; 
		} 
	}
        
	/**
	 * Method to validate an url
	 *
	 * @since   0.1
	 */
        public function validateUrl($url)
        {
                return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
        }
        
	/**
	 * Method to get the URL of an avatar
	 *
	 * @since   0.1
	 */
        public function getAvatar($user)
        {
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

		if (!$user) return JURI::root( true ).'/media/com_hwdmediashare/assets/images/default-avatar.png';
                
                if ($config->get('community_avatar') == 'cb')
                {                   
                        $db =& JFactory::getDBO();
                        $query = $db->getQuery(true);
                        $query->select('avatar');
                        $query->from('#__comprofiler');
                        $query->where('user_id = '.$user->id);
                        $db->setQuery($query);
                        $db->query();                
                        $cbAvatar = $db->loadResult();
 
                        if (!empty($cbAvatar) && file_exists(JPATH_ROOT.'/images/comprofiler/tn'.$cbAvatar))
                        {
                                return JURI::root( true ).'/images/comprofiler/tn'.$cbAvatar;
                        }
                        else if (!empty($cbAvatar) && file_exists(JPATH_ROOT.'/images/comprofiler/'.$cbAvatar))
                        {
                                return JURI::root( true ).'/images/comprofiler/'.$cbAvatar;
                        }                        
                }
                else if ($config->get('community_avatar') == 'jomsocial' && file_exists(JPATH_ROOT.'/components/com_community/libraries/core.php'))
                {
                        include_once(JPATH_ROOT.'/components/com_community/libraries/core.php');
                        $JSUser = CFactory::getUser($user->id);
                        return $JSUser->getThumbAvatar();
                }
                else if ($config->get('community_avatar') == 'gravatar' && isset($user->email))
                {
                        return "http://www.gravatar.com/avatar/".md5( strtolower( trim( $user->email ) ) );                    
                }
                else if ($config->get('community_avatar') == 'jomwall')
                {
                        include_once (JPATH_ROOT.DS.'components'.DS .'com_awdwall' .DS . 'helpers' . DS . 'user.php');
                        return AwdwallHelperUser::getBigAvatar51($user->id);	
                }
                
                if (!isset($user->key))
                {
                        // Load user
                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                        $table =& JTable::getInstance('UserChannel', 'hwdMediaShareTable');
                        $table->load( $user->id );

                        $properties = $table->getProperties(1);
                        $user = JArrayHelper::toObject($properties, 'JObject');

                }
                    
                if (isset($user->key))
                {
                        hwdMediaShareFactory::load('files');
                        hwdMediaShareFiles::getLocalStoragePath();

                        $folders = hwdMediaShareFiles::getFolders($user->key);
                        $filename = hwdMediaShareFiles::getFilename($user->key, 10);
                        $ext = hwdMediaShareFiles::getExtension($user, 10);

                        $path = hwdMediaShareFiles::getPath($folders, $filename, $ext);

                        if (file_exists($path))
                        {
                                return hwdMediaShareFiles::getUrl($folders, $filename, $ext);
                        }
                }
                
                return JURI::root( true ).'/media/com_hwdmediashare/assets/images/default-avatar.png';
        }
        
	/**
	 * Method to get the URL of an avatar
	 *
	 * @since   0.1
	 */
        public function sendSystemEmail($emailSubject, $emailBody)
        {
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                $db =& JFactory::getDBO();
                $app =& JFactory::getApplication();
                
                // get all admin users
                $query = 'SELECT name, email, sendEmail, id' .
                         ' FROM #__users' .
                         ' WHERE sendEmail=1';

                $db->setQuery( $query );
                $rows = $db->loadObjectList();

                // Send mail to all users with users creating permissions and receiving system emails
                foreach( $rows as $row )
                {
                        $usercreator = JFactory::getUser($id = $row->id);
                        if ($usercreator->authorise('core.create', 'com_users'))
                        {
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
	 * Method to get the size of a modal window
	 *
	 * @since   0.1
	 */
        public function modalSize($size="small")
        {
                JLoader::register('hwdMediaShareHelperMobile', JPATH_ROOT.'/components/com_hwdmediashare/helpers/mobile.php');
                $mobile = hwdMediaShareHelperMobile::getInstance();
                if ($mobile->_isIpad)
                {
                        $retval = "x: 500, y: 400";
                }
                elseif ($mobile->_isMobile)
                {
                        $retval = "x: 220, y: 220";
                }
                else
                {
                        if ($size == "small")
                        {
                                $retval = "x: 400, y: 350";    
                        }
                        else
                        {
                                $retval = "x: 800, y: 500";
                        } 
                }
                return $retval;
        }
        
	/**
	 * Method to get the width of a media item
	 *
	 * @since   1.0.2
	 */
        public function getMediaWidth()
        {
                // Load hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                JLoader::register('hwdMediaShareHelperMobile', JPATH_ROOT.'/components/com_hwdmediashare/helpers/mobile.php');
                $mobile = hwdMediaShareHelperMobile::getInstance();

                if ($mobile->_isIpad)
                {
                        return "620";
                }
                elseif ($mobile->_isMobile)
                {
                        return "300";
                }
                else
                {
                        return JRequest::getInt('mediaitem_size', $config->get('mediaitem_size', '500'));
                }
        }
        
	/**
	 * Convert links from relative to absolute
	 */
	public function relToAbs($url)
	{ 
                $url = strpos($url,'http') === 0 ? $url : rtrim((isset($_SERVER['HTTPS']) ? 'https://' : 'http://').$_SERVER['SERVER_NAME'], '/') . '/' . ltrim($url, '/');
		return $url;
	}    
        
	/**
	 * Convert links from relative to absolute
	 */
	public function printModalNotice($error, $description=null, $reload=false)
	{ 
                $document = JFactory::getDocument();            
                $document->addStyleDeclaration('#main,.contentpane { margin: 0!important; padding: 0!important; } #system-message-container { display:none; }');                    
                // Start output
                ob_start();
                ?>
                <div id="hwd-container" style="visibility:hidden;">
                <h2><?php echo JText::_($error); ?></h2>
                <p><?php echo JText::_($description); ?></p>
                </div>

<script type='text/javascript'>
<?php if ($reload) : ?>
setTimeout('window.parent.location.reload(true);',3000);
<?php else : ?>
setTimeout('window.parent.SqueezeBox.close();',5000);
<?php endif; ?>
parent.document.getElementById('sbox-content').getElement('iframe').setStyles({'width':'100%','height':'100%','overflow':'hidden'});
var delayID;
var begin = function() {
  delayID = (function() {
    //do all the rotation stuff here
    var size = $('hwd-container').getSize();
    var width = 400;
    var height = size.y + 10;
    parent.document.getElementById('sbox-content').getElement('iframe').setStyles({'width':'100%','height':'100%','overflow':'hidden'});
    window.parent.SqueezeBox.resize({x:width,y:height},false);    
    $('hwd-container').setStyles({'visibility':'visible'});
  }).delay(100);
}
begin();
</script> 
                <?php
                $html = ob_get_contents();
                ob_end_clean();
                print $html;
                return true;
	}           
 
}
