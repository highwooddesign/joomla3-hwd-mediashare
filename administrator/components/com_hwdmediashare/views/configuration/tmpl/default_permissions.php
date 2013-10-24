<?php
/**
 * @version    SVN $Id: default_permissions.php 425 2012-06-28 07:48:57Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      23-Nov-2011 15:54:07
 */

// No direct access
defined('_JEXEC') or die;

?>
<div class="width-100">
    <fieldset class="adminform">
            <legend><?php echo JText::_('COM_HWDMS_PERMISSIONS'); ?></legend>
            <ul class="adminformlist">
                <li><?php echo $this->form->getLabel('rules'); ?>
                <?php echo $this->form->getInput('rules'); ?></li>                      
            </ul>
    </fieldset>
</div>

