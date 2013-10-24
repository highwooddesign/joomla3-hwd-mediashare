<?php
/**
 * @version    SVN $Id: default_body.php 1140 2013-02-21 11:09:27Z dhorsfall $
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
$canEdit	= $user->authorise('core.edit',	'com_hwdmediashare');
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
                <td>
                        <?php if ($item->checked_out) : ?>
                                <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'extensions.', $canCheckin); ?>
                        <?php endif; ?>
                        <?php if ($canEdit) : ?>
                                <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=extension.edit&id='.(int) $item->id); ?>">
                                        <?php echo $this->escape($item->ext); ?></a>
                        <?php else : ?>
                                        <?php echo $this->escape($item->ext); ?>
                        <?php endif; ?>
                </td>
                <td class="center">
                        <?php echo JHtml::_('jgrid.published', $item->published, $i, 'extensions.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
                </td>
                <td>
                        <?php echo $this->getMediaType($item); ?>
                </td>
                <td class="center">
                        <?php echo $this->escape($item->access_level); ?>
                </td>
                <td class="center">
                        <?php echo $this->escape($owner->username); ?>
                </td>
                <td>
                        <?php echo $item->id; ?>
                </td>
        </tr>
<?php endforeach; ?>
