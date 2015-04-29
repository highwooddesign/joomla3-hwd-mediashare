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
?>
<form action="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=playlist&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm">

        <h1><?php echo JText::_('COM_HWDMS_MEDIA_PROCESSOR_LOG'); ?></h1>
        <p><?php echo JText::sprintf( 'COM_HWDMS_GENERATE_X_FOR_MEDIA_X', '<strong>'.$this->getProcessType($this->item).'</strong>', '<a href="">'.$this->item->title.'</a>'); ?></p>
        <br />
        <p class="alert alert-info"><?php echo JText::_('COM_HWDMS_PROCESS_LOG_ALERT_DESC'); ?></p>
        <br />
        <?php if (count($this->items) == 0) : ?>
        
                <p><?php echo JText::_('COM_HWDMS_THE_LOG_IS_EMPTY'); ?></p>
        
        <?php else : ?>  
                        
                <?php echo JHtml::_('bootstrap.startAccordion', 'slide-log', array('active' => 'log-0')); ?>
  
                <?php foreach($this->items as $i => $item): ?>
                        <?php echo JHtml::_('bootstrap.addSlide', 'slide-log', JText::sprintf('%s <span class="pull-right">%s</span>', hwdMediaShareProcesses::getStatus($item), JHtml::_('date.relative', $item->created)), 'log-'.$i); ?>
                        <div class="row-fluid form-horizontal-desktop">
                                <div class="control-group">
                                        <div class="control-label">
                                                <label><?php echo JText::_('COM_HWDMS_INPUT'); ?></label>
                                                    
                                        </div>
                                        <div class="controls">
                                                <textarea class="log-entry"><?php echo $item->input; ?></textarea>
                                        </div>
                                </div> 
                                <div class="control-group">
                                        <div class="control-label">
                                                <label><?php echo JText::_('COM_HWDMS_OUTPUT'); ?></label>
                                                    
                                        </div>
                                        <div class="controls">
                                                <textarea class="log-entry"><?php echo $item->output; ?></textarea>
                                        </div>
                                </div> 
                        </div> 
                        <?php echo JHtml::_('bootstrap.endSlide'); ?>
                <?php endforeach; ?>

                <?php echo JHtml::_('bootstrap.endAccordion'); ?>
                
        <?php endif; ?>
                
        <input type="hidden" name="task" value="" />
	<input type="hidden" name="return" value="<?php echo $input->get('return', null, 'base64'); ?>" />
        <?php echo JHtml::_('form.token'); ?>
</form>
