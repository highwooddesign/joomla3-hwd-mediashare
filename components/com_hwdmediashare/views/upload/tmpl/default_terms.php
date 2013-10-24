<?php
/**
 * @version    SVN $Id: default_terms.php 435 2012-07-16 15:16:13Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      29-Mar-2012 13:53:51
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.modal');
JLoader::register('ContentHelperRoute', JPATH_ROOT.'/components/com_content/helpers/route.php');

?>
<div class="edit">
<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm" class="form-validate" autocomplete="off">
<div id="hwd-container"> <a name="top" id="top" title="top"></a> <?php echo hwdMediaShareHelperNavigation::getInternalNavigation(); ?>
  <!-- Media Header -->
  <div class="media-header">
    <h2 class="media-title"><?php echo JText::_('COM_HWDMS_UPLOADS_TERMS_AND_CONDITIONS'); ?></h2>
    <div class="clear"></div>
  </div>
  <div id="media-item-container" class="media-item-container">
    <p><?php echo JText::_('COM_HWDMS_AGREE_TO_TERMS_AND_CONDITIONS'); ?></p>
    <fieldset>
      <div class="formelm">
        <label id="jform_password-lbl" for="jform_password" class="hasTip required" title="<?php echo JText::_('COM_HWDMS_PASSWORD_LABEL'); ?>::<?php echo JText::_('COM_HWDMS_PASSWORD_DESC'); ?>"><a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($this->params->get('upload_terms_id')).'&tmpl=component'); ?>" class="modal" rel="{handler: 'iframe', size: {<?php echo $this->utilities->modalSize('large'); ?>}}"><?php echo JText::_('COM_HWDMS_TERMS_AND_CONDITIONS'); ?></a><span class="star">&#160;*</span></label>
        <input type="checkbox" name="jform[terms]" id="jform_terms" value="" class="inputbox required" />
      </div>
      <div class="formelm-buttons">
        <button type="button" class="button" onclick="Joomla.submitbutton('addmedia.terms')"><?php echo JText::_('COM_HWDMS_AGREE'); ?></button>
      </div>
    </fieldset>
    <div class="clear"></div>
  </div>
  <div class="clear"></div>
<input type="hidden" name="task" value="" />
<input type="hidden" name="return" value="<?php echo $this->return;?>" />
<?php echo JHtml::_( 'form.token' ); ?></form>
</div>