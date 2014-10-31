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

// Load additional <head> assets
$doc = JFactory::getDocument();
$doc->addStyleDeclaration('
#photos {
   -webkit-column-count: '.intval($helper->params->get('columns', 5)).';
   -moz-column-count:    '.intval($helper->params->get('columns', 5)).';
   column-count:         '.intval($helper->params->get('columns', 5)).';
}');
?>
<div class="mod_hwd_flickr_photobox_grid">
<section id="photos">
<?php foreach ($helper->items as $id => $item) : ?>
<div class="block_grid"> 
    <div class="media-item hasTooltip" title="<?php echo JHtml::tooltipText($item->title, JHtml::_('string.truncate', $item->description, 300, true, false)); ?>">
        <a class="popup-thumbnail-<?php echo $module->id; ?>" href="<?php echo $item->media; ?>" id="mb<?php echo $id; ?>" title="<?php echo htmlspecialchars($item->title); ?>">
           <img src="<?php echo $item->thumbnail; ?>" border="0" alt="" title="" class="media-thumb"  />
           <?php echo ($helper->params->get('icon', 1) ? '<span class="media-link-span"></span>' : ''); ?>
        </a>
    </div>
</div>
<?php endforeach; ?>
</section>
</div>
