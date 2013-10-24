<?php
/**
 * @version    SVN $Id: default_body.php 706 2012-10-26 09:02:39Z dhorsfall $
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
$user		= JFactory::getUser();
$userId		= $user->get('id');

$count          = 0;
$i		= 0;
?>
<?php foreach($this->items as $i => $item) :
$input          = JHtml::_('grid.id', $count, $item->id);
$ordering	= ($listOrder == 'a.ordering');
$canCreate	= $user->authorise('core.create', 'com_hwdmediashare.category');
$canEdit	= $user->authorise('core.edit', 'com_hwdmediashare.category');
$canCheckin	= $user->authorise('core.manage', 'com_checkin') || $item->checked_out==$user->get('id') || $item->checked_out==0;
$canChange	= $user->authorise('core.edit.state', 'com_hwdmediashare.category') && $canCheckin; ?>

        <?php if ($item->type == 'group') : ?>
        <tr>
                <td  style="background-color: #EEEEEE;">
                        <?php echo $input; ?>
                </td>
                <td colspan="3" style="background-color: #EEEEEE;">
                        <strong>
                                <span id="name<?php echo $item->id; ?>">
                                <?php if ($canEdit) : ?>
                                        <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=customfield.edit&id='.(int) $item->id); ?>">
                                                <?php echo $this->escape($item->name); ?>
                                        </a>
                                <?php else : ?>
                                        <?php echo $this->escape($item->name); ?>
                                <?php endif; ?>
                                </span>
                        </strong>
                        <div style="clear: both;"></div>
                </td>
                <td align="center">
                        <?php echo $this->getElementText($item->element_type); ?>
                </td>
                <td align="center" id="published<?php echo $item->id;?>" style="background-color: #EEEEEE;">
                        <?php echo JHtml::_('jgrid.published', $item->published, $count, 'customfields.', $canChange, 'cb'); ?>
                        <?php //echo $this->getPublish($item, 'published', $count); ?>
                </td>
                <td align="center" id="searchable<?php echo $item->id;?>" style="background-color: #eee;">
                        <?php echo $this->getPublish( $item, 'searchable', $count); ?>
                </td>
                <td align="center" id="visible<?php echo $item->id;?>" style="background-color: #EEEEEE;">
                        <?php echo $this->getPublish($item, 'visible', $count); ?>
                </td>
                <td align="center" id="required<?php echo $item->id;?>" style="background-color: #EEEEEE;">
                        <?php echo $this->getPublish($item, 'required', $count); ?>
                </td>
                <td class="order">
                        <?php if ($canChange) : ?>
                                <?php if ($saveOrder) :?>
                                        <?php if ($listDirn == 'asc') : ?>
                                                <span><?php echo $this->pagination->orderUpIcon($count, ($item->element_type== @$this->items[$count-1]->element_type), 'customfields.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                                                <span><?php echo $this->pagination->orderDownIcon($count, $this->pagination->total, ($item->element_type == @$this->items[$count+1]->element_type), 'customfields.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                                        <?php elseif ($listDirn == 'desc') : ?>
                                                <span><?php echo $this->pagination->orderUpIcon($count, ($item->element_type == @$this->items[$count-1]->element_type), 'customfields.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                                                <span><?php echo $this->pagination->orderDownIcon($count, $this->pagination->total, ($item->element_type == @$this->items[$count+1]->element_type), 'customfields.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                                        <?php endif; ?>
                                <?php endif; ?>
                                <?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
                                <input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
                        <?php else : ?>
                                <?php echo $item->ordering; ?>
                        <?php endif; ?>
                </td>
                <td  style="background-color: #EEEEEE;">&nbsp;</td>
        </tr>
        <?php $i = 0; // Reset count ?>
        <?php elseif ($item->type != 'group') : ?>
        <?php ++$i; ?>
        <tr class="row<?php echo $i%2;?>" id="rowid<?php echo $item->id;?>">
                <td>
                        <?php echo $input; ?>
                </td>
                <td>
                        <span class="editlinktip hasTip" title="<?php echo $this->escape( $item->name ); ?>::<?php echo $item->tooltip; ?>" id="name<?php echo $item->id;?>">
                        <?php if ($canEdit) : ?>
                                <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=customfield.edit&id='.(int) $item->id); ?>">
                                        <?php echo $this->escape($item->name); ?>
                                </a>
                        <?php else : ?>
                                <?php echo $this->escape($item->name); ?>
                        <?php endif; ?>
                        </span>
                </td>
                <td align="center">
                        <?php echo $item->fieldcode; ?>
                </td>
                <td align="center">
                        <span id="type<?php echo $item->id;?>" onclick="$('typeOption').style.display = 'block';$(this).style.display = 'none';">
                        <?php echo $this->getFieldText( $item->type ); ?>
                        </span>
                </td>
                <td align="center">
                        <?php echo $this->getElementText($item->element_type); ?>
                </td>
                <td align="center" id="published<?php echo $item->id;?>">
                        <?php echo JHtml::_('jgrid.published', $item->published, $count, 'customfields.', $canChange, 'cb'); ?>
                        <?php //echo $this->getPublish($item, 'published', $count); ?>
                </td>
                <td align="center" id="searchable<?php echo $item->id;?>">
                        <?php echo $this->getPublish( $item, 'searchable', $count); ?>
                </td>
                <td align="center" id="visible<?php echo $item->id;?>">
                        <?php echo $this->getPublish($item, 'visible', $count); ?>
                </td>
                <td align="center" id="required<?php echo $item->id;?>">
                        <?php echo $this->getPublish($item, 'required', $count); ?>
                </td>
                <td class="order">
                        <?php if ($canChange) : ?>
                                <?php if ($saveOrder) :?>
                                        <?php if ($listDirn == 'asc') : ?>
                                                <span><?php echo $this->pagination->orderUpIcon($count, ($item->element_type == @$this->items[$count-1]->element_type), 'customfields.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                                                <span><?php echo $this->pagination->orderDownIcon($count, $this->pagination->total, ($item->element_type == @$this->items[$count+1]->element_type), 'customfields.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                                        <?php elseif ($listDirn == 'desc') : ?>
                                                <span><?php echo $this->pagination->orderUpIcon($count, ($item->element_type == @$this->items[$count-1]->element_type), 'customfields.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                                                <span><?php echo $this->pagination->orderDownIcon($count, $this->pagination->total, ($item->element_type == @$this->items[$count+1]->element_type), 'customfields.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                                        <?php endif; ?>
                                <?php endif; ?>
                                <?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
                                <input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
                        <?php else : ?>
                                <?php echo $item->ordering; ?>
                        <?php endif; ?>
                </td>
                <td><?php echo $i;?></td>
        </tr>
        <?php endif; ?>
        <?php $count++; ?>
<?php endforeach;
