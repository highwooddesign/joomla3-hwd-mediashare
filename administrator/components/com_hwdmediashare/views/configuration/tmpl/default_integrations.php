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
                        <legend><?php echo JText::_('COM_HWDMS_COMMUNITY'); ?></legend>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('community_avatar'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('community_avatar'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('community_link'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('community_link'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('commenting'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('commenting'); ?></div>
                        </div>                        
                </fieldset>
                <fieldset class="form-horizontal">
                        <legend><?php echo JText::_('COM_HWDMS_MEDIA_HOSTING_PLATFORM'); ?></legend>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('platform'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('platform'); ?></div>
                        </div>                      
                </fieldset>
                <fieldset class="form-horizontal">
                        <legend><?php echo JText::_('COM_HWDMS_MEDIA_CDN'); ?></legend>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('cdn'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('cdn'); ?></div>
                        </div>                       
                </fieldset>
        </div>
        <div class="span6">
                <fieldset class="form-horizontal">
                        <legend><?php echo JText::_('COM_HWDMS_GOOGLE_MAPS_API_V3'); ?></legend>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('google_maps_api_v3_key'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('google_maps_api_v3_key'); ?></div>
                        </div>
                </fieldset>
                <fieldset class="form-horizontal">
                        <legend><?php echo JText::_('COM_HWDMS_FACEBOOK_DEVELOPERS'); ?></legend>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('facebook_appid'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('facebook_appid'); ?></div>
                        </div>
                </fieldset>
                <fieldset class="form-horizontal">
                        <legend><?php echo JText::_('COM_HWDMS_TWITTER'); ?></legend>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('twitter_username'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('twitter_username'); ?></div>
                        </div>
                </fieldset>
        </div>
</div>
