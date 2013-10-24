<?php
/**
 * @version    SVN $Id: default_site.php 1241 2013-03-08 14:05:05Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      17-Jan-2012 08:52:20
 */

// No direct access
defined('_JEXEC') or die;
$app =& JFactory::getApplication();
?>
<div class="width-50 fltlft">
        <fieldset class="adminform">
                <legend><?php echo JText::_('COM_HWDMS_SETTINGS'); ?></legend>
                <ul class="adminformlist">
                    <li><?php echo $this->form->getLabel('offline'); ?>
                    <?php echo $this->form->getInput('offline'); ?></li>
                    <li><?php echo $this->form->getLabel('caching'); ?>
                    <?php echo $this->form->getInput('caching'); ?></li> 
                    <li><?php echo $this->form->getLabel('internal_navigation'); ?>
                    <?php echo $this->form->getInput('internal_navigation'); ?></li> 
                    <li><?php echo $this->form->getLabel('load_joomla_css'); ?>
                    <?php echo $this->form->getInput('load_joomla_css'); ?></li> 
                    <li><?php echo $this->form->getLabel('author'); ?>
                    <?php echo $this->form->getInput('author'); ?></li> 
                    <li><?php echo $this->form->getLabel('editor'); ?>
                    <?php echo $this->form->getInput('editor'); ?></li>                    
                    <li><?php echo $this->form->getLabel('default_access'); ?>
                    <?php echo $this->form->getInput('default_access'); ?></li>                    
                    <li><?php echo $this->form->getLabel('default_download'); ?>
                    <?php echo $this->form->getInput('default_download'); ?></li>  
                    <li><?php echo $this->form->getLabel('entice_mode'); ?>
                    <?php echo $this->form->getInput('entice_mode'); ?></li> 
                    <li><label for="jform_secret"><?php echo JText::_('COM_HWDMS_SECRET_WORD_LABEL'); ?></label><input type="text" id="jform_secret" value="<?php echo $app->getCfg('secret'); ?>" class="readonly" size="40"></li>
                </ul>
        </fieldset>
        <fieldset class="adminform">
                <legend><?php echo JText::_('COM_HWDMS_FEATURES'); ?></legend>
                <ul class="adminformlist">
                    <li><?php echo $this->form->getLabel('enable_audio'); ?>
                    <?php echo $this->form->getInput('enable_audio'); ?></li>
                    <li><?php echo $this->form->getLabel('enable_documents'); ?>
                    <?php echo $this->form->getInput('enable_documents'); ?></li>
                    <li><?php echo $this->form->getLabel('enable_images'); ?>
                    <?php echo $this->form->getInput('enable_images'); ?></li>
                    <li><?php echo $this->form->getLabel('enable_videos'); ?>
                    <?php echo $this->form->getInput('enable_videos'); ?></li>
                    <li><?php echo $this->form->getLabel('enable_categories'); ?>
                    <?php echo $this->form->getInput('enable_categories'); ?></li>
                    <li><?php echo $this->form->getLabel('enable_albums'); ?>
                    <?php echo $this->form->getInput('enable_albums'); ?></li>                    
                    <li><?php echo $this->form->getLabel('enable_groups'); ?>
                    <?php echo $this->form->getInput('enable_groups'); ?></li>                    
                    <li><?php echo $this->form->getLabel('enable_user_channels'); ?>
                    <?php echo $this->form->getInput('enable_user_channels'); ?></li>                    
                    <li><?php echo $this->form->getLabel('enable_playlists'); ?>
                    <?php echo $this->form->getInput('enable_playlists'); ?></li> 
                    <li><?php echo $this->form->getLabel('enable_tags'); ?>
                    <?php echo $this->form->getInput('enable_tags'); ?></li> 
                    <li><?php echo $this->form->getLabel('enable_subscriptions'); ?>
                    <?php echo $this->form->getInput('enable_subscriptions'); ?></li>                                         
                </ul>
        </fieldset>
        <fieldset class="adminform">
                <legend><?php echo JText::_('COM_HWDMS_NOTIFICATIONS'); ?></legend>
                <ul class="adminformlist">
                    <li><?php echo $this->form->getLabel('notify_new_media'); ?>
                    <?php echo $this->form->getInput('notify_new_media'); ?></li>
                    <li><?php echo $this->form->getLabel('notify_new_albums'); ?>
                    <?php echo $this->form->getInput('notify_new_albums'); ?></li>
                    <li><?php echo $this->form->getLabel('notify_new_groups'); ?>
                    <?php echo $this->form->getInput('notify_new_groups'); ?></li>
                    <li><?php echo $this->form->getLabel('notify_new_playlists'); ?>
                    <?php echo $this->form->getInput('notify_new_playlists'); ?></li>
                    <li><?php echo $this->form->getLabel('notify_new_user_channels'); ?>
                    <?php echo $this->form->getInput('notify_new_user_channels'); ?></li>
                    <li><?php echo $this->form->getLabel('notify_new_activities'); ?>
                    <?php echo $this->form->getInput('notify_new_activities'); ?></li>                    
                </ul>
        </fieldset>
        <fieldset class="adminform">
                <legend><?php echo JText::_('COM_HWDMS_APPROVALS'); ?></legend>
                <ul class="adminformlist">
                    <li><?php echo $this->form->getLabel('approve_new_media'); ?>
                    <?php echo $this->form->getInput('approve_new_media'); ?></li>
                    <li><?php echo $this->form->getLabel('approve_new_albums'); ?>
                    <?php echo $this->form->getInput('approve_new_albums'); ?></li>
                    <li><?php echo $this->form->getLabel('approve_new_groups'); ?>
                    <?php echo $this->form->getInput('approve_new_groups'); ?></li>
                    <li><?php echo $this->form->getLabel('approve_new_playlists'); ?>
                    <?php echo $this->form->getInput('approve_new_playlists'); ?></li>
                    <li><?php echo $this->form->getLabel('approve_new_user_channels'); ?>
                    <?php echo $this->form->getInput('approve_new_user_channels'); ?></li>
                    <li><?php echo $this->form->getLabel('approve_new_activities'); ?>
                    <?php echo $this->form->getInput('approve_new_activities'); ?></li>
                </ul>
        </fieldset>  
        <fieldset class="adminform">
                <legend><?php echo JText::_('COM_HWDMS_GENERAL'); ?></legend>
                <ul class="adminformlist">
                    <li><?php echo $this->form->getLabel('channel_auto_create'); ?>
                    <?php echo $this->form->getInput('channel_auto_create'); ?></li>
                </ul>
        </fieldset>  
