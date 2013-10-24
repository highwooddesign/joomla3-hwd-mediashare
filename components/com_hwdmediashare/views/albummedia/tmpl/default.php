<?php
/**
 * @version    SVN $Id: default.php 1156 2013-02-21 11:23:16Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$selectOptions = array("linked" => "COM_HWDMS_LINKED", "all" => "COM_HWDMS_ALL");

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

JHtml::_('behavior.framework');
?>
<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm">
  <div id="hwd-container"> <a name="top" id="top"></a>
    <!-- Media Header -->
    <div class="media-header">
      <h2 class="media-album-title"><?php echo JText::_($this->view_header);?></h2>
      
      <div class="fltrt">
        <button type="button" onclick="Joomla.submitform('<?php echo $this->view_list;?>.unlink', this.form);">
        <?php echo JText::_('COM_HWDMS_REMOVE');?></button>
        <?php if ($this->viewAll) : ?>
        <button type="button" onclick="Joomla.submitform('<?php echo $this->view_list;?>.link', this.form);">
        <?php echo JText::_('COM_HWDMS_ADD');?></button>
        <?php endif; ?>
        <button type="button" onclick="<?php echo JRequest::getBool('refresh', 0) ? 'window.parent.location.href=window.parent.location.href;' : '';?>  window.parent.SqueezeBox.close();">
        <?php echo JText::_('JCANCEL');?></button>

        <select name="filter_linked" class="inputbox" onchange="this.form.submit()">
        <?php echo JHtml::_('select.options', $selectOptions, 'value', 'text', $this->state->get('filter.linked'), true);?>
        </select>
      </div>
      
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
        <!-- @TODO add hidden inputs -->
        <input type="hidden" name="tmpl" value="component" />
        <input type="hidden" name="album_id" value="<?php echo $this->albumId;?>" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="view" value="<?php echo $this->view_list;?>" />
        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
        <?php echo JHtml::_('form.token'); ?>
      </fieldset>
      <div class="clear"></div>
    </div>
    <?php echo $this->loadTemplate('list');?>
    <!-- Pagination -->
    <div class="pagination"> <?php echo $this->pagination->getPagesLinks(); ?> </div>
  </div>
</form>