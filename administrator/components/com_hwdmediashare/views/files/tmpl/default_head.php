<?php
/**
 * @version    SVN $Id: default_head.php 953 2013-01-29 13:45:49Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Mar-2012 21:28:45
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canOrder	= $user->authorise('core.edit.state', 'com_hwdmediashare.group');
$saveOrder	= $listOrder == 'a.ordering';
?>
<tr>
        <th width="20">
                <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
        </th>
        <th>
                <?php echo JHtml::_('grid.sort', JText::_('COM_HWDMS_TITLE'), 'm.title', $listDirn, $listOrder); ?>
        </th>
        <th width="10%">
                <?php echo JHtml::_('grid.sort', JText::_('COM_HWDMS_FILE_TYPE'), 'a.file_type', $listDirn, $listOrder); ?>
        </th>
        <th>
                <?php echo JHtml::_('grid.sort', JText::_('COM_HWDMS_FILE_PATH'), 'a.basename', $listDirn, $listOrder); ?>
        </th>
        <th width="5%">
                <?php echo JHtml::_('grid.sort', JText::_('COM_HWDMS_EXTENSION'), 'a.ext', $listDirn, $listOrder); ?>
        </th>
        <th width="10%">
                <?php echo JHtml::_('grid.sort', JText::_('COM_HWDMS_FILE_SIZE'), 'a.size', $listDirn, $listOrder, NULL, 'desc'); ?>
        </th>
        <th width="10%">
                <?php echo JHtml::_('grid.sort', JText::_('COM_HWDMS_DOWNLOAD'), 'download_level', $listDirn, $listOrder); ?>
        </th>
        <th width="5%">
                <?php echo JHtml::_('grid.sort', JText::_('JDATE'), 'a.created', $listDirn, $listOrder); ?>
        </th>
        <th width="5%">
                <?php echo JHtml::_('grid.sort', JText::_('JGLOBAL_HITS'), 'a.hits', $listDirn, $listOrder); ?>
        </th>
        <th width="1%" class="nowrap">
                <?php echo JHtml::_('grid.sort', JText::_('JGRID_HEADING_ID'), 'a.id', $listDirn, $listOrder); ?>
        </th>
</tr>

