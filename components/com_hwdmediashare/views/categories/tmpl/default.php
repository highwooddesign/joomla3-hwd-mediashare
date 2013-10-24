<?php
/**
 * @version    SVN $Id: default.php 457 2012-08-02 15:09:12Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

?>
<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm">
  <div id="hwd-container"> <a name="top" id="top"></a>
    <!-- Media Navigation -->
    <?php echo hwdMediaShareHelperNavigation::getInternalNavigation(); ?>
    <!-- Media Header -->
    <div class="media-header">
      <h2 class="media-category-title"><?php echo JText::_('COM_HWDMS_CATEGORIES'); ?></h2>
      <ul class="media-category-ls">
        <?php if ($this->params->get('list_details_button') != 'hide') :?><li><a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getSelfRoute('details')); ?>" class="ls-detail" title="<?php echo JText::_('COM_HWDMS_DETAILS'); ?>"><?php echo JText::_('COM_HWDMS_DETAILS'); ?></a></li><?php endif; ?>
        <?php if ($this->params->get('list_tree_button') != 'hide') :?><li><a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getSelfRoute('tree')); ?>" class="ls-tree" title="<?php echo JText::_('COM_HWDMS_TREE'); ?>"><?php echo JText::_('COM_HWDMS_TREE'); ?></a></li><?php endif; ?>
      </ul>
      <div class="clear"></div>
      <?php if ($this->params->get('category_list_quick_view') != 'hide' && count($this->items[$this->parent->id]) != 0) :?>
        <?php  echo JHtml::_('sliders.start', 'media-category-slider', array('startOffset' => 1)); ?>
        <?php  echo JHtml::_('sliders.panel',JText::_('COM_HWDMS_CATEGORY_QUICK_VIEW'), 'quick-view'); ?>
        <div class="media-categories-lists">
          <?php echo $this->loadTemplate('list'); ?>
          <div class="clear"></div>
        </div>
        <?php echo JHtml::_('sliders.end'); ?>
        <div class="clear"></div>
      <?php endif; ?>
      <div class="clear"></div>
    </div>
    <?php echo $this->loadTemplate($this->display); ?>
  </div>
</form>
