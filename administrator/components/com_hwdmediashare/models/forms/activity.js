/**
 * @version    SVN $Id: activity.js 277 2012-03-28 10:03:31Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) ${date?date?string("yyyy")} Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      ${date} ${time}
 */

window.addEvent('domready', function() {
	document.formvalidator.setHandler('integer',
		function (value) {
			regex=/^[^0-9]+$/;
			return regex.test(value);
	});
});

