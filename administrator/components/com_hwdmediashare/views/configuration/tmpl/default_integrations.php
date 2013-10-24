<?php
/**
 * @version    SVN $Id: default_integrations.php 1240 2013-03-08 14:04:33Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      10-Jan-2012 09:15:07
 */

// No direct access
defined('_JEXEC') or die;

?>
<div class="width-50 fltlft">
    <div class="width-100">
        <fieldset class="adminform">
                <legend><?php echo JText::_('COM_HWDMS_COMMUNITY'); ?></legend>
                <ul class="adminformlist">
                    <li><?php echo $this->form->getLabel('community_avatar'); ?>
                    <?php echo $this->form->getInput('community_avatar'); ?></li>   
                    <li><?php echo $this->form->getLabel('community_link'); ?>
                    <?php echo $this->form->getInput('community_link'); ?></li> 
                    <li><?php echo $this->form->getLabel('commenting'); ?>
                    <?php echo $this->form->getInput('commenting'); ?></li>
                </ul>
        </fieldset>
        <fieldset class="adminform">
                <legend><?php echo JText::_('COM_HWDMS_MEDIA_HOSTING_PLATFORM'); ?></legend>
                <ul class="adminformlist">
                    <li><?php echo $this->form->getLabel('platform'); ?>
                    <?php echo $this->form->getInput('platform'); ?></li>                      
                </ul>
        </fieldset>
        <fieldset class="adminform">
                <legend><?php echo JText::_('COM_HWDMS_MEDIA_CDN'); ?></legend>
                <ul class="adminformlist">
                    <li><?php echo $this->form->getLabel('cdn'); ?>
                    <?php echo $this->form->getInput('cdn'); ?></li>                      
                </ul>
        </fieldset>        
    </div>
</div>
<div class="width-50 fltrt">
    <div class="width-100">
        <fieldset class="adminform">
                <legend><?php echo JText::_('COM_HWDMS_GOOGLE_MAPS_API_V3'); ?></legend>
                <ul class="adminformlist">
                    <li><?php echo $this->form->getLabel('google_maps_api_v3_key'); ?>
                    <?php echo $this->form->getInput('google_maps_api_v3_key'); ?></li>                      
                </ul>
        </fieldset>
        <fieldset class="adminform">
                <legend><?php echo JText::_('COM_HWDMS_FACEBOOK_DEVELOPERS'); ?></legend>
                <ul class="adminformlist">
                    <li><?php echo $this->form->getLabel('facebook_appid'); ?>
                    <?php echo $this->form->getInput('facebook_appid'); ?></li>                      
                </ul>
        </fieldset>
        <fieldset class="adminform">
                <legend><?php echo JText::_('COM_HWDMS_RECAPTCHA'); ?></legend>
                <ul class="adminformlist">
                    <li><?php echo $this->form->getLabel('recaptcha_public_key'); ?>
                    <?php echo $this->form->getInput('recaptcha_public_key'); ?></li>         
                    <li><?php echo $this->form->getLabel('recaptcha_private_key'); ?>
                    <?php echo $this->form->getInput('recaptcha_private_key'); ?></li>   
                </ul>
        </fieldset>        
     </div>
</div>