<?php
/**
 * @version    SVN $Id: default_foot.php 277 2012-03-28 10:03:31Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

include_once JPATH_ROOT . '/administrator/components/com_hwdmediashare/models/fields/album.php';
include_once JPATH_ROOT . '/administrator/components/com_hwdmediashare/models/fields/playlist.php';
include_once JPATH_ROOT . '/administrator/components/com_hwdmediashare/models/fields/group.php';
include_once JPATH_ROOT . '/administrator/components/com_hwdmediashare/models/fields/process.php';
jimport('joomla.form.form');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('album');
JFormHelper::loadFieldClass('playlist');
JFormHelper::loadFieldClass('group');
JFormHelper::loadFieldClass('process');
$this->form = & JForm::getInstance('report', JPATH_SITE.'/administrator/components/com_hwdmediashare/models/forms/batch.xml');
?>
<tr>
        <td colspan="15"><?php echo $this->pagination->getListFooter(); ?></td>
</tr>
<tr>
        <td colspan="15">
                <fieldset class="batch">
                        <legend><?php echo JText::_('COM_HWDMS_BATCH_PROCESS_SELECTED_MEDIA'); ?></legend>
                        
                        <div class="clr"></div>
                        <?php echo $this->form->getLabel('batch_user'); ?>
                        <?php echo $this->form->getInput('batch_user'); ?>

                        <div class="clr"></div>
                        <?php echo $this->form->getLabel('batch_access'); ?>
                        <?php echo $this->form->getInput('batch_access'); ?>

                        <div class="clr"></div>
                        <?php echo $this->form->getLabel('batch_language'); ?>
                        <?php echo $this->form->getInput('batch_language'); ?>

                        <div class="clr"></div>
                        <button type="submit" onclick="submitbutton('media.batch');">
                        <?php echo JText::_('COM_HWDMS_PROCESS'); ?></button>
                        <button type="button" onclick="document.id('batch_user').value='';document.id('batch_access').value='';document.id('batch_language').value=''">
                        <?php echo JText::_('COM_HWDMS_CLEAR'); ?></button>

                        <div class="clr"></div>
                        <div class="width-50 fltlft">
                                <fieldset class="adminform">
                                <legend><?php echo JText::_('COM_HWDMS_BATCH_ADD'); ?></legend>
                                
                                <div class="clr"></div>
                                <div class="fltrt"><input type="button" class="button" value="<?php echo JText::_('COM_HWDMS_ADD_TO_CATEGORY'); ?>" onclick="javascript:if (document.adminForm.boxchecked.value==0){alert('Please first make a selection from the list');}else{ Joomla.submitbutton('media.assigncategory')}" ></div>
                                <?php echo $this->form->getLabel('assign_category_id'); ?>
                                <?php echo $this->form->getInput('assign_category_id'); ?>
                                
                                <div class="clr"></div>
                                <div class="fltrt"><input type="button" class="button" value="<?php echo JText::_('COM_HWDMS_ADD_TO_ALBUM'); ?>" onclick="javascript:if (document.adminForm.boxchecked.value==0){alert('Please first make a selection from the list');}else{ Joomla.submitbutton('media.assignalbum')}" ></div>
                                <?php echo $this->form->getLabel('assign_album_id'); ?>
                                <?php echo $this->form->getInput('assign_album_id'); ?>
                                
                                <div class="clr"></div>
                                <div class="fltrt"><input type="button" class="button" value="<?php echo JText::_('COM_HWDMS_ADD_TO_PLAYLIST'); ?>" onclick="javascript:if (document.adminForm.boxchecked.value==0){alert('Please first make a selection from the list');}else{ Joomla.submitbutton('media.assignplaylist')}" ></div>
                                <?php echo $this->form->getLabel('assign_playlist_id'); ?>
                                <?php echo $this->form->getInput('assign_playlist_id'); ?>
                                
                                <div class="clr"></div>
                                <div class="fltrt"><input type="button" class="button" value="<?php echo JText::_('COM_HWDMS_ADD_TO_GROUP'); ?>" onclick="javascript:if (document.adminForm.boxchecked.value==0){alert('Please first make a selection from the list');}else{ Joomla.submitbutton('media.assigngroup')}" ></div>
                                <?php echo $this->form->getLabel('assign_group_id'); ?>
                                <?php echo $this->form->getInput('assign_group_id'); ?>
                                
                                <div class="clr"></div>
                                <div class="fltrt"><input type="button" class="button" value="<?php echo JText::_('COM_HWDMS_QUEUE_THIS_PROCESS'); ?>" onclick="javascript:if (document.adminForm.boxchecked.value==0){alert('Please first make a selection from the list');}else{ Joomla.submitbutton('media.assignprocess')}" ></div>
                                <?php echo $this->form->getLabel('assign_process_type'); ?>
                                <?php echo $this->form->getInput('assign_process_type'); ?>
                                </fieldset>
                        </div>
                        <div class="width-50 fltrt">
                                <fieldset class="adminform">
                                <legend><?php echo JText::_('COM_HWDMS_BATCH_REMOVAL'); ?></legend>
                                
                                <div class="clr"></div>
                                <div class="fltrt"><input type="button" class="button" value="<?php echo JText::_('COM_HWDMS_REMOVE_FROM_CATEGORY'); ?>" onclick="javascript:if (document.adminForm.boxchecked.value==0){alert('Please first make a selection from the list');}else{ Joomla.submitbutton('media.unassigncategory')}" ></div>
                                <?php echo $this->form->getLabel('unassign_category_id'); ?>
                                <?php echo $this->form->getInput('unassign_category_id'); ?>
                                
                                <div class="clr"></div>
                                <div class="fltrt"><input type="button" class="button" value="<?php echo JText::_('COM_HWDMS_REMOVE_FROM_ALBUM'); ?>" onclick="javascript:if (document.adminForm.boxchecked.value==0){alert('Please first make a selection from the list');}else{ Joomla.submitbutton('media.unassignalbum')}" ></div>
                                <?php echo $this->form->getLabel('unassign_album_id'); ?>
                                <?php echo $this->form->getInput('unassign_album_id'); ?>                          
                                
                                <div class="clr"></div>
                                <div class="fltrt"><input type="button" class="button" value="<?php echo JText::_('COM_HWDMS_REMOVE_FROM_PLAYLIST'); ?>" onclick="javascript:if (document.adminForm.boxchecked.value==0){alert('Please first make a selection from the list');}else{ Joomla.submitbutton('media.unassignplaylist')}" ></div>
                                <?php echo $this->form->getLabel('unassign_playlist_id'); ?>
                                <?php echo $this->form->getInput('unassign_playlist_id'); ?>
                                
                                <div class="clr"></div>
                                <div class="fltrt"><input type="button" class="button" value="<?php echo JText::_('COM_HWDMS_REMOVE_FROM_GROUP'); ?>" onclick="javascript:if (document.adminForm.boxchecked.value==0){alert('Please first make a selection from the list');}else{ Joomla.submitbutton('media.unassigngroup')}" ></div>
                                <?php echo $this->form->getLabel('unassign_group_id'); ?>
                                <?php echo $this->form->getInput('unassign_group_id'); ?>
                                </fieldset>
                        </div>
                </div>
        </td>
</tr>