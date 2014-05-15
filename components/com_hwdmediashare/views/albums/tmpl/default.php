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
$canAdd = $user->authorise('core.create', 'com_hwdmediashare');
?>
<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm">
  <div id="hwd-container" class="<?php echo $this->pageclass_sfx;?>"> <a name="top" id="top"></a>
    <!-- Media Navigation -->
    <?php echo hwdMediaShareHelperNavigation::getInternalNavigation(); ?>
    <!-- Media Header -->
    <div class="media-header">
      <?php if ($this->params->get('item_meta_title') != 'hide' && $this->params->get('show_page_heading', 1)) :?>
        <h2 class="media-album-title"><?php echo $this->escape($this->params->get('page_heading')); ?></h2>
      <?php endif; ?>            
      <!-- Buttons -->
      <div class="btn-group pull-right">
        <?php if ($canAdd): ?>
          <a title="<?php echo JText::_('COM_HWDMS_ADD_ALBUM'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=albumform&layout=edit&return=' . $this->return); ?>" class="btn"><i class="icon-plus"></i> <?php echo JText::_('COM_HWDMS_ADD_ALBUM'); ?></a>
        <?php endif; ?>
        <?php if ($this->state->get('albums.show_featured') == 'only'): ?>
          <a title="<?php echo JText::_('COM_HWDMS_FEATURED'); ?>" href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getAlbumsRoute(array('show_featured' => 'show'))); ?>" class="btn btn-info active"><?php echo JText::_('COM_HWDMS_FEATURED'); ?></a>
        <?php else: ?>  
          <a title="<?php echo JText::_('COM_HWDMS_SHOW_FEATURED'); ?>" href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getAlbumsRoute(array('show_featured' => 'only'))); ?>" class="btn"><?php echo JText::_('COM_HWDMS_SHOW_FEATURED'); ?></a>
        <?php endif; ?>  
        <?php if ($this->params->get('list_details_button') != 'hide') : ?>
          <a title="<?php echo JText::_('COM_HWDMS_DETAILS'); ?>" href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getAlbumsRoute(array('display' => 'details'))); ?>" class="btn"><i class="icon-image"></i></a>
        <?php endif; ?>
        <?php if ($this->params->get('list_list_button') != 'hide') : ?>
          <a title="<?php echo JText::_('COM_HWDMS_LIST'); ?>" href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getAlbumsRoute(array('display' => 'list'))); ?>" class="btn"><i class="icon-list"></i></a>
        <?php endif; ?>
      </div>        
      <div class="clear"></div>
      <!-- Search Filters -->
      <?php echo JLayoutHelper::render('search_tools', array('view' => $this), JPATH_ROOT.'/components/com_hwdmediashare/libraries/layouts'); ?>
      <div class="clear"></div>
    </div>
    <div class="media-<?php echo $this->display; ?>-view">
        <?php echo JLayoutHelper::render('albums_' . $this->display, $this, JPATH_ROOT.'/components/com_hwdmediashare/libraries/layouts'); ?>
    </div>  
    <!-- Pagination -->
    <div class="pagination"> <?php echo $this->pagination->getPagesLinks(); ?> </div>
  </div>
</form>
