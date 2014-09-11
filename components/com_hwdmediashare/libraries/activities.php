<?php
/**
 * @package     Joomla.site
 * @subpackage  Component.hwdmediashare
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 * @inspiration https://github.com/justquick/django-activity-stream
 */

defined('_JEXEC') or die;

class hwdMediaShareActivities extends JObject
{
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
	 * Returns the hwdMediaShareActivities object, only creating it if it
	 * doesn't already exist.
	 *
	 * @access  public
         * @static
	 * @return  hwdMediaShareActivities Object.
	 */
	public static function getInstance()
	{
		static $instance;

		if (!isset ($instance))
                {
			$c = 'hwdMediaShareActivities';
                        $instance = new $c;
		}

		return $instance;
	}
        
	/**
	 * Method to save an activity.
         * 
         * @access  public
         * @param   object  $activity The activity object.
         * @return  boolean True on success.
	 */
	public function save($activity)
	{
                // Initialise variables.            
                $db = JFactory::getDBO();
                $user = JFactory::getUser();
                $app = JFactory::getApplication();
                $date = JFactory::getDate();

                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                $table = JTable::getInstance('Activity', 'hwdMediaShareTable');

                $post = array();
                
                $post['actor']                  = (int) $user->id;
                $post['action']                 = (int) $activity->action;
                $post['target']                 = (int) $activity->target;
                $post['verb']                   = (int) $activity->verb;
                $post['created']                = $date->toSql();
                $post['access']                 = (int) 1;

                // Save the data to the database.
                if (!$table->save($post))
                {
                        $this->setError($table->getError());
                        return false; 
                }
        
                return true;
	}
        
	/**
	 * Method to get human readable activity type.
         * 
         * @access  public
         * @static
         * @param   object  $activity The activity object.
         * @return  string  The human readable string. 
	 */
        public static function getActivityType($activity)
        {
                switch ($activity->verb)
                {
                        case 2:
                                return JText::_('COM_HWDMS_X_UPLOADED_A_NEW_MEDIA');
                        break;    
                        case 3:
                                return JText::_('COM_HWDMS_X_CREATED_A_NEW_ALBUM');
                        break;   
                        case 4:
                                return JText::_('COM_HWDMS_X_CREATED_A_NEW_GROUP');
                        break; 
                        case 5:
                                return JText::_('COM_HWDMS_X_CREATED_A_NEW_PLAYLIST');
                        break;
                        case 6:
                                return JText::_('COM_HWDMS_X_CREATED_A_NEW_CHANNEL');
                        break; 
                        case 7:
                                return JText::_('COM_HWDMS_X_JOINED_A_GROUP');
                        break; 
                        case 8:
                                return JText::_('COM_HWDMS_X_LEFT_A_GROUP');
                        break;  
                        case 9:
                                return JText::_('COM_HWDMS_X_SHARED_MEDIA_WITH_A_GROUP');
                        break;                    
                }
        }
        
