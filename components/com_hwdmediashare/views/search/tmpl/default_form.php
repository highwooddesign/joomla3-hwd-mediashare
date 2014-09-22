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
?>
<div class="btn-toolbar">
  <div class="btn-group pull-left">
    <?php echo $this->form->getInput('keyword'); ?>
  </div>
  <div class="btn-group pull-left">
    <button name="Search" onclick="Joomla.submitbutton('search.processform')" class="btn" title="<?php echo JHtml::tooltipText('COM_HWDMS_SEARCH');?>"><span class="icon-search"></span></button>
  </div>
  <div class="clearfix"></div>
</div>
