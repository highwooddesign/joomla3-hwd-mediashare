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
$saveOrder	= $listOrder == 'a.ordering';

$archived	= $this->state->get('filter.published') == 2 ? true : false;
$trashed	= $this->state->get('filter.published') == -2 ? true : false;
?>
<?php foreach ($this->items as $i => $item) :
$ordering   = ($listOrder == 'a.ordering');
$canCreate  = $user->authorise('core.create',     'com_hwdmediashare');
$canEdit    = $user->authorise('core.edit',       'com_hwdmediashare');
$canCheckin = $user->authorise('core.manage',     'com_hwdmediashare') || $item->checked_out == $userId || $item->checked_out == 0;
$canChange  = $user->authorise('core.edit.state', 'com_hwdmediashare') && $canCheckin;
?>
<tr class="row<?php echo $i % 2; ?>">
        <td class="order nowrap center hidden-phone">
                <?php
                $iconClass = '';
                if (!$canChange)
                {
                        $iconClass = ' inactive';
                }
                elseif (!$saveOrder)
                {
                        $iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
                }
                ?>
                <span class="sortable-handler<?php echo $iconClass ?>">
                        <i class="icon-menu"></i>
                </span>
                <?php if ($canChange && $saveOrder) : ?>
                        <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
                <?php endif; ?>
        </td>
        <td class="center hidden-phone">
                <?php echo JHtml::_('grid.id', $i, $item->id); ?>
        </td>
        <?php if ($item->type == 'group') : ?>
        <td class="center">
                <div class="btn-group">
                        <?php echo JHtml::_('jgrid.published', $item->published, $i, 'customfields.', $canChange, 'cb'); ?>
                        <?php
                        // Create dropdown items
                        $action = $archived ? 'unarchive' : 'archive';
                        JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'customfields');

                        $action = $trashed ? 'untrash' : 'trash';
                        JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'customfields');

                        // Render dropdown list
                        echo JHtml::_('actionsdropdown.render', $this->escape($item->name));
                        ?>                    
                </div>
        </td>
        <td class="nowrap has-context" colspan="8">
                <div class="pull-left">
                        <?php if ($canEdit) : ?>
                                <strong><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=customfield.edit&id=' . $item->id); ?>" title="<?php echo JText::_('JACTION_EDIT'); ?>">
                                        <?php echo $this->escape($item->name); ?></a></strong>
                        <?php else : ?>
                                <span title="<?php echo JText::sprintf('JFIELD_ALIAS_LABEL', $this->escape($item->tooltip)); ?>"><?php echo $this->escape($item->name); ?></span>
                        <?php endif; ?>                              
                </div>
        </td> 
        <?php else: ?> 
        <td class="center">
                <div class="btn-group">
                        <?php echo JHtml::_('jgrid.published', $item->published, $i, 'customfields.', $canChange, 'cb'); ?>
                        <?php echo JHtml::_('HwdAdminCustomfields.searchable', $item->searchable, $i, $canChange); ?>
                        <?php echo JHtml::_('HwdAdminCustomfields.visible', $item->visible, $i, $canChange); ?>
                        <?php echo JHtml::_('HwdAdminCustomfields.required', $item->required, $i, $canChange); ?>
                        <?php
                        // Create dropdown items
                        $action = $archived ? 'unarchive' : 'archive';
                        JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'customfields');

                        $action = $trashed ? 'untrash' : 'trash';
                        JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'customfields');

                        // Render dropdown list
                        echo JHtml::_('actionsdropdown.render', $this->escape($item->name));
                        ?>                    
                </div>
        </td>
        <td class="nowrap has-context">
                <div class="pull-left">
                        <?php if ($canEdit) : ?>
                                <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=customfield.edit&id=' . $item->id); ?>" title="<?php echo JText::_('JACTION_EDIT'); ?>">
                                        <?php echo $this->escape($item->name); ?></a>
                        <?php else : ?>
                                <span title="<?php echo JText::sprintf('JFIELD_ALIAS_LABEL', $this->escape($item->tooltip)); ?>"><?php echo $this->escape($item->name); ?></span>
                        <?php endif; ?>
                        <div class="small">
                                <?php echo $this->escape($item->tooltip); ?>
                        </div>                                
                </div>
        </td>     
        <td class="nowrap small hidden-phone">
                <?php echo $item->fieldcode; ?>
        </td>
        <td class="small hidden-phone">
                <?php echo $this->getFieldText($item->type); ?>
        </td>
        <td class="small hidden-phone">
                <?php echo $this->getElementText($item->element_type); ?>
        </td>  
        <td class="center hidden-phone">
                <?php echo (int) $item->id; ?>
        </td>
        <?php endif; ?>
</tr>
<?php endforeach; ?>
