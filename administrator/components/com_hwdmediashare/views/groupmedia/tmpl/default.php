<?php
/**
 * @version    SVN $Id: default.php 425 2012-06-28 07:48:57Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      01-Nov-2011 14:19:47
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// load tooltip behavior
JHtml::_('behavior.tooltip');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$selectOptions = array("linked" => "COM_HWDMS_LINKED", "all" => "COM_HWDMS_ALL");
?>
<form action="<?php echo JRoute::_('index.php?option=com_hwdmediashare'); ?>" method="post" name="adminForm" id="adminForm">
        <fieldset>
		<div class="fltrt">
                        <button type="button" onclick="Joomla.submitform('<?php echo $this->view_list;?>.unlink', this.form);">
                                <?php echo JText::_('COM_HWDMS_REMOVE');?></button>
                        <?php if ($this->viewAll) : ?>
                                <button type="button" onclick="Joomla.submitform('<?php echo $this->view_list;?>.link', this.form);">
                                        <?php echo JText::_('COM_HWDMS_ADD');?></button>
                        <?php endif; ?>
			<button type="button" onclick="<?php echo JRequest::getBool('refresh', 0) ? 'window.parent.location.href=window.parent.location.href;' : '';?>  window.parent.SqueezeBox.close();">
				<?php echo JText::_('JCANCEL');?></button>
		</div>
                <div class="configuration">
                        <?php echo JText::_($this->view_header);?>
                </div>
        </fieldset>
        <fieldset id="filter-bar">
                <div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_HWDMS_SEARCH_IN_TITLE'); ?>" />
			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="filter-select fltrt">
			<select name="filter_linked" class="inputbox" onchange="this.form.submit()">
				<?php echo JHtml::_('select.options', $selectOptions, 'value', 'text', $this->state->get('filter.linked'), true);?>
			</select>
		</div>
	</fieldset>
	<div class="clr"> </div>
        <table class="adminlist">
                <thead><?php echo $this->loadTemplate('head');?></thead>
                <tfoot><?php echo $this->loadTemplate('foot');?></tfoot>
                <tbody><?php echo $this->loadTemplate('body');?></tbody>
        </table>
        <div>
                <input type="hidden" name="tmpl" value="component" />
                <input type="hidden" name="group_id" value="<?php echo $this->groupId;?>" />
                <input type="hidden" name="task" value="" />
                <input type="hidden" name="view" value="<?php echo $this->view_list;?>" />
                <input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
                <?php echo JHtml::_('form.token'); ?>
        </div>
</form>
