<?php
/**
 * @version    SVN $Id: default.php 492 2012-08-24 15:11:58Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.modal');
?>
<form action="<?php echo JRoute::_('index.php?option=com_hwdmediashare'); ?>" method="post" name="adminForm" id="adminForm">
<table width="100%" border="0">
	<tr>
		<td width="55%" valign="top">
			<div id="cpanel">
				<?php echo $this->addIcon('icon-48-media.png','index.php?option=com_hwdmediashare&view=media', JText::sprintf('COM_HWDMS_MEDIA_COUNTN', $this->nummedia));?>
				<?php echo $this->addIcon('icon-48-add.png','index.php?option=com_hwdmediashare&view=addmedia', JText::_('COM_HWDMS_ADD_NEW_MEDIA'));?>
				<?php echo $this->addIcon('icon-48-category.png','index.php?option=com_categories&extension=com_hwdmediashare', JText::sprintf('COM_HWDMS_CATEGORIES_COUNTN', $this->numcategories));?>
				<?php echo $this->addIcon('icon-48-album.png','index.php?option=com_hwdmediashare&view=albums', JText::sprintf('COM_HWDMS_ALBUMS_COUNTN', $this->numalbums));?>
				<?php echo $this->addIcon('icon-48-groups.png','index.php?option=com_hwdmediashare&view=groups', JText::sprintf('COM_HWDMS_GROUPS_COUNTN', $this->numgroups));?>
				<?php echo $this->addIcon('icon-48-channels.png','index.php?option=com_hwdmediashare&view=users', JText::sprintf('COM_HWDMS_USER_CHANNELS_COUNTN', $this->numchannels));?>
				<?php echo $this->addIcon('icon-48-playlist.png','index.php?option=com_hwdmediashare&view=playlists', JText::sprintf('COM_HWDMS_PLAYLISTS_COUNTN', $this->numplaylists));?>
				<?php echo $this->addIcon('icon-48-activities.png','index.php?option=com_hwdmediashare&view=activities', JText::_('COM_HWDMS_ACTIVITIES'));?>
				<?php echo $this->addIcon('icon-48-files.png','index.php?option=com_hwdmediashare&view=files', JText::_('COM_HWDMS_FILES'));?>
				<?php echo $this->addIcon('icon-48-maintenance.png','index.php?option=com_hwdmediashare&view=maintenance', JText::_('COM_HWDMS_MAINTENANCE'));?>
				<?php echo $this->addIcon('icon-48-reported.png','index.php?option=com_hwdmediashare&view=reported&tmpl=component', JText::_('COM_HWDMS_REPORTED'), false, true);?>
				<?php echo $this->addIcon('icon-48-pending.png','index.php?option=com_hwdmediashare&view=pending&tmpl=component', JText::_('COM_HWDMS_PENDING'), false, true);?>
				<?php echo $this->addIcon('icon-48-process.png','index.php?option=com_hwdmediashare&view=processes', JText::_('COM_HWDMS_PROCESSOR'));?>
				<?php echo $this->addIcon('icon-48-config.png','index.php?option=com_hwdmediashare&view=configuration', JText::_('COM_HWDMS_CONFIGURATION'));?>
				<?php echo $this->addIcon('icon-48-tag.png','index.php?option=com_hwdmediashare&view=tags', JText::_('COM_HWDMS_TAGS'));?>
				<?php echo $this->addIcon('icon-48-subscription.png','index.php?option=com_hwdmediashare&view=subscriptions', JText::_('COM_HWDMS_SUBSCRIPTIONS'));?>
				<?php echo $this->addIcon('icon-48-extensions.png','index.php?option=com_hwdmediashare&view=extensions', JText::_('COM_HWDMS_FILE_EXTENSIONS'));?>
				<?php echo $this->addIcon('icon-48-field.png','index.php?option=com_hwdmediashare&view=customfields', JText::_('COM_HWDMS_CUSTOM_FIELDS'));?>
				<?php echo $this->addIcon('icon-48-info.png','http://hwdmediashare.co.uk/about-hwdmediashare?version='.$this->version, JText::_('COM_HWDMS_ABOUT'), true );?>
				<?php echo $this->addIcon('icon-48-help.png','http://hwdmediashare.co.uk/docs', JText::_('COM_HWDMS_HELP'), true ); ?>
			</div>
		</td>
		<td width="45%" valign="top">
			<?php echo JHtml::_('sliders.start', 'stat-pane'); ?>
			<?php echo JHtml::_('sliders.panel', JText::_('COM_HWDMS_WELCOME_TO_HWDMEDIASHARE'), 'welcome'); ?>
			<table class="adminlist">
				<tr>
					<td>
                                                <div style="font-weight:700;">
							<p><?php echo JText::_('COM_HWDMS_WELCOME_STATEMENT');?></p>
						</div>
						<p>
							If you require support just head on to the forums at
							<a href="http://hwdmediashare.co.uk/forum/" target="_blank">http://hwdmediashare.co.uk/forum</a>.
							For developers, you can browse through the documentation at
							<a href="http://hwdmediashare.co.uk/docs/" target="_blank">http://hwdmediashare.co.uk/docs</a>.
						</p>
					</td>
				</tr>
			</table>
			<?php echo JHtml::_('sliders.end'); ?>
                        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
                        <script type="text/javascript">
                            google.load("visualization", "1", {packages:["corechart"]});
                            google.setOnLoadCallback(drawChart);
                            function drawChart() {
                                var data = new google.visualization.DataTable();
                                data.addColumn('date', '<?php echo JText::_( 'COM_HWDMS_DATE' ); ?>');
                                data.addColumn('number', '<?php echo JText::_( 'COM_HWDMS_MEDIA' ); ?>');
                                data.addRows([
                                <?php foreach($this->media as $i => $item): ?>
                                    [new Date('<?php echo date("Y", strtotime($item->created)); ?>, <?php echo date("m", strtotime($item->created)); ?>, <?php echo date("d", strtotime($item->created)); ?>'), <?php echo $item->total; ?>],
                                <?php endforeach; ?>
                                ]);

                                var options = {
                                    title: '<?php echo JText::_( 'COM_HWDMS_RECENTLY_ADDED_MEDIA' ); ?>',
                                    hAxis: {format:'MMM d',gridlines: {count: 4}}
                                };

                                var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
                                chart.draw(data, options);
                            }
                        </script>
                        <div id="chart_div" style="width: 100%; height: 300px;"></div>
                </td>
	</tr>
</table>
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="1" />
</form>