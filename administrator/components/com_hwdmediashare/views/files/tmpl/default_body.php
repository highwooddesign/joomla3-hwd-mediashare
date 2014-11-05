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
        <td class="center">
                <div class="btn-group">
                        <?php echo JHtml::_('jgrid.published', $item->published, $i, 'files.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>                  
                </div>
        </td>        
        <td class="nowrap">
                <div class="pull-left">
                        <?php if ($canEdit) : ?>
                                <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=editmedia.edit&id=' . $item->element->id . '&return=' . $this->return); ?>" title="<?php echo JText::_('JACTION_EDIT'); ?>">
                                        <?php echo $this->escape(JHtml::_('string.truncate', $item->element->title, 20, false, false)); ?></a>
                        <?php else : ?>
                                <span title="<?php echo JText::sprintf('JFIELD_ALIAS_LABEL', $this->escape($item->element->alias)); ?>"><?php echo $this->escape($item->element->title); ?></span>
                        <?php endif; ?> 
                        <div class="small">
                                <?php echo hwdMediaShareUtilities::getElementType($item); ?>
                        </div>                                
                </div>
        </td>  
        <td class="has-context">
                <a href="<?php echo hwdMediaShareDownloads::protectedUrl($item->element_id, $item->file_type, $item->element_type, 1); ?>"><?php echo $this->getPath($item->element, $item); ?></a>
        </td>        
        <td class="small hidden-phone">
                <?php echo hwdMediaShareFiles::getFileType($item); ?>
        </td>
        <td class="small hidden-phone">
                <?php echo hwdMediaShareFiles::getExtension($item->element, $item->file_type); ?>
        </td>
        <td class="nowrap small hidden-phone">
                <?php echo JHtml::_('number.bytes', $item->size); ?>
        </td>
        <td class="small hidden-phone">
                <?php echo $this->escape($item->access_level); ?>
        </td>
        <td class="nowrap small hidden-phone">
                <?php echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC4')); ?>
        </td>
        <td class="center hidden-phone">
                <?php echo (int) $item->hits; ?>
        </td>
        <td class="center hidden-phone">
                <?php echo (int) $item->id; ?>
        </td>
</tr>
<?php endforeach; ?>
