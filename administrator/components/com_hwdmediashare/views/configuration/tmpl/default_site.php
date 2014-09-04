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

// For secret word display
$app = JFactory::getApplication();
?>
<div class="row-fluid">
        <div class="span6">
                <fieldset class="form-horizontal">
                        <legend><?php echo JText::_('COM_HWDMS_SETTINGS'); ?></legend>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('offline'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('offline'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('caching'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('caching'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('internal_navigation'); ?></div>
                                <a href="http://hwdmediashare.co.uk/learn/docs/57-configuration/hwdmediashare-menu" target="_blank"><span class="pull-left icon-info"></span></a>
                                <div class="controls"><?php echo $this->form->getInput('internal_navigation'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('load_joomla_css'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('load_joomla_css'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('author'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('author'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('editor'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('editor'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('default_access'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('default_access'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('default_download'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('default_download'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('download_action'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('download_action'); ?></div>
                        </div>                        
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('entice_mode'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('entice_mode'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><label title="" class="hasTooltip" for="jform_secret" id="jform_secret-lbl" data-original-title="Secret"><?php echo JText::_('COM_HWDMS_SECRET_WORD_LABEL'); ?></label></div>
                                <div class="controls"><input type="text" aria-required="true" required="" readonly="" value="<?php echo $app->getCfg('secret'); ?>" id="jform_secret" class="validate-secret" name="jform[secret]"></div>
                        </div>
                </fieldset>
                <fieldset class="form-horizontal">
                        <legend><?php echo JText::_('COM_HWDMS_FEATURES'); ?></legend>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('enable_audio'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('enable_audio'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('enable_documents'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('enable_documents'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('enable_images'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('enable_images'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('enable_videos'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('enable_videos'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('enable_categories'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('enable_categories'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('enable_albums'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('enable_albums'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('enable_groups'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('enable_groups'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('enable_channels'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('enable_channels'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('enable_playlists'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('enable_playlists'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('enable_tags'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('enable_tags'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('enable_subscriptions'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('enable_subscriptions'); ?></div>
                        </div>
                </fieldset>
                <fieldset class="form-horizontal">
                        <legend><?php echo JText::_('COM_HWDMS_NOTIFICATIONS'); ?></legend>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('notify_new_media'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('notify_new_media'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('notify_new_albums'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('notify_new_albums'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('notify_new_groups'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('notify_new_groups'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('notify_new_playlists'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('notify_new_playlists'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('notify_new_channels'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('notify_new_channels'); ?></div>
                        </div>
                </fieldset>
                <fieldset class="form-horizontal">
                        <legend><?php echo JText::_('COM_HWDMS_APPROVALS'); ?></legend>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('approve_new_media'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('approve_new_media'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('approve_new_albums'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('approve_new_albums'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('approve_new_groups'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('approve_new_groups'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('approve_new_playlists'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('approve_new_playlists'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('approve_new_channels'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('approve_new_channels'); ?></div>
                        </div>
                </fieldset>
                <fieldset class="form-horizontal">
                        <legend><?php echo JText::_('COM_HWDMS_GENERAL'); ?></legend>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('channel_auto_create'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('channel_auto_create'); ?></div>
                        </div>
                </fieldset>
        </div>
        <div class="span6">
                <fieldset class="form-horizontal">
                        <legend><?php echo JText::_('COM_HWDMS_LOCAL_STORAGE'); ?></legend>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('use_default_storage_location'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('use_default_storage_location'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('storage_location'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('storage_location'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('protect_media'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('protect_media'); ?></div>
                        </div>
                </fieldset>
                <fieldset class="form-horizontal">
                        <legend><?php echo JText::_('COM_HWDMS_REDIRECTS'); ?></legend>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('offline_redirect'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('offline_redirect'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('no_access_redirect'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('no_access_redirect'); ?></div>
                        </div>
                </fieldset>
                <fieldset class="form-horizontal">
                        <legend><?php echo JText::_('COM_HWDMS_SERVER_SETTINGS'); ?></legend>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('path_ffmpeg'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('path_ffmpeg'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('path_flvtool2'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('path_flvtool2'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('path_mencoder'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('path_mencoder'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('path_php'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('path_php'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('path_imagemagick'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('path_imagemagick'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('path_yamdi'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('path_yamdi'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('path_flvmdi'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('path_flvmdi'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('path_qt_faststart'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('path_qt_faststart'); ?></div>
                        </div>
                </fieldset>
                <fieldset class="form-horizontal">
                        <legend><?php echo JText::_('COM_HWDMS_METADATA'); ?></legend>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('meta_desc'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('meta_desc'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('meta_keys'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('meta_keys'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('meta_rights'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('meta_rights'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('meta_author'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('meta_author'); ?></div>
                        </div>
                </fieldset>
                <fieldset class="form-horizontal">
                        <legend><?php echo JText::_('COM_HWDMS_PAGE_MANAGEMENT'); ?></legend>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('menu_bind_media'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('menu_bind_media'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('menu_bind_mediaitem1'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('menu_bind_mediaitem1'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('menu_bind_mediaitem2'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('menu_bind_mediaitem2'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('menu_bind_mediaitem3'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('menu_bind_mediaitem3'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('menu_bind_mediaitem4'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('menu_bind_mediaitem4'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('menu_bind_categories'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('menu_bind_categories'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('menu_bind_category'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('menu_bind_category'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('menu_bind_albums'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('menu_bind_albums'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('menu_bind_album'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('menu_bind_album'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('menu_bind_groups'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('menu_bind_groups'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('menu_bind_group'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('menu_bind_group'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('menu_bind_playlists'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('menu_bind_playlists'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('menu_bind_playlist'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('menu_bind_playlist'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('menu_bind_channels'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('menu_bind_channels'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('menu_bind_channel'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('menu_bind_channel'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('menu_bind_upload'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('menu_bind_upload'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('menu_bind_account'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('menu_bind_account'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('menu_bind_search'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('menu_bind_search'); ?></div>
                        </div>
                </fieldset>
        </div>
</div>
