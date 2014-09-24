<?php
/**
 * @package     Joomla.site
 * @subpackage  Component.hwdmediashare
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

JHtml::_('behavior.modal');
JHtml::_('behavior.framework', true);
JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');

// Initialise variables.
$share_url            = hwdMediaShareMedia::getPermalink($this->item->id);
$share_title          = JHtml::_('string.truncate', $this->item->title, 100, true, false);
$share_description    = JHtml::_('string.truncate', $this->item->description, 500, true, false);
$share_thumbnail      = JRoute::_(hwdMediaShareThumbnails::thumbnail($this->item));
$share_thumbnail      = strpos($share_thumbnail, 'http') === 0 ? $share_thumbnail : rtrim(JURI::base(), '/') . $share_thumbnail;
$share_permalink      = hwdMediaShareMedia::getPermalink($this->item->id);
$share_embedcode      = hwdMediaShareMedia::getEmbedCode($this->item->id);           
?>
<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm">
  <div id="hwd-container" class="hwd-modal <?php echo $this->pageclass_sfx;?>"> <a name="top" id="top"></a>
    <!-- Media Header -->
    <div class="media-header">
      <?php if ($this->params->get('item_meta_title') != '0') :?>
        <h2 class="media-modal-title"><?php echo $this->escape($this->params->get('page_heading')); ?></h2>
      <?php endif; ?> 
    </div>
    <div class="row-fluid share-panel">
      <div class="span6">
        <label><?php echo JText::_('COM_HWDMS_PERMALINK'); ?></label>
        <div class="row-fluid share-url">
          <i class="icon-link"></i>
          <input title="<?php echo JText::_('COM_HWDMS_PERMALINK'); ?>" value="<?php echo $share_permalink; ?>" name="share_url" class="share-url-input span12">
        </div>
      </div>    
      <div class="span6">
        <label><?php echo JText::_('COM_HWDMS_SOCIAL'); ?></label>
        <div class="social-icon pull-left">
          <a href="https://www.facebook.com/dialog/share?app_id=<?php echo $this->params->get('facebook_appid', '121857621243659'); ?>&display=popup&href=<?php echo urlencode($share_url); ?>&display=popup&redirect_uri=http://facebook.com/" onclick="javascript:window.open(this.href,'','menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=236,width=561');return false;">
            <img src="<?php echo JURI::root(true); ?>/media/com_hwdmediashare/assets/images/icons/128/facebook.png" alt="<?php echo JText::_('COM_HWDMS_FACEBOOK'); ?>"/>
          </a>
        </div>
        <div class="social-icon pull-left">
          <script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
          <a href="http://twitter.com/intent/tweet?url=<?php echo urlencode($share_url); ?>&text=<?php echo urlencode($share_title); ?><?php echo ($this->params->get('twitter_username') ? '&via=' . $this->params->get('twitter_username') : ''); ?>">
            <img src="<?php echo JURI::root(true); ?>/media/com_hwdmediashare/assets/images/icons/128/twitter.png" alt="<?php echo JText::_('COM_HWDMS_TWITTER'); ?>" border="0" />
          </a>
        </div>
        <div class="social-icon pull-left">
          <a href="https://plus.google.com/share?url=<?php echo urlencode($share_url); ?>" onclick="javascript:window.open(this.href,'','menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
            <img src="<?php echo JURI::root(true); ?>/media/com_hwdmediashare/assets/images/icons/128/googleplus.png" alt="<?php echo JText::_('COM_HWDMS_GOOGLEPLUS'); ?>"/>
          </a>
        </div>
        <div class="social-icon pull-left">
          <a href="//www.pinterest.com/pin/create/button/?url=<?php echo urlencode($share_url); ?>&description=<?php echo urlencode($share_description); ?>" data-pin-do="buttonPin" data-pin-config="above" onclick="javascript:window.open(this.href,'','menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=770');return false;">
            <img src="<?php echo JURI::root(true); ?>/media/com_hwdmediashare/assets/images/icons/128/pinterest.png" alt="<?php echo JText::_('COM_HWDMS_PINTEREST'); ?>"/>
          </a>
        </div>
      </div>  
    </div>
    <div class="row-fluid share-panel">
      <div class="span12">
        <label><?php echo JText::_('COM_HWDMS_EMBED'); ?></label>
        <div class="row-fluid share-embed">
          <i class="icon-embed"></i>
          <input title="<?php echo JText::_('COM_HWDMS_EMBED_CODE'); ?>" value="<?php echo $this->escape($share_embedcode); ?>" name="share_embed" class="share-embed-input span12">
        </div>
      </div>    
    </div>
    <div class="clearfix"></div>
  </div>
</form>