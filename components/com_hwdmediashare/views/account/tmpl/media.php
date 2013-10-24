<?php
/**
 * @version    SVN $Id: media.php 1153 2013-02-21 11:21:50Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      30-Nov-2011 16:40:50
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Require media type field
JLoader::register('JFormFieldMediaType', JPATH_ROOT.'/administrator/components/com_hwdmediashare/models/fields/mediatype.php');
$mediaTypeTypeField = new JFormFieldMediaType;

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
  <div id="hwd-container"> <a name="top" id="top"></a>
    <!-- Media Navigation -->
    <?php echo hwdMediaShareHelperNavigation::getInternalNavigation(); ?>
    <?php echo hwdMediaShareHelperNavigation::getAccountNavigation(); ?>
    <!-- Media Header -->
    <div class="media-header">
      <h2 class="media-media-title"><?php echo JText::_('COM_HWDMS_MY_MEDIA'); ?></h2>
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
        <?php if ($this->params->get('list_filter_media') != 'hide') :?>
        <div class="display-limit">
          <label class="filter-type-lbl" for="filter_type">&#160;</label>
          <?php echo $mediaTypeTypeField->getPublicInput(array('name'=>'filter_mediaType','class'=>'inputbox','onchange'=>'this.form.submit()','value'=>$this->state->get('filter.mediaType'),'none'=>JText::_('COM_HWDMS_ALL_MEDIA'))); ?>
        </div>
        <?php endif; ?>
        <!-- @TODO add hidden inputs -->
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" /> 
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
        <input type="hidden" name="limitstart" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="view" value="account" />
        <input type="hidden" name="return" value="<?php echo $this->return; ?>" />        
      </fieldset>
      <!-- Action buttons -->
      <?php if ($this->params->get('enable_categories') || $this->params->get('enable_albums') || $this->params->get('enable_groups') || $this->params->get('enable_playlists')): ?>
      <fieldset class="filters">   
        <div class="display-limit">
          <button type="button" class="button" onclick="document.adminForm.view.value='account';this.form.submit();"><?php echo JText::_('COM_HWDMS_FILTER'); ?></button>
        </div>
        <?php if ($this->params->get('enable_categories')): ?>
        <div class="display-limit">
          <?php echo $this->form->getInput('category_id'); ?>
        </div>
        <?php endif; ?>
        <?php if ($this->params->get('enable_albums')): ?>
        <div class="display-limit">
          <?php echo $this->form->getInput('filter_album_id'); ?>
        </div>
        <?php endif; ?>
        <?php if ($this->params->get('enable_groups')): ?>
        <div class="display-limit">
          <?php echo $this->form->getInput('filter_group_id'); ?>
        </div> 
        <?php endif; ?>
        <?php if ($this->params->get('enable_playlists')): ?>
        <div class="display-limit">
          <?php echo $this->form->getInput('filter_playlist_id'); ?>
        </div>
        <?php endif; ?>
      </fieldset>    
      <?php endif; ?>
      <div class="clear"></div>
      <button type="button" class="button" onclick="Joomla.submitbutton('media.delete')"><?php echo JText::_('COM_HWDMS_REMOVE'); ?></button>
      <button type="button" class="button" onclick="Joomla.submitbutton('media.publish')"><?php echo JText::_('COM_HWDMS_PUBLISH'); ?></button>
      <button type="button" class="button" onclick="Joomla.submitbutton('media.unpublish')"><?php echo JText::_('COM_HWDMS_UNPUBLISH'); ?></button>
      <div class="clear"></div>
    </div>
    <?php echo $this->loadTemplate('list'); ?>
    <!-- Pagination -->
    <div class="pagination"> <?php echo $this->pagination->getPagesLinks(); ?> </div>
    
    <?php if ($this->params->get('enable_categories') || $this->params->get('enable_albums') || $this->params->get('enable_groups') || $this->params->get('enable_playlists')): ?>
    <fieldset class="adminform">
        <legend><?php echo JText::_('COM_HWDMS_BATCH_ADD'); ?></legend>

        <?php if ($this->params->get('enable_categories')): ?>
        <div class="clear"></div>
        <div class="display-limit"><input type="button" class="button" value="<?php echo JText::_('COM_HWDMS_ADD_TO_CATEGORY'); ?>" onclick="javascript:if (document.adminForm.boxchecked.value==0){alert('Please first make a selection from the list');}else{ Joomla.submitbutton('media.assigncategory')}" ></div>
        <div class="formelm">
        <?php echo $this->batch->getLabel('assign_category_id'); ?>
        <?php echo $this->batch->getInput('assign_category_id'); ?>
        </div>
        <?php endif; ?>

        <?php if ($this->params->get('enable_albums')): ?>
        <div class="clear"></div>
        <div class="display-limit"><input type="button" class="button" value="<?php echo JText::_('COM_HWDMS_ADD_TO_ALBUM'); ?>" onclick="javascript:if (document.adminForm.boxchecked.value==0){alert('Please first make a selection from the list');}else{ Joomla.submitbutton('media.assignalbum')}" ></div>
        <div class="formelm">
        <?php echo $this->batch->getLabel('assign_album_id'); ?>
        <?php echo $this->batch->getInput('assign_album_id'); ?>
        </div>
        <?php endif; ?>

        <?php if ($this->params->get('enable_playlists')): ?>
        <div class="clear"></div>
        <div class="display-limit"><input type="button" class="button" value="<?php echo JText::_('COM_HWDMS_ADD_TO_PLAYLIST'); ?>" onclick="javascript:if (document.adminForm.boxchecked.value==0){alert('Please first make a selection from the list');}else{ Joomla.submitbutton('media.assignplaylist')}" ></div>
        <div class="formelm">
        <?php echo $this->batch->getLabel('assign_playlist_id'); ?>
        <?php echo $this->batch->getInput('assign_playlist_id'); ?>
        </div>
        <?php endif; ?>

        <?php if ($this->params->get('enable_groups')): ?>
        <div class="clear"></div>
        <div class="display-limit"><input type="button" class="button" value="<?php echo JText::_('COM_HWDMS_ADD_TO_GROUP'); ?>" onclick="javascript:if (document.adminForm.boxchecked.value==0){alert('Please first make a selection from the list');}else{ Joomla.submitbutton('media.assigngroup')}" ></div>
        <div class="formelm">
        <?php echo $this->batch->getLabel('assign_group_id'); ?>
        <?php echo $this->batch->getInput('assign_group_id'); ?>
        </div>
        <?php endif; ?>
    </fieldset>
      
    <fieldset class="adminform">
        <legend><?php echo JText::_('COM_HWDMS_BATCH_REMOVAL'); ?></legend>

        <?php if ($this->params->get('enable_categories')): ?>
        <div class="clear"></div>
        <div class="display-limit"><input type="button" class="button" value="<?php echo JText::_('COM_HWDMS_REMOVE_FROM_CATEGORY'); ?>" onclick="javascript:if (document.adminForm.boxchecked.value==0){alert('Please first make a selection from the list');}else{ Joomla.submitbutton('media.unassigncategory')}" ></div>
        <div class="formelm">
        <?php echo $this->batch->getLabel('unassign_category_id'); ?>
        <?php echo $this->batch->getInput('unassign_category_id'); ?>
        </div>
        <?php endif; ?>

        <?php if ($this->params->get('enable_albums')): ?>
        <div class="clear"></div>
        <div class="display-limit"><input type="button" class="button" value="<?php echo JText::_('COM_HWDMS_REMOVE_FROM_ALBUM'); ?>" onclick="javascript:if (document.adminForm.boxchecked.value==0){alert('Please first make a selection from the list');}else{ Joomla.submitbutton('media.unassignalbum')}" ></div>
        <div class="formelm">
        <?php echo $this->batch->getLabel('unassign_album_id'); ?>
        <?php echo $this->batch->getInput('unassign_album_id'); ?>                          
        </div>
        <?php endif; ?>

        <?php if ($this->params->get('enable_playlists')): ?>
        <div class="clear"></div>
        <div class="display-limit"><input type="button" class="button" value="<?php echo JText::_('COM_HWDMS_REMOVE_FROM_PLAYLIST'); ?>" onclick="javascript:if (document.adminForm.boxchecked.value==0){alert('Please first make a selection from the list');}else{ Joomla.submitbutton('media.unassignplaylist')}" ></div>
        <div class="formelm">
        <?php echo $this->batch->getLabel('unassign_playlist_id'); ?>
        <?php echo $this->batch->getInput('unassign_playlist_id'); ?>
        </div>
        <?php endif; ?>

        <?php if ($this->params->get('enable_groups')): ?>
        <div class="clear"></div>
        <div class="display-limit"><input type="button" class="button" value="<?php echo JText::_('COM_HWDMS_REMOVE_FROM_GROUP'); ?>" onclick="javascript:if (document.adminForm.boxchecked.value==0){alert('Please first make a selection from the list');}else{ Joomla.submitbutton('media.unassigngroup')}" ></div>
        <div class="formelm">
        <?php echo $this->batch->getLabel('unassign_group_id'); ?>
        <?php echo $this->batch->getInput('unassign_group_id'); ?>
        </div>
        <?php endif; ?>
    </fieldset>
    <?php endif; ?>
  </div>
</form>