<?php
/**
 * @version    SVN $Id: default_overview.php 577 2012-10-16 09:51:39Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      30-Nov-2011 16:40:50
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$user = JFactory::getUser();
$uri	= JFactory::getURI();

$canEdit = ($user->authorise('core.edit', 'com_hwdmediashare.user.'.$this->item->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.user.'.$this->item->id) && ($this->item->id == $user->id)));
$canEditState = $user->authorise('core.edit.state', 'com_hwdmediashare.user.'.$this->item->id);
$canDelete = ($user->authorise('core.delete', 'com_hwdmediashare.user.'.$this->item->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.user.'.$this->item->id) && ($this->item->id == $user->id)));
$canAdd = $user->authorise('core.create', 'com_hwdmediashare');

?>
<div class="media-account-header">
  <h2 class="media-category-title"><?php echo JText::_('COM_HWDMS_OVERVIEW'); ?></h2>
  <div class="clear"></div>
    <!-- Description -->
    <div class="media-account-description">
    <!-- Thumbnail Image -->
    <div class="media-item">
      <?php if ($canEdit || $canDelete): ?>
      <!-- Actions -->
      <ul class="media-nav">
        <li><a href="#" class="pagenav-manage"><?php echo JText::_('COM_HWDMS_MANAGE'); ?> </a>
          <ul class="media-subnav">
            <?php if ($canEdit) : ?>
            <li><?php echo JHtml::_('hwdicon.edit', 'user', $this->item, $this->params); ?></li>
            <?php endif; ?>
            <?php if ($canEditState) : ?>
            <?php if ($this->item->published != '1') : ?>
            <li><?php echo JHtml::_('hwdicon.publish', 'user', $this->item, $this->params); ?></li>
            <?php else : ?>
            <li><?php echo JHtml::_('hwdicon.unpublish', 'user', $this->item, $this->params); ?></li>
            <?php endif; ?>
            <?php endif; ?>
            <?php if ($canDelete) : ?>
            <li><?php echo JHtml::_('hwdicon.delete', 'user', $this->item, $this->params); ?></li>
            <?php endif; ?>
          </ul>
        </li>
      </ul>
      <?php endif; ?>
      <!-- Media Type -->
      <?php if ($this->params->get('item_meta_thumbnail') != 'hide') :?>
      <?php if ($this->params->get('item_meta_type_icon') != 'hide') :?>
      <div class="media-item-format-2">
        <img src="<?php echo JHtml::_('hwdicon.overlay', 5); ?>" alt="<?php echo JText::_('COM_HWDMS_USER_CHANNEL'); ?>" />
      </div>
      <?php endif; ?>
      <img src="<?php echo JRoute::_(hwdMediaShareDownloads::thumbnail($this->item, 5)); ?>" border="0" alt="<?php echo $this->escape($this->item->title); ?>" style="width:120px;" />
      <?php endif; ?>
    </div>
    <dl class="article-info">
    <dt class="article-info-term"><?php echo JText::_('COM_HWDMS_DETAILS'); ?> </dt>
      <dd class="media-info-profile">
        <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=userform.edit&id='.$user->id.'&return='.base64_encode($uri)); ?>"><?php echo JText::_('COM_HWDMS_PROFILE'); ?></a>
      </dd>
      <dd class="media-info-media">
        <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=account&layout=media'); ?>"><?php echo JText::_('COM_HWDMS_MEDIA'); ?></a> 
        (<?php echo $this->item->nummedia; ?>)
      </dd>
      <dd class="media-info-favourites">
        <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=account&layout=favourites'); ?>"><?php echo JText::_('COM_HWDMS_FAVOURITES'); ?></a> 
        (<?php echo $this->item->numfavourites; ?>)
      </dd>
      <?php if ($this->params->get('enable_albums')): ?>
        <dd class="media-info-albums">
          <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=account&layout=albums'); ?>"><?php echo JText::_('COM_HWDMS_ALBUMS'); ?></a> 
          (<?php echo $this->item->numalbums; ?>)
          <?php if ($canAdd) :?>
            (<a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=albumform&layout=edit&return='.base64_encode(JFactory::getURI())); ?>" title="<?php echo JText::_('COM_HWDMS_ADD_ALBUM'); ?>"><?php echo JText::_('COM_HWDMS_ADD_ALBUM'); ?></a>)
          <?php endif; ?>
        </dd>
      <?php endif; ?>
      <?php if ($this->params->get('enable_groups')): ?>
        <dd class="media-info-groups">
          <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=account&layout=groups'); ?>"><?php echo JText::_('COM_HWDMS_GROUPS'); ?></a>
          (<?php echo $this->item->numgroups; ?>)
          <?php if ($canAdd) :?>
            (<a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=groupform&layout=edit&return='.base64_encode(JFactory::getURI())); ?>" title="<?php echo JText::_('COM_HWDMS_ADD_GROUP'); ?>"><?php echo JText::_('COM_HWDMS_ADD_GROUP'); ?></a>)
          <?php endif; ?>          
        </dd>
      <?php endif; ?>
      <?php if ($this->params->get('enable_playlists')): ?>
        <dd class="media-info-playlists">
          <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=account&layout=playlists'); ?>"><?php echo JText::_('COM_HWDMS_PLAYLISTS'); ?></a>
          (<?php echo $this->item->numplaylists; ?>)
          <?php if ($canAdd) :?>
            (<a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=playlistform&layout=edit&return='.base64_encode(JFactory::getURI())); ?>" title="<?php echo JText::_('COM_HWDMS_ADD_PLAYLIST'); ?>"><?php echo JText::_('COM_HWDMS_ADD_PLAYLIST'); ?></a>)
          <?php endif; ?>          
        </dd>
      <?php endif; ?>
      <?php if ($this->params->get('enable_subscriptions')): ?>
        <dd class="media-info-subscriptions">
          <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=account&layout=subscriptions'); ?>"><?php echo JText::_('COM_HWDMS_SUBSCRIPTIONS'); ?></a> 
          (<?php echo $this->item->numsubscriptions; ?>)         
        </dd>
      <?php endif; ?>
      <dd class="media-info-hits">
        <?php echo JText::_('COM_HWDMS_VIEWS'); ?> 
        (<?php echo $this->item->hits; ?>)
      </dd>      
    </dl>
    <div class="clear"></div>
  </div>
  <div class="clear"></div>
</div>
<div class="media-account-description">
  <?php echo JHtml::_('content.prepare', $this->item->description); ?>
</div>
<div class="clear"></div>
