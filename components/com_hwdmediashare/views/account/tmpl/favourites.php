<?php
/**
 * @version    SVN $Id: favourites.php 1151 2013-02-21 11:21:06Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      18-Jan-2012 17:30:20
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
      <h2 class="media-media-title"><?php echo JText::_('COM_HWDMS_MY_FAVOURITES'); ?></h2>
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
      <div class="clear"></div>
      <button type="button" class="button" onclick="Joomla.submitbutton('account.removefavourite')"><?php echo JText::_('COM_HWDMS_REMOVE'); ?></button>
      <div class="clear"></div>
    </div>
    <?php echo $this->loadTemplate('list'); ?>
    <!-- Pagination -->
    <div class="pagination"> <?php echo $this->pagination->getPagesLinks(); ?> </div>
  </div>
</form>