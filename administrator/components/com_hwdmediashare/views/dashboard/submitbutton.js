/**
 * @version    SVN $Id: submitbutton.js 315 2012-04-11 12:21:43Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      16-Oct-2011 10:45:45
 */

Joomla.submitbutton = function(task)
{
	if (task == '')
	{
		return false;
	}
	else if (task == 'help')
	{
                SqueezeBox.resize({x:560,y:315}, true);
                SqueezeBox.setContent( 'iframe', 'http://www.youtube.com/embed/85SoLcO8v80' );
                return false;
	}
        else
        {
                Joomla.submitform(task);
                return true;
        }
}

