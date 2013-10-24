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
                <?php echo JHtml::_('grid.sort',  JText::_('COM_HWDMS_TITLE'), 'a.title', $listDirn, $listOrder); ?>
        </th>
        <th width="1%">
                <?php echo JHtml::_('grid.sort',  JText::_('COM_HWDMS_LINKED'), 'connection', $listDirn, $listOrder); ?>
        </th>
        <th width="1%">
                <?php echo JText::_('COM_HWDMS_ID'); ?>
        </th>
</tr>

