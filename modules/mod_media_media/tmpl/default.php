<?php
/**
 * @version    $Id: default.php 1329 2013-03-20 10:40:06Z dhorsfall $
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
JHtml::_('behavior.modal');
JHtml::_('behavior.framework', true);
JHtml::_('behavior.tooltip');

$row=0;
$counter=0;
$leadingcount=0;
$introcount=0;
?>
<!-- Module Container -->
<div class="hwd-module">
<div class="media-details-view">
<?php foreach ($items as $id => &$item) :
$id= ($id-$leadingcount)+1;
$rowcount=( ((int)$id-1) %	(int) $helper->get('columns')) +1;
$row = $counter / $helper->get('columns') ;
$item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;
$canEdit = ($user->authorise('core.edit', 'com_hwdmediashare.media.'.$item->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.media.'.$item->id) && ($item->created_user_id == $user->id)));
$canEditState = $user->authorise('core.edit.state', 'com_hwdmediashare.media.'.$item->id);
$canDelete = ($user->authorise('core.delete', 'com_hwdmediashare.media.'.$item->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.media.'.$item->id) && ($item->created_user_id == $user->id)));
?>
  <!-- Row -->
  <?php if ($rowcount == 1) : ?>
  <div class="items-row cols-<?php echo (int) $helper->get('columns');?> <?php echo 'row-'.$row ; ?>">
    <?php endif; ?>
    <!-- Column -->
    <div class="item column-<?php echo $rowcount;?><?php echo ($item->published != '1' ? ' system-unpublished' : false); ?>">
      <!-- Cell -->
      <?php if ($item->published != '1') : ?>
      <div class="system-unpublished">
        <?php endif; ?>
        <?php if ($params->get('list_meta_title') != 'hide') :?>
          <h<?php echo $params->get('list_item_heading'); ?> class="contentheading<?php echo ($params->get('list_tooltip_location') > '1' ? ' hasTip' : ''); ?>" title="<?php echo $helper->get('utilities')->escape($item->title); ?>::<?php echo ($params->get('list_tooltip_contents') != '0' ? $helper->get('utilities')->escape(JHtmlString::truncate($item->description, $params->get('list_desc_truncate'), true, false)) : ''); ?>">
            <?php if ($params->get('list_link_titles') == 1) :?><a href="<?php echo ($params->get('modal') == '1' ? JRoute::_(hwdMediaShareHelperRoute::getMediaModalRoute($item->slug)) : JRoute::_(hwdMediaShareHelperRoute::getMediaItemRoute($item->slug, array(), false))); ?>" <?php echo ($params->get('modal') == '1' ? 'class="modal" rel="{handler: \'iframe\', size: {x: 500, y: 100}}"' : false); ?>><?php endif; ?>
              <?php echo $helper->get('utilities')->escape(JHtmlString::truncate($item->title, $params->get('list_title_truncate'))); ?> 
            <?php if ($params->get('list_link_titles') == 1) :?></a><?php endif; ?>
          </h<?php echo $params->get('list_item_heading'); ?>>
        <?php endif; ?>
        <!-- Thumbnail Image -->
        <div class="media-item">
          <div class="media-aspect<?php echo $params->get('list_thumbnail_aspect'); ?>"></div>                    
          <?php if ($canEdit || $canDelete): ?>
          <!-- Actions -->
          <ul class="media-nav">
            <li><a href="#" class="pagenav-manage"><?php echo JText::_('COM_HWDMS_MANAGE'); ?> </a>
              <ul class="media-subnav">
                <?php if ($canEdit) : ?>
                <li><?php echo JHtml::_('hwdicon.edit', 'media', $item, $params); ?></li>
                <?php endif; ?>
                <?php if ($canEditState) : ?>
                <?php if ($item->published != '1') : ?>
                <li><?php echo JHtml::_('hwdicon.publish', 'media', $item, $params); ?></li>
                <?php else : ?>
                <li><?php echo JHtml::_('hwdicon.unpublish', 'media', $item, $params); ?></li>
                <?php endif; ?>
                <?php endif; ?>
                <?php if ($canDelete) : ?>
                <li><?php echo JHtml::_('hwdicon.delete', 'media', $item, $params); ?></li>
                <?php endif; ?>
              </ul>
            </li>
          </ul>
          <?php endif; ?>
          <!-- Media Type -->
          <?php if ($params->get('list_meta_thumbnail') != 'hide') :?>
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
          <?php if ($params->get('list_link_thumbnails') == 1) : ?><a href="<?php echo ($params->get('modal') == '1' ? JRoute::_(hwdMediaShareHelperRoute::getMediaModalRoute($item->slug)) : JRoute::_(hwdMediaShareHelperRoute::getMediaItemRoute($item->slug, array(), false))); ?>" <?php echo ($params->get('modal') == '1' ? 'class="modal" rel="{handler: \'iframe\', size: {x: 500, y: 100}}"' : false); ?>><?php endif; ?>
             <img src="<?php echo JRoute::_(hwdMediaShareDownloads::thumbnail($item, $helper->get('elementType'))); ?>" border="0" alt="<?php echo $helper->get('utilities')->escape($item->title); ?>" class="media-thumb <?php echo ($params->get('list_tooltip_location') > '2' ? 'hasTip' : ''); ?>" title="<?php echo $helper->get('utilities')->escape($item->title); ?>::<?php echo ($params->get('list_tooltip_contents') != '0' ? $helper->get('utilities')->escape(JHtmlString::truncate($item->description, $params->get('list_desc_truncate'), true, false)) : ''); ?>" />
          <?php if ($params->get('list_link_thumbnails') == 1) :?></a><?php endif; ?>
          <?php endif; ?>
        </div>
        <!-- Clears Item and Information -->
        <div class="clear"></div>
        <?php if ($params->get('list_meta_description') != 'hide' || $params->get('list_meta_category') != 'hide' || $params->get('list_meta_author') != 'hide' || $params->get('list_meta_created') != 'hide' || $params->get('list_meta_likes') != 'hide' || $params->get('list_meta_hits') != 'hide') : ?>
        <!-- Item Meta -->
        <dl class="article-info">
          <dt class="article-info-term"><?php echo JText::_('COM_HWDMS_DETAILS'); ?> </dt>
          <?php if ($params->get('list_meta_description') != 'hide') :?>
            <dd class="media-info-description"> <?php echo $helper->get('utilities')->escape(JHtmlString::truncate($item->description, $params->get('list_desc_truncate'), true, false)); ?> </dd>
          <?php endif; ?>
          <?php if ($params->get('list_meta_category') != 'hide' && $params->get('enable_categories')) :?>
            <dd class="media-info-category"> <?php echo $helper->getCategories($item); ?> </dd>
          <?php endif; ?>            
          <?php if ($params->get('list_meta_author') != 'hide') :?>
            <dd class="media-info-createdby"> <?php echo JText::sprintf('COM_HWDMS_CREATED_BY', '<a href="'.JRoute::_(hwdMediaShareHelperRoute::getUserRoute($item->created_user_id)).'">'.htmlspecialchars($item->author, ENT_COMPAT, 'UTF-8').'</a>'); ?></dd>
          <?php endif; ?>
          <?php if ($params->get('list_meta_created') != 'hide') :?>
            <dd class="media-info-created"> <?php echo JText::sprintf('COM_HWDMS_CREATED_ON', JHtml::_('date', $item->created, $params->get('list_date_format'))); ?></dd>
          <?php endif; ?>
          <?php if ($params->get('list_meta_likes') != 'hide') :?>
            <dd class="media-info-like"> <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=mediaitem.like&id=' . $item->id . '&return=' . $helper->get('return') . '&tmpl=component'); ?>"><?php echo JText::_('COM_HWDMS_LIKE'); ?></a> (<?php echo $helper->get('utilities')->escape($item->likes); ?>) <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=mediaitem.dislike&id=' . $item->id . '&return=' . $helper->get('return') . '&tmpl=component'); ?>"><?php echo JText::_('COM_HWDMS_DISLIKE'); ?></a> (<?php echo $helper->get('utilities')->escape($item->dislikes); ?>) </dd>
          <?php endif; ?>
          <?php if ($params->get('list_meta_hits') != 'hide') :?>
            <dd class="media-info-hits"> <?php echo JText::_('COM_HWDMS_VIEWS'); ?> (<?php echo (int) $item->hits; ?>)</dd>
          <?php endif; ?>
        </dl>
        <?php endif; ?> 
      <?php if ($item->published != '1') : ?>
      </div>
      <?php endif; ?>
      <div class="item-separator"></div>
    </div>
  <?php if (($rowcount == $helper->get('columns')) or (($counter + 1) == count($items))): ?>
  <span class="row-separator"></span>
  </div>
  <?php endif; ?>
  <?php $counter++; ?>
  <?php endforeach; ?>
  <?php if ($params->get('show_more_link') != 'hide') :?><p><a href="<?php echo ((intval($params->get('show_more_link')) > 0) ? JRoute::_($menu->getItem($params->get('show_more_link'))->link.'&Itemid='.$params->get('show_more_link')) : JRoute::_(hwdMediaShareHelperRoute::getMediaRoute())); ?>"><?php echo JText::_($params->get('more_link_text', 'COM_HWDMS_VIEW_ALL')); ?></a></p><?php endif; ?>  
</div>
</div>