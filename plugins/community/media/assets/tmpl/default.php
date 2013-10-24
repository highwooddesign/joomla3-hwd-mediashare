<?php
/**
 * @version    SVN $Id: default.php 462 2012-08-13 17:10:07Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      16-Nov-2011 19:45:01
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$user = JFactory::getUser();
$canEdit = $user->authorise('core.edit', 'com_hwdmediashare.user.'.$this->channel->id);
$canEditState = $user->authorise('core.edit.state', 'com_hwdmediashare.user.'.$this->channel->id);
$canDelete = $user->authorise('core.delete', 'com_hwdmediashare.user.'.$this->channel->id);
JHtml::_('behavior.modal');
JHtml::_('behavior.framework', true);

?>
<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm">
  <div id="hwd-container"> <a name="top" id="top"></a>
    <?php echo JHtml::_('sliders.start', 'media-user-slider'); ?>
    <?php echo JHtml::_('sliders.panel',JText::_('COM_HWDMS_MEDIA'), 'media'); ?>
        <?php echo $this->loadTemplate('default_media'); ?>
        <div class="clear"></div>
        <p class="readmore">
        <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=user&id=' . $this->channel->id . '&layout=media&display='.$this->display.'&tmpl=component'); ?>" class="modal" rel="{handler: 'iframe', size: {x: 840, y: 500}}">
        <?php echo JText::_('COM_HWDMS_VIEW_ALL'); ?>
        </a>
        </p>
    <?php if ($this->params->get('display_favourites') && $this->favourites) : ?>
    <?php echo JHtml::_('sliders.panel',JText::_('COM_HWDMS_FAVOURITES'), 'favourites'); ?>
        <?php echo $this->loadTemplate('default_favourites'); ?>
        <div class="clear"></div>
        <p class="readmore">
        <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=user&id=' . $this->channel->id . '&layout=favourites&display='.$this->display.'&tmpl=component'); ?>" class="modal" rel="{handler: 'iframe', size: {x: 840, y: 500}}">
        <?php echo JText::_('COM_HWDMS_VIEW_ALL'); ?>
        </a>
        </p>
    <?php endif; ?>
    <?php if ($this->params->get('display_groups') && $this->groups) : ?>
    <?php echo JHtml::_('sliders.panel',JText::_('COM_HWDMS_GROUPS'), 'groups'); ?>
        <?php echo $this->loadTemplate('default_groups'); ?>
        <div class="clear"></div>
        <p class="readmore">
        <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=user&id=' . $this->channel->id . '&layout=groups&display='.$this->display.'&tmpl=component'); ?>" class="modal" rel="{handler: 'iframe', size: {x: 840, y: 500}}">
        <?php echo JText::_('COM_HWDMS_VIEW_ALL'); ?>
        </a>
        </p>
    <?php endif; ?>
    <?php if ($this->params->get('display_playlists') && $this->playlists) : ?>
    <?php echo JHtml::_('sliders.panel',JText::_('COM_HWDMS_PLAYLISTS'), 'playlists'); ?>
        <?php echo $this->loadTemplate('default_playlists'); ?>
        <div class="clear"></div>
        <p class="readmore">
        <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=user&id=' . $this->channel->id . '&layout=playlists&display='.$this->display.'&tmpl=component'); ?>" class="modal" rel="{handler: 'iframe', size: {x: 840, y: 500}}">
        <?php echo JText::_('COM_HWDMS_VIEW_ALL'); ?>
        </a>
        </p>
    <?php endif; ?>
    <?php if ($this->params->get('display_albums') && $this->albums) : ?>
    <?php echo JHtml::_('sliders.panel',JText::_('COM_HWDMS_ALBUMS'), 'albums'); ?>
        <?php echo $this->loadTemplate('default_albums'); ?>
        <div class="clear"></div>
        <p class="readmore">
        <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=user&id=' . $this->channel->id . '&layout=albums&display='.$this->display.'&tmpl=component'); ?>" class="modal" rel="{handler: 'iframe', size: {x: 840, y: 500}}">
        <?php echo JText::_('COM_HWDMS_VIEW_ALL'); ?>
        </a>
        </p>
    <?php endif; ?>
    <?php if ($this->params->get('display_activities') && $this->activities) : ?>
    <?php echo JHtml::_('sliders.panel',JText::_('COM_HWDMS_ACTIVITY'), 'activities'); ?>
        <?php echo $this->loadTemplate('default_activities'); ?>
        <div class="clear"></div>
        <p class="readmore">
        <a class="modal" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=activities&user_id='.$this->channel->id.'&tmpl=component'); ?>" rel="{handler: 'iframe', size: {x: 800, y: 500}}">
        <?php echo JText::_('COM_HWDMS_VIEW_ALL'); ?>
        </a>
        </p>
    <?php endif; ?>
    <?php if ($this->params->get('display_subscribers') && $this->subscribers) : ?>
    <?php echo JHtml::_('sliders.panel',JText::_('COM_HWDMS_SUBSCRIBERS'), 'subscribers'); ?>
        <?php echo $this->loadTemplate('subscribers_'.$this->display); ?>
        <div class="clear"></div>
        <p class="readmore">
        <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=user&id=' . $this->channel->id . '&layout=subscribers&display='.$this->display.'&tmpl=component'); ?>" class="modal" rel="{handler: 'iframe', size: {x: 840, y: 500}}">
        <?php echo JText::_('COM_HWDMS_VIEW_ALL'); ?>
        </a>
        </p>
    <?php endif; ?>
    <?php echo JHtml::_('sliders.end'); ?>
  </div>
</form>