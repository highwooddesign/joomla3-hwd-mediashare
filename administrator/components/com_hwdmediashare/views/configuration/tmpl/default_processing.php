<?php
/**
 * @version    SVN $Id: default_processing.php 425 2012-06-28 07:48:57Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      17-Jan-2012 09:14:55
 */

// No direct access
defined('_JEXEC') or die;
JHtml::_('behavior.modal');

?>
<div class="width-50 fltlft">
        <fieldset class="adminform">
                <legend><?php echo JText::_('COM_HWDMS_PROCESSING'); ?></legend>
                <p><a href="index.php?option=com_hwdmediashare&task=configuration.background&tmpl=component" class="modal" rel="{handler: 'iframe', size: {x: 500, y: 250}}"><?php echo JText::_('COM_HWDMS_WILL_AUTO_PROCESS_WORK'); ?></a></p>
                <ul class="adminformlist">
                    <li><?php echo $this->form->getLabel('process'); ?>
                    <?php echo $this->form->getInput('process'); ?></li>
                    <li><?php echo $this->form->getLabel('process_auto'); ?>
                    <?php echo $this->form->getInput('process_auto'); ?></li>
                    <li><?php echo $this->form->getLabel('process_watermark'); ?>
                    <?php echo $this->form->getInput('process_watermark'); ?></li>  
                    <li><?php echo $this->form->getLabel('watermark_path'); ?>
                    <?php echo $this->form->getInput('watermark_path'); ?></li>  
                    <li><?php echo $this->form->getLabel('watermark_position'); ?>
                    <?php echo $this->form->getInput('watermark_position'); ?></li> 
                </ul>
        </fieldset>
        <fieldset class="adminform">
                <legend><?php echo JText::_('COM_HWDMS_IMAGES'); ?></legend>
                <ul class="adminformlist">
                    <li><?php echo $this->form->getLabel('process_image_quality'); ?>
                    <?php echo $this->form->getInput('process_image_quality'); ?></li>
                    <li><?php echo $this->form->getLabel('process_jpeg_75'); ?>
                    <?php echo $this->form->getInput('process_jpeg_75'); ?></li>
                    <li><?php echo $this->form->getLabel('process_jpeg_100'); ?>
                    <?php echo $this->form->getInput('process_jpeg_100'); ?></li>
                    <li><?php echo $this->form->getLabel('process_jpeg_240'); ?>
                    <?php echo $this->form->getInput('process_jpeg_240'); ?></li>            
                    <li><?php echo $this->form->getLabel('process_jpeg_500'); ?>
                    <?php echo $this->form->getInput('process_jpeg_500'); ?></li>  
                    <li><?php echo $this->form->getLabel('process_jpeg_640'); ?>
                    <?php echo $this->form->getInput('process_jpeg_640'); ?></li>                      
                    <li><?php echo $this->form->getLabel('process_jpeg_1024'); ?>
                    <?php echo $this->form->getInput('process_jpeg_1024'); ?></li>  
                    <li><?php echo $this->form->getLabel('optimise_jpeg'); ?>
                    <?php echo $this->form->getInput('optimise_jpeg'); ?></li>  
                </ul>
        </fieldset>
        <fieldset class="adminform">
                <legend><?php echo JText::_('COM_HWDMS_AUDIO'); ?></legend>
                <ul class="adminformlist">
                    <li><?php echo $this->form->getLabel('process_audio_mp3'); ?>
                    <?php echo $this->form->getInput('process_audio_mp3'); ?></li> 
                    <li><?php echo $this->form->getLabel('process_audio_ogg'); ?>
                    <?php echo $this->form->getInput('process_audio_ogg'); ?></li> 
                </ul>
        </fieldset>
</div>
<div class="width-50 fltrt">
        <fieldset class="adminform">
                <legend><?php echo JText::_('COM_HWDMS_VIDEO_SETTINGS'); ?></legend>
                <ul class="adminformlist">
                    <li><?php echo $this->form->getLabel('process_vpreset'); ?>
                    <?php echo $this->form->getInput('process_vpreset'); ?></li> 
                    <li><?php echo $this->form->getLabel('metadata_injector'); ?>
                    <?php echo $this->form->getInput('metadata_injector'); ?></li> 
                    <li><?php echo $this->form->getLabel('process_max_vbitrate_240'); ?>
                    <?php echo $this->form->getInput('process_max_vbitrate_240'); ?></li>      
                    <li><?php echo $this->form->getLabel('process_max_abitrate_240'); ?>
                    <?php echo $this->form->getInput('process_max_abitrate_240'); ?></li> 
                    <li><?php echo $this->form->getLabel('process_max_vbitrate_360'); ?>
                    <?php echo $this->form->getInput('process_max_vbitrate_360'); ?></li>   
                    <li><?php echo $this->form->getLabel('process_max_abitrate_360'); ?>
                    <?php echo $this->form->getInput('process_max_abitrate_360'); ?></li> 
                    <li><?php echo $this->form->getLabel('process_max_vbitrate_480'); ?>
                    <?php echo $this->form->getInput('process_max_vbitrate_480'); ?></li>    
                    <li><?php echo $this->form->getLabel('process_max_abitrate_480'); ?>
                    <?php echo $this->form->getInput('process_max_abitrate_480'); ?></li>
                    <li><?php echo $this->form->getLabel('process_max_vbitrate_720'); ?>
                    <?php echo $this->form->getInput('process_max_vbitrate_720'); ?></li>
                    <li><?php echo $this->form->getLabel('process_max_abitrate_720'); ?>
                    <?php echo $this->form->getInput('process_max_abitrate_720'); ?></li>
                    <li><?php echo $this->form->getLabel('process_max_vbitrate_1080'); ?>
                    <?php echo $this->form->getInput('process_max_vbitrate_1080'); ?></li>
                    <li><?php echo $this->form->getLabel('process_max_abitrate_1080'); ?>
                    <?php echo $this->form->getInput('process_max_abitrate_1080'); ?></li>
                </ul>
        </fieldset>
        <fieldset class="adminform">
                <legend><?php echo JText::_('COM_HWDMS_VIDEO'); ?></legend>
                <ul class="adminformlist"> 
                    <li><?php echo $this->form->getLabel('process_flv_240'); ?>
                    <?php echo $this->form->getInput('process_flv_240'); ?></li>
                    <li><?php echo $this->form->getLabel('process_flv_360'); ?>
                    <?php echo $this->form->getInput('process_flv_360'); ?></li>                   
                    <li><?php echo $this->form->getLabel('process_flv_480'); ?>
                    <?php echo $this->form->getInput('process_flv_480'); ?></li>                      
                                        
                    <li><?php echo $this->form->getLabel('process_mp4_360'); ?>
                    <?php echo $this->form->getInput('process_mp4_360'); ?></li>                   
                    <li><?php echo $this->form->getLabel('process_mp4_480'); ?>
                    <?php echo $this->form->getInput('process_mp4_480'); ?></li>                      
                    <li><?php echo $this->form->getLabel('process_mp4_720'); ?>
                    <?php echo $this->form->getInput('process_mp4_720'); ?></li>                      
                    <li><?php echo $this->form->getLabel('process_mp4_1080'); ?>
                    <?php echo $this->form->getInput('process_mp4_1080'); ?></li>
                                        
                    <li><?php echo $this->form->getLabel('process_webm_360'); ?>
                    <?php echo $this->form->getInput('process_webm_360'); ?></li>                   
                    <li><?php echo $this->form->getLabel('process_webm_480'); ?>
                    <?php echo $this->form->getInput('process_webm_480'); ?></li>                      
                    <li><?php echo $this->form->getLabel('process_webm_720'); ?>
                    <?php echo $this->form->getInput('process_webm_720'); ?></li>                      
                    <li><?php echo $this->form->getLabel('process_webm_1080'); ?>
                    <?php echo $this->form->getInput('process_webm_1080'); ?></li>
                    
                    <li><?php echo $this->form->getLabel('process_ogg_360'); ?>
                    <?php echo $this->form->getInput('process_ogg_360'); ?></li>                   
                    <li><?php echo $this->form->getLabel('process_ogg_480'); ?>
                    <?php echo $this->form->getInput('process_ogg_480'); ?></li>                      
                    <li><?php echo $this->form->getLabel('process_ogg_720'); ?>
                    <?php echo $this->form->getInput('process_ogg_720'); ?></li>                      
                    <li><?php echo $this->form->getLabel('process_ogg_1080'); ?>
                    <?php echo $this->form->getInput('process_ogg_1080'); ?></li>                   
                </ul>
        </fieldset>
</div>