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
JHtml::_('HwdPopup.iframe', 'page');

$user = JFactory::getUser();
$canEdit = ($user->authorise('core.edit', 'com_hwdmediashare.group.'.$this->group->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.group.'.$this->group->id) && ($this->group->created_user_id == $user->id)));
$canEditState = $user->authorise('core.edit.state', 'com_hwdmediashare.group.'.$this->group->id);
$canDelete = ($user->authorise('core.delete', 'com_hwdmediashare.group.'.$this->group->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.group.'.$this->group->id) && ($this->group->created_user_id == $user->id)));
$canAddMedia = ((JFactory::getUser()->authorise('hwdmediashare.upload', 'com_hwdmediashare') || JFactory::getUser()->authorise('hwdmediashare.import', 'com_hwdmediashare')) && ($this->group->isMember));
$canManageMedia = ($this->group->isAdmin);
$canManageMembers = ($this->group->isAdmin);
?>
<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm">
  <div id="hwd-container" class="<?php echo $this->pageclass_sfx;?>"> <a name="top" id="top"></a>
    <!-- Media Navigation -->
    <?php echo hwdMediaShareHelperNavigation::getInternalNavigation(); ?>
    <!-- Media Header -->
    <div class="media-header">
      <?php if ($this->params->get('item_meta_title') != '0') :?>
        <h2 class="media-group-title"><?php echo $this->escape($this->group->title); ?></h2>
      <?php endif; ?>       
      <!-- Buttons -->
      <div class="btn-group pull-right">
        <?php if ($canAddMedia): ?>
          <a title="<?php echo JText::_('COM_HWDMS_ADD_MEDIA'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=upload&group_id='.(int)$this->group->id); ?>" class="btn"><i class="icon-plus"></i> <?php echo JText::_('COM_HWDMS_ADD_MEDIA'); ?></a>
        <?php endif; ?>
        <?php if ($canManageMedia): ?>
          <a title="<?php echo JText::_('COM_HWDMS_MANAGE_MEDIA'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=groupmedia&tmpl=component&group_id=' . $this->group->id); ?>" class="btn media-popup-iframe-page" ><?php echo JText::_('COM_HWDMS_MANAGE_MEDIA'); ?></a>
        <?php endif; ?>
        <?php if ($canManageMembers): ?>
          <a title="<?php echo JText::_('COM_HWDMS_MANAGE_MEMBERS'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=groupmembers&tmpl=component&group_id=' . $this->group->id); ?>" class="btn media-popup-iframe-page" ><?php echo JText::_('COM_HWDMS_MANAGE_MEMBERS'); ?></a>
        <?php endif; ?>          
        <?php if ($this->group->featured && $this->params->get('item_meta_featured_status') != '0') : ?>
          <div class="btn btn-danger btn-noevent"><?php echo JText::_('COM_HWDMS_FEATURED'); ?></div>
        <?php endif; ?> 
        <?php if ($this->group->status != 1) : ?>
          <div class="btn btn-danger btn-noevent"><?php echo $this->utilities->getReadableStatus($this->group); ?></div>
        <?php endif; ?>   
        <?php if ($this->group->published != 1) : ?>
          <div class="btn btn-danger btn-noevent"><?php echo JText::_('COM_HWDMS_UNPUBLISHED'); ?></div>
        <?php endif; ?>           
        <?php if ($user->id && $this->group->isMember) : ?>
          <a title="<?php echo JText::_('COM_HWDMS_LEAVE_GROUP'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=groups.leave&id=' . $this->group->id . '&return=' . $this->return . '&tmpl=component&'.JSession::getFormToken().'=1'); ?>" class="btn"><i class="icon-user"></i> <?php echo JText::_('COM_HWDMS_LEAVE_GROUP'); ?></a>
	<?php elseif ($user->id && !$this->group->isMember): ?>
          <a title="<?php echo JText::_('COM_HWDMS_JOIN_GROUP'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=groups.join&id=' . $this->group->id . '&return=' . $this->return . '&tmpl=component&'.JSession::getFormToken().'=1'); ?>" class="btn"><i class="icon-user"></i> <?php echo JText::_('COM_HWDMS_JOIN_GROUP'); ?></a>
	<?php endif; ?>
        <?php if ($this->params->get('item_meta_report') != '0'): ?>  
          <a title="<?php echo JText::_('COM_HWDMS_REPORT'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=groupform.report&id=' . $this->group->id . '&return=' . $this->return . '&tmpl=component'); ?>" class="btn media-popup-iframe-form"><i class="icon-flag"></i> <?php echo JText::_('COM_HWDMS_REPORT'); ?></a>
        <?php endif; ?>    
        <?php if ($canEdit || $canDelete): ?>
        <?php
        // Create dropdown items
        if ($canEdit) : 
          JHtml::_('hwddropdown.edit', $this->group->id, 'groupform'); 
        endif;    
        if ($canEditState) :
          $action = $this->group->published ? 'unpublish' : 'publish';
          JHtml::_('hwddropdown.' . $action, $this->group->id, 'groups'); 
        endif; 
        if ($canEdit) : 
          JHtml::_('hwddropdown.delete', $this->group->id, 'groups'); 
        endif;         
        // Render dropdown list       
        echo JHtml::_('hwddropdown.render', $this->escape($this->group->title));
        ?>
        <?php endif; ?>
      </div>        
      <div class="clear"></div>
    </div>
    <!-- Map -->  
    <?php if ($this->params->get('groupitem_media_map') != '0') :?>
      <div class="media-group-map">
        <?php echo ($this->group->map); ?>
      </div>
    <?php endif; ?>     
    <div class="row-fluid">
      <!-- Main Column -->  
      <div class="span8">
        <?php if (count($this->items) && $this->params->get('groupitem_group_media') != '0') : ?>  
          <div class="media-details-view">
            <?php echo JLayoutHelper::render('media_details', $this, JPATH_ROOT.'/components/com_hwdmediashare/libraries/layouts'); ?>
          </div>    
          <!-- Pagination -->
          <div class="pagination"> <?php echo $this->pagination->getPagesLinks(); ?> </div>
        <?php endif; ?>
      </div>
      <!-- Side Column -->        
      <div class="span4">       
        <?php if ($this->params->get('groupitem_group_members') != '0') :?>  
          <h3 class="media-group-members-title"><?php echo JText::_('COM_HWDMS_NEWEST_MEMBERS'); ?></h3>
          <div class="media-group-members">
            <?php if (count($this->members) == 0) : ?>
            <?php echo JText::_('COM_HWDMS_NO_MEMBERS'); ?>
            <?php endif; ?>
            <?php foreach ($this->members as $id => $item) : ?>   
              <a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getUserRoute($item->id)); ?>" class="pull-left">
                <?php echo JHtml::_('hwdimage.avatar', $item->id, 50); ?></a>
            <?php endforeach; ?>              
          </div>
          <div class="btn-toolbar row-fluid">
            <a class="btn span12 media-popup-iframe-form" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=group&id='.$this->group->id.'&layout=members&tmpl=component'); ?>" title="<?php echo JText::_('COM_HWDMS_VIEW_ALL_MEMBERS'); ?>"><?php echo JText::_('COM_HWDMS_VIEW_ALL_MEMBERS'); ?></a>
          </div> 
        <?php endif; ?> 
        <?php if ($this->params->get('groupitem_group_activity') != '0' && $this->activities) :?>  
          <h3 class="media-user-browse"><?php echo JText::_('COM_HWDMS_RECENT_ACTIVITY'); ?></h3>
          <ul class="media-activity-list">
            <?php foreach ($this->activities as $activity) : ?>
              <li class="media-activity-item">
                <a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getUserRoute($activity->actor)); ?>" class="media-activity-avatar">
                  <?php echo JHtml::_('hwdimage.avatar', $activity->actor, 50); ?></a>
                <p class="media-activity-desc"><?php echo hwdMediaShareActivities::renderActivityHtml($activity); ?></p>
                <p class="media-activity-date"><?php echo JHtml::_('hwddate.relative', $activity->created); ?></p>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>  
      </div> 
    </div>
    <!-- Comments -->
    <?php if ($this->params->get('commenting') != 1) : ?>
      <?php echo $this->getComments($this->group); ?>
    <?php endif; ?>  
    <!-- Description -->
    <div class="well media-group-description">
      <?php if ($this->params->get('item_meta_description') != '0') :?>
        <?php echo JHtml::_('content.prepare', $this->group->description); ?>
      <?php endif; ?> 
      <?php if ($this->params->get('item_meta_likes') != '0' || $this->params->get('item_meta_dislikes') != '0') :?>
      <div class="pull-right">
        <div class="media-info-likes pull-right">      
          <?php if ($this->params->get('item_meta_likes') != '0') :?><i class="icon-thumbs-up"></i> <span id="media-likes"><?php echo (int) $this->group->likes; ?></span><?php endif; ?>
          <?php if ($this->params->get('item_meta_dislikes') != '0') :?><i class="icon-thumbs-down"></i> <span id="media-dislikes"><?php echo (int) $this->group->dislikes; ?></span><?php endif; ?>
        </div>
      </div>       
      <?php endif; ?> 
      <?php if ($this->params->get('item_meta_media_count') != '0' || $this->params->get('item_meta_author') != '0' || $this->params->get('item_meta_created') != '0' || $this->params->get('item_meta_hits') != '0') :?>
      <dl class="media-info">
        <dt class="media-info-term"><?php echo JText::_('COM_HWDMS_DETAILS'); ?> </dt>      
        <?php if ($this->params->get('item_meta_author') != '0' || $this->params->get('item_meta_created') != '0') : ?>
        <dd class="media-info-meta">
          <?php if ($this->params->get('item_meta_author') != '0') : ?>
            <span class="media-info-createdby">
              <?php echo JText::sprintf('COM_HWDMS_BY_X_USER', '<a href="'.JRoute::_(hwdMediaShareHelperRoute::getUserRoute($this->group->created_user_id)).'">'.htmlspecialchars($this->group->author, ENT_COMPAT, 'UTF-8').'</a>'); ?>
            </span>
          <?php endif; ?>
          <?php if ($this->params->get('item_meta_created') != '0') : ?>
            <span class="media-info-created">
              <?php echo JHtml::_('hwddate.relative', $this->group->created); ?>
            </span>
          <?php endif; ?>
        </dd>
        <?php endif; ?>
        <?php if ($this->params->get('item_meta_hits') != '0') :?>
          <dd class="media-info-hits label"><?php echo JText::sprintf('COM_HWDMS_X_VIEWS', number_format((int) $this->group->hits)); ?></dd>
        <?php endif; ?>      
        <?php if ($this->params->get('item_meta_media_count') != '0') :?>
          <dd class="media-info-count label"><?php echo JText::plural('COM_HWDMS_X_MEDIA_COUNT', (int) $this->group->nummedia); ?></dd>
          <dd class="media-info-count label"><?php echo JText::plural('COM_HWDMS_X_MEMBER_COUNT', (int) $this->group->nummembers); ?></dd>
        <?php endif; ?>        
        <div class="clearfix"></div>         
      </dl>
      <?php endif; ?>  
      <?php if ($this->params->get('item_meta_likes') != '0' || $this->params->get('item_meta_dislikes') != '0' || $this->params->get('item_meta_report') != '0') : ?>
        <div class="btn-group">
          <?php if ($this->params->get('item_meta_likes') != '0') : ?>
            <a title="<?php echo JText::_('COM_HWDMS_LIKE'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=groups.like&id=' . $this->group->id . '&return=' . $this->return . '&tmpl=component&'.JSession::getFormToken().'=1'); ?>" class="btn btn-mini" id="group-like-btn" data-media-task="like" data-media-id="<?php echo $this->group->id; ?>" data-media-return="<?php echo $this->return; ?>" data-media-token="<?php echo JSession::getFormToken(); ?>"><i class="icon-thumbs-up"></i> <?php echo JText::_('COM_HWDMS_LIKE'); ?></a>
          <?php endif; ?>
          <?php if ($this->params->get('item_meta_dislikes') != '0') : ?>
            <a title="<?php echo JText::_('COM_HWDMS_DISLIKE'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=groups.dislike&id=' . $this->group->id . '&return=' . $this->return . '&tmpl=component&'.JSession::getFormToken().'=1'); ?>" class="btn btn-mini" id="group-dislike-btn" data-media-task="dislike" data-media-id="<?php echo $this->group->id; ?>" data-media-return="<?php echo $this->return; ?>" data-media-token="<?php echo JSession::getFormToken(); ?>"><i class="icon-thumbs-down"></i> <?php echo JText::_('COM_HWDMS_DISLIKE'); ?></a>
          <?php endif; ?>
          <?php if ($this->params->get('item_meta_report') != '0') : ?>
            <a title="<?php echo JText::_('COM_HWDMS_REPORT'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=groupform.report&id=' . $this->group->id . '&return=' . $this->return . '&tmpl=component'); ?>" class="btn btn-mini media-popup-iframe-form" id="group-report-btn"><i class="icon-flag"></i> <?php echo JText::_('COM_HWDMS_REPORT'); ?></a>
          <?php endif; ?>  
        </div>          
      <?php endif; ?>
      <!-- Tags -->
      <?php if ($this->params->get('show_tags', 1) && !empty($this->group->tags)) : ?>
	<?php $this->group->tagLayout = new JLayoutFile('joomla.content.tags'); ?>
	<?php echo $this->group->tagLayout->render($this->group->tags->itemTags); ?>
      <?php endif; ?>
      <!-- Custom fields -->
      <dl class="media-info">
        <?php foreach($this->group->customfields->fields as $group => $fields) : ?>
          <dt class="media-info-group"><?php echo JText::_($group); ?> </dt>
          <?php foreach($fields as $field) : ?>
            <dd class="media-info-<?php echo $field->fieldcode; ?>"><?php echo $field->name; ?>: <?php echo $this->group->customfields->display($field); ?></dd>
          <?php endforeach; ?>
        <?php endforeach; ?>
      </dl>
      <div class="clear"></div>        
    </div>
  </div>
</form>
