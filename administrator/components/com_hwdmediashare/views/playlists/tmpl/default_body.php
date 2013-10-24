<?php
/**
 * @version    SVN $Id: default_body.php 1265 2013-03-13 13:53:35Z dhorsfall $
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

        $owner =& JFactory::getUser($item->created_user_id);
        ?>
        <tr class="row<?php echo $i % 2; ?>">
                <td>
                        <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                </td>
                <?php if ($this->state->get('filter.status') == 3) : ?>  
                <td>
                        <?php echo (int) $item->report_count; ?>
                </td>
                <?php endif; ?>
                <td>
                        <?php if ($item->checked_out) : ?>
                                <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'playlists.', $canCheckin); ?>
                        <?php endif; ?>
                        <span class="editlinktip hasTip" title="<?php echo $this->escape($item->title); ?>::<?php echo $this->escape($item->description); ?>" >
                        <?php if ($canEdit) : ?>
                                <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=playlist.edit&id='.(int) $item->id); ?>">
                                        <?php echo $this->escape($item->title); ?></a>
                        <?php else : ?>
                                        <?php echo $this->escape($item->title); ?>
                        <?php endif; ?>
                        <p class="smallsub">
                                <?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?>
                        </p>
                        </span>
                </td>
                <td class="center">
                        <?php echo JHtml::_('jgrid.published', $item->published, $i, 'playlists.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
                </td>
                <td align="center">
                        <?php echo $this->getPublish($item, 'status', $i); ?>
                </td>
                <td align="center">
                        <?php echo $this->getPublish($item, 'featured', $i); ?>
                </td>
                <td class="order">
                        <?php if ($canChange) : ?>
                                <?php if ($saveOrder) :?>
                                        <?php if ($listDirn == 'asc') : ?>
                                                <span><?php echo $this->pagination->orderUpIcon($i, true, 'playlist.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                                                <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'media.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                                        <?php elseif ($listDirn == 'desc') : ?>
                                                <span><?php echo $this->pagination->orderUpIcon($i, true, 'playlist.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                                                <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'media.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                                        <?php endif; ?>
                                <?php endif; ?>
                                <?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
                                <input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
                        <?php else : ?>
                                <?php echo $item->ordering; ?>
                        <?php endif; ?>
                </td>
                <td class="center">
                        <?php echo $this->escape($item->access_level); ?>
                </td>
                <td class="center">
                        <?php echo $this->escape($owner->username); ?>
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
<?php endforeach; ?>
