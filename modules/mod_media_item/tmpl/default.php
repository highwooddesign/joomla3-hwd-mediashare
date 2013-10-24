<?php
/**
 * @version    $Id: default.php 506 2012-09-07 12:20:09Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.modal');
JHtml::_('behavior.framework', true);
JHtml::_('behavior.tooltip');

?>
<!-- Module Container -->
<div class="hwd-module">
  <div class="media-header">
    <?php if ($params->get('list_meta_title') != 'hide') :?>
      <h<?php echo $params->get('list_item_heading', 3); ?> class="contentheading">
        <?php if ($params->get('list_link_titles') != '0') :?><a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getMediaItemRoute($item->id)); ?>"><?php endif; ?>
            <?php echo JHtmlString::truncate($helper->get('utilities')->escape($item->title), $params->get('list_title_truncate')); ?> 
        <?php if ($params->get('list_link_titles') != '0') :?></a><?php endif; ?>
      </h<?php echo $params->get('list_item_heading', 3); ?>>
    <?php endif; ?>
    <div class="clear"></div>
  </div>
  <div id="media-item-container" class="media-item-container">
    <!-- Item Media -->
    <div class="media-item-full" id="media-item" style="width:100%;">
    <?php echo hwdMediaShareMedia::get($item); ?>
    </div>
    <div class="clear"></div>
  <!-- Clears Top Link -->
  </div>
  <?php if ($params->get('show_more_link') != 'hide') : ?><p><a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getMediaRoute()); ?>"><?php echo JText::_($params->get('more_link_text', 'COM_HWDMS_VIEW_ALL')); ?></a></p><?php endif; ?>
</div>
