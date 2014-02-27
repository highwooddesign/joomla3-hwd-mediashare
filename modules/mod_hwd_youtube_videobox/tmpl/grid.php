<?php
/**
 * @version    SVN $Id: default.php 1537 2013-05-30 10:51:51Z dhorsfall $
 * @package    hwdYourTube
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      09-Nov-2011 14:02:20
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Load additional <head> assets
$doc = JFactory::getDocument();
$doc->addStylesheet($helper->get('url').'css/grid.css');
$doc->addStyleDeclaration('
#photos {
   -webkit-column-count: '.intval($params->get('columns', 5)).';
   -moz-column-count:    '.intval($params->get('columns', 5)).';
   column-count:         '.intval($params->get('columns', 5)).';
}');
?>
<div class="mod_hwd_youtube_videobox_grid">
<section id="photos">
<?php foreach ($items as $id => &$item) : ?>
<div class="block_grid"> 
    <div class="media-item hasTooltip" title="<?php echo JHtml::tooltipText($item->title, JHtmlString::truncate($item->description, 300, true, false)); ?>">
        <?php if ($item->duration > 0) :?>
        <div class="media-duration">
           <?php echo $helper->secondsToTime($item->duration); ?>
        </div>
        <?php endif; ?>
        <a class="popup-thumbnail-<?php echo $module->id; ?>" href="https://www.youtube.com/embed/<?php echo $item->id; ?>?wmode=opaque&amp;autohide=<?php echo $params->get('autohide',2); ?>&amp;border=<?php echo $params->get('border',0); ?>&amp;cc_load_policy=<?php echo $params->get('cc_load_policy',1); ?>&amp;color=<?php echo $params->get('color','red'); ?>&amp;color1=<?php echo $params->get('color1'); ?>&amp;color2=<?php echo $params->get('color2'); ?>&amp;controls=<?php echo $params->get('controls',1); ?>&amp;fs=<?php echo $params->get('fs',0); ?>&amp;hd=<?php echo $params->get('hd',0); ?>&amp;iv_load_policy=<?php echo $params->get('iv_load_policy',1); ?>&amp;modestbranding=<?php echo $params->get('modestbranding',1); ?>&amp;rel=<?php echo $params->get('rel',1); ?>&amp;theme=<?php echo $params->get('theme','dark'); ?>" id="mb<?php echo $id; ?>" title="<?php echo htmlspecialchars($item->title); ?>">
           <img src="<?php echo $item->thumbnail; ?>" border="0" alt="" title="" class="media-thumb"  />
           <?php echo ($params->get('icon', 1) ? '<span class="media-link-span"></span>' : ''); ?>
        </a>
    </div>
</div>
<?php endforeach; ?>
</section>
</div>
