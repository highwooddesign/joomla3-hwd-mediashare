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

class hwdMediaShareEvents
{
    	/**
	 * Constructor override, defines a white list of column filters.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 */
	public function __construct($config = array())
	{
	}

	/**
	 * Returns the hwdMediaShareEvents object, only creating it if it
	 * doesn't already exist.
	 *
	 * @return  hwdMediaShareEvents Object.
	 */
	public static function getInstance()
	{
		static $instance;

		if (!isset ($instance))
                {
			$c = 'hwdMediaShareEvents';
                        $instance = new $c;
		}

		return $instance;
	}
        
	/**
	 * Method to trigger a Joomla event.
         * @return	void
	 */
	public function triggerEvent($event, $action, $target = null)
	{
                // Initialise variables.              
		$dispatcher = JDispatcher::getInstance();
                
                // Load HWD activities
                hwdMediaShareFactory::load('activities');
                $HWDactivities = hwdMediaShareActivities::getInstance();
                
                // New activity.
                $activity = new StdClass;
                
		// Avoid problem with php 5.3
		if(is_null($action)) $action = new StdClass;

		switch($event)
		{
                        case 'onAfterMediaAdd':
                                $activity->action = $action->id; // The object linked to the action itself.
                                $activity->target = 0;           // The object to which the activity was performed.
                                $activity->verb = 2;             // The verb phrase that identifies the action of the activity.    
			break;
                        case 'onAfterAlbumAdd':
                                $activity->action = $action->id;
                                $activity->target = 0;
                                $activity->verb = 3;
			break;
                        case 'onAfterGroupAdd':
                                $activity->action = $action->id;
                                $activity->target = 0;
                                $activity->verb = 4;
			break;
                        case 'onAfterPlaylistAdd':
                                $activity->action = $action->id;
                                $activity->target = 0;
                                $activity->verb = 5;
			break;
                        case 'onAfterChannelAdd':
                                $activity->action = $action->id;
                                $activity->target = 0;
                                $activity->verb = 6;
			break;
                        case 'onAfterJoinGroup':
                                $activity->action = $action->id;
                                $activity->target = 0;
                                $activity->verb = 7;
			break;                    
                        case 'onAfterLeaveGroup':
                                $activity->action = $action->id;
                                $activity->target = 0;
                                $activity->verb = 8;
			break;                     
                        case 'onAfterShareMediaWithGroup':
                                $activity->action = $action->id;
                                $activity->target = $target->id;
                                $activity->verb = 9;
			break;
                        case 'onAfterMediaLike':
                                $activity->action = $action->id;
                                $activity->target = 0;
                                $activity->verb = 10;
			break;
                        case 'onAfterMediaDislike':
                                $activity->action = $action->id;
                                $activity->target = 0;
                                $activity->verb = 11;
			break;
                        case 'onAfterAlbumLike':
                                $activity->action = $action->id;
                                $activity->target = 0;
                                $activity->verb = 12;
			break;
                        case 'onAfterAlbumDislike':
                                $activity->action = $action->id;
                                $activity->target = 0;
                                $activity->verb = 13;
			break;
                        case 'onAfterGroupLike':
                                $activity->action = $action->id;
                                $activity->target = 0;
                                $activity->verb = 14;
			break;
                        case 'onAfterGroupDislike':
                                $activity->action = $action->id;
                                $activity->target = 0;
                                $activity->verb = 15;
			break;
                        case 'onAfterPlaylistLike':
                                $activity->action = $action->id;
                                $activity->target = 0;
                                $activity->verb = 16;
			break;
                        case 'onAfterPlaylistDislike':
                                $activity->action = $action->id;
                                $activity->target = 0;
                                $activity->verb = 17;
			break;
                        case 'onAfterChannelLike':
                                $activity->action = $action->id;
                                $activity->target = 0;
                                $activity->verb = 18;
			break;
                        case 'onAfterChannelDislike':
                                $activity->action = $action->id;
                                $activity->target = 0;
                                $activity->verb = 19;
			break;
                        case 'onAfterMediaFavourite':
                                $activity->action = $action->id;
                                $activity->target = 0;
                                $activity->verb = 20;
			break;
                        case 'onAfterAddMediaToAlbum':
                                $activity->action = $action->id;
                                $activity->target = $target->id;
                                $activity->verb = 21;
			break;
                        case 'onAfterAddMediaToPlaylist':
                                $activity->action = $action->id;
                                $activity->target = $target->id;
                                $activity->verb = 22;
			break;
                        case 'onAfterAddMediaToCategory':
                                $activity->action = $action->id;
                                $activity->target = $target->id;
                                $activity->verb = 23;
			break;
                }

                // Save the new activity.
                $HWDactivities->save($activity);

                // Load HWD plugins, and JomSocial plugins.
                JPluginHelper::importPlugin('hwdmediashare');
                if (file_exists(JPATH_SITE.'/components/com_community/')) JPluginHelper::importPlugin('community');
                
                // Trigger the event.
                jimport('joomla.utilities.arrayhelper');                
                $results = $dispatcher->trigger($event, $action, $target);

		return $results;
	}
}
