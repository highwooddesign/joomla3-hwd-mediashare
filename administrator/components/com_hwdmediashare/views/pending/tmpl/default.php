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
?>
<form action="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=pending'); ?>" method="post" name="adminForm" id="adminForm">
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
                                                <a class="btn" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=media&filter[status]=2'); ?>"><i class="icon-eye"></i> <?php echo JText::_('COM_HWDMS_BTN_VIEW'); ?></a>
                                        </div>                                     
                                        <?php echo JText::sprintf('COM_HWDMS_N_PENDING_MEDIA', $this->media); ?>
                                </td>
                        </tr>
                        <tr>
                                <td>
                                        <div class="btn-group pull-right">
                                                <a class="btn" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=albums&filter[status]=2'); ?>"><i class="icon-eye"></i> <?php echo JText::_('COM_HWDMS_BTN_VIEW'); ?></a>
                                        </div>                                     
                                        <?php echo JText::sprintf('COM_HWDMS_N_PENDING_ALBUMS', $this->albums); ?>
                                </td>
                        </tr>
                        <tr>
                                <td>
                                        <div class="btn-group pull-right">
                                                <a class="btn" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=groups&filter[status]=2'); ?>"><i class="icon-eye"></i> <?php echo JText::_('COM_HWDMS_BTN_VIEW'); ?></a>
                                        </div>                                     
                                        <?php echo JText::sprintf('COM_HWDMS_N_PENDING_GROUPS', $this->groups); ?>
                                </td>
                        </tr>
                        <tr>
                                <td>
                                        <div class="btn-group pull-right">
                                                <a class="btn" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=channels&filter[status]=2'); ?>"><i class="icon-eye"></i> <?php echo JText::_('COM_HWDMS_BTN_VIEW'); ?></a>
                                        </div>                                     
                                        <?php echo JText::sprintf('COM_HWDMS_N_PENDING_CHANNELS', $this->channels); ?>
                                </td>
                        </tr>
                        <tr>
                                <td>
                                        <div class="btn-group pull-right">
                                                <a class="btn" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=playlists&filter[status]=2'); ?>"><i class="icon-eye"></i> <?php echo JText::_('COM_HWDMS_BTN_VIEW'); ?></a>
                                        </div>                                      
                                        <?php echo JText::sprintf('COM_HWDMS_N_PENDING_PLAYLISTS', $this->playlists); ?>
                                </td>
                        </tr>
                </tbody>
        </table>
        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>
        </div>
</form>
