<?php
/**
 * @version    SVN $Id: default_body.php 446 2012-07-27 10:08:18Z dhorsfall $
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
$canEdit	= $user->authorise('core.edit',	'com_hwdmediashare');
?>
<?php foreach($this->items as $i => $item):
        $canCheckin	= $user->authorise('core.manage', 'com_checkin') || $item->checked_out==$userId || $item->checked_out==0;
        $canChange	= $user->authorise('core.edit.state', 'com_hwdmediashare') && $canCheckin;

        ?>
        <?php if ($item->created) : ?>
                <tr class="row<?php echo $i % 2; ?>">
                        <td>
                                <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                        </td>
                        <td align="center">
                                <?php echo $this->getPublish($item, 'created', $i); ?>
                        </td>
                        <?php if ($this->state->get('filter.status') == 3) : ?>  
                        <td>
                                <?php echo (int) $item->report_count; ?>
                        </td>
                        <?php endif; ?>
                        <td>
                                <?php if ($item->checked_out) : ?>
                                        <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'users.', $canCheckin); ?>
                                <?php endif; ?>
                                <span class="editlinktip hasTip" title="<?php echo $this->escape($item->name); ?>::<?php echo $this->escape($item->alias); ?>" >
                                <?php if ($canEdit) : ?>
                                <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=user.edit&id='.(int) $item->id); ?>" title="<?php echo JText::sprintf('COM_USERS_EDIT_USER', $this->escape($item->name)); ?>">
                                        <?php echo $this->escape($item->name); ?></a>
                                <?php else : ?>
                                        <?php echo $this->escape($item->name); ?>
                                <?php endif; ?>
                                <p class="smallsub">
                                        <?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?>
                                </p>
                        </span>

                        </td>
                        <td class="center">
                                <?php echo JHtml::_('jgrid.published', $item->published, $i, 'users.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
                        </td>
                        <td align="center">
                                <?php echo $this->getPublish($item, 'status', $i); ?>
                        </td>
                        <td align="center">
                                <?php echo $this->getPublish($item, 'featured', $i); ?>
                        </td>
                        <td class="center">
                                <?php echo $this->escape($item->access_level); ?>
                        </td>
                        <td class="center">
                                <?php echo $this->escape($item->username); ?>
                        </td>
                        <td class="center nowrap">
                                <?php echo JHtml::_('date',$item->created, JText::_('DATE_FORMAT_LC4')); ?>
                        </td>
                        <td class="center">
                                <?php echo (int) $item->hits; ?>
                        </td>
                        <td class="center">
                                <?php if ($item->language=='*'):?>
                                        <?php echo JText::alt('JALL','language'); ?>
                                <?php else:?>
                                        <?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
                                <?php endif;?>
                        </td>
                        <td class="center">
                                <?php echo (int) $item->id; ?>
                        </td>
                </tr>
        <?php else : ?>
                <tr class="row<?php echo $i % 2; ?>">
                        <td>
                                <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                        </td>
                        <td align="center">
                                <?php echo $this->getPublish($item, 'created', $i); ?>
                        </td>
                        <td>
                                <?php if ($canEdit) : ?>
                                        <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=user.edit&id='.(int) $item->id); ?>" title="<?php echo JText::sprintf('COM_USERS_EDIT_USER', $this->escape($item->name)); ?>">
                                                <?php echo $this->escape($item->name); ?>
                                        </a>
                                <?php else : ?>
                                        <?php echo $this->escape($item->name); ?>
                                <?php endif; ?>
                        </td>
                        <td class="center">-</td>
                        <td align="center">-</td>
                        <td align="center">-</td>
                        <td class="center">-</td>
                        <td class="center">-</td>
                        <td class="center">-</td>
                        <td class="center">-</td>
                        <td class="center">-</td>
                        <td class="center">
                                <?php echo (int) $item->id; ?>
                        </td>
                </tr>
        <?php endif; ?>
<?php endforeach; ?>
