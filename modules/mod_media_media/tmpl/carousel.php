<?php
/**
 * @package     Joomla.site
 * @subpackage  Module.mod_media_media
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

$app = JFactory::getApplication();
$menu = $app->getMenu();
?>
<div class="hwd-module">
  <div id="media-carousel-view" class="media-carousel-view">
    <?php if (empty($helper->items)): ?>
      <div class="alert alert-no-items">
        <?php echo JText::_('COM_HWDMS_NOTHING_TO_SHOW'); ?>
      </div>
    <?php else: ?>
      <?php echo JLayoutHelper::render('media_slick', $helper, JPATH_ROOT.'/components/com_hwdmediashare/libraries/layouts'); ?>
    <?php endif; ?>
  </div> 
  <?php if ($params->get('show_more_link') != 'hide') :?><p><a href="<?php echo ((intval($params->get('show_more_link')) > 0) ? JRoute::_($menu->getItem($params->get('show_more_link'))->link.'&Itemid='.$params->get('show_more_link')) : JRoute::_(hwdMediaShareHelperRoute::getMediaRoute())); ?>" class="btn"><?php echo JText::_($params->get('more_link_text', 'COM_HWDMS_VIEW_ALL')); ?></a></p><?php endif; ?>  
</div>

<script type="text/javascript">
jQuery.noConflict();
(function( $ ) {
  $(function() {
    $(document).ready(function() {
      $('.media-carousel-view').slick({
        autoplay: <?php echo $params->get('autoplay', 0) ? 'true' : 'false'; ?>,
        arrows: <?php echo $params->get('arrows', 1) ? 'true' : 'false'; ?>,
        dots: <?php echo $params->get('dots', 1) ? 'true' : 'false'; ?>,
        infinite: <?php echo $params->get('infinite', 1) ? 'true' : 'false'; ?>,
        speed: <?php echo $params->get('speed', 300) ? 'true' : 'false'; ?>,
        slidesToShow: <?php echo $params->get('slidesToShow', 4); ?>,
        slidesToScroll: <?php echo $params->get('slidesToScroll', 1); ?>,
          responsive: [
          {
            breakpoint: 768,
            settings: {
              arrows: false,
              slidesToShow: 2
            }
          },
          {
            breakpoint: 480,
            settings: {
              arrows: false,
              slidesToShow: 1
            }
          }
        ]
      });                                                
    });
  });
})(jQuery);
</script>
