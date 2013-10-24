<?php
/**
 * @version    SVN $Id: default_share.php 1341 2013-03-20 10:59:09Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      18-Nov-2011 10:01:46
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.modal');
JHtml::_('behavior.framework', true);

// Stuff we need for sharing
$url		= urlencode(hwdMediaShareMedia::getPermalink(JRequest::getInt('id')));
$hashtags       = "hwdMediaShare";  // Comma separated list of hashtags (without #) to automatically be inserted into the tweet
$text           = trim(urlencode($this->item->title));
$description    = trim(urlencode($this->item->description));
$thumbnail      = JRoute::_(hwdMediaShareDownloads::thumbnail($this->item));
$thumbnail      = strpos($thumbnail,'http') === 0 ? $thumbnail : rtrim(JURI::base(), '/') . $thumbnail;
?>

<div class="edit">   
<!-- Facebook -->
<script type="text/javascript" src='http://connect.facebook.net/en_US/all.js'></script>
<a class="social-icon" href="https://www.facebook.com/dialog/feed?app_id=121857621243659&link=<?php echo $url; ?>&picture=<?php echo $thumbnail ?>&name=<?php echo $text; ?>&caption=<?php echo $description; ?>&display=popup&redirect_uri=http://facebook.com/" onclick="javascript:window.open(this.href,'','menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=236,width=516');return false;">
    <img src="<?php echo JURI::root(true); ?>/media/com_hwdmediashare/assets/images/icons/32/social-icon-facebook.png" alt="Facebook This"/>
</a>
<!-- Twitter -->
<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
<a class="social-icon" href="http://twitter.com/intent/tweet?text=<?php echo $text; ?>&url=<?php echo $url; ?>&via=hwdMediaShare">
    <img src="<?php echo JURI::root(true); ?>/media/com_hwdmediashare/assets/images/icons/32/social-icon-twitter.png" alt="Tweet This" border="0" />
</a>
<!-- GooglePlus -->
<a class="social-icon" href="https://plus.google.com/share?url=<?php echo $url; ?>" onclick="javascript:window.open(this.href,'','menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
    <img src="<?php echo JURI::root(true); ?>/media/com_hwdmediashare/assets/images/icons/32/social-icon-googleplus.png" alt="Share on Google+"/>
</a>
<!-- Pinterest -->
<a class="social-icon" href="http://pinterest.com/pin/create/button/?url=<?php echo $url; ?>&media=<?php echo $thumbnail; ?>&description=<?php echo $description; ?>" onclick="javascript:window.open(this.href,'','menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
  <img src="<?php echo JURI::root(true); ?>/media/com_hwdmediashare/assets/images/icons/32/social-icon-pinterest.png" alt="Pinterest"/>
</a>    
<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
    <fieldset>
      <legend><?php echo JText::_('COM_HWDMS_PERMALINK'); ?></legend>
      <div class="formelm">
        <?php echo $this->form->getInput('permalink'); ?>
      </div>
    </fieldset>
    <fieldset>
      <legend><?php echo JText::_('COM_HWDMS_EMBED_CODE'); ?></legend>
      <div class="formelm">
        <?php echo $this->form->getInput('embed_code'); ?>
      </div>
    </fieldset>
    </fieldset>
  </form>
</div>
