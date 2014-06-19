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
<?php foreach ($this->items as $i => $item) : ?>
<tr class="row<?php echo $i % 2; ?>">
        <td class="nowrap has-context">
                <div class="pull-left">
                        <?php echo hwdMediaShareActivities::renderActivityHtml($item); ?>                                
                </div>
        </td>     
        <td class="nowrap small hidden-phone">
                <?php echo JHtml::_('date.relative', $item->created); ?>
        </td>
        <td class="center hidden-phone">
                <?php echo (int) $item->id; ?>
        </td>
</tr>
<?php endforeach; ?>
