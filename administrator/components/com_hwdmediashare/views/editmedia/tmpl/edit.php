<?php
/**
 * @version    SVN $Id: edit.php 1516 2013-05-17 14:56:40Z dhorsfall $
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
$params = $this->form->getFieldsets('params');
$publishing = $this->form->getFieldsets('publishing');
JHtml::_('behavior.modal');
?>
<form action="<?php echo JRoute::_('index.php?option=com_hwdmediashare&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">

        <div class="width-60 fltlft">

                <fieldset class="adminform">
                        <legend><?php echo JText::_( 'COM_HWDMS_MEDIA_DETAILS' ); ?></legend>
                        <ul class="adminformlist">
                                <div class="fltrt">
                                <div class="button2-left">
                                <div class="blank">
                                <a class="modal" title="<?php echo JText::_( 'COM_HWDMS_UPDATE_MEDIA' ); ?>" href="<?php echo 'index.php?option=com_hwdmediashare&view=addmedia&id='.$this->item->id.'&tmpl=component'; ?>" rel="{handler: 'iframe', size: {x: 800, y: 500}}">
                                <?php echo JText::_( 'COM_HWDMS_UPDATE_MEDIA' ); ?>
                                </a>
                                </div>
                                </div>
                                </div>
                                <div class="fltrt">
                                <div class="button2-left">
                                <div class="blank">
                                <a class="modal" title="<?php echo JText::_( 'COM_HWDMS_VIEW_MEDIA' ); ?>" href="<?php echo 'index.php?option=com_hwdmediashare&task=editmedia.view&id='.$this->item->id.'&tmpl=component'; ?>" rel="{handler: 'iframe', size: {x: 800, y: 500}}">
                                <?php echo JText::_( 'COM_HWDMS_VIEW_MEDIA' ); ?>
                                </a>
                                </div>
                                </div>
                                </div>
                                <li><label for="jform_type"><?php echo JText::_( 'COM_HWDMS_TYPE' ); ?></label><input type="text" size="40" class="readonly" value="<?php echo $this->getType($this->item); ?>" id="jform_type"></li>  
                                <?php if ($this->item->type == 2 || $this->item->type == 7): ?>
                                    <li>
                                        <label for="jform_source"><?php echo JText::_( 'COM_HWDMS_MEDIA_SOURCE_LABEL' ); ?></label>
                                        <span class="readonly">
                                            <a title="<?php echo JText::_( 'COM_HWDMS_VIEW_ORIGINAL' ); ?>" href="<?php echo $this->item->source; ?>" target="_blank"><?php echo $this->item->source; ?></a>
                                        </span>
                                    </li>
                                <?php endif; ?>
                                <?php if ($this->item->type == 4): ?>
                                    <li>
                                        <label for="jform_streamer"><?php echo JText::_( 'COM_HWDMS_RTMP_STREAMER_LABEL' ); ?></label>
                                        <span class="readonly">
                                            <?php echo $this->item->streamer; ?></a>
                                        </span>
                                    </li>
                                    <li>
                                        <label for="jform_file"><?php echo JText::_( 'COM_HWDMS_RTMP_FILE_LABEL' ); ?></label>
                                        <span class="readonly">
                                            <?php echo $this->item->file; ?></a>
                                        </span>
                                    </li>
                                <?php endif; ?>
                                <?php if ($this->item->media_type > 0): ?>
                                    <li><label for="jform_type"><?php echo JText::_( 'COM_HWDMS_MEDIA_TYPE' ); ?></label><input type="text" size="40" class="readonly" value="<?php echo $this->getMediaType($this->item); ?>" id="jform_type"></li>                            
                                <?php endif; ?>
                                <?php foreach($this->form->getFieldset('details') as $field): ?>
                                        <?php if ($field->name == "jform[type]"): ?>
                                        <?php elseif ($field->name == "jform[media_type]"): ?>
                                            <?php if ($this->item->type == 3 || $this->item->type == 4): ?>
                                                <li><?php echo $field->label;echo $field->input;?></li>
                                            <?php endif; ?>
                                        <?php elseif ($field->name == "jform[description]"): ?>
                                            <div class="clr"></div>
                                            <li><?php echo $field->input; ?></li>
                                            <div class="clr"></div>
                                        <?php else: ?>
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
                    <fieldset class="adminform">
                             <ul class="adminformlist">
                                    <li>
                                            <img src="<?php echo JRoute::_(hwdMediaShareDownloads::thumbnail($this->item)); ?>" border="0" alt="<?php echo $this->escape($this->item->title); ?>" style="max-width:300px;" />
                                    </li>
                                    <?php if ($this->item->customthumbnail) : ?>
                                            <li><?php echo $this->form->getLabel('remove_thumbnail');echo $this->form->getInput('remove_thumbnail'); ?></li>
                                    <?php endif; ?>  
                                    <div class="clr"></div>
                             </ul>
                    </fieldset>

                    <fieldset class="adminform" >
                        <ul class="panelform">
                            <li><?php echo $this->form->getLabel('thumbnail');echo $this->form->getInput('thumbnail'); ?></li>
                        </ul>
                        <ul class="panelform">
                            <li><?php echo $this->form->getLabel('thumbnail_remote');echo $this->form->getInput('thumbnail_remote'); ?></li>
                        </ul>                        
                    </fieldset>
            
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

                <?php echo JHtml::_('sliders.panel', JText::_( 'COM_HWDMS_LINKED_ALBUMS' ), 'albums');?>
                        <fieldset class="panelform" >
                                <ul class="adminformlist">
                                        <li>
                                                <label title="<?php echo JText::_( 'COM_HWDMS_MANAGE_ALBUMS' ); ?>" class="hasTip" id="jform_managealbums-lbl"><?php echo JText::_( 'COM_HWDMS_ALBUMS' ); ?> (<?php echo $this->item->albumcount; ?>)</label>
                                                <div class="button2-left">
                                                <div class="blank">
                                                    <a rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=linkedalbums&tmpl=component&media_id='.(int) $this->item->id); ?>" title="<?php echo JText::_( 'COM_HWDMS_MANAGE_ALBUMS' ); ?>" class="modal">
                                                        <?php echo JText::_( 'COM_HWDMS_MANAGE_ALBUMS_LINKED_WITH_THIS_MEDIA' ); ?>
                                                    </a>
                                                </div>
                                                </div>
                                        </li>
                                </ul>
                        </fieldset>

                <?php echo JHtml::_('sliders.panel', JText::_( 'COM_HWDMS_LINKED_PLAYLISTS' ), 'playlists');?>
                        <fieldset class="panelform" >
                                <ul class="adminformlist">
                                        <li>
                                                <label title="<?php echo JText::_( 'COM_HWDMS_MANAGE_PLAYLISTS' ); ?>" class="hasTip" id="jform_manageplaylists-lbl"><?php echo JText::_( 'COM_HWDMS_PLAYLISTS' ); ?> (<?php echo $this->item->playlistcount; ?>)</label>
                                                <div class="button2-left">
                                                <div class="blank">
                                                    <a rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=linkedplaylists&tmpl=component&media_id='.(int) $this->item->id); ?>" title="<?php echo JText::_( 'COM_HWDMS_MANAGE_PLAYLISTS' ); ?>" class="modal">
                                                        <?php echo JText::_( 'COM_HWDMS_MANAGE_PLAYLISTS_LINKED_WITH_THIS_MEDIA' ); ?>
                                                    </a>
                                                </div>
                                                </div>
                                        </li>
                                </ul>
                        </fieldset>

                <?php echo JHtml::_('sliders.panel', JText::_( 'COM_HWDMS_LINKED_GROUPS' ), 'groups');?>
                        <fieldset class="panelform" >
                                <ul class="adminformlist">
                                        <li>
                                                <label title="<?php echo JText::_( 'COM_HWDMS_MANAGE_GROUPS' ); ?>" class="hasTip" id="jform_managegroups-lbl"><?php echo JText::_( 'COM_HWDMS_GROUPS' ); ?> (<?php echo $this->item->groupcount; ?>)</label>
                                                <div class="button2-left">
                                                <div class="blank">
                                                    <a rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=linkedgroups&tmpl=component&media_id='.(int) $this->item->id); ?>" title="<?php echo JText::_( 'COM_HWDMS_MANAGE_GROUPS' ); ?>" class="modal">
                                                        <?php echo JText::_( 'COM_HWDMS_MANAGE_GROUPS_LINKED_WITH_THIS_MEDIA' ); ?>
                                                    </a>
                                                </div>
                                                </div>
                                        </li>
                                </ul>
                        </fieldset>

                <?php echo JHtml::_('sliders.panel', JText::_( 'COM_HWDMS_LINKED_MEDIA' ), 'linked-media');?>
                        <fieldset class="panelform" >
                                <ul class="adminformlist">
                                        <li>
                                                <label title="<?php echo JText::_( 'COM_HWDMS_MANAGE_MEDIA' ); ?>" class="hasTip" id="jform_managemedia-lbl"><?php echo JText::_( 'COM_HWDMS_MEDIA' ); ?> (<?php echo $this->item->linkedmediacount; ?>)</label>
                                                <div class="button2-left">
                                                <div class="blank">
                                                    <a rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=linkedmedia&tmpl=component&media_id='.(int) $this->item->id); ?>" title="<?php echo JText::_( 'COM_HWDMS_MANAGE_MEDIA' ); ?>" class="modal">
                                                        <?php echo JText::_( 'COM_HWDMS_MANAGE_OTHER_MEDIA_LINKED_WITH_THIS_MEDIA' ); ?>
                                                    </a>
                                                </div>
                                                </div>
                                        </li>
                                </ul>
                        </fieldset>

                <?php echo JHtml::_('sliders.panel', JText::_( 'COM_HWDMS_LINKED_PAGES' ), 'linked-pages');?>
                        <fieldset class="panelform" >
                                <ul class="adminformlist">
                                        <li>
                                                <label title="<?php echo JText::_( 'COM_HWDMS_MANAGE_PAGES' ); ?>" class="hasTip" id="jform_managepages-lbl"><?php echo JText::_( 'COM_HWDMS_PAGES' ); ?> (<?php echo $this->item->linkedpagescount; ?>)</label>
                                                <div class="button2-left">
                                                <div class="blank">
                                                    <a rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=linkedpages&tmpl=component&media_id='.(int) $this->item->id); ?>" title="<?php echo JText::_( 'COM_HWDMS_MANAGE_PAGES' ); ?>" class="modal">
                                                        <?php echo JText::_( 'COM_HWDMS_MANAGE_PAGES_LINKED_WITH_THIS_MEDIA' ); ?>
                                                    </a>
                                                </div>
                                                </div>
                                        </li>
                                </ul>
                        </fieldset>

                <?php echo JHtml::_('sliders.panel', JText::_( 'COM_HWDMS_RESPONSES' ), 'responses');?>
                        <fieldset class="panelform" >
                                <ul class="adminformlist">
                                        <li>
                                                <label title="<?php echo JText::_( 'COM_HWDMS_MANAGE_RESPONSES' ); ?>" class="hasTip" id="jform_manageresponse-lbl">Response to...</label>
                                                <?php foreach($this->item->responds as $i => $response): ?>
                                                <div class="button2-left">
                                                <div class="blank">
                                                    <a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=editmedia.edit&id='.(int) $response->media_id); ?>" title="Title">
                                                        <?php echo $this->escape( $response->media_id ); ?>
                                                    </a>
                                                </div>
                                                </div>
                                                <?php endforeach; ?>
                                        </li>
                                        <li>
                                                <label title="Manage responses" class="hasTip" id="jform_manageresponses-lbl">Responses (<?php echo $this->item->responsescount; ?>)</label>
                                                <div class="button2-left">
                                                <div class="blank">
                                                    <a rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=linkedresponses&tmpl=component&media_id='.(int) $this->item->id); ?>" title="Manage responses" class="modal">
                                                        <?php echo JText::_( 'COM_HWDMS_MANAGE_RESPONSES' ); ?>
                                                    </a>
                                                </div>
                                                </div>
                                        </li>
                                </ul>
                        </fieldset>

		<?php echo JHtml::_('sliders.end'); ?>
                                
                <?php if (!empty($this->item->location)): ?>
                <fieldset class="adminform">
                        <legend><?php echo JText::_( 'COM_HWDMS_MAP' ); ?></legend>
                        <ul class="adminformlist">
                                <?php echo $this->item->map; ?>
                        </ul>
                </fieldset>
                <?php endif; ?>             
	</div>

        <div class="clr"> </div>

        <h1><?php echo JText::_( 'COM_HWDMS_MEDIA_FILES' ); ?></h1>
        <table class="adminlist">
            <?php echo $this->loadTemplate('files');?>
        </table>

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