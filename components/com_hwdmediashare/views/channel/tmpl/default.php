<?php
/**
 * @package     Joomla.site
 * @subpackage  Component.hwdmediashare
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/html');
JHtml::_('HwdPopup.iframe', 'form');

$user = JFactory::getUser();
$canEdit = ($user->authorise('core.edit', 'com_hwdmediashare.channel.'.$this->channel->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.channel.'.$this->channel->id) && ($this->channel->created_user_id == $user->id)));
$canEditState = $user->authorise('core.edit.state', 'com_hwdmediashare.channel.'.$this->channel->id);
$canDelete = ($user->authorise('core.delete', 'com_hwdmediashare.channel.'.$this->channel->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.channel.'.$this->channel->id) && ($this->channel->created_user_id == $user->id)));
$canAdd = ($user->authorise('hwdmediashare.upload','com_hwdmediashare') || $user->authorise('hwdmediashare.import','com_hwdmediashare'));
?>
<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm">
  <div id="hwd-container" class="<?php echo $this->pageclass_sfx;?>"> <a name="top" id="top"></a>
    <!-- Media Navigation -->
    <?php echo hwdMediaShareHelperNavigation::getInternalNavigation(); ?>
    <!-- Media Header -->
    <div class="media-header">
      <?php if ($this->params->get('item_meta_title') != '0') :?>
        <h2 class="media-channel-title"><?php echo $this->escape($this->channel->title); ?></h2>
      <?php endif; ?>
      <!-- Buttons -->
      <div class="btn-group pull-right">
        <?php if ($this->params->get('enable_subscriptions') && $this->channel->id != 0) : ?>
          <?php if ($this->channel->isSubscribed) : ?>
          <a title="<?php echo JText::_('COM_HWDMS_UNSUBSCRIBE'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=channels.unsubscribe&id=' . $this->channel->id . '&return=' . $this->return . '&tmpl=component&'.JSession::getFormToken().'=1'); ?>" class="btn active"><i class="icon-user"></i> <?php echo JText::_('COM_HWDMS_UNSUBSCRIBE'); ?></a>
          <?php else : ?>
          <a title="<?php echo JText::_('COM_HWDMS_SUBSCRIBE'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=channels.subscribe&id=' . $this->channel->id . '&return=' . $this->return . '&tmpl=component&'.JSession::getFormToken().'=1'); ?>" class="btn"><i class="icon-user"></i> <?php echo JText::_('COM_HWDMS_SUBSCRIBE'); ?></a>
          <?php endif; ?>
        <?php endif; ?>          
        <?php if ($this->params->get('item_meta_report') != '0'): ?>  
          <a title="<?php echo JText::_('COM_HWDMS_REPORT'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=channelform.report&id=' . $this->channel->id . '&return=' . $this->return . '&tmpl=component'); ?>" class="btn media-popup-iframe-form"><i class="icon-flag"></i> <?php echo JText::_('COM_HWDMS_REPORT'); ?></a>
        <?php endif; ?>    
        <?php if ($canEdit || $canDelete): ?>
        <?php
        // Create dropdown items
        if ($canEdit) : 
          JHtml::_('hwddropdown.edit', $this->channel->id, 'channelform'); 
        endif;    
        if ($canEditState) :
          $action = $this->channel->published ? 'unpublish' : 'publish';
          JHtml::_('hwddropdown.' . $action, $this->channel->id, 'channels'); 
        endif; 
        if ($canEdit) : 
          JHtml::_('hwddropdown.delete', $this->channel->id, 'channels'); 
        endif;         
        // Render dropdown list       
        echo JHtml::_('hwddropdown.render', $this->escape($this->channel->title));
        ?>
        <?php endif; ?>
      </div>        
      <div class="clear"></div>
    </div>
    <div class="row-fluid">
      <!-- Main Column -->  
      <div class="span8">
        <?php if (count($this->items)) : ?>   
        <!-- Search Filters -->
        <?php echo JLayoutHelper::render('search_tools', array('view' => $this), JPATH_ROOT.'/components/com_hwdmediashare/libraries/layouts'); ?>
        <div class="clear"></div>
        <div class="media-details-view">
          <?php echo JLayoutHelper::render($this->layout . '_details', $this, JPATH_ROOT.'/components/com_hwdmediashare/libraries/layouts'); ?>
        </div> 
        <!-- Pagination -->
        <div class="pagination"> <?php echo $this->pagination->getPagesLinks(); ?> </div>
        <div class="clear"></div>
        <?php endif; ?>
      </div>
      <!-- Side Column -->        
      <div class="span4">
        <h3 class="media-user-browse">Browse channel</h3>
        <ul>
          <?php if ($this->channel->nummedia > 0) : ?><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=channel&id=' . $this->channel->id . '&layout=media&display='.$this->display); ?>"><li><?php echo (int) $this->channel->nummedia; ?> Media</li></a><?php endif; ?>
          <?php if ($this->channel->numfavourites > 0) : ?><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=channel&id=' . $this->channel->id . '&layout=favourites&display='.$this->display); ?>"><li><?php echo (int) $this->channel->numfavourites; ?> Favourites</li></a><?php endif; ?>
          <?php if ($this->channel->numgroups > 0) : ?><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=channel&id=' . $this->channel->id . '&layout=groups&display='.$this->display); ?>"><li><?php echo (int) $this->channel->numgroups; ?> Groups</li></a><?php endif; ?>
          <?php if ($this->channel->numplaylists > 0) : ?><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=channel&id=' . $this->channel->id . '&layout=playlists&display='.$this->display); ?>"><li><?php echo (int) $this->channel->numplaylists; ?> Playlists</li></a><?php endif; ?>
          <?php if ($this->channel->numalbums > 0) : ?><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=channel&id=' . $this->channel->id . '&layout=albums&display='.$this->display); ?>"><li><?php echo (int) $this->channel->numalbums; ?> Albums</li></a><?php endif; ?>
          <?php if ($this->channel->numsubscribers > 0) : ?><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=channel&id=' . $this->channel->id . '&layout=subscribers&display='.$this->display); ?>"><li><?php echo (int) $this->channel->numsubscribers; ?> Subscribers</li></a><?php endif; ?>
        </ul>
        <h3 class="media-user-browse">Recent activity</h3>
        <?php if (count($this->activities)) : ?> 
          <ul class="media-activity-list">
            <?php foreach ($this->activities as $activity) : ?>
              <li class="media-activity-item">
                  <div class="category-desc">
                    <a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getUserRoute($activity->actor)); ?>" class="image-left"><img width="50" height="50" border="0" src="<?php //echo JRoute::_($this->utilities->getAvatar(JFactory::getUser($activity->actor))); ?>" alt="User"/></a>
                    <p><?php echo hwdMediaShareActivities::renderActivityHtml($activity); ?></p>
                    <p class="small"><?php echo JHtml::_('hwddate.relative', $activity->created); ?></p>
                  </div>
                <div class="clear"></div>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>         
      </div>        
    </div>
    <!-- Description -->
    <div class="well media-user-description">
      <?php if ($this->params->get('item_meta_description') != '0') :?>
        <?php echo JHtml::_('content.prepare', $this->channel->description); ?>
      <?php endif; ?> 
        <?php if ($this->params->get('item_meta_media_count') != '0' || $this->params->get('item_meta_created') != '0' || $this->params->get('item_meta_hits') != '0' || $this->params->get('item_meta_likes') != '0' || $this->params->get('item_meta_report') != '0') : ?>
        <dl class="media-info">
          <dt class="media-info-term"><?php echo JText::_('COM_HWDMS_DETAILS'); ?> </dt>
          <?php if ($this->params->get('item_meta_media_count') != '0') :?>
            <dd class="media-info-count"><?php echo JText::_('COM_HWDMS_MEDIA'); ?> (<?php echo (int) $this->channel->nummedia; ?>)</dd>
          <?php endif; ?>
          <?php if ($this->params->get('item_meta_created') != '0') :?>
            <dd class="media-info-created"> <?php echo JText::sprintf('COM_HWDMS_CREATED_ON', JHtml::_('date', $this->channel->created, $this->params->get('list_date_format'))); ?></dd>
          <?php endif; ?>            
          <?php if ($this->params->get('item_meta_hits') != '0') :?>
            <dd class="media-info-hits"><?php echo JText::_('COM_HWDMS_VIEWS'); ?> (<?php echo (int) $this->channel->hits; ?>)</dd>
          <?php endif; ?>           
          <?php if ($this->params->get('item_meta_likes') != '0') :?>
            <dd class="media-info-like"> <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=channels.like&id=' . $this->channel->id . '&return=' . $this->return . '&tmpl=component&'.JSession::getFormToken().'=1'); ?>"><?php echo JText::_('COM_HWDMS_LIKE'); ?></a> (<?php echo $this->escape($this->channel->likes); ?>) <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=channels.dislike&id=' . $this->channel->id . '&return=' . $this->return . '&tmpl=component&'.JSession::getFormToken().'=1'); ?>"><?php echo JText::_('COM_HWDMS_DISLIKE'); ?></a> (<?php echo $this->escape($this->channel->dislikes); ?>) </dd>
          <?php endif; ?>   
          <?php if ($this->params->get('item_meta_report') != '0') :?>
            <dd class="media-info-report"> <a title="<?php echo JText::_('COM_HWDMS_REPORT'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=channelform.report&id=' . $this->channel->id . '&return=' . $this->return . '&tmpl=component'); ?>" class="media-popup-iframe-form"><?php echo JText::_('COM_HWDMS_REPORT'); ?> </a> </dd>
          <?php endif; ?>  
        </dl>
        <?php endif; ?>   
        <!-- Tags -->
        <?php if ($this->params->get('show_tags', 1) && !empty($this->channel->tags)) : ?>
	  <?php $this->channel->tagLayout = new JLayoutFile('joomla.content.tags'); ?>
	  <?php echo $this->channel->tagLayout->render($this->channel->tags->itemTags); ?>
        <?php endif; ?>
      <!-- Custom fields -->
      <dl class="media-info">
        <?php foreach($this->channel->customfields->fields as $group => $fields) : ?>
          <dt class="media-info-group"><?php echo JText::_($group); ?> </dt>
          <?php foreach($fields as $field) : ?>
            <dd class="media-info-<?php echo $field->fieldcode; ?>"><?php echo $field->name; ?>: <?php echo $this->channel->customfields->display($field); ?></dd>
          <?php endforeach; ?>
        <?php endforeach; ?>
      </dl>
      <div class="clear"></div>        
    </div>
  </div>
</form>
