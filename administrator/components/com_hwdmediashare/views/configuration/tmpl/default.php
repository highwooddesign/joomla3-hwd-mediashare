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
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');

// Some servers have a "suhosin.post.max_vars" limit (or similar), and this can lead to the end variables being 
// dropped during submission. Therefore, we have brought the "task" and "token" variables to the start of the 
// form so that it can at least be saved correctly

?>
<form action="<?php echo JRoute::_('index.php?option=com_hwdmediashare');?>" method="post" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">
<input type="hidden" name="task" value="" />
<?php echo JHtml::_('form.token'); ?>   
<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
		<!-- Begin Content -->
                <?php echo JHtml::_('bootstrap.startTabSet', 'config', array('active' => 'page-site')); ?>

                <?php echo JHtml::_('bootstrap.addTab', 'config', 'page-site', JText::_('COM_HWDMS_SITE', true)); ?>
                        <?php echo $this->loadTemplate('site'); ?>
                <?php echo JHtml::_('bootstrap.endTab'); ?>

                <?php echo JHtml::_('bootstrap.addTab', 'config', 'page-media', JText::_('COM_HWDMS_MEDIA', true)); ?>
                        <?php echo $this->loadTemplate('media'); ?>
                <?php echo JHtml::_('bootstrap.endTab'); ?>

                <?php echo JHtml::_('bootstrap.addTab', 'config', 'page-processing', JText::_('COM_HWDMS_PROCESSING', true)); ?>
                        <?php echo $this->loadTemplate('processing'); ?>
                <?php echo JHtml::_('bootstrap.endTab'); ?>

                <?php echo JHtml::_('bootstrap.addTab', 'config', 'page-permissions', JText::_('COM_HWDMS_PERMISSIONS', true)); ?>
                        <?php echo $this->loadTemplate('permissions'); ?>
                <?php echo JHtml::_('bootstrap.endTab'); ?>

                <?php echo JHtml::_('bootstrap.addTab', 'config', 'page-layout', JText::_('COM_HWDMS_LAYOUT', true)); ?>
                        <?php echo $this->loadTemplate('layout'); ?>
                <?php echo JHtml::_('bootstrap.endTab'); ?>

                <?php echo JHtml::_('bootstrap.addTab', 'config', 'page-integrations', JText::_('COM_HWDMS_INTEGRATIONS', true)); ?>
                        <?php echo $this->loadTemplate('integrations'); ?>
                <?php echo JHtml::_('bootstrap.endTab'); ?>

                <?php echo JHtml::_('bootstrap.addTab', 'config', 'page-uploads', JText::_('COM_HWDMS_UPLOADS', true)); ?>
                        <?php echo $this->loadTemplate('uploads'); ?>
                <?php echo JHtml::_('bootstrap.endTab'); ?>                   

                <?php echo JHtml::_('bootstrap.endTabSet'); ?>
		<!-- End Content -->
        </div>
</form>
