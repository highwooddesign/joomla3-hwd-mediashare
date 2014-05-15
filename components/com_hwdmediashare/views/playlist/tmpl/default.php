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
JHtml::_('HwdPopup.form');
JHtml::_('HwdPopup.page');

$user = JFactory::getUser();
$canEdit = ($user->authorise('core.edit', 'com_hwdmediashare.playlist.'.$this->playlist->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.playlist.'.$this->playlist->id) && ($this->playlist->created_user_id == $user->id)));
$canEditState = $user->authorise('core.edit.state', 'com_hwdmediashare.playlist.'.$this->playlist->id);
$canDelete = ($user->authorise('core.delete', 'com_hwdmediashare.playlist.'.$this->playlist->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.playlist.'.$this->playlist->id) && ($this->playlist->created_user_id == $user->id)));
$canAddMedia = (($user->authorise('hwdmediashare.upload','com_hwdmediashare') || $user->authorise('hwdmediashare.import','com_hwdmediashare')) && ($this->playlist->created_user_id == $user->id));
$canManageMedia = ($this->playlist->created_user_id == $user->id);
?>
<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm">
  <div id="hwd-container"> <a name="top" id="top"></a>
    <!-- Media Navigation -->
    <?php echo hwdMediaShareHelperNavigation::getInternalNavigation(); ?>
    <!-- Media Header -->
    <div class="media-header">
      <?php if ($this->params->get('item_meta_title') != 'hide') :?>
        <h2 class="media-playlist-title"><?php echo $this->escape($this->playlist->title); ?></h2>
      <?php endif; ?>
      <!-- Buttons -->
      <div class="btn-group pull-right">
        <?php if ($canAddMedia): ?>
          <a title="<?php echo JText::_('COM_HWDMS_ADD_MEDIA'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=upload&tmpl=component&playlist_id='.(int)$this->playlist->id); ?>" class="btn"><i class="icon-plus"></i> <?php echo JText::_('COM_HWDMS_ADD_MEDIA'); ?></a>
        <?php endif; ?>
        <?php if ($canManageMedia): ?>
          <a title="<?php echo JText::_('COM_HWDMS_MANAGE_MEDIA'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=playlistmedia&tmpl=component&playlist_id=' . $this->playlist->id); ?>" class="btn media-popup-page" ><?php echo JText::_('COM_HWDMS_MANAGE_MEDIA'); ?></a>
        <?php endif; ?>          
        <?php if ($this->playlist->featured) : ?>
          <div class="btn btn-info"><i class="icon-star"></i> <?php echo JText::_('COM_HWDMS_FEATURED'); ?></div>
        <?php endif; ?>
        <?php if ($this->playlist->status != 1) : ?>
          <div class="btn btn-danger"><i class="icon-notification"></i> <?php echo $this->utilities->getReadableStatus($this->playlist); ?></div>
        <?php endif; ?>
        <?php if ($this->playlist->published != 1) : ?>
          <div class="btn btn-danger"><i class="icon-unpublish"></i> <?php echo JText::_('COM_HWDMS_UNPUBLISHED'); ?></div>
        <?php endif; ?>           
        <a title="<?php echo JText::_('COM_HWDMS_PLAY_NOW'); ?>" href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getSlideshowRoute(null, array('playlist_id' => $this->playlist->id), false)); ?>" class="btn"><i class="icon-play"></i> <?php echo JText::_('COM_HWDMS_PLAY_NOW'); ?></a>            
        <?php if ($this->params->get('item_meta_report') != 'hide'): ?>  
          <a title="<?php echo JText::_('COM_HWDMS_REPORT'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=playlistform.report&id=' . $this->playlist->id . '&return=' . $this->return . '&tmpl=component'); ?>" class="btn media-popup-form"><i class="icon-warning"></i> <?php echo JText::_('COM_HWDMS_REPORT'); ?></a>
        <?php endif; ?>
        <?php if ($canEdit || $canDelete): ?>
        <?php
        // Create dropdown items
        if ($canEdit) : 
          JHtml::_('hwddropdown.edit', $this->playlist->id, 'playlistform'); 
        endif;           
        if ($canEditState) :
          $action = $this->playlist->published ? 'unpublish' : 'publish';
          JHtml::_('hwddropdown.' . $action, $this->playlist->id, 'playlists'); 
        endif; 
        if ($canEdit) : 
          JHtml::_('hwddropdown.delete', $this->playlist->id, 'playlists'); 
        endif;         
        // Render dropdown list       
        echo JHtml::_('hwddropdown.render', $this->escape($this->playlist->title));
        ?>
        <?php endif; ?>
      </div>        
      <div class="clear"></div>
      <!-- Search Filters -->
      <?php echo JLayoutHelper::render('search_tools', array('view' => $this), JPATH_ROOT.'/components/com_hwdmediashare/libraries/layouts'); ?>
      <div class="clear"></div>
    </div>
    <div class="media-list-view">
      <?php echo JLayoutHelper::render('media_list', $this, JPATH_ROOT.'/components/com_hwdmediashare/libraries/layouts'); ?>
    </div>           
    <!-- Pagination -->
    <div class="pagination"> <?php echo $this->pagination->getPagesLinks(); ?> </div>
    <div class="clear"></div>
    <!-- Description -->
    <div class="well media-playlist-description"> 
      <?php if ($this->params->get('item_meta_description') != 'hide') :?>
        <?php echo JHtml::_('content.prepare', $this->playlist->description); ?>
      <?php endif; ?>         
      <?php if ($this->params->get('item_meta_author') != 'hide' || $this->params->get('item_meta_created') != 'hide' || $this->params->get('item_meta_hits') != 'hide' || $this->params->get('item_meta_likes') != 'hide' || $this->params->get('item_meta_report') != 'hide') : ?>
      <dl class="media-info">
        <dt class="media-info-term"><?php echo JText::_('COM_HWDMS_DETAILS'); ?> </dt>
        <?php if ($this->params->get('list_meta_author') != 'hide' || $this->params->get('list_meta_created') != 'hide') : ?>
          <dd class="media-info-createdby">
            <?php if ($this->params->get('list_meta_author') != 'hide') : ?><?php echo JText::sprintf('COM_HWDMS_CREATED_BY', '<a href="'.JRoute::_(hwdMediaShareHelperRoute::getUserRoute($this->playlist->created_user_id)).'">'.htmlspecialchars($this->playlist->author, ENT_COMPAT, 'UTF-8').'</a>'); ?><?php endif; ?><?php if ($this->params->get('list_meta_created') != 'hide') : ?>, <?php echo JHtml::_('date.relative', $this->playlist->created); ?><?php endif; ?>
          </dd>
        <?php endif; ?>
        <?php if ($this->params->get('item_meta_media_count') != 'hide') :?>
          <dd class="media-info-count"> <?php echo JText::_('COM_HWDMS_MEDIA'); ?> (<?php echo (int) $this->playlist->nummedia; ?>)</dd>
        <?php endif; ?>
        <?php if ($this->params->get('item_meta_hits') != 'hide') :?>
          <dd class="media-info-hits"> <?php echo JText::_('COM_HWDMS_VIEWS'); ?> (<?php echo (int) $this->playlist->hits; ?>)</dd>
        <?php endif; ?>   
        <?php if ($this->params->get('item_meta_likes') != 'hide') :?>
          <dd class="media-info-like"> <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=playlists.like&id=' . $this->playlist->id . '&return=' . $this->return . '&tmpl=component&'.JSession::getFormToken().'=1'); ?>"><?php echo JText::_('COM_HWDMS_LIKE'); ?></a> (<?php echo $this->escape($this->playlist->likes); ?>) <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=playlists.dislike&id=' . $this->playlist->id . '&return=' . $this->return . '&tmpl=component&'.JSession::getFormToken().'=1'); ?>"><?php echo JText::_('COM_HWDMS_DISLIKE'); ?></a> (<?php echo $this->escape($this->playlist->dislikes); ?>) </dd>
        <?php endif; ?>  
        <?php if ($this->params->get('item_meta_report') != 'hide') :?>
          <dd class="media-info-report"> <a title="<?php echo JText::_('COM_HWDMS_REPORT'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=playlistform.report&id=' . $this->playlist->id . '&return=' . $this->return . '&tmpl=component'); ?>" class="media-popup-form"><?php echo JText::_('COM_HWDMS_REPORT'); ?> </a> </dd>
        <?php endif; ?>             
      </dl>
      <?php endif; ?>  
      <!-- Tags -->
      <?php if ($this->params->get('show_tags', 1) && !empty($this->playlist->tags)) : ?>
	<?php $this->playlist->tagLayout = new JLayoutFile('joomla.content.tags'); ?>
	<?php echo $this->playlist->tagLayout->render($this->playlist->tags->itemTags); ?>
      <?php endif; ?>
      <!-- Custom fields -->
      <dl class="media-media-info">
      <?php foreach ($this->playlist->customfields['fields'] as $group => $groupFields) : ?>
        <dt class="media-media-info-term"><?php echo JText::_( $group ); ?></dt>
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
      <div class="clear"></div> 
    </div>
  </div>
</form>