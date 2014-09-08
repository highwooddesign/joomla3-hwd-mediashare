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

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/html');
JHtml::_('HwdPopup.iframe', 'page');

JLoader::register('ContentHelperRoute', JPATH_ROOT.'/components/com_content/helpers/route.php');
?>
<div class="edit">
<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">
  <div id="hwd-container" class="<?php echo $this->pageclass_sfx;?>"> <a name="top" id="top"></a>
    <!-- Media Navigation -->
    <?php echo hwdMediaShareHelperNavigation::getInternalNavigation(); ?>
    <!-- Media Header -->
    <div class="media-header">
      <h2 class="media-upload-title"><?php echo JText::_('COM_HWDMS_UPLOAD_TERMS_AND_CONDITIONS'); ?></h2>
    </div>
    <div class="clear"></div>
    <p><?php echo JText::_('COM_HWDMS_ACCEPT_TERMS_AND_CONDITIONS_STATEMENT'); ?></p>
    <p><a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($this->params->get('upload_terms_id')).'&tmpl=component'); ?>" class="media-popup-iframe-page"><?php echo JText::_('COM_HWDMS_TERMS_AND_CONDITIONS'); ?></a></p>
    <div class="btn-toolbar">
      <div class="btn-group">
        <button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('addmedia.accepttos')">
          <span class="icon-ok"></span>&#160;<?php echo JText::_('COM_HWDMS_BUTTON_ACCEPT') ?>
        </button>
      </div>
      <div class="btn-group">
        <button type="button" class="btn" onclick="Joomla.submitbutton('addmedia.cancel')">
          <span class="icon-cancel"></span>&#160;<?php echo JText::_('JCANCEL') ?>
        </button>
      </div>
    </div>      
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="return" value="<?php echo $this->return;?>" />
    <?php echo JHtml::_( 'form.token' ); ?>
  </div>
</form>
</div>
