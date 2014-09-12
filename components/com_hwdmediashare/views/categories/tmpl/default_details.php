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

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/html');

$user = JFactory::getUser();
JHtml::_('bootstrap.tooltip');
?>
<?php foreach ($this->items[$this->parent->id] as $id => $item) :
$rowcount = (((int)$id) % (int) $this->columns) + 1;
$item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;
$canEdit = ($user->authorise('core.edit', 'com_hwdmediashare.category.'.$item->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.category.'.$item->id) && ($item->created_user_id == $user->id)));
$canEditState = $user->authorise('core.edit.state', 'com_hwdmediashare.category.'.$item->id);
$canDelete = ($user->authorise('core.delete', 'com_hwdmediashare.category.'.$item->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.category.'.$item->id) && ($item->created_user_id == $user->id)));
?>
<?php if ($rowcount == 1) : ?>
<!-- Row -->
<div class="row-fluid">
<?php endif; ?>
  <!-- Cell -->
  <div class="span<?php echo intval(12/$this->columns); ?>">
    <!-- Thumbnail Image -->
    <div class="media-item">
      <div class="media-aspect<?php echo $this->params->get('list_thumbnail_aspect'); ?>"></div>        
      <!-- Media Type -->
      <?php if ($this->params->get('list_meta_thumbnail') != '0') :?>
      <?php if ($this->params->get('list_meta_type_icon') != '0') :?>
      <div class="media-item-format-6">
         <img src="<?php echo JHtml::_('hwdicon.overlay', 6, $item); ?>" alt="<?php echo JText::_('COM_HWDMS_CATEGORY'); ?>" />
      </div>
      <?php endif; ?>
      <?php if ($this->params->get('list_link_thumbnails') == 1) :?><a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getCategoryRoute($item->slug)); ?>"><?php endif; ?>
         <img src="<?php echo JRoute::_(hwdMediaShareThumbnails::thumbnail($item, 6)); ?>" border="0" alt="<?php echo $this->escape($item->title); ?>" class="media-thumb<?php echo ($this->params->get('list_tooltip_location') > '2' ? ' hasTooltip' : ''); ?>" title="<?php echo JHtml::tooltipText($item->title, ($this->params->get('list_tooltip_contents') != '0' ? JHtmlString::truncate($item->description, $this->params->get('list_desc_truncate'), false, false) : '')); ?>" />
      <?php if ($this->params->get('list_link_thumbnails') == 1) :?></a><?php endif; ?>
      <?php endif; ?>
    </div>
    <?php if ($canEdit || $canDelete): ?>
    <div class="btn-group pull-right">
      <?php
      // Create dropdown items
      if ($canEdit) : 
        JHtml::_('hwddropdown.edit', $item->id, 'categoryform'); 
      endif;    
      if ($canEditState) :
        $action = $item->published == 1 ? 'unpublish' : 'publish';
        JHtml::_('hwddropdown.' . $action, $item->id, 'categories'); 
      endif; 
      if ($canDelete && $item->published != -2) : 
        JHtml::_('hwddropdown.delete', $item->id, 'categories'); 
      endif;         
      // Render dropdown list
      echo JHtml::_('hwddropdown.render', $this->escape($item->title), ' btn-micro');
      ?>                    
    </div>
    <?php endif; ?>
    <!-- Title -->
    <?php if ($this->params->get('list_meta_title') != '0') :?>
      <h<?php echo $this->params->get('list_item_heading'); ?> class="contentheading<?php echo ($this->params->get('list_tooltip_location') > '1' ? ' hasTooltip' : ''); ?>" title="<?php echo JHtml::tooltipText($item->title, ($this->params->get('list_tooltip_contents') != '0' ? JHtmlString::truncate($item->description, $this->params->get('list_desc_truncate'), false, false) : '')); ?>">
        <?php if ($this->params->get('list_link_titles') == 1) :?><a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getCategoryRoute($item->slug)); ?>"><?php endif; ?>
          <?php echo $this->escape(JHtmlString::truncate($item->title, $this->params->get('list_title_truncate'))); ?> 
        <?php if ($this->params->get('list_link_titles') == 1) :?></a><?php endif; ?>
      </h<?php echo $this->params->get('list_item_heading'); ?>>
    <?php endif; ?>        
    <!-- Clears Item and Information -->
    <div class="clear"></div>
    <?php if ($item->published != 1) : ?>
      <span class="label"><?php echo JText::_('COM_HWDMS_UNPUBLISHED'); ?></span>
    <?php endif; ?>        
    <div class="clear"></div>
    <?php if ($this->params->get('list_meta_description') != '0' || $this->params->get('category_list_meta_media_count') != '0' || $this->params->get('category_list_meta_subcategory_count') != '0') : ?>
    <!-- Item Meta -->
    <dl class="media-info">
      <dt class="media-info-term"><?php echo JText::_('COM_HWDMS_DETAILS'); ?> </dt>
      <?php if ($this->params->get('list_meta_description') != '0') :?>
        <dd class="media-info-description"><?php echo $this->escape(JHtmlString::truncate($item->description, $this->params->get('list_desc_truncate'), false, false)); ?></dd>
      <?php endif; ?>
      <div class="clearfix"></div>       
      <?php if ($this->params->get('category_list_meta_media_count') != '0') :?>
        <dd class="media-info-count"><?php echo JText::plural('COM_HWDMS_X_MEDIA_COUNT', (int) $item->numitems); ?></dd>
      <?php endif; ?>     
      <?php if ($this->params->get('category_list_meta_subcategory_count') != '0' && count($item->getChildren()) > 0) :?>
        <dd class="media-info-count"><?php echo JText::plural('COM_HWDMS_X_SUBCATEGORY_COUNT', (int) count($item->getChildren())); ?></dd>
      <?php endif; ?> 
    </dl>
    <?php endif; ?> 
  </div>
<?php if (($rowcount == $this->columns) or (($id + 1) == count($this->items[$this->parent->id]))): ?>
</div>
<?php endif; ?>
<?php endforeach; ?>
