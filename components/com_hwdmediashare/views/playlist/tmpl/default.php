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
$canEdit = ($user->authorise('core.edit', 'com_hwdmediashare.playlist.'.$this->playlist->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.playlist.'.$this->playlist->id) && ($this->playlist->created_user_id == $user->id)));
$canEditState = $user->authorise('core.edit.state', 'com_hwdmediashare.playlist.'.$this->playlist->id);
$canDelete = ($user->authorise('core.delete', 'com_hwdmediashare.playlist.'.$this->playlist->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.playlist.'.$this->playlist->id) && ($this->playlist->created_user_id == $user->id)));
$canAddMedia = (($user->authorise('hwdmediashare.upload', 'com_hwdmediashare') || $user->authorise('hwdmediashare.import', 'com_hwdmediashare')) && ($this->playlist->created_user_id == $user->id));
$canManageMedia = ($this->playlist->created_user_id == $user->id);
?>
<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm">
  <div id="hwd-container" class="<?php echo $this->pageclass_sfx;?>"> <a name="top" id="top"></a>
    <!-- Media Navigation -->
    <?php echo hwdMediaShareHelperNavigation::getInternalNavigation(); ?>
    <!-- Media Header -->
    <div class="media-header">
      <?php if ($this->params->get('item_meta_title') != '0') :?>
        <h2 class="media-playlist-title"><?php echo $this->escape($this->params->get('page_heading')); ?></h2>
      <?php endif; ?>    
      <!-- Buttons -->
      <div class="btn-group pull-right">
        <?php if ($canAddMedia): ?>
          <a title="<?php echo JText::_('COM_HWDMS_ADD_MEDIA'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=upload&tmpl=component&playlist_id='.(int)$this->playlist->id); ?>" class="btn"><i class="icon-plus"></i> <?php echo JText::_('COM_HWDMS_ADD_MEDIA'); ?></a>
        <?php endif; ?>
        <?php if ($canManageMedia): ?>
          <a title="<?php echo JText::_('COM_HWDMS_MANAGE_MEDIA'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=playlistmedia&tmpl=component&playlist_id=' . $this->playlist->id); ?>" class="btn media-popup-iframe-page" ><?php echo JText::_('COM_HWDMS_MANAGE_MEDIA'); ?></a>
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
        <?php if (count($this->items)): ?>  
          <a title="<?php echo JText::_('COM_HWDMS_PLAY_NOW'); ?>" href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getMediaItemRoute(reset($this->items)->id)); ?>" class="btn"><i class="icon-play"></i> <?php echo JText::_('COM_HWDMS_PLAY_NOW'); ?></a>            
        <?php endif; ?>
        <?php if ($this->params->get('item_meta_report') != '0'): ?>  
          <a title="<?php echo JText::_('COM_HWDMS_REPORT'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=playlistform.report&id=' . $this->playlist->id . '&return=' . $this->return . '&tmpl=component'); ?>" class="btn media-popup-iframe-form"><i class="icon-flag"></i> <?php echo JText::_('COM_HWDMS_REPORT'); ?></a>
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
      <?php if (empty($this->items)) : ?>
        <div class="alert alert-no-items">
          <?php echo JText::_('COM_HWDMS_NOTHING_TO_SHOW'); ?>
        </div>
      <?php else : ?>
        <?php echo JLayoutHelper::render('media_list', $this, JPATH_ROOT.'/components/com_hwdmediashare/libraries/layouts'); ?>
      <?php endif; ?> 
    </div>           
    <!-- Pagination -->
    <div class="pagination"> <?php echo $this->pagination->getPagesLinks(); ?> </div>
    <div class="clear"></div>
    <!-- Description -->
    <div class="well media-playlist-description"> 
      <?php if ($this->params->get('item_meta_description') != '0') :?>
        <?php echo JHtml::_('content.prepare', $this->playlist->description); ?>
      <?php endif; ?>         
      <?php if ($this->params->get('item_meta_author') != '0' || $this->params->get('item_meta_created') != '0' || $this->params->get('item_meta_hits') != '0' || $this->params->get('item_meta_likes') != '0' || $this->params->get('item_meta_report') != '0') : ?>
      <dl class="media-info">
        <dt class="media-info-term"><?php echo JText::_('COM_HWDMS_DETAILS'); ?> </dt>
        <?php if ($this->params->get('list_meta_likes') != '0') :?>
          <dd class="media-info-likes">
            <i class="icon-thumbs-up"></i> <span id="media-likes"><?php echo (int) $this->playlist->likes; ?></span>
            <i class="icon-thumbs-down"></i> <span id="media-dislikes"><?php echo (int) $this->playlist->dislikes; ?></span>
          </dd>
        <?php endif; ?>        
        <?php if ($this->params->get('list_meta_author') != '0' || $this->params->get('list_meta_created') != '0') : ?>
        <dd class="media-info-meta">
          <?php if ($this->params->get('list_meta_author') != '0') : ?>
            <span class="media-info-createdby">
              <?php echo JText::sprintf('COM_HWDMS_BY_X_USER', '<a href="'.JRoute::_(hwdMediaShareHelperRoute::getUserRoute($this->playlist->created_user_id)).'">'.htmlspecialchars($this->playlist->author, ENT_COMPAT, 'UTF-8').'</a>'); ?>
            </span>
          <?php endif; ?>
          <?php if ($this->params->get('list_meta_created') != '0') : ?>
            <span class="media-info-created">
              <?php echo JHtml::_('hwddate.relative', $this->playlist->created); ?>
            </span>
          <?php endif; ?>
        </dd>
        <?php endif; ?>
        <?php if ($this->params->get('item_meta_hits') != '0') :?>
          <dd class="media-info-hits label"><?php echo JText::sprintf('COM_HWDMS_X_VIEWS', number_format((int) $this->playlist->hits)); ?></dd>
        <?php endif; ?>      
        <?php if ($this->params->get('item_meta_media_count') != '0') :?>
          <dd class="media-info-count label"><?php echo JText::plural('COM_HWDMS_X_MEDIA_COUNT', (int) $this->playlist->nummedia); ?></dd>
        <?php endif; ?>        
        <div class="clearfix"></div>
        <?php if ($this->params->get('item_meta_likes') != '0') :?>
          <dd class="media-info-like"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=playlists.like&id=' . $this->playlist->id . '&return=' . $this->return . '&tmpl=component&'.JSession::getFormToken().'=1'); ?>"><?php echo JText::_('COM_HWDMS_LIKE'); ?></a></dd>
          <dd class="media-info-dislike"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=playlists.dislike&id=' . $this->playlist->id . '&return=' . $this->return . '&tmpl=component&'.JSession::getFormToken().'=1'); ?>"><?php echo JText::_('COM_HWDMS_DISLIKE'); ?></a></dd>
        <?php endif; ?>   
        <?php if ($this->params->get('item_meta_report') != '0') :?>
          <dd class="media-info-report"><a title="<?php echo JText::_('COM_HWDMS_REPORT'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=playlistform.report&id=' . $this->playlist->id . '&return=' . $this->return . '&tmpl=component'); ?>" class="media-popup-iframe-form"><?php echo JText::_('COM_HWDMS_REPORT'); ?></a></dd>
        <?php endif; ?>          
      </dl>
      <?php endif; ?>  
      <!-- Tags -->
      <?php if ($this->params->get('show_tags', 1) && !empty($this->playlist->tags)) : ?>
	<?php $this->playlist->tagLayout = new JLayoutFile('joomla.content.tags'); ?>
	<?php echo $this->playlist->tagLayout->render($this->playlist->tags->itemTags); ?>
      <?php endif; ?>
      <!-- Custom fields -->
      <dl class="media-info">
        <?php foreach($this->playlist->customfields->fields as $group => $fields) : ?>
          <dt class="media-info-group"><?php echo JText::_($group); ?> </dt>
          <?php foreach($fields as $field) : ?>
            <dd class="media-info-<?php echo $field->fieldcode; ?>"><?php echo $field->name; ?>: <?php echo $this->playlist->customfields->display($field); ?></dd>
          <?php endforeach; ?>
        <?php endforeach; ?>
      </dl>
      <div class="clear"></div> 
    </div>
  </div>
</form>