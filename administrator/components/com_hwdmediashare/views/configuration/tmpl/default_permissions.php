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
?>
<div class="row-fluid">
        <fieldset class="form-vertical">
                <legend><?php echo JText::_('COM_HWDMS_PERMISSIONS'); ?></legend>
                <div class="control-group">
                        <div class="controls"><?php echo $this->form->getInput('rules'); ?></div>
                </div>
        </fieldset>
</div>