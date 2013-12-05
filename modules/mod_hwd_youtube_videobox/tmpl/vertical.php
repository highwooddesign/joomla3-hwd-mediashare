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

// Load JHtmlString
JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');

// Load Mootools
JHtml::_('behavior.framework', true);
JHtml::_('behavior.tooltip', '.hasHwdTip', array('className'=>'hwdTip'));

// Define Joomla document object
$doc = JFactory::getDocument();
// Add multiBox assets to header
$doc->addStylesheet($helper->get('url').'css/magnific-popup.css');
$doc->addScript($helper->get('url').'js/jquery.magnific-popup.js');
$doc->addScriptDeclaration("
jQuery.noConflict();
(function( $ ) {
  $(function() {
    // More code using $ as alias to jQuery
    $(document).ready(function() {
      $('.popup-title-" . $module->id . "').magnificPopup({ 
        type: 'iframe',
        iframe: {     
          patterns: {
            youtube: {
              id: function(url) {        
                return url;
              },
              src: '%id%'
            }
          }
        },
        gallery: {
          enabled: true
        }
      }); 
      $('.popup-thumbnail-" . $module->id . "').magnificPopup({ 
        type: 'iframe',
        iframe: {
          patterns: {
            youtube: {
              id: function(url) {        
                return url;
              },
              src: '%id%'
            }
          }
        },
        gallery: {
          enabled: true
        }
      });       
    });
  });
})(jQuery);
");

// Load and define assets for default template
$doc->addStylesheet($helper->get('url').'css/strapped.hwd.css');
$doc->addStylesheet($helper->get('url').'css/vertical.css');
?>
<div class="row mod_hwd_youtube_videobox-responsive">
<?php foreach ($items as $id => &$item) : ?>
  <!-- Clear the cols if their content doesn't match in height -->
  <?php if ($id % 1 == 0) :?><div class="clearfix visible-xs"></div><?php endif; ?>
  <?php if ($id % 2 == 0) :?><div class="clearfix visible-sm"></div><?php endif; ?>
  <?php if ($id % 2 == 0) :?><div class="clearfix visible-md visible-lg"></div><?php endif; ?>    
  <!-- Cell -->
  <div class="cell col-xs-12 col-sm-6 col-md-6">
    <!-- Thumbnail Image -->
    <div class="media-item hasHwdTip pull-half-left" title="<?php echo htmlspecialchars($item->title); ?>::<?php echo htmlspecialchars(JHtmlString::truncate($item->description, 300, true, false)); ?>">
      <div class="media-aspect169"></div>        
      <!-- Media Type -->
      <?php if ($item->duration > 0) :?>
      <div class="media-duration">
         <?php echo $helper->secondsToTime($item->duration); ?>
      </div>
      <?php endif; ?>
      <a class="popup-thumbnail-<?php echo $module->id; ?>" href="https://www.youtube.com/embed/<?php echo $item->id; ?>?wmode=opaque&amp;autohide=<?php echo $params->get('autohide',2); ?>&amp;border=<?php echo $params->get('border',0); ?>&amp;cc_load_policy=<?php echo $params->get('cc_load_policy',1); ?>&amp;color=<?php echo $params->get('color','red'); ?>&amp;color1=<?php echo $params->get('color1'); ?>&amp;color2=<?php echo $params->get('color2'); ?>&amp;controls=<?php echo $params->get('controls',1); ?>&amp;fs=<?php echo $params->get('fs',0); ?>&amp;hd=<?php echo $params->get('hd',0); ?>&amp;iv_load_policy=<?php echo $params->get('iv_load_policy',1); ?>&amp;modestbranding=<?php echo $params->get('modestbranding',1); ?>&amp;rel=<?php echo $params->get('rel',1); ?>&amp;theme=<?php echo $params->get('theme','dark'); ?>" id="mb<?php echo $id; ?>" title="<?php echo htmlspecialchars($item->title); ?>">
         <img src="<?php echo $item->thumbnail; ?>" border="0" alt="" title="" class="media-thumb"  />
         <span class="media-link-span"></span>
      </a>
    </div>
    <div class="pull-half-right">
    <!-- Clears Item and Information -->   
    <?php if ($params->get('videos_show_videos_title') != 'hide') :?>
      <h3 class="hasHwdTip title" title="<?php echo htmlspecialchars($item->title); ?>::<?php echo htmlspecialchars(JHtmlString::truncate($item->description, 300, true, false)); ?>">
        <a class="popup-title-<?php echo $module->id; ?>" href="https://www.youtube.com/embed/<?php echo $item->id; ?>?wmode=opaque&amp;autohide=<?php echo $params->get('autohide',2); ?>&amp;border=<?php echo $params->get('border',0); ?>&amp;cc_load_policy=<?php echo $params->get('cc_load_policy',1); ?>&amp;color=<?php echo $params->get('color','red'); ?>&amp;color1=<?php echo $params->get('color1'); ?>&amp;color2=<?php echo $params->get('color2'); ?>&amp;controls=<?php echo $params->get('controls',1); ?>&amp;fs=<?php echo $params->get('fs',0); ?>&amp;hd=<?php echo $params->get('hd',0); ?>&amp;iv_load_policy=<?php echo $params->get('iv_load_policy',1); ?>&amp;modestbranding=<?php echo $params->get('modestbranding',1); ?>&amp;rel=<?php echo $params->get('rel',1); ?>&amp;theme=<?php echo $params->get('theme','dark'); ?>" id="mb<?php echo $id; ?>" title="<?php echo htmlspecialchars($item->title); ?>" rel="[youtube-l],width:<?php echo $params->get('video_size',600); ?>,height:<?php echo intval($params->get('video_size',600)*0.6666); ?>">
          <?php echo JHtmlString::truncate($item->title, 50); ?> 
        </a>
      </h3>
    <?php endif; ?>
    <!-- Clears Item and Information -->
    <div class="clear"></div>
    <!-- Item Meta -->
    <?php if ($params->get('videos_show_videos_category') != 'hide'):?><span class="small badge"><?php echo $item->category; ?></span><?php endif; ?>
    <?php if ($params->get('videos_show_videos_views') != 'hide'):?><span class="small"><?php echo JText::sprintf('MOD_HWD_YOUTUBE_VIDEOBOX_X_VIEWS', number_format($item->views)); ?></span><?php endif; ?>
    </div>
 </div>
 <?php endforeach; ?>
</div>