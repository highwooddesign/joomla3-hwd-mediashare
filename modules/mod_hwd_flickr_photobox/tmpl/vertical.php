<?php
/**
 * @version    $Id: vertical.php 1233 2013-03-07 09:53:14Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Sam Cummings
 * @since      20-Dec-2012 11:01:24
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Define Joomla document object, and load the default assets
$doc = JFactory::getDocument();
$doc->addStylesheet($helper->get('url').'css/vertical.css');
?>
<div class="row mod_hwd_flickr_photobox_vertical">
<?php foreach ($items as $id => &$item) : ?>
  <!-- Clear the cols if their content doesn't match in height -->
  <?php if ($id % 1 == 0) :?><div class="clearfix visible-xs"></div><?php endif; ?>
  <?php if ($id % 1 == 0) :?><div class="clearfix visible-sm"></div><?php endif; ?>
  <?php if ($id % 1 == 0) :?><div class="clearfix visible-md visible-lg"></div><?php endif; ?>    
  <!-- Cell -->
  <div class="cell col-xs-12 col-sm-12 col-md-12">
    <!-- Thumbnail Image -->
    <div class="media-item hasTooltip pull-half-left" title="<?php echo JHtml::tooltipText($item->title, JHtmlString::truncate($item->description, 300, true, false)); ?>">
      <a class="popup-thumbnail-<?php echo $module->id; ?>" href="<?php echo $item->media; ?>" id="mb<?php echo $id; ?>" title="<?php echo htmlspecialchars($item->title); ?>">
         <img src="<?php echo $item->thumbnail; ?>" border="0" alt="" title="" class="media-thumb"  />
         <?php echo ($params->get('icon', 1) ? '<span class="media-link-span"></span></span>' : ''); ?>
      </a>
    </div>
    <div class="pull-half-right">
    <!-- Clears Item and Information -->
    <div class="clear"></div>        
    <?php if ($params->get('show_title') != 'hide') :?>
      <div class="hasTooltip title" title="<?php echo JHtml::tooltipText($item->title, JHtmlString::truncate($item->description, 300, true, false)); ?>">
        <a class="popup-title-<?php echo $module->id; ?>" href="<?php echo $item->media; ?>" id="mb<?php echo $id; ?>" title="<?php echo htmlspecialchars($item->title); ?>">
          <?php echo JHtmlString::truncate($item->title, 50); ?> 
        </a>
      </div>
    <?php endif; ?>
    <!-- Clears Item and Information -->
    <div class="clear"></div>
    <!-- Item Meta -->
    <?php if ($params->get('show_created') != 'hide'):?><span class="small"><?php echo JHtml::_('date.relative', $item->created); ?></span><?php endif; ?>
    </div>
 </div>
 <?php endforeach; ?>
</div>
