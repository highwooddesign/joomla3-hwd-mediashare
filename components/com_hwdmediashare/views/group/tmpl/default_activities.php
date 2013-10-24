<?php
/**
 * @version    SVN $Id: default_details.php 615 2012-10-17 14:05:56Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      09-Nov-2011 16:21:17
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$user = JFactory::getUser();

JHtml::_('behavior.tooltip');

?>
    <form action="<?php echo JRoute::_('index.php?option=com_hwdmediashare'); ?>" method="post">
    <!-- Comments -->
    <div class="media-comments">
    <h3><?php echo JText::_('COM_HWDMS_GROUP_ACTIVITY'); ?></h3>
    <div class="categories-list">
      <ul class="category-module">
<?php foreach ($this->group->activities as $id => &$activity) :
$canEdit = ($user->authorise('core.edit', 'com_hwdmediashare.activity.'.$activity->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.activity.'.$activity->id) && ($activity->created_user_id == $user->id)));
$canEditState = $user->authorise('core.edit.state', 'com_hwdmediashare.activity.'.$activity->id);
$canDelete = ($user->authorise('core.delete', 'com_hwdmediashare.activity.'.$activity->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.activity.'.$activity->id) && ($activity->created_user_id == $user->id)));
?>
                        <li class="">
                                <div class="<?php echo ($activity->published != '1' ? ' system-unpublished' : false); ?>">
                                <div class="category-desc">
                                            <a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getUserRoute($activity->created_user_id)); ?>" class="image-left"><img width="50" height="50" border="0" src="<?php echo JRoute::_($this->utilities->getAvatar(JFactory::getUser($activity->created_user_id))); ?>" alt="User"/></a>
                                            <?php if ($canEdit || $canDelete): ?>
                                            <!-- Actions -->
                                            <ul class="media-nav">
                                            <li><a href="#" class="pagenav-manage"><?php echo JText::_('COM_HWDMS_MANAGE'); ?> </a>
                                                <ul class="media-subnav">
                                                    <?php if ($canEdit) : ?>
                                                    <li><?php echo JHtml::_('hwdicon.edit', 'activity', $activity, $this->params); ?></li>
                                                    <?php endif; ?>
                                                    <?php if ($canEditState) : ?>
                                                    <?php if ($activity->published != '1') : ?>
                                                    <li><?php echo JHtml::_('hwdicon.publish', 'activity', $activity, $this->params); ?></li>
                                                    <?php else : ?>
                                                    <li><?php echo JHtml::_('hwdicon.unpublish', 'activity', $activity, $this->params); ?></li>
                                                    <?php endif; ?>
                                                    <?php endif; ?>
                                                    <?php if ($canDelete) : ?>
                                                    <li><?php echo JHtml::_('hwdicon.delete', 'activity', $activity, $this->params); ?></li>
                                                    <?php endif; ?>
                                                </ul>
                                            </li>
                                            </ul>
                                            <?php endif; ?>
                                            <p>
                                                <span class="item-title"><strong><?php echo JText::sprintf($activity->verb, $activity->actor, $activity->object, $activity->target); ?></strong></span>
                                                <span class="media-comment-created small"><?php echo JHtml::_('date.relative', $activity->created); ?></span>
                                            </p>

                                    </div>
                                    <!--<dl>-->
                                            <!--<dd class="media-comment-created small"><?php echo JHtml::_('date.relative', $activity->created); ?></dd>-->
                                            <!--<dd class="media-comment-reply small"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=activityform.reply&element_id=' . $activity->element_id . '&element_type=' . $activity->element_type . '&reply_id=' . $activity->id .'&tmpl=component&return=' . $this->return); ?>" class="pagenav-zoom modal" rel="{handler: 'iframe', size: {<?php echo $this->utilities->modalSize(); ?>}}" title="<?php echo JText::_('COM_HWDMS_REPLY'); ?>"><?php echo JText::_('COM_HWDMS_REPLY'); ?></a></dd>-->
                                            <!--<dd class="media-comment-like small"> <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=activity.like&id=' . $activity->id . '&return=' . $this->return . '&tmpl=component'); ?>"><?php echo JText::_('COM_HWDMS_LIKE'); ?></a> (<?php echo $this->utilities->escape($activity->likes); ?>)</dd>-->
                                            <!--<dd class="media-comment-dislike small"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=activity.dislike&id=' . $activity->id . '&return=' . $this->return . '&tmpl=component'); ?>"><?php echo JText::_('COM_HWDMS_DISLIKE'); ?></a> (<?php echo $this->utilities->escape($activity->dislikes); ?>)</dd>-->
                                            <!--<dd class="media-comment-report small"> <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=activityform.report&id=' . $activity->id . '&return=' . $this->return . '&tmpl=component'); ?>" class="modal" rel="{handler: 'iframe', size: {<?php echo $this->utilities->modalSize(); ?>}}" title="<?php echo JText::_('COM_HWDMS_REPORT'); ?>"><?php echo JText::_('COM_HWDMS_REPORT'); ?></a> </dd>-->

                                    <!--</dl>-->
                                    <div class="clear"></div>
                                    </div>
                        </li>    


<?php endforeach; ?>
      </ul>
    </div>
    <p class="readmore">
      <a class="modal" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=activities&element_type=3&element_id='.$this->group->id.'&tmpl=component'); ?>" rel="{handler: 'iframe', size: {<?php echo $this->utilities->modalSize('large'); ?>}}">
        <?php echo JText::_('COM_HWDMS_VIEW_ALL_ACTIVITY'); ?>
      </a>
    </p>
    </div>
    </form>





