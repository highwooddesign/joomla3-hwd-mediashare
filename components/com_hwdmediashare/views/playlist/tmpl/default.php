<?php
/**
 * @version    SVN $Id: default.php 1337 2013-03-20 10:53:31Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

$user = JFactory::getUser();

$canEdit = ($user->authorise('core.edit', 'com_hwdmediashare.playlist.'.$this->playlist->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.playlist.'.$this->playlist->id) && ($this->playlist->created_user_id == $user->id)));
$canEditState = $user->authorise('core.edit.state', 'com_hwdmediashare.playlist.'.$this->playlist->id);
$canDelete = ($user->authorise('core.delete', 'com_hwdmediashare.playlist.'.$this->playlist->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.playlist.'.$this->playlist->id) && ($this->playlist->created_user_id == $user->id)));
    
JHtml::_('behavior.modal');
JHtml::_('behavior.framework', true);
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
      <!-- View Type -->
      <ul class="media-category-ls">
        <?php if ($this->params->get('item_meta_media_count') != 'hide') :?><li class="media-playlist-items"> <?php echo JText::_('COM_HWDMS_MEDIA'); ?> (<?php echo (int) $this->playlist->nummedia; ?>) </li><?php endif; ?>
        <li class="media-playlist-play"> <a title="<?php echo JText::_('COM_HWDMS_PLAY_NOW'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=slideshow&playlist_id=' . $this->playlist->id . '&tmpl=component&return=' . $this->return); ?>"><?php echo JText::_('COM_HWDMS_PLAY_NOW'); ?></a> </li>
      </ul>
      <div class="clear"></div>
      <!-- Description -->
      <div class="media-playlist-description">
        <!-- Thumbnail Image -->
        <?php if ($this->params->get('item_meta_thumbnail') != 'hide') :?>
        <div class="media-item">
          <div class="media-aspect<?php echo $this->params->get('list_thumbnail_aspect'); ?>"></div>                    
          <?php if ($canEdit || $canDelete): ?>
          <!-- Actions -->
          <ul class="media-nav">
            <li><a href="#" class="pagenav-manage"><?php echo JText::_('COM_HWDMS_MANAGE'); ?> </a>
              <ul class="media-subnav">
                <?php if ($canEdit) : ?>
                <li><?php echo JHtml::_('hwdicon.edit', 'playlist', $this->playlist, $this->params); ?></li>
                <?php endif; ?>
                <?php if ($canEditState) : ?>
                <?php if ($this->playlist->published != '1') : ?>
                <li><?php echo JHtml::_('hwdicon.publish', 'playlist', $this->playlist, $this->params); ?></li>
                <?php else : ?>
                <li><?php echo JHtml::_('hwdicon.unpublish', 'playlist', $this->playlist, $this->params); ?></li>
                <?php endif; ?>
                <?php endif; ?>
                <?php if ($canDelete) : ?>
                <li><?php echo JHtml::_('hwdicon.delete', 'playlist', $this->playlist, $this->params); ?></li>
                <?php endif; ?>
              </ul>
            </li>
          </ul>
          <?php endif; ?>
          <!-- Media Type -->
          <?php if ($this->params->get('item_meta_type_icon') != 'hide') :?>
          <div class="media-item-format-4">
             <img src="<?php echo JHtml::_('hwdicon.overlay', 4); ?>"  alt="<?php echo JText::_('COM_HWDMS_PLAYLIST'); ?>" />
          </div>
          <?php endif; ?>
          <img src="<?php echo JRoute::_(hwdMediaShareDownloads::thumbnail($this->playlist, 4)); ?>" border="0" alt="<?php echo $this->escape($this->playlist->title); ?>" class="media-thumb" />
        </div>
        <?php endif; ?>
        <?php if ($this->params->get('item_meta_author') != 'hide' || $this->params->get('item_meta_created') != 'hide' || $this->params->get('item_meta_hits') != 'hide' || $this->params->get('item_meta_likes') != 'hide' || $this->params->get('item_meta_report') != 'hide') : ?>
        <dl class="article-info">
          <dt class="article-info-term"><?php echo JText::_('COM_HWDMS_DETAILS'); ?> </dt>
          <?php if ($this->params->get('item_meta_author') != 'hide') :?>
            <dd class="media-info-createdby"> <?php echo JText::sprintf('COM_HWDMS_CREATED_BY', '<a href="'.JRoute::_(hwdMediaShareHelperRoute::getUserRoute($this->playlist->created_user_id)).'">'.htmlspecialchars($this->playlist->author, ENT_COMPAT, 'UTF-8').'</a>'); ?></dd>
          <?php endif; ?>
          <?php if ($this->params->get('item_meta_created') != 'hide') :?>
            <dd class="media-info-created"> <?php echo JText::sprintf('COM_HWDMS_CREATED_ON', JHtml::_('date', $this->playlist->created, $this->params->get('list_date_format'))); ?></dd>
          <?php endif; ?> 
          <?php if ($this->params->get('item_meta_hits') != 'hide') :?>
            <dd class="media-info-hits"> <?php echo JText::_('COM_HWDMS_VIEWS'); ?> (<?php echo (int) $this->playlist->hits; ?>)</dd>
          <?php endif; ?>   
          <?php if ($this->params->get('item_meta_likes') != 'hide') :?>
            <dd class="media-info-like"> <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=playlist.like&id=' . $this->playlist->id . '&return=' . $this->return . '&tmpl=component'); ?>"><?php echo JText::_('COM_HWDMS_LIKE'); ?></a> (<?php echo $this->escape($this->playlist->likes); ?>) <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=playlist.dislike&id=' . $this->playlist->id . '&return=' . $this->return . '&tmpl=component'); ?>"><?php echo JText::_('COM_HWDMS_DISLIKE'); ?></a> (<?php echo $this->escape($this->playlist->dislikes); ?>) </dd>
          <?php endif; ?>  
          <?php if ($this->params->get('item_meta_report') != 'hide') :?>
            <dd class="media-info-report"> <a title="<?php echo JText::_('COM_HWDMS_REPORT'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=playlistform.report&id=' . $this->playlist->id . '&return=' . $this->return . '&tmpl=component'); ?>" class="modal" rel="{handler: 'iframe', size: {<?php echo $this->utilities->modalSize(); ?>}}"><?php echo JText::_('COM_HWDMS_REPORT'); ?> </a> </dd>
          <?php endif; ?>             
        </dl>
        <?php endif; ?>  
        <!-- Custom fields -->
        <dl class="media-article-info">
        <?php foreach ($this->playlist->customfields['fields'] as $group => $groupFields) : ?>
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
        <?php echo JHtml::_('content.prepare', $this->playlist->description); ?>
        <?php endif; ?> 
        <div class="clear"></div> 
      </div>
      <div class="clear"></div>
      <!-- Search Filters -->
      <fieldset class="filters">
        <?php if ($this->params->get('list_filter_search') != 'hide') :?>
        <legend class="hidelabeltxt"> <?php echo JText::_('JGLOBAL_FILTER_LABEL'); ?> </legend>
        <div class="filter-search">
          <label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
          <input type="text" name="filter_search" id="filter_search" class="inputbox" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_HWDMS_SEARCH_IN_TITLE'); ?>" />
          <button type="submit" class="button"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
          <button type="button" class="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
        </div>
        <?php endif; ?>
        <?php if ($this->params->get('list_filter_pagination') != 'hide') : ?>
        <div class="display-limit"> <label class="filter-pagination-lbl" for="filter_pagination"><?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?></label> <?php echo $this->pagination->getLimitBox(); ?> </div>
        <?php endif; ?>
        <!-- @TODO add hidden inputs -->
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
        <input type="hidden" name="limitstart" value="" />
      </fieldset>
      <div class="clear"></div>
    </div>
    <div class="media-playlist">
        <?php echo $this->loadTemplate('list'); ?>
    </div>
    <!-- Pagination -->
    <div class="pagination"> <?php echo $this->pagination->getPagesLinks(); ?> </div>
  </div>
</form>