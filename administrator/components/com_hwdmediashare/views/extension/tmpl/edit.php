<?php
/**
 * @version    SVN $Id: edit.php 277 2012-03-28 10:03:31Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');
JHtml::_('behavior.formvalidation');
?>
<form action="<?php echo JRoute::_('index.php?option=com_hwdmediashare&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_HWDMS_FILE_EXTENSION' ); ?></legend>
			<ul class="adminformlist">
                                <?php foreach($this->form->getFieldset('details') as $field): ?>
                                        <li><?php echo $field->label;echo $field->input;?></li>
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
                <?php echo JHtml::_('sliders.end'); ?>
	</div>

        <div class="clr"> </div>

        <div>
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

