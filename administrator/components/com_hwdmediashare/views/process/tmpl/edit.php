<?php
/**
 * @version    SVN $Id: edit.php 425 2012-06-28 07:48:57Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      09-Nov-2011 09:20:38
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// load tooltip behavior
JHtml::_('behavior.tooltip');

?>
<form action="<?php echo JRoute::_('index.php?option=com_hwdmediashare&layout=edit'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
        <fieldset class="adminform" >
                <legend><?php echo JText::_('COM_HWDMS_MEDIA_PROCESSOR_LOG'); ?></legend>
                <div class="clr"></div>
                <p><?php echo JText::sprintf( 'COM_HWDMS_GENERATE_X_FOR_MEDIA_X', '<strong>'.$this->getProcessType($this->process).'</strong>', '<strong>'.$this->process->title ).'</strong>'; ?></p>
                <?php if (count($this->items) == 0) : ?>
                        <p><?php echo JText::_('COM_HWDMS_THE_LOG_IS_EMPTY'); ?></p>
                <?php else : ?>               
                <ul class="panelform">
                         <?php echo JHtml::_('sliders.start', 'hwdmediashare-slider'); ?>
                                <?php foreach($this->items as $i => $item): ?>
                                        <?php echo JHtml::_('sliders.panel', JText::sprintf( '%s - %s', JHtml::_('date',$item->created, JText::_('DATE_FORMAT_LC2')), hwdMediaShareProcesses::getStatus($item) ), 'publishing');?>
                                        <fieldset class="adminform" >
                                                <ul class="panelform">
                                                        <li>
                                                                <label title="" class="hasTip" for="jform_input" id="jform_input-lbl"><?php echo JText::_('COM_HWDMS_INPUT'); ?></label>
                                                                <textarea style="width: 100%; height: 100px;" rows="0" cols="0" id="jform_input" name="jform[input]"><?php echo $item->input; ?></textarea>
                                                        </li>
                                                        <li>
                                                                <label title="" class="hasTip" for="jform_output" id="jform_output-lbl"><?php echo JText::_('COM_HWDMS_OUTPUT'); ?></label>
                                                                <textarea style="width: 100%; height: 100px;" rows="0" cols="0" id="jform_output" name="jform[output]"><?php echo $item->output; ?></textarea>
                                                        </li>
                                                </ul>
                                        </fieldset>
                                <?php endforeach; ?>
                        <?php echo JHtml::_('sliders.end'); ?>

                </ul>
                <?php endif; ?>
        </fieldset>
	<div>
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

