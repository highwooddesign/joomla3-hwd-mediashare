<?php
/**
 * @version    SVN $Id: default_head.php 277 2012-03-28 10:03:31Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canOrder	= $user->authorise('core.edit.state', 'com_hwdmediashare.customfield');
$saveOrder	= $listOrder == 'a.ordering';
?>
<tr>
        <th width="1%">
                <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" />
        </th>
        <th>
                <?php echo JHtml::_('grid.sort', 'COM_HWDMS_NAME', 'a.name', $listDirn, $listOrder); ?>
        </th>
        <th width="10%">
                <?php echo JHtml::_('grid.sort', 'COM_HWDMS_FIELD_CODE', 'a.fieldcode', $listDirn, $listOrder); ?>
        </th>
        <th width="10%">
                <?php echo JHtml::_('grid.sort', 'COM_HWDMS_TYPE', 'a.type', $listDirn, $listOrder); ?>
        </th>
        <th width="10%">
                <?php echo JHtml::_('grid.sort', 'COM_HWDMS_ELEMENT', 'a.element', $listDirn, $listOrder); ?>
        </th>
        <th width="1%">
                <?php echo JHtml::_('grid.sort', 'COM_HWDMS_PUBLISHED', 'a.published', $listDirn, $listOrder); ?>
        </th>
        <th width="1%">
                <?php echo JHtml::_('grid.sort', 'COM_HWDMS_SEARCHABLE', 'a.searchable', $listDirn, $listOrder); ?>
        </th>
        <th width="1%">
                <?php echo JHtml::_('grid.sort', 'COM_HWDMS_VISIBLE', 'a.visible', $listDirn, $listOrder); ?>
        </th>
        <th width="1%">
                <?php echo JHtml::_('grid.sort', 'COM_HWDMS_REQUIRED', 'a.required', $listDirn, $listOrder); ?>
        </th>
        <th width="10%">
                <?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ORDERING', 'a.ordering', $listDirn, $listOrder); ?>
                <?php if ($saveOrder) :?>
                        <?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'customfields.saveorder'); ?>
                <?php endif; ?>
        </th>
        <th width="1%">
                <?php echo JHtml::_('grid.sort', 'COM_HWDMS_ID', 'a.id', $listDirn, $listOrder); ?>
        </th>
</tr>

