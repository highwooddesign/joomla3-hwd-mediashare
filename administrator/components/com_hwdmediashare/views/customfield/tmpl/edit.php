<?php
/**
 * @version    SVN $Id: edit.php 277 2012-03-28 10:03:31Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
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
            $('jform_coreparams').removeClass('width-60').addClass('width-100');
            $('jform_fieldcode').set('disabled',true);
            $('jform_required0').set('disabled',true);
            $('jform_required1').set('disabled',true);
            $('jform_tooltip').set('disabled',true);
        }
        else
        {
            $('jform_fieldparams').addClass('width-40 fltrt').setStyle('visibility', 'visible');
            $('jform_coreparams').removeClass('width-100').addClass('width-60 fltlft');
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
                            var output = '<fieldset class="adminform"><legend>'+name+'</legend><ul class="adminformlist">'
                            for(field in object)
                            {
                                    output += '<li>'+object[field][0];
                                    output += '<fieldset class="radio" >'
                                    output += object[field][1];
                                    output += '</fieldset>'
                                    output += '</li>';
                                    output += '<div class="clr"></div>';
                            }
                            output += '</ul></fieldset>';

                            $('jform_fieldparams').removeClass('ajax-loading').set('html', output);
                    }
            }).send();
        }
};

window.addEvent( 'domready', function() { $('jform_type').addEvent( 'change', loadParams); });
window.addEvent( 'domready', function() { loadParams(); });

EOD;

$doc = & JFactory::getDocument();
$doc->addScriptDeclaration( $ajax );
?>
<form action="<?php echo JRoute::_('index.php?option=com_hwdmediashare&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
    <div class="width-60 fltlft" id="jform_coreparams">
        <fieldset class="adminform">
                <legend><?php echo JText::_( 'COM_HWDMS_CUSTOM_FIELD_DETAILS' ); ?></legend>
                <ul class="adminformlist">
                        <?php foreach($this->form->getFieldset('details') as $field): ?>
                                <li><?php echo $field->label;echo $field->input;?></li>
                                <div class="clr"></div>
                        <?php endforeach; ?>
                </ul>
        </fieldset>
    </div>
    <div class="width-40 fltrt" id="jform_fieldparams">
    </div>
	<div>
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

