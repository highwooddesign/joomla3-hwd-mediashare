<?php
/**
 * @version    SVN $Id: default.php 1632 2013-08-15 09:51:56Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      01-Dec-2011 09:58:16
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// load tooltip behavior
JHtml::_('behavior.tooltip');
JHtml::_('behavior.framework');

$user = & JFactory::getUser();
$maxUpload = (int)$this->params->get('max_upload_filesize');
$maxPhpUpload = min((int)ini_get('post_max_size'),(int)ini_get('upload_max_filesize'),(int)$maxUpload);
?>   
<form action="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=addmedia.upload'); ?>" method="post" target="_top" name="adminForm" id="adminForm" class="formelm form-validate" enctype="multipart/form-data">
  <div id="hwd-container"> <a name="top" id="top"></a>     
    <?php echo $this->getAssociated(); ?>
    <!-- Media Navigation -->
    <?php echo hwdMediaShareHelperNavigation::getInternalNavigation(); ?>
    <!-- Media Header -->
      <?php echo JHtml::_('sliders.start', 'media-upload-slider'); ?>
      <?php if (JFactory::getUser()->authorise('hwdmediashare.upload','com_hwdmediashare') && $this->params->get('enable_uploads_file') == "1") : ?>
        <?php if (is_array($this->standardExtensions) && count($this->standardExtensions) > 0) : ?>
        <?php echo JHtml::_('sliders.panel', JText::sprintf( 'COM_HWDMS_UPLOAD_TYPES_LESS_THAN_N_MB', $this->getReadableAllowedMediaTypes('standard'), $maxPhpUpload ), 'publishing');?>
        <fieldset class="adminform">
          <div class="formelm">
            <label><?php echo JText::_('COM_HWDMS_SUPPORTED_FORMATS_LABEL'); ?></label>
            <span><?php echo $this->getReadableAllowedExtensions($this->standardExtensions); ?></span>
          </div>
        </fieldset>
        <fieldset class="adminform" id="hwd-upload-fallback">
          <div class="formelm">
            <label for="hwd-upload-photoupload">
              <?php echo JText::_('COM_HWDMS_UPLOAD_A_FILE') ?>
            </label>
            <input type="file" name="Filedata" />
            <input type="hidden" name="fallback" value="true11" />
          </div>
          <div class="formelm-buttons">
            <button type="button" onclick="Joomla.submitbutton('addmedia.upload')">
              <?php echo JText::_('COM_HWDMS_UPLOAD') ?>
            </button>
          </div>
        </fieldset>
        <?php if ($this->params->get('upload_tool_fancy') == 1) : ?>
        <div id="hwd-upload-status" class="hide">
          <p>
            <a href="#" id="hwd-upload-browse" class="button"><?php echo JText::_('COM_HWDMS_BROWSE_FILES'); ?></a>
            <a href="#" id="hwd-upload-clear" class="button"><?php echo JText::_('COM_HWDMS_CLEAR_LIST'); ?></a>
            <a href="#" id="hwd-upload-upload" class="button"><?php echo JText::_('COM_HWDMS_START_UPLOAD'); ?></a>
          </p>
          <div class="clear"></div>
          <div>
            <span class="overall-title"></span>
            <img src="<?php echo JURI::root(true); ?>/media/com_hwdmediashare/assets/images/ajaxupload/progress-bar/bar.gif" class="progress overall-progress" />
          </div>
          <div class="clear"></div>
          <div>
            <span class="current-title"></span>
            <img src="<?php echo JURI::root(true); ?>/media/com_hwdmediashare/assets/images/ajaxupload/progress-bar/bar.gif" class="progress current-progress" />
          </div>
          <div class="current-text"></div>
        </div>
        <ul id="hwd-upload-list"></ul>
        <?php endif; ?>
      <?php endif; ?>
      <?php if ($this->params->get('upload_tool_perl') == 1) : ?>
      <?php echo JHtml::_('sliders.panel', JText::sprintf( 'COM_HWDMS_UPLOAD_LARGE_TYPES_UP_TO_N_MB', $this->getReadableAllowedMediaTypes('large'), $maxUpload ), 'large');?>
        <?php echo $this->uberUploadHtml; ?>
      <?php endif; ?> 
      <?php if (count($this->platformExtensions) > 1) : ?>
      <?php echo JHtml::_('sliders.panel', JText::sprintf( 'COM_HWDMS_UPLOAD_TYPES_UP_TO_N_MB', $this->getReadableAllowedMediaTypes('platform'), $maxUpload ), 'large');?>
        <?php echo $this->getPlatformUploadForm(); ?>
      <?php endif; ?>
      <?php endif; ?> 
      <?php if (JFactory::getUser()->authorise('hwdmediashare.import','com_hwdmediashare') && $this->params->get('enable_uploads_remote') == 1) : ?>
      <?php echo JHtml::_('sliders.panel', JText::_( 'COM_HWDMS_ADD_REMOTE_MEDIA' ), 'large');?>
        <fieldset class="adminform">
          <div class="formelm">
            <span><?php echo JText::_('COM_HWDMS_SUPPORTED_SITES_LABEL'); ?> <?php echo $this->getReadableAllowedRemotes(); ?></span>
          </div>
        </fieldset>
        <fieldset class="adminform">
          <?php foreach($this->form->getFieldset('remote') as $field): ?>
            <div class="formelm">
              <?php echo $field->label;echo $field->input;?>
            </div>
          <?php endforeach; ?>
          <div class="formelm-buttons">
            <button type="button" onclick="Joomla.submitbutton('addmedia.remote')">
              <?php echo JText::_('COM_HWDMS_ADD') ?>
            </button>
          </div>
        </fieldset>
      <?php endif; ?>  
      <?php echo JHtml::_('sliders.end'); ?>
      <div class="clear"> </div>
      <div>
        <!--<input type="hidden" name="tmpl" value="<?php echo $this->template; ?>" />-->
        <input type="hidden" name="redirect" value="<?php echo JRequest::getWord('redirect'); ?>" />
        <input type="hidden" name="task" value="addmedia.upload" />
        <?php //echo JHtml::_('form.token'); ?>
      </div>
  </div>
</form>
