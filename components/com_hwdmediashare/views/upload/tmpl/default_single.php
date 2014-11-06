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

// Load tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.framework');

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/html');
JHtml::_('HwdPopup.iframe', 'page');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.calendar');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', '.hwd-form-catid', null, array('placeholder_text_multiple' => 'Category'));
JHtml::_('formbehavior.chosen', '.hwd-form-tags', null, array('placeholder_text_multiple' => 'Tags'));
JHtml::_('formbehavior.chosen', 'select');

$user = JFactory::getUser();
?>   
<form action="<?php echo JRoute::_('index.php?option=com_hwdmediashare'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">
  <div id="hwd-container" class="<?php echo $this->pageclass_sfx;?>"> <a name="top" id="top"></a>
    <!-- Media Navigation -->
    <?php echo hwdMediaShareHelperNavigation::getInternalNavigation(); ?>
    <!-- Media Header -->
    <div class="media-header">
      <h2 class="media-upload-title"></h2>
    </div>   
    <?php if ($user->authorise('hwdmediashare.upload', 'com_hwdmediashare') && $this->params->get('enable_uploads_file') == "1") : ?>
    <fieldset>
      <div class="btn-toolbar row-fluid">   
        <div class="span12">
          <div class="control-group">
            <div class="control-label hide">
              <?php echo $this->form->getLabel('Filedata'); ?>
            </div>                  
            <div class="controls">
              <?php echo $this->form->getInput('Filedata'); ?>
            </div>
          </div> 
        </div>
      </div>
      <div class="row-fluid">
        <div class="span8">
          <div class="control-group">
            <div class="control-label hide">
              <?php echo $this->form->getLabel('title'); ?>
            </div>                  
            <div class="controls">
              <?php echo $this->form->getInput('title'); ?>
            </div>
          </div>        
          <div class="control-group">
            <div class="control-label hide">
              <?php echo $this->form->getLabel('catid'); ?>
            </div>                
            <div class="controls">
              <?php echo $this->form->getInput('catid'); ?>
            </div>
          </div>             
          <div class="control-group">
            <div class="control-label hide">
              <?php echo $this->form->getLabel('tags'); ?>
            </div>              
            <div class="controls">
              <?php echo $this->form->getInput('tags'); ?>
            </div>
          </div>            
        </div>
        <div class="span4">
          <div class="control-group">
            <div class="controls">
              <?php echo $this->form->getInput('private'); ?>
            </div>
          </div>            
          <div class="btn-toolbar row-fluid">
            <button type="button" class="btn btn-primary btn-large span12" onclick="Joomla.submitbutton('addmedia.upload')">
              <?php echo JText::_('COM_HWDMS_BUTTON_UPLOAD') ?>
            </button>
          </div> 
          <div class="btn-toolbar row-fluid">
            <a title="<?php echo JText::_('COM_HWDMS_ADD_MEDIA'); ?>" href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getUploadRoute(array('method' => 'remote'))); ?>" class="btn span12">or add remote media</a>
          </div>             
        </div>
      </div>  
      <?php echo $this->form->getInput('description'); ?>
      <div class="clearfix"></div>
      <div class="well well-small">
        <h3><?php echo JText::_('COM_HWDMS_HELP_AND_SUGGESTIONS'); ?></h3>
        <?php if ($this->params->get('upload_terms_id')): ?>
          <p><?php echo JText::sprintf('COM_HWDMS_ACKNOWLEDGE_TERMS_AND_CONDITIONS', '<a href="' . JRoute::_(ContentHelperRoute::getArticleRoute($this->params->get('upload_terms_id')) . '&tmpl=component') . '" class="media-popup-iframe-page">' . JText::_('COM_HWDMS_TERMS_AND_CONDITIONS_LINK') . '</a>'); ?></p>      
        <?php endif; ?>
        <p><?php echo JText::sprintf('COM_HWDMS_SUPPORTED_FORMATS_LIST_X', implode(', ', $this->localExtensions)); ?> <?php echo JText::sprintf('COM_HWDMS_MAXIMUM_UPLOAD_SIZE_X', hwdMediaShareUpload::getMaximumUploadSize('standard')); ?></p>
      </div> 
    </fieldset> 
    <?php endif; ?>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="return" value="<?php echo $this->return; ?>" />
    <?php foreach($this->jformdata as $name => $value): ?>
      <?php if (is_array($value)) : ?>
        <?php foreach($value as $key => $id): ?>
          <?php if (!empty($id)) : ?><input type="hidden" name="jform[<?php echo $name; ?>][]" value="<?php echo $id; ?>" /><?php endif; ?>
        <?php endforeach; ?>
      <?php elseif(!empty($value)): ?>
        <input type="hidden" name="jform[<?php echo $name; ?>]" value="<?php echo $value; ?>" />
      <?php endif; ?>
    <?php endforeach; ?> 
    <?php // The token breaks the XML redirect file for uber, so it is removed by the uber javascript ?>
    <input type="hidden" name="<?php echo JSession::getFormToken(); ?>" id="formtoken" value="1" />
  </div>
</form>
