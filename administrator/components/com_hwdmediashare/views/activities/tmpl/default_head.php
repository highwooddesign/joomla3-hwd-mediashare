<?php
/**
 * @version    SVN $Id: default_head.php 953 2013-01-29 13:45:49Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      26-Oct-2011 10:22:53
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canOrder	= $user->authorise('core.edit.state', 'com_hwdmediashare.activity');
$saveOrder	= $listOrder == 'a.ordering';
?>
<tr>
        <th width="1%">
                <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
        </th>
        <?php if ($this->state->get('filter.status') == 3) : ?>  
        <th width="5%">
                <?php echo JHtml::_('grid.sort',  JText::_('COM_HWDMS_REPORTS'), 'a.report_count', $listDirn, $listOrder); ?>
        </th>
        <?php endif; ?>
        <th width="10%">
                <?php echo JHtml::_('grid.sort',  JText::_('COM_HWDMS_ACTIVITY'), 'a.activity_type', $listDirn, $listOrder); ?>
        </th>
        <th>
                <?php echo JHtml::_('grid.sort',  JText::_('COM_HWDMS_COMMENT'), 'a.description', $listDirn, $listOrder); ?>
        </th>
        <th width="1%">
                <?php echo JHtml::_('grid.sort', JText::_('JPUBLISHED'), 'a.published', $listDirn, $listOrder); ?>
        </th>
        <th width="1%">
                <?php echo JHtml::_('grid.sort', JText::_('JSTATUS'), 'a.status', $listDirn, $listOrder); ?>
        </th>
        <th width="1%">
                <?php echo JHtml::_('grid.sort', JText::_('JFEATURED'), 'a.featured', $listDirn, $listOrder, NULL, 'desc'); ?>
        </th>
        <th width="10%">
                <?php echo JHtml::_('grid.sort', JText::_('JGRID_HEADING_ACCESS'), 'access_level', $listDirn, $listOrder); ?>
        </th>
        <th width="10%">
                <?php echo JHtml::_('grid.sort', JText::_('JGRID_HEADING_CREATED_BY'), 'a.created_user_id', $listDirn, $listOrder); ?>
        </th>
        <th width="5%">
                <?php echo JHtml::_('grid.sort', JText::_('JDATE'), 'a.created', $listDirn, $listOrder); ?>
        </th>
        <th width="5%">
                <?php echo JHtml::_('grid.sort', JText::_('JGRID_HEADING_LANGUAGE'), 'language', $listDirn, $listOrder); ?>
        </th>
        <th width="1%" class="nowrap">
                <?php echo JHtml::_('grid.sort', JText::_('JGRID_HEADING_ID'), 'a.id', $listDirn, $listOrder); ?>
        </th>
</tr>

