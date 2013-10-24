<?php
/**
 * @version    SVN $Id: editor.php 1544 2013-06-11 10:43:48Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      17-Mar-2012 13:04:44
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// load tooltip behavior
JHtml::_('behavior.tooltip');
JHtml::_('script','system/multiselect.js', false, true);

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$function       = JRequest::getCmd('function', 'jSelectMedia');
$displayArray = array(JHtml::_('select.option', 'inline', JText::_('COM_HWDMS_SHOW_IN_PAGE')), JHtml::_('select.option', 'modal', JText::_('COM_HWDMS_OPEN_IN_LIGHTBOX')), JHtml::_('select.option', 'link', JText::_('COM_HWDMS_LINK_TO_MEDIA_PAGE')));
$alignArray = array(JHtml::_('select.option', 'left', JText::_('COM_HWDMS_LEFT')), JHtml::_('select.option', 'center', JText::_('COM_HWDMS_CENTER')), JHtml::_('select.option', 'right', JText::_('COM_HWDMS_RIGHT')));

// Load default parameters from content media plugin:
$plugin =& JPluginHelper::getPlugin('content', 'media');
$params = new JRegistry( @$plugin->params );
?>
<form action="<?php echo JRoute::_('index.php?option=com_hwdmediashare'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-select fltrt">
			<label class="filter-width-lbl" for="filter_width"><?php echo JText::_('COM_HWDMS_WIDTH'); ?></label>
			<input type="text" name="filter_width" id="filter_width" value="<?php echo $params->get('width','200'); ?>" title="<?php echo JText::_('COM_HWDMS_WIDTH'); ?>" />
			<select name="filter_align" id="filter_align" class="inputbox">
				<?php echo JHtml::_('select.options', $alignArray, 'value', 'text', $params->get('align','left'), true);?>
			</select>
			<select name="filter_display" id="filter_display" class="inputbox">
				<?php echo JHtml::_('select.options', $displayArray, 'value', 'text', $params->get('display','modal'), true);?>
			</select>
		</div>
	</fieldset>
        <fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_HWDMS_SEARCH_IN_TITLE'); ?>" />
			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="filter-select fltrt">
			<select name="filter_published" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true);?>
			</select>
                        <select name="filter_access" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_ACCESS');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'));?>
			</select>
			<select name="filter_language" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'));?>
			</select>
		</div>
	</fieldset>
	<div class="clr"> </div>
        <table class="adminlist">
                <thead>
                        <tr>
                                <th>
                                        <?php echo JHtml::_('grid.sort',  JText::_('COM_HWDMS_TITLE'), 'a.title', $listDirn, $listOrder); ?>
                                </th>
                                <th width="10%">
                                        <?php echo JHtml::_('grid.sort', JText::_('JGRID_HEADING_ACCESS'), 'access_level', $listDirn, $listOrder); ?>
                                </th>
                                <th width="10%">
                                        <?php echo JHtml::_('grid.sort', JText::_('JGRID_HEADING_CREATED_BY'), 'a.created_user_id', $listDirn, $listOrder); ?>
                                </th>
                                <th width="5%">
                                        <?php echo JHtml::_('grid.sort', JText::_('JDATE'), 'a.created', $listDirn, $listOrder); ?>
                                </th>
                                <th width="5%">
                                        <?php echo JHtml::_('grid.sort', JText::_('JGLOBAL_HITS'), 'a.hits', $listDirn, $listOrder); ?>
                                </th>
                                <th width="5%">
                                        <?php echo JHtml::_('grid.sort', JText::_('JGRID_HEADING_LANGUAGE'), 'language', $listDirn, $listOrder); ?>
                                </th>
                           </tr>
                </thead>
                <tbody>
                        <?php foreach($this->items as $i => $item):
                                $owner =& JFactory::getUser($item->created_user_id);
                                ?>
                                <tr class="row<?php echo $i % 2; ?>">
                                        <td>
                                                <span class="editlinktip hasTip" title="<?php echo $this->escape($item->title); ?>::<?php echo $this->escape($item->description); ?>" >
                                                <a class="pointer" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $item->id; ?>', document.getElementById('filter_width').value, document.getElementById('filter_align').options[document.getElementById('filter_align').selectedIndex].value, document.getElementById('filter_display').options[document.getElementById('filter_display').selectedIndex].value);"><?php echo $this->escape($item->title); ?></a>
                                                </span>
                                        </td>
                                        <td class="center">
                                                <?php echo $this->escape($item->access_level); ?>
                                        </td>
                                        <td class="center">
                                                <?php echo $this->escape($owner->username); ?>
                                        </td>
                                        <td class="center nowrap">
                                                <?php echo JHtml::_('date',$item->created, JText::_('DATE_FORMAT_LC4')); ?>
                                        </td>
                                        <td class="center">
                                                <?php echo (int) $item->hits; ?>
                                        </td>
                                        <td class="center">
                                                <?php if ($item->language=='*'):?>
                                                        <?php echo JText::alt('JALL','language'); ?>
                                                <?php else:?>
                                                        <?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
                                                <?php endif;?>
                                        </td>
                                </tr>
                        <?php endforeach; ?>
                </tbody>
                <tfoot>
                        <tr>
                                <td colspan="6"><?php echo $this->pagination->getListFooter(); ?></td>
                        </tr>
                </tfoot>
        </table>
        <div>
                <input type="hidden" name="task" value="" />
                <input type="hidden" name="view" value="media" />
                <input type="hidden" name="boxchecked" value="0" />
                <input type="hidden" name="layout" value="editor" />
                <input type="hidden" name="tmpl" value="component" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
                <?php echo JHtml::_('form.token'); ?>
        </div>
</form>
