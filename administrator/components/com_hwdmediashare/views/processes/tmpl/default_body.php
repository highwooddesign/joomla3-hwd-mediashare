<?php
/**
 * @version    SVN $Id: default_body.php 1144 2013-02-21 11:11:19Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('script','system/multiselect.js', false, true);

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$saveOrder	= $listOrder == 'a.ordering';
$canOrder	= $user->authorise('core.edit.state', 'com_hwdmediashare.category');

$ordering	= ($listOrder == 'a.ordering');
$canEdit	= $user->authorise('core.edit', 'com_hwdmediashare');
?>
<?php foreach($this->items as $i => $item):
        $canCheckin	= $user->authorise('core.manage', 'com_checkin') || $item->checked_out==$userId || $item->checked_out==0;
        $canChange	= $user->authorise('core.edit.state', 'com_hwdmediashare') && $canCheckin;
        ?>
        <tr class="row<?php echo $i % 2; ?>">
                <td>
                        <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                </td>
                <td>
                        <?php if ($canEdit) : ?>
                                <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=editmedia.edit&id='.(int) $item->media_id); ?>">
                                        <?php echo $this->escape($item->media_id); ?>
                                </a>
                        <?php else : ?>
                                        <?php echo $this->escape($item->media_id); ?>
                        <?php endif; ?>
                </td>
                <td>
                        <?php if ($item->checked_out) : ?>
                                <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'processes.', $canCheckin); ?>
                        <?php endif; ?>
                        <span class="editlinktip hasTip" title="<?php echo $this->getProcessType($item); ?>" >
                        <?php if ($canEdit) : ?>
                                <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=process.edit&id='.(int) $item->id); ?>">
                                        <?php echo $this->getProcessType($item); ?>
                                </a>
                        <?php else : ?>
                                        <?php echo $this->getProcessType($item); ?>
                        <?php endif; ?>
                        </span>
                </td>
                <td class="center">
                        <?php echo $this->getStatus($item); ?>
                </td>
                <td class="center">
                        <?php echo $this->escape($item->attempts); ?>
                </td>

                <td class="center nowrap">
                        <?php echo JHtml::_('date',$item->created, JText::_('DATE_FORMAT_LC4')); ?>
                </td>
                <td class="center">
                        <?php echo (int) $item->id; ?>
                </td>
        </tr>
<?php endforeach; ?>
