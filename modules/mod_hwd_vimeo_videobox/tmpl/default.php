<?php
/**
 * @package     Joomla.site
 * @subpackage  Module.mod_hwd_vimeo_videobox
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;
?>
<div class="row hwd-strapped3 hwd-vimeo-videobox-default">
<?php foreach ($helper->items as $id => $item) : ?>
  <!-- Clear the columns if their content doesn't match in height -->
  <?php if ($id % 1 == 0) :?><div class="clearfix visible-xs"></div><?php endif; ?>
  <?php if ($id % 2 == 0) :?><div class="clearfix visible-sm"></div><?php endif; ?>
  <?php if ($id % $params->get('columns', 3) == 0) :?><div class="clearfix visible-md visible-lg"></div><?php endif; ?>    
  <div class="cell col-xs-12 col-sm-6 col-md-<?php echo intval(12/$params->get('columns', 3)); ?>">
    <div class="media-item hasTooltip" title="<?php echo JHtml::tooltipText($item->title, JHtml::_('string.truncate', $item->description, 300, true, false)); ?>">
      <div class="media-aspect"></div>        
      <?php if ($item->duration > 0 && $params->get('show_duration', 1)) :?>
      <div class="media-duration">
         <?php echo $helper->secondsToTime($item->duration); ?>
      </div>
      <?php endif; ?>
      <a class="popup-thumbnail-<?php echo $module->id; ?>" href="http://player.vimeo.com/video/<?php echo $item->id; ?>?color=<?php echo $params->get('color'); ?>&autoplay=<?php echo $params->get('autoplay'); ?>&title=<?php echo $params->get('title'); ?>" id="mb<?php echo $id; ?>" title="<?php echo htmlspecialchars($item->title); ?>">
         <img src="<?php echo $item->thumbnail; ?>" border="0" alt="" title="" class="media-thumb"  />
         <?php echo ($params->get('icon', 1) ? '<span class="media-link-span"></span></span>' : ''); ?>
      </a>
    </div>
    <div class="clear"></div>        
    <?php if ($params->get('show_title') != 'hide') :?>
      <div class="hasTooltip title" title="<?php echo JHtml::tooltipText($item->title, JHtml::_('string.truncate', $item->description, 300, true, false)); ?>">
        <a class="popup-title-<?php echo $module->id; ?>" href="http://player.vimeo.com/video/<?php echo $item->id; ?>?color=<?php echo $params->get('color'); ?>&autoplay=<?php echo $params->get('autoplay'); ?>&title=<?php echo $params->get('title'); ?>" id="mb<?php echo $id; ?>" title="<?php echo htmlspecialchars($item->title); ?>">
          <?php echo JHtml::_('string.truncate', $item->title, 50); ?> 
        </a>
      </div>
    <?php endif; ?>
    <div class="clear"></div>
    <?php if ($params->get('show_date', 1)):?><div class="small pull-right"><?php echo JHtml::_('date.relative', $item->uploadDate); ?></div><?php endif; ?>
    <?php if ($params->get('show_views', 1) && $item->views):?><div class="small"><?php echo JText::sprintf('MOD_HWD_VIMEO_VIDEOBOX_X_VIEWS', number_format($item->views)); ?></div><?php endif; ?>
 </div>
 <?php endforeach; ?>
</div>
