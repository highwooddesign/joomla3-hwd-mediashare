<?php
/**
 * @version    SVN $Id: modal.php 1569 2013-06-13 10:18:58Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      17-Mar-2012 12:55:40
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.modal');
JHtml::_('behavior.framework', true);
?>             
<div id="hwd-container">
  <!-- Media Header -->
  <div class="media-header">
    <h2 class="media-title">
      <a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getMediaItemRoute($this->item->id)); ?>" target="_top">
        <?php echo $this->escape($this->item->title); ?>
      </a>
    </h2>
    <div class="clear"></div>
  </div>
  <div id="media-item-container" class="media-item-container">
    <!-- Item Media -->
    <div class="media-item-full" id="media-item" style="width:100%;">
    <?php echo hwdMediaShareMedia::get($this->item); ?>
    </div>
    <div class="clear"></div>
  </div>
  <div class="clear"></div>
</div>
<script type='text/javascript'>
parent.document.getElementById('sbox-content').getElement('iframe').setStyles({'width':'100%','height':'100%','overflow':'hidden'});
window.addEvent('load', function() {
    var size = $('hwd-container').getSize();
    var width = size.x + 40;
    var height = size.y + 50;
    parent.document.getElementById('sbox-content').getElement('iframe').setStyles({'width':'100%','height':'100%','overflow':'hidden'});
    window.parent.SqueezeBox.resize({x:width,y:height},false);
});
</script> 