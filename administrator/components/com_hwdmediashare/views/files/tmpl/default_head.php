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
        <th width="1%" class="center hidden-phone">
                <?php echo JHtml::_('grid.checkall'); ?>
        </th> 
        <th width="1%" style="min-width:55px" class="nowrap center">
		<?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
        </th>            
        <th width="10%" class="nowrap">
		<?php echo JHtml::_('searchtools.sort', 'COM_HWDMS_TITLE', 'm.title', $listDirn, $listOrder); ?>
        </th> 
        <th>
		<?php echo JHtml::_('searchtools.sort', 'COM_HWDMS_FILE_PATH', 'a.basename', $listDirn, $listOrder); ?>
        </th>         
        <th width="10%" class="nowrap hidden-phone">
		<?php echo JHtml::_('searchtools.sort', 'COM_HWDMS_FILE_TYPE', 'a.file_type', $listDirn, $listOrder); ?>
        </th>
        <th width="1%" class="nowrap hidden-phone">
		<?php echo JHtml::_('searchtools.sort', 'COM_HWDMS_EXTENSION', 'a.ext', $listDirn, $listOrder); ?>
        </th>
        <th width="7%" class="nowrap hidden-phone">
		<?php echo JHtml::_('searchtools.sort', 'COM_HWDMS_FILE_SIZE', 'a.size', $listDirn, $listOrder); ?>
        </th>
        <th width="10%" class="nowrap hidden-phone">
		<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ACCESS', 'access_level', $listDirn, $listOrder); ?>
        </th>
        <th width="10%" class="nowrap hidden-phone">
		<?php echo JHtml::_('searchtools.sort', 'JDATE', 'a.created', $listDirn, $listOrder); ?>
        </th>
        <th width="10%" class="hidden-phone">
		<?php echo JHtml::_('searchtools.sort', 'JGLOBAL_HITS', 'a.hits', $listDirn, $listOrder); ?>
        </th>
        <th width="1%" class="nowrap hidden-phone">
		<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
        </th>
</tr>
