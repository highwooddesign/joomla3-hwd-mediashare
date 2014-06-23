<?php
/**
 * @package     Joomla.site
 * @subpackage  Module.mod_hwd_cnn_videobox
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

// Define Joomla document object, and load the template assets.
$doc = JFactory::getDocument();
$doc->addStylesheet($helper->get('url').'css/default.css');
?>
<div class="row mod_hwd_dailymotion_videobox_default">
<?php foreach ($helper->items as $id => $item) : ?>
  <!-- Clear the columns if their content doesn't match in height -->
  <?php if ($id % 2 == 0) :?><div class="clearfix visible-xs"></div><?php endif; ?>
  <?php if ($id % 3 == 0) :?><div class="clearfix visible-sm"></div><?php endif; ?>
  <?php if ($id % $helper->params->get('columns', 3) == 0) :?><div class="clearfix visible-md visible-lg"></div><?php endif; ?>    
  <div class="cell col-xs-6 col-sm-4 col-md-<?php echo intval(12/$helper->params->get('columns', 3)); ?>">
    <div class="media-item hasTooltip" title="<?php echo JHtml::tooltipText($item->title, JHtmlString::truncate($item->description, 300, true, false)); ?>">
      <div class="media-aspect"></div>        
      <?php if ($item->duration > 0) :?>
      <div class="media-duration">
         <?php echo $item->duration; ?>
      </div>
      <?php endif; ?>
      <a class="popup-thumbnail-<?php echo $module->id; ?>" href="https://www.dailymotion.com/embed/video/<?php echo $item->id; ?>?autoPlay=<?php echo $params->get('autoplay'); ?><?php echo ($params->get('color') ? '&background='.$params->get('color') : ''); ?><?php echo ($params->get('color1') ? '&foreground='.$params->get('color1') : ''); ?><?php echo ($params->get('color2') ? '&highlight='.$params->get('color2') : ''); ?>" id="mb<?php echo $id; ?>" title="<?php echo htmlspecialchars($item->title); ?>">
         <img src="<?php echo $item->thumbnail; ?>" border="0" alt="" title="" class="media-thumb"  />
         <?php echo ($helper->params->get('icon', 1) ? '<span class="media-link-span"></span></span>' : ''); ?>
      </a>
    </div>
    <div class="clear"></div>        
    <?php if ($helper->params->get('show_title') != 'hide') :?>
      <div class="hasTooltip title" title="<?php echo JHtml::tooltipText($item->title, JHtmlString::truncate($item->description, 300, true, false)); ?>">
        <a class="popup-title-<?php echo $module->id; ?>" href="https://www.dailymotion.com/embed/video/<?php echo $item->id; ?>?autoPlay=<?php echo $params->get('autoplay'); ?><?php echo ($params->get('color') ? '&background='.$params->get('color') : ''); ?><?php echo ($params->get('color1') ? '&foreground='.$params->get('color1') : ''); ?><?php echo ($params->get('color2') ? '&highlight='.$params->get('color2') : ''); ?>" id="mb<?php echo $id; ?>" title="<?php echo htmlspecialchars($item->title); ?>">
          <?php echo JHtmlString::truncate($item->title, 50); ?> 
        </a>
      </div>
    <?php endif; ?>
    <div class="clear"></div>
    <?php if ($params->get('show_views') != 'hide'):?><span class="small"><?php echo JText::sprintf('MOD_HWD_DAILYMOTION_VIDEOBOX_X_VIEWS', number_format($item->views)); ?></span><?php endif; ?>
  </div>
 <?php endforeach; ?>
</div>
