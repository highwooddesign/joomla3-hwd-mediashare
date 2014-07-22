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
JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');
JHtml::_('behavior.tabstate');
JHtml::_('formbehavior.chosen', 'select');

$app = JFactory::getApplication();
$input = $app->input;
$isNew = $this->item->id == 0 ? true : false ; 
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'editmedia.cancel' || document.formvalidator.isValid(document.id('item-form')))
		{
			<?php echo $this->form->getField('description')->save(); ?>
			Joomla.submitform(task, document.getElementById('item-form'));
		}
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=editmedia&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate" enctype="multipart/form-data">

        <div class="btn-group pull-right">                         
                <a class="btn btn-success modal" href="<?php echo 'index.php?option=com_hwdmediashare&view=addmedia&id='.$this->item->id.'&tmpl=component'; ?>" rel="{handler: 'iframe', size: {x: 800, y: 500}}" title="<?php echo JText::_('COM_HWDMS_UPDATE_MEDIA'); ?>"><span class="icon-upload"></span> <?php echo JText::_('COM_HWDMS_UPDATE_MEDIA'); ?></a>
                <?php echo JHtml::_('HwdPopup.link', $this->item, '<span class="icon-search"></span> ' . JText::_('COM_HWDMS_VIEW_MEDIA'), array('class' => 'btn btn-primary')); ?>
        </div>  

	<?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>

	<div class="form-horizontal">
            
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_HWDMS_MEDIA_DETAILS', true)); ?>
		<div class="row-fluid">
			<div class="span9">
				<fieldset class="adminform">
					<?php echo $this->form->getInput('description'); ?>
				</fieldset>
			</div>
			<div class="span3">
                            <div class="row-striped">
                                <div class="row-fluid">
                                    <div class="span5"><strong class="row-title"><?php echo JText::_('JGRID_HEADING_ID'); ?></strong></div>
                                    <div class="span6"><span class="badge"><?php echo $this->form->getValue('id'); ?></span></div>
                                </div>
                                <div class="row-fluid">
                                    <div class="span5"><strong class="row-title"><?php echo JText::_('COM_HWDMS_TYPE'); ?></strong></div>
                                    <div class="span6"><span class="badge"><?php echo hwdMediaShareMedia::getType($this->item); ?></span></div>
                                </div>
                                <?php if ($this->item->media_type > 0): ?>                                
                                <div class="row-fluid">
                                    <div class="span5"><strong class="row-title"><?php echo JText::_('COM_HWDMS_MEDIA_TYPE'); ?></strong></div>
                                    <div class="span6"><span class="badge"><?php echo hwdMediaShareMedia::getMediaType($this->item); ?></span></div>
                                </div>                                 
                                <?php endif; ?>
                                <?php if ($this->item->type == 2 || $this->item->type == 7): ?>
                                <div class="row-fluid">
                                    <div class="span12"><strong class="row-title"><?php echo JText::_('COM_HWDMS_MEDIA_SOURCE_LABEL'); ?></strong>
                                                  <br /><a title="<?php echo JText::_('COM_HWDMS_VIEW_ORIGINAL'); ?>" href="<?php echo $this->item->source; ?>" target="_blank"><?php echo $this->item->source; ?></a></div>
                                </div>  
                                <?php endif; ?>
                                <?php if ($this->item->type == 4): ?>
                                <div class="row-fluid">
                                    <div class="span12"><strong class="row-title"><?php echo JText::_('COM_HWDMS_RTMP_STREAMER_LABEL'); ?></strong>
                                                  <br /><span class="badge"><?php echo $this->item->streamer; ?></span></div>
                                </div>  
                                <div class="row-fluid">
                                    <div class="span12"><strong class="row-title"><?php echo JText::_('COM_HWDMS_RTMP_FILE_LABEL'); ?></strong>
                                                  <br /><span class="badge"><?php echo $this->item->file; ?></span></div>
                                </div>
                                <?php endif; ?>
                                <div class="row-fluid">
                                    <div class="span12">
                                        <?php echo JHtml::_('HwdPopup.link', $this->item, '<img src="' . JRoute::_(hwdMediaShareDownloads::thumbnail($this->item)) . '" border="0" alt="' . $this->escape($this->item->title) . '" style="max-width:218px;" />'); ?>                                      
                                    </div>
                                </div>
                            </div>
			</div>                  
			<div class="span3">
				<?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

                <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('COM_HWDMS_PUBLISHING', true)); ?>
                        <div class="row-fluid form-horizontal-desktop">
                                <div class="span6">
                                        <?php echo JLayoutHelper::render('joomla.edit.publishingdata', $this); ?>
                                </div>
                                <div class="span6">
                                        <div class="control-group">
                                                <div class="control-label">
                                                        <?php echo $this->form->getLabel('status'); ?>
                                                </div>
                                                <div class="controls">
                                                        <?php echo $this->form->getInput('status'); ?>
                                                </div>
                                        </div> 
                                        <div class="control-group">
                                                <div class="control-label">
                                                        <?php echo $this->form->getLabel('download'); ?>
                                                </div>
                                                <div class="controls">
                                                        <?php echo $this->form->getInput('download'); ?>
                                                </div>
                                        </div> 
                                        <div class="control-group">
                                                <div class="control-label">
                                                        <?php echo $this->form->getLabel('private'); ?>
                                                </div>
                                                <div class="controls">
                                                        <?php echo $this->form->getInput('private'); ?>
                                                </div>
                                        </div>
                                        <div class="control-group">
                                                <div class="control-label">
                                                        <?php echo $this->form->getLabel('likes'); ?>
                                                </div>
                                                <div class="controls">
                                                        <?php echo $this->form->getInput('likes'); ?>
                                                </div>
                                        </div>                                
                                        <div class="control-group">
                                                <div class="control-label">
                                                        <?php echo $this->form->getLabel('dislikes'); ?>
                                                </div>
                                                <div class="controls">
                                                        <?php echo $this->form->getInput('dislikes'); ?>
                                                </div>
                                        </div>   
                                        <div class="control-group">
                                                <div class="control-label">
                                                        <?php echo $this->form->getLabel('location'); ?>
                                                </div>
                                                <div class="controls">
                                                        <?php echo $this->form->getInput('location'); ?>
                                                </div>
                                        </div>   
                                        <div class="control-group">
                                                <div class="control-label">
                                                        <?php echo $this->form->getLabel('duration'); ?>
                                                </div>
                                                <div class="controls">
                                                        <?php echo $this->form->getInput('duration'); ?>
                                                </div>
                                        </div> 
                                </div>
                        </div>            
                <?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JLayoutHelper::render('joomla.edit.params', $this); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'thumbnail', JText::_('COM_HWDMS_CUSTOM_THUMBNAIL', true)); ?>
                        <div class="pull-right">
                                <img src="<?php echo JRoute::_(hwdMediaShareDownloads::thumbnail($this->item)); ?>" border="0" alt="<?php echo $this->escape($this->item->title); ?>" style="max-width:300px;" />
                        </div>
                        <div class="pull-left">
                                <?php if ($this->item->customthumbnail) : ?>
                                <div class="control-group">
                                        <div class="control-label">
                                                <?php echo $this->form->getLabel('remove_thumbnail'); ?>
                                        </div>
                                        <div class="controls">
                                                <?php echo $this->form->getInput('remove_thumbnail'); ?>
                                        </div>
                                </div>
                                <?php endif; ?>  
                                <div class="control-group">
                                        <div class="control-label">
                                                <?php echo $this->form->getLabel('thumbnail'); ?>
                                        </div>
                                        <div class="controls">
                                                <?php echo $this->form->getInput('thumbnail'); ?>
                                        </div>
                                </div>
                                <div class="control-group">
                                        <div class="control-label">
                                                <?php echo $this->form->getLabel('thumbnail_remote'); ?>
                                        </div>
                                        <div class="controls">
                                                <?php echo $this->form->getInput('thumbnail_remote'); ?>
                                        </div>
                                </div>
                        </div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
            
                <?php if (!$isNew) : ?>            
                <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'associations', JText::_('JGLOBAL_FIELDSET_ASSOCIATIONS', true)); ?>
                <div class="row-fluid">
                    <div class="well well-small">
                        <h2 class="module-title nav-header"><?php echo JText::_('COM_HWDMS_MEDIA_ASSOCIATED_WITH_FOLLOWING_ITEMS'); ?></h2>
                        <div class="row-striped">
                            <div class="row-fluid">
                                <div class="span9">
                                    <span class="badge"><?php echo $this->item->numalbums; ?></span>
                                    <strong class="row-title">
                                        <a rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=linkedalbums&tmpl=component&media_id='.(int) $this->item->id); ?>" title="<?php echo JText::_('COM_HWDMS_MANAGE_ALBUMS'); ?>" class="modal">
                                            <?php echo JText::_('COM_HWDMS_ALBUMS'); ?>
                                        </a>
                                    </strong>
                                </div>
                                <div class="span3">
                                    <div class="btn-group">
                                        <a rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=linkedalbums&tmpl=component&media_id='.(int) $this->item->id.'&add=0'); ?>" title="<?php echo JText::_('COM_HWDMS_MANAGE_ALBUMS'); ?>" class="modal btn"><i class="icon-cog"></i> <?php echo JText::_('COM_HWDMS_MANAGE'); ?></a>
                                        <a rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=linkedalbums&tmpl=component&media_id='.(int) $this->item->id.'&add=1'); ?>" title="<?php echo JText::_('COM_HWDMS_ADD_TO_ALBUMS'); ?>" class="modal btn"><i class="icon-new"></i> <?php echo JText::_('COM_HWDMS_ADD_TO_ALBUMS'); ?></a>
                                    </div>                                       
                                </div>
                            </div>
                            <div class="row-fluid">
                                <div class="span9">
                                    <span class="badge"><?php echo $this->item->numplaylists; ?></span>
                                    <strong class="row-title">
                                        <a rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=linkedplaylists&tmpl=component&media_id='.(int) $this->item->id); ?>" title="<?php echo JText::_('COM_HWDMS_MANAGE_PLAYLISTS'); ?>" class="modal">
                                            <?php echo JText::_('COM_HWDMS_PLAYLISTS'); ?>
                                        </a>
                                    </strong>
                                </div>
                                <div class="span3">
                                    <div class="btn-group">
                                        <a rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=linkedplaylists&tmpl=component&media_id='.(int) $this->item->id.'&add=0'); ?>" title="<?php echo JText::_('COM_HWDMS_MANAGE_PLAYLISTS'); ?>" class="modal btn"><i class="icon-cog"></i> <?php echo JText::_('COM_HWDMS_MANAGE'); ?></a>
                                        <a rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=linkedplaylists&tmpl=component&media_id='.(int) $this->item->id.'&add=1'); ?>" title="<?php echo JText::_('COM_HWDMS_ADD_TO_PLAYLISTS'); ?>" class="modal btn"><i class="icon-new"></i> <?php echo JText::_('COM_HWDMS_ADD_TO_PLAYLISTS'); ?></a>
                                    </div>                                       
                                </div>
                            </div>                            
                            <div class="row-fluid">
                                <div class="span9">
                                    <span class="badge"><?php echo $this->item->numgroups; ?></span>
                                    <strong class="row-title">
                                        <a rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=linkedgroups&tmpl=component&media_id='.(int) $this->item->id); ?>" title="<?php echo JText::_('COM_HWDMS_MANAGE_GROUPS'); ?>" class="modal">
                                            <?php echo JText::_('COM_HWDMS_GROUPS'); ?>
                                        </a>
                                    </strong>
                                </div>
                                <div class="span3">
                                    <div class="btn-group">
                                        <a rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=linkedgroups&tmpl=component&media_id='.(int) $this->item->id.'&add=0'); ?>" title="<?php echo JText::_('COM_HWDMS_MANAGE_GROUPS'); ?>" class="modal btn"><i class="icon-cog"></i> <?php echo JText::_('COM_HWDMS_MANAGE'); ?></a>
                                        <a rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=linkedgroups&tmpl=component&media_id='.(int) $this->item->id.'&add=1'); ?>" title="<?php echo JText::_('COM_HWDMS_ADD_TO_GROUPS'); ?>" class="modal btn"><i class="icon-new"></i> <?php echo JText::_('COM_HWDMS_ADD_TO_GROUPS'); ?></a>
                                    </div>                                       
                                </div>
                            </div>
                            <div class="row-fluid">
                                <div class="span9">
                                    <span class="badge"><?php echo $this->item->numlinkedmedia; ?></span>
                                    <strong class="row-title">
                                        <a rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=linkedmedia&tmpl=component&media_id='.(int) $this->item->id); ?>" title="<?php echo JText::_('COM_HWDMS_MANAGE_MEDIA'); ?>" class="modal">
                                            <?php echo JText::_('COM_HWDMS_MEDIA'); ?>
                                        </a>
                                    </strong>
                                </div>
                                <div class="span3">
                                    <div class="btn-group">
                                        <a rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=linkedmedia&tmpl=component&media_id='.(int) $this->item->id.'&add=0'); ?>" title="<?php echo JText::_('COM_HWDMS_MANAGE_MEDIA'); ?>" class="modal btn"><i class="icon-cog"></i> <?php echo JText::_('COM_HWDMS_MANAGE'); ?></a>
                                        <a rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=linkedmedia&tmpl=component&media_id='.(int) $this->item->id.'&add=1'); ?>" title="<?php echo JText::_('COM_HWDMS_LINK_WITH_MEDIA'); ?>" class="modal btn"><i class="icon-new"></i> <?php echo JText::_('COM_HWDMS_LINK_WITH_MEDIA'); ?></a>
                                    </div>                                       
                                </div>
                            </div>
                            <!--
                            <div class="row-fluid">
                                <div class="span9">
                                    <span title="" class="badge badge- hasTooltip" data-original-title="Hits"><?php echo $this->item->numlinkedpages; ?></span>
                                    <strong class="row-title">
                                        <a rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=linkedpages&tmpl=component&media_id='.(int) $this->item->id); ?>" title="<?php echo JText::_('COM_HWDMS_MANAGE'); ?>" class="modal">
                                            <?php echo JText::_('COM_HWDMS_LINKED_PAGES'); ?>
                                        </a>
                                    </strong>
                                </div>
                                <div class="span3">
                                    <span class="small"><i class="icon-cog"></i> 
                                        <a rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=linkedpages&tmpl=component&media_id='.(int) $this->item->id); ?>" title="<?php echo JText::_('COM_HWDMS_MANAGE'); ?>" class="modal">
                                            <?php echo JText::_('COM_HWDMS_MANAGE'); ?>
                                        </a>
                                    </span>
                                </div>
                            </div>
                            -->                            
                        </div>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="well well-small">
                        <h2 class="module-title nav-header"><?php echo JText::_('COM_HWDMS_RESPONSES_TO_THIS_MEDIA'); ?></h2>
                        <div class="row-striped">
                            <div class="row-fluid">
                                <div class="span9">
                                    <span class="badge"><?php echo $this->item->numresponses; ?></span>
                                    <strong class="row-title">
                                        <a rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=linkedresponses&tmpl=component&media_id='.(int) $this->item->id); ?>" title="<?php echo JText::_('COM_HWDMS_MANAGE_RESPONSES'); ?>" class="modal">
                                            <?php echo JText::_('COM_HWDMS_RESPONSES'); ?>
                                        </a>
                                    </strong>
                                </div>
                                <div class="span3">
                                    <div class="btn-group">
                                        <a rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=linkedresponses&tmpl=component&media_id='.(int) $this->item->id.'&add=0'); ?>" title="<?php echo JText::_('COM_HWDMS_MANAGE_RESPONSES'); ?>" class="modal btn"><i class="icon-cog"></i> <?php echo JText::_('COM_HWDMS_MANAGE'); ?></a>
                                        <a rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=linkedresponses&tmpl=component&media_id='.(int) $this->item->id.'&add=1'); ?>" title="<?php echo JText::_('COM_HWDMS_ADD_RESPONSES'); ?>" class="modal btn"><i class="icon-new"></i> <?php echo JText::_('COM_HWDMS_ADD_RESPONSES'); ?></a>
                                    </div>                                       
                                </div>
                            </div>                            
                        </div>
                    </div>
                </div> 
                <?php echo JHtml::_('bootstrap.endTab'); ?>
                <?php endif; ?>

                <?php foreach($this->item->customfields->fields as $group => $fields) : ?>
                        <?php echo JHtml::_('bootstrap.addTab', 'myTab', $group, JText::_($group, true)); ?>
                                <?php foreach($fields as $field) : ?>
                                        <div class="control-group">
                                                <div class="control-label">
                                                        <?php echo $this->item->customfields->getLabel($field); ?>
                                                </div>
                                                <div class="controls">
                                                        <?php echo $this->item->customfields->getInput($field); ?>
                                                </div>
                                        </div>
                                <?php endforeach; ?>
                        <?php echo JHtml::_('bootstrap.endTab'); ?>
                <?php endforeach; ?>
                        
                <?php if (!empty($this->item->location)): ?>
                        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'maps', JText::_('COM_HWDMS_MAP', true)); ?>
                                <?php echo $this->item->map; ?>
                        <?php echo JHtml::_('bootstrap.endTab'); ?>
                <?php endif; ?>   
                        
                <?php if (count($this->item->mediafiles)): ?>
                    <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'files', JText::_('COM_HWDMS_MEDIA_FILES', true)); ?>
                        <table class="adminlist">
                            <?php echo $this->loadTemplate('files');?>
                        </table>
                    <?php echo JHtml::_('bootstrap.endTab'); ?> 
                <?php endif; ?>   

                <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'permissions', JText::_('COM_HWDMS_PERMISSIONS', true)); ?>
                        <?php echo $this->form->getInput('rules'); ?>
                <?php echo JHtml::_('bootstrap.endTab'); ?>
            
		<?php echo JHtml::_('bootstrap.endTabSet'); ?>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="return" value="<?php echo $input->get('return', null, 'base64'); ?>" />
		<?php echo JHtml::_('form.token'); ?>
        </div>
</form>
