<?php
/**
 * @version    SVN $Id: default.php 1367 2013-04-23 12:14:23Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Load tooltip behavior
JHtml::_('behavior.tooltip');
JHtml::_('behavior.framework');

$user = & JFactory::getUser();
$maxUpload = (int)$this->config->get('max_upload_filesize');
$maxPhpUpload = min((int)ini_get('post_max_size'),(int)ini_get('upload_max_filesize'),(int)$maxUpload);
?>
<form action="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=addmedia.upload'); ?>" method="post" target="_parent" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">
        <?php echo JHtml::_('sliders.start', 'hwdmediashare-slider-upload'); ?>
        <?php if ($this->config->get('enable_uploads_file') == 1) : ?>
                <?php if (is_array($this->standardExtensions) && count($this->standardExtensions) > 0) : ?>
                <?php echo JHtml::_('sliders.panel', JText::sprintf( 'COM_HWDMS_UPLOAD_TYPES_LESS_THAN_N_MB', $this->getReadableAllowedMediaTypes('standard'), $maxPhpUpload ), 'publishing');?>
                        <fieldset class="adminform">
                                <div class="formelm">
                                        <label><?php echo JText::_('COM_HWDMS_SUPPORTED_FORMATS_LABEL'); ?></label>
                                        <span class="faux-label" style="clear:none;"><?php echo $this->getReadableAllowedExtensions($this->standardExtensions); ?></span>
                                </div>
                        </fieldset>
                        <fieldset class="adminform" id="hwd-upload-fallback">
                                <ul class="panelform">
                                        <li>
                                                <label for="hwd-upload-photoupload">
                                                        <?php echo JText::_('COM_HWDMS_UPLOAD_A_FILE') ?>
                                                </label>
                                                <input type="file" name="Filedata" />
                                                <input type="hidden" name="fallback" value="true11" />
                                        </li>
                                        <li>
                                                <label></label>
                                                <button type="button" onclick="Joomla.submitbutton('addmedia.upload')">
                                                <?php echo JText::_('COM_HWDMS_UPLOAD') ?>
                                                </button>
                                        </li>
                                </ul>
                        </fieldset>
                        <?php if ($this->config->get('upload_tool_fancy') == 1 && !$this->replace) : ?>
                        <fieldset class="adminform">
                        <div id="hwd-upload-status" class="hide">
                                <p>
                                        <a href="#" id="hwd-upload-browse" class="button"><?php echo JText::_('COM_HWDMS_BROWSE_FILES'); ?></a> | 
                                        <a href="#" id="hwd-upload-clear" class="button"><?php echo JText::_('COM_HWDMS_CLEAR_LIST'); ?></a> | 
                                        <a href="#" id="hwd-upload-upload" class="button"><?php echo JText::_('COM_HWDMS_START_UPLOAD'); ?></a>
                                </p>
                                <div>
                                        <span class="overall-title"></span>
                                        <img src="<?php echo JURI::root(true); ?>/media/com_hwdmediashare/assets/images/ajaxupload/progress-bar/bar.gif" class="progress overall-progress" />
                                </div>
                                <div class="clr"></div>
                                <div>
                                        <span class="current-title"></span>
                                        <img src="<?php echo JURI::root(true); ?>/media/com_hwdmediashare/assets/images/ajaxupload/progress-bar/bar.gif" class="progress current-progress" />
                                </div>
                                <div class="current-text"></div>
                        </div>
                        <ul id="hwd-upload-list"></ul>
                        </fieldset>
                        <?php endif; ?>
                <?php endif; ?>
                <?php if ($this->config->get('upload_tool_perl') == 1) : ?>
                <?php echo JHtml::_('sliders.panel', JText::sprintf( 'COM_HWDMS_UPLOAD_LARGE_TYPES_UP_TO_N_MB', $this->getReadableAllowedMediaTypes('large'), $maxUpload ), 'large');?>
                        <?php echo $this->uberUploadHtml; ?>
                <?php endif; ?>
                <?php if (count($this->platformExtensions) > 1) : ?>
                <?php echo JHtml::_('sliders.panel', JText::sprintf( 'COM_HWDMS_UPLOAD_TYPES_UP_TO_N_MB', $this->getReadableAllowedMediaTypes('platform'), $maxUpload ), 'large');?>
                        <fieldset class="adminform">
                                <?php echo $this->getPlatformUploadForm(); ?>
                        </fieldset>
                <?php endif; ?>
        <?php endif; ?>
        <?php if ($this->config->get('enable_uploads_remote') == 1) : ?>
        <?php echo JHtml::_('sliders.panel', JText::_( 'COM_HWDMS_ADD_REMOTE_MEDIA' ), 'remote');?>
                <fieldset class="adminform" >
                        <ul class="panelform">
                                <?php foreach($this->form->getFieldset('remote') as $field): ?>
                                        <li><?php echo $field->label;echo $field->input;?></li>
                                <?php endforeach; ?>
                                <li>
                                        <label></label>
                                        <button type="button" onclick="Joomla.submitbutton('addmedia.remote')">
                                                <?php echo JText::_('COM_HWDMS_ADD') ?>
                                        </button>
                                </li>
                        </ul>
                </fieldset>
        <?php echo JHtml::_('sliders.panel', JText::_( 'COM_HWDMS_ADD_REMOTE_FILE' ), 'link');?>
                <fieldset class="adminform" >
                        <ul class="panelform">
                                <?php foreach($this->form->getFieldset('link') as $field): ?>
                                        <li><?php echo $field->label;echo $field->input;?></li>
                                <?php endforeach; ?>
                                <li>
                                        <label></label>
                                        <button type="button" onclick="Joomla.submitbutton('addmedia.link')">
                                                <?php echo JText::_('COM_HWDMS_ADD') ?>
                                        </button>
                                </li>
                        </ul>
                </fieldset>
        <?php endif; ?>
        <?php if ($this->config->get('enable_uploads_embed') == 1) : ?>
        <?php echo JHtml::_('sliders.panel', JText::_( 'COM_HWDMS_ADD_EMBED_CODE' ), 'embed');?>
                <fieldset class="adminform" >
                        <ul class="panelform">
                                <?php foreach($this->form->getFieldset('embed') as $field): ?>
                                        <li><?php echo $field->label;echo $field->input;?></li>
                                <?php endforeach; ?>
                                <li>
                                        <label></label>
                                        <button type="button" onclick="Joomla.submitbutton('addmedia.embed')">
                                                <?php echo JText::_('COM_HWDMS_ADD') ?>
                                        </button>
                                </li>
                        </ul>
                </fieldset>
        <?php endif; ?>
        <?php if ($this->config->get('enable_uploads_rtmp') == 1) : ?>
        <?php echo JHtml::_('sliders.panel', JText::_( 'COM_HWDMS_ADD_RTMP' ), 'rtmp');?>
                <fieldset class="adminform" >
                        <ul class="panelform">
                                <?php foreach($this->form->getFieldset('rtmp') as $field): ?>
                                        <li><?php echo $field->label;echo $field->input;?></li>
                                <?php endforeach; ?>
                                <li>
                                        <label></label>
                                        <button type="button" onclick="Joomla.submitbutton('addmedia.rtmp')">
                                                <?php echo JText::_('COM_HWDMS_ADD') ?>
                                        </button>
                                </li>
                        </ul>
                </fieldset>
        <?php endif; ?>
        <?php if (!$this->replace) : ?>
        <?php echo JHtml::_('sliders.panel', JText::_( 'COM_HWDMS_ADD_FROM_SERVER' ), 'server');?>   
                <table width="100%">
                        <tr valign="top">
                                <td>
                                        <fieldset id="treeview">
                                                <!--<legend><?php echo JText::_('COM_HWDMS_FOLDERS'); ?></legend>-->
                                                <div id="media-tree_tree"></div>
                                                <?php echo $this->loadTemplate('folders'); ?>
                                        </fieldset>
                                </td>
                                <td>
                                        <fieldset id="folderview">
                                                <div class="view">
                                                        <iframe src="index.php?option=com_hwdmediashare&amp;task=addmedia.scan&amp;tmpl=component&amp;folder=<?php echo $this->state->folder;?>" id="folderframe" name="folderframe" width="100%" height="300" marginwidth="0" marginheight="0" scrolling="auto"></iframe>
                                                </div>
                                                <!--<legend><?php echo JText::_('COM_HWDMS_FILES'); ?></legend>-->
                                                <div class="path">
                                                        <input class="inputbox" type="hidden" id="folderpath" readonly="readonly" />
                                                        <input class="inputbox" type="hidden" id="foldername" name="foldername"  />
                                                </div>
                                        </fieldset>
                                </td>
                        </tr>
                </table>
        <?php endif; ?>
        <?php echo JHtml::_('sliders.end'); ?>
        <div class="clr"> </div>
        <div>
		<input type="hidden" name="task" value="" />
                <?php if ($this->replace) : ?><input type="hidden" name="jform[id]" value="<?php echo JRequest::getInt('id'); ?>" /><?php endif; ?>
                <?php if ($this->replace) : ?><input type="hidden" name="jform_id" value="<?php echo JRequest::getInt('id'); ?>" /><?php endif; ?>
		<?php // The token breaks the XML redirect file for uber // echo JHtml::_('form.token'); ?>
	</div>
</form>
