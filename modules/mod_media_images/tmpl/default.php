<?php
/**
 * @version    $Id: default.php 1380 2013-04-23 12:51:10Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$application = JFactory::getApplication();
$menu = $application->getMenu();
?>
<div style="clear:both;"></div>
<div class="slide">
 <div id="slide">
    <?php foreach($items as $item) : ?>
    <?php $item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id; ?>
      <div class="slide-container">
        <a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getMediaItemRoute($item->slug)); ?>"><?php echo hwdMediaShareMedia::get($item); ?></a>
      </div>
    <?php endforeach; ?>
  </div>
  <div class="slider-tabs">
    <a href="#page-p" class="slide-prev">&laquo;</a>
      <?php $counter = 0; ?>
      <?php foreach($items as $item) : ?>
        <a href="#<?php $counter; ?>"><?php echo $counter+1; ?></a>
        <?php $counter++; ?>
      <?php endforeach; ?>
    <a href="#page-p" class="slide-next">&raquo;</a>
  </div>
</div>
<div style="clear:both;"></div>
<?php if ($params->get('show_more_link') != 'hide') :?><p><a href="<?php echo ((intval($params->get('show_more_link')) > 0) ? JRoute::_($menu->getItem($params->get('show_more_link'))->link.'&Itemid='.$params->get('show_more_link')) : JRoute::_(hwdMediaShareHelperRoute::getMediaRoute(array('filter_mediaType'=>3)))); ?>"><?php echo JText::_($params->get('more_link_text', 'COM_HWDMS_VIEW_ALL')); ?></a></p><?php endif; ?>  
<div style="clear:both;"></div>
