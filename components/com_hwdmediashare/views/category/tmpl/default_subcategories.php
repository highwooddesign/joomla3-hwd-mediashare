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

JHtml::_('bootstrap.tooltip');

$subcatcols = min($this->columns, 3);
?>
<?php if (count($this->subcategories)) : ?>
  <h2 class="media-category-title"><?php echo JText::_('COM_HWDMS_SUBCATEGORIES'); ?></h2>
  <div class="clear"></div>
  <div class="media-details-view">
    <?php foreach ($this->subcategories as $id => $item) :
    $rowcount = (((int)$id) % (int) $subcatcols) + 1;
    $item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;
    ?>
      <?php if ($rowcount == 1) : ?>
      <!-- Row -->
      <div class="row-fluid">
      <?php endif; ?>
        <!-- Column -->
        <div class="span<?php echo intval(12/$subcatcols); ?>">
          <!-- Cell -->
          <a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getCategoryRoute($item->slug)); ?>" class="row-fluid">
            <?php if ($this->params->get('list_meta_thumbnail') != '0') :?>
            <div class="span4">
              <div class="media-item<?php echo ($this->params->get('list_thumbnail_aspect') == 0 ? ' originalaspect' : ''); ?>">
                <div class="media-aspect<?php echo $this->params->get('list_thumbnail_aspect'); ?>"></div>        
                  <img src="<?php echo JRoute::_(hwdMediaShareThumbnails::thumbnail($item, 6)); ?>" border="0" alt="<?php echo $this->escape($item->title); ?>" class="media-thumb<?php echo ($this->params->get('list_tooltip_location') > '2' ? ' hasTooltip' : ''); ?>" title="<?php echo JHtml::tooltipText($item->title, ($this->params->get('list_tooltip_contents') != '0' ? JHtml::_('string.truncate', $item->description, $this->params->get('list_desc_truncate'), false, false) : '')); ?>" />
              </div>    
            </div>
            <?php endif; ?>
            <div class="span<?php echo $this->params->get('list_meta_thumbnail') != '0' ? 8: 12; ?>">
              <?php if ($this->params->get('list_meta_title') != '0') :?>
                <h<?php echo $this->params->get('list_item_heading'); ?> class="contentheading<?php echo ($this->params->get('list_tooltip_location') > '1' ? ' hasTooltip' : ''); ?>" title="<?php echo JHtml::tooltipText($item->title, ($this->params->get('list_tooltip_contents') != '0' ? JHtml::_('string.truncate', $item->description, $this->params->get('list_desc_truncate'), false, false) : '')); ?>">
                  <?php echo $this->escape(JHtml::_('string.truncate', $item->title, $this->params->get('list_title_truncate'))); ?> 
                </h<?php echo $this->params->get('list_item_heading'); ?>>
              <?php endif; ?>
              <?php if ($this->params->get('list_meta_description') != '0') : ?>
                <?php echo $this->escape(JHtml::_('string.truncate', $item->description, $this->params->get('list_desc_truncate'), false, false)); ?>
              <?php endif; ?>
                <?php if ($this->params->get('category_list_meta_media_count') != '0' || $this->params->get('category_list_meta_subcategory_count') != '0') : ?>
                <!-- Item Meta -->
                <dl class="media-info">
                  <dt class="media-info-term"><?php echo JText::_('COM_HWDMS_DETAILS'); ?> </dt>  
                  <?php if ($this->params->get('category_list_meta_media_count') != '0' && count($item->numitems)) :?>
                    <dd class="media-info-count"><?php echo JText::plural('COM_HWDMS_X_MEDIA_COUNT', (int) $item->numitems); ?></dd>
                  <?php endif; ?>
                  <?php if ($this->params->get('category_list_meta_subcategory_count') != '0' && count($item->getChildren())) :?>
                    <dd class="media-info-count"><?php echo JText::plural('COM_HWDMS_X_SUBCATEGORY_COUNT', (int) count($item->getChildren())); ?></dd>
                  <?php endif; ?>
                </dl>
                <?php endif; ?>
            </div>
          </a>
        </div>
      <?php if (($rowcount == $subcatcols) or (($id + 1) == count($this->subcategories))): ?>
      </div>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>  
<?php endif; ?>
