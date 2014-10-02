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
<div id="hwd-container" class="<?php echo $this->pageclass_sfx;?>"> <a name="top" id="top"></a>
  <!-- Media Navigation -->
  <?php echo hwdMediaShareHelperNavigation::getInternalNavigation(); ?>
  <div class="media-featured-view">
    <?php if ($this->document->countModules('media-discover-leading')): ?>
    <div class="row-fluid">
      <div class="span12">
        <?php echo hwdMediaShareHelperModule::_loadpos('media-discover-leading'); ?>
      </div>
    </div>
    <?php endif; ?>  
    <?php if ($this->document->countModules('media-discover-1') || $this->document->countModules('media-discover-2')): ?>
    <div class="row-fluid">
      <div class="span6">
        <?php echo hwdMediaShareHelperModule::_loadpos('media-discover-1'); ?>
      </div>
      <div class="span6">
        <?php echo hwdMediaShareHelperModule::_loadpos('media-discover-2'); ?>
      </div>
    </div>
    <?php endif; ?>  
    <?php if ($this->document->countModules('media-discover-3') || $this->document->countModules('media-discover-4')): ?>
    <div class="row-fluid">
      <div class="span6">
        <?php echo hwdMediaShareHelperModule::_loadpos('media-discover-3'); ?>
      </div>
      <div class="span6">
        <?php echo hwdMediaShareHelperModule::_loadpos('media-discover-4'); ?>
      </div>
    </div>
    <?php endif; ?>  
  </div>
</div>
