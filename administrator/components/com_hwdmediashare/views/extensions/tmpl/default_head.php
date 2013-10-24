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
?>
<tr>
        <th width="1%">
                <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
        </th>
        <th>
                <?php echo JHtml::_('grid.sort', 'COM_HWDMS_FILE_EXTENSION', 'ext', $listDirn, $listOrder); ?>
        </th>
        <th width="5%">
                <?php echo JHtml::_('grid.sort', JText::_('JPUBLISHED'), 'a.published', $listDirn, $listOrder); ?>
        </th>
        <th width="5%">
                <?php echo JHtml::_('grid.sort', 'COM_HWDMS_MEDIA', 'media_type', $listDirn, $listOrder); ?>
        </th>
        <th width="10%">
                <?php echo JHtml::_('grid.sort', JText::_('JGRID_HEADING_ACCESS'), 'access_level', $listDirn, $listOrder); ?>
        </th>
        <th width="10%">
                <?php echo JHtml::_('grid.sort', JText::_('JGRID_HEADING_CREATED_BY'), 'a.created_user_id', $listDirn, $listOrder); ?>
        </th>
        <th width="1%">
                <?php echo JHtml::_('grid.sort', JText::_('JGRID_HEADING_ID'), 'a.id', $listDirn, $listOrder); ?>
        </th>
</tr>

