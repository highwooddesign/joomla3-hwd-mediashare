<?php
/**
 * @version    SVN $Id: default_body.php 425 2012-06-28 07:48:57Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Mar-2012 21:29:13
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');
JHtml::_('script','system/multiselect.js', false, true);

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

$ordering	= ($listOrder == 'a.ordering');
$canEdit	= $user->authorise('core.edit', 'com_hwdmediashare');
?>
<?php foreach($this->items as $i => $item):
        $canCheckin	= $user->authorise('core.manage', 'com_checkin') || $item->checked_out==$user->get('id') || $item->checked_out==0;
        $canChange	= $user->authorise('core.edit.state', 'com_hwdmediashare') && $canCheckin;
        ?>
        <tr class="row<?php echo $i % 2; ?>">
                <td>
                        <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                </td>
                <td>
                        <?php if ($canEdit) : ?>
                                <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=editmedia.edit&id='.(int) $item->mediaid); ?>">
                                        <?php echo $this->escape($item->title); ?>
                                </a>
                        <?php else : ?>
                                        <?php echo $this->escape($item->title); ?>
                        <?php endif; ?>
                </td>
                <td>
                        <a href="<?php echo 'index.php?option=com_hwdmediashare&task=editmedia.view&id='.$item->element_id.'&file_type='.$item->file_type.'&tmpl=component'; ?>" class="modal" rel="{handler: 'iframe', size: {x: 500, y: 400}}" ><?php echo $this->getFileType($item); ?></a>
                </td>
                <td>
                        <?php echo $this->getPath($item); ?>
                </td>
                <td align="center">
                        <?php echo $this->getExtension($item); ?>
                </td>
                <td align="center">
                        <?php echo JHtml::_('number.bytes', $item->size); ?>
                </td>
                <td class="center">
                        <?php echo $this->escape($item->download_level); ?>
                </td>
                <td class="center">
                        <?php echo JHtml::_('date',$item->created, JText::_('DATE_FORMAT_LC4')); ?>
                </td>
                <td class="center">
                        <?php echo (int) $item->hits; ?>
                </td>
                <td class="center">
                        <?php echo (int) $item->id; ?>
                </td>
        </tr>
<?php endforeach; ?>
