<?php
/**
 * @version    SVN $Id: default_scan.php 496 2012-08-29 13:26:32Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// load tooltip behavior
JHtml::_('behavior.tooltip');
JHtml::_('behavior.framework');

$user = & JFactory::getUser();
?>
<form action="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=addmedia.import'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
        <fieldset class="adminform" >
                <ul class="panelform">
                        <li>
                                <label title="" class="hasTip" for="jform_embed_code" id="jform_embed_code-lbl">Directory</label>
                                <input type="text" size="40" class="readonly" value="/media/<?php echo $this->folder; ?>" id="jform_secret">
                        </li>
                        <li>
                                <label title="" class="hasTip" for="jform_embed_code" id="jform_embed_code-lbl">Media discovered</label>
                                <input type="text" size="40" class="readonly" value="<?php echo $this->count; ?> media" id="jform_secret">
                        </li>
                        <?php if ($this->count > 0): ?>
                        <li>
                                <label></label>
                                <button type="button" onclick="Joomla.submitbutton('addmedia.import')">
                                        <?php echo JText::_('COM_HWDMS_IMPORT') ?>
                                </button>
                        </li>
                        <?php endif; ?>
                </ul>
        </fieldset>
        <div class="clr"> </div>
        <div>
		<input type="hidden" name="task" value="" />
                <input type="hidden" name="tmpl" value="component" />
                <input type="hidden" name="folder" value="<?php echo $this->folder; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
