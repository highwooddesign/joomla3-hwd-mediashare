<?php
/**
 * @package     Joomla.administrator
 * @subpackage  Component.hwdmediashare
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>
<?php foreach ($this->items as $i => $item) :
$ordering   = ($listOrder == 'a.ordering');
$canCreate  = $user->authorise('core.create',     'com_hwdmediashare');
$canEdit    = $user->authorise('core.edit',       'com_hwdmediashare');
$canCheckin = $user->authorise('core.manage',     'com_hwdmediashare') || $item->checked_out == $userId || $item->checked_out == 0;
$canEditOwn = $user->authorise('core.edit.own',   'com_hwdmediashare') && $item->created_user_id == $userId;
$canChange  = $user->authorise('core.edit.state', 'com_hwdmediashare') && $canCheckin;
?>
<tr class="row<?php echo $i % 2; ?>">
        <td class="center hidden-phone">
                <?php echo JHtml::_('grid.id', $i, $item->id); ?>
        </td>
        <td class="center">
                <?php echo $this->getStatus($item); ?>
        </td>        
        <td class="nowrap has-context">
                <div class="pull-left">
                        <?php if ($item->checked_out) : ?>
                                <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'processes.', $canCheckin); ?>
                        <?php endif; ?>
                        <?php if ($canEdit) : ?>
                                <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=process.edit&id=' . $item->id); ?>" title="<?php echo JText::_('JACTION_EDIT'); ?>">
                                        <?php echo hwdMediaShareProcesses::getType($item); ?></a>
                        <?php else : ?>
                                <?php echo hwdMediaShareProcesses::getType($item); ?>
                        <?php endif; ?>   
                        <div class="small">
                                <?php echo JText::sprintf('COM_HWDMS_FOR_MEDIA_N', '<a href="' . JRoute::_('index.php?option=com_hwdmediashare&task=editmedia.edit&id='.(int) $item->media_id . '&return=' . $this->return) . '">' . $this->escape($item->title) . '</a>'); ?>
                        </div>  
                </div>
        </td>     
        <td class="nowrap small hidden-phone">
                <?php echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC4')); ?>
        </td>
        <td class="center hidden-phone">
                <?php echo (int) $item->attempts; ?>
        </td>
        <td class="center hidden-phone">
                <?php echo (int) $item->id; ?>
        </td>
</tr>
<?php endforeach; ?>
