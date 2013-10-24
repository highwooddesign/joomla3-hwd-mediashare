/**
 * @version    SVN $Id: submitbutton.js 277 2012-03-28 10:03:31Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

Joomla.submitbutton = function(task)
{
        var tasks = new Array();
        tasks[0]="cleancategorymap";
        tasks[1]="cleantagmap";
        tasks[2]="emptyuploadtokens";

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
                maintenanceRequest.send('option=com_hwdmediashare&task=maintenance.run&format=raw&maintenance=' + task);
        }
}