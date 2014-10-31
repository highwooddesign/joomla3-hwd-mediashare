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

// Load additional <head> assets
$doc = JFactory::getDocument();
$doc->addStyleDeclaration('
#photos {
   -webkit-column-count: '.intval($helper->params->get('columns', 5)).';
   -moz-column-count:    '.intval($helper->params->get('columns', 5)).';
   column-count:         '.intval($helper->params->get('columns', 5)).';
}');
?>
<div class="mod_hwd_youtube_videobox_grid">
<section id="photos">
<?php foreach ($helper->items as $id => $item) : ?>
<div class="block_grid"> 
    <div class="media-item hasTooltip" title="<?php echo JHtml::tooltipText($item->title, JHtml::_('string.truncate', $item->description, 300, true, false)); ?>">
        <?php if ($item->duration > 0 && $helper->params->get('show_duration', 1)) :?>
        <div class="media-duration">
           <?php echo $helper->secondsToTime($item->duration); ?>
        </div>
        <?php endif; ?>
        <a class="popup-thumbnail-<?php echo $module->id; ?>" href="https://www.youtube.com/embed/<?php echo $item->id; ?>?wmode=opaque&amp;autohide=<?php echo $helper->params->get('autohide',2); ?>&amp;border=<?php echo $helper->params->get('border',0); ?>&amp;cc_load_policy=<?php echo $helper->params->get('cc_load_policy',1); ?>&amp;color=<?php echo $helper->params->get('color','red'); ?>&amp;color1=<?php echo $helper->params->get('color1'); ?>&amp;color2=<?php echo $helper->params->get('color2'); ?>&amp;controls=<?php echo $helper->params->get('controls',1); ?>&amp;fs=<?php echo $helper->params->get('fs',0); ?>&amp;hd=<?php echo $helper->params->get('hd',0); ?>&amp;iv_load_policy=<?php echo $helper->params->get('iv_load_policy',1); ?>&amp;modestbranding=<?php echo $helper->params->get('modestbranding',1); ?>&amp;rel=<?php echo $helper->params->get('rel',1); ?>&amp;theme=<?php echo $helper->params->get('theme','dark'); ?>" id="mb<?php echo $id; ?>" title="<?php echo htmlspecialchars($item->title); ?>">
           <img src="<?php echo $item->thumbnail; ?>" border="0" alt="" title="" class="media-thumb"  />
           <?php echo ($helper->params->get('icon', 1) ? '<span class="media-link-span"></span>' : ''); ?>
        </a>
    </div>
</div>
<?php endforeach; ?>
</section>
</div>
