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

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');

$app = JFactory::getApplication();
$input = $app->input;
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'addmedia.cancel' || document.formvalidator.isValid(document.id('item-form')))
		{
			<?php echo $this->form->getField('description')->save(); ?>
			Joomla.submitform(task, document.getElementById('item-form'));
		}
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=addmedia'); ?>" method="post" name="adminForm" id="item-form" class="form-validate" enctype="multipart/form-data">

	<?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>
    
	<div class="form-horizontal">
            
		<div class="row-fluid">
			<div class="span9">
				<fieldset class="adminform">
					<?php echo $this->form->getInput('description'); ?>
				</fieldset>
			</div>
			<div class="span3">
				<?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
			</div>
		</div>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="return" value="<?php echo $input->getCmd('return'); ?>" />
                <?php echo $this->form->getInput('album_id'); ?>
                <?php echo $this->form->getInput('category_id'); ?>
                <?php echo $this->form->getInput('group_id'); ?>
                <?php echo $this->form->getInput('playlist_id'); ?>
		<?php echo JHtml::_('form.token'); ?>
        </div>
</form>
