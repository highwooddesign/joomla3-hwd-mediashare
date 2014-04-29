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
$class = ' class="first"';

?>
<div class="categories-list">
  <?php if (count($this->items[$this->parent->id]) > 0 && $this->maxLevelcat != 0) : ?>
  <ul>
  <?php foreach($this->items[$this->parent->id] as $id => $item) :
  $canEdit = $user->authorise('core.edit', 'com_hwdmediashare.category.'.$item->id);
  $canEditState = $user->authorise('core.edit.state', 'com_hwdmediashare.category.'.$item->id);
  $canDelete = $user->authorise('core.delete', 'com_hwdmediashare.category.'.$item->id); 
  ?>
    <?php if(!isset($this->items[$this->parent->id][$id + 1])) $class = ' class="last"'; ?>
    <li<?php echo $class; ?>>
      <?php $class = ''; ?>
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
      <?php if ($this->params->get('list_meta_title') != 'hide') :?>
        <span class="item-title<?php echo ($this->params->get('list_tooltip_location') > '1' ? ' hasTooltip' : ''); ?>" title="<?php echo JHtml::tooltipText($item->title, ($this->params->get('list_tooltip_contents') != '0' ? JHtmlString::truncate($item->description, $this->params->get('list_desc_truncate'), false, false) : '')); ?>">
        <?php if ($this->params->get('list_link_titles') == 1) :?><a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getCategoryRoute($item->slug)); ?>"><?php endif; ?>
          <?php echo $this->escape($item->title); ?>
        <?php if ($this->params->get('list_link_titles') == 1) :?></a><?php endif; ?>
        </span>
      <?php endif; ?>        
      <div class="category-desc">
        <!-- Thumbnail Image -->
        <div class="media-item pull-left">
          <div class="media-aspect<?php echo $this->params->get('list_thumbnail_aspect'); ?>"></div>
          <!-- Media Type -->
          <?php if ($this->params->get('list_meta_thumbnail') != 'hide') :?>
          <?php if ($this->params->get('list_meta_type_icon') != 'hide') :?>
          <div class="media-item-format-2">
             <img src="<?php echo JHtml::_('hwdicon.overlay', 6, $item); ?>" alt="<?php echo JText::_('COM_HWDMS_CATEGORY'); ?>" />
          </div>
          <?php endif; ?>
          <?php if ($this->params->get('list_link_thumbnails') == 1) :?><a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getCategoryRoute($item->slug)); ?>"><?php endif; ?>
             <img src="<?php echo JRoute::_(hwdMediaShareDownloads::thumbnail($item, 6)); ?>" border="0" alt="<?php echo $this->escape($item->title); ?>" class="media-thumb <?php echo ($this->params->get('list_tooltip_location') > '2' ? ' hasTooltip' : ''); ?>" title="<?php echo JHtml::tooltipText($item->title, ($this->params->get('list_tooltip_contents') != '0' ? JHtmlString::truncate($item->description, $this->params->get('list_desc_truncate'), false, false) : '')); ?>" />
          <?php if ($this->params->get('list_link_thumbnails') == 1) :?></a><?php endif; ?>
          <?php endif; ?>
        </div>
        <?php if ($this->params->get('category_list_meta_category_desc') != 'hide') :?>
          <?php echo $this->escape(JHtmlString::truncate(strip_tags($item->description), $this->params->get('list_desc_truncate'))); ?>
        <?php endif; ?>
      </div>
      <!-- Item Meta -->
      <?php if ($this->params->get('category_list_meta_subcategory_count') != 'hide' || $this->params->get('category_list_meta_media_count') != 'hide') :?>
      <dl>
        <?php if ($this->params->get('category_list_meta_media_count') != 'hide') :?>
        <dt><?php echo JText::_('COM_HWDMS_MEDIA'); ?></dt>
        <dd>(<?php echo (int) $item->numitems; ?>)</dd>
        <?php endif; ?>
        <?php if ($this->params->get('category_list_meta_subcategory_count') != 'hide' && count($item->getChildren()) > 0) :?>
        <dt><?php echo JText::_('COM_HWDMS_SUBCATEGORIES'); ?></dt>
        <dd>(<?php echo (int) count($item->getChildren()); ?>)</dd>
        <?php endif; ?>
      </dl>
      <?php endif; ?>
      <!-- Item Children -->
      <?php if(count($item->getChildren()) > 0) :
        $this->items[$item->id] = $item->getChildren();
        $this->parent = $item;
        $this->maxLevelcat--;
        echo $this->loadTemplate('tree');
        $this->parent = $item->getParent();
        $this->maxLevelcat++;
      endif; ?>
    </li>
  <?php endforeach; ?>
  </ul>
  <?php endif; ?>
</div>