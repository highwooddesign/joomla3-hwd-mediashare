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
JHtml::_('formbehavior.chosen', '.hwd-form-tags', null, array('placeholder_text_multiple' => 'Tags'));
JHtml::_('formbehavior.chosen', 'select');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'groupform.cancel' || document.formvalidator.isValid(document.getElementById('adminForm')))
		{
			<?php echo $this->form->getField('description')->save(); ?>
			Joomla.submitform(task);
		}
	}
</script>
<div class="edit">
<form action="<?php echo JRoute::_('index.php?option=com_hwdmediashare&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">
  <div id="hwd-container" class="<?php echo $this->pageclass_sfx;?>"> <a name="top" id="top"></a>
    <!-- Media Navigation -->
    <?php echo hwdMediaShareHelperNavigation::getInternalNavigation(); ?>
    <!-- Media Header -->
    <div class="media-header">
      <h2 class="media-group-title"><?php echo $this->escape($this->params->get('page_heading')); ?></h2>
    </div>
    <div class="clear"></div>  
    <fieldset>
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
              <?php echo $this->form->getLabel('tags'); ?>
            </div>              
            <div class="controls">
              <?php echo $this->form->getInput('tags'); ?>
            </div>
          </div>            
        </div>
        <div class="span4">
          <div class="control-group">
            <div class="control-label hide">
              <?php echo $this->form->getLabel('private'); ?>
            </div>                 
            <div class="controls">
              <?php echo $this->form->getInput('private'); ?>
            </div>
          </div> 
          <div class="btn-toolbar row-fluid">
            <button type="button" class="btn btn-primary btn-large span12" onclick="Joomla.submitbutton('groupform.save')">
              <?php echo JText::_('JSAVE') ?>
            </button>
          </div> 
          <?php if (!$this->isNew) : ?>
            <div class="btn-toolbar row-fluid">
              <a class="btn span12 media-popup-iframe-page" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=groupmedia&tmpl=component&group_id=' . $this->item->id); ?>" title="<?php echo JText::_('COM_HWDMS_MANAGE_MEDIA'); ?>"><?php echo JText::_('COM_HWDMS_MANAGE_MEDIA'); ?></a>
            </div> 
            <div class="btn-toolbar row-fluid">
              <a class="btn span12 media-popup-iframe-page" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=groupmembers&tmpl=component&group_id=' . $this->item->id); ?>" title="<?php echo JText::_('COM_HWDMS_MANAGE_MEMBERS'); ?>"><?php echo JText::_('COM_HWDMS_MANAGE_MEMBERS'); ?></a>
            </div> 
          <?php endif; ?>           
        </div>
      </div>  
    </fieldset>     
    <?php echo JHtml::_('bootstrap.startTabSet', 'pane', array('active' => 'group')); ?>
    <!-- Details -->
    <?php echo JHtml::_('bootstrap.addTab', 'pane', 'group', JText::_('COM_HWDMS_GROUP', true)); ?>
    <fieldset>
      <?php echo $this->form->getInput('description'); ?>
    </fieldset> 
    <?php echo JHtml::_('bootstrap.endTab'); ?> 
    <!-- Thumbnail -->
    <?php echo JHtml::_('bootstrap.addTab', 'pane', 'thumbnail', JText::_('COM_HWDMS_THUMBNAIL', true)); ?>
    <fieldset>
      <div class="row-fluid">
        <div class="span8">
          <?php if (!$this->isNew && $this->item->thumbnail) : ?>
          <div class="control-group">
            <div class="control-label">
              <?php echo $this->form->getLabel('remove_thumbnail'); ?>
            </div>
            <div class="controls">
              <?php echo $this->form->getInput('remove_thumbnail'); ?>
            </div>
          </div>  
          <?php endif; ?> 
          <div class="control-group">
            <div class="control-label">
              <?php echo $this->form->getLabel('thumbnail'); ?>
            </div>
            <div class="controls">
              <?php echo $this->form->getInput('thumbnail'); ?>
            </div>
          </div> 
        </div>
        <div class="span4">
          <img src="<?php echo JRoute::_(hwdMediaShareDownloads::thumbnail($this->item, 3)); ?>" border="0" class="img-responsive pull-right" alt="<?php echo $this->escape($this->item->title); ?>" /> 
        </div>
      </div>  
    </fieldset> 
    <?php echo JHtml::_('bootstrap.endTab'); ?>
    <!-- Custom Fields -->
    <?php foreach($this->item->customfields->fields as $group => $fields) : ?>
      <?php echo JHtml::_('bootstrap.addTab', 'pane', $group, JText::_($group, true)); ?>
        <fieldset>
        <?php foreach($fields as $field) : ?>
          <div class="control-group">
            <div class="control-label">
              <?php echo $this->item->customfields->getLabel($field); ?>
            </div>
            <div class="controls">
              <?php echo $this->item->customfields->getInput($field); ?>
            </div>
          </div>
        <?php endforeach; ?>
        </fieldset>
      <?php echo JHtml::_('bootstrap.endTab'); ?>
    <?php endforeach; ?> 
    <!-- Moderation -->
    <?php if ($this->item->attributes->get('access-change')): ?>
    <?php echo JHtml::_('bootstrap.addTab', 'pane', 'moderation', JText::_('COM_HWDMS_MODERATION', true)); ?>
    <fieldset>
      <div class="row-fluid">
        <div class="span8">
            <div class="control-group">
              <div class="control-label">
                <?php echo $this->form->getLabel('alias'); ?>
              </div>
              <div class="controls">
                <?php echo $this->form->getInput('alias'); ?>
              </div>
            </div>        
            <div class="control-group">
              <div class="control-label">
                <?php echo $this->form->getLabel('publish_up'); ?>
              </div>
              <div class="controls">
                <?php echo $this->form->getInput('publish_up'); ?>
              </div>
            </div>  
            <div class="control-group">
              <div class="control-label">
                <?php echo $this->form->getLabel('publish_down'); ?>
              </div>
              <div class="controls">
                <?php echo $this->form->getInput('publish_down'); ?>
              </div>
            </div> 
            <div class="control-group">
              <div class="control-label">
                <?php echo $this->form->getLabel('meta_desc', 'params'); ?>
              </div>
              <div class="controls">
                <?php echo $this->form->getInput('meta_desc', 'params'); ?>      
              </div>
            </div>
            <div class="control-group">
              <div class="control-label">
                <?php echo $this->form->getLabel('meta_keys', 'params'); ?>
              </div>
              <div class="controls">
                <?php echo $this->form->getInput('meta_keys', 'params'); ?>    
              </div>
            </div>
        </div>
        <div class="span4">
            <div class="control-group">
              <div class="control-label hide">
                <?php echo $this->form->getLabel('status'); ?>
              </div>
              <div class="controls">
                <?php echo $this->form->getInput('status'); ?>
              </div>
            </div> 
            <div class="control-group">
              <div class="control-label hide">
                <?php echo $this->form->getLabel('published'); ?>
              </div>
              <div class="controls">
                <?php echo $this->form->getInput('published'); ?>
              </div>
            </div>  
            <div class="control-group">
              <div class="control-label hide">
                <?php echo $this->form->getLabel('featured'); ?>
              </div>
              <div class="controls">
                <?php echo $this->form->getInput('featured'); ?>
              </div>
            </div>            
            <div class="control-group">
              <div class="control-label">
                <?php echo $this->form->getLabel('access'); ?>
              </div>
              <div class="controls">
                <?php echo $this->form->getInput('access'); ?>
              </div>
            </div> 
            <div class="control-group">
              <div class="control-label">
                <?php echo $this->form->getLabel('language'); ?>
              </div>
              <div class="controls">
                <?php echo $this->form->getInput('language'); ?>
              </div>
            </div>  
        </div>
      </div>  
    </fieldset> 
    <?php else: // These need to be set when submitted, but they are also validated later. ?>
      <input type="hidden" name="jform[published]" value="1" />
      <input type="hidden" name="jform[status]" value="1" />
      <input type="hidden" name="jform[featured]" value="0" />
      <input type="hidden" name="jform[access]" value="1" />
    <?php endif; ?>
    <?php echo JHtml::_('bootstrap.endTab'); ?>          
    <?php echo JHtml::_('bootstrap.endTabSet'); ?>         
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="return" value="<?php echo $this->return_page;?>" />
    <?php echo JHtml::_( 'form.token' ); ?>
  </div>
</form>
</div>