	/**
	 * Method to render html for a single activity.
         * 
         * @access  public
         * @static
         * @param   object  $activity The activity object.
         * @return  string  The html for the activity. 
	 */
	public static function renderActivityHtml($activity)
	{
                // Initialise variables.            
                $db = JFactory::getDBO();
                $user = JFactory::getUser();
                $app = JFactory::getApplication();
                $date = JFactory::getDate();

                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                if (!isset($activity) || !is_object($activity))
                {        
                        return;
                }                

                // Load HWD table path.
                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');

                switch ($activity->verb) 
                {
                        case 2: // New Media
                                $table = JTable::getInstance('Media', 'hwdMediaShareTable');
                            
                                // Attempt to load the table row.
                                $return = $table->load($activity->action);

                                // Check for a table object error.
                                if ($return === false && $table->getError())
                                {
                                        $this->setError($table->getError());
                                        return false;
                                }
                
                                $properties = $table->getProperties(1);
                                $item = JArrayHelper::toObject($properties, 'JObject');
                                if (isset($item->title))
                                {
                                            $routeuser  = ($app->isAdmin() ? 'index.php?option=com_hwdmediashare&task=user.edit&id=' . $activity->actor : JRoute::_(hwdMediaShareHelperRoute::getUserRoute($activity->actor)));
                                            $routemedia = ($app->isAdmin() ? 'index.php?option=com_hwdmediashare&task=editmedia.edit&id=' . $activity->action : JRoute::_(hwdMediaShareHelperRoute::getMediaItemRoute($activity->action)));
                                            $return =  JText::sprintf(hwdMediaShareActivities::getActivityType($activity), 
                                                                      '<a href="'.$routeuser.'">'.$activity->author.'</a>', 
                                                                      '<a href="'.$routemedia.'">'.JHtmlString::truncate($item->title, $config->get('list_title_truncate'), false, false).'</a>');
                                            return $return;
                                }
                        break;
                        case 3: // New Album
                                $table = JTable::getInstance('Album', 'hwdMediaShareTable');
                            
                                // Attempt to load the table row.
                                $return = $table->load($activity->action);

                                // Check for a table object error.
                                if ($return === false && $table->getError())
                                {
                                        $this->setError($table->getError());
                                        return false;
                                }
                
                                $properties = $table->getProperties(1);
                                $item = JArrayHelper::toObject($properties, 'JObject');
                                if (isset($item->title))
                                {
                                            $routeuser  = ($app->isAdmin() ? 'index.php?option=com_hwdmediashare&task=user.edit&id=' . $activity->actor : JRoute::_(hwdMediaShareHelperRoute::getUserRoute($activity->actor)));
                                            $routemedia = ($app->isAdmin() ? 'index.php?option=com_hwdmediashare&task=album.edit&id=' . $activity->action : JRoute::_(hwdMediaShareHelperRoute::getAlbumRoute($activity->action)));
                                            $return =  JText::sprintf(hwdMediaShareActivities::getActivityType($activity), 
                                                                      '<a href="'.$routeuser.'">'.$activity->author.'</a>', 
                                                                      '<a href="'.$routemedia.'">'.JHtmlString::truncate($item->title, $config->get('list_title_truncate'), false, false).'</a>');
                                            return $return;
                                }
                        break;  
                        case 4: // New Group
                        case 7: // Joined Group
                        case 8: // Left Group
                                $table = JTable::getInstance('Group', 'hwdMediaShareTable');
                            
                                // Attempt to load the table row.
                                $return = $table->load($activity->action);

                                // Check for a table object error.
                                if ($return === false && $table->getError())
                                {
                                        $this->setError($table->getError());
                                        return false;
                                }
                
                                $properties = $table->getProperties(1);
                                $item = JArrayHelper::toObject($properties, 'JObject');
                                if (isset($item->title))
                                {
                                            $routeuser  = ($app->isAdmin() ? 'index.php?option=com_hwdmediashare&task=user.edit&id=' . $activity->actor : JRoute::_(hwdMediaShareHelperRoute::getUserRoute($activity->actor)));
                                            $routemedia = ($app->isAdmin() ? 'index.php?option=com_hwdmediashare&task=album.edit&id=' . $activity->action : JRoute::_(hwdMediaShareHelperRoute::getAlbumRoute($activity->action)));
                                            $return =  JText::sprintf(hwdMediaShareActivities::getActivityType($activity), 
                                                                      '<a href="'.$routeuser.'">'.$activity->author.'</a>', 
                                                                      '<a href="'.$routemedia.'">'.JHtmlString::truncate($item->title, $config->get('list_title_truncate'), false, false).'</a>');
                                            return $return;
                                }
                        break; 
                        case 5: // New Playlist
                                $table = JTable::getInstance('Playlist', 'hwdMediaShareTable');
                            
                                // Attempt to load the table row.
                                $return = $table->load($activity->action);

                                // Check for a table object error.
                                if ($return === false && $table->getError())
                                {
                                        $this->setError($table->getError());
                                        return false;
                                }
                
                                $properties = $table->getProperties(1);
                                $item = JArrayHelper::toObject($properties, 'JObject');
                                if (isset($item->title))
                                {
                                            $routeuser  = ($app->isAdmin() ? 'index.php?option=com_hwdmediashare&task=user.edit&id=' . $activity->actor : JRoute::_(hwdMediaShareHelperRoute::getUserRoute($activity->actor)));
                                            $routemedia = ($app->isAdmin() ? 'index.php?option=com_hwdmediashare&task=playlist.edit&id=' . $activity->action : JRoute::_(hwdMediaShareHelperRoute::getPlaylistRoute($activity->action)));
                                            $return =  JText::sprintf(hwdMediaShareActivities::getActivityType($activity), 
                                                                      '<a href="'.$routeuser.'">'.$activity->author.'</a>', 
                                                                      '<a href="'.$routemedia.'">'.JHtmlString::truncate($item->title, $config->get('list_title_truncate'), false, false).'</a>');
                                            return $return;
                                }
                        break; 
                }

                return false;
	}
}