</div>
<div class="width-50 fltrt">
        <fieldset class="adminform">
                <legend><?php echo JText::_('COM_HWDMS_LOCAL_STORAGE'); ?></legend>
                <ul class="adminformlist">
                    <li><?php echo $this->form->getLabel('use_default_storage_location'); ?>
                    <?php echo $this->form->getInput('use_default_storage_location'); ?></li>
                    <li><?php echo $this->form->getLabel('storage_location'); ?>
                    <?php echo $this->form->getInput('storage_location'); ?></li>
                    <li><?php echo $this->form->getLabel('protect_media'); ?>
                    <?php echo $this->form->getInput('protect_media'); ?></li>
                </ul>
        </fieldset> 
        <fieldset class="adminform">
                <legend><?php echo JText::_('COM_HWDMS_REDIRECTS'); ?></legend>
                <ul class="adminformlist">
                    <li><?php echo $this->form->getLabel('offline_redirect'); ?>
                    <?php echo $this->form->getInput('offline_redirect'); ?></li>
                    <li><?php echo $this->form->getLabel('no_access_redirect'); ?>
                    <?php echo $this->form->getInput('no_access_redirect'); ?></li>
                </ul>
        </fieldset> 
        <fieldset class="adminform">
                <legend><?php echo JText::_('COM_HWDMS_SERVER_SETTINGS'); ?></legend>
                <ul class="adminformlist">
                        <li><?php echo $this->form->getLabel('path_ffmpeg'); ?>
                        <?php echo $this->form->getInput('path_ffmpeg'); ?></li>
                        <li><?php echo $this->form->getLabel('path_flvtool2'); ?>
                        <?php echo $this->form->getInput('path_flvtool2'); ?></li>                        
                        <li><?php echo $this->form->getLabel('path_mencoder'); ?>
                        <?php echo $this->form->getInput('path_mencoder'); ?></li>  
                        <li><?php echo $this->form->getLabel('path_php'); ?>
                        <?php echo $this->form->getInput('path_php'); ?></li>                           
                        <li><?php echo $this->form->getLabel('path_imagemagick'); ?>
                        <?php echo $this->form->getInput('path_imagemagick'); ?></li>     
                        <li><?php echo $this->form->getLabel('path_yamdi'); ?>
                        <?php echo $this->form->getInput('path_yamdi'); ?></li>                          
                        <li><?php echo $this->form->getLabel('path_flvmdi'); ?>
                        <?php echo $this->form->getInput('path_flvmdi'); ?></li>  
                        <li><?php echo $this->form->getLabel('path_qt_faststart'); ?>
                        <?php echo $this->form->getInput('path_qt_faststart'); ?></li> 
                </ul>
        </fieldset>
        <fieldset class="adminform">
                <legend><?php echo JText::_('COM_HWDMS_METADATA'); ?></legend>
                <ul class="adminformlist">
                        <li><?php echo $this->form->getLabel('meta_desc'); ?>
                        <?php echo $this->form->getInput('meta_desc'); ?></li>  
                        <li><?php echo $this->form->getLabel('meta_keys'); ?>
                        <?php echo $this->form->getInput('meta_keys'); ?></li>  
                        <li><?php echo $this->form->getLabel('meta_rights'); ?>
                        <?php echo $this->form->getInput('meta_rights'); ?></li>  
                        <li><?php echo $this->form->getLabel('meta_author'); ?>
                        <?php echo $this->form->getInput('meta_author'); ?></li>  
                </ul>
        </fieldset>
        <fieldset class="adminform">
                <legend><?php echo JText::_('COM_HWDMS_PAGE_MANAGEMENT'); ?></legend>
                <ul class="adminformlist">
                        <li><?php echo $this->form->getLabel('menu_bind_media'); ?>
                        <?php echo $this->form->getInput('menu_bind_media'); ?></li>  
                        <li><?php echo $this->form->getLabel('menu_bind_mediaitem1'); ?>
                        <?php echo $this->form->getInput('menu_bind_mediaitem1'); ?></li>     
                        <li><?php echo $this->form->getLabel('menu_bind_mediaitem2'); ?>
                        <?php echo $this->form->getInput('menu_bind_mediaitem2'); ?></li> 
                        <li><?php echo $this->form->getLabel('menu_bind_mediaitem3'); ?>
                        <?php echo $this->form->getInput('menu_bind_mediaitem3'); ?></li> 
                        <li><?php echo $this->form->getLabel('menu_bind_mediaitem4'); ?>
                        <?php echo $this->form->getInput('menu_bind_mediaitem4'); ?></li>  
                        <li><?php echo $this->form->getLabel('menu_bind_categories'); ?>
                        <?php echo $this->form->getInput('menu_bind_categories'); ?></li>  
                        <li><?php echo $this->form->getLabel('menu_bind_category'); ?>
                        <?php echo $this->form->getInput('menu_bind_category'); ?></li>  
                        <li><?php echo $this->form->getLabel('menu_bind_albums'); ?>
                        <?php echo $this->form->getInput('menu_bind_albums'); ?></li>  
                        <li><?php echo $this->form->getLabel('menu_bind_album'); ?>
                        <?php echo $this->form->getInput('menu_bind_album'); ?></li>  
                        <li><?php echo $this->form->getLabel('menu_bind_groups'); ?>
                        <?php echo $this->form->getInput('menu_bind_groups'); ?></li>  
                        <li><?php echo $this->form->getLabel('menu_bind_group'); ?>
                        <?php echo $this->form->getInput('menu_bind_group'); ?></li>  
                        <li><?php echo $this->form->getLabel('menu_bind_playlists'); ?>
                        <?php echo $this->form->getInput('menu_bind_playlists'); ?></li>  
                        <li><?php echo $this->form->getLabel('menu_bind_playlist'); ?>
                        <?php echo $this->form->getInput('menu_bind_playlist'); ?></li>  
                        <li><?php echo $this->form->getLabel('menu_bind_users'); ?>
                        <?php echo $this->form->getInput('menu_bind_users'); ?></li>  
                        <li><?php echo $this->form->getLabel('menu_bind_user'); ?>
                        <?php echo $this->form->getInput('menu_bind_user'); ?></li>  
                        <li><?php echo $this->form->getLabel('menu_bind_upload'); ?>
                        <?php echo $this->form->getInput('menu_bind_upload'); ?></li>  
                        <li><?php echo $this->form->getLabel('menu_bind_account'); ?>
                        <?php echo $this->form->getInput('menu_bind_account'); ?></li>  
                        <li><?php echo $this->form->getLabel('menu_bind_search'); ?>
                        <?php echo $this->form->getInput('menu_bind_search'); ?></li>    
                </ul>
        </fieldset>
</div>