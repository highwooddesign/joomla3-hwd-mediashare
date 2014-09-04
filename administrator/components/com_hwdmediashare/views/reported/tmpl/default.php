<?php
/**
 * @package     Joomla.administrator
 * @subpackage  Component.hwdmediashare
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

JHtml::_('behavior.framework', true);
JHtml::_('behavior.modal');
?>
<form action="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=reported'); ?>" method="post" name="adminForm" id="adminForm">
<?php if (!empty($this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
        <table class="table table-striped" id="maintenanceList">
                <tbody>
                        <tr>
                                <td>
                                        <div class="btn-group pull-right">
                                                <a class="btn" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=media&filter[status]=3'); ?>"><i class="icon-eye"></i> <?php echo JText::_('COM_HWDMS_BTN_VIEW'); ?></a>
                                                <a class="btn modal" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=reported&layout=media&tmpl=component'); ?>" rel="{handler: 'iframe', size: {x: 800, y: 500}}"><i class="icon-cog"></i> <?php echo JText::_('COM_HWDMS_BTN_MANAGE'); ?></a>
                                        </div>                                     
                                        <?php echo JText::sprintf('COM_HWDMS_N_REPORTED_MEDIA', $this->media); ?>
                                </td>
                        </tr>
                        <tr>
                                <td>
                                        <div class="btn-group pull-right">
                                                <a class="btn" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=albums&filter[status]=3'); ?>"><i class="icon-eye"></i> <?php echo JText::_('COM_HWDMS_BTN_VIEW'); ?></a>
                                                <a class="btn modal" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=reported&layout=albums&tmpl=component'); ?>" rel="{handler: 'iframe', size: {x: 800, y: 500}}"><i class="icon-cog"></i> <?php echo JText::_('COM_HWDMS_BTN_MANAGE'); ?></a>
                                        </div>                                      
                                        <?php echo JText::sprintf('COM_HWDMS_N_REPORTED_ALBUMS', $this->albums); ?>
                                </td>
                        </tr>
                        <tr>
                                <td>
                                        <div class="btn-group pull-right">
                                                <a class="btn" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=groups&filter[status]=3'); ?>"><i class="icon-eye"></i> <?php echo JText::_('COM_HWDMS_BTN_VIEW'); ?></a>
                                                <a class="btn modal" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=reported&layout=groups&tmpl=component'); ?>" rel="{handler: 'iframe', size: {x: 800, y: 500}}"><i class="icon-cog"></i> <?php echo JText::_('COM_HWDMS_BTN_MANAGE'); ?></a>
                                        </div>                                       
                                        <?php echo JText::sprintf('COM_HWDMS_N_REPORTED_GROUPS', $this->groups); ?>
                                </td>
                        </tr>
                        <tr>
                                <td>
                                        <div class="btn-group pull-right">
                                                <a class="btn" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=channels&filter[status]=3'); ?>"><i class="icon-eye"></i> <?php echo JText::_('COM_HWDMS_BTN_VIEW'); ?></a>
                                                <a class="btn modal" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=reported&layout=channels&tmpl=component'); ?>" rel="{handler: 'iframe', size: {x: 800, y: 500}}"><i class="icon-cog"></i> <?php echo JText::_('COM_HWDMS_BTN_MANAGE'); ?></a>
                                        </div>                                       
                                        <?php echo JText::sprintf('COM_HWDMS_N_REPORTED_USERS', $this->users); ?>
                                </td>
                        </tr>
                        <tr>
                                <td>
                                        <div class="btn-group pull-right">
                                                <a class="btn" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=playlists&filter[status]=3'); ?>"><i class="icon-eye"></i> <?php echo JText::_('COM_HWDMS_BTN_VIEW'); ?></a>
                                                <a class="btn modal" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=reported&layout=playlists&tmpl=component'); ?>" rel="{handler: 'iframe', size: {x: 800, y: 500}}"><i class="icon-cog"></i> <?php echo JText::_('COM_HWDMS_BTN_MANAGE'); ?></a>
                                        </div>                                      
                                        <?php echo JText::sprintf('COM_HWDMS_N_REPORTED_PLAYLISTS', $this->playlists); ?>
                                </td>
                        </tr>
                </tbody>
        </table>
        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>
        </div>
</form>