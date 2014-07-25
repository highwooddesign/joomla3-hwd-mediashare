<?php
/**
 * @package     Joomla.administrator
 * @subpackage  Component.hwdmediashare
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');
JHtml::_('behavior.tabstate');
JHtml::_('formbehavior.chosen', 'select');

$app = JFactory::getApplication();
$input = $app->input;
$isNew = $this->item->id == 0 ? true : false ;
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'user.cancel' || document.formvalidator.isValid(document.id('item-form')))
		{
			<?php echo $this->form->getField('description')->save(); ?>
			Joomla.submitform(task, document.getElementById('item-form'));
		}
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=playlist&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate" enctype="multipart/form-data">
	<?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>
    	<div class="form-horizontal">
                <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_HWDMS_USER_CHANNEL_DETAILS', true)); ?>
		<div class="row-fluid">
			<div class="span9">
				<fieldset class="adminform">
					<?php echo $this->form->getInput('description'); ?>
				</fieldset>
			</div>
			<div class="span3">
				<?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
            
                <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('COM_HWDMS_PUBLISHING', true)); ?>
                        <div class="row-fluid form-horizontal-desktop">
                                <div class="span6">
                                        <?php echo JLayoutHelper::render('joomla.edit.publishingdata', $this); ?>
                                </div>
                                <div class="span6">
                                        <div class="control-group">
                                                <div class="control-label">
                                                        <?php echo $this->form->getLabel('status'); ?>
                                                </div>
                                                <div class="controls">
                                                        <?php echo $this->form->getInput('status'); ?>
                                                </div>
                                        </div> 
                                        <div class="control-group">
                                                <div class="control-label">
                                                        <?php echo $this->form->getLabel('private'); ?>
                                                </div>
                                                <div class="controls">
                                                        <?php echo $this->form->getInput('private'); ?>
                                                </div>
                                        </div>
                                        <div class="control-group">
                                                <div class="control-label">
                                                        <?php echo $this->form->getLabel('likes'); ?>
                                                </div>
                                                <div class="controls">
                                                        <?php echo $this->form->getInput('likes'); ?>
                                                </div>
                                        </div>                                
                                        <div class="control-group">
                                                <div class="control-label">
                                                        <?php echo $this->form->getLabel('dislikes'); ?>
                                                </div>
                                                <div class="controls">
                                                        <?php echo $this->form->getInput('dislikes'); ?>
                                                </div>
                                        </div>   
                                </div>
                        </div>            
                <?php echo JHtml::_('bootstrap.endTab'); ?>
            
		<?php echo JLayoutHelper::render('joomla.edit.params', $this); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'thumbnail', JText::_('COM_HWDMS_CHANNEL_ART', true)); ?>
                        <?php if ($this->item->thumbnail) : ?>
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
                        <img src="<?php echo JRoute::_(hwdMediaShareThumbnails::getChannelArt($this->item)); ?>" border="0" alt="<?php echo $this->escape($this->item->title); ?>" />
		<?php echo JHtml::_('bootstrap.endTab'); ?>
            
                <?php foreach($this->item->customfields->fields as $group => $fields) : ?>
                        <?php echo JHtml::_('bootstrap.addTab', 'myTab', $group, JText::_($group, true)); ?>
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
                        <?php echo JHtml::_('bootstrap.endTab'); ?>
                <?php endforeach; ?>
            
                <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'permissions', JText::_('COM_HWDMS_PERMISSIONS', true)); ?>
                        <?php echo $this->form->getInput('rules'); ?>
                <?php echo JHtml::_('bootstrap.endTab'); ?>
            
		<?php echo JHtml::_('bootstrap.endTabSet'); ?>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="return" value="<?php echo $input->getCmd('return'); ?>" />
		<?php echo JHtml::_('form.token'); ?>
        </div>
</form>
