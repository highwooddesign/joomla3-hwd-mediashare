Joomla.submitbutton = function(task)
{      
    if (task == "maintenance.run")
    {
        jQuery("[id^=ajax-container]").html("")
        post("cleancategorymap")
        post("emptyuploadtokens")
        post("purgeoldprocesses")
        post("uninstalloldextensions")
        post("databaseindexoptimisation")
    }
    else
    {
        Joomla.submitform(task);
        return true;
    }

    return false;
}

function post(task) {
    setTimeout(function() {
        jQuery("#ajax-container-" + task).html("").addClass("ajax-loading");

        var url = location.href;
        var posting = jQuery.ajax({
            async: false,
            url: url,
            data: 
            {
                    option: "com_hwdmediashare",
                    task: "maintenance.run",
                    format: "raw",
                    maintenance: task
            }
        });

        posting.done(function(data) {
            try {
                var results = jQuery.parseJSON(data);
                if (results.status == "success") {
                    jQuery("#ajax-container-" + task).empty();
                    jQuery("#ajax-container-" + task).removeClass('ajax-loading');
                    jQuery("#ajax-container-" + task).html('<span class="label label-success">Success</span>'); 
                } else {
                    jQuery("#ajax-container-" + task).empty();
                    jQuery("#ajax-container-" + task).removeClass('ajax-loading');
                    jQuery("#ajax-container-" + task).html('<span class="label label-danger">Failed</span>');
                }                
            }
            catch(err) {
                jQuery("#ajax-container-" + task).empty();
                jQuery("#ajax-container-" + task).removeClass('ajax-loading');
                jQuery("#ajax-container-" + task).html('<span class="label label-danger">Failed</span>');
            }
        });

        posting.fail(function(data) {
            $("#ajax-container-" + task).empty();
            $("#ajax-container-" + task).removeClass('ajax-loading');
            $("#ajax-container-" + task).html('<span class="label label-danger">Failed</span>');
        });
    }, 1);
}
