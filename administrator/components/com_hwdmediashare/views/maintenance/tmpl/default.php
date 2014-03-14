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
                                <td width="90%">
                                        <div><?php echo JText::_( 'COM_HWDMS_CLEAN_CATEGORY_MAP' ); ?></div>
                                </td>
                                <td width="10%">
                                        <div id="ajax-container-cleancategorymap" class="fltrt"></div>
                                </td>
                        </tr>
                        <tr>
                                <td>
                                        <div><?php echo JText::_( 'COM_HWDMS_CLEAN_TAG_MAP' ); ?></div>
                                </td>
                                <td>
                                        <div id="ajax-container-cleantagmap" class="fltrt"></div>
                                </td>
                        </tr>
                        <tr>
                                <td>
                                        <div><?php echo JText::_( 'COM_HWDMS_EMPTY_UPLOAD_TOKENS' ); ?></div>
                                </td>
                                <td>
                                        <div id="ajax-container-emptyuploadtokens" class="fltrt"></div>
                                </td>
                        </tr>
                        <tr>
                                <td>
                                        <div><?php echo JText::_( 'COM_HWDMS_PURGE_OLD_PROCESSES' ); ?></div>
                                </td>
                                <td>
                                        <div id="ajax-container-purgeoldprocesses" class="fltrt"></div>
                                </td>
                        </tr>
                </tbody>
        </table>
        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>
        </div>
</form>
