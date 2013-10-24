<?php
/**
 * @version    SVN $Id: media.php 1249 2013-03-08 14:24:48Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      01-Feb-2012 08:50:16
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// load tooltip behavior
JHtml::_('behavior.tooltip');
JHtml::_('script','system/multiselect.js', false, true);

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$id             = $this->escape($this->state->get('filter.element_id'));
?>
<form action="<?php echo JRoute::_('index.php?option=com_hwdmediashare'); ?>" method="post" name="adminForm" id="adminForm">
        <fieldset>
		<div class="fltrt">
                        <button type="button" onclick="Joomla.submitform('reported.delete', this.form);">
                                <?php echo JText::_('JTOOLBAR_DELETE');?></button>
 			<button type="button" onclick="<?php echo JRequest::getBool('refresh', 0) ? 'window.parent.location.href=window.parent.location.href;' : '';?>  window.parent.SqueezeBox.close();">
				<?php echo JText::_('JCANCEL');?></button>
		</div>
                <div class="configuration">
                        <?php echo JText::_('COM_HWDMS_REPORTED_MEDIA');?>
                </div>
        </fieldset>
        <fieldset id="filter-bar">
                <div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_HWDMS_SEARCH_IN_TITLE'); ?>" />
			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
	</fieldset>
	<div class="clr"> </div>
        <table class="adminlist">
                <thead>
                        <tr>
                                <th width="20">
                                        <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
                                </th>                            
                                <th width="20%">
                                        <?php echo JHtml::_('grid.sort',  JText::_('COM_HWDMS_REPORTS'), 'a.report_id', $listDirn, $listOrder); ?>
                                </th>
                                <th> 
                                        <?php echo JHtml::_('grid.sort', JText::_('COM_HWDMS_COMMENT'), 'a.description', $listDirn, $listOrder); ?>
                                </th>
                                <th width="20%">
                                        <?php echo JHtml::_('grid.sort', JText::_('JGRID_HEADING_CREATED_BY'), 'a.user_id', $listDirn, $listOrder); ?>
                                </th>
                                <th width="5%">
                                        <?php echo JHtml::_('grid.sort', JText::_('JDATE'), 'a.created', $listDirn, $listOrder); ?>
                                </th>
                           </tr>
                </thead>
                <tbody>
                        <?php foreach($this->items as $i => $item):
                                $owner =& JFactory::getUser($item->user_id);
                                ?>
                                <tr class="row<?php echo $i % 2; ?>">
                                        <td>
                                                <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                                        </td>
                                        <td class="center">
                                                <?php echo $this->getReportType($item); ?>
                                        </td>
                                        <td>
                                                <span class="editlinktip hasTip" title="<?php echo JText::_('COM_HWDMS_DESCRIPTION'); ?>::<?php echo $this->escape($item->description); ?>" >
                                                <?php echo $this->escape($item->description); ?>
                                                </span>
                                        </td>
                                        <td class="center">
                                                <?php echo $this->escape($owner->username); ?>
                                        </td>
                                        <td class="center nowrap">
                                                <?php echo JHtml::_('date',$item->created, JText::_('DATE_FORMAT_LC4')); ?>
                                        </td>
                                </tr>
                        <?php endforeach; ?>
                </tbody>
                <tfoot>
                        <tr>
                                <td colspan="6"><?php echo $this->pagination->getListFooter(); ?></td>
                        </tr>
                </tfoot>
        </table>
        <div>
                <input type="hidden" name="task" value="" />
                <input type="hidden" name="view" value="reported" />
                <input type="hidden" name="boxchecked" value="0" />
                <input type="hidden" name="tmpl" value="component" />
                <input type="hidden" name="layout" value="<?php echo JRequest::getWord('layout'); ?>" />
                <input type="hidden" name="id" value="<?php echo $id; ?>" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
                <?php echo JHtml::_('form.token'); ?>
        </div>
</form>
