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
      <h2 class="media-category-title"><?php echo $this->escape($this->params->get('page_heading')); ?></h2>
      <!-- Buttons -->
      <div class="btn-group pull-right">  
        <?php if ($this->params->get('list_tree_button') != '0') : ?>
          <a title="<?php echo JText::_('COM_HWDMS_TREE'); ?>" href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getCategoriesRoute(array('display' => 'tree'))); ?>" class="btn"><i class="icon-tree-2"></i></a>
        <?php endif; ?>
        <?php if ($this->params->get('list_details_button') != '0') : ?>
          <a title="<?php echo JText::_('COM_HWDMS_DETAILS'); ?>" href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getCategoriesRoute(array('display' => 'details'))); ?>" class="btn"><i class="icon-image"></i></a>
        <?php endif; ?>
      </div>    
      <div class="clear"></div>
      <?php if ($this->params->get('category_list_quick_view') != '0' && count($this->items[$this->parent->id]) != 0) :?>
	<?php echo JHtml::_('bootstrap.startAccordion', 'slide-quickview', array('active' => '')); ?>
	<?php echo JHtml::_('bootstrap.addSlide', 'slide-quickview', JText::_('COM_HWDMS_CATEGORY_QUICK_VIEW'), 'details'); ?>
        <div class="media-categories-lists">
          <?php echo $this->loadTemplate('list'); ?>
          <div class="clear"></div>
        </div>
        <?php echo JHtml::_('bootstrap.endSlide'); ?>
        <?php echo JHtml::_('bootstrap.endAccordion'); ?>
        <div class="clear"></div>
      <?php endif; ?>
      <div class="clear"></div>
    </div>
    <div class="media-<?php echo $this->display; ?>-view">    
      <?php echo $this->loadTemplate($this->display); ?>
    </div>
  </div>
</form>
