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
<div class="row-fluid">
        <div class="span6">
                <fieldset class="form-horizontal">
                        <legend><?php echo JText::_('COM_HWDMS_MEDIA_DEFAULTS'); ?></legend>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('media_autoplay'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('media_autoplay'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('mediaitem_size'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('mediaitem_size'); ?></div>
                        </div>                           
                </fieldset>            
                <fieldset class="form-horizontal">
                        <legend><?php echo JText::_('COM_HWDMS_MEDIA_DELIVERY'); ?></legend>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('media_player'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('media_player'); ?></div>
                        </div>                        
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('fallback'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('fallback'); ?></div>
                        </div>
                </fieldset>
        </div>
        <div class="span6">
                <fieldset class="form-horizontal">
                        <legend><?php echo JText::_('COM_HWDMS_VIDEO_DEFAULTS'); ?></legend>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('video_quality'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('video_quality'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('video_aspect'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('video_aspect'); ?></div>
                        </div>
                </fieldset>
        </div>
</div>
