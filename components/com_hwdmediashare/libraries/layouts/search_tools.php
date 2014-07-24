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

$data = $displayData;

// Receive overridable options
$data['options'] = !empty($data['options']) ? $data['options'] : array();

// Set some basic options
$customOptions = array(
	'filtersHidden'       => isset($data['options']['filtersHidden']) ? $data['options']['filtersHidden'] : empty($data['view']->activeFilters),
	'defaultLimit'        => isset($data['options']['defaultLimit']) ? $data['options']['defaultLimit'] : JFactory::getApplication()->getCfg('list_limit', 20),
	'searchFieldSelector' => '#filter_search',
	'orderFieldSelector'  => '#list_fullordering'
);

$data['options'] = array_unique(array_merge($customOptions, $data['options']));

$formSelector = !empty($data['options']['formSelector']) ? $data['options']['formSelector'] : '#adminForm';

// Load search tools
JHtml::_('searchtools.form', $formSelector, $data['options']);

$filters = $data['view']->filterForm->getGroup('filter');
$list = $data['view']->filterForm->getGroup('list');
?>
<div class="js-stools clearfix">
	<div class="clearfix">
		<?php if ($data['view']->params->get('list_filter_search') != '0') :?>
                <div class="js-stools-container-bar">
                        <?php if (!empty($filters['filter_search'])) : ?>
                                <label for="filter_search" class="element-invisible">
                                        <?php echo JText::_('JSEARCH_FILTER'); ?>
                                </label>
                                <div class="btn-wrapper input-append">
                                        <?php echo $filters['filter_search']->input; ?>
                                        <button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>">
                                                <i class="icon-search"></i>
                                        </button>
                                </div>
                                <div class="btn-wrapper">
                                        <button type="button" class="btn hasTooltip js-stools-btn-clear" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>">
                                                <?php echo JText::_('JSEARCH_FILTER_CLEAR');?>
                                        </button>
                                </div>
                        <?php endif; ?>
                </div>
                <?php endif; ?>
		<?php if ($list && ($data['view']->params->get('list_filter_ordering') != '0' || $data['view']->params->get('list_filter_pagination') != '0')) : ?>
                <div class="js-stools-container-list hidden-phone hidden-tablet">
                        <div class="ordering-select hidden-phone">
                                <?php if (isset($list['list_fullordering']) && $data['view']->params->get('list_filter_ordering') != '0') : ?>
                                        <div class="js-stools-field-list">
                                                <?php echo $list['list_fullordering']->input; ?>
                                        </div>
                                <?php endif; ?>                            
                                <?php if (isset($list['list_limit']) && $data['view']->params->get('list_filter_pagination') != '0') : ?>
                                        <div class="js-stools-field-list">
                                                <?php echo $list['list_limit']->input; ?>
                                        </div>
                                <?php endif; ?>  
                        </div>
		</div>
                <?php endif; ?>
                <input type="hidden" name="task" value="" />
                <input type="hidden" name="limitstart" value="0" />            
	</div>
</div>
