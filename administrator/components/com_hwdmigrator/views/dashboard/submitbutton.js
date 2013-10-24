/**
 * @version    SVN $Id: submitbutton.js 481 2012-08-21 16:28:14Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

Joomla.submitbutton = function(task)
{
        if (task == 'maintenace.run')
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
                                var object = JSON.decode(response);
                                if (object['success'] == 1)
                                {
                                        var html = '<div class="jgrid"><span class="state publish"><span class="text">Success</span></span></div>';
                                }
                                else
                                {
                                        $('ajax-container-' + task).addClass('hasResult').set('title', object['data']['error_msg']);
                                        var html = '<div class="jgrid"><span class="state unpublish"><span class="text">Fail</span></span></div>';
                                        var JTooltips = new Tips($$('.hasResult'));
                                }                
                                $('ajax-container-' + object['data']['task']).removeClass('ajax-loading').set('html', html);
                        },
                        onFailure: function()
                        {
                                var html = '<div class="jgrid"><span class="state unpublish"><span class="text">Fail</span></span></div>';
                        }
                });

                for (var i = 0; i < tasks.length; i++)
                {
                        var task = tasks[i];
                        maintenanceRequest.send('option=com_hwdmigrator&task=migrate.run&format=raw&migrate=' + task);
                }
                return false;                
	}
	if (task == 'maintenace.refresh')
	{
		window.location = "index.php?option=com_hwdmigrator&view=dashboard";
                return false;
	}
	else if (task == 'help')
	{
                window.open ("http://hwdmediashare.co.uk/learn/docs", "helpWindow","status=1,toolbar=1");
                return false;
	}
        else
        {
                Joomla.submitform(task);
                return true;
        }


}