<?php
/**
 * @version    $Id: default.php 1377 2013-04-23 12:49:49Z dhorsfall $
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
<table class="category" id="media-audio-playlist">
  <?php if (count($items) > 0) : ?>
  <thead>
    <tr>
      <th colspan="4">
	<div id="media-audio-playlist-container-<?php echo $module->id; ?>">
          <?php echo hwdMediaShareMedia::get($items[0]); ?>
        </div>
      </th>
    </tr>
  </thead>
  <?php endif; ?>
  <tbody>
  <?php foreach ($items as $id => &$item) :
  $item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id; ?>
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
        <?php if ($params->get('list_link_thumbnails') == 1) :?><a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getMediaItemRoute($item->slug)); ?>"><?php endif; ?>
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
            <?php if ($params->get('list_link_titles') == 1) :?><a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getMediaItemRoute($item->slug)); ?>"><?php endif; ?>
                <?php echo $helper->get('utilities')->escape(JHtmlString::truncate($item->title, $params->get('list_title_truncate'))); ?> 
            <?php if ($params->get('list_link_titles') == 1) :?></a><?php endif; ?>
            </p>
            <?php if ($params->get('list_meta_description') != 'hide' && !empty($item->description)) :?>
                <p><?php echo $helper->get('utilities')->escape(JHtmlString::truncate($item->description, $params->get('list_desc_truncate'), true, false)); ?></p>
            <?php endif; ?>  
        </td>
      <?php endif; ?>
      <?php if ($params->get('list_meta_duration') != 'hide') :?><td width="40"><?php echo hwdMediaShareMedia::secondsToTime($item->duration); ?></td><?php endif; ?>
      <td width="1%"><a href="#" class="media-audio-playlist-play button" id="media-audio-playlist-play<?php echo $counter; ?>" rel="{mp3: '<?php echo JRoute::_(hwdMediaShareDownloads::url($item,8)); ?>', ogg: '<?php echo JRoute::_(hwdMediaShareDownloads::url($item,9)); ?>', id: '<?php echo $item->id; ?>', playerId: 'player-mod-<?php echo $module->id; ?>', playerContainer: 'media-audio-playlist-container-<?php echo $module->id; ?>'}">Play</a></td>
    </tr>
  <?php $counter++; ?>
  <?php endforeach; ?>
  </tbody>
</table>
<?php if ($params->get('show_more_link') != 'hide') :?><p><a href="<?php echo ((intval($params->get('show_more_link')) > 0) ? JRoute::_($menu->getItem($params->get('show_more_link'))->link.'&Itemid='.$params->get('show_more_link')) : JRoute::_(hwdMediaShareHelperRoute::getMediaRoute(array('filter_mediaType'=>1)))); ?>"><?php echo JText::_($params->get('more_link_text', 'COM_HWDMS_VIEW_ALL')); ?></a></p><?php endif; ?>  
</div>
<div style="clear:both;"></div>

