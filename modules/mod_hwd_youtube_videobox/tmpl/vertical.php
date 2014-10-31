<?php
/**
 * @package     Joomla.site
 * @subpackage  Module.mod_hwd_youtube_videobox
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;
?>
<div class="row mod_hwd_youtube_videobox_vertical">
<?php foreach ($helper->items as $id => $item) : ?>
  <!-- Clear the columns if their content doesn't match in height -->
  <div class="clearfix visible-xs"></div>
  <div class="clearfix visible-sm"></div>
  <div class="clearfix visible-md visible-lg"></div>
  <div class="cell col-xs-12 col-sm-12 col-md-12">
    <div class="media-item hasTooltip" title="<?php echo JHtml::tooltipText($item->title, JHtml::_('string.truncate', $item->description, 300, true, false)); ?>">
      <div class="media-aspect"></div>        
      <?php if ($item->duration > 0 && $params->get('show_duration', 1)) :?>
      <div class="media-duration">
         <?php echo $helper->secondsToTime($item->duration); ?>
      </div>
      <?php endif; ?>
      <a class="popup-thumbnail-<?php echo $module->id; ?>" href="https://www.youtube.com/embed/<?php echo $item->id; ?>?wmode=opaque&amp;autohide=<?php echo $params->get('autohide',2); ?>&amp;border=<?php echo $params->get('border',0); ?>&amp;cc_load_policy=<?php echo $params->get('cc_load_policy',1); ?>&amp;color=<?php echo $params->get('color','red'); ?>&amp;color1=<?php echo $params->get('color1'); ?>&amp;color2=<?php echo $params->get('color2'); ?>&amp;controls=<?php echo $params->get('controls',1); ?>&amp;fs=<?php echo $params->get('fs',0); ?>&amp;hd=<?php echo $params->get('hd',0); ?>&amp;iv_load_policy=<?php echo $params->get('iv_load_policy',1); ?>&amp;modestbranding=<?php echo $params->get('modestbranding',1); ?>&amp;rel=<?php echo $params->get('rel',1); ?>&amp;theme=<?php echo $params->get('theme','dark'); ?>" id="mb<?php echo $id; ?>" title="<?php echo htmlspecialchars($item->title); ?>">
         <img src="<?php echo $item->thumbnail; ?>" border="0" alt="" title="" class="media-thumb"  />
         <?php echo ($params->get('icon', 1) ? '<span class="media-link-span"></span></span>' : ''); ?>
      </a>
    </div>
    <div class="clear"></div>        
    <?php if ($params->get('show_title') != 'hide') :?>
      <div class="hasTooltip title" title="<?php echo JHtml::tooltipText($item->title, JHtml::_('string.truncate', $item->description, 300, true, false)); ?>">
        <a class="popup-title-<?php echo $module->id; ?>" href="https://www.youtube.com/embed/<?php echo $item->id; ?>?wmode=opaque&amp;autohide=<?php echo $params->get('autohide',2); ?>&amp;border=<?php echo $params->get('border',0); ?>&amp;cc_load_policy=<?php echo $params->get('cc_load_policy',1); ?>&amp;color=<?php echo $params->get('color','red'); ?>&amp;color1=<?php echo $params->get('color1'); ?>&amp;color2=<?php echo $params->get('color2'); ?>&amp;controls=<?php echo $params->get('controls',1); ?>&amp;fs=<?php echo $params->get('fs',0); ?>&amp;hd=<?php echo $params->get('hd',0); ?>&amp;iv_load_policy=<?php echo $params->get('iv_load_policy',1); ?>&amp;modestbranding=<?php echo $params->get('modestbranding',1); ?>&amp;rel=<?php echo $params->get('rel',1); ?>&amp;theme=<?php echo $params->get('theme','dark'); ?>" id="mb<?php echo $id; ?>" title="<?php echo htmlspecialchars($item->title); ?>">
          <?php echo JHtml::_('string.truncate', $item->title, 50); ?> 
        </a>
      </div>
    <?php endif; ?>
    <div class="clear"></div>
    <?php if ($params->get('show_category', 1)):?><span class="small badge pull-right"><?php echo $item->category; ?></span><?php endif; ?>
    <?php if ($params->get('show_views', 1)):?><span class="small"><?php echo JText::sprintf('MOD_HWD_YOUTUBE_VIDEOBOX_X_VIEWS', number_format($item->views)); ?></span><?php endif; ?>
 </div>
 <?php endforeach; ?>
</div>
