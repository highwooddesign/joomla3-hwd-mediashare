<?php
/**
 * @version    SVN $Id: default_report.php 425 2012-06-28 07:48:57Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      18-Dec-2011 09:49:25
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.modal');
JHtml::_('behavior.framework', true);

?>

<div class="edit">
  <form action="<?php echo JRoute::_('index.php?option=com_hwdmediashare'); ?>" method="post" id="adminForm" class="formelm">
    <fieldset>
      <legend><?php echo JText::_( 'COM_HWDMS_REPORT_ACTIVITY' ); ?></legend>
      <?php foreach($this->form->getFieldset('details') as $field): ?>
      <div class="formelm"><?php echo $field->label;echo $field->input;?></div>
      <?php endforeach; ?>
    </fieldset>
    <div class="formelm-buttons">
      <button onclick="Joomla.submitbutton('activity.report')" type="button" class="button"><?php echo JText::_('COM_HWDMS_REPORT'); ?></button>
      <button onclick="window.parent.SqueezeBox.close();" type="button" class="button"><?php echo JText::_('COM_HWDMS_CANCEL'); ?></button>
    </div>
    <div>
      <input type="hidden" name="tmpl" value="component" />
      <input type="hidden" name="id" value="<?php echo $this->id; ?>" />
      <input type="hidden" name="task" value="activity.report" />
      <?php echo JHtml::_('form.token'); ?> </div>
  </form>
</div>