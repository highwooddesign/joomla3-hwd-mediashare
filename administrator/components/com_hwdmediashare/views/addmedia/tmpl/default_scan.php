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

JHtml::_('behavior.formvalidation');
?>
<form action="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=addmedia&tmpl=component'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
        <div class="row-fluid">
                <div class="span12">
                        <div class="well well-small">
                                <h4><?php echo JText::_('COM_HWDMS_DIRECTORY_SCANNING_IN', $this->folder); ?></h4>
                                <p><code>/media/<?php echo $this->folder; ?></code></p>
                                <div class="label<?php echo ($this->count > 0 ? ' label-success' : ''); ?>"><?php echo JText::sprintf('COM_HWDMS_FOUND_N_MEDIA', (int) $this->count); ?></div>
                        </div>
                </div>
        </div>

        <?php if ($this->count > 0): ?>
        <div>
                <div class="btn-group">
                        <a class="btn" onclick="Joomla.submitbutton('addmedia.import')" href="javascript:void(0);"><?php echo JText::_('COM_HWDMS_IMPORT'); ?></a>
                </div> 
        </div>  
        <?php endif; ?>

        <input type="hidden" name="tmpl" value="component" />
        <input type="hidden" name="folder" value="<?php echo $this->folder; ?>" />   
        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>
</form>
