<?php
/**
 * @version    SVN $Id: default_body.php 425 2012-06-28 07:48:57Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      07-Nov-2011 11:38:52
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');
JHtml::_('script','system/multiselect.js', false, true);

$user		= JFactory::getUser();

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>
<?php foreach($this->items as $i => $item):
        $canCreate	= $user->authorise('core.create', 'com_hwdmediashare.category');
        $canEdit	= $user->authorise('core.edit', 'com_hwdmediashare.category');
        $subscriber = & JFactory::getUser($item->user_id);
        ?>
        <tr class="row<?php echo $i % 2; ?>">
                <td>
                        <?php echo $item->id; ?>
                </td>
                <td>
                        <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                </td>
                <td>
                        <?php echo $item->element_id; ?>
                </td>
                <td>
                        <?php echo hwdMediaShareFactory::getElementType($item); ?>
                </td>
                <td>
                        <b><?php echo $subscriber->name; ?></b>
                        <p class="smallsub">
                                <?php echo $subscriber->username; ?>
                        </p>
                </td>
        </tr>
<?php endforeach; ?>
