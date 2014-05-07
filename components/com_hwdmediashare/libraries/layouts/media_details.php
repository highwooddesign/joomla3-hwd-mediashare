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

$user = JFactory::getUser();
JHtml::_('bootstrap.tooltip');
?>
<?php foreach ($displayData->items as $id => $item) :
$rowcount = (((int)$id) % (int) $displayData->columns) + 1;
$item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;
$canEdit = ($user->authorise('core.edit', 'com_hwdmediashare.media.'.$item->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.media.'.$item->id) && ($item->created_user_id == $user->id)));
$canEditState = $user->authorise('core.edit.state', 'com_hwdmediashare.media.'.$item->id);
$canDelete = ($user->authorise('core.delete', 'com_hwdmediashare.media.'.$item->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.media.'.$item->id) && ($item->created_user_id == $user->id)));
?>
<?php if ($rowcount == 1) : ?>
<!-- Row -->
<div class="row-fluid">
<?php endif; ?>
  <!-- Cell -->
  <div class="span<?php echo intval(12/$displayData->columns); ?>">
    <!-- Thumbnail Image -->
    <div class="media-item">
      <div class="media-aspect<?php echo $displayData->params->get('list_thumbnail_aspect'); ?>"></div>        
      <!-- Media Type -->
      <?php if ($displayData->params->get('list_meta_thumbnail') != 'hide') :?>
      <?php if ($displayData->params->get('list_meta_type_icon') != 'hide') :?>
      <div class="media-item-format-1-<?php echo $item->media_type; ?>">
         <img src="<?php echo JHtml::_('hwdicon.overlay', '1-'.$item->media_type, $item); ?>" alt="<?php echo JText::_('COM_HWDMS_MEDIA_TYPE'); ?>" />
      </div>
      <?php endif; ?>
      <?php if ($displayData->params->get('list_meta_duration') != 'hide' && $item->duration > 0) :?>
      <div class="media-duration">
         <?php echo hwdMediaShareMedia::secondsToTime($item->duration); ?>
      </div>
      <?php endif; ?>
      <?php if ($displayData->params->get('list_link_thumbnails') == 1) :?><a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getMediaItemRoute($item->slug)); ?>"><?php endif; ?>
         <img src="<?php echo JRoute::_(hwdMediaShareDownloads::thumbnail($item)); ?>" border="0" alt="<?php echo $displayData->escape($item->title); ?>" class="media-thumb<?php echo ($displayData->params->get('list_tooltip_location') > '2' ? ' hasTooltip' : ''); ?>" title="<?php echo JHtml::tooltipText($item->title, ($displayData->params->get('list_tooltip_contents') != '0' ? JHtmlString::truncate($item->description, $displayData->params->get('list_desc_truncate'), false, false) : '')); ?>" />
      <?php if ($displayData->params->get('list_link_thumbnails') == 1) :?></a><?php endif; ?>
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
      echo JHtml::_('hwddropdown.render', $this->escape($item->title), ' btn-micro');
      ?>                    
    </div>
    <?php endif; ?>
    <!-- Title -->
    <?php if ($displayData->params->get('list_meta_title') != 'hide') :?>
      <h<?php echo $displayData->params->get('list_item_heading'); ?> class="contentheading<?php echo ($displayData->params->get('list_tooltip_location') > '1' ? ' hasTooltip' : ''); ?>" title="<?php echo JHtml::tooltipText($item->title, ($displayData->params->get('list_tooltip_contents') != '0' ? JHtmlString::truncate($item->description, $displayData->params->get('list_desc_truncate'), false, false) : '')); ?>">
        <?php if ($displayData->params->get('list_link_titles') == 1) :?><a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getMediaItemRoute($item->slug)); ?>"><?php endif; ?>
          <?php echo $displayData->escape(JHtmlString::truncate($item->title, $displayData->params->get('list_title_truncate'), false, false)); ?> 
        <?php if ($displayData->params->get('list_link_titles') == 1) :?></a><?php endif; ?>
      </h<?php echo $displayData->params->get('list_item_heading'); ?>>
    <?php endif; ?>         
    <!-- Clears Item and Information -->
    <div class="clear"></div>
    <?php if ($item->featured): ?>
      <span class="label label-info"><?php echo JText::_('COM_HWDMS_FEATURED'); ?></span>
    <?php endif; ?>
    <?php if ($item->status != 1) : ?>
      <span class="label label-danger"><?php echo $displayData->utilities->getReadableStatus($item); ?></span>
    <?php endif; ?>
    <?php if ($item->published != 1) : ?>
      <span class="label label-danger"><?php echo JText::_('COM_HWDMS_UNPUBLISHED'); ?></span>
    <?php endif; ?>        
    <!-- Clears Item and Information -->
    <div class="clear"></div>
    <?php if ($displayData->params->get('list_meta_description') != 'hide' || $displayData->params->get('list_meta_category') != 'hide' || $displayData->params->get('list_meta_author') != 'hide' || $displayData->params->get('list_meta_created') != 'hide' || $displayData->params->get('list_meta_likes') != 'hide' || $displayData->params->get('list_meta_hits') != 'hide') : ?>
    <!-- Item Meta -->
    <dl class="media-info">
      <dt class="media-info-term"><?php echo JText::_('COM_HWDMS_DETAILS'); ?> </dt>
      <?php if ($displayData->params->get('list_meta_author') != 'hide' || $displayData->params->get('list_meta_created') != 'hide') : ?>
        <dd class="media-info-createdby">
          <?php if ($displayData->params->get('list_meta_author') != 'hide') : ?><?php echo JText::sprintf('COM_HWDMS_BY_X_USER', '<a href="'.JRoute::_(hwdMediaShareHelperRoute::getUserRoute($item->created_user_id)).'">'.htmlspecialchars($item->author, ENT_COMPAT, 'UTF-8').'</a>'); ?><?php endif; ?><?php if ($displayData->params->get('list_meta_created') != 'hide') : ?>, <?php echo JHtml::_('date.relative', $item->created); ?><?php endif; ?>
        </dd>
      <?php endif; ?>
      <?php if ($displayData->params->get('list_meta_category') != 'hide' && $displayData->params->get('enable_categories') && (count($item->categories) > 0)) : ?>
        <dd class="media-info-category"><?php echo JText::sprintf('COM_HWDMS_IN_X_CATEGORY', $displayData->getCategories($item)); ?></dd>
      <?php endif; ?>              
      <?php if ($displayData->params->get('list_meta_description') != 'hide' && !empty($item->description)) :?>
        <dd class="media-info-description"> <?php echo $displayData->escape(JHtmlString::truncate($item->description, $displayData->params->get('list_desc_truncate'), false, false)); ?> </dd>
      <?php endif; ?>
      <?php if ($displayData->params->get('list_meta_hits') != 'hide') :?>
        <dd class="media-info-hits"><?php echo JText::sprintf('COM_HWDMS_X_VIEWS', number_format((int) $item->hits)); ?></dd>
      <?php endif; ?>
    </dl>
    <?php endif; ?> 
  </div>
<?php if (($rowcount == $displayData->columns) or (($id + 1) == count($displayData->items))): ?>
</div>
<?php endif; ?>
<?php endforeach; ?>