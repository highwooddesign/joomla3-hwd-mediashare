<?php
/**
 * @version    SVN $Id: default.php 1033 2013-02-01 10:59:03Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      25-Nov-2011 17:33:20
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.calendar');
JHtml::_('behavior.formvalidation');

$lang = JFactory::getLanguage();
$upper_limit = $lang->getUpperLimitSearchWord();
$area = JRequest::getInt('area',1);

?>
<div class="search">
<form id="searchForm" action="<?php echo JRoute::_('index.php?option=com_hwdmediashare');?>" method="post">
  <div id="hwd-container"> <a name="top" id="top"></a>
    <!-- Media Navigation -->
    <?php echo hwdMediaShareHelperNavigation::getInternalNavigation(); ?>
    <!-- Media Header -->
    <div class="media-header" style="float:right;">
      <?php if ($area == 1) : ?><strong><?php echo JText::_('COM_HWDMS_MEDIA'); ?></strong><?php else: ?><a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getSearchRoute(array('area'=>1))); ?>"><?php echo JText::_('COM_HWDMS_MEDIA'); ?></a><?php endif; ?>  
      <?php if ($area == 2) : ?><strong><?php echo JText::_('COM_HWDMS_ALBUMS'); ?></strong><?php else: ?><a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getSearchRoute(array('area'=>2))); ?>"><?php echo JText::_('COM_HWDMS_ALBUMS'); ?></a><?php endif; ?>  
      <?php if ($area == 3) : ?><strong><?php echo JText::_('COM_HWDMS_GROUPS'); ?></strong><?php else: ?><a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getSearchRoute(array('area'=>3))); ?>"><?php echo JText::_('COM_HWDMS_GROUPS'); ?></a><?php endif; ?>  
      <?php if ($area == 4) : ?><strong><?php echo JText::_('COM_HWDMS_PLAYLISTS'); ?></strong><?php else: ?><a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getSearchRoute(array('area'=>4))); ?>"><?php echo JText::_('COM_HWDMS_PLAYLISTS'); ?></a><?php endif; ?>  
    </div>
    <div class="clear"></div>

    <div class="media-header">
      <h2 class="media-media-title"><?php echo JText::_('COM_HWDMS_SEARCH'); ?></h2>
      <div class="clear"></div>
    </div>

    <!-- Form -->
    <fieldset class="word">
      <label for="search-searchword">
        <?php echo JText::_('COM_SEARCH_SEARCH_KEYWORD'); ?>
      </label>
      <input type="text" name="searchword" id="search-searchword" size="30" maxlength="<?php echo $upper_limit; ?>" value="<?php echo $this->escape($this->origkeyword); ?>" class="inputbox" />
      <button name="Search" onclick="this.form.submit()" class="button"><?php echo JText::_('COM_SEARCH_SEARCH');?></button>
      <input type="hidden" name="task" value="search" />
    </fieldset>

    <div class="searchintro">
      <?php if (!empty($this->searchword)):?>
        <p><?php echo JText::plural('COM_SEARCH_SEARCH_KEYWORD_N_RESULTS', $this->total);?></p>
      <?php endif;?>
    </div>

    <fieldset class="phrases">
      <legend><?php echo JText::_('COM_SEARCH_FOR');?></legend>
      <div class="phrases-box">
        <?php echo $this->lists['searchphrase']; ?>
      </div>
      <div class="ordering-box">
        <label for="ordering" class="ordering">
          <?php echo JText::_('COM_SEARCH_ORDERING');?>
        </label>
        <?php echo $this->lists['ordering'];?>
      </div>
    </fieldset>
     
    <!-- Advanced Header -->    
    <?php echo JHtml::_('sliders.start', 'media-user-slider', array('useCookie'=>true)); ?>
    <?php echo JHtml::_('sliders.panel', JText::_('COM_HWDMS_ADVANCED_SEARCH'), 'search'); ?>
      <fieldset>
        <div class="formelm">
          <?php echo $this->form->getLabel('catid'); ?>
          <?php echo $this->form->getInput('catid'); ?>
        </div>
      </fieldset>
      <?php hwdMediaShareFactory::load('customfields');
      $customfields = hwdMediaShareCustomFields::get(null, $area); ?>
      <?php foreach ($customfields['fields'] as $group => $groupFields) : ?>
        <?php echo JHtml::_('sliders.panel', JText::_($group), 'search'); ?>
        <fieldset>
        <?php foreach ($groupFields as $field) : ?>
        <?php $field = JArrayHelper::toObject ( $field );                 
        $field->value = JRequest::getVar('field'.$field->id); ?>
        <?php if ($field->searchable) : ?>
        <div class="formelm">        
        <label title="" class="hasTip" for="jform_<?php echo $field->id;?>" id="jform_<?php echo $field->id;?>-lbl"><?php echo JText::_( $field->name );?></label>
        <?php echo hwdMediaShareCustomFields::getFieldHTML( $field , '' ); ?>
        </div>        
        <?php endif; ?>   
        <?php endforeach; ?>
        </fieldset>
      <?php endforeach; ?>
      <?php echo JHtml::_('sliders.end'); ?>
      <fieldset><button name="Search" onclick="this.form.submit()" class="button"><?php echo JText::_('COM_HWDMS_ADVANCED_SEARCH');?></button></fieldset>
    </fieldset>

    <?php if ($this->total > 0) : ?>
    <div class="form-limit">
    <label for="limit">
    <?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>
    </label>
    <?php echo $this->pagination->getLimitBox(); ?>
    </div>
    <p class="counter">
    <?php echo $this->pagination->getPagesCounter(); ?>
    </p>
    <?php endif; ?>
        
    <?php 
    if ($this->error==null && count($this->results) > 0) :
            echo $this->loadTemplate('results');
    else :
            echo $this->loadTemplate('error');
    endif; 
    ?>    
  </div>
  <input type="hidden" name="area" value="<?php echo $area; ?>" />
  <input type="hidden" name="task" value="search.search" />
</form>
</div>