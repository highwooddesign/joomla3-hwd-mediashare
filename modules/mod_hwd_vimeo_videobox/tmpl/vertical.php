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
<div class="row mod_hwd_vimeo_videobox_vertical">
<?php foreach ($helper->items as $id => $item) : ?>
  <!-- Clear the columns if their content doesn't match in height -->
  <?php if ($id % 1 == 0) :?><div class="clearfix visible-xs"></div><?php endif; ?>
  <?php if ($id % 1 == 0) :?><div class="clearfix visible-sm"></div><?php endif; ?>
  <?php if ($id % 1 == 0) :?><div class="clearfix visible-md visible-lg"></div><?php endif; ?>    
  <div class="cell col-xs-12 col-sm-12 col-md-12">
    <div class="media-item hasTooltip pull-half-left" title="<?php echo JHtml::tooltipText($item->title, JHtmlString::truncate($item->description, 300, true, false)); ?>">
      <div class="media-aspect"></div>        
      <?php if ($item->duration > 0) :?>
      <div class="media-duration">
         <?php echo $helper->secondsToTime($item->duration); ?>
      </div>
      <?php endif; ?>
      <a class="popup-thumbnail-<?php echo $module->id; ?>" href="http://player.vimeo.com/video/<?php echo $item->id; ?>?color=<?php echo $params->get('color'); ?>&autoplay=<?php echo $params->get('autoplay'); ?>&title=<?php echo $params->get('title'); ?>" id="mb<?php echo $id; ?>" title="<?php echo htmlspecialchars($item->title); ?>">
         <img src="<?php echo $item->thumbnail; ?>" border="0" alt="" title="" class="media-thumb"  />
         <?php echo ($helper->params->get('icon', 1) ? '<span class="media-link-span"></span></span>' : ''); ?>
      </a>
    </div>
    <div class="pull-half-right">
    <div class="clear"></div>        
    <?php if ($helper->params->get('show_title') != 'hide') :?>
      <div class="hasTooltip title" title="<?php echo JHtml::tooltipText($item->title, JHtmlString::truncate($item->description, 300, true, false)); ?>">
        <a class="popup-title-<?php echo $module->id; ?>" href="http://player.vimeo.com/video/<?php echo $item->id; ?>?color=<?php echo $params->get('color'); ?>&autoplay=<?php echo $params->get('autoplay'); ?>&title=<?php echo $params->get('title'); ?>" id="mb<?php echo $id; ?>" title="<?php echo htmlspecialchars($item->title); ?>">
          <?php echo JHtmlString::truncate($item->title, 50); ?> 
        </a>
      </div>
    <?php endif; ?>
    <div class="clear"></div>
    <?php if ($helper->params->get('show_views') != 'hide'):?><span class="small badge"><?php echo JText::sprintf('MOD_HWD_VIMEO_VIDEOBOX_X_VIEWS', number_format(intval($item->views))); ?></span><?php endif; ?>
    <?php if ($helper->params->get('show_date') != 'hide'):?><span class="small"><?php echo JHtml::_('date.relative', $item->uploadDate); ?></span><?php endif; ?>
    </div>
 </div>
 <?php endforeach; ?>
</div>