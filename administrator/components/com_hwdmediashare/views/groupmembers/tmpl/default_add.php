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

JHtml::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.framework', true);

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=groupmembers&tmpl=component&group_id='.$this->groupId.'&add=1'); ?>" method="post" name="adminForm" id="adminForm" class="form-inline">
        <fieldset class="filter clearfix">
		<div class="btn-toolbar">
			<div class="btn-group pull-left">
				<input type="text" name="filter[search]" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" size="30" title="<?php echo JText::_('COM_HWDMS_FILTER_SEARCH_DESC'); ?>" />
			</div>
			<div class="btn-group pull-left">
				<button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>" data-placement="bottom">
					<span class="icon-search"></span><?php echo '&#160;' . JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
				<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" data-placement="bottom" onclick="document.id('filter_search').value='';this.form.submit();">
					<span class="icon-remove"></span><?php echo '&#160;' . JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
			</div>                
			<div class="btn-group pull-left">
                                <a class="btn btn-info" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=groupmembers&tmpl=component&group_id='.$this->groupId.'&add=0'); ?>">
					<i class="icon-menu"></i><?php echo '&#160;' . JText::_('COM_HWDMS_BTN_ORGANISE_MEMBERS'); ?></a>                       
			</div>
			<div class="clearfix"></div>
		</div>
	</fieldset>
	<table class="table table-striped table-condensed">
		<thead>
			<tr>
                                <th width="1%" class="center">
                                        <?php echo JHtml::_('grid.checkall', 'checkall-toggle', 'JGLOBAL_CHECK_ALL', 'Joomla.checkAll(this, \'cb\')'); ?>
                                </th>
                                <th>
                                        <a class="label" href="javascript:void(0);" onclick="Joomla.submitbutton('groupmembers.link')">
                                                <strong><?php echo JText::_('COM_HWDMS_BTN_ADD_SELECTED_TO_GROUP') ?></strong>
                                        </a>                                  
                                </th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="2">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) : ?>
			<tr class="row<?php echo $i % 2; ?>">
                                <td class="center">
                                        <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                                </td>                            
				<td>
                                        <?php echo $this->getButton($item, $i); ?>                                                                                                           
                                        <div class="pull-left thumb-wrapper">
                                                <?php echo JHtml::_('hwdimage.avatar', $item->id, 50); ?>
                                        </div>
                                        <p><strong><?php echo $this->escape($item->name); ?></strong></p>
                                        <?php echo $item->username; ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
