<?php
/**
 * @package    HWD.MediaApps
 * @copyright  Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Define Joomla document object, and load the default assets
$doc = JFactory::getDocument();
$doc->addStylesheet($helper->get('url').'css/default.css');
?>
<div class="row mod_hwd_youtube_videobox_default">
<?php foreach ($items as $id => &$item) : ?>
  <!-- Clear the cols if their content doesn't match in height -->
  <?php if ($id % 2 == 0) :?><div class="clearfix visible-xs"></div><?php endif; ?>
  <?php if ($id % 3 == 0) :?><div class="clearfix visible-sm"></div><?php endif; ?>
  <?php if ($id % $params->get('columns', 5) == 0) :?><div class="clearfix visible-md visible-lg"></div><?php endif; ?>    
  <!-- Cell -->
  <div class="cell col-xs-6 col-sm-4 col-md-<?php echo intval(12/$params->get('columns', 5)); ?>">
    <!-- Thumbnail Image -->
    <div class="media-item hasTooltip" title="<?php echo JHtml::tooltipText($item->title, JHtmlString::truncate($item->description, 300, true, false)); ?>">
      <div class="media-aspect"></div>        
      <?php if ($item->duration > 0) :?>
      <div class="media-duration">
         <?php echo $helper->secondsToTime($item->duration); ?>
      </div>
      <?php endif; ?>
      <a class="popup-thumbnail-<?php echo $module->id; ?>" href="https://www.youtube.com/embed/<?php echo $item->id; ?>?wmode=opaque&amp;autohide=<?php echo $params->get('autohide',2); ?>&amp;border=<?php echo $params->get('border',0); ?>&amp;cc_load_policy=<?php echo $params->get('cc_load_policy',1); ?>&amp;color=<?php echo $params->get('color','red'); ?>&amp;color1=<?php echo $params->get('color1'); ?>&amp;color2=<?php echo $params->get('color2'); ?>&amp;controls=<?php echo $params->get('controls',1); ?>&amp;fs=<?php echo $params->get('fs',0); ?>&amp;hd=<?php echo $params->get('hd',0); ?>&amp;iv_load_policy=<?php echo $params->get('iv_load_policy',1); ?>&amp;modestbranding=<?php echo $params->get('modestbranding',1); ?>&amp;rel=<?php echo $params->get('rel',1); ?>&amp;theme=<?php echo $params->get('theme','dark'); ?>" id="mb<?php echo $id; ?>" title="<?php echo htmlspecialchars($item->title); ?>">
         <img src="<?php echo $item->thumbnail; ?>" border="0" alt="" title="" class="media-thumb"  />
         <?php echo ($params->get('icon', 1) ? '<span class="media-link-span"></span></span>' : ''); ?>
      </a>
    </div>
    <!-- Clears Item and Information -->
    <div class="clear"></div>        
    <?php if ($params->get('show_title') != 'hide') :?>
      <div class="hasTooltip title" title="<?php echo JHtml::tooltipText($item->title, JHtmlString::truncate($item->description, 300, true, false)); ?>">
        <a class="popup-title-<?php echo $module->id; ?>" href="https://www.youtube.com/embed/<?php echo $item->id; ?>?wmode=opaque&amp;autohide=<?php echo $params->get('autohide',2); ?>&amp;border=<?php echo $params->get('border',0); ?>&amp;cc_load_policy=<?php echo $params->get('cc_load_policy',1); ?>&amp;color=<?php echo $params->get('color','red'); ?>&amp;color1=<?php echo $params->get('color1'); ?>&amp;color2=<?php echo $params->get('color2'); ?>&amp;controls=<?php echo $params->get('controls',1); ?>&amp;fs=<?php echo $params->get('fs',0); ?>&amp;hd=<?php echo $params->get('hd',0); ?>&amp;iv_load_policy=<?php echo $params->get('iv_load_policy',1); ?>&amp;modestbranding=<?php echo $params->get('modestbranding',1); ?>&amp;rel=<?php echo $params->get('rel',1); ?>&amp;theme=<?php echo $params->get('theme','dark'); ?>" id="mb<?php echo $id; ?>" title="<?php echo htmlspecialchars($item->title); ?>">
          <?php echo JHtmlString::truncate($item->title, 50); ?> 
        </a>
      </div>
    <?php endif; ?>
    <!-- Clears Item and Information -->
    <div class="clear"></div>
    <!-- Item Meta -->
    <?php if ($params->get('show_category') != 'hide'):?><span class="small badge pull-right"><?php echo $item->category; ?></span><?php endif; ?>
    <?php if ($params->get('show_views') != 'hide'):?><span class="small"><?php echo JText::sprintf('MOD_HWD_YOUTUBE_VIDEOBOX_X_VIEWS', number_format($item->views)); ?></span><?php endif; ?>
 </div>
 <?php endforeach; ?>
</div>