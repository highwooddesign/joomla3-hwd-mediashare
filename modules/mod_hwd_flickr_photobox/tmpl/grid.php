<?php
/**
 * @package    HWD.MediaApps
 * @copyright  Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
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
<div class="mod_hwd_flickr_photobox_grid">
<section id="photos">
<?php foreach ($items as $id => &$item) : ?>
<div class="block_grid"> 
    <div class="media-item hasTooltip" title="<?php echo JHtml::tooltipText($item->title, JHtmlString::truncate($item->description, 300, true, false)); ?>">
      <a class="popup-thumbnail-<?php echo $module->id; ?>" href="<?php echo $item->media; ?>" id="mb<?php echo $id; ?>" title="<?php echo htmlspecialchars($item->title); ?>">
         <img src="<?php echo $item->thumbnail; ?>" border="0" alt="" title="" class="media-thumb"  />
         <?php echo ($params->get('icon', 1) ? '<span class="media-link-span"></span>' : ''); ?>
      </a>
    </div>
</div>
<?php endforeach; ?>
</section>
</div>