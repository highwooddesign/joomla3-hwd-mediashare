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
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');

$app = JFactory::getApplication();
$input = $app->input;
$isNew = $this->item->id == 0 ? true : false ;
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'extension.cancel' || document.formvalidator.isValid(document.id('item-form')))
		{
			Joomla.submitform(task, document.getElementById('item-form'));
		}
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=extension&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate" enctype="multipart/form-data">

        <div class="form-inline form-inline-header">
                <div class="control-group">
                        <div class="control-label">
                                <?php echo $this->form->getLabel('ext'); ?>
                        </div>
                        <div class="controls">
                                <?php echo $this->form->getInput('ext'); ?>
                        </div>
                </div>
        </div>
    
	<div class="form-horizontal">
            
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_HWDMS_FILE_EXTENSION', true)); ?>
                <div class="control-group">
                        <div class="control-label">
                                <?php echo $this->form->getLabel('media_type'); ?>
                        </div>
                        <div class="controls">
                                <?php echo $this->form->getInput('media_type'); ?>
                        </div>
                </div>
                <div class="control-group">
                        <div class="control-label">
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
		<?php echo JHtml::_('bootstrap.endTab'); ?>

                <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('COM_HWDMS_PUBLISHING', true)); ?>
                        <?php echo JLayoutHelper::render('joomla.edit.publishingdata', $this); ?>         
                <?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JHtml::_('bootstrap.endTabSet'); ?>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="return" value="<?php echo $input->getCmd('return'); ?>" />
		<?php echo JHtml::_('form.token'); ?>
        </div>
</form>
