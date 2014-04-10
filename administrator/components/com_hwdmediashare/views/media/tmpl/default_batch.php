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
					<?php echo JHtml::_('batch.tag'); ?>
				</div>
			</div>
			<div class="control-group span6">
				<div class="controls">
					<?php echo JHtml::_('batch.language'); ?>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="control-group span6">
				<div class="controls">
					<?php echo JHtml::_('batch.access'); ?>
				</div>
			</div>
			<div class="control-group span6">
				<div class="controls">
                                        <?php echo $this->batchForm->getLabel('assignprocess'); ?>
                                        <?php echo $this->batchForm->getInput('assignprocess'); ?>                                    
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="control-group span6">
				<div class="controls">
                                        <?php echo $this->batchForm->getLabel('assigncategory'); ?>
                                        <?php echo $this->batchForm->getInput('assigncategory'); ?> 
				</div>
			</div>
			<div class="control-group span6">
				<div class="controls">
                                        <?php echo $this->batchForm->getLabel('unassigncategory'); ?>
                                        <?php echo $this->batchForm->getInput('unassigncategory'); ?>                                     
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="control-group span6">
				<div class="controls">
                                        <?php echo $this->batchForm->getLabel('assignalbum'); ?>
                                        <?php echo $this->batchForm->getInput('assignalbum'); ?> 
				</div>
			</div>
			<div class="control-group span6">
				<div class="controls">
                                        <?php echo $this->batchForm->getLabel('unassignalbum'); ?>
                                        <?php echo $this->batchForm->getInput('unassignalbum'); ?> 
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="control-group span6">
				<div class="controls">
                                        <?php echo $this->batchForm->getLabel('assignplaylist'); ?>
                                        <?php echo $this->batchForm->getInput('assignplaylist'); ?> 
				</div>
			</div>
			<div class="control-group span6">
				<div class="controls">
                                        <?php echo $this->batchForm->getLabel('unassignplaylist'); ?>
                                        <?php echo $this->batchForm->getInput('unassignplaylist'); ?> 
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="control-group span6">
				<div class="controls">
                                        <?php echo $this->batchForm->getLabel('assigngroup'); ?>
                                        <?php echo $this->batchForm->getInput('assigngroup'); ?> 
				</div>
			</div>
			<div class="control-group span6">
				<div class="controls">
                                        <?php echo $this->batchForm->getLabel('unassigngroup'); ?>
                                        <?php echo $this->batchForm->getInput('unassigngroup'); ?> 
				</div>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button class="btn" type="button" onclick="document.id('batch-access').value='';" data-dismiss="modal">
			<?php echo JText::_('JCANCEL'); ?>
		</button>
		<button class="btn btn-primary" type="submit" onclick="Joomla.submitbutton('editmedia.batch');">
			<?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?>
		</button>
	</div>
</div>
