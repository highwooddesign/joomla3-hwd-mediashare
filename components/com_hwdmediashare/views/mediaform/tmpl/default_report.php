<?php
/**
 * @package     Joomla.site
 * @subpackage  Component.hwdmediashare
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

JHtml::_('behavior.framework', true);
?>
<div class="edit">
  <form action="<?php echo JRoute::_('index.php?option=com_hwdmediashare&tmpl=component&id=' . $this->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
    <fieldset>
      <legend><?php echo JText::_('COM_HWDMS_REPORT_MEDIA'); ?></legend>
      <?php foreach($this->form->getFieldset('details') as $field): ?>
      <div class="control-group">
        <div class="control-label">
          <?php echo $field->label; ?>
        </div>
        <div class="controls">
          <?php echo $field->input; ?>
        </div>
      </div>       
      <?php endforeach; ?>
    </fieldset>
    <div class="formelm-buttons">
      <button onclick="Joomla.submitbutton('media.report')" type="button" class="btn"><?php echo JText::_('COM_HWDMS_REPORT'); ?></button>
    </div>
    <div>
    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?> </div>
  </form>
</div>
