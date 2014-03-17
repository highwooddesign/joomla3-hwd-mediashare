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
<?php if (!empty( $this->sidebar)) : ?>
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
                                <td width="80%">
                                        <div><?php echo JText::sprintf('COM_HWDMS_N_PENDING_MEDIA', $this->media); ?></div>
                                </td>
                                <td width="20%">
                                        <div class="btn-group">
                                                <a class="btn" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=media&filter_status=2'); ?>"><?php echo JText::_('COM_HWDMS_BTN_VIEW'); ?></a>
                                        </div>                            
                                </td>
                        </tr>
                        <tr>
                                <td>
                                        <div><?php echo JText::sprintf('COM_HWDMS_N_PENDING_ALBUMS', $this->albums); ?></div>
                                </td>
                                <td>
                                        <div class="btn-group">
                                                <a class="btn" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=albums&filter_status=2'); ?>"><?php echo JText::_('COM_HWDMS_BTN_VIEW'); ?></a>
                                        </div>                                      
                                </td>
                        </tr>
                        <tr>
                                <td>
                                        <div><?php echo JText::sprintf('COM_HWDMS_N_PENDING_GROUPS', $this->groups); ?></div>
                                </td>
                                <td>
                                        <div class="btn-group">
                                                <a class="btn" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=groups&filter_status=2'); ?>"><?php echo JText::_('COM_HWDMS_BTN_VIEW'); ?></a>
                                        </div>                                       
                                </td>
                        </tr>
                        <tr>
                                <td>
                                        <div><?php echo JText::sprintf('COM_HWDMS_N_PENDING_USERS', $this->users); ?></div>
                                </td>
                                <td>
                                        <div class="btn-group">
                                                <a class="btn" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=users&filter_status=2'); ?>"><?php echo JText::_('COM_HWDMS_BTN_VIEW'); ?></a>
                                        </div>                                      
                                </td>
                        </tr>
                        <tr>
                                <td>
                                        <div><?php echo JText::sprintf('COM_HWDMS_N_PENDING_PLAYLISTS', $this->playlists); ?></div>
                                </td>
                                <td>
                                        <div class="btn-group">
                                                <a class="btn" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=playlists&filter_status=2'); ?>"><?php echo JText::_('COM_HWDMS_BTN_VIEW'); ?></a>
                                        </div>                                      
                                </td>
                        </tr>
                        <tr>
                                <td>
                                        <div><?php echo JText::sprintf('COM_HWDMS_N_PENDING_ACTIVITIES', $this->activities); ?></div>
                                </td>
                                <td>
                                        <div class="btn-group">
                                                <a class="btn" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=activities&filter_status=2'); ?>"><?php echo JText::_('COM_HWDMS_BTN_VIEW'); ?></a>
                                        </div>
                                </td>
                        </tr>
                </tbody>
        </table>
        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>
        </div>
</form>
