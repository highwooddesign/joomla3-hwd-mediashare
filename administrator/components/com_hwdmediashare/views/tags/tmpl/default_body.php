<?php
/**
 * @version    SVN $Id: default_body.php 277 2012-03-28 10:03:31Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
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
$canOrder	= $user->authorise('core.edit.state', 'com_hwdmediashare.category');
$saveOrder	= $listOrder == 'a.ordering';
?>

<?php foreach($this->items as $i => $item):
        $ordering	= ($listOrder == 'a.ordering');
        $canCreate	= $user->authorise('core.create',		'com_hwdmediashare.category');
        $canEdit	= $user->authorise('core.edit',			'com_hwdmediashare.category');
        $canCheckin	= $user->authorise('core.manage',		'com_checkin') || $item->checked_out==$user->get('id') || $item->checked_out==0;
        $canChange	= $user->authorise('core.edit.state',	'com_hwdmediashare.category') && $canCheckin;
        ?>
        <tr class="row<?php echo $i % 2; ?>">
                <td>
                        <?php echo $item->id; ?>
                </td>
                <td>
                        <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                </td>
                <td>
                        <?php if ($canEdit) : ?>
                                <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=tag.edit&id='.(int) $item->id); ?>">
                                        <?php echo $this->escape($item->tag); ?>
                                </a>
                        <?php else : ?>
                                        <?php echo $this->escape($item->tag); ?>
                        <?php endif; ?>
                </td>
                </td>
                <td>
                        <?php echo $item->count; ?>
                </td>
        </tr>
<?php endforeach; ?>
