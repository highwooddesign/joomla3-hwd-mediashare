<?php
/**
 * @version    $Id: sidebar-simple.php 1579 2013-06-13 10:35:09Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      19-Sep-2012 18:05:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$application = JFactory::getApplication();
$user = JFactory::getUser();
$menu = $application->getMenu();
$counter=0;

// Define Joomla document object
$doc = JFactory::getDocument();
$doc->addStylesheet($helper->get('url').'css/sidebar-simple.css');
//$doc->addScript($helper->get('url').'js/scrollbar.js');
?>
<div class="hwd-module">
    <div class="hwd-module-video-sidebar-container" style="width:<?php echo intval($params->get('mediaitem_size')+125); ?>px;">
        <div class="hwd-module-video-sidebar-left scrollbars osx">
            <?php foreach ($items as $id => &$item) :
            $item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;
            $flv = hwdMediaShareDownloads::flvUrl($item);
            $mp4 = hwdMediaShareDownloads::mp4Url($item);
            $webm = hwdMediaShareDownloads::webmUrl($item);
            $ogg = hwdMediaShareDownloads::oggUrl($item); ?>
            <div class="hwd-module-video-sidebar-left-item">
                <div class="media-item" style="padding:0;margin:0px;">
                    <?php if ($params->get('list_meta_type_icon') != 'hide') :?>
                    <div class="media-item-format-1-<?php echo $item->media_type; ?>">
                        <img src="<?php echo JHtml::_('hwdicon.overlay', '1-'.$item->media_type, $item); ?>" alt="<?php echo JText::_('COM_HWDMS_MEDIA_TYPE'); ?>" />
                    </div>
                    <?php endif; ?>
                    <?php if ($params->get('list_meta_duration') != 'hide' && $item->duration > 0) :?>
                    <div class="media-duration">
                        <?php echo hwdMediaShareMedia::secondsToTime($item->duration); ?>
                    </div>
                    <?php endif; ?>
                    <a href="#" class="media-video-playlist-play" id="media-video-playlist-play-<?php echo $module->id; ?>-<?php echo $counter; ?>" rel="{flv: '<?php echo $flv; ?>', mp4: '<?php echo $mp4; ?>', webm: '<?php echo $webm; ?>', ogg: '<?php echo $ogg; ?>', id: '<?php echo $item->id; ?>', playerId: 'player-mod-<?php echo $module->id; ?>', playerContainer: 'media-video-playlist-container-<?php echo $module->id; ?>', width: '<?php echo $params->get('mediaitem_size'); ?>', height: '<?php //echo $helper->player->video_height; ?>'}">
                        <img src="<?php echo JRoute::_(hwdMediaShareDownloads::thumbnail($item)); ?>" border="0" alt="<?php echo $helper->get('utilities')->escape($item->title); ?>" style="width:105px;" />
                    </a>
                </div>
            </div>
            <?php $counter++; ?>
            <?php endforeach; ?>
        </div>
        <?php if (count($items) > 0) : ?>
            <div id="media-video-playlist-container-<?php echo $module->id; ?>" style="padding-left:125px">
                <?php echo hwdMediaShareMedia::get($items[0]); ?>
            </div>
        <?php endif; ?>
    </div>
<?php if ($params->get('show_more_link') != 'hide') :?><p><a href="<?php echo ((intval($params->get('show_more_link')) > 0) ? JRoute::_($menu->getItem($params->get('show_more_link'))->link.'&Itemid='.$params->get('show_more_link')) : JRoute::_(hwdMediaShareHelperRoute::getMediaRoute(array('filter_mediaType'=>4)))); ?>"><?php echo JText::_($params->get('more_link_text', 'COM_HWDMS_VIEW_ALL')); ?></a></p><?php endif; ?>  
</div>