<?php
/**
 * @version    SVN $Id: default.php 1691 2013-10-16 15:14:00Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      16-Nov-2011 19:45:40
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

$user = JFactory::getUser();

$canEdit = ($user->authorise('core.edit', 'com_hwdmediashare.group.'.$this->group->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.group.'.$this->group->id) && ($this->group->created_user_id == $user->id)));
$canEditState = $user->authorise('core.edit.state', 'com_hwdmediashare.group.'.$this->group->id);
$canDelete = ($user->authorise('core.delete', 'com_hwdmediashare.group.'.$this->group->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.group.'.$this->group->id) && ($this->group->created_user_id == $user->id)));
$canAdd = (($this->group->ismember) && (JFactory::getUser()->authorise('hwdmediashare.upload','com_hwdmediashare') || JFactory::getUser()->authorise('hwdmediashare.import','com_hwdmediashare')));

JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');
?>
  <div id="hwd-container"> <a name="top" id="top"></a>
    <!-- Media Navigation -->
    <?php echo hwdMediaShareHelperNavigation::getInternalNavigation(); ?>
    <!-- Media Header -->
    <div class="media-header">
      <?php if ($this->params->get('item_meta_title') != 'hide') :?>
        <h2 class="media-group-title"><?php echo $this->escape($this->group->title); ?></h2>
      <?php endif; ?> 
      <div class="clear"></div>
      <!-- 2 column (responsive) grid -->
      <div class="media-grid">
        <div class="main">
 
      <?php if ($this->params->get('groupitem_media_map') != 'hide') :?>
          <div class="media-group-map">
            <?php echo ($this->group->map); ?>
            <div class="clear"></div>
          </div>
          <p class="readmore">
            <a class="modal" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=group&id='.$this->group->id.'&layout=map&tmpl=component'); ?>" rel="{handler: 'iframe', size: {<?php echo $this->utilities->modalSize(); ?>}}">
              <?php echo JText::_('COM_HWDMS_ENLARGE_MAP'); ?>
            </a>
          </p>
      <?php endif; ?> 
            
            
            <?php if ($this->params->get('groupitem_group_media') != 'hide') :?>  
              <?php echo $this->loadTemplate('details'); ?>
              <p class="readmore">
                <a class="modal" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=group&id='.$this->group->id.'&layout=media&tmpl=component'); ?>" rel="{handler: 'iframe', size: {<?php echo $this->utilities->modalSize('large'); ?>}}">
                  <?php echo JText::_('COM_HWDMS_VIEW_ALL_MEDIA'); ?>
                </a>
              </p>
            <?php endif; ?>
              
              
    <?php if ($this->params->get('commenting') != 1) : ?>
    <?php echo $this->getComments($this->group); ?>
    <?php endif; ?>      
              
            
        </div>
        <div class="sidebar">
           
            
            
            
        <?php if ($user->id && $this->group->ismember) : ?>
          <a class="button" title="<?php echo JText::_('COM_HWDMS_LEAVE_GROUP'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=group.leave&id=' . $this->group->id . '&return=' . $this->return . '&tmpl=component'); ?>"><?php echo JText::_('COM_HWDMS_LEAVE_GROUP'); ?></a>
	<?php elseif ($user->id && !$this->group->ismember): ?>
          <a class="button" title="<?php echo JText::_('COM_HWDMS_JOIN_GROUP'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=group.join&id=' . $this->group->id . '&return=' . $this->return . '&tmpl=component'); ?>"><?php echo JText::_('COM_HWDMS_JOIN_GROUP'); ?></a>
	<?php endif; ?>
          
        <?php if ($canAdd) :?>
         <a class="button" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=upload&tmpl=component&group_id='.(int)$this->group->id); ?>" class="ls-add modal" rel="{handler: 'iframe', size: {<?php echo $this->utilities->modalSize(); ?>}}" title="<?php echo JText::_('COM_HWDMS_ADD_MEDIA'); ?>"><?php echo JText::_('COM_HWDMS_ADD_MEDIA'); ?></a>
        <?php endif; ?>
          
          
      <!-- Description -->
      <div class="media-album-description">
        <!-- Thumbnail Image -->
        <?php if ($this->params->get('item_meta_thumbnail') != 'hide') :?>
        <div class="media-item">
          <?php if ($canEdit || $canDelete): ?>
          <!-- Actions -->
          <ul class="media-nav">
            <li><a href="#" class="pagenav-manage"><?php echo JText::_('COM_HWDMS_MANAGE'); ?> </a>
              <ul class="media-subnav">
                <?php if ($canEdit) : ?>
                <li><?php echo JHtml::_('hwdicon.edit', 'group', $this->group, $this->params); ?></li>
                <?php endif; ?>
                <?php if ($canEditState) : ?>
                <?php if ($this->group->published != '1') : ?>
                <li><?php echo JHtml::_('hwdicon.publish', 'group', $this->group, $this->params); ?></li>
                <?php else : ?>
                <li><?php echo JHtml::_('hwdicon.unpublish', 'group', $this->group, $this->params); ?></li>
                <?php endif; ?>
                <?php endif; ?>
                <?php if ($canDelete) : ?>
                <li><?php echo JHtml::_('hwdicon.delete', 'group', $this->group, $this->params); ?></li>
                <?php endif; ?>
              </ul>
            </li>
          </ul>
          <?php endif; ?>
          <!-- Media Type -->
          <?php if ($this->params->get('item_meta_type_icon') != 'hide') :?>
          <div class="media-item-format-3">
             <img src="<?php echo JHtml::_('hwdicon.overlay', 3); ?>" alt="Group" />
          </div>
          <?php endif; ?>
          <img src="<?php echo JRoute::_(hwdMediaShareDownloads::thumbnail($this->group, 3)); ?>" border="0" alt="<?php echo $this->escape($this->group->title); ?>" style="width:120px;" />
        </div>
        <?php endif; ?>
        <?php if ($this->params->get('item_meta_author') != 'hide' || $this->params->get('item_meta_created') != 'hide' || $this->params->get('item_meta_hits') != 'hide' || $this->params->get('item_meta_likes') != 'hide' || $this->params->get('item_meta_report') != 'hide') : ?>
        <dl class="article-info">
          <dt class="article-info-term"><?php echo JText::_('COM_HWDMS_DETAILS'); ?> </dt>
          <?php if ($this->params->get('item_meta_author') != 'hide') :?>
            <dd class="media-info-createdby"> <?php echo JText::sprintf('COM_HWDMS_CREATED_BY', '<a href="'.JRoute::_(hwdMediaShareHelperRoute::getUserRoute($this->group->created_user_id)).'">'.htmlspecialchars($this->group->author, ENT_COMPAT, 'UTF-8').'</a>'); ?></dd>
          <?php endif; ?>
          <?php if ($this->params->get('item_meta_created') != 'hide') :?>
            <dd class="media-info-created"> <?php echo JHtml::_('date.relative', $this->group->created); ?></dd>
          <?php endif; ?>
          <?php if ($this->params->get('item_meta_hits') != 'hide') :?>
            <dd class="media-info-hits"> <?php echo JText::_('COM_HWDMS_VIEWS'); ?> (<?php echo (int) $this->group->hits; ?>)</dd>
          <?php endif; ?> 
          <?php if ($this->params->get('item_meta_likes') != 'hide') :?>
            <dd class="media-info-like"> <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=group.like&id=' . $this->group->id . '&return=' . $this->return . '&tmpl=component'); ?>"><?php echo JText::_('COM_HWDMS_LIKE'); ?></a> (<?php echo $this->escape($this->group->likes); ?>) <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=group.dislike&id=' . $this->group->id . '&return=' . $this->return . '&tmpl=component'); ?>"><?php echo JText::_('COM_HWDMS_DISLIKE'); ?></a> (<?php echo $this->escape($this->group->dislikes); ?>) </dd>
          <?php endif; ?> 
          <?php if ($this->params->get('item_meta_report') != 'hide') :?>
            <dd class="media-info-report"> <a title="<?php echo JText::_('COM_HWDMS_REPORT'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=groupform.report&id=' . $this->group->id . '&return=' . $this->return . '&tmpl=component'); ?>" class="modal" rel="{handler: 'iframe', size: {<?php echo $this->utilities->modalSize(); ?>}}"><?php echo JText::_('COM_HWDMS_REPORT'); ?> </a> </dd>
          <?php endif; ?> 
        </dl>
        <?php endif; ?>  
        <!-- Custom fields -->
        <dl class="media-article-info">
        <?php foreach ($this->group->customfields['fields'] as $group => $groupFields) : ?>
          <dt class="media-article-info-term"><?php echo JText::_( $group ); ?></dt>
          <?php foreach ($groupFields as $field) :
          $field	= JArrayHelper::toObject ( $field );
          $field->value = $this->escape( $field->value );
          ?>
            <dd class="media-createdby" title="" class="hasTip" for="jform_<?php echo $field->id;?>" id="jform_<?php echo $field->id;?>-lbl">
              <?php echo JText::_( $field->name );?> <?php echo $this->escape($field->value); ?>
            </dd>
          <?php endforeach; ?>
        <?php endforeach; ?>
        </dl>
        <?php if ($this->params->get('item_meta_description') != 'hide') :?>
        <div class="clear"></div> 
        <?php echo JHtml::_('content.prepare', $this->group->description); ?>
        <?php endif; ?> 
        <div class="clear"></div>
      </div>
      <div class="clear"></div>            

          
        <h3 class="media-user-browse">Browse this group</h3>
        <ul>
            <?php if ($this->params->get('groupitem_member_count') != 'hide') :?><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=group&id='.$this->group->id.'&layout=members'); ?>"><li><?php echo (int) $this->group->nummembers; ?> <?php echo JText::_('COM_HWDMS_MEMBERS'); ?></a></li><?php endif; ?>
            <?php if ($this->params->get('groupitem_media_count') != 'hide') :?><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=group&id='.$this->group->id.'&layout=media'); ?>"><li><?php echo (int) $this->group->nummedia; ?> <?php echo JText::_('COM_HWDMS_MEDIA'); ?></a></li><?php endif; ?>
       </ul>          
  
        <h3 class="media-user-browse">Newest members</h3>
            <?php if ($this->params->get('groupitem_group_members') != 'hide') :?>  
              <div class="media-gallery-view">
                <?php if (count($this->members) == 0) : ?>
                <?php echo JText::_('COM_HWDMS_NO_MEMBERS'); ?>
                <?php endif; ?>
                <?php foreach ($this->members as $id => &$item) : ?>
                    <a class="image-left hasTip" title="<?php echo $this->escape(JHtmlString::truncate($item->title, $this->params->get('list_title_truncate'))); ?>::" href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getUserRoute($item->id)); ?>"><img width="75" height="75" border="0" src="<?php echo JRoute::_(hwdMediaShareDownloads::thumbnail($item, 5)); ?>" /></a>
                <?php endforeach; ?>
              </div>
              <div class="clear"></div>
              <p class="readmore">
                <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=group&id='.$this->group->id.'&layout=members'); ?>">
                  <?php echo JText::_('COM_HWDMS_VIEW_ALL_MEMBERS'); ?>
                </a>
              </p>
            <?php endif; ?>          

          <?php if ($this->params->get('groupitem_group_activity') != 'hide') :?>  
  <?php echo $this->loadTemplate('activities'); ?>
  <div class="clear"></div>
<?php endif; ?> 
            
	</div>
      </div> 
      <div class="clear"></div>

      
      
      
      

    </div>
    <div class="media-group-container">
     
      <div class="items-row cols-2 row-0">

        <div class="item column-2">

	  <div class="item-separator"></div>
	</div>
      <span class="row-separator"></span>
      </div>
    </div>
  </div>
