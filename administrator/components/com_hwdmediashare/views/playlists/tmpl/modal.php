<?php
/**
 * @version    SVN $Id: modal.php 1142 2013-02-21 11:10:18Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      06-Dec-2011 09:59:57
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// load tooltip behavior
JHtml::_('behavior.tooltip');
JHtml::_('script','system/multiselect.js', false, true);

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$function       = JRequest::getCmd('function', 'jSelectPlaylist_playlist_id');
?>
<form action="<?php echo JRoute::_('index.php?option=com_hwdmediashare'); ?>" method="post" name="adminForm" id="adminForm">
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
                                                <a class="pointer" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($item->title)); ?>');"><?php echo $this->escape($item->title); ?></a>
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
                <input type="hidden" name="view" value="playlists" />
                <input type="hidden" name="boxchecked" value="0" />
                <input type="hidden" name="layout" value="modal" />
                <input type="hidden" name="tmpl" value="component" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<input type="hidden" name="function" value="jSelectPlaylist_assign_playlist_id" />                
                <?php echo JHtml::_('form.token'); ?>
        </div>
</form>
