<?php
/**
 * @version    SVN $Id: default_tree.php 1222 2013-03-05 13:34:15Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$user = JFactory::getUser();

JHtml::_('behavior.tooltip');

?>
<div class="categories-list">
  <?php
  $class = ' class="first"';
  if (count($this->items[$this->parent->id]) > 0 && $this->maxLevelcat != 0) :
  ?>
  <ul>
  <?php foreach($this->items[$this->parent->id] as $id => $item) :
  $canEdit = $user->authorise('core.edit', 'com_hwdmediashare.category.'.$item->id);
  $canEditState = $user->authorise('core.edit.state', 'com_hwdmediashare.category.'.$item->id);
  $canDelete = $user->authorise('core.delete', 'com_hwdmediashare.category.'.$item->id);
  ?>
    <?php
    if(!isset($this->items[$this->parent->id][$id + 1]))
    {
            $class = ' class="last"';
    }
    ?>
    <li<?php echo $class; ?>>
      <?php $class = ''; ?>
      <?php if ($this->params->get('list_meta_title') != 'hide') :?>
        <span class="item-title<?php echo ($this->params->get('list_tooltip_location') > '1' ? ' hasTip' : ''); ?>" title="<?php echo $this->escape($item->title); ?>::<?php echo ($this->params->get('list_tooltip_contents') != '0' ? $this->escape(JHtmlString::truncate($item->description, $this->params->get('list_desc_truncate'), true, false)) : ''); ?>">
        <?php if ($this->params->get('list_link_titles') == 1) :?><a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getCategoryRoute($item->slug)); ?>"><?php endif; ?>
          <?php echo $this->escape($item->title); ?>
        <?php if ($this->params->get('list_link_titles') == 1) :?></a><?php endif; ?>
        </span>
      <?php endif; ?>        
      <div class="category-desc">
        <!-- Thumbnail Image -->
        <div class="media-item image-left">
          <div class="media-aspect<?php echo $this->params->get('list_thumbnail_aspect'); ?>"></div>                    
          <?php if ($canEdit || $canDelete): ?>
          <!-- Actions -->
          <ul class="media-nav">
            <li><a href="#" class="pagenav-manage"><?php echo JText::_('COM_HWDMS_MANAGE'); ?> </a>
              <ul class="media-subnav">
                <?php if ($canEdit) : ?>
                <li><?php echo JHtml::_('hwdicon.edit', $this->view_item, $item, $this->params); ?></li>
                <?php endif; ?>
                <?php if ($canEditState) : ?>
                <?php if ($item->published != '1') : ?>
                <li><?php echo JHtml::_('hwdicon.publish', $this->view_item, $item, $this->params); ?></li>
                <?php else : ?>
                <li><?php echo JHtml::_('hwdicon.unpublish', $this->view_item, $item, $this->params); ?></li>
                <?php endif; ?>
                <?php endif; ?>
                <?php if ($canDelete) : ?>
                <li><?php echo JHtml::_('hwdicon.delete', $this->view_item, $item, $this->params); ?></li>
                <?php endif; ?>
              </ul>
            </li>
          </ul>
          <?php endif; ?>
          <!-- Media Type -->
          <?php if ($this->params->get('list_meta_thumbnail') != 'hide') :?>
          <?php if ($this->params->get('list_meta_type_icon') != 'hide') :?>
          <div class="media-item-format-<?php echo $this->elementType; ?>">
             <img src="<?php echo JHtml::_('hwdicon.overlay', $this->elementType, $item); ?>" alt="<?php echo $this->elementName; ?>" />
          </div>
          <?php endif; ?>
          <?php if ($this->params->get('list_link_thumbnails') == 1) :?><a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getCategoryRoute($item->slug)); ?>"><?php endif; ?>
             <img src="<?php echo JRoute::_(hwdMediaShareDownloads::thumbnail($item, $this->elementType)); ?>" border="0" alt="<?php echo $this->escape($item->title); ?>" class="media-thumb <?php echo ($this->params->get('list_tooltip_location') > '2' ? 'hasTip' : ''); ?>" title="<?php echo $this->escape($item->title); ?>::<?php echo ($this->params->get('list_tooltip_contents') != '0' ? $this->escape(JHtmlString::truncate($item->description, $this->params->get('list_desc_truncate'), true, false)) : ''); ?>" />
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