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
?>
<div id="ubr_alert_container" class="hide alert">
        <span id="ubr_alert"></span>
</div>
<!-- Start Progress Bar -->
<div id="progress_bar" class="hide">
        <div class="bar1" id="upload_status_wrap">
                <div class="bar2" id="upload_status"></div>
        </div>
        <table class="table table-striped table-bordered table-hover">
            <tbody>
            <tr>
                <th scope="row">
                    <?php echo JText::_('COM_HWDMS_PERCENT_COMPLETE'); ?>
                </th>
                <td class="center">
                    <span id="percent">0%</span>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php echo JText::_('COM_HWDMS_FILES_UPLOADED'); ?>
                </th>
                <td class="center">
                    <span id="uploaded_files">0</span> of <span id="total_uploads"></span>
                </td>
            </tr>
            <?php // HWD Modification: changed name of ID to avoid conflicts ?>
            <tr>
                <th scope="row">
                    <?php echo JText::_('COM_HWDMS_CURRENT_POSITION'); ?>
                </th>
                <td class="center">
                    <span id="currentupld">0</span> / <span id="total_kbytes"></span> KBs
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php echo JText::_('COM_HWDMS_ELAPSED_TIME'); ?>
                </th>
                <td class="center">
                    <span id="time">0</span>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php echo JText::_('COM_HWDMS_EST_TIME_LEFT'); ?>
                </th>
                <td class="center">
                    <span id="remain">0</span>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php echo JText::_('COM_HWDMS_EST_SPEED'); ?>
                </th>
                <td class="center">
                    <span id="speed">0</span> KB/s.
                </td>
            </tr>
            </tbody>
          </table>
</div>
<!-- End Progress Bar -->
<noscript><p><?php echo JText::_('COM_HWDMS_PLEASE_ENABLE_JAVASCRIPT'); ?></p></noscript>
<fieldset class="adminform">
        <div class="control-group">
                <div class="control-label hide">
                        <?php echo $this->form->getLabel('Filedata'); ?>
                </div>
                <div class="controls">
                        <div class="btn-group" id="upload_slots">
                                <input type="file" name="upfile_0" <?php echo $this->config->get('upload_workflow') == 1 ? 'onChange="addUploadSlot(1)"' : ''; ?> onkeypress="return handleKey(event)" value="" class="hwd-form-filedata">
                                <button type="button" id="upload_button" class="btn" name="upload_button" value="Upload" onClick="linkUpload();">
                                        <?php echo JText::_('COM_HWDMS_UPLOAD') ?>
                                </button>
                        </div>                             
                </div>
        </div>
</fieldset>
<p></p>
<div class="well well-small">
        <h3><?php echo JText::_('COM_HWDMS_HELP_AND_SUGGESTIONS'); ?></h3>
        <p><?php echo JText::sprintf('COM_HWDMS_SUPPORTED_FORMATS_LIST_X', implode(', ', $this->localExtensions)); ?> <?php echo JText::sprintf('COM_HWDMS_MAXIMUM_UPLOAD_SIZE_X', hwdMediaShareUpload::getMaximumUploadSize('large')); ?></p>  
</div> 