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

JHtml::_('behavior.tooltip');
JHtml::_('behavior.framework');
JHtml::_('formbehavior.chosen', 'select');
?>
<fieldset class="adminform" id="hwd-upload-fallback">
        <div class="control-group">
                <div class="control-label hide">
                        <?php echo $this->form->getLabel('Filedata'); ?>
                </div>
                <div class="controls">
                        <div class="btn-group">
                                <?php echo $this->form->getInput('Filedata'); ?>
                                <button type="button" class="btn" onclick="Joomla.submitbutton('addmedia.upload')">
                                        <?php echo JText::_('COM_HWDMS_UPLOAD') ?>
                                </button>
                        </div>                             
                </div>
        </div> 
</fieldset>
<div id="hwd-upload-status" class="hide">
        <div class="btn-group">
                <a href="#" id="hwd-upload-browse" class="btn"><?php echo JText::_('COM_HWDMS_BROWSE_FILES'); ?></a>
                <a href="#" id="hwd-upload-clear" class="btn"><?php echo JText::_('COM_HWDMS_CLEAR_LIST'); ?></a>
                <a href="#" id="hwd-upload-upload" class="btn"><?php echo JText::_('COM_HWDMS_START_UPLOAD'); ?></a>
        </div>    
        <div class="clearfix"></div>
        <div>
                <span class="overall-title"></span>
                <div class="hwd-upload-progress overall-progress">
                        <div id="overall-progress-active" style="width:0;"></div>
                </div>
        </div>
        <div class="clearfix"></div>
        <div>
                <span class="current-title"></span>
                <div class="hwd-upload-progress current-progress">
                        <div id="current-progress-active" style="width:0;"></div>
                </div>
        </div>
        <div class="clearfix"></div>        
        <div class="current-text"></div>
        <div class="clearfix"></div>
        <ul id="hwd-upload-list"></ul>
</div>
<p></p>
<div class="well well-small">
        <h3>Help and Suggestions</h3>
        <p><?php echo JText::_('COM_HWDMS_SUPPORTED_FORMATS_LABEL'); ?> <?php echo hwdMediaShareUpload::getReadableAllowedExtensions($this->localExtensions); ?></p>
        <p><?php echo JText::sprintf('COM_HWDMS_MAXIMUM_UPLOAD_SIZE_X', hwdMediaShareUpload::getMaximumUploadSize('standard')); ?></p>  
</div> 