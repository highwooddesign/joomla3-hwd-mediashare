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

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$saveOrder	= $listOrder == 'a.ordering';
?>
<tr>
        <th width="20">
                <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
        </th>
        <th width="1%" class="nowrap">
        </th>
        <th width="1%" class="nowrap">
        </th>
        <?php if ($this->state->get('filter.status') == 3) : ?>  
        <th width="5%">
                <?php echo JHtml::_('grid.sort',  JText::_('COM_HWDMS_REPORTS'), 'a.report_count', $listDirn, $listOrder); ?>
        </th>
        <?php endif; ?>
        <th>
                <?php echo JHtml::_('grid.sort',  JText::_('COM_HWDMS_TITLE'), 'a.title', $listDirn, $listOrder); ?>
        </th>
        <th width="5%">
                <?php echo JHtml::_('grid.sort', JText::_('JPUBLISHED'), 'a.published', $listDirn, $listOrder); ?>
        </th>
        <th width="5%">
                <?php echo JHtml::_('grid.sort', JText::_('JSTATUS'), 'a.status', $listDirn, $listOrder); ?>
        </th>
        <th width="5%">
                <?php echo JHtml::_('grid.sort', JText::_('JFEATURED'), 'a.featured', $listDirn, $listOrder, NULL, 'desc'); ?>
        </th>
        <th width="10%">
                <?php echo JText::_('JCATEGORY'); ?>
                <?php //echo JHtml::_('grid.sort', JText::_('JCATEGORY'), 'category_title', $listDirn, $listOrder); ?>
        </th>
        <th width="10%">
                <?php echo JHtml::_('grid.sort',  JText::_('JGRID_HEADING_ORDERING'), 'a.ordering', $listDirn, $listOrder); ?>
                <?php if ($saveOrder) :?>
                        <?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'media.saveorder'); ?>
                <?php endif; ?>
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
                <?php echo JHtml::_('grid.sort', JText::_('JGLOBAL_HITS'), 'a.hits', $listDirn, $listOrder); ?>
        </th>
        <th width="5%">
                <?php echo JHtml::_('grid.sort', JText::_('JGRID_HEADING_LANGUAGE'), 'language', $listDirn, $listOrder); ?>
        </th>
        <th width="1%" class="nowrap">
                <?php echo JHtml::_('grid.sort', JText::_('JGRID_HEADING_ID'), 'a.id', $listDirn, $listOrder); ?>
        </th>
</tr>

