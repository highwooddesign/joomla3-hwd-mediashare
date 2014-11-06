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
                <div class="<?php echo $this->replace ? 'span12' : 'span9'; ?>">
                        <?php echo JHtml::_('bootstrap.startAccordion', 'hwdmediashare-slider-upload', array('active' => 'local')); ?>
                        <?php if ($this->config->get('enable_uploads_file') == 1) : ?>   
                                <?php echo JHtml::_('bootstrap.addSlide', 'hwdmediashare-slider-upload', JText::_('COM_HWDMS_SELECT_FILES_TO_UPLOAD'), 'local'); ?>
                                        <?php if ($this->config->get('upload_tool_perl') == 1): ?>
                                                <?php echo $this->loadTemplate('large'); ?>
                                        <?php elseif ($this->config->get('upload_workflow') == 1 && !$this->replace): ?>
                                                <?php echo $this->loadTemplate('multi'); ?>
                                        <?php else: ?>
                                                <?php echo $this->loadTemplate('single'); ?>
                                        <?php endif; ?>
                                <?php echo JHtml::_('bootstrap.endSlide'); ?>
                        <?php endif; ?>
                        <?php if ($this->config->get('enable_uploads_platform') == 1) : ?>   
                                <?php echo JHtml::_('bootstrap.addSlide', 'hwdmediashare-slider-upload', $this->getPlatformUploadTitle(), 'platform'); ?>
                                        <?php echo $this->getPlatformUploadForm(); ?>
                                <?php echo JHtml::_('bootstrap.endSlide'); ?>
                        <?php endif; ?>
                        <?php if ($this->config->get('enable_uploads_remote') == 1) : ?>
                                <?php echo JHtml::_('bootstrap.addSlide', 'hwdmediashare-slider-upload', JText::_('COM_HWDMS_ADD_REMOTE_FILE'), 'link'); ?>                 
                                        <div class="btn-wrapper input-append">
                                                <div class="control-group">
                                                        <div class="control-label hide">
                                                                <?php echo $this->form->getLabel('link_url'); ?>
                                                        </div>
                                                        <div class="controls">
                                                                <?php echo $this->form->getInput('link_url'); ?>
                                                                <button type="button" class="btn" onclick="Joomla.submitbutton('addmedia.link')">
                                                                        <?php echo JText::_('COM_HWDMS_ADD') ?>
                                                                </button>                                                   
                                                        </div>
                                                </div>
                                        </div>
                                        <p></p>
                                        <div class="well well-small">
                                                <h3><?php echo JText::_('COM_HWDMS_HELP_AND_SUGGESTIONS'); ?></h3>
                                                <p><?php echo JText::_('COM_HWDMS_HELP_ADDING_REMOTE_FILES'); ?></p>  
                                                <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=addmedia&method=remote'); ?>" class="btn"><?php echo JText::_('COM_HWDMS_ADD_REMOTE_MEDIA'); ?></a>   
                                                <a href="http://hwdmediashare.co.uk/learn/docs/72-adding-media/adding-remote-files/321-adding-remote-files" class="btn" target="_blank"><?php echo JText::_('COM_HWDMS_BTN_DOCUMENTATION'); ?></a>   
                                        </div>                     
                                <?php echo JHtml::_('bootstrap.endSlide'); ?>
                        <?php endif; ?>
                        <?php if ($this->config->get('enable_uploads_embed') == 1) : ?>
                                <?php echo JHtml::_('bootstrap.addSlide', 'hwdmediashare-slider-upload', JText::_('COM_HWDMS_ADD_EMBED_CODE'), 'embed'); ?>
                                        <div class="control-group">
                                                <div class="control-label hide">
                                                        <?php echo $this->form->getLabel('embed_code'); ?>
                                                </div>
                                                <div class="controls media-textarea">
                                                        <?php echo $this->form->getInput('embed_code'); ?> 
                                                        <button type="button" class="btn" onclick="Joomla.submitbutton('addmedia.embed')">
                                                                <?php echo JText::_('COM_HWDMS_ADD') ?>
                                                        </button>  
                                                </div>
                                        </div>
                                        <p></p>
                                        <div class="well well-small">
                                                <h3><?php echo JText::_('COM_HWDMS_HELP_AND_SUGGESTIONS'); ?></h3>
                                                <p><?php echo JText::_('COM_HWDMS_HELP_ADDING_EMBED_CODES'); ?></p>  
                                                <a href="http://hwdmediashare.co.uk/learn/docs/75-adding-media/adding-embed-codes/317-importing-embed-codes" class="btn" target="_blank"><?php echo JText::_('COM_HWDMS_BTN_DOCUMENTATION'); ?></a>   
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
                                        <p></p>
                                        <div class="well well-small">
                                                <h3><?php echo JText::_('COM_HWDMS_HELP_AND_SUGGESTIONS'); ?></h3>
                                                <p><?php echo JText::_('COM_HWDMS_HELP_ADDING_RTMP_STREAM'); ?></p>  
                                                <a href="http://hwdmediashare.co.uk/learn/docs/73-adding-media/adding-rtmp-streams/320-adding-rtmp-streams" class="btn" target="_blank"><?php echo JText::_('COM_HWDMS_BTN_DOCUMENTATION'); ?></a>   
                                        </div>                                           
                                <?php echo JHtml::_('bootstrap.endSlide'); ?>
                        <?php endif; ?>
                        <?php if (!$this->replace) : ?>
                        <?php echo JHtml::_('bootstrap.addSlide', 'hwdmediashare-slider-upload', JText::_('COM_HWDMS_ADD_FROM_SERVER'), 'server'); ?>
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
                                        <p></p>
                                        <div class="well well-small">
                                                <h3><?php echo JText::_('COM_HWDMS_HELP_AND_SUGGESTIONS'); ?></h3>
                                                <p><?php echo JText::_('COM_HWDMS_HELP_ADDING_FROM_SERVER'); ?></p>  
                                                <a href="http://hwdmediashare.co.uk/learn/docs/74-adding-media/server-directory-scanning" class="btn" target="_blank"><?php echo JText::_('COM_HWDMS_BTN_DOCUMENTATION'); ?></a>   
                                        </div>                                   
                        <?php echo JHtml::_('bootstrap.endSlide'); ?>
                        <?php endif; ?>
                        <?php echo JHtml::_('bootstrap.endAccordion'); ?>
                </div>
                <?php if (!$this->replace) : ?>
                <div class="span3">
                        <?php if ($this->jformreg->get('title')): ?>
                                <h2><?php echo $this->jformreg->get('title'); ?></h2>
                        <?php endif; ?>
                        <?php if ($this->jformreg->get('alias')): ?>
                                <p class="small"><?php echo $this->jformreg->get('alias'); ?></p>
                        <?php endif; ?>                        
                        <?php if ($this->jformreg->get('description')): ?>
                                <p><?php echo JHtml::_('string.truncate', $this->jformreg->get('description'), 160, false, false); ?></p>
                        <?php endif; ?>  
                        <?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>  
                </div>
                <?php endif; ?>
        </div>
        <input type="hidden" name="task" value="" />
	<input type="hidden" name="return" value="<?php echo $this->return; ?>" />
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
