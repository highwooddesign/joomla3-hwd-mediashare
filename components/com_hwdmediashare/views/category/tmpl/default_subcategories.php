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
?>
<?php if (count($this->subcategories)) : ?>
  <h2 class="media-category-title"><?php echo JText::_('COM_HWDMS_SUBCATEGORIES'); ?></h2>
  <div class="clear"></div>
  <div class="media-details-view">
    <?php foreach ($this->subcategories as $id => $item) :
    $rowcount = (((int)$id) % (int) ($this->columns-1)) + 1;
    $item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;
    ?>
      <?php if ($rowcount == 1) : ?>
      <!-- Row -->
      <div class="row-fluid">
      <?php endif; ?>
        <!-- Column -->
        <div class="span<?php echo intval(12/($this->columns-1)); ?>">
          <!-- Cell -->
          <a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getCategoryRoute($item->slug)); ?>" class="row-fluid">
            <div class="span4">
              <div class="media-item">
                <div class="media-aspect<?php echo $this->params->get('list_thumbnail_aspect'); ?>"></div>        
                <?php if ($this->params->get('list_meta_thumbnail') != 'hide') :?>
                  <img src="<?php echo JRoute::_(hwdMediaShareDownloads::thumbnail($item, 6)); ?>" border="0" alt="<?php echo $this->escape($item->title); ?>" class="media-thumb<?php echo ($this->params->get('list_tooltip_location') > '2' ? ' hasTooltip' : ''); ?>" title="<?php echo JHtml::tooltipText($item->title, ($this->params->get('list_tooltip_contents') != '0' ? JHtmlString::truncate($item->description, $this->params->get('list_desc_truncate'), false, false) : '')); ?>" />
                <?php endif; ?>
              </div>    
            </div>
            <div class="span8">
              <?php if ($this->params->get('list_meta_title') != 'hide') :?>
                <h<?php echo $this->params->get('list_item_heading'); ?> class="contentheading<?php echo ($this->params->get('list_tooltip_location') > '1' ? ' hasTooltip' : ''); ?>" title="<?php echo JHtml::tooltipText($item->title, ($this->params->get('list_tooltip_contents') != '0' ? JHtmlString::truncate($item->description, $this->params->get('list_desc_truncate'), false, false) : '')); ?>">
                  <?php echo $this->escape(JHtmlString::truncate($item->title, $this->params->get('list_title_truncate'))); ?> 
                </h<?php echo $this->params->get('list_item_heading'); ?>>
              <?php endif; ?>
              <?php echo $this->escape(JHtmlString::truncate($item->description, $this->params->get('list_desc_truncate'), false, false)); ?>
            </div>
          </a>
        </div>
      <?php if (($rowcount == ($this->columns-1)) or (($id + 1) == count($this->subcategories))): ?>
      </div>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>  
<?php endif; ?>
