<?php
/**
 * @package     Joomla.site
 * @subpackage  Module.mod_media_images
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

$app = JFactory::getApplication();
$menu = $app->getMenu();
$user = JFactory::getUser();
$associate = isset($displayData->module) ? false : true;
$doc = JFactory::getDocument();

$doc->addStyleSheet(JURI::root() . 'modules/mod_media_images/slick/slick.css');
$doc->addScript(JURI::root() . 'modules/mod_media_images/slick/slick.min.js');
$doc->addScriptDeclaration("
jQuery(document).ready(function(){
    jQuery('#media-carousel-view-" . $module->id . "').slick({
        autoplay: false,
        arrows: false,
        dots: true,
        infinite: true,
        fade: true,
        cssEase: 'linear',
        onAfterChange: function(slider,i) {
          var slideHeight = jQuery(slider.\$slides[i]).height();
          jQuery(slider.\$slider).height(slideHeight);
        }
    }); 
});
");                           
?>
<div class="hwd-container">
  <div id="media-carousel-view-<?php echo $module->id; ?>" class="media-details-view">
    <?php if (empty($helper->items)): ?>
      <div class="alert alert-no-items">
        <?php echo JText::_('COM_HWDMS_NOTHING_TO_SHOW'); ?>
      </div>
    <?php else: ?>
      <?php foreach ($helper->items as $id => $item) :
      $rowcount = (((int)$id) % (int) $helper->columns) + 1;
      $item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;
      $canEdit = ($user->authorise('core.edit', 'com_hwdmediashare.media.'.$item->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.media.'.$item->id) && ($item->created_user_id == $user->id)));
      $canEditState = $user->authorise('core.edit.state', 'com_hwdmediashare.media.'.$item->id);
      $canDelete = ($user->authorise('core.delete', 'com_hwdmediashare.media.'.$item->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.media.'.$item->id) && ($item->created_user_id == $user->id)));
      ?>
      <div class="media-carousel-item">
        <?php if ($helper->params->get('list_link_thumbnails') == 1) :?><a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getMediaItemRoute($item->slug, array(), false)); ?>"><?php endif; ?>
          <img src="<?php echo JRoute::_(hwdMediaShareThumbnails::thumbnail($item)); ?>" border="0" alt="<?php echo $helper->escape($item->title); ?>" class="media-carousel-thumb<?php echo ($helper->params->get('list_tooltip_location') > '2' ? ' hasTooltip' : ''); ?>" title="<?php echo ($helper->params->get('list_tooltip_location') > '1' ? JHtml::tooltipText($item->title, ($helper->params->get('list_tooltip_contents') != '0' ? JHtml::_('string.truncate', $item->description, $helper->params->get('list_desc_truncate'), false, false) : '')) : $item->title); ?>" />
        <?php if ($helper->params->get('list_link_thumbnails') == 1) :?></a><?php endif; ?>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div> 
  <div class="clearfix"></div>  
  <?php if ($params->get('show_more_link') != 'hide') :?><p><a href="<?php echo ((intval($params->get('show_more_link')) > 0) ? JRoute::_($menu->getItem($params->get('show_more_link'))->link.'&Itemid='.$params->get('show_more_link')) : JRoute::_(hwdMediaShareHelperRoute::getMediaRoute())); ?>" class="btn"><?php echo JText::_($params->get('more_link_text', 'MOD_MEDIA_IMAGES_VIEW_MORE')); ?></a></p><?php endif; ?>  
</div>
