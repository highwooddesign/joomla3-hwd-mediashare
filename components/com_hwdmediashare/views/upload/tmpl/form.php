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

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.calendar');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', '.hwd-form-catid', null, array('placeholder_text_multiple' => 'Category'));
JHtml::_('formbehavior.chosen', '.hwd-form-tags', null, array('placeholder_text_multiple' => 'Tags'));
JHtml::_('formbehavior.chosen', 'select');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'addmedia.cancel' || document.formvalidator.isValid(document.getElementById('adminForm')))
		{
			<?php echo $this->form->getField('description')->save(); ?>
			Joomla.submitform(task);
		}
	}
</script>
<div class="edit">
<form action="<?php echo JRoute::_('index.php?option=com_hwdmediashare'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">
  <div id="hwd-container" class="<?php echo $this->pageclass_sfx;?>"> <a name="top" id="top"></a>
    <!-- Media Navigation -->
    <?php echo hwdMediaShareHelperNavigation::getInternalNavigation(); ?>
    <!-- Media Header -->
    <div class="media-header">
      <a title="<?php echo JText::_('COM_HWDMS_ADD_MEDIA'); ?>" href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getUploadRoute(array('method' => 'remote'))); ?>" class="btn pull-right media-upload-switch"><?php echo JText::_('COM_HWDMS_BTN_UPLOAD_SWITCH_ADD_REMOTE'); ?></a>
      <h2 class="media-upload-title"><?php echo JText::_('COM_HWDMS_UPLOAD_MEDIA_FILES'); ?></h2>
    </div>
    <div class="clear"></div> 
    <fieldset>
      <div class="row-fluid form-horizontal-desktop">
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
            <button type="button" class="btn btn-primary btn-large span12" onclick="Joomla.submitbutton('addmedia.processform')">
              <?php echo JText::_('COM_HWDMS_BUTTON_UPLOAD') ?>
            </button>
          </div> 
          <p>Click "Upload" to save your details and upload your media.</p>
        </div>
      </div>  
      <?php echo $this->form->getInput('description'); ?>
    </fieldset> 
    <?php // These need to be set when submitted, but they are also validated later. ?>    
    <input type="hidden" name="jform[published]" value="1" />
    <input type="hidden" name="jform[status]" value="1" />
    <input type="hidden" name="jform[featured]" value="0" />
    <input type="hidden" name="jform[access]" value="1" />  
    <input type="hidden" name="task" value="" />
    <?php echo $this->form->getInput('album_id'); ?>
    <?php echo $this->form->getInput('category_id'); ?>
    <?php echo $this->form->getInput('group_id'); ?>
    <?php echo $this->form->getInput('playlist_id'); ?>
    <?php echo JHtml::_('form.token'); ?>
  </div>
</form>
</div>
