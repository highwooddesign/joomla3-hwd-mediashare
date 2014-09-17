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

JHtml::_('behavior.modal');

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
$canCreate  = $user->authorise('core.create',     'com_hwdmediashare.media');
$canEdit    = $user->authorise('core.edit',       'com_hwdmediashare.media.'.$item->id);
$canCheckin = $user->authorise('core.manage',     'com_hwdmediashare') || $item->checked_out == $userId || $item->checked_out == 0;
$canEditOwn = $user->authorise('core.edit.own',   'com_hwdmediashare.media.'.$item->id) && $item->created_user_id == $userId;
$canChange  = $user->authorise('core.edit.state', 'com_hwdmediashare.media.'.$item->id) && $canCheckin;
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
        <td class="center">
                <div class="btn-group">
                        <?php echo JHtml::_('jgrid.published', $item->published, $i, 'media.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
                        <?php echo JHtml::_('HwdAdminMedia.featured', $item->featured, $i, $canChange); ?>
                        <?php echo JHtml::_('HwdAdminMedia.status', $item->status, $i, $canChange); ?>
                        <?php
                        // Create dropdown items
                        $action = $archived ? 'unarchive' : 'archive';
                        JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'media');

                        $action = $trashed ? 'untrash' : 'trash';
                        JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'media');

                        // Render dropdown list
                        echo JHtml::_('actionsdropdown.render', $this->escape($item->title));
                        ?>                    
                </div>
        </td>
        <?php if ($this->state->get('filter.status') == 3) : ?>  
        <td class="center">
                <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=reported&layout=media&tmpl=component&id=' . $item->id); ?>" class="modal" rel="{handler: 'iframe', size: {x: 800, y: 500}}">
                        <?php echo (int) $item->report_count; ?></a>                    
        </td>
        <?php endif; ?>      
        <td class="nowrap-todo has-context-todo">
                <div class="pull-right"><span class="editlinktip hasTooltip" title="<?php echo JHtml::tooltipText($this->getMediaType($item)); ?>" ><img src="<?php echo $this->getMediaTypeIcon($item); ?>" width="24" /></span></div>
                <div class="pull-left thumb-wrapper">
                        <span class="editlinktip hasTooltip" title="<?php echo JHtml::tooltipText($item->title, JHtmlString::truncate($item->description, 160, true, false)); ?>" ><img src="<?php echo JRoute::_(hwdMediaShareThumbnails::thumbnail($item)); ?>" width="75" /></span>
                </div>
                <div class="pull-left-todo">
                        <?php if ($item->checked_out) : ?>
                                <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'media.', $canCheckin); ?>
                        <?php endif; ?>
                        <?php if ($item->language == '*'):?>
                                <?php $language = JText::alt('JALL', 'language'); ?>
                        <?php else:?>
                                <?php $language = $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
                        <?php endif;?>
                        <?php if ($canEdit || $canEditOwn) : ?>
                                <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=editmedia.edit&id=' . $item->id); ?>" title="<?php echo JText::_('JACTION_EDIT'); ?>">
                                        <?php echo $this->escape($item->title); ?></a>
                        <?php else : ?>
                                <span title="<?php echo JText::sprintf('JFIELD_ALIAS_LABEL', $this->escape($item->alias)); ?>"><?php echo $this->escape($item->title); ?></span>
                        <?php endif; ?>
                        <div class="small">
                                <?php echo JText::_('JCATEGORY') . ": "; ?><?php echo $this->getCategories($item); ?>
                        </div>
                </div>
        </td>     
        <td class="small hidden-phone">
                <?php echo $this->escape($item->access_level); ?>
        </td>
        <td class="small hidden-phone">
                <?php if ($item->created_user_id_alias) : ?>
                        <a href="<?php echo JRoute::_('index.php?option=com_users&task=user.edit&id='.(int) $item->created_user_id); ?>" title="<?php echo JText::_('JAUTHOR'); ?>">
                        <?php echo $this->escape($item->author); ?></a>
                        <p class="smallsub"> <?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->created_user_id_alias)); ?></p>
                <?php else : ?>
                        <a href="<?php echo JRoute::_('index.php?option=com_users&task=user.edit&id='.(int) $item->created_user_id); ?>" title="<?php echo JText::_('JAUTHOR'); ?>">
                        <?php echo $this->escape($item->author); ?></a>
                <?php endif; ?>
        </td>
        <td class="small hidden-phone">
                <?php if ($item->language == '*'):?>
                        <?php echo JText::alt('JALL', 'language'); ?>
                <?php else:?>
                        <?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
                <?php endif;?>
        </td>
        <td class="nowrap small hidden-phone">
                <?php echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC4')); ?>
        </td>
        <td class="center">
                <?php echo (int) $item->hits; ?>
        </td>
        <td class="center hidden-phone">
                <?php echo (int) $item->id; ?>
        </td>
</tr>
<?php endforeach; ?>
