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
		if (task == 'categoryform.cancel' || document.formvalidator.isValid(document.getElementById('adminForm')))
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
      <h2 class="media-category-title"><?php echo $this->escape($this->params->get('page_heading')); ?></h2>
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
          <div class="btn-toolbar category-save-button row-fluid">
            <button type="button" class="btn btn-primary btn-large span12" onclick="Joomla.submitbutton('categoryform.save')">
              <?php echo JText::_('JSAVE') ?>
            </button>
          </div> 
        </div>
      </div>  
    </fieldset>      
    <?php echo JHtml::_('bootstrap.startTabSet', 'pane', array('active' => 'category')); ?>
    <!-- Details -->
    <?php echo JHtml::_('bootstrap.addTab', 'pane', 'category', JText::_('COM_HWDMS_CATEGORY', true)); ?>
    <fieldset>
      <?php echo $this->form->getInput('description'); ?>
    </fieldset>
    <?php echo JHtml::_('bootstrap.endTab'); ?>
    <?php if ($this->item->attributes->get('access-change')): ?>
    <!-- Moderation -->
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
                <?php echo $this->form->getLabel('metadesc'); ?>
              </div>
              <div class="controls">
                <?php echo $this->form->getInput('metadesc'); ?>
              </div>
            </div>
            <div class="control-group">
              <div class="control-label">
                <?php echo $this->form->getLabel('metakey'); ?>
              </div>
              <div class="controls">
                <?php echo $this->form->getInput('metakey'); ?>
              </div>
            </div>
        </div>
        <div class="span4">
            <div class="control-group">
              <div class="control-label hide">
                <?php echo $this->form->getLabel('published'); ?>
              </div>
              <div class="controls">
                <?php echo $this->form->getInput('published'); ?>
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
    <?php endif; ?>
    <?php echo JHtml::_('bootstrap.endTab'); ?>          
    <?php echo JHtml::_('bootstrap.endTabSet'); ?>         
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="return" value="<?php echo $this->return_page;?>" />
    <?php echo JHtml::_( 'form.token' ); ?>
  </div>
</form>
</div>
