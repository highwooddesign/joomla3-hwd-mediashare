<?php
/**
 * @version    SVN $Id: default_head.php 953 2013-01-29 13:45:49Z dhorsfall $
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
$canOrder	= $user->authorise('core.edit.state', 'com_hwdmediashare.group');
$saveOrder	= $listOrder == 'a.ordering';
?>
<tr>
        <th width="20">
                <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
        </th>
        <th width="1%" class="nowrap">
                <?php echo JHtml::_('grid.sort', JText::_('COM_HWDMS_MEDIA_ID'), 'a.media_id', $listDirn, $listOrder); ?>
        </th>        
        <th>
                <?php echo JHtml::_('grid.sort',  JText::_('COM_HWDMS_PROCESS'), 'a.process_type', $listDirn, $listOrder); ?>
        </th>
        <th width="5%">
                <?php echo JHtml::_('grid.sort', JText::_('JSTATUS'), 'a.status', $listDirn, $listOrder); ?>
        </th>
        <th width="5%">
                <?php echo JHtml::_('grid.sort', JText::_('COM_HWDMS_ATTEMPTS'), 'a.attempts', $listDirn, $listOrder, NULL, 'desc'); ?>
        </th>
        <th width="5%">
                <?php echo JHtml::_('grid.sort', JText::_('JDATE'), 'a.created', $listDirn, $listOrder); ?>
        </th>
        <th width="1%" class="nowrap">
                <?php echo JHtml::_('grid.sort', JText::_('JGRID_HEADING_ID'), 'a.id', $listDirn, $listOrder); ?>
        </th>
</tr>

