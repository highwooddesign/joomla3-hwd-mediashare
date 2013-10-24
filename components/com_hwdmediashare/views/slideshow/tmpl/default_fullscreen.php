<?php
/**
 * @version    SVN $Id: default_fullscreen.php 1392 2013-04-23 13:13:39Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.modal');
JHtml::_('behavior.framework', true);
JHtml::_('behavior.tooltip');

$user = JFactory::getUser();
$canEdit = ($user->authorise('core.edit', 'com_hwdmediashare.media.'.$this->item->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.media.'.$this->item->id) && ($this->item->created_user_id == $user->id)));
$canEditState = $user->authorise('core.edit.state', 'com_hwdmediashare.media.'.$this->item->id);
$canDelete = ($user->authorise('core.delete', 'com_hwdmediashare.media.'.$this->item->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.media.'.$this->item->id) && ($this->item->created_user_id == $user->id)));

$counter = 0; 
?>
<div class="fullscreen">
<div id="hwd-container">
  <!-- Media Header -->
  <div class="media-header">
    <ul class="media-nav">
      <li><a href="<?php echo JRoute::_(base64_decode(JRequest::getVar('return', hwdMediaShareHelperRoute::getMediaItemRoute($this->item->id)))); ?>" class="pagenav-close"><?php echo JText::_('COM_HWDMS_CLOSE'); ?></a></li>
    </ul>
    <a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getMediaItemRoute($this->item->id)); ?>">
      <h2 class="media-title"><?php echo $this->escape($this->item->title); ?></h2>
    </a>    
    <dl class="article-info">
    <dt class="article-info-term"><?php echo JText::_('COM_HWDMS_DETAILS'); ?> </dt>
    <dd class="media-createdby"> <?php echo JText::sprintf('COM_HWDMS_CREATED_BY', '<a href="'.JRoute::_(hwdMediaShareHelperRoute::getUserRoute($this->item->created_user_id)).'">'.htmlspecialchars($this->item->author, ENT_COMPAT, 'UTF-8').'</a>'); ?> </dd>
    <?php if ($this->element) : ?>
        <dd class="media-viewing"> <?php echo JText::sprintf('COM_HWDMS_VIEWING_ELEMENTX_CALLEDX', strtolower(JText::_($this->element->type)), $this->element->link, strtolower(JText::_($this->element->order))); ?> </dd>
    <?php endif; ?>
    </dl>
    <!-- Media Actions -->
    <div class="media-actions-container">
      <ul class="media-actions">
        <?php if ($this->params->get('mediaitem_like_button') != 'hide') : ?>
          <li id="media-like" class="media-like"><a title="<?php echo JText::_('COM_HWDMS_LIKE'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=mediaitem.like&id=' . $this->item->id . '&return=' . $this->return . '&tmpl=component'); ?>" class="media-like-link" id="media-like-link"><?php echo JText::_('COM_HWDMS_LIKE'); ?></a></li>
          <li id="media-dislike" class="media-dislike"><a title="<?php echo JText::_('COM_HWDMS_DISLIKE'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=mediaitem.dislike&id=' . $this->item->id . '&return=' . $this->return . '&tmpl=component'); ?>" class="media-dislike-link" id="media-dislike-link"><?php echo JText::_('COM_HWDMS_DISLIKE'); ?></a></li>
        <?php endif; ?>
        <?php if ($this->params->get('mediaitem_favourite_button') != 'hide') : ?>
          <?php if ($this->item->favoured) : ?>
            <li id="media-favadd" class="media-favadd"><a title="<?php echo JText::_('COM_HWDMS_FAVOURITE'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=mediaitem.unfavour&id=' . $this->item->id . '&return=' . $this->return . '&tmpl=component'); ?>" class="media-favadd-link" id="media-favadd-link"><?php echo JText::_('COM_HWDMS_FAVOURITES'); ?></a></li>
          <?php else : ?>
            <li id="media-fav" class="media-fav"><a title="<?php echo JText::_('COM_HWDMS_FAVOURITE'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=mediaitem.favour&id=' . $this->item->id . '&return=' . $this->return . '&tmpl=component'); ?>" class="media-fav-link" id="media-fav-link"><?php echo JText::_('COM_HWDMS_FAVOURITES'); ?></a></li>
          <?php endif; ?>
        <?php endif; ?>
        <?php if ($this->params->get('mediaitem_report_button') != 'hide') : ?><li class="media-report"><a title="<?php echo JText::_('COM_HWDMS_REPORT'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=mediaform.report&id=' . $this->item->id . '&return=' . $this->return . '&tmpl=component'); ?>" class="media-report-link modal" rel="{handler: 'iframe', size: {<?php echo $this->utilities->modalSize(); ?>}}"><?php echo JText::_('COM_HWDMS_REPORT'); ?></a></li><?php endif; ?>
      </ul>
      <div class="clear"></div>
    </div>
    <div class="clear"></div>
  </div>
  <div id="media-item-container" class="media-item-container">
    <!-- Item Media -->
    <div id="media-item" class="media-item-full-slideshow">
      <div class="centered-cell">
        <?php echo hwdMediaShareMedia::get($this->item); ?>
      </div>
    </div>
    <div class="clear"></div>
  </div>
  <!-- Carousel -->
  <div class="media-slideshow">
    <div id="media-slideshow-toggle" class="media-slideshow-toggle">
      <div id="media-slideshow-container" class="media-slideshow-container">
        <div class="slide-previous">
          <a href="#page-p" class="pagenav">&laquo;</a>
        </div>
        <div class="slide">
          <div id="slide">
            <?php foreach ($this->items as $id => &$item) : ?>
            <div class="slide-unit" onClick="parent.location='<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=slideshow&key='.$item->id.'&Itemid='.JRequest::getInt('Itemid').'&tmpl=component&return='.JRequest::getVar('return') . (JFactory::getApplication()->input->get('category_id') ? '&category_id=' . JFactory::getApplication()->input->get('category_id') : '') . (JFactory::getApplication()->input->get('playlist_id') ? '&playlist_id=' . JFactory::getApplication()->input->get('playlist_id') : '') . (JFactory::getApplication()->input->get('album_id') ? '&album_id=' . JFactory::getApplication()->input->get('album_id') : '') . (JFactory::getApplication()->input->get('group_id') ? '&group_id=' . JFactory::getApplication()->input->get('group_id') : '')); ?>'"> 
                <div class="slide-unit-count"><?php echo $counter+1; ?></div>
                <div class="slide-unit-thumb"><div class="centered-cell"><img src="<?php echo JRoute::_(hwdMediaShareDownloads::thumbnail($item)); ?>" id="image-slideshow-<?php echo $counter; ?>" <?php echo (JRequest::getInt('id') == $item->id ? 'class="highlighted"' : ''); ?> /></div></div>
                <div class="slide-unit-title">
                <?php echo (JRequest::getInt('id') == $item->id ? '<span class="now-playing">'.JText::_('COM_HWDMS_NOW_PLAYING').'</span>' : ''); ?>
                <?php echo JHtmlString::truncate($item->title, 20); ?>
                <span class="uploader"> <?php echo JText::sprintf('COM_HWDMS_CREATED_BY', htmlspecialchars($item->author, ENT_COMPAT, 'UTF-8')); ?> </span>
                </div>
            </div>
            <?php $counter++; endforeach; ?>
          </div>
        </div>
        <div class="slide-next">
          <a href="#page-p" class="pagenav">&raquo;</a>
        </div>
        <div class="clear"></div>
      </div>
    </div>
  </div>
</div>
</div>
