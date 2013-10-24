<?php
/**
 * @version    SVN $Id: default.php 277 2012-03-28 10:03:31Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.framework', true);
JHtml::_('behavior.tooltip');

?>
<form action="<?php echo JRoute::_('index.php?option=com_hwdmediashare'); ?>" method="post" name="adminForm" id="adminForm">
        <table class="adminlist">
                <tbody>
                        <tr class="row0">
                                <td>
                                        <div><?php echo JText::_( 'COM_HWDMS_CLEAN_CATEGORY_MAP' ); ?></div>
                                </td>
                                <td>
                                        <div id="ajax-container-cleancategorymap" class="fltrt"></div>
                                </td>
                        </tr>
                        <tr class="row1">
                                <td>
                                        <div><?php echo JText::_( 'COM_HWDMS_CLEAN_TAG_MAP' ); ?></div>
                                </td>
                                <td>
                                        <div id="ajax-container-cleantagmap" class="fltrt"></div>
                                </td>
                        </tr>
                        <tr class="row0">
                                <td>
                                        <div><?php echo JText::_( 'COM_HWDMS_EMPTY_UPLOAD_TOKENS' ); ?></div>
                                </td>
                                <td>
                                        <div id="ajax-container-emptyuploadtokens" class="fltrt"></div>
                                </td>
                        </tr>
                </tbody>
        </table>
	<input type="hidden" name="task" value="" />
        <input type="hidden" name="view" value="maintenance" />
        <?php echo JHtml::_('form.token'); ?>
</form>
