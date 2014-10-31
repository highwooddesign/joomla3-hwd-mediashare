<?php
/**
 * @package     Joomla.site
 * @subpackage  Module.mod_hwd_flickr_photobox
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;
?>
<div class="row mod_hwd_flickr_photobox_vertical">
<?php foreach ($helper->items as $id => $item) : ?>
  <!-- Clear the columns if their content doesn't match in height -->
  <div class="clearfix visible-xs"></div>
  <div class="clearfix visible-sm"></div>
  <div class="clearfix visible-md visible-lg"></div>
  <div class="cell col-xs-12 col-sm-12 col-md-12">
    <div class="media-item hasTooltip" title="<?php echo JHtml::tooltipText($item->title, JHtml::_('string.truncate', $item->description, 300, true, false)); ?>">
      <a class="popup-thumbnail-<?php echo $module->id; ?>" href="<?php echo $item->media; ?>" id="mb<?php echo $id; ?>" title="<?php echo htmlspecialchars($item->title); ?>">
         <img src="<?php echo $item->thumbnail; ?>" border="0" alt="" title="" class="media-thumb"  />
         <?php echo ($params->get('icon', 1) ? '<span class="media-link-span"></span></span>' : ''); ?>
      </a>
    </div>
    <div class="clear"></div>        
    <?php if ($params->get('show_title') != 'hide') :?>
      <div class="hasTooltip title" title="<?php echo JHtml::tooltipText($item->title, JHtml::_('string.truncate', $item->description, 300, true, false)); ?>">
        <a class="popup-title-<?php echo $module->id; ?>" href="<?php echo $item->media; ?>" id="mb<?php echo $id; ?>" title="<?php echo htmlspecialchars($item->title); ?>">
          <?php echo JHtml::_('string.truncate', $item->title, 50); ?> 
        </a>
      </div>
    <?php endif; ?>
    <div class="clear"></div>
    <?php if ($params->get('show_created') != 'hide'):?><span class="small"><?php echo JHtml::_('date.relative', $item->created); ?></span><?php endif; ?>
 </div>
 <?php endforeach; ?>
</div>
