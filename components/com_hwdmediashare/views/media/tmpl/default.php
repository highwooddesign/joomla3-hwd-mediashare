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
?>
<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm">
  <div id="hwd-container" class="<?php echo $this->pageclass_sfx;?>"> <a name="top" id="top"></a>
    <!-- Media Navigation -->
    <?php echo hwdMediaShareHelperNavigation::getInternalNavigation(); ?>
    <!-- Media Header -->
    <div class="media-header">
      <?php if ($this->params->get('list_page_title') != '0') :?>
        <h2 class="media-media-title"><?php echo $this->escape($this->params->get('page_heading')); ?></h2>
      <?php endif; ?>         
      <!-- Buttons -->
      <div class="btn-group pull-right"> 
        <?php if ($this->params->get('list_feature_button') != '0' && $this->state->get('media.show_featured') == 'only'): ?>
          <a title="<?php echo JText::_('COM_HWDMS_FEATURED'); ?>" href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getMediaRoute(array('show_featured' => 'show'))); ?>" class="btn btn-info active"><?php echo JText::_('COM_HWDMS_FEATURED'); ?></a>
        <?php elseif ($this->params->get('list_feature_button') != '0'): ?>  
          <a title="<?php echo JText::_('COM_HWDMS_SHOW_FEATURED'); ?>" href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getMediaRoute(array('show_featured' => 'only'))); ?>" class="btn"><?php echo JText::_('COM_HWDMS_SHOW_FEATURED'); ?></a>
        <?php endif; ?>  
        <?php if ($this->params->get('list_details_button') != '0') : ?>
          <a title="<?php echo JText::_('COM_HWDMS_DETAILS'); ?>" href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getMediaRoute(array('display' => 'details'))); ?>" class="btn"><i class="icon-image"></i></a>
        <?php endif; ?>
        <?php if ($this->params->get('list_gallery_button') != '0') : ?>
          <a title="<?php echo JText::_('COM_HWDMS_GALLERY'); ?>" href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getMediaRoute(array('display' => 'gallery'))); ?>" class="btn"><i class="icon-grid"></i></a>
        <?php endif; ?>          
        <?php if ($this->params->get('list_list_button') != '0') : ?>
          <a title="<?php echo JText::_('COM_HWDMS_LIST'); ?>" href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getMediaRoute(array('display' => 'list'))); ?>" class="btn"><i class="icon-list"></i></a>
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
  </div>
</form>
