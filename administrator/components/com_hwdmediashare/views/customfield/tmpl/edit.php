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

// Load Mootools for javascript
JHtml::_('behavior.framework', true);

JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
//@TODO: Get the parameter AJAX update working with the chosen framework
//JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');

$app = JFactory::getApplication();
$input = $app->input;
$isNew = $this->item->id == 0 ? true : false ;

// Define URL to load parameters for custom field type
$url = 'index.php?option=com_hwdmediashare&task=customfield.fieldparameters&field='.intval($this->item->id).'&format=json';

$ajax = <<<EOD
var loadParams = function()
{
        var type = $('jform_type').get("value");
        var select_list_selected_index = $('jform_type').selectedIndex;
        var name = $('jform_type').options[select_list_selected_index].text;

        if (type == "group")
        {
            $('jform_fieldparams').empty().setStyle('visibility', 'hidden');
            $('jform_fieldcode').set('disabled',true);
            $('jform_required0').set('disabled',true);
            $('jform_required1').set('disabled',true);
            $('jform_tooltip').set('disabled',true);
        }
        else
        {
            $('jform_fieldparams').addClass('width-40 fltrt').setStyle('visibility', 'visible');
            $('jform_fieldcode').set('disabled',false);
            $('jform_required0').set('disabled',false);
            $('jform_required1').set('disabled',false);
            $('jform_tooltip').set('disabled',false);

            $('jform_fieldparams').empty().addClass('ajax-loading');

            var a = new Request({
                    url: '{$url}&type='+type,
                    method: 'get',
                    onComplete: function( response ) {
                            var object = JSON.decode(response);
                            var output = ''                 
                            for(field in object)
                            {
                                    //console.log(object[field]['input']);  
                                    output += '<div class="control-group">';
                                    output += '<div class="control-label">';
                                    output += object[field]['label'];
                                    output += '</div>';
                                    output += '<div class="controls">';
                                    output += object[field]['input'];
                                    output += '</div>';
                                    output += '</div>';
                            }
                            output += '';

                            $('jform_fieldparams').removeClass('ajax-loading').set('html', output);
                    }
            }).send();
        }
};
window.addEvent('domready', function() { $('jform_type').addEvent('change', loadParams); });
window.addEvent('domready', function() { loadParams(); });
EOD;
$doc = JFactory::getDocument();
$doc->addScriptDeclaration($ajax);
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'customfield.cancel' || document.formvalidator.isValid(document.id('item-form')))
		{
			Joomla.submitform(task, document.getElementById('item-form'));
		}
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=customfield&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate" enctype="multipart/form-data">

        <div class="form-inline form-inline-header">
                <div class="control-group">
                        <div class="control-label">
                                <?php echo $this->form->getLabel('name'); ?>
                        </div>
                        <div class="controls">
                                <?php echo $this->form->getInput('name'); ?>
                        </div>
                </div>
        </div>
    
	<div class="form-horizontal">
            
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_HWDMS_CUSTOM_FIELD_DETAILS', true)); ?>
                <div class="row-fluid form-horizontal-desktop">
                        <div class="span6" id="jform_coreparams">
                                <div class="control-group">
                                        <div class="control-label">
                                                <?php echo $this->form->getLabel('element_type'); ?>
                                        </div>
                                        <div class="controls">
                                                <?php echo $this->form->getInput('element_type'); ?>
                                        </div>
                                </div>
                                <div class="control-group">
                                        <div class="control-label">
                                                <?php echo $this->form->getLabel('type'); ?>
                                        </div>
                                        <div class="controls">
                                                <?php echo $this->form->getInput('type'); ?>
                                        </div>
                                </div>
                                <div class="control-group">
                                        <div class="control-label">
                                                <?php echo $this->form->getLabel('fieldcode'); ?>
                                        </div>
                                        <div class="controls">
                                                <?php echo $this->form->getInput('fieldcode'); ?>
                                        </div>
                                </div>
                                <div class="control-group">
                                        <div class="control-label">
                                                <?php echo $this->form->getLabel('published'); ?>
                                        </div>
                                        <div class="controls">
                                                <?php echo $this->form->getInput('published'); ?>
                                        </div>
                                </div>
                                <div class="control-group">
                                        <div class="control-label">
                                                <?php echo $this->form->getLabel('searchable'); ?>
                                        </div>
                                        <div class="controls">
                                                <?php echo $this->form->getInput('searchable'); ?>
                                        </div>
                                </div>
                                <div class="control-group">
                                        <div class="control-label">
                                                <?php echo $this->form->getLabel('visible'); ?>
                                        </div>
                                        <div class="controls">
                                                <?php echo $this->form->getInput('visible'); ?>
                                        </div>
                                </div>
                                <div class="control-group">
                                        <div class="control-label">
                                                <?php echo $this->form->getLabel('required'); ?>
                                        </div>
                                        <div class="controls">
                                                <?php echo $this->form->getInput('required'); ?>
                                        </div>
                                </div>
                                <div class="control-group">
                                        <div class="control-label">
                                                <?php echo $this->form->getLabel('tooltip'); ?>
                                        </div>
                                        <div class="controls">
                                                <?php echo $this->form->getInput('tooltip'); ?>
                                        </div>
                                </div>
                                <div class="control-group">
                                        <div class="control-label">
                                                <?php echo $this->form->getLabel('options'); ?>
                                        </div>
                                        <div class="controls">
                                                <?php echo $this->form->getInput('options'); ?>
                                        </div>
                                </div>
                        </div>
                        <div class="span6" id="jform_fieldparams">

                        </div>
                </div>           
                <?php echo JHtml::_('bootstrap.endTab'); ?>
 
		<?php echo JHtml::_('bootstrap.endTabSet'); ?>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="return" value="<?php echo $input->getCmd('return'); ?>" />
		<?php echo JHtml::_('form.token'); ?>
        </div>
</form>

    
    
    
    
      
