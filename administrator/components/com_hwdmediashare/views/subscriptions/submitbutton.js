/**
 * @version    SVN $Id: submitbutton.js 290 2012-04-02 08:45:51Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      07-Nov-2011 11:38:52
 */

Joomla.submitbutton = function(task)
{
	if (task == 'help')
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