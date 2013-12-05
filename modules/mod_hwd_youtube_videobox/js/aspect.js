/**
 * @version    $Id: aspect.js 1250 2013-03-08 14:26:27Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) ${date?date?string("yyyy")} Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      ${date} ${time}
 */

/**
 * hwdms.aspect
 *
 * Function to vertically align thumbnails where the aspect has been forced
 */
window.addEvent('load', function () {
    var galleryImgs = $$('.media-thumb');
    if (galleryImgs.length > 0) galleryImgs.each(function(image) {
        var parent = image.getParent(".media-item");
        var container = parent.getSize().y/2;
        var margin = (container - (image.height/2));
        image.setStyle('margin-top', margin + 'px');	
    });     
});

