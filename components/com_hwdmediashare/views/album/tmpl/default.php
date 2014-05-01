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

$user = JFactory::getUser();
$canEdit = ($user->authorise('core.edit', 'com_hwdmediashare.album.'.$this->album->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.album.'.$this->album->id) && ($this->album->created_user_id == $user->id)));
$canEditState = $user->authorise('core.edit.state', 'com_hwdmediashare.album.'.$this->album->id);
$canDelete = ($user->authorise('core.delete', 'com_hwdmediashare.album.'.$this->album->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.album.'.$this->album->id) && ($this->album->created_user_id == $user->id)));
$canAddMedia = (($user->authorise('hwdmediashare.upload','com_hwdmediashare') || $user->authorise('hwdmediashare.import','com_hwdmediashare')) && ($this->album->created_user_id == $user->id));
?>
<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm">
  <div id="hwd-container" class="<?php echo $this->pageclass_sfx;?>"> <a name="top" id="top"></a>
    <!-- Media Navigation -->
    <?php echo hwdMediaShareHelperNavigation::getInternalNavigation(); ?>
    <!-- Media Header -->
    <div class="media-header">
      <?php if ($this->params->get('item_meta_title') != 'hide' && $this->params->get('show_page_heading', 1)) :?>
        <h2 class="media-album-title"><?php echo $this->escape($this->params->get('page_heading')); ?></h2>
      <?php endif; ?>     
      <!-- Buttons -->
      <div class="btn-group pull-right">
        <?php if ($canAddMedia): ?>
          <a title="<?php echo JText::_('COM_HWDMS_ADD_MEDIA'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=upload&album_id='.(int)$this->album->id.'&return=' . $this->return); ?>" class="btn"><i class="icon-plus"></i> <?php echo JText::_('COM_HWDMS_ADD_MEDIA'); ?></a>
        <?php endif; ?>
        <?php if ($this->params->get('item_meta_report') != 'hide' && $this->album->created_user_id != $user->id): ?>                  
          <a title="<?php echo JText::_('COM_HWDMS_REPORT'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=albumform.report&id=' . $this->album->id . '&return=' . $this->return . '&tmpl=component'); ?>" class="btn media-popup-form"><i class="icon-warning"></i> <?php echo JText::_('COM_HWDMS_REPORT'); ?></a>
        <?php endif; ?>    
        <?php if ($this->params->get('list_details_button') != 'hide') : ?>
          <a title="<?php echo JText::_('COM_HWDMS_DETAILS'); ?>" href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getSelfRoute('details')); ?>" class="btn"><i class="icon-image"></i></a>
        <?php endif; ?>
        <?php if ($this->params->get('list_gallery_button') != 'hide') : ?>
          <a title="<?php echo JText::_('COM_HWDMS_GALLERY'); ?>" href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getSelfRoute('gallery')); ?>" class="btn"><i class="icon-grid"></i></a>
        <?php endif; ?>
        <?php if ($this->params->get('list_list_button') != 'hide') : ?>
          <a title="<?php echo JText::_('COM_HWDMS_LIST'); ?>" href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getSelfRoute('list')); ?>" class="btn"><i class="icon-list"></i></a>
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
        <?php echo JLayoutHelper::render('media_' . $this->display, $this, JPATH_ROOT.'/components/com_hwdmediashare/libraries/layouts'); ?>
    </div>    
    <!-- Pagination -->
    <div class="pagination"> <?php echo $this->pagination->getPagesLinks(); ?> </div>
    <div class="clear"></div>
    <!-- Description -->
    <div class="well media-album-description">
      <?php if ($this->params->get('item_meta_description') != 'hide') :?>
        <?php echo JHtml::_('content.prepare', $this->album->description); ?>
      <?php endif; ?> 
      <?php if ($this->params->get('item_meta_media_count') != 'hide' || $this->params->get('item_meta_author') != 'hide' || $this->params->get('item_meta_created') != 'hide' || $this->params->get('item_meta_hits') != 'hide' || $this->params->get('item_meta_likes') != 'hide' || $this->params->get('item_meta_report') != 'hide') : ?>
      <dl class="media-info">
        <dt class="media-info-term"><?php echo JText::_('COM_HWDMS_DETAILS'); ?> </dt>
        <?php if ($this->params->get('list_meta_author') != 'hide' || $this->params->get('list_meta_created') != 'hide') : ?>
          <dd class="media-info-createdby">
            <?php if ($this->params->get('list_meta_author') != 'hide') : ?><?php echo JText::sprintf('COM_HWDMS_CREATED_BY', '<a href="'.JRoute::_(hwdMediaShareHelperRoute::getUserRoute($this->album->created_user_id)).'">'.htmlspecialchars($this->album->author, ENT_COMPAT, 'UTF-8').'</a>'); ?><?php endif; ?><?php if ($this->params->get('list_meta_created') != 'hide') : ?>, <?php echo JHtml::_('date.relative', $this->album->created); ?><?php endif; ?>
          </dd>
        <?php endif; ?>
        <?php if ($this->params->get('item_meta_media_count') != 'hide') :?>
          <dd class="media-info-count"><?php echo JText::_('COM_HWDMS_MEDIA'); ?> (<?php echo (int) $this->album->nummedia; ?>)</dd>
        <?php endif; ?>
        <?php if ($this->params->get('item_meta_hits') != 'hide') :?>
          <dd class="media-info-hits"><?php echo JText::_('COM_HWDMS_VIEWS'); ?> (<?php echo (int) $this->album->hits; ?>)</dd>
        <?php endif; ?>           
        <?php if ($this->params->get('item_meta_likes') != 'hide') :?>
          <dd class="media-info-like"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=albums.like&id=' . $this->album->id . '&return=' . $this->return . '&tmpl=component&'.JSession::getFormToken().'=1'); ?>"><?php echo JText::_('COM_HWDMS_LIKE'); ?></a> (<?php echo $this->escape($this->album->likes); ?>) <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=albums.dislike&id=' . $this->album->id . '&return=' . $this->return . '&tmpl=component&'.JSession::getFormToken().'=1'); ?>"><?php echo JText::_('COM_HWDMS_DISLIKE'); ?></a> (<?php echo $this->escape($this->album->dislikes); ?>)</dd>
        <?php endif; ?>   
        <?php if ($this->params->get('item_meta_report') != 'hide') :?>
          <dd class="media-info-report"><a title="<?php echo JText::_('COM_HWDMS_REPORT'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=albumform.report&id=' . $this->album->id . '&return=' . $this->return . '&tmpl=component'); ?>" class="media-popup-form"><?php echo JText::_('COM_HWDMS_REPORT'); ?></a></dd>
        <?php endif; ?>              
      </dl>
      <?php endif; ?>  
      <!-- Tags -->
      <?php if ($this->params->get('show_tags', 1) && !empty($this->album->tags)) : ?>
	<?php $this->album->tagLayout = new JLayoutFile('joomla.content.tags'); ?>
	<?php echo $this->album->tagLayout->render($this->album->tags->itemTags); ?>
      <?php endif; ?>
      <!-- Custom fields -->
      <dl class="media-media-info">
      <?php foreach ($this->album->customfields['fields'] as $group => $groupFields) : ?>
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
