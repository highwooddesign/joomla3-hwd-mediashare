<?php
/**
 * @version    SVN $Id: default_body.php 1406 2013-04-30 09:34:56Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      26-Oct-2011 10:22:53
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
        
        switch ($item->activity_type) {
            case 1:
                break;
            case 2: //new media
                $row =& JTable::getInstance('Media', 'hwdMediaShareTable');
                if ($row->load($item->element_id))
                {
                            $item->title = $row->title; 
                }
                break;
            case 3: //new album
                $row =& JTable::getInstance('Album', 'hwdMediaShareTable');
                if ($row->load($item->element_id))
                {
                            $item->title = $row->title; 
                }                                                            
                break;    
            case 4: //new group
            case 7: //joined group
            case 8: //left group
                $row =& JTable::getInstance('Group', 'hwdMediaShareTable');
                if ($row->load($item->element_id))
                {
                            $item->title = $row->title; 
                }
                break;
            case 5: //new playlist
                $row =& JTable::getInstance('Playlist', 'hwdMediaShareTable');
                if ($row->load($item->element_id))
                {
                            $item->title = $row->title; 
                }
                break;
        }
        
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
                                <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'activities.', $canCheckin); ?>
                        <?php endif; ?>
                        <span class="editlinktip hasTip" title="<?php echo $this->escape($item->title); ?>" >
                        <?php if ($canEdit) : ?>
                                <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=activity.edit&id='.(int) $item->id); ?>">
                                        <?php echo JText::sprintf(hwdMediaShareActivities::getActivityType($item), $this->escape($item->author), $this->escape($item->title)); ?>                                   
                                </a>
                        <?php else : ?>
                                <?php echo $this->getActivityType($item); ?>
                        <?php endif; ?>
                        </span>
                </td>
                <td>
                        <?php switch($item->activity_type): 
                            case 1: ?>
                                <?php echo $this->escape($item->description); ?>
                            <?php break; ?>
                            <?php case 2: ?>
                                <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=editmedia.edit&id='.(int) $item->element_id); ?>">
                                <?php echo $this->escape($item->title); ?>                                   
                                </a>
                            <?php break; ?>
                            <?php case 3: ?>
                                <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=album.edit&id='.(int) $item->element_id); ?>">
                                <?php echo $this->escape($item->title); ?>                                   
                                </a>
                            <?php break; ?>                    
                            <?php case 4: 
                                  case 7: 
                                  case 8: ?>
                                <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=group.edit&id='.(int) $item->element_id); ?>">
                                <?php echo $this->escape($item->title); ?>                                   
                                </a>
                            <?php break; ?>  
                            <?php case 5: ?>
                                <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=playlist.edit&id='.(int) $item->element_id); ?>">
                                <?php echo $this->escape($item->title); ?>                                   
                                </a>
                            <?php break; ?>  
                        <?php endswitch; ?>  
                </td>
                <td class="center">
                        <?php echo JHtml::_('jgrid.published', $item->published, $i, 'activities.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
                </td>
                <td align="center">
                        <?php echo $this->getPublish($item, 'status', $i); ?>
                </td>
                <td align="center">
                        <?php echo $this->getPublish($item, 'featured', $i); ?>
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
