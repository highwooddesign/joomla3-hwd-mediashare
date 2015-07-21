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
$canEdit = ($user->authorise('core.edit', 'com_hwdmediashare.album.'.$this->album->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.album.'.$this->album->id) && ($this->album->created_user_id == $user->id)));
$canEditState = $user->authorise('core.edit.state', 'com_hwdmediashare.album.'.$this->album->id);
$canDelete = ($user->authorise('core.delete', 'com_hwdmediashare.album.'.$this->album->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.album.'.$this->album->id) && ($this->album->created_user_id == $user->id)));
$canAddMedia = (($user->authorise('hwdmediashare.upload', 'com_hwdmediashare') || $user->authorise('hwdmediashare.import', 'com_hwdmediashare')) && ($this->album->created_user_id == $user->id));
$canManageMedia = ($this->album->created_user_id == $user->id);
?>
<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm">
  <div id="hwd-container" class="<?php echo $this->pageclass_sfx;?>"> <a name="top" id="top"></a>
    <!-- Media Navigation -->
    <?php echo hwdMediaShareHelperNavigation::getInternalNavigation(); ?>
    <!-- Media Header -->
    <div class="media-header">
      <?php if ($this->params->get('item_meta_title') != '0') :?>
        <h2 class="media-album-title"><?php echo $this->escape($this->params->get('page_heading')); ?></h2>
      <?php endif; ?>     
      <!-- Buttons -->
      <div class="btn-group pull-right">
        <?php if ($canAddMedia): ?>
          <a title="<?php echo JText::_('COM_HWDMS_ADD_MEDIA'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=upload&album_id='.(int)$this->album->id.'&return=' . $this->return); ?>" class="btn"><i class="icon-plus"></i> <?php echo JText::_('COM_HWDMS_ADD_MEDIA'); ?></a>
        <?php endif; ?>
        <?php if ($canManageMedia): ?>
          <a title="<?php echo JText::_('COM_HWDMS_MANAGE_MEDIA'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=albummedia&tmpl=component&album_id=' . $this->album->id); ?>" class="btn media-popup-iframe-page" ><?php echo JText::_('COM_HWDMS_MANAGE_MEDIA'); ?></a>
        <?php endif; ?>
        <?php if ($this->album->featured && $this->params->get('item_meta_featured_status') != '0'): ?>
          <div class="btn btn-info btn-noevent"><?php echo JText::_('COM_HWDMS_FEATURED'); ?></div>
        <?php endif; ?>
        <?php if ($this->album->status != 1) : ?>
          <div class="btn btn-danger btn-noevent"><?php echo $this->utilities->getReadableStatus($this->album); ?></div>
        <?php endif; ?>   
        <?php if ($this->album->published != 1) : ?>
          <div class="btn btn-danger btn-noevent"><?php echo JText::_('COM_HWDMS_UNPUBLISHED'); ?></div>
        <?php endif; ?> 
        <?php if ($this->params->get('item_meta_report') != '0' && $this->album->created_user_id != $user->id): ?>                  
          <a title="<?php echo JText::_('COM_HWDMS_REPORT'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=albumform.report&id=' . $this->album->id . '&return=' . $this->return . '&tmpl=component'); ?>" class="btn media-popup-iframe-form"><i class="icon-flag"></i> <?php echo JText::_('COM_HWDMS_REPORT'); ?></a>
        <?php endif; ?>    
        <?php if ($this->params->get('list_details_button') != '0') : ?>
          <a title="<?php echo JText::_('COM_HWDMS_DETAILS'); ?>" href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getAlbumRoute($this->album->id, array('display' => 'details'))); ?>" class="btn"><i class="icon-image"></i></a>
        <?php endif; ?>
        <?php if ($this->params->get('list_gallery_button') != '0') : ?>
          <a title="<?php echo JText::_('COM_HWDMS_GALLERY'); ?>" href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getAlbumRoute($this->album->id, array('display' => 'gallery'))); ?>" class="btn"><i class="icon-grid"></i></a>
        <?php endif; ?>
        <?php if ($this->params->get('list_list_button') != '0') : ?>
          <a title="<?php echo JText::_('COM_HWDMS_LIST'); ?>" href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getAlbumRoute($this->album->id, array('display' => 'list'))); ?>" class="btn"><i class="icon-list"></i></a>
        <?php endif; ?>
        <?php if ($canEdit || $canDelete): ?>
        <?php
        // Create dropdown items
        if ($canEdit) : 
          JHtml::_('hwddropdown.edit', $this->album->id, 'albumform'); 
        endif;    
        if ($canEditState) :
          $action = $this->album->published == 1 ? 'unpublish' : 'publish';
          JHtml::_('hwddropdown.' . $action, $this->album->id, 'albums'); 
        endif; 
        if ($canDelete && $this->album->published != -2) : 
          JHtml::_('hwddropdown.delete', $this->album->id, 'albums'); 
        endif;       
        // Render dropdown list       
        echo JHtml::_('hwddropdown.render', $this->escape($this->album->title));
        ?>
        <?php endif; ?>
      </div>        
      <div class="clear"></div>
      <!-- Search Filters -->
      <?php echo JLayoutHelper::render('search_tools', array('view' => $this), JPATH_ROOT.'/components/com_hwdmediashare/libraries/layouts'); ?>
      <div class="clear"></div>
    </div>
    <div class="media-<?php echo $this->display; ?>-view">
      <?php if (empty($this->items)) : ?>
        <div class="alert alert-no-items">
          <?php echo JText::_('COM_HWDMS_NOTHING_TO_SHOW'); ?>
        </div>
      <?php else : ?>
        <?php echo JLayoutHelper::render('media_' . $this->display, $this, JPATH_ROOT.'/components/com_hwdmediashare/libraries/layouts'); ?>
      <?php endif; ?>        
    </div>    
    <!-- Pagination -->
    <div class="pagination"> <?php echo $this->pagination->getPagesLinks(); ?> </div>
    <div class="clear"></div>
    <!-- Description -->
    <div class="well media-album-description">
      <?php if ($this->params->get('item_meta_description') != '0') :?>
        <?php echo JHtml::_('content.prepare', $this->album->description); ?>
      <?php endif; ?> 
      <?php if ($this->params->get('item_meta_likes') != '0' || $this->params->get('item_meta_dislikes') != '0') :?>
      <div class="pull-right">
        <div class="media-info-likes pull-right">      
          <?php if ($this->params->get('item_meta_likes') != '0') :?><i class="icon-thumbs-up"></i> <span id="media-likes"><?php echo (int) $this->album->likes; ?></span><?php endif; ?>
          <?php if ($this->params->get('item_meta_dislikes') != '0') :?><i class="icon-thumbs-down"></i> <span id="media-dislikes"><?php echo (int) $this->album->dislikes; ?></span><?php endif; ?>
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
              <?php echo JText::sprintf('COM_HWDMS_BY_X_USER', '<a href="'.JRoute::_(hwdMediaShareHelperRoute::getUserRoute($this->album->created_user_id)).'">'.htmlspecialchars($this->album->author, ENT_COMPAT, 'UTF-8').'</a>'); ?>
            </span>
          <?php endif; ?>
          <?php if ($this->params->get('item_meta_created') != '0') : ?>
            <span class="media-info-created">
              <?php echo JHtml::_('hwddate.relative', $this->album->created); ?>
            </span>
          <?php endif; ?>
        </dd>
        <?php endif; ?>
        <?php if ($this->params->get('item_meta_hits') != '0') :?>
          <dd class="media-info-hits label"><?php echo JText::sprintf('COM_HWDMS_X_VIEWS', number_format((int) $this->album->hits)); ?></dd>
        <?php endif; ?>      
        <?php if ($this->params->get('item_meta_media_count') != '0') :?>
          <dd class="media-info-count label"><?php echo JText::plural('COM_HWDMS_X_MEDIA_COUNT', (int) $this->album->nummedia); ?></dd>
        <?php endif; ?>        
        <div class="clearfix"></div>         
      </dl>
      <?php endif; ?>  
      <?php if ($this->params->get('item_meta_likes') != '0' || $this->params->get('item_meta_dislikes') != '0' || $this->params->get('item_meta_report') != '0') : ?>
        <div class="btn-group">
          <?php if ($this->params->get('item_meta_likes') != '0') : ?>
            <a title="<?php echo JText::_('COM_HWDMS_LIKE'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=albums.like&id=' . $this->album->id . '&return=' . $this->return . '&tmpl=component&'.JSession::getFormToken().'=1'); ?>" class="btn btn-mini" id="album-like-btn" data-media-task="like" data-media-id="<?php echo $this->album->id; ?>" data-media-return="<?php echo $this->return; ?>" data-media-token="<?php echo JSession::getFormToken(); ?>"><i class="icon-thumbs-up"></i> <?php echo JText::_('COM_HWDMS_LIKE'); ?></a>
          <?php endif; ?>
          <?php if ($this->params->get('item_meta_dislikes') != '0') : ?>
            <a title="<?php echo JText::_('COM_HWDMS_DISLIKE'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=albums.dislike&id=' . $this->album->id . '&return=' . $this->return . '&tmpl=component&'.JSession::getFormToken().'=1'); ?>" class="btn btn-mini" id="album-dislike-btn" data-media-task="dislike" data-media-id="<?php echo $this->album->id; ?>" data-media-return="<?php echo $this->return; ?>" data-media-token="<?php echo JSession::getFormToken(); ?>"><i class="icon-thumbs-down"></i> <?php echo JText::_('COM_HWDMS_DISLIKE'); ?></a>
          <?php endif; ?>
          <?php if ($this->params->get('item_meta_report') != '0') : ?>
            <a title="<?php echo JText::_('COM_HWDMS_REPORT'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=albumform.report&id=' . $this->album->id . '&return=' . $this->return . '&tmpl=component'); ?>" class="btn btn-mini media-popup-iframe-form" id="album-report-btn"><i class="icon-flag"></i> <?php echo JText::_('COM_HWDMS_REPORT'); ?></a>
          <?php endif; ?>  
        </div>          
      <?php endif; ?>
      <!-- Tags -->
      <?php if ($this->params->get('show_tags', 1) && !empty($this->album->tags)) : ?>
	<?php $this->album->tagLayout = new JLayoutFile('joomla.content.tags'); ?>
	<?php echo $this->album->tagLayout->render($this->album->tags->itemTags); ?>
      <?php endif; ?>
      <!-- Custom fields -->
      <dl class="media-info">
        <?php foreach($this->album->customfields->fields as $group => $fields) : ?>
          <dt class="media-info-group"><?php echo JText::_($group); ?> </dt>
          <?php foreach($fields as $field) : ?>
            <dd class="media-info-field-<?php echo $field->fieldcode; ?>"><?php echo $field->name; ?>: <?php echo $this->album->customfields->display($field); ?></dd>
          <?php endforeach; ?>
        <?php endforeach; ?>
      </dl>
      <div class="clear"></div>        
    </div>
  </div>
</form>
