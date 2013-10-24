<?php
/**
 * @version    SVN $Id: edit.php 431 2012-07-12 09:55:56Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      20-Jan-2012 09:00:25
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.calendar');
JHtml::_('behavior.formvalidation');

?>
<div class="edit">
<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
  <div id="hwd-container"> <a name="top" id="top"></a>
    <!-- Media Navigation -->
    <?php echo hwdMediaShareHelperNavigation::getInternalNavigation(); ?>
    <!-- Media Header -->
    <div class="media-header">
      <h2 class="media-album-title"><?php echo JText::_('COM_HWDMS_EDIT_ACTIVITY'); ?></h2>
      <div class="clear"></div>
    </div>
    <!-- Form -->
    <fieldset>
      <legend><?php echo JText::_('JEDITOR'); ?></legend>
      <div class="formelm-buttons">
        <button type="button" onclick="Joomla.submitbutton('activityform.save')">
          <?php echo JText::_('JSAVE') ?>
        </button>
        <button type="button" onclick="Joomla.submitbutton('activityform.cancel')">
          <?php echo JText::_('JCANCEL') ?>
        </button>
      </div>
      <?php echo $this->form->getInput('description'); ?>
    </fieldset>
    <!-- Publishing -->
    <fieldset>
      <legend><?php echo JText::_('COM_HWDMS_PUBLISHING'); ?></legend>
      <?php if ($this->item->params->get('access-change')): ?>
        <div class="formelm">
          <?php echo $this->form->getLabel('published'); ?>
          <?php echo $this->form->getInput('published'); ?>
        </div>
        <div class="formelm">
          <?php echo $this->form->getLabel('featured'); ?>
          <?php echo $this->form->getInput('featured'); ?>
        </div>
        <div class="formelm">
          <?php echo $this->form->getLabel('publish_up'); ?>
          <?php echo $this->form->getInput('publish_up'); ?>
        </div>
        <div class="formelm">
          <?php echo $this->form->getLabel('publish_down'); ?>
          <?php echo $this->form->getInput('publish_down'); ?>
        </div>
      <?php endif; ?>
      <div class="formelm">
        <?php echo $this->form->getLabel('access'); ?>
        <?php echo $this->form->getInput('access'); ?>
      </div>
      <div class="formelm">
        <?php echo $this->form->getLabel('language'); ?>
        <?php echo $this->form->getInput('language'); ?>
      </div>
      <input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
      <input type="hidden" name="task" value="" />
      <input type="hidden" name="return" value="<?php echo $this->return_page;?>" />
      <?php echo JHtml::_( 'form.token' ); ?>  
      <div class="formelm-buttons">
        <button type="button" onclick="Joomla.submitbutton('mediaform.save')">
          <?php echo JText::_('JSAVE') ?>
        </button>
        <button type="button" onclick="Joomla.submitbutton('mediaform.cancel')">
          <?php echo JText::_('JCANCEL') ?>
        </button>
      </div>
    </fieldset>
  </div>
</form>
</div>