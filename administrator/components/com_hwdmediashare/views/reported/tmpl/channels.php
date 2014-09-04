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

$app = JFactory::getApplication();

if ($app->isSite())
{
	JSession::checkToken('get') or die(JText::_('JINVALID_TOKEN'));
}

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.framework', true);

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>              
<form action="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=reported&layout=channels&tmpl=component&id=' . $app->input->get('id', 0, 'integer'));?>" method="post" name="adminForm" id="adminForm" class="form-inline">
<?php if (!empty($this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
        <table class="table table-striped table-condensed">
		<thead>
			<tr>
				<th class="title">
					<?php echo JHtml::_('grid.sort', 'COM_HWDMS_REPORT_DETAILS', 'a.report_id', $listDirn, $listOrder); ?>                                    
				</th>
                                <th width="15%" class="center nowrap">
                                        <?php echo JHtml::_('grid.sort', 'COM_HWDMS_REPORTED_BY', 'a.user_id', $listDirn, $listOrder); ?>
                                </th>                                
				<th width="5%" class="center nowrap">
					<?php echo JHtml::_('grid.sort', 'JDATE', 'a.created', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="3">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) : ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td>  
                                        <?php echo JHtml::_('HwdGrid.id', $i, $item->id, 'cb', false, 'cid', 'hide'); ?>
                                        <div class="pull-right label"><?php echo $this->getReportType($item); ?></div>
                                        <p><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=channel.edit&id=' . $item->element_id); ?>" title="<?php echo JText::_('JACTION_EDIT'); ?>" target="_blank"><?php echo $this->escape($item->title); ?></a></p>
                                        <div class="small">
                                                <blockquote><?php echo $this->escape($item->description); ?></blockquote> 
                                        </div>  
                                        <div class="btn-group">
                                                <a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i; ?>', 'report.dismiss')" class="btn btn-mini"><i class="icon-trash"></i> <?php echo JText::_('COM_HWDMS_BTN_DISMISS_REPORT'); ?></a>                                            
                                                <a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i; ?>', 'report.remove')" class="btn btn-mini"><i class="icon-trash"></i> <?php echo JText::_('COM_HWDMS_BTN_REMOVE_CHANNEL'); ?></a>                                            
                                                <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=channel.edit&id=' . $item->element_id); ?>" target="_blank" class="btn btn-mini"><i class="icon-eye"></i> <?php echo JText::_('COM_HWDMS_BTN_VIEW_CHANNEL'); ?></a>                                            
                                        </div>  
				</td>
				<td class="center">
					<?php echo $this->escape($item->author); ?>
				</td>
				<td class="center nowrap">
					<?php echo JHtml::_('date.relative', $item->created); ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
        <?php if (empty($this->items)) : ?>
                <div class="alert alert-no-items">
                        <?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                </div>                                 
        <?php endif; ?>             
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<input type="hidden" name="return" value="<?php echo $this->return; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
	</div>
</form>

