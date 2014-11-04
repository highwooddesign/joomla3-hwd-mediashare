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
                        <legend><?php echo JText::_('COM_HWDMS_UPLOAD_METHODS'); ?></legend>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('enable_uploads_file'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('enable_uploads_file'); ?></div>
                        </div>  
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('enable_uploads_platform'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('enable_uploads_platform'); ?></div>
                        </div>                          
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('enable_uploads_remote'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('enable_uploads_remote'); ?></div>
                        </div>  
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('enable_uploads_embed'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('enable_uploads_embed'); ?></div>
                        </div>  
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('enable_uploads_rtmp'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('enable_uploads_rtmp'); ?></div>
                        </div>  
                </fieldset>
                <fieldset class="form-horizontal">
                        <legend><?php echo JText::_('COM_HWDMS_UPLOAD_PROCESS'); ?></legend>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('upload_workflow'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('upload_workflow'); ?></div>
                        </div>  
                </fieldset>
                <fieldset class="form-horizontal">
                        <legend><?php echo JText::_('COM_HWDMS_LARGE_FILE_UPLOAD_CONFIG'); ?></legend>
                        <p class="alert alert-info"><?php echo JText::_('COM_HWDMS_LARGE_FILE_UPLOAD_CONFIG_DESC'); ?></p>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('upload_tool_perl'); ?></div>
                                <a href="http://hwdmediashare.co.uk/learn/docs/44-configuration/107-configuring-the-large-file-uploader" target="_blank"><span class="pull-left icon-info"></span></a>
                                <div class="controls"><?php echo $this->form->getInput('upload_tool_perl'); ?></div>
                        </div> 
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('upload_uber_tmp_path'); ?></div>
                                <a href="http://hwdmediashare.co.uk/learn/docs/44-configuration/107-configuring-the-large-file-uploader" target="_blank"><span class="pull-left icon-info"></span></a>
                                <div class="controls"><?php echo $this->form->getInput('upload_uber_tmp_path'); ?></div>
                        </div> 
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('upload_uber_perl_url'); ?></div>
                                <a href="http://hwdmediashare.co.uk/learn/docs/44-configuration/107-configuring-the-large-file-uploader" target="_blank"><span class="pull-left icon-info"></span></a>
                                <div class="controls"><?php echo $this->form->getInput('upload_uber_perl_url'); ?></div>
                        </div> 
                </fieldset>
        </div>
        <div class="span6">
                <fieldset class="form-horizontal">
                        <legend><?php echo JText::_('COM_HWDMS_UPLOAD_TERMS'); ?></legend>                    
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('upload_terms'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('upload_terms'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('upload_terms_id'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('upload_terms_id'); ?></div>
                        </div>  
                </fieldset>
                <fieldset class="form-horizontal">
                        <legend><?php echo JText::_('COM_HWDMS_UPLOAD_LIMITS'); ?></legend>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('max_upload_filesize'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('max_upload_filesize'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('enable_limits'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('enable_limits'); ?></div>
                        </div>
                </fieldset>
                <fieldset class="form-vertical">
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('upload_limits'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('upload_limits'); ?></div>
                        </div>
                </fieldset>
        </div>
</div>
