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
?>
<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm">
  <div id="hwd-container" class="<?php echo $this->pageclass_sfx;?>"> <a name="top" id="top"></a>
    <!-- Media Navigation -->
    <?php echo hwdMediaShareHelperNavigation::getInternalNavigation(); ?>
    <!-- Media Header -->
    <div class="media-header">
      <h2 class="media-channel-title"><?php echo $this->escape($this->params->get('page_heading')); ?></h2>
      <!-- Buttons -->
      <div class="btn-group pull-right">
        <?php if ($user->id): ?>
          <a title="<?php echo JText::_('COM_HWDMS_MY_CHANNEL'); ?>" href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getChannelRoute($user->id)); ?>" class="btn"><i class="icon-user"></i> <?php echo JText::_('COM_HWDMS_MY_CHANNEL'); ?></a>
        <?php endif; ?>  
        <?php if ($this->state->get('channels.show_featured') == 'only'): ?>
          <a title="<?php echo JText::_('COM_HWDMS_FEATURED'); ?>" href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getChannelsRoute(array('show_featured' => 'show'))); ?>" class="btn btn-info active"><?php echo JText::_('COM_HWDMS_FEATURED'); ?></a>
        <?php else: ?>  
          <a title="<?php echo JText::_('COM_HWDMS_SHOW_FEATURED'); ?>" href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getChannelsRoute(array('show_featured' => 'only'))); ?>" class="btn"><?php echo JText::_('COM_HWDMS_SHOW_FEATURED'); ?></a>
        <?php endif; ?>  
        <?php if ($this->params->get('list_details_button') != '0') : ?>
          <a title="<?php echo JText::_('COM_HWDMS_DETAILS'); ?>" href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getChannelsRoute(array('display' => 'details'))); ?>" class="btn"><i class="icon-image"></i></a>
        <?php endif; ?>
        <?php if ($this->params->get('list_list_button') != '0') : ?>
          <a title="<?php echo JText::_('COM_HWDMS_LIST'); ?>" href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getChannelsRoute(array('display' => 'list'))); ?>" class="btn"><i class="icon-list"></i></a>
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
        <?php echo JLayoutHelper::render('channels_' . $this->display, $this, JPATH_ROOT.'/components/com_hwdmediashare/libraries/layouts'); ?>
      <?php endif; ?>  
    </div>  
    <!-- Pagination -->
    <div class="pagination"> <?php echo $this->pagination->getPagesLinks(); ?> </div>
  </div>
</form>