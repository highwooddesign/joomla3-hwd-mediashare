<?php
/**
 * @version    SVN $Id: edit.php 1407 2013-04-30 09:35:55Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      26-Oct-2011 10:32:50
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
$params = $this->form->getFieldsets('params');
$publishing = $this->form->getFieldsets('publishing');
JHtml::_('behavior.modal');
?>
<form action="<?php echo JRoute::_('index.php?option=com_hwdmediashare&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
	<div class="width-60 fltlft">
                <fieldset class="adminform">
                        <legend><?php echo JText::_( 'COM_HWDMS_ACTIVITY_DETAILS' ); ?></legend>
                        <ul class="adminformlist">
                                <?php foreach($this->form->getFieldset('details') as $field): ?>
                                        <?php if ($field->name == "jform[description]"): ?>
                                            <?php if ($this->item->activity_type == 1): ?>
                                            <li><?php echo $field->label;echo $field->input;?></li>
                                            <?php endif;?>
                                        <?php else:?>
                                            <li><?php echo $field->label;echo $field->input;?></li>
                                        <?php endif;?>
                                <?php endforeach; ?>
                        </ul>
                </fieldset>
        </div>

	<div class="width-40 fltrt">

		<?php echo JHtml::_('sliders.start', 'hwdmediashare-slider'); ?>

                <?php echo JHtml::_('sliders.panel', JText::_( 'COM_HWDMS_PUBLISHING' ), 'publishing');?>
                <fieldset class="adminform" >
                        <ul class="panelform">
                            <?php foreach($this->form->getFieldset('publishing') as $field): ?>
                                <li><?php echo $field->label;echo $field->input;?></li>
                            <?php endforeach; ?>
                        </ul>
                </fieldset>

                <?php foreach ($params as $name => $fieldset): ?>
                        <?php echo JHtml::_('sliders.panel', JText::_($fieldset->label), $name.'-params');?>
                        <?php if (isset($fieldset->description) && trim($fieldset->description)): ?>
                            <p class="tip"><?php echo $this->escape(JText::_($fieldset->description));?></p>
                        <?php endif;?>
                        <fieldset class="panelform" >
                                <ul class="adminformlist">
                                        <?php foreach ($this->form->getFieldset($name) as $field) : ?>
                                                <li><?php echo $field->label; ?><?php echo $field->input; ?></li>
                                        <?php endforeach; ?>
                                </ul>
                        </fieldset>
                <?php endforeach; ?>

                <?php echo JHtml::_('sliders.end'); ?>

	</div>

        <div class="clr"> </div>

	<div>
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>