<?php
/**
 * @version    SVN $Id: default.php 459 2012-08-13 12:58:37Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// load tooltip behavior
JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$statusOptions = array("1" => "COM_HWDMS_QUEUED", "2" => "COM_HWDMS_SUCCESSFUL", "3" => "COM_HWDMS_FAILED", "4" => "COM_HWDMS_UNNECESSARY");
// Require hwdMediaShare factory
JLoader::register('JFormFieldProcess', JPATH_ROOT.'/administrator/components/com_hwdmediashare/models/fields/process.php');
$processTypeField = new JFormFieldProcess;

?>
<div id="ajax-container"></div>
<form action="<?php echo JRoute::_('index.php?option=com_hwdmediashare'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
                <?php if ($this->successful) : ?>
                        <div class="filter-search fltlft">
                                <button type="button" onclick="Joomla.submitbutton('processes.deletesuccessful')"><?php echo JText::sprintf('COM_HWDMS_DELETE_X_SUCCESSFUL', $this->successful); ?></button>
                        </div>
                <?php endif; ?>
                <?php if ($this->unnecessary) : ?>
                        <div class="filter-search fltlft">
                                <button type="button" onclick="Joomla.submitbutton('processes.deleteunnecessary')"><?php echo JText::sprintf('COM_HWDMS_DELETE_X_UNNECESSARY', $this->unnecessary); ?></button>
                        </div>
                <?php endif; ?>
		<div class="filter-select fltrt">
			<select name="filter_status" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('COM_HWDMS_LIST_SELECT_STATUS');?></option>
				<?php echo JHtml::_('select.options', $statusOptions, 'value', 'text', $this->state->get('filter.status'), true);?>
			</select>
                        <select name="filter_process_type" class="inputbox" onchange="this.form.submit()">
				<?php echo JHtml::_('select.options', $processTypeField->getOptions(), 'value', 'text', $this->state->get('filter.process_type'), true);?>
			</select>
		</div>
	</fieldset>
	<div class="clr"> </div>
        <table class="adminlist">
                <thead><?php echo $this->loadTemplate('head');?></thead>
                <tbody><?php echo $this->loadTemplate('body');?></tbody>
                <tfoot><?php echo $this->loadTemplate('foot');?></tfoot>
        </table>
        <div>
                <input type="hidden" name="task" value="" />
                <input type="hidden" name="view" value="processes" />
                <input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
                <?php echo JHtml::_('form.token'); ?>
        </div>
</form>
