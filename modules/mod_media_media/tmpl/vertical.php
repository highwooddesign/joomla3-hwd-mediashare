<?php
/**
 * @package     Joomla.site
 * @subpackage  Module.mod_media_media
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

$app = JFactory::getApplication();
$user = JFactory::getUser();
$menu = $app->getMenu();
?>
<div class="hwd-container">
  <div class="media-details-view vertical">
    <?php foreach ($helper->items as $id => $item) :
    $item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;
    $canEdit = ($user->authorise('core.edit', 'com_hwdmediashare.media.'.$item->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.media.'.$item->id) && ($item->created_user_id == $user->id)));
    $canEditState = $user->authorise('core.edit.state', 'com_hwdmediashare.media.'.$item->id);
    $canDelete = ($user->authorise('core.delete', 'com_hwdmediashare.media.'.$item->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.media.'.$item->id) && ($item->created_user_id == $user->id)));
    ?>
    <div class="row-fluid">
      <div class="span12">
        <!-- Thumbnail Image -->
        <div class="media-item">
          <div class="media-aspect<?php echo $helper->params->get('list_thumbnail_aspect'); ?>"></div>        
          <!-- Media Type -->
          <?php if ($helper->params->get('list_meta_thumbnail') != '0') :?>
          <?php if ($helper->params->get('list_meta_type_icon') != '0') :?>
          <div class="media-item-format-1-<?php echo $item->media_type; ?>">
             <img src="<?php echo JHtml::_('hwdicon.overlay', '1-'.$item->media_type, $item); ?>" alt="<?php echo JText::_('COM_HWDMS_MEDIA_TYPE'); ?>" />
          </div>
          <?php endif; ?>
          <?php if ($helper->params->get('list_meta_duration') != '0' && $item->duration > 0) :?>
          <div class="media-duration">
             <?php echo hwdMediaShareMedia::secondsToTime($item->duration); ?>
          </div>
          <?php endif; ?>
          <?php if ($helper->params->get('list_link_thumbnails') == 1) :?><a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getMediaItemRoute($item->slug)); ?>"><?php endif; ?>
             <img src="<?php echo JRoute::_(hwdMediaShareThumbnails::thumbnail($item)); ?>" border="0" alt="<?php echo $helper->escape($item->title); ?>" class="media-thumb<?php echo ($helper->params->get('list_tooltip_location') > '2' ? ' hasTooltip' : ''); ?>" title="<?php echo JHtml::tooltipText($item->title, ($helper->params->get('list_tooltip_contents') != '0' ? JHtml::_('string.truncate', $item->description, $helper->params->get('list_desc_truncate'), false, false) : '')); ?>" />
          <?php if ($helper->params->get('list_link_thumbnails') == 1) :?></a><?php endif; ?>
          <?php endif; ?>
        </div>
        <?php if ($canEdit || $canDelete): ?>
        <div class="btn-group pull-right">
          <?php
          // Create dropdown items
          if ($canEdit) : 
            JHtml::_('hwddropdown.edit', $item->id, 'mediaform'); 
          endif;    
          if ($canEditState) :
            $action = $item->published == 1 ? 'unpublish' : 'publish';
            JHtml::_('hwddropdown.' . $action, $item->id, 'media'); 
          endif; 
          if ($canDelete && $item->published != -2) : 
            JHtml::_('hwddropdown.delete', $item->id, 'media'); 
          endif;         
          // Render dropdown list
          echo JHtml::_('hwddropdown.render', $helper->escape($item->title), ' btn-micro');
          ?>                    
        </div>
        <?php endif; ?>
        <!-- Title -->
        <?php if ($helper->params->get('list_meta_title') != '0') :?>
          <h<?php echo $helper->params->get('list_item_heading'); ?> class="contentheading<?php echo ($helper->params->get('list_tooltip_location') > '1' ? ' hasTooltip' : ''); ?>" title="<?php echo JHtml::tooltipText($item->title, ($helper->params->get('list_tooltip_contents') != '0' ? JHtml::_('string.truncate', $item->description, $helper->params->get('list_desc_truncate'), false, false) : '')); ?>">
            <?php if ($helper->params->get('list_link_titles') == 1) :?><a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getMediaItemRoute($item->slug)); ?>"><?php endif; ?>
              <?php echo $helper->escape(JHtml::_('string.truncate', $item->title, $helper->params->get('list_title_truncate'), false, false)); ?> 
            <?php if ($helper->params->get('list_link_titles') == 1) :?></a><?php endif; ?>
          </h<?php echo $helper->params->get('list_item_heading'); ?>>
        <?php endif; ?>         
        <!-- Clears Item and Information -->
        <div class="clear"></div>
        <?php if ($item->featured): ?>
          <span class="label label-info"><?php echo JText::_('COM_HWDMS_FEATURED'); ?></span>
        <?php endif; ?>
        <?php if ($item->status != 1) : ?>
          <span class="label"><?php echo $helper->utilities->getReadableStatus($item); ?></span>
        <?php endif; ?>
        <?php if ($item->published != 1) : ?>
          <span class="label"><?php echo JText::_('COM_HWDMS_UNPUBLISHED'); ?></span>
        <?php endif; ?>        
        <!-- Clears Item and Information -->
        <div class="clear"></div>
        <?php if ($helper->params->get('list_meta_description') != '0' || $helper->params->get('list_meta_category') != '0' || $helper->params->get('list_meta_author') != '0' || $helper->params->get('list_meta_created') != '0' || $helper->params->get('list_meta_likes') != '0' || $helper->params->get('list_meta_hits') != '0') : ?>
        <!-- Item Meta -->
        <dl class="media-info">
          <dt class="media-info-term"><?php echo JText::_('COM_HWDMS_DETAILS'); ?> </dt>     
          <div class="clearfix"></div>
          <?php if ($helper->params->get('list_meta_likes') != '0') :?>
            <dd class="media-info-likes">
              <i class="icon-thumbs-up"></i> <span id="media-likes"><?php echo (int) $item->likes; ?></span>
              <i class="icon-thumbs-down"></i> <span id="media-dislikes"><?php echo (int) $item->dislikes; ?></span>
            </dd>
          <?php endif; ?>
          <?php if ($helper->params->get('list_meta_hits') != '0') :?>
            <dd class="media-info-hits"><?php echo JText::sprintf('COM_HWDMS_X_VIEWS', number_format((int) $item->hits)); ?></dd>
          <?php endif; ?>
        </dl>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div> 
  <?php if ($params->get('show_more_link') != 'hide') :?><p><a href="<?php echo ((intval($params->get('show_more_link')) > 0) ? JRoute::_($menu->getItem($params->get('show_more_link'))->link.'&Itemid='.$params->get('show_more_link')) : JRoute::_(hwdMediaShareHelperRoute::getMediaRoute())); ?>" class="btn"><?php echo JText::_($params->get('more_link_text', 'MOD_MEDIA_MEDIA_VIEW_MORE')); ?></a></p><?php endif; ?>  
</div>