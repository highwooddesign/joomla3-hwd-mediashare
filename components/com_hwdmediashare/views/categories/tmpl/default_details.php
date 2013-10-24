<?php
/**
 * @version    SVN $Id: default_details.php 1716 2013-10-17 14:43:55Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Nov-2011 10:26:33
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$user = JFactory::getUser();

$row=0;
$counter=0;
$leadingcount=0;
$introcount=0;
?>
<!-- View Container -->
<div class="media-details-view">
<?php foreach ($this->items[$this->parent->id] as $id => $item) :
$id= ($id-$leadingcount)+1;
$rowcount=( ((int)$id-1) %	(int) $this->columns) +1;
$row = $counter / $this->columns ;
$item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;
$canEdit = ($user->authorise('core.edit', 'com_hwdmediashare.category.'.$item->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.category.'.$item->id) && ($item->created_user_id == $user->id)));
$canEditState = $user->authorise('core.edit.state', 'com_hwdmediashare.category.'.$item->id);
$canDelete = ($user->authorise('core.delete', 'com_hwdmediashare.category.'.$item->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.category.'.$item->id) && ($item->created_user_id == $user->id)));

// Load cateogry media
JRequest::setVar('category_id', $item->id);
$this->assignRef('media', $this->getCategoryMedia($item));

// Set tooltip position
$hwdTooltipPos = "position: {edge: 'left', position: 'right'}";
if ($rowcount == $this->columns) $hwdTooltipPos = "position: {edge: 'right', position: 'left'}";
?>
  <?php if (count($this->media) > 0) : ?>
    <!-- Tooltip container-->
    <div class="tipContainer" id="tipContainer<?php echo $item->id; ?>"><?php echo $this->loadTemplate('media_list'); ?></div>
  <?php endif; ?>
  <!-- Row -->
  <?php if ($rowcount == 1) : ?>
  <div class="items-row cols-<?php echo (int) $this->columns;?> <?php echo 'row-'.$row ; ?>">
    <?php endif; ?>
    <!-- Column -->
    <div id="hwd-tooltip-<?php echo $item->id; ?>" class="item column-<?php echo $rowcount;?><?php echo ($item->published != '1' ? ' system-unpublished' : false); ?>">
      <!-- Cell -->
      <?php if ($item->published != '1') : ?>
      <div class="system-unpublished">
        <?php endif; ?>
        <!-- Thumbnail Image -->
        <div class="media-item">
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
        <!-- Title -->
        <?php if ($this->params->get('list_meta_title') != 'hide') :?>
          <h<?php echo $this->params->get('list_item_heading'); ?> class="contentheading<?php echo ($this->params->get('list_tooltip_location') > '1' ? ' hasTip' : ''); ?>" title="<?php echo $this->escape($item->title); ?>::<?php echo ($this->params->get('list_tooltip_contents') != '0' ? $this->escape(JHtmlString::truncate($item->description, $this->params->get('list_desc_truncate'), true, false)) : ''); ?>">
            <?php if ($this->params->get('list_link_titles') == 1) :?><a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getCategoryRoute($item->slug)); ?>"><?php endif; ?>
              <?php echo $this->escape(JHtmlString::truncate($item->title, $this->params->get('list_title_truncate'))); ?> 
            <?php if ($this->params->get('list_link_titles') == 1) :?></a><?php endif; ?>
          </h<?php echo $this->params->get('list_item_heading'); ?>>
        <?php endif; ?>        
        <!-- Clears Item and Information -->
        <div class="clear"></div>
        <!-- Item Meta -->
        <?php if ($this->params->get('category_list_meta_category_desc') != 'hide') :?>
          <div class="media-category-description">
            <?php echo $this->escape(JHtmlString::truncate(strip_tags($item->description), $this->params->get('list_desc_truncate'))); ?>
          </div>
        <?php endif; ?>
        <?php if ($this->params->get('category_list_meta_subcategory_count') != 'hide' || $this->params->get('category_list_meta_media_count') != 'hide') :?>
          <dl class="article-info">
            <dt class="article-info-term"><?php echo JText::_('COM_HWDMS_DETAILS'); ?> </dt>
            <?php if ($this->params->get('category_list_meta_media_count') != 'hide') :?>
              <dd class="media-info-count"> <?php echo JText::_('COM_HWDMS_MEDIA'); ?> (<?php echo (int) $item->numitems; ?>)</dd>
            <?php endif; ?>
            <?php if ($this->params->get('category_list_meta_subcategory_count') != 'hide' && count($item->getChildren()) > 0) :?>
              <dd class="media-info-subcategories"> <?php echo JText::_('COM_HWDMS_SUBCATEGORIES'); ?> (<?php echo (int) count($item->getChildren()); ?>)</dd>
            <?php endif; ?>
          </dl>
        <?php endif; ?>
      <?php if ($item->published != '1') : ?>
      </div>
      <?php endif; ?>
      <div class="item-separator"></div>
    </div>
    <?php if (count($this->media) > 0) : ?>
    <!-- Tooltip Javascript -->
    <script type="text/javascript">
    window.addEvent('domready', function() {
            document.id('hwd-tooltip-<?php echo $item->id; ?>').addEvent('mouseenter', function() {
                    ToolTip.instance(this, {
                            autohide: true,
                            <?php echo $hwdTooltipPos; ?>
                    }, new Element('div', {
                            html: document.id('tipContainer<?php echo $item->id; ?>').innerHTML
                            }
                    )).show();
            });
    });
    </script>
    <?php endif; ?>
  <?php if (($rowcount == $this->columns) or (($counter + 1) == count($this->items[$this->parent->id]))): ?>
  <span class="row-separator"></span>
  </div>
  <?php endif; ?>
  <?php $counter++; ?>
  <?php endforeach; ?>
</div>






