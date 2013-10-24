/**
 * @version    SVN $Id: submitbutton.js 290 2012-04-02 08:45:51Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

Joomla.submitbutton = function(task)
{
        if (task == 'process.run')
	{
                stub = 'cb';
                cid  = '';
                if ($('adminForm')) {
                        var c = 0;
                        for (var i = 0, n = $('adminForm').elements.length; i < n; i++) {
                                var e = $('adminForm').elements[i];
                                if (e.type == 'checkbox') {
                                        if (e.checked) {
                                                cid += '&cid[]=' + e.value;
                                        }
                                }
                        }
                }

                SqueezeBox.resize({x: 500, y: 400});
                SqueezeBox.setContent( 'iframe', 'index.php?option=com_hwdmediashare&task=process.run&tmpl=component' + cid );
                return false;
	}
	if (task == 'process.all')
	{
		SqueezeBox.resize({x: 500, y: 400})
                SqueezeBox.setContent( 'iframe', 'index.php?option=com_hwdmediashare&task=process.run&tmpl=component' );
                return false;
	}
	else if (task == 'help')
	{
                SqueezeBox.resize({x:560,y:315},true);
                SqueezeBox.setContent( 'iframe', 'http://www.youtube.com/embed/85SoLcO8v80' );
                return false;
	}
        else
        {
                Joomla.submitform(task);
                return true;
        }
}