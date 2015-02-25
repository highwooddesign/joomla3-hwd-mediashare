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

$user = JFactory::getUser();
$canEdit = ($user->authorise('core.edit', 'com_hwdmediashare.category.'.$this->category->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.category.'.$this->category->id) && ($this->category->created_user_id == $user->id)));
$canEditState = $user->authorise('core.edit.state', 'com_hwdmediashare.category.'.$this->category->id);
$canDelete = ($user->authorise('core.delete', 'com_hwdmediashare.category.'.$this->category->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.category.'.$this->category->id) && ($this->category->created_user_id == $user->id)));
$canAddMedia = ($user->authorise('hwdmediashare.upload', 'com_hwdmediashare') || $user->authorise('hwdmediashare.import', 'com_hwdmediashare'));
?>
<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm">
  <div id="hwd-container" class="<?php echo $this->pageclass_sfx;?>"> <a name="top" id="top"></a>
    <!-- Media Navigation -->
    <?php echo hwdMediaShareHelperNavigation::getInternalNavigation(); ?>
    <!-- Media Header -->
    <div class="media-header">
      <?php if ($this->params->get('item_meta_title') != '0') :?>
        <h2 class="media-category-title"><?php echo $this->escape($this->params->get('page_heading')); ?></h2>
      <?php endif; ?> 
      <!-- Buttons -->
      <div class="btn-group pull-right">
        <?php if ($canAddMedia): ?>
          <a title="<?php echo JText::_('COM_HWDMS_ADD_MEDIA'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=upload&category_id='.(int)$this->category->id); ?>" class="btn"><i class="icon-plus"></i> <?php echo JText::_('COM_HWDMS_ADD_MEDIA'); ?></a>
        <?php endif; ?>
        <?php if ($this->params->get('list_feature_button') != '0' && $this->state->get('category.show_featured') == 'only'): ?>
          <a title="<?php echo JText::_('COM_HWDMS_FEATURED'); ?>" href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getCategoryRoute($this->category->id, array('show_featured' => 'show'))); ?>" class="btn btn-info active"><?php echo JText::_('COM_HWDMS_FEATURED'); ?></a>
        <?php elseif ($this->params->get('list_feature_button') != '0'): ?>  
          <a title="<?php echo JText::_('COM_HWDMS_SHOW_FEATURED'); ?>" href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getCategoryRoute($this->category->id, array('show_featured' => 'only'))); ?>" class="btn"><?php echo JText::_('COM_HWDMS_SHOW_FEATURED'); ?></a>
        <?php endif; ?>           
        <?php if ($this->params->get('list_details_button') != '0') : ?>
          <a title="<?php echo JText::_('COM_HWDMS_DETAILS'); ?>" href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getCategoryRoute($this->category->id, array('display' => 'details'))); ?>" class="btn"><i class="icon-image"></i></a>
        <?php endif; ?>
        <?php if ($this->params->get('list_gallery_button') != '0') : ?>
          <a title="<?php echo JText::_('COM_HWDMS_GALLERY'); ?>" href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getCategoryRoute($this->category->id, array('display' => 'gallery'))); ?>" class="btn"><i class="icon-grid"></i></a>
        <?php endif; ?>
        <?php if ($this->params->get('list_list_button') != '0') : ?>
          <a title="<?php echo JText::_('COM_HWDMS_LIST'); ?>" href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getCategoryRoute($this->category->id, array('display' => 'list'))); ?>" class="btn"><i class="icon-list"></i></a>
        <?php endif; ?>
        <?php if ($canEdit || $canDelete): ?>
        <?php
        // Create dropdown items
        if ($canEdit) : 
          JHtml::_('hwddropdown.edit', $this->category->id, 'categoryform'); 
        endif;    
        if ($canEditState) :
          $action = $this->category->published == 1 ? 'unpublish' : 'publish';
          JHtml::_('hwddropdown.' . $action, $this->category->id, 'categories'); 
        endif; 
        if ($canDelete && $this->category->published != -2) : 
          JHtml::_('hwddropdown.delete', $this->category->id, 'categories'); 
        endif;             
        // Render dropdown list       
        echo JHtml::_('hwddropdown.render', $this->escape($this->category->title));
        ?>
        <?php endif; ?>
      </div> 
      <div class="clear"></div>
      <!-- Featured Media -->
      <?php echo $this->loadTemplate('feature'); ?>
      <!-- Subcategories -->
      <?php echo $this->loadTemplate('subcategories'); ?>
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
    <!-- Category Description -->
    <div class="well media-category-description">
      <?php if ($this->params->get('item_meta_description') != '0') :?>
        <?php echo JHtml::_('content.prepare', $this->category->description); ?>
      <?php endif; ?>            
      <?php if ($this->params->get('item_meta_media_count') != '0' || $this->params->get('item_meta_hits') != '0') : ?>
      <dl class="media-info">
        <dt class="media-info-term"><?php echo JText::_('COM_HWDMS_DETAILS'); ?></dt>
        <?php if ($this->params->get('item_meta_hits') != '0') :?>
          <dd class="media-info-hits label"><?php echo JText::sprintf('COM_HWDMS_X_VIEWS', number_format((int) $this->category->hits)); ?></dd>
        <?php endif; ?>      
        <?php if ($this->params->get('item_meta_media_count') != '0') :?>
          <dd class="media-info-count label"><?php echo JText::plural('COM_HWDMS_X_MEDIA_COUNT', (int) $this->category->nummedia); ?></dd>
        <?php endif; ?>  
        <div class="clearfix"></div>        
      </dl>
      <?php endif; ?>
      <!-- Tags -->
      <?php if ($this->params->get('show_tags', 1) && !empty($this->category->tags)) : ?>
	<?php $this->category->tagLayout = new JLayoutFile('joomla.content.tags'); ?>
	<?php echo $this->category->tagLayout->render($this->category->tags->itemTags); ?>
      <?php endif; ?>
    </div>
    <div class="clear"></div>
  </div>
</form>
