<?php
/**
 * @version    SVN $Id: default_body.php 425 2012-06-28 07:48:57Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$user		= JFactory::getUser();
?>
<?php foreach($this->items as $i => $item):
        $canEdit = $user->authorise('core.edit', 'com_hwdmediashare.'.$this->view_type);
        ?>
        <tr class="row<?php echo $i % 2; ?>">
                <td>
                        <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                </td>
                <td>
                        <?php if ($canEdit) : ?>
                                <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task='.$this->view_edit.'.edit&id='.(int)$item->id); ?>" target="_top">
                                        <?php echo $this->escape($item->title); ?>
                                </a>
                        <?php else : ?>
                                <?php echo $this->escape($item->title); ?>
                        <?php endif; ?>
                        <p class="smallsub">
                                <?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?>
                        </p>
                </td>
                </td>
                <td class="center">
                        <?php echo $this->getConnection($item, $i); ?>
                </td>
                <td class="center">
                        <?php echo $item->id; ?>
                </td>
        </tr>
<?php endforeach; ?>
