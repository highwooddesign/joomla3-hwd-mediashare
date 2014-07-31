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
$canChange  = $user->authorise('core.edit.state', 'com_hwdmediashare') && $canCheckin;
?>
<tr class="row<?php echo $i % 2; ?>">
        <td class="center hidden-phone">
                <?php echo JHtml::_('grid.id', $i, $item->id); ?>
        </td>
        <td class="nowrap has-context">
                <div class="pull-left">
                        <?php if ($canEdit) : ?>
                                <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=subscription.edit&id=' . $item->id); ?>" title="<?php echo JText::_('JACTION_EDIT'); ?>">
                                        <?php echo $this->escape($item->element_id); ?></a>
                        <?php else : ?>
                                <span title="<?php echo JText::sprintf('JFIELD_ALIAS_LABEL', $this->escape($item->alias)); ?>"><?php echo $this->escape($item->element_id); ?></span>
                        <?php endif; ?>
                        <div class="small">
                                <?php echo hwdMediaShareUtilities::getElementType($item); ?>
                        </div>                                
                </div>
        </td>     
        <td class="small">
                <a href="<?php echo JRoute::_('index.php?option=com_users&task=user.edit&id='.(int) $item->user_id); ?>" title="<?php echo JText::_('JAUTHOR'); ?>">
                <?php echo $this->escape($item->name); ?></a>
                <p class="smallsub"><?php echo $this->escape($item->username); ?></p>  
        </td>
        <td class="nowrap small hidden-phone">
                <?php echo JHtml::_('date.relative', $item->created); ?>
        </td>
        <td class="center hidden-phone">
                <?php echo (int) $item->id; ?>
        </td>
</tr>
<?php endforeach; ?>
