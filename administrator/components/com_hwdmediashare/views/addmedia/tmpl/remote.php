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
<form action="<?php echo JRoute::_('index.php?option=com_hwdmediashare'); ?>" method="post" target="_parent" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">
        <div class="row-fluid">
                <div class="span9">
                        <div class="alert">
                                <p><?php echo JText::_('COM_HWDMS_ADD_REMOTE_MEDIA_DESC'); ?></p>
                                <p><?php echo hwdMediaShareRemote::getReadableAllowedRemotes(); ?></p>
                        </div>
                        <?php if ($this->config->get('upload_workflow') == 1) : ?>
                                <p class="alert alert-info"><?php echo JText::_('COM_HWDMS_ADD_MULTIPLE_REMOTE_MEDIA_DESC'); ?></p>
                        <?php endif; ?>
                        <?php if ($this->config->get('upload_workflow') == 0) : ?>
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
                                <button type="button" class="btn btn-info btn-large" onclick="Joomla.submitbutton('addmedia.remote')">
                                        <?php echo JText::_('COM_HWDMS_ADD') ?>
                                </button>
                        </div>  
                </div>
                <div class="span3">
                        <?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
                </div>
        </div>
        <input type="hidden" name="task" value="" />
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
        <?php echo JHtml::_('form.token'); ?>            
</form>