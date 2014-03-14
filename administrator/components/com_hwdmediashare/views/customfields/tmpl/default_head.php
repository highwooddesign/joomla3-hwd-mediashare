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

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>
<tr>
        <th width="1%" class="nowrap center hidden-phone">
		<?php echo JHtml::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
        </th>
        <th width="1%" class="hidden-phone">
                <?php echo JHtml::_('grid.checkall'); ?>
        </th>
        <th width="1%" style="min-width:55px" class="nowrap center">
		<?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
        </th>    
        <th>
		<?php echo JHtml::_('searchtools.sort', 'COM_HWDMS_NAME', 'a.title', $listDirn, $listOrder); ?>
        </th>     
        <th width="10%" class="nowrap hidden-phone">
		<?php echo JHtml::_('searchtools.sort', 'COM_HWDMS_FIELD_CODE', 'a.fieldcode', $listDirn, $listOrder); ?>
        </th>
        <th width="10%" class="nowrap hidden-phone">
		<?php echo JHtml::_('searchtools.sort', 'COM_HWDMS_TYPE', 'a.type', $listDirn, $listOrder); ?>
        </th>
        <th width="10%" class="nowrap hidden-phone">
		<?php echo JHtml::_('searchtools.sort', 'COM_HWDMS_ELEMENT', 'a.element', $listDirn, $listOrder); ?>
        </th>
        <th width="1%" class="nowrap hidden-phone">
		<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
        </th>
</tr>
