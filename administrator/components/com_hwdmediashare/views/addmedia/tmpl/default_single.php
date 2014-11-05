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
<fieldset class="adminform">
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
<p></p>
<div class="well well-small">
        <h3><?php echo JText::_('COM_HWDMS_HELP_AND_SUGGESTIONS'); ?></h3>
        <p><?php echo JText::sprintf('COM_HWDMS_SUPPORTED_FORMATS_LIST_X', implode(', ', $this->localExtensions)); ?> <?php echo JText::sprintf('COM_HWDMS_MAXIMUM_UPLOAD_SIZE_X', hwdMediaShareUpload::getMaximumUploadSize('standard')); ?></p>  
</div> 