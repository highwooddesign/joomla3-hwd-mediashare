<?php
/**
 * @version    SVN $Id: default_media.php 955 2013-01-30 08:59:19Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access
defined('_JEXEC') or die;
?>
<div class="width-50 fltlft">
        <fieldset class="adminform">
                <legend><?php echo JText::_('COM_HWDMS_MEDIA_DELIVERY'); ?></legend>
                <ul class="adminformlist">
                    <li><?php echo $this->form->getLabel('fallback'); ?>
                    <?php echo $this->form->getInput('fallback'); ?></li>
                    <li><?php echo $this->form->getLabel('media_player'); ?>
                    <?php echo $this->form->getInput('media_player'); ?></li>

                </ul>
        </fieldset>    
</div>
<div class="width-50 fltrt">
        <fieldset class="adminform">
                <legend><?php echo JText::_('COM_HWDMS_VIDEO_DEFAULTS'); ?></legend>
                <ul class="adminformlist">
                    <li><?php echo $this->form->getLabel('media_autoplay'); ?>
                    <?php echo $this->form->getInput('media_autoplay'); ?></li>
                    <li><?php echo $this->form->getLabel('video_quality'); ?>
                    <?php echo $this->form->getInput('video_quality'); ?></li>  
                    <li><?php echo $this->form->getLabel('video_aspect'); ?>
                    <?php echo $this->form->getInput('video_aspect'); ?></li> 
                </ul>
        </fieldset>
</div>