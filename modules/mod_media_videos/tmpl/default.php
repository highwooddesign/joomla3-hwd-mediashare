<?php
/**
 * @version    $Id: default.php 1491 2013-04-30 15:35:05Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$application = JFactory::getApplication();
$user = JFactory::getUser();
$menu = $application->getMenu();
$counter=0;

?>
<div style="clear:both;"></div>
<div class="hwd-module">
<table class="category" id="media-video-playlist">
  <?php if (count($items) > 0) : ?>
  <thead>
    <tr>
      <th colspan="4">
	<div id="media-video-playlist-container-<?php echo $module->id; ?>">
          <?php echo hwdMediaShareMedia::get($items[0]); ?>
        </div>
      </th>
    </tr>
  </thead>
  <?php endif; ?>
  <tbody>
  <?php foreach ($items as $id => &$item) :
    $item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;
    $flv = hwdMediaShareDownloads::flvUrl($item);
    $mp4 = hwdMediaShareDownloads::mp4Url($item);
    $webm = hwdMediaShareDownloads::webmUrl($item);
    $ogg = hwdMediaShareDownloads::oggUrl($item); ?>
    <tr class="<?php echo ($item->published != '1' ? 'system-unpublished ' : false); ?>cat-list-row<?php echo ($counter % 2);?>">
      <?php if ($params->get('list_meta_thumbnail') != 'hide') :?>
        <td width="1%">
        <div class="media-item">
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
        <?php if ($params->get('list_link_thumbnails') == 1) :?><a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getMediaItemRoute($item->slug, array(), false)); ?>"><?php endif; ?>
            <img src="<?php echo JRoute::_(hwdMediaShareDownloads::thumbnail($item)); ?>" border="0" alt="<?php echo $helper->get('utilities')->escape($item->title); ?>" style="max-width:100px;" />
        <?php if ($params->get('list_link_thumbnails') == 1) :?></a><?php endif; ?>
        </div>
        </td>
      <?php elseif ($params->get('list_meta_type_icon') != 'hide'): ?>
        <td width="1%"><img src="<?php echo JHtml::_('hwdicon.overlay', '1-'.$item->media_type, $item); ?>" alt="<?php echo JText::_('COM_HWDMS_MEDIA_TYPE'); ?>" /></td>
      <?php endif; ?>
      <?php if ($params->get('list_meta_title') != 'hide' || $params->get('list_meta_description') != 'hide') : ?>
        <td class="list-title">
            <p>
            <?php if ($params->get('list_link_titles') == 1) :?><a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getMediaItemRoute($item->slug, array(), false)); ?>"><?php endif; ?>
                <?php echo $helper->get('utilities')->escape(JHtmlString::truncate($item->title, $params->get('list_title_truncate'))); ?> 
            <?php if ($params->get('list_link_titles') == 1) :?></a><?php endif; ?>
            </p>
            <?php if ($params->get('list_meta_description') != 'hide' && !empty($item->description)) :?>
                <p><?php echo $helper->get('utilities')->escape(JHtmlString::truncate($item->description, $params->get('list_desc_truncate'), true, false)); ?></p>
            <?php endif; ?>  
        </td>
      <?php endif; ?>
      <?php if ($params->get('list_meta_duration') != 'hide') :?><td width="40"><?php echo hwdMediaShareMedia::secondsToTime($item->duration); ?></td><?php endif; ?>
      <td width="1%"><a href="#" class="media-video-playlist-play button" id="media-video-playlist-play-<?php echo $module->id; ?>-<?php echo $counter; ?>" rel="{flv: '<?php echo $flv; ?>', mp4: '<?php echo $mp4; ?>', webm: '<?php echo $webm; ?>', ogg: '<?php echo $ogg; ?>', id: '<?php echo $item->id; ?>', playerId: 'player-mod-<?php echo $module->id; ?>', playerContainer: 'media-video-playlist-container-<?php echo $module->id; ?>', width: '<?php echo $params->get('mediaitem_size'); ?>', height: '<?php //echo $helper->player->video_height; ?>'}">Play</a></td>
    </tr>
  <?php $counter++; ?>
  <?php endforeach; ?>
  </tbody>
</table>
<?php if ($params->get('show_more_link') != 'hide') :?><p><a href="<?php echo ((intval($params->get('show_more_link')) > 0) ? JRoute::_($menu->getItem($params->get('show_more_link'))->link.'&Itemid='.$params->get('show_more_link')) : JRoute::_(hwdMediaShareHelperRoute::getMediaRoute(array('filter_mediaType'=>4)))); ?>"><?php echo JText::_($params->get('more_link_text', 'COM_HWDMS_VIEW_ALL')); ?></a></p><?php endif; ?>  
</div>
<div style="clear:both;"></div>
