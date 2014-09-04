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

JHtml::_('behavior.modal');
?>
<form action="<?php echo JRoute::_('index.php?option=com_hwdmediashare'); ?>" method="post" name="adminForm" id="adminForm">
<div class="row-fluid">
<?php if (!empty($this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
                <div class="well well-small">
                        <h4><?php echo JText::_('COM_HWDMS_WELCOME_STATEMENT');?></h4>
                        <p>If you require support or help, just head on to the <a href="http://hwdmediashare.co.uk/forum/" target="_blank">forums</a> and browse through the <a href="http://hwdmediashare.co.uk/docs/" target="_blank">documentation</a>.</p>
                </div>                    
                <?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span6">
<?php else : ?>
	<div id="j-main-container" class="span8">
<?php endif;?> 
                <div class="well well-small">
                        <div class="module-title nav-header"><?php echo JText::_('COM_HWDMS_RECENT_ACTIVITY'); ?></div>
                        <div class="row-striped">
                                <?php if (!$this->activity): ?>
                                <div class="row-fluid">
                                        <div class="span12">
                                                <div class="alert"><?php echo JText::_('COM_HWDMS_MSG_NO_RECENT_ACTIVITY'); ?></div>
                                        </div>
                                </div>
                                <?php else: ?>  
                                        <?php foreach ($this->activity as $activity) : ?>
                                        <div class="row-fluid">
                                                <div class="span9">
                                                        <strong class="row-title"><?php echo hwdMediaShareActivities::renderActivityHtml($activity); ?> </strong>
                                                </div>
                                                <div class="span3">
                                                        <span title="" class="small"><i class="icon-calendar"></i> <?php echo JHtml::_('date.relative', $activity->created); ?></span>
                                                </div>
                                        </div>
                                        <?php endforeach; ?>
                                <?php endif; ?>                            
                        </div>
                </div>  
                <div class="well well-small">
                        <div class="module-title nav-header"><?php echo JText::_('COM_HWDMS_RECENTLY_ADDED_MEDIA'); ?></div>
                        <div class="row-striped">
                                <div class="row-fluid">
                                        <div class="span12">
                                                <script type="text/javascript" src="https://www.google.com/jsapi"></script>
                                                <script type="text/javascript">
                                                    google.load("visualization", "1", {packages:["corechart"]});
                                                    google.setOnLoadCallback(drawChart);
                                                    function drawChart() {
                                                        var data = new google.visualization.DataTable();
                                                        data.addColumn('date', '<?php echo JText::_('COM_HWDMS_DATE'); ?>');
                                                        data.addColumn('number', '<?php echo JText::_('COM_HWDMS_MEDIA'); ?>');
                                                        data.addRows([
                                                        <?php foreach($this->media as $i => $item): ?>
                                                            [new Date('<?php echo date("Y", strtotime($item->created)); ?>, <?php echo date("m", strtotime($item->created)); ?>, <?php echo date("d", strtotime($item->created)); ?>'), <?php echo $item->total; ?>],
                                                        <?php endforeach; ?>
                                                        ]);

                                                        var options = {
                                                            width: '100%',
                                                            height: 300,
                                                            backgroundColor: '#F9F9F9',                              
                                                            hAxis: {format:'MMM d',gridlines: {count: 4}}
                                                        };

                                                        var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
                                                        chart.draw(data, options);
                                                    }
                                                </script>
                                                <div id="chart_div" style="width: 100%; height: 300px;"></div>
                                        </div>
                                </div>
                        </div>          
                </div>    
	</div>
	<div class="span4">
                <div class="well well-small">
                        <div class="module-title nav-header"><?php echo JText::_('COM_HWDMS_QUICK_LINKS'); ?></div>
                        <div class="row-striped">
                                <div class="row-fluid">
                                        <div class="span12">
                                                <a href="index.php?option=com_hwdmediashare&view=media"><i class="icon-play"></i> <span><?php echo JText::sprintf('COM_HWDMS_MEDIA_COUNTN', $this->nummedia); ?></span></a>
                                        </div>
                                </div>
                                <div class="row-fluid">
                                        <div class="span12">
                                                <a href="index.php?option=com_hwdmediashare&view=addmedia"><i class="icon-plus"></i> <span><?php echo JText::_('COM_HWDMS_ADD_NEW_MEDIA'); ?></span></a>
                                        </div>
                                </div>
                                <div class="row-fluid">
                                        <div class="span12">
                                                <a href="index.php?option=com_categories&extension=com_hwdmediashare"><i class="icon-folder"></i> <span><?php echo JText::sprintf('COM_HWDMS_CATEGORIES_COUNTN', $this->numcategories); ?></span></a>
                                        </div>
                                </div>
                                <div class="row-fluid">
                                        <div class="span12">
                                                <a href="index.php?option=com_hwdmediashare&view=albums"><i class="icon-book"></i> <span><?php echo JText::sprintf('COM_HWDMS_ALBUMS_COUNTN', $this->numalbums); ?></span></a>
                                        </div>
                                </div>
                                <div class="row-fluid">
                                        <div class="span12">
                                                <a href="index.php?option=com_hwdmediashare&view=groups"><i class="icon-users"></i> <span><?php echo JText::sprintf('COM_HWDMS_GROUPS_COUNTN', $this->numgroups); ?></span></a>
                                        </div>
                                </div>
                                <div class="row-fluid">
                                        <div class="span12">
                                                <a href="index.php?option=com_hwdmediashare&view=playlists"><i class="icon-list"></i> <span><?php echo JText::sprintf('COM_HWDMS_PLAYLISTS_COUNTN', $this->numplaylists); ?></span></a>
                                        </div>
                                </div>
                                <div class="row-fluid">
                                        <div class="span12">
                                                <a href="index.php?option=com_hwdmediashare&view=users"><i class="icon-user"></i> <span><?php echo JText::sprintf('COM_HWDMS_CHANNELS_COUNTN', $this->numchannels); ?></span></a>
                                        </div>
                                </div>
                                <div class="row-fluid">
                                        <div class="span12">
                                                <a href="index.php?option=com_hwdmediashare&view=activities"><i class="icon-grid"></i> <span><?php echo JText::_('COM_HWDMS_ACTIVITIES'); ?></span></a>
                                        </div>
                                </div>
                                <div class="row-fluid">
                                        <div class="span12">
                                                <a href="index.php?option=com_hwdmediashare&view=files"><i class="icon-file"></i> <span><?php echo JText::_('COM_HWDMS_FILES'); ?></span></a>
                                        </div>
                                </div>
                                <div class="row-fluid">
                                        <div class="span12">
                                                <a href="index.php?option=com_hwdmediashare&view=maintenance"><i class="icon-cog"></i> <span><?php echo JText::_('COM_HWDMS_MAINTENANCE'); ?></span></a>
                                        </div>
                                </div>
                                <div class="row-fluid">
                                        <div class="span12">
                                                <a href="index.php?option=com_hwdmediashare&view=reported"><i class="icon-notification"></i> <span><?php echo JText::_('COM_HWDMS_REPORTED'); ?></span></a>
                                        </div>
                                </div>
                                <div class="row-fluid">
                                        <div class="span12">
                                                <a href="index.php?option=com_hwdmediashare&view=pending"><i class="icon-notification"></i> <span><?php echo JText::_('COM_HWDMS_PENDING'); ?></span></a>
                                        </div>
                                </div>
                                <div class="row-fluid">
                                        <div class="span12">
                                                <a href="index.php?option=com_hwdmediashare&view=processes="><i class="icon-cog"></i> <span><?php echo JText::_('COM_HWDMS_PROCESSOR'); ?></span></a>
                                        </div>
                                </div>
                                <div class="row-fluid">
                                        <div class="span12">
                                                <a href="index.php?option=com_hwdmediashare&view=configuration"><i class="icon-cog"></i> <span><?php echo JText::_('COM_HWDMS_CONFIGURATION'); ?></span></a>
                                        </div>
                                </div>
                                <div class="row-fluid">
                                        <div class="span12">
                                                <a href="index.php?option=com_tags"><i class="icon-tag"></i> <span><?php echo JText::_('COM_HWDMS_TAGS'); ?></span></a>
                                        </div>
                                </div>
                                <div class="row-fluid">
                                        <div class="span12">
                                                <a href="index.php?option=com_hwdmediashare&view=subscriptions"><i class="icon-users"></i> <span><?php echo JText::_('COM_HWDMS_SUBSCRIPTIONS'); ?></span></a>
                                        </div>
                                </div>
                                <div class="row-fluid">
                                        <div class="span12">
                                                <a href="index.php?option=com_hwdmediashare&view=extensions"><i class="icon-file-2"></i> <span><?php echo JText::_('COM_HWDMS_FILE_EXTENSIONS'); ?></span></a>
                                        </div>
                                </div>
                                <div class="row-fluid">
                                        <div class="span12">
                                                <a href="index.php?option=com_hwdmediashare&view=customfields"><i class="icon-checkmark-circle"></i> <span><?php echo JText::_('COM_HWDMS_CUSTOM_FIELDS'); ?></span></a>
                                        </div>
                                </div>
                                <div class="row-fluid">
                                        <div class="span12">
                                                <a href="http://hwdmediashare.co.uk/about-hwdmediashare?version=<?php echo $this->version; ?>" target="_blank"><i class="icon-info"></i> <span><?php echo JText::_('COM_HWDMS_ABOUT'); ?></span></a>
                                        </div>
                                </div>
                                <div class="row-fluid">
                                        <div class="span12">
                                                <a href="http://hwdmediashare.co.uk/docs" target="_blank"><i class="icon-help"></i> <span><?php echo JText::_('COM_HWDMS_HELP'); ?></span></a>
                                        </div>
                                </div>
                        </div>
                </div>
	</div>
</div>
<input type="hidden" name="task" value="" />
</form>