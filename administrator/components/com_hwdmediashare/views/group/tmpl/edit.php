<?php
/**
 * @version    SVN $Id: edit.php 686 2012-10-24 10:31:31Z dhorsfall $
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
JHtml::_('behavior.modal');
$params = $this->form->getFieldsets('params');
$publishing = $this->form->getFieldsets('publishing');
$isNew = $this->item->id == 0 ? false : true ;
?>
<form action="<?php echo JRoute::_('index.php?option=com_hwdmediashare&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">
	<div class="width-60 fltlft">
                <fieldset class="adminform">
                        <legend><?php echo JText::_( 'COM_HWDMS_GROUP_DETAILS' ); ?></legend>
                        <ul class="adminformlist">
                                <?php foreach($this->form->getFieldset('details') as $field): ?>
                                        <?php if ($field->name == "jform[description]"): ?>
                                            <div class="clr"></div>
                                            <li><?php echo $field->input; ?></li>
                                            <div class="clr"></div>
                                        <?php else:?>
                                            <li><?php echo $field->label;echo $field->input;?></li>
                                        <?php endif;?>
                                <?php endforeach; ?>
                        </ul>
                </fieldset>

                <?php foreach( $this->item->customfields['fields'] as $group => $groupFields ) : ?>
                        <fieldset class="adminform">
                        <legend><?php echo JText::_( $group ); ?></legend>
                                <ul class="adminformlist">
                                        <?php foreach( $groupFields as $field ) : 
                                        $field = JArrayHelper::toObject ( $field );
                                        $field->value = $this->escape( $field->value ); ?>
                                                <li>
                                                        <label title="<?php echo $this->escape($field->name);?>::<?php echo $this->escape($field->tooltip); ?>" class="hasTip" for="jform_<?php echo $field->id;?>" id="jform_<?php echo $field->id;?>-lbl"><?php echo JText::_( $field->name );?><?php if($field->required == 1) echo '<span class="star">&nbsp;*</span>'; ?></label>
                                                        <?php echo hwdMediaShareCustomFields::getFieldHTML( $field , '' ); ?>
                                                </li>
                                                <div class="clr"></div>
                                        <?php endforeach; ?>
                                </ul>
                        </fieldset>
                <?php endforeach; ?>
        </div>

	<div class="width-40 fltrt">

		<?php echo JHtml::_('sliders.start', 'hwdmediashare-slider'); ?>

                <?php echo JHtml::_('sliders.panel', JText::_( 'COM_HWDMS_CUSTOM_THUMBNAIL' ), 'thumbnail');?>
                    <?php if (!empty($this->item->thumbnail)) : ?>
                    <fieldset class="adminform">
                             <ul class="adminformlist">
                                    <li>
                                            <img src="<?php echo $this->item->thumbnail; ?>" style="max-width:300px;"/>
                                    </li>
                                    <li><?php echo $this->form->getLabel('remove_thumbnail');echo $this->form->getInput('remove_thumbnail'); ?></li>
                                    <div class="clr"></div>
                             </ul>
                    </fieldset>
                    <?php endif; ?>

                    <fieldset class="adminform" >
                        <ul class="panelform">
                            <li><?php echo $this->form->getLabel('thumbnail');echo $this->form->getInput('thumbnail'); ?></li>
                        </ul>
                    </fieldset>

                <?php if ($isNew) : ?>
                <?php echo JHtml::_('sliders.panel', JText::_( 'COM_HWDMS_MEMBERS' ), 'members');?>
                        <fieldset class="panelform" >
                                <ul class="adminformlist">
                                        <li>
                                                <label title="<?php echo JText::_( 'COM_HWDMS_MANAGE_MEMBERS' ); ?>" class="hasTip" id="jform_managemembers-lbl"><?php echo JText::_( 'COM_HWDMS_GROUP_MEMBERS' ); ?> (<?php echo $this->item->membercount; ?>)</label>
                                                <div class="button2-left">
                                                <div class="blank">
                                                    <a rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=groupmembers&tmpl=component&group_id='.(int) $this->item->id); ?>" title="<?php echo JText::_( 'COM_HWDMS_MANAGE_MEMBERS' ); ?>" class="modal">
                                                        <?php echo JText::_( 'COM_HWDMS_MANAGE_MEMBERS' ); ?>
                                                    </a>
                                                </div>
                                                </div>
                                        </li>
                                </ul>
                        </fieldset>

                <?php echo JHtml::_('sliders.panel', JText::_( 'COM_HWDMS_MEDIA' ), 'media');?>
                        <fieldset class="panelform" >
                                <ul class="adminformlist">
                                        <li>
                                                <label title="<?php echo JText::_( 'COM_HWDMS_MANAGE_MEDIA' ); ?>" class="hasTip" id="jform_managemedia-lbl"><?php echo JText::_( 'COM_HWDMS_GROUP_MEDIA' ); ?> (<?php echo $this->item->mediacount; ?>)</label>
                                                <div class="button2-left">
                                                <div class="blank">
                                                    <a rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=groupmedia&tmpl=component&group_id='.(int) $this->item->id); ?>" title="<?php echo JText::_( 'COM_HWDMS_MANAGE_MEDIA' ); ?>" class="modal">
                                                        <?php echo JText::_( 'COM_HWDMS_MANAGE_MEDIA' ); ?>
                                                    </a>
                                                </div>
                                                </div>
                                        </li>
                                </ul>
                        </fieldset>
                <?php endif; ?>

                <?php echo JHtml::_('sliders.panel', JText::_( 'COM_HWDMS_PUBLISHING' ), 'publishing');?>
                <fieldset class="adminform" >
                        <ul class="panelform">
                            <?php foreach($this->form->getFieldset('publishing') as $field): ?>
                                <li><?php echo $field->label;echo $field->input;?></li>
                            <?php endforeach; ?>
                        </ul>
                </fieldset>

                <?php foreach ($params as $name => $fieldset): ?>
                        <?php echo JHtml::_('sliders.panel', JText::_($fieldset->label), $name.'-params');?>
                        <?php if (isset($fieldset->description) && trim($fieldset->description)): ?>
                            <p class="tip"><?php echo $this->escape(JText::_($fieldset->description));?></p>
                        <?php endif;?>
                        <fieldset class="panelform" >
                                <ul class="adminformlist">
                                        <?php foreach ($this->form->getFieldset($name) as $field) : ?>
                                                <li><?php echo $field->label; ?><?php echo $field->input; ?></li>
                                        <?php endforeach; ?>
                                </ul>
                        </fieldset>
                <?php endforeach; ?>

                <?php echo JHtml::_('sliders.end'); ?>

	</div>

        <div class="clr"> </div>

        <div class="width-100">
            <fieldset class="adminform">
                    <legend><?php echo JText::_('COM_HWDMS_PERMISSIONS'); ?></legend>
                    <ul class="adminformlist">
                        <li><?php echo $this->form->getLabel('rules'); ?>
                        <?php echo $this->form->getInput('rules'); ?></li>                      
                    </ul>
            </fieldset>
        </div>
        
	<div>
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>