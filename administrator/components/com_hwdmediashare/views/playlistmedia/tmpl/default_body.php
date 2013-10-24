<?php
/**
 * @version    SVN $Id: default_body.php 425 2012-06-28 07:48:57Z dhorsfall $
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
$saveOrder	= $listOrder == 'map.ordering';
$ordering	= ($listOrder == 'map.ordering');

$user		= JFactory::getUser();
?>
<?php foreach($this->items as $i => $item):
        $canCheckin	= $user->authorise('core.manage', 'com_checkin') || $item->checked_out==$user->get('id') || $item->checked_out==0;
        $canChange	= $user->authorise('core.edit.state', 'com_hwdmediashare') && $canCheckin;
        $canEdit        = $user->authorise('core.edit', 'com_hwdmediashare.'.$this->view_type);
        ?>
        <tr class="row<?php echo $i % 2; ?>">
                <td>
                        <?php echo $item->id; ?>
                </td>
                <td>
                        <?php if ($saveOrder) :?>
                                <?php echo JHtml::_('grid.id', $i, $item->mapid); ?>
                        <?php else : ?>
                                <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                        <?php endif; ?>
                </td>
                <td>
                        <?php if ($canEdit) : ?>
                                <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task='.$this->view_edit.'.edit&id='.(int)$item->id); ?>" target="_top">
                                        <?php echo $this->escape($item->title); ?>
                                </a>
                        <?php else : ?>
                                <?php echo $this->escape($item->title); ?>
                        <?php endif; ?>
                        <p class="smallsub">
                                <?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?>
                        </p>
                </td>
                <?php if (!$this->viewAll) : ?>
                <td class="order">
                        <?php if ($canChange) : ?>
                                <?php if ($saveOrder) :?>
                                        <?php if ($listDirn == 'asc') : ?>
                                                <span><?php echo $this->pagination->orderUpIcon($i, ($item->playlist_id == @$this->items[$i-1]->playlist_id), 'playlistmedia.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                                                <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, ($item->playlist_id == @$this->items[$i+1]->playlist_id), 'playlistmedia.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                                        <?php elseif ($listDirn == 'desc') : ?>
                                                <span><?php echo $this->pagination->orderUpIcon($i, ($item->playlist_id == @$this->items[$i-1]->playlist_id), 'playlistmedia.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                                                <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, ($item->playlist_id == @$this->items[$i+1]->playlist_id), 'playlistmedia.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                                        <?php endif; ?>
                                <?php endif; ?>
                                <?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
                                <input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
                        <?php else : ?>
                                <?php echo $item->ordering; ?>
                        <?php endif; ?>
                </td>
                <?php endif; ?>
                <?php if ($this->viewAll || !$saveOrder) :?>
                <td>
                        <?php echo $this->getConnection($item, $i); ?>
                </td>
                <?php endif; ?>
        </tr>
<?php endforeach; ?>
