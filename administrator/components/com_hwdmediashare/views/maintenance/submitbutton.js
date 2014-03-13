Joomla.submitbutton = function(task)
{
        var tasks = new Array();
        tasks[0]="cleancategorymap";
        tasks[1]="cleantagmap";
        tasks[2]="emptyuploadtokens";
        tasks[3]="purgeoldprocesses";

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
                                if (object['success'] == 1)
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
                maintenanceRequest.send('option=com_hwdmediashare&task=maintenance.run&format=raw&maintenance=' + task);
        }
}