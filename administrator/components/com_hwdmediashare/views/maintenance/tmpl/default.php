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
<form action="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=maintenance'); ?>" method="post" name="adminForm" id="adminForm">
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
                                        <div id="ajax-container-cleancategorymap" class="pull-right"></div>
                                        <?php echo JText::_('COM_HWDMS_CLEAN_CATEGORY_MAP'); ?>
                                </td>
                        </tr>
                        <tr>
                                <td>
                                        <div id="ajax-container-emptyuploadtokens" class="pull-right"></div>
                                        <?php echo JText::_('COM_HWDMS_EMPTY_UPLOAD_TOKENS'); ?>
                                </td>
                        </tr>
                        <tr>
                                <td>
                                        <div id="ajax-container-purgeoldprocesses" class="pull-right"></div>
                                        <?php echo JText::_('COM_HWDMS_PURGE_OLD_PROCESSES'); ?>
                                </td>
                        </tr>
                        <tr>
                                <td>
                                        <div id="ajax-container-uninstalloldextensions" class="pull-right"></div>
                                        <?php echo JText::_('COM_HWDMS_UNINSTALL_OLD_EXTENSIONS'); ?>
                                </td>
                        </tr>
                        <tr>
                                <td>
                                        <div id="ajax-container-databaseindexoptimisation" class="pull-right"></div>
                                        <?php echo JText::_('COM_HWDMS_DATABASE_INDEX_OPTIMISATION'); ?>
                                </td>
                        </tr>
                        <tr>
                                <td>
                                        <div id="ajax-container-migratelegacytags" class="pull-right"></div>
                                        <?php echo JText::_('COM_HWDMS_MIGRATE_LEGACY_TAGS'); ?>
                                </td>
                        </tr>
                </tbody>
        </table>
        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>
        </div>
</form>
