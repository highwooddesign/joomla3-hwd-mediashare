<?php
/**
 * @version    SVN $Id: activities.php 1507 2013-05-13 13:34:02Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * hwdMediaShare framework activities class
 *
 * @package hwdMediaShare
 * @since   0.1
 */
class hwdMediaShareActivities
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
	 * Returns the hwdMediaShareActivities object, only creating it if it
	 * doesn't already exist.
	 *
	 * @return  hwdMediaShareActivities A hwdMediaShareActivities object.
	 * @since   0.1
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
	 * Method to save an activity
         * 
         * @since   0.1
	 **/
	public function save($params)
	{
                $app = JFactory::getApplication();
                $date =& JFactory::getDate();
                $user = JFactory::getUser();
                $db =& JFactory::getDBO();
                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                $row =& JTable::getInstance('Activity', 'hwdMediaShareTable');

                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                // Create an object to bind to the database
                $object = new StdClass;
                $object->activity_type = $params->activityType;
                $object->element_type = $params->elementType;
                $object->element_id = $params->elementId;
                $object->reply_id = isset($params->replyId) ? $params->replyId : 0;
                $object->description = isset($params->description) ? $params->description : '';
                if (!$app->isAdmin() && $config->get('approve_new_activities') == 1) 
                { 
                        $object->status = 2;
                }
                else
                {
                        $object->status = 1;
                }
                $object->published = 1;
                $object->access = 1;
                $object->created_user_id = isset($params->userId) ? $params->userId : $user->id;
                $object->created = $date->format('Y-m-d H:i:s');
                $object->publish_up = $date->format('Y-m-d H:i:s');
                $object->publish_down = "0000-00-00 00:00:00";                   
                $object->language = "*";                   

                if (!$row->bind($object))
                {
                        return JError::raiseWarning( 500, $row->getError() );
                }

                if (!$row->store())
                {
                        JError::raiseError(500, $row->getError() );
                }
                                    
                return true;
	}

	/**
	 * Method to render html for activity stream
         * 
         * @since   0.1
	 **/
	public function getActivities( &$item, $parent = true )
	{
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                $user = JFactory::getUser();
           
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                $return = base64_encode(JFactory::getURI()->toString());

                if (!isset($item) || !is_array($item))
                {        
                        return;
                }
                ob_start();
                ?>
                <ul <?php echo ($parent == true ? 'class="category-module"' : false); ?>>
                <?php foreach ($item as $id => &$activity) :
                        $canEdit = ($user->authorise('core.edit', 'com_hwdmediashare.activity.'.$activity->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.activity.'.$activity->id) && ($activity->created_user_id == $user->id)));
                        $canEditState = $user->authorise('core.edit.state', 'com_hwdmediashare.activity.'.$activity->id);
                        $canDelete = ($user->authorise('core.delete', 'com_hwdmediashare.activity.'.$activity->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.activity.'.$activity->id) && ($activity->created_user_id == $user->id)));

                        // Verify activity data exists
                        if (!$activity->element_type) continue;
                        if (!$activity->element_id) continue;
                        
                        // Define a variable used to hold extra activity information
                        $activity->element_name = '';
                        $activity->thumbnail = '';
                        switch ($activity->activity_type) {
                            case 1:
                                break;
                            case 2://new media
                                //Get row in table where ID's are equal
                                $row =& JTable::getInstance('Media', 'hwdMediaShareTable');

                                jimport( 'joomla.application.component.model' );
                                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                                $model =& JModelLegacy::getInstance('MediaItem', 'hwdMediaShareModel', array('ignore_request' => true));
                                $model->setState('media.id', $activity->element_id);

                                if ($media = $model->getItem())
                                {
                                            $activity->title       = '<a href="'.JRoute::_(hwdMediaShareHelperRoute::getMediaItemRoute($activity->element_id)).'">'.$media->title.'</a>'; 
                                            $activity->description = $media->description;
                                            $activity->thumbnail = '<div class="media-item">
                                                                    <div class="media-item-format-1-'.$media->media_type .'">
                                                                         <img src="'.JHtml::_('hwdicon.overlay', '1-'.$media->media_type, $media).'" alt="'.JText::_('COM_HWDMS_MEDIA_TYPE').'" />
                                                                    </div>';
                                                                    if($media->duration > 0) :
                                                                    $activity->thumbnail.= '
                                                                           <div class="media-duration">'
                                                                               .hwdMediaShareMedia::secondsToTime($media->duration).
                                                                           '</div>';
                                                                    endif;
                                                                    $activity->thumbnail.= '
                                                                        <a href="'.JRoute::_(hwdMediaShareHelperRoute::getMediaItemRoute($activity->element_id)).'">                                            
                                                                          <img src="'. JRoute::_(hwdMediaShareDownloads::thumbnail($media)) .'" border="0" alt="'.$media->title.'" style="max-width:100%;" class="media-thumb" title="'.$media->title.'"/>
                                                                       </a>
                                                                   </div>';
                                }
                                break;
                            case 3://new album
                                //Get row in table where ID's are equal
                                jimport( 'joomla.application.component.model' );
                                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                                $model =& JModelLegacy::getInstance('Album', 'hwdMediaShareModel', array('ignore_request' => true));
                                $model->setState('filter.album_id', $activity->element_id);

                                if ($album = $model->getAlbum())
                                {
                                            $activity->title       = '<a href="'.JRoute::_(hwdMediaShareHelperRoute::getAlbumRoute($activity->element_id)).'">'.$album->title.'</a>'; 
                                            $activity->description = $album->description;
                                            $activity->thumbnail =   '<div class="media-item">
                                                                <div class="media-item-format-2">
                                                                    <img src="'.JHtml::_('hwdicon.overlay', 2).'" alt="Album" />
                                                                </div>
                                                                <a href="'.JRoute::_(hwdMediaShareHelperRoute::getAlbumRoute($activity->element_id)).'">    
                                                                    <img src="'.JRoute::_(hwdMediaShareDownloads::thumbnail($album, 2)).'" border="0" alt="'.$utilities->escape($album->title).'" style="max-width:100%;" />
                                                                </a>
                                                            </div>';
                                }                                                                
                                break;    
                            case 4://new group
                            case 7://joined group
                            case 8://left group
                                //Get row in table where ID's are equal
                                jimport( 'joomla.application.component.model' );
                                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                                $model =& JModelLegacy::getInstance('Group', 'hwdMediaShareModel', array('ignore_request' => true));
                                $model->setState('filter.group_id', $activity->element_id);

                                if ($group = $model->getGroup())
                                {
                                            $activity->title       = '<a href="'.JRoute::_(hwdMediaShareHelperRoute::getGroupRoute($activity->element_id)).'">'.$group->title.'</a>'; 
                                            $activity->description = $group->description;
                                            $activity->thumbnail = '<div class="media-item">
                                                                <div class="media-item-format-3">
                                                                    <img src="'.JHtml::_('hwdicon.overlay', 3).'" alt="Group" />
                                                                </div>
                                                                <a href="'.JRoute::_(hwdMediaShareHelperRoute::getGroupRoute($activity->element_id)).'">    
                                                                    <img src="'.JRoute::_(hwdMediaShareDownloads::thumbnail($group, 3)).'" border="0" alt="'.$utilities->escape($group->title).'" style="width:100px;" />
                                                                </a>    
                                                            </div>';
                                }
                                break;
                            case 5://new playlist
                                //Get row in table where ID's are equal
                                jimport( 'joomla.application.component.model' );
                                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                                $model =& JModelLegacy::getInstance('Playlist', 'hwdMediaShareModel', array('ignore_request' => true));
                                $model->setState('filter.playlist_id', $activity->element_id);

                                if ($playlist = $model->getPlaylist())
                                {
                                            $activity->title       = '<a href="'.JRoute::_(hwdMediaShareHelperRoute::getPlaylistRoute($activity->element_id)).'">'.$playlist->title.'</a>'; 
                                            $activity->description = $playlist->description;
                                            $activity->thumbnail =   '<div class="media-item">
                                                                <div class="media-item-format-4">
                                                                    <img src="'.JHtml::_('hwdicon.overlay', 4).'" alt="Playlist" />
                                                                </div>
                                                                <a href="'.JRoute::_(hwdMediaShareHelperRoute::getPlaylistRoute($activity->element_id)).'">    
                                                                    <img src="'.JRoute::_(hwdMediaShareDownloads::thumbnail($playlist)).'" border="0" alt="'.$utilities->escape($album->title).'" style="max-width:100%;" />
                                                                </a>
                                                            </div>';   
                                }    
                                 break;
                            case 6: 
                                break;
//                            case 7:
//                                break;
//                            case 8:
//                                break;
                            case 9:
                                //Get row in table where ID's are equal
                                jimport( 'joomla.application.component.model' );
                                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                                $model =& JModelLegacy::getInstance('MediaItem', 'hwdMediaShareModel', array('ignore_request' => true));
                                $model->setState('media.id', $activity->element_id);

                                $activity->element_name = 'Group Name';
                                if ($media = $model->getItem())
                                {
                                            $activity->title       = '<a href="'.JRoute::_(hwdMediaShareHelperRoute::getMediaItemRoute($activity->element_id)).'">'.$media->title.'</a>'; 
                                            $activity->description = $media->description;
                                            $activity->thumbnail = '<div class="media-item">
                                                                    <div class="media-item-format-1-'.$media->media_type .'">
                                                                         <img src="'.JHtml::_('hwdicon.overlay', '1-'.$media->media_type, $media).'" alt="'.JText::_('COM_HWDMS_MEDIA_TYPE').'" />
                                                                    </div>';
                                                                    if($media->duration > 0) :
                                                                    $activity->thumbnail.= '
                                                                           <div class="media-duration">'
                                                                               .hwdMediaShareMedia::secondsToTime($media->duration).
                                                                           '</div>';
                                                                    endif;
                                                                    $activity->thumbnail.= '
                                                                        <a href="'.JRoute::_(hwdMediaShareHelperRoute::getMediaItemRoute($activity->element_id)).'">                                            
                                                                          <img src="'. JRoute::_(hwdMediaShareDownloads::thumbnail($media)) .'" border="0" alt="'.$media->title.'" style="max-width:100%;" class="media-thumb" title="'.$media->title.'"/>
                                                                       </a>
                                                                   </div>';
                                }
                                break;
                        }
                        
                        ?>
                        <li class="">
                                <div class="<?php echo ($activity->published != '1' ? ' system-unpublished' : false); ?>">
                                <div class="category-desc">
                                            <a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getUserRoute($activity->created_user_id)); ?>" class="image-left"><img width="50" height="50" border="0" src="<?php echo JRoute::_($utilities->getAvatar(JFactory::getUser($activity->created_user_id))); ?>" alt="User"/></a>
                                            <?php if ($canEdit || $canDelete): ?>
                                            <!-- Actions -->
                                            <ul class="media-nav">
                                            <li><a href="#" class="pagenav-manage"><?php echo JText::_('COM_HWDMS_MANAGE'); ?> </a>
                                                <ul class="media-subnav">
                                                    <?php if ($canEdit) : ?>
                                                    <li><?php echo JHtml::_('hwdicon.edit', 'activity', $activity, $config); ?></li>
                                                    <?php endif; ?>
                                                    <?php if ($canEditState) : ?>
                                                    <?php if ($activity->published != '1') : ?>
                                                    <li><?php echo JHtml::_('hwdicon.publish', 'activity', $activity, $config); ?></li>
                                                    <?php else : ?>
                                                    <li><?php echo JHtml::_('hwdicon.unpublish', 'activity', $activity, $config); ?></li>
                                                    <?php endif; ?>
                                                    <?php endif; ?>
                                                    <?php if ($canDelete) : ?>
                                                    <li><?php echo JHtml::_('hwdicon.delete', 'activity', $activity, $config); ?></li>
                                                    <?php endif; ?>
                                                </ul>
                                            </li>
                                            </ul>
                                            <?php endif; ?>
                                            <p><span class="item-title"><strong><?php echo JText::sprintf(hwdMediaShareActivities::getActivityType($activity), 
                                                                                                          '<a href="'.JRoute::_(hwdMediaShareHelperRoute::getUserRoute($activity->created_user_id)).'">'.$utilities->escape($activity->author).'</a>', 
                                                                                                          $activity->title, 
                                                                                                          $activity->element_name); ?></strong></span></p>
                                            <div class="activity-info">
                                              <div class="activity-info-thumbnail"><?php echo $activity->thumbnail; ?></div>
                                              <div class="activity-info-title"><?php echo $activity->title; ?></div>
                                              <div class="activity-info-description"><?php echo $activity->description; ?></div>
                                            </div>
                                            <div class="clear"></div>
                                    </div>
                                    <dl>
                                            <dd class="media-comment-created small"><?php echo JHtml::_('date',$activity->created, $config->get('list_date_format')); ?></dd>
                                            <dd class="media-comment-reply small"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=activityform.reply&element_id=' . $activity->element_id . '&element_type=' . $activity->element_type . '&reply_id=' . $activity->id .'&tmpl=component&return=' . $return); ?>" class="pagenav-zoom modal" rel="{handler: 'iframe', size: {<?php echo $utilities->modalSize(); ?>}}" title="<?php echo JText::_('COM_HWDMS_REPLY'); ?>"><?php echo JText::_('COM_HWDMS_REPLY'); ?></a></dd>
                                            <dd class="media-comment-like small"> <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=activity.like&id=' . $activity->id . '&return=' . $return . '&tmpl=component'); ?>"><?php echo JText::_('COM_HWDMS_LIKE'); ?></a> (<?php echo $utilities->escape($activity->likes); ?>)</dd>
                                            <dd class="media-comment-dislike small"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=activity.dislike&id=' . $activity->id . '&return=' . $return . '&tmpl=component'); ?>"><?php echo JText::_('COM_HWDMS_DISLIKE'); ?></a> (<?php echo $utilities->escape($activity->dislikes); ?>)</dd>
                                            <dd class="media-comment-report small"> <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=activityform.report&id=' . $activity->id . '&return=' . $return . '&tmpl=component'); ?>" class="modal" rel="{handler: 'iframe', size: {<?php echo $utilities->modalSize(); ?>}}" title="<?php echo JText::_('COM_HWDMS_REPORT'); ?>"><?php echo JText::_('COM_HWDMS_REPORT'); ?></a> </dd>

                                    </dl>
                                    <div class="clear"></div>
                                    </div>
                                    <?php echo $this->getActivities($activity->children, false);?>
                        </li>
                <?php endforeach; ?>
                </ul>
                <?php
                $html = ob_get_contents();
                ob_end_clean();

                return $html;
	}
        
	/**
	 * Method to get human readable activity type
         * 
         * @since   0.1
	 **/
        function getActivityType($item)
        {
                switch ($item->activity_type) {
                    case 1:
                        $return = ($item->reply_id > 0 ? JText::_('COM_HWDMS_X_REPLIED') : JText::_('COM_HWDMS_X_WROTE'));
                        return $return;
                        break;
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
}
