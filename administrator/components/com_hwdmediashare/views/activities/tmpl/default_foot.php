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
jimport('joomla.form.form');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('album');
JFormHelper::loadFieldClass('playlist');
JFormHelper::loadFieldClass('group');
$this->form = & JForm::getInstance('report', JPATH_SITE.'/administrator/components/com_hwdmediashare/models/forms/batch.xml');
?>
<tr>
        <td colspan="15"><?php echo $this->pagination->getListFooter(); ?></td>
</tr>
<tr>
        <td colspan="15">
                <fieldset class="batch">
                        <legend><?php echo JText::_('COM_HWDMS_BATCH_PROCESS_SELECTED_ACTIVITIES'); ?></legend>
                        
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
                        <button type="submit" onclick="submitbutton('activities.batch');">
                        <?php echo JText::_('COM_HWDMS_PROCESS'); ?></button>
                        <button type="button" onclick="document.id('batch_user').value='';document.id('batch_access').value='';document.id('batch_language').value=''">
                        <?php echo JText::_('COM_HWDMS_CLEAR'); ?></button>
                </fieldset>
        </td>
</tr>