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

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
?>
<div class="modal hide fade" id="collapseModal">
	<div class="modal-header">
		<button type="button" role="presentation" class="close" data-dismiss="modal">&#215;</button>
		<h3><?php echo JText::_('COM_HWDMS_BATCH_OPTIONS');?></h3>
	</div>
	<div class="modal-body modal-batch">
		<div class="row-fluid">
			<div class="control-group span6">
				<div class="controls">
                                        <?php echo $this->batchForm->getLabel('searchable'); ?>
                                        <?php echo $this->batchForm->getInput('searchable'); ?>  
				</div>
			</div>
			<div class="control-group span6">
				<div class="controls">
                                        <?php echo $this->batchForm->getLabel('visible'); ?>
                                        <?php echo $this->batchForm->getInput('visible'); ?>                                      
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="control-group span6">
				<div class="controls">
                                        <?php echo $this->batchForm->getLabel('required'); ?>
                                        <?php echo $this->batchForm->getInput('required'); ?>         
				</div>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button class="btn" type="button" onclick="document.id('batch_searchable').value='';document.id('batch_visible').value='';document.id('batch_required').value=''" data-dismiss="modal">
			<?php echo JText::_('JCANCEL'); ?>
		</button>
		<button class="btn btn-primary" type="submit" onclick="Joomla.submitbutton('customfield.batch');">
			<?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?>
		</button>
	</div>
</div>
