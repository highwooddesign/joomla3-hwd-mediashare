<?php
/**
 * @version    SVN $Id: default.php 1052 2013-02-07 14:50:07Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access
defined('_JEXEC') or die;

// Load tooltips behavior
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.switcher');
JHtml::_('behavior.tooltip');

// Some servers have a "suhosin.post.max_vars" limit (or similar), and this can lead to the end variables being 
// dropped during submission. Therefore, we have brought the "task" and "token" variables to the start of the 
// form so that it can at least be saved correctly

?>
<form action="<?php echo JRoute::_('index.php?option=com_hwdmediashare');?>" method="post" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">
        <input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>    
	<div id="config-document">
		<div id="page-site" class="tab">
			<div class="noshow">
				<?php echo $this->loadTemplate('site'); ?>
			</div>
		</div>
		<div id="page-media" class="tab">
			<div class="noshow">
				<?php echo $this->loadTemplate('media'); ?>
			</div>
		</div>
		<div id="page-processing" class="tab">
			<div class="noshow">
				<?php echo $this->loadTemplate('processing'); ?>
			</div>
		</div>
		<div id="page-permissions" class="tab">
			<div class="noshow">
				<?php echo $this->loadTemplate('permissions'); ?>
			</div>
		</div>
		<div id="page-layout" class="tab">
			<div class="noshow">
				<?php echo $this->loadTemplate('layout'); ?>
			</div>
		</div>
		<div id="page-integrations" class="tab">
			<div class="noshow">
				<?php echo $this->loadTemplate('integrations'); ?>
			</div>
		</div>
		<div id="page-uploads" class="tab">
			<div class="noshow">
				<?php echo $this->loadTemplate('uploads'); ?>
			</div>
		</div>            
	</div>
	<div class="clr"></div>
</form>
