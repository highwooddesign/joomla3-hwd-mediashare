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

JHtml::_('behavior.tooltip');
JHtml::_('behavior.framework');
JHtml::_('formbehavior.chosen', 'select');
?>
<form action="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=addmedia.upload'); ?>" method="post" target="_parent" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">
        <div class="row-fluid">
                <div class="span9">
                        <?php echo JHtml::_('bootstrap.startAccordion', 'hwdmediashare-slider-upload', array('active' => 'local')); ?>
                        <?php if ($this->config->get('enable_uploads_file') == 1) : ?>            
                                <?php if ($this->config->get('upload_tool') == 0 || $this->config->get('upload_tool') == 1 && count($this->localExtensions)) : ?>
                                        <?php echo JHtml::_('bootstrap.addSlide', 'hwdmediashare-slider-upload', hwdMediaShareUpload::getReadableAllowedMediaTypes('local'), 'local'); ?>
                                                <p><?php echo JText::_('COM_HWDMS_SUPPORTED_FORMATS_LABEL'); ?> <?php echo hwdMediaShareUpload::getReadableAllowedExtensions($this->localExtensions); ?></p>
                                                <p><?php echo JText::sprintf('COM_HWDMS_MAXIMUM_UPLOAD_SIZE_X', hwdMediaShareUpload::getMaximumUploadSize('standard')); ?></p>
                                                <fieldset class="adminform" id="hwd-upload-fallback">
                                                        <div class="control-group">
                                                                <div class="control-label hide">
                                                                        <?php echo $this->form->getLabel('Filedata'); ?>
                                                                </div>
                                                                <div class="controls">
                                                                        <?php echo $this->form->getInput('Filedata'); ?>
                                                                </div>
                                                        </div>
                                                        <input type="hidden" name="fallback" value="true" />
                                                        <div class="btn-group">
                                                                <button type="button" class="btn btn-info" onclick="Joomla.submitbutton('addmedia.upload')">
                                                                        <?php echo JText::_('COM_HWDMS_UPLOAD') ?>
                                                                </button>
                                                        </div>                              
                                                </fieldset>
                                                <?php if ($this->config->get('upload_tool') == 1 && !$this->replace) : ?>
                                                <fieldset class="adminform">
                                                <div id="hwd-upload-status" class="hide">
                                                        <p>
                                                                <a href="#" id="hwd-upload-browse" class="btn btn-info"><?php echo JText::_('COM_HWDMS_BROWSE_FILES'); ?></a>&nbsp;
                                                                <a href="#" id="hwd-upload-clear" class="btn btn-danger"><?php echo JText::_('COM_HWDMS_CLEAR_LIST'); ?></a>&nbsp;
                                                                <a href="#" id="hwd-upload-upload" class="btn btn-success"><?php echo JText::_('COM_HWDMS_START_UPLOAD'); ?></a>
                                                        </p>
                                                        <div>
                                                                <span class="overall-title"></span>
                                                                <img src="<?php echo JURI::root(true); ?>/media/com_hwdmediashare/assets/images/ajaxupload/progress-bar/bar.gif" class="progress overall-progress" />
                                                        </div>
                                                        <div class="clearfix"></div>
                                                        <div>
                                                                <span class="current-title"></span>
                                                                <img src="<?php echo JURI::root(true); ?>/media/com_hwdmediashare/assets/images/ajaxupload/progress-bar/bar.gif" class="progress current-progress" />
                                                        </div>
                                                        <div class="clearfix"></div>
                                                        <div class="current-text"></div>
                                                </div>
                                                <ul id="hwd-upload-list"></ul>
                                                </fieldset>
                                                <?php endif; ?>
                                        <?php echo JHtml::_('bootstrap.endSlide'); ?>
                                <?php elseif ($this->config->get('upload_tool') == 2 && $this->config->get('upload_tool_perl') == 1 && count($this->localExtensions)): ?>
                                        <?php echo JHtml::_('bootstrap.addSlide', 'hwdmediashare-slider-upload', hwdMediaShareUpload::getReadableAllowedMediaTypes('local'), 'local'); ?>
                                                <?php echo $this->uberUploadHtml; ?>
                                        <?php echo JHtml::_('bootstrap.endSlide'); ?>
                                <?php endif; ?>
                                <?php if ($this->showPlatform) : ?>
                                        <?php echo JHtml::_('bootstrap.addSlide', 'hwdmediashare-slider-upload', hwdMediaShareUpload::getReadableAllowedMediaTypes('platform'), 'platform'); ?>
                                                <fieldset class="adminform">
                                                        <?php echo $this->getPlatformUploadForm(); ?>
                                                </fieldset>
                                        <?php echo JHtml::_('bootstrap.endSlide'); ?>
                                <?php endif; ?>
                        <?php endif; ?>
                        <?php if ($this->config->get('enable_uploads_remote') == 1) : ?>
                                <?php echo JHtml::_('bootstrap.addSlide', 'hwdmediashare-slider-upload', JText::_('COM_HWDMS_ADD_REMOTE_MEDIA'), 'remote'); ?>
                                        <p><?php echo JText::_('COM_HWDMS_ADD_REMOTE_MEDIA_DESC'); ?></p>
                                        <p><?php echo hwdMediaShareRemote::getReadableAllowedRemotes(); ?></p>
                                        <?php if ($this->config->get('upload_workflow') == 1) : ?>
                                                <p class="alert alert-info"><?php echo JText::_('COM_HWDMS_ADD_MULTIPLE_REMOTE_MEDIA_DESC'); ?></p>
                                        <?php endif; ?>                                                
                                        <?php if ($this->config->get('upload_workflow') == 0 || $this->replace) : ?>
                                                <div class="control-group">
                                                        <div class="control-label hide">
                                                                <?php echo $this->form->getLabel('remote'); ?>
                                                        </div>
                                                        <div class="controls">
                                                                <?php echo $this->form->getInput('remote'); ?>
                                                        </div>
                                                </div>
                                        <?php else: ?>  
                                                <div class="control-group">
                                                        <div class="control-label hide">
                                                                <?php echo $this->form->getLabel('remotes'); ?>
                                                        </div>
                                                        <div class="controls">
                                                                <?php echo $this->form->getInput('remotes'); ?>
                                                        </div>
                                                </div>
                                        <?php endif; ?>
                                        <div class="btn-group">
                                                <button type="button" class="btn btn-info" onclick="Joomla.submitbutton('addmedia.remote')">
                                                        <?php echo JText::_('COM_HWDMS_ADD') ?>
                                                </button>
                                        </div>                        
                                <?php echo JHtml::_('bootstrap.endSlide'); ?>
                                <?php echo JHtml::_('bootstrap.addSlide', 'hwdmediashare-slider-upload', JText::_('COM_HWDMS_ADD_REMOTE_FILE'), 'link'); ?>
                                        <?php foreach($this->form->getFieldset('link') as $field): ?>
                                        <div class="control-group">
                                                <div class="control-label">
                                                        <?php echo $field->label; ?>
                                                </div>
                                                <div class="controls">
                                                        <?php echo $field->input; ?>
                                                </div>
                                        </div>
                                        <?php endforeach; ?>
                                        <div class="btn-group">
                                                <button type="button" class="btn btn-info" onclick="Joomla.submitbutton('addmedia.link')">
                                                        <?php echo JText::_('COM_HWDMS_ADD') ?>
                                                </button>
                                        </div> 
                                <?php echo JHtml::_('bootstrap.endSlide'); ?>
                        <?php endif; ?>
                        <?php if ($this->config->get('enable_uploads_embed') == 1) : ?>
                                <?php echo JHtml::_('bootstrap.addSlide', 'hwdmediashare-slider-upload', JText::_('COM_HWDMS_ADD_EMBED_CODE'), 'embed'); ?>
                                        <?php foreach($this->form->getFieldset('embed') as $field): ?>
                                        <div class="control-group">
                                                <div class="control-label">
                                                        <?php echo $field->label; ?>
                                                </div>
                                                <div class="controls">
                                                        <?php echo $field->input; ?>
                                                </div>
                                        </div>
                                        <?php endforeach; ?>
                                        <div class="btn-group">
                                                <button type="button" class="btn btn-info" onclick="Joomla.submitbutton('addmedia.embed')">
                                                        <?php echo JText::_('COM_HWDMS_ADD') ?>
                                                </button>
                                        </div> 
                                <?php echo JHtml::_('bootstrap.endSlide'); ?>
                        <?php endif; ?>
                        <?php if ($this->config->get('enable_uploads_rtmp') == 1) : ?>
                                <?php echo JHtml::_('bootstrap.addSlide', 'hwdmediashare-slider-upload', JText::_('COM_HWDMS_ADD_RTMP'), 'rtmp'); ?>
                                        <?php foreach($this->form->getFieldset('rtmp') as $field): ?>
                                        <div class="control-group">
                                                <div class="control-label">
                                                        <?php echo $field->label; ?>
                                                </div>
                                                <div class="controls">
                                                        <?php echo $field->input; ?>
                                                </div>
                                        </div>
                                        <?php endforeach; ?>
                                        <div class="btn-group">
                                                <button type="button" class="btn btn-info" onclick="Joomla.submitbutton('addmedia.rtmp')">
                                                        <?php echo JText::_('COM_HWDMS_ADD') ?>
                                                </button>
                                        </div> 
                                <?php echo JHtml::_('bootstrap.endSlide'); ?>
                        <?php endif; ?>
                        <?php if (!$this->replace) : ?>
                        <?php echo JHtml::_('bootstrap.addSlide', 'hwdmediashare-slider-upload', JText::_('COM_HWDMS_ADD_FROM_SERVER'), 'server'); ?>
                                <p class="alert"><?php echo JText::_('COM_HWDMS_ADD_FROM_SERVER_ALERT_DESC'); ?></p>      
                                <table width="100%">
                                        <tr valign="top">
                                                <td>
                                                        <fieldset id="treeview">
                                                                <div id="media-tree_tree"></div>
                                                                <?php echo $this->loadTemplate('folders'); ?>
                                                        </fieldset>
                                                </td>
                                                <td>
                                                        <fieldset id="folderview">
                                                                <div class="view">
                                                                        <iframe src="index.php?option=com_hwdmediashare&amp;task=addmedia.scan&amp;tmpl=component&amp;folder=<?php echo $this->state->folder;?>" id="folderframe" name="folderframe" width="90%" height="300" marginwidth="0" marginheight="0" scrolling="no"></iframe>
                                                                </div>
                                                                <div class="path">
                                                                        <input class="inputbox" type="hidden" id="folderpath" readonly="readonly" />
                                                                        <input class="inputbox" type="hidden" id="foldername" name="foldername"  />
                                                                </div>
                                                        </fieldset>
                                                </td>
                                        </tr>
                                </table>
                        <?php echo JHtml::_('bootstrap.endSlide'); ?>
                        <?php endif; ?>
                        <?php echo JHtml::_('bootstrap.endAccordion'); ?>
                </div>
                <div class="span3">
                        <?php if ($this->jformreg->get('title')): ?>
                                <h2><?php echo $this->jformreg->get('title'); ?></h2>
                        <?php endif; ?>
                        <?php if ($this->jformreg->get('alias')): ?>
                                <p class="small"><?php echo $this->jformreg->get('alias'); ?></p>
                        <?php endif; ?>                        
                        <?php if ($this->jformreg->get('description')): ?>
                                <p><?php echo JHtmlString::truncate($this->jformreg->get('description'), 160, false, false); ?></p>
                        <?php endif; ?>  
                        <?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>  
                </div>
        </div>
        <input type="hidden" name="task" value="" />
        <?php if ($this->replace) : ?><input type="hidden" name="jform[id]" value="<?php echo $this->replace; ?>" /><?php endif; ?>
        <?php if ($this->replace) : ?><input type="hidden" name="jform_id" value="<?php echo $this->replace; ?>" /><?php endif; ?>
        <?php foreach($this->jformdata as $name => $value): ?>
                <?php if (in_array($name, array("catid", "tags", "published", "featured", "access", "language"))) continue; // We remove any inputs which have been included with the joomla.edit.global layout file ?>
                <?php if (is_array($value)) : ?>
                        <?php foreach($value as $key => $id): ?>
                                <?php if (!empty($id)) : ?><input type="hidden" name="jform[<?php echo $name; ?>][]" value="<?php echo $id; ?>" /><?php endif; ?>
                        <?php endforeach; ?>
                <?php elseif(!empty($value)): ?>
                        <input type="hidden" name="jform[<?php echo $name; ?>]" value="<?php echo $value; ?>" />
                <?php endif; ?>
        <?php endforeach; ?> 
        <?php // The token breaks the XML redirect file for uber, so it is removed by the uber javascript ?>
        <input type="hidden" name="<?php echo JSession::getFormToken(); ?>" id="formtoken" value="1" />
</form>
