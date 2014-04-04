<?php
/**
 * @package     Joomla.administrator
 * @subpackage  Component.hwdmediashare
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

$user = JFactory::getUser();
$canEdit = ($user->authorise('core.edit', 'com_hwdmediashare.album.'.$this->album->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.album.'.$this->album->id) && ($this->album->created_user_id == $user->id)));
$canEditState = $user->authorise('core.edit.state', 'com_hwdmediashare.album.'.$this->album->id);
$canDelete = ($user->authorise('core.delete', 'com_hwdmediashare.album.'.$this->album->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.album.'.$this->album->id) && ($this->album->created_user_id == $user->id)));
$canAddMedia = ($user->authorise('hwdmediashare.upload','com_hwdmediashare') || $user->authorise('hwdmediashare.import','com_hwdmediashare'));
?>
<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm">
  <div id="hwd-container"> <a name="top" id="top"></a>
    <!-- Media Navigation -->
    <?php echo hwdMediaShareHelperNavigation::getInternalNavigation(); ?>
    <!-- Media Header -->
    <div class="media-header">
      <?php if ($this->params->get('item_meta_title') != 'hide') :?>
        <h2 class="media-album-title"><?php echo $this->escape($this->album->title); ?></h2>
      <?php endif; ?>        
      <!-- Buttons -->
      <div class="btn-group pull-right">
        <?php if ($canAddMedia): ?>
          <a title="<?php echo JText::_('COM_HWDMS_ADD_MEDIA'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=upload&tmpl=component&album_id='.(int)$this->album->id); ?>" class="btn modal" rel="{handler: 'iframe', size: {<?php echo $this->utilities->modalSize('large'); ?>}}"><i class="icon-plus"></i> <?php echo JText::_('COM_HWDMS_ADD_MEDIA'); ?></a>
        <?php endif; ?>
        <?php if ($this->params->get('item_meta_report') != 'hide' && $this->album->created_user_id != $user->id): ?>  
          <a title="<?php echo JText::_('COM_HWDMS_REPORT'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=albumform.report&id=' . $this->album->id . '&return=' . $this->return . '&tmpl=component'); ?>" class="btn"><i class="icon-warning"></i> <?php echo JText::_('COM_HWDMS_REPORT'); ?></a>
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
          $action = $this->album->published ? 'unpublish' : 'publish';
          JHtml::_('hwddropdown.' . $action, $this->album->id, 'albums'); 
        endif; 
        if ($canEdit) : 
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
    <div class="media-album-description">
      <?php if ($this->params->get('item_meta_media_count') != 'hide' || $this->params->get('item_meta_author') != 'hide' || $this->params->get('item_meta_created') != 'hide' || $this->params->get('item_meta_hits') != 'hide' || $this->params->get('item_meta_likes') != 'hide' || $this->params->get('item_meta_report') != 'hide') : ?>
      <dl class="article-info">
        <dt class="article-info-term"><?php echo JText::_('COM_HWDMS_DETAILS'); ?> </dt>
        <?php if ($this->params->get('item_meta_media_count') != 'hide') :?>
          <dd class="media-info-count"> <?php echo JText::_('COM_HWDMS_MEDIA'); ?> (<?php echo (int) $this->album->nummedia; ?>)</dd>
        <?php endif; ?>
        <?php if ($this->params->get('item_meta_author') != 'hide') :?>
          <dd class="media-info-createdby"> <?php echo JText::sprintf('COM_HWDMS_CREATED_BY', '<a href="'.JRoute::_(hwdMediaShareHelperRoute::getUserRoute($this->album->created_user_id)).'">'.htmlspecialchars($this->album->author, ENT_COMPAT, 'UTF-8').'</a>'); ?></dd>
        <?php endif; ?>
        <?php if ($this->params->get('item_meta_created') != 'hide') :?>
          <dd class="media-info-created"> <?php echo JText::sprintf('COM_HWDMS_CREATED_ON', JHtml::_('date', $this->album->created, $this->params->get('list_date_format'))); ?></dd>
        <?php endif; ?>            
        <?php if ($this->params->get('item_meta_hits') != 'hide') :?>
          <dd class="media-info-hits"> <?php echo JText::_('COM_HWDMS_VIEWS'); ?> (<?php echo (int) $this->album->hits; ?>)</dd>
        <?php endif; ?>           
        <?php if ($this->params->get('item_meta_likes') != 'hide') :?>
          <dd class="media-info-like"> <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=albums.like&id=' . $this->album->id . '&return=' . $this->return . '&tmpl=component&'.JSession::getFormToken().'=1'); ?>"><?php echo JText::_('COM_HWDMS_LIKE'); ?></a> (<?php echo $this->escape($this->album->likes); ?>) <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=albums.dislike&id=' . $this->album->id . '&return=' . $this->return . '&tmpl=component&'.JSession::getFormToken().'=1'); ?>"><?php echo JText::_('COM_HWDMS_DISLIKE'); ?></a> (<?php echo $this->escape($this->album->dislikes); ?>) </dd>
        <?php endif; ?>   
        <?php if ($this->params->get('item_meta_report') != 'hide') :?>
          <dd class="media-info-report"> <a title="<?php echo JText::_('COM_HWDMS_REPORT'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=albumform.report&id=' . $this->album->id . '&return=' . $this->return . '&tmpl=component'); ?>" class="modal" rel="{handler: 'iframe', size: {<?php echo $this->utilities->modalSize(); ?>}}"><?php echo JText::_('COM_HWDMS_REPORT'); ?> </a> </dd>
        <?php endif; ?>              
      </dl>
      <?php endif; ?>  
      <!-- Custom fields -->
      <dl class="media-article-info">
      <?php foreach ($this->album->customfields['fields'] as $group => $groupFields) : ?>
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
      <?php echo JHtml::_('content.prepare', $this->album->description); ?>
      <?php endif; ?> 
      <div class="clear"></div>        
    </div>
  </div>
</form>
