<?php
/**
 * @version    SVN $Id: events.php 1176 2013-02-25 15:16:38Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * hwdMediaShare framework events class
 *
 * @package hwdMediaShare
 * @since   0.1
 */
class hwdMediaShareEvents
{
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
	 * Returns the hwdMediaShareEvents object, only creating it if it
	 * doesn't already exist.
	 *
	 * @return  hwdMediaShareEvents A hwdMediaShareEvents object.
	 * @since   0.1
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
	 * Method to trigger a Joomla event
         * 
         * @since   0.1
	 **/
	public function triggerEvent( $event , $params = null , $needOrdering = false )
	{
		$dispatcher =& JDispatcher::getInstance();
                $act = new StdClass;
                
		// Avoid problem with php 5.3
		if(is_null($params))
                {
			$params = array();
		}

		switch( $event )
		{
                        case 'onAfterMediaAdd':
                                $act->activityType = 2;
                                $act->elementType = 1;
                                $act->elementId = $params->id;
                                hwdMediaShareFactory::load('activities');
                                hwdMediaShareActivities::save($act);                                
			break;
                        case 'onAfterAlbumAdd':
                                $act->activityType = 3;
                                $act->elementType = 2;
                                $act->elementId = $params->id;
                                hwdMediaShareFactory::load('activities');
                                hwdMediaShareActivities::save($act);
			break;
                        case 'onAfterGroupAdd':
                                $act->activityType = 4;
                                $act->elementType = 3;
                                $act->elementId = $params->id;
                                hwdMediaShareFactory::load('activities');
                                hwdMediaShareActivities::save($act);
			break;
                        case 'onAfterPlaylistAdd':
                                $act->activityType = 5;
                                $act->elementType = 4;
                                $act->elementId = $params->id;
                                hwdMediaShareFactory::load('activities');
                                hwdMediaShareActivities::save($act);
			break;
                        case 'onAfterChannelAdd':
                                $act->activityType = 6;
                                $act->elementType = 5;
                                $act->elementId = $params->id;
                                hwdMediaShareFactory::load('activities');
                                hwdMediaShareActivities::save($act);
			break;
                        case 'onAfterJoinGroup':
                                $act->activityType = 7;
                                $act->elementType = 3;
                                $act->elementId = $params->id;
                                hwdMediaShareFactory::load('activities');
                                hwdMediaShareActivities::save($act);
			break;                    
                        case 'onAfterLeaveGroup':
                                $act->activityType = 8;
                                $act->elementType = 3;
                                $act->elementId = $params->id;
                                hwdMediaShareFactory::load('activities');
                                hwdMediaShareActivities::save($act);
			break;                     
                        case 'onAfterShareMediaWithGroup':
                                $act->activityType = 9;
                                $act->elementType = 1;
                                $act->elementId = $params->media_id;
                                $act->title = $params->group_id;
                                hwdMediaShareFactory::load('activities');
                                hwdMediaShareActivities::save($act);
			break;
                        case 'onAfterMediaLike':
                                $act->activityType = 10;
                                $act->elementType = 1;
                                $act->elementId = $params->media_id;
                                hwdMediaShareFactory::load('activities');
                                hwdMediaShareActivities::save($act);
			break;
                        case 'onAfterMediaDislike':
                                $act->activityType = 11;
                                $act->elementType = 1;
                                $act->elementId = $params->media_id;
                                hwdMediaShareFactory::load('activities');
                                hwdMediaShareActivities::save($act);
			break;
                        case 'onAfterAlbumLike':
                                $act->activityType = 12;
                                $act->elementType = 2;
                                $act->elementId = $params->id;
                                hwdMediaShareFactory::load('activities');
                                hwdMediaShareActivities::save($act);
			break;
                        case 'onAfterAlbumDislike':
                                $act->activityType = 13;
                                $act->elementType = 2;
                                $act->elementId = $params->id;
                                hwdMediaShareFactory::load('activities');
                                hwdMediaShareActivities::save($act);
			break;
                        case 'onAfterGroupLike':
                                $act->activityType = 14;
                                $act->elementType = 3;
                                $act->elementId = $params->id;
                                hwdMediaShareFactory::load('activities');
                                hwdMediaShareActivities::save($act);
			break;
                        case 'onAfterGroupDislike':
                                $act->activityType = 15;
                                $act->elementType = 3;
                                $act->elementId = $params->id;
                                hwdMediaShareFactory::load('activities');
                                hwdMediaShareActivities::save($act);
			break;
                        case 'onAfterPlaylistLike':
                                $act->activityType = 16;
                                $act->elementType = 4;
                                $act->elementId = $params->id;
                                hwdMediaShareFactory::load('activities');
                                hwdMediaShareActivities::save($act);
			break;
                        case 'onAfterPlaylistDislike':
                                $act->activityType = 17;
                                $act->elementType = 4;
                                $act->elementId = $params->id;
                                hwdMediaShareFactory::load('activities');
                                hwdMediaShareActivities::save($act);
			break;
                        case 'onAfterChannelLike':
                                $act->activityType = 18;
                                $act->elementType = 5;
                                $act->elementId = $params->id;
                                hwdMediaShareFactory::load('activities');
                                hwdMediaShareActivities::save($act);
			break;
                        case 'onAfterChannelDislike':
                                $act->activityType = 19;
                                $act->elementType = 5;
                                $act->elementId = $params->id;
                                hwdMediaShareFactory::load('activities');
                                hwdMediaShareActivities::save($act);
			break;
                        case 'onAfterMediaFavourite':
                                $act->activityType = 20;
                                $act->elementType = 1;
                                $act->elementId = $params->media_id;
                                hwdMediaShareFactory::load('activities');
                                hwdMediaShareActivities::save($act);
			break;
                        case 'onAfterAddMediaToAlbum':
                                $act->activityType = 21;
                                $act->elementType = 1;
                                $act->elementId = $params->media_id;
                                $act->title = $params->album_id;
                                hwdMediaShareFactory::load('activities');
                                hwdMediaShareActivities::save($act);
			break;
                        case 'onAfterAddMediaToPlaylist':
                                $act->activityType = 22;
                                $act->elementType = 1;
                                $act->elementId = $params->media_id;
                                $act->title = $params->playlist_id;
                                hwdMediaShareFactory::load('activities');
                                hwdMediaShareActivities::save($act);
			break;
                        case 'onAfterAddMediaToCategory':
                                $act->activityType = 23;
                                $act->elementType = 1;
                                $act->elementId = $params->media_id;
                                $act->title = $params->category_id;
                                hwdMediaShareFactory::load('activities');
                                hwdMediaShareActivities::save($act);
			break;
                }

                jimport( 'joomla.utilities.arrayhelper' );                
                JPluginHelper::importPlugin( 'hwdmediashare' );
                if (file_exists(JPATH_SITE.'/components/com_community/')) JPluginHelper::importPlugin( 'community' );
                
                $results = $dispatcher->trigger( $event, array('media' => JArrayHelper::fromObject($params)) );

		return $results;
	}
}
