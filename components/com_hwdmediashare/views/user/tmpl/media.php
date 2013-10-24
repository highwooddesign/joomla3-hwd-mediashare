<?php
/**
 * @version    SVN $Id: media.php 1635 2013-08-15 10:15:25Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      08-Jan-2012 12:12:40
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Require media type field
JLoader::register('JFormFieldMediaType', JPATH_ROOT.'/administrator/components/com_hwdmediashare/models/fields/mediatype.php');
$mediaTypeTypeField = new JFormFieldMediaType;

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

// This page will open in a modal window and we want to direct all item links to the parent frame
$this->document->addScriptDeclaration( "window.onload = function(){
  var anchors = document.getElementById('hwd-media-modal-items').getElementsByTagName('a');
  for (var i=0; i<anchors.length; i++){
    anchors[i].setAttribute('target', '_top');
  }
}" );
?>
<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm">
  <div id="hwd-container"> <a name="top" id="top"></a>
    <!-- Media Header -->
    <div class="media-header">
      <h2 class="media-media-title"><?php echo JText::sprintf( 'COM_HWDMS_USERXS_MEDIA', $this->escape($this->channel->title)); ?></h2>
      <!-- View Type -->
      <ul class="media-category-ls">
        <?php if ($this->params->get('list_details_button') != 'hide') :?><li><a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getSelfRoute('details')); ?>" class="ls-detail" title="<?php echo JText::_('COM_HWDMS_DETAILS'); ?>"><?php echo JText::_('COM_HWDMS_DETAILS'); ?></a></li><?php endif; ?>
        <?php if ($this->params->get('list_gallery_button') != 'hide') :?><li><a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getSelfRoute('gallery')); ?>" class="ls-grid" title="<?php echo JText::_('COM_HWDMS_GALLERY'); ?>"><?php echo JText::_('COM_HWDMS_GALLERY'); ?></a></li><?php endif; ?>
        <?php if ($this->params->get('list_list_button') != 'hide') :?><li><a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getSelfRoute('list')); ?>" class="ls-list" title="<?php echo JText::_('COM_HWDMS_LIST'); ?>"><?php echo JText::_('COM_HWDMS_LIST'); ?></a></li><?php endif; ?>
      </ul>
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
        <div class="display-limit">
          <label class="filter-order-lbl" for="filter_order"><?php echo JText::_('COM_HWDMS_ORDER'); ?></label>
          <select onchange="this.form.submit()" size="1" class="inputbox" name="filter_order" id="filter_order">
            <option value="a.created"<?php echo ($listOrder == 'a.created' ? ' selected="selected"' : false); ?>><?php echo JText::_( 'COM_HWDMS_OPTION_MOST_RECENT' ); ?></option>
            <?php if ($this->params->get('list_meta_hits') != 'hide') :?><option value="a.hits"<?php echo ($listOrder == 'a.hits' ? ' selected="selected"' : false); ?>><?php echo JText::_( 'COM_HWDMS_OPTION_MOST_HITS' ); ?></option><?php endif; ?>
            <?php if ($this->params->get('list_meta_likes') != 'hide') :?><option value="a.likes"<?php echo ($listOrder == 'a.likes' ? ' selected="selected"' : false); ?>><?php echo JText::_( 'COM_HWDMS_OPTION_MOST_LIKES' ); ?></option><?php endif; ?>
            <?php if ($this->params->get('list_meta_likes') != 'hide') :?><option value="a.dislikes"<?php echo ($listOrder == 'a.dislikes' ? ' selected="selected"' : false); ?>><?php echo JText::_( 'COM_HWDMS_OPTION_MOST_DISLIKES' ); ?></option><?php endif; ?>
            <option value="a.modified"<?php echo ($listOrder == 'a.modified' ? ' selected="selected"' : false); ?>><?php echo JText::_( 'COM_HWDMS_OPTION_RECENTLY_MODIFIED' ); ?></option>
            <option value="a.viewed"<?php echo ($listOrder == 'a.viewed' ? ' selected="selected"' : false); ?>><?php echo JText::_( 'COM_HWDMS_OPTION_RECENTLY_VIEWED' ); ?></option>
            <?php if ($this->params->get('list_meta_title') != 'hide') :?><option value="a.title"<?php echo ($listOrder == 'a.title' ? ' selected="selected"' : false); ?>><?php echo JText::_( 'COM_HWDMS_OPTION_TITLE_ALPHABETICAL' ); ?></option><?php endif; ?>
            <option value="author"<?php echo ($listOrder == 'author' ? ' selected="selected"' : false); ?>><?php echo JText::_( 'COM_HWDMS_OPTION_AUTHOR_ALPHABETICAL' ); ?></option>
            <option value="random"<?php echo ($listOrder == 'random' ? ' selected="selected"' : false); ?>><?php echo JText::_( 'COM_HWDMS_OPTION_RANDOM' ); ?></option>
          </select>
        </div>
        <?php if ($this->params->get('list_filter_media') != 'hide') :?>
        <div class="display-limit">
          <label class="filter-type-lbl" for="filter_type">&#160;</label>
          <?php echo $mediaTypeTypeField->getPublicInput(array('name'=>'filter_mediaType','class'=>'inputbox','onchange'=>'this.form.submit()','value'=>$this->state->get('filter.mediaType'),'none'=>JText::_('COM_HWDMS_ALL_MEDIA'))); ?>
        </div>
        <?php endif; ?>
        <!-- @TODO add hidden inputs -->
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
        <input type="hidden" name="limitstart" value="" />
      </fieldset>
      <div class="clear"></div>
    </div>
    <div id="hwd-media-modal-items">
      <?php $this->setLayout('default');
      echo $this->loadTemplate('media_'.$this->display); ?>
    </div>
    <!-- Pagination -->
    <div class="pagination"> <?php echo $this->pagination->getPagesLinks(); ?> </div>
  </div>
</form>
