<?php
/**
 * @version    SVN $Id: default_uploads.php 425 2012-06-28 07:48:57Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Jan-2012 10:00:26
 */

// No direct access
defined('_JEXEC') or die;

?>
<div class="width-50 fltlft">
    <div class="width-100">
        <fieldset class="adminform">
                <legend><?php echo JText::_('COM_HWDMS_UPLOAD_TERMS'); ?></legend>
                <ul class="adminformlist">
                    <li><?php echo $this->form->getLabel('upload_terms'); ?>
                    <?php echo $this->form->getInput('upload_terms'); ?></li>
                    <li><?php echo $this->form->getLabel('upload_terms_id'); ?>
                    <?php echo $this->form->getInput('upload_terms_id'); ?></li>
                </ul>
        </fieldset>
        <fieldset class="adminform">
                <legend><?php echo JText::_('COM_HWDMS_UPLOAD_METHODS'); ?></legend>
                <ul class="adminformlist">   
                    <li><?php echo $this->form->getLabel('enable_uploads_file'); ?>
                    <?php echo $this->form->getInput('enable_uploads_file'); ?></li>
                    <li><?php echo $this->form->getLabel('enable_uploads_remote'); ?>
                    <?php echo $this->form->getInput('enable_uploads_remote'); ?></li>
                    <li><?php echo $this->form->getLabel('enable_uploads_embed'); ?>
                    <?php echo $this->form->getInput('enable_uploads_embed'); ?></li>
                    <li><?php echo $this->form->getLabel('enable_uploads_rtmp'); ?>
                    <?php echo $this->form->getInput('enable_uploads_rtmp'); ?></li>
                </ul>
        </fieldset>        
        <fieldset class="adminform">
                <legend><?php echo JText::_('COM_HWDMS_STANDARD_FILE_UPLOAD_CONFIG'); ?></legend>
                <ul class="adminformlist">   
                    <li><?php echo $this->form->getLabel('upload_tool_fancy'); ?>
                    <?php echo $this->form->getInput('upload_tool_fancy'); ?></li>
                </ul>
        </fieldset>        
        <fieldset class="adminform">
                <legend><?php echo JText::_('COM_HWDMS_LARGE_FILE_UPLOAD_CONFIG'); ?></legend>
                <ul class="adminformlist">
                    <li><?php echo $this->form->getLabel('upload_tool_perl'); ?>
                    <?php echo $this->form->getInput('upload_tool_perl'); ?></li>
                    <li><?php echo $this->form->getLabel('upload_uber_tmp_path'); ?>
                    <?php echo $this->form->getInput('upload_uber_tmp_path'); ?></li>
                    <li><?php echo $this->form->getLabel('upload_uber_perl_url'); ?>
                    <?php echo $this->form->getInput('upload_uber_perl_url'); ?></li>                    
                </ul>
        </fieldset>
     </div>
</div>
<div class="width-50 fltrt">
        <fieldset class="adminform">
                <legend><?php echo JText::_('COM_HWDMS_FILE_UPLOADERS'); ?></legend>
                <ul class="adminformlist">   
                    <li><?php echo $this->form->getLabel('audio_uploads'); ?>
                    <?php echo $this->form->getInput('audio_uploads'); ?></li>
                    <li><?php echo $this->form->getLabel('document_uploads'); ?>
                    <?php echo $this->form->getInput('document_uploads'); ?></li>
                    <li><?php echo $this->form->getLabel('image_uploads'); ?>
                    <?php echo $this->form->getInput('image_uploads'); ?></li>
                    <li><?php echo $this->form->getLabel('video_uploads'); ?>
                    <?php echo $this->form->getInput('video_uploads'); ?></li>
                </ul>
        </fieldset> 
        <fieldset class="adminform">
                <legend><?php echo JText::_('COM_HWDMS_UPLOAD_LIMITS'); ?></legend>
                <ul class="adminformlist">
                    <li><?php echo $this->form->getLabel('max_upload_filesize'); ?>
                    <?php echo $this->form->getInput('max_upload_filesize'); ?></li>
                    <li><?php echo $this->form->getLabel('enable_limits'); ?>
                    <?php echo $this->form->getInput('enable_limits'); ?></li>
                    <div class="clr"></div>
                    <?php echo $this->form->getLabel('upload_limits'); ?>
                    <div class="clr"> </div>
                    <?php echo $this->form->getInput('upload_limits'); ?>
                </ul>
        </fieldset>
</div>