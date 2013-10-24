<?php
/**
 * @version    SVN $Id: edit.php 834 2012-12-21 16:48:08Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      26-Nov-2011 12:06:48
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.calendar');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.modal', 'button.modal');

?>
<div class="edit">
<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">
  <div id="hwd-container"> <a name="top" id="top"></a>
    <!-- Media Navigation -->
    <?php echo hwdMediaShareHelperNavigation::getInternalNavigation(); ?>
    <!-- Media Header -->
    <div class="media-header">
      <h2 class="media-media-title"><?php echo JText::sprintf( 'COM_HWDMS_EDIT_MEDIAX', $this->escape($this->item->title)); ?></h2>
      <div class="clear"></div>
    </div>
    <!-- Form -->
    <fieldset>
      <legend><?php echo JText::_('JEDITOR'); ?></legend>
      <div class="formelm">
        <?php echo $this->form->getLabel('title'); ?>
        <?php echo $this->form->getInput('title'); ?>
      </div>
      <?php if (is_null($this->item->id)):?>
        <div class="formelm">
          <?php echo $this->form->getLabel('alias'); ?>
          <?php echo $this->form->getInput('alias'); ?>
        </div>
      <?php endif; ?>
      <div class="formelm-buttons">
        <button type="button" href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getMediaModalRoute($this->item->id)); ?>" class="modal" rel="{handler: 'iframe', size: {x: 800, y: 500}}">
          <?php echo JText::_('COM_HWDMS_VIEW_MEDIA') ?>
        </button>                  
        <button type="button" onclick="Joomla.submitbutton('mediaform.save')">
          <?php echo JText::_('JSAVE') ?>
        </button>
        <button type="button" onclick="Joomla.submitbutton('mediaform.cancel')">
          <?php echo JText::_('JCANCEL') ?>
        </button>
      </div>
      <?php echo $this->form->getInput('description'); ?>
    </fieldset>
    <!-- Thumbnail -->
    <fieldset>
      <legend><?php echo JText::_('COM_HWDMS_THUMBNAIL'); ?></legend>
      <div style="float:right;">
        <img src="<?php echo JRoute::_(hwdMediaShareDownloads::thumbnail($this->item)); ?>" border="0" alt="<?php echo $this->escape($this->item->title); ?>" style="max-width:200px;" />
      </div> 
      <?php if ($this->item->customthumbnail) : ?>
        <div class="formelm">
          <?php echo $this->form->getLabel('remove_thumbnail'); ?>
          <?php echo $this->form->getInput('remove_thumbnail'); ?>
        </div>
      <?php endif; ?> 
      <div class="formelm">
        <?php echo $this->form->getLabel('thumbnail'); ?>
        <?php echo $this->form->getInput('thumbnail'); ?>
      </div>
    </fieldset>
    <!-- Publishing -->
    <fieldset>
      <legend><?php echo JText::_('COM_HWDMS_PUBLISHING'); ?></legend>
        <div class="formelm">
          <?php echo $this->form->getLabel('catid'); ?>
          <?php if($this->params->get('enable_category', 0) == 1) : ?>
            <span class="category">
              <?php echo $this->category_title; ?>
            </span>
          <?php else : ?>
            <?php   echo $this->form->getInput('catid'); ?>
          <?php endif;?>
        </div>
        <div class="formelm">
          <?php echo $this->form->getLabel('tags'); ?>
          <?php if($this->params->get('enable_category', 0) == 1) : ?>
            <span class="category">
              <?php echo $this->tags; ?>
            </span>
          <?php else : ?>
            <?php echo $this->form->getInput('tags'); ?>
          <?php endif;?>
        </div>
      <?php if ($this->item->controls->get('access-change')): ?>
        <div class="formelm">
          <?php echo $this->form->getLabel('published'); ?>
          <?php echo $this->form->getInput('published'); ?>
        </div>
        <div class="formelm">
          <?php echo $this->form->getLabel('featured'); ?>
          <?php echo $this->form->getInput('featured'); ?>
        </div>
        <div class="formelm">
          <?php echo $this->form->getLabel('publish_up'); ?>
          <?php echo $this->form->getInput('publish_up'); ?>
        </div>
        <div class="formelm">
          <?php echo $this->form->getLabel('publish_down'); ?>
          <?php echo $this->form->getInput('publish_down'); ?>
        </div>
      <?php else: ?>
        <input type="hidden" name="jform[published]" value="1" />
      <?php endif; ?>
      <div class="formelm">
        <?php echo $this->form->getLabel('private'); ?>
        <?php echo $this->form->getInput('private'); ?>
      </div>        
      <div class="formelm">
        <?php echo $this->form->getLabel('access'); ?>
        <?php echo $this->form->getInput('access'); ?>
      </div>
      <div class="formelm">
        <?php echo $this->form->getLabel('language'); ?>
        <?php echo $this->form->getInput('language'); ?>
      </div>
      <div class="formelm">
        <?php echo $this->form->getLabel('location'); ?>
        <?php echo $this->form->getInput('location'); ?>
      </div>
      <div class="formelm">
        <?php echo $this->form->getLabel('created_by_alias'); ?>
        <?php echo $this->form->getInput('created_by_alias'); ?>
      </div>
    </fieldset>
    <!-- Custom -->
    <?php foreach ($this->item->customfields['fields'] as $group => $groupFields) : ?>
    <fieldset class="adminform">
      <legend><?php echo JText::_( $group ); ?></legend>
      <?php foreach ($groupFields as $field) :
      $field	= JArrayHelper::toObject ( $field );
      $field->value	= $this->escape( $field->value );
      ?>
        <div class="formelm">
          <label title="<?php echo $field->name;?>::<?php echo $field->tooltip;?>" class="hasTip" for="jform_<?php echo $field->id;?>" id="jform_<?php echo $field->id;?>-lbl"><?php echo JText::_( $field->name );?><?php if($field->required == 1) echo '<span class="star">&nbsp;*</span>'; ?></label>
          <?php echo hwdMediaShareCustomFields::getFieldHTML( $field , '' ); ?>
        </div>
      <?php endforeach; ?>
    </fieldset>
    <?php endforeach; ?> 
    <!-- Meta -->
    <fieldset>
      <legend><?php echo JText::_('COM_HWDMS_METADATA'); ?></legend>
      <div class="formelm">
        <?php echo $this->form->getLabel('meta_desc', 'params'); ?>
        <?php echo $this->form->getInput('meta_desc', 'params'); ?>          
      </div>
      <div class="formelm">
        <?php echo $this->form->getLabel('meta_keys', 'params'); ?>
        <?php echo $this->form->getInput('meta_keys', 'params'); ?>
      </div>
      <input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
      <input type="hidden" name="task" value="" />
      <input type="hidden" name="return" value="<?php echo $this->return_page;?>" />
      <?php if($this->params->get('enable_category', 0) == 1) :?>
        <input type="hidden" name="jform[catid]" value="<?php echo $this->params->get('catid', 1);?>"/>
      <?php endif;?>
      <?php echo JHtml::_( 'form.token' ); ?>
      <div class="formelm-buttons">
        <button type="button" href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getMediaModalRoute($this->item->id)); ?>" class="modal" rel="{handler: 'iframe', size: {x: 800, y: 500}}">
          <?php echo JText::_('COM_HWDMS_VIEW_MEDIA') ?>
        </button>            
        <button type="button" onclick="Joomla.submitbutton('mediaform.save')">
          <?php echo JText::_('JSAVE') ?>
        </button>
        <button type="button" onclick="Joomla.submitbutton('mediaform.cancel')">
          <?php echo JText::_('JCANCEL') ?>
        </button>
      </div>
    </fieldset>
  </div>
</form>
</div>