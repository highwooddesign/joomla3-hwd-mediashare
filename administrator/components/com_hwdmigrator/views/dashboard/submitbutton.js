Joomla.submitbutton = function(task)
{      
        if (task == 'maintenance.run')
	{
                var tasks = new Array();
                tasks[0]="videoitems";
                tasks[1]="videocategories";
                tasks[2]="videogroups";
                tasks[3]="videoplaylists";
                tasks[4]="photoitems";
                tasks[5]="photocategories";
                tasks[6]="photogroups";
                tasks[7]="photoalbums";
                tasks[8]="matchvideocategories";
                tasks[9]="matchvideotags";
                tasks[10]="matchvideogroups";
                tasks[11]="matchvideoplaylists";
                tasks[12]="matchphotocategories";
                tasks[13]="matchphototags";
                tasks[14]="matchphotogroups";
                tasks[15]="matchphotoalbums";

                var maintenanceRequest = new Request({
                        url: 'index.php',
                        method: 'post',
                        link: 'chain',
                        async: false,
                        onRequest: function()
                        {
                                $('ajax-container-' + task).empty().addClass('ajax-loading');
                        },
                        onComplete: function( response )
                        {
                                try {
                                        var object = JSON.decode(response);
                                } catch(e) {
                                        $('ajax-container-' + task).empty();
                                        $('ajax-container-' + task).removeClass('ajax-loading');
                                        $('ajax-container-' + task).set('html', '<span class="label label-danger">Failed</span>');
                                }

                                try {
                                        if (object['status'] == 'success')
                                        {
                                                $('ajax-container-' + task).empty();
                                                $('ajax-container-' + task).removeClass('ajax-loading');
                                                $('ajax-container-' + task).set('html', '<span class="label label-success">Success</span>');                                    
                                        }
                                        else
                                        {
                                                $('ajax-container-' + task).empty();
                                                $('ajax-container-' + task).removeClass('ajax-loading');
                                                $('ajax-container-' + task).set('html', '<span class="label label-danger">Failed</span>'); 
                                        }
                                } catch(e) {
                                        $('ajax-container-' + task).empty();
                                        $('ajax-container-' + task).removeClass('ajax-loading');
                                        $('ajax-container-' + task).set('html', '<span class="label label-danger">Failed</span>');
                                }                                    
                        },
                        onFailure: function()
                        {
                                $('ajax-container-' + task).empty();
                                $('ajax-container-' + task).removeClass('ajax-loading');
                                $('ajax-container-' + task).set('html', '<span class="label label-danger">Failed</span>');
                        }
                });

                for (var i = 0; i < tasks.length; i++)
                {
                        var task = tasks[i];
                        maintenanceRequest.send('option=com_hwdmigrator&task=migrate.run&format=raw&migrate=' + task);
                }
                
	}
	else if (task == 'maintenance.refresh')
	{
		window.location = "index.php?option=com_hwdmigrator&view=dashboard";
                return false;
	}
        else
        {
                Joomla.submitform(task);
                return true;
        }
        
        return false;
}
