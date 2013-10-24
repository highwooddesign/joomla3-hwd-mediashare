/**
 * @version    $Id: hwd.js 1718 2013-10-17 15:12:33Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      ${date} ${time}
 */

/**
 * Set jQuery to noConflict mode
 */
if (typeof jQuery != 'undefined') {
     jQuery.noConflict();
 }
/**
 * Define slideshow key if missing
 */
if (!key) {
    var key = 0;
}
/**
 * hwdms.mapreload
 *
 * Function to reload map on tab selection
 */
window.addEvent('domready', function () {
    if (document.getElementById('paneMap')) {
        document.getElementById('paneMap').addEvent('click', function()
        {
            setTimeout("onLoadmap()",100);
        });
    }
});
/**
 * hwdms.carousel
 *
 * Function to setup the slideshow 
 */
window.addEvent('domready', function () {

        var duration = 300,

        div = document.getElement('div.media-slideshow-container')

        if (div) {
            links = div.getElements('a'),

            carousel = new Carousel.Extra({
                activeClass: 'selected',
                autostart: true,
                container: 'slide',
                scroll: 5, // @TODO: Need to test this, and maybe keep the value low
                circular: true,
                current: key,
                previous: links.shift(),
                next: links.pop(),
                tabs: links,
                distance: 1,
                /* mode: 'horizontal', */
                fx: {

                    duration: duration
                }
            }), removed = 0;

            function change() {

                var panel = this.retrieve('panel');

                if (this.checked) {

                    if (!panel) {

                        if (carousel.running) {

                            carousel.addEvent('complete:once', change.bind(this));
                            return
                        }

                        panel = carousel.remove(Math.max(0, this.value - removed));

                        if (panel) {

                            this.store('panel', panel);
                            removed++;
                        }

                        this.checked = !! panel
                    }

                } else {

                    if (panel) {

                        this.eliminate('panel');
                        removed--;
                        carousel.add(panel.panel, panel.tab.inject(div.getFirst(), 'after'), this.value)
                    }
                }
            }

            $$('input.remove').addEvents({
                click: change,
                change: change
            })
        }

    var mediaSlideshowContainer = document.getElementById('media-slideshow-container');
    var mediaSlideshowTab = document.getElementById('slideshow-tab');
    if (mediaSlideshowContainer && mediaSlideshowTab) {
        var status = {
            'true': 'Hide',
            'false': 'Show'
        };

        // -- vertical
        var myVerticalSlide = new Fx.Slide('media-slideshow-container');

        document.getElementById('slideshow-tab').addEvent('click', function (event) {
            event.stop();
            myVerticalSlide.toggle();
            setHeight();
        });

        // When Vertical Slide ends its transition, we check for its status
        // note that complete will not affect 'hide' and 'show' methods
        myVerticalSlide.addEvent('complete', function () {
            document.getElementById('slideshow-status').set('text', status[myVerticalSlide.open]);
        });

        loadMedia(key);
        setHeight();
    }
})
/**
 * hwdms.load.slideshow.media
 *
 * Function to load media into slideshow during load
 */
function loadMedia(id) {
    if (document.getElementById('image-slideshow-' + id)) {
        var rel = document.getElementById('image-slideshow-' + id).getAttribute("rel");
        var mediaObject = JSON.decode(rel);
        var position = mediaObject.position;

        document.getElementById('media-item').empty().addClass('ajax-loading-slideshow');
        document.getElementById('slide').getElements(".highlighted").removeClass("highlighted");
        document.getElementById('image-slideshow-' + id).addClass('highlighted');
        document.getElementById('current-position').set('html', position);
        document.getElementById('current-title').set('html', mediaObject['title']);

        var a = new Request({
            url: 'index.php?option=com_hwdmediashare&task=get.html&format=raw&id=' + mediaObject['id'],
            method: 'get',
            onComplete: function (response) {
                document.getElementById('media-item').removeClass('ajax-loading-slideshow').set('html', response);
            }
        }).send();

        setHeight();
    }
}
/**
 * hwdms.set.height
 *
 * Function to set height for slideshow media
 */
function setHeight() {
    var mediaSlideshowToggle = document.getElementById('media-slideshow-toggle');
    if (mediaSlideshowToggle) {
        var parentHeight = document.body.getSize().y;
        var toggleHeight = document.getElementById('media-slideshow-toggle').getSize().y;
        var mediaHeight = parentHeight - toggleHeight - 30;
        var height = mediaHeight + 'px';
        document.getElementById('media-item').setStyle('height', height);
    }
}
/**
 * hwdms.audio.playlist
 *
 * Function to load audio and video tracks
 */
window.addEvent('domready', function () {
        $$('a.media-audio-playlist-play').each(function(el){
                el.addEvent('click',function(e){
                        e.stop();
                        var rel = document.getElementById(this.get('id')).get('rel');
                        var object = JSON.decode(rel);
                        var playerId = object.playerId;
                        var playerContainer = object.playerContainer;
$$('div.mejs-pause').fireEvent('click');

                        $(this.get('id')).addClass('active');

                        var targetUrl = hwdms_live_site;

                        var a = new Request.HTML({
                            url: targetUrl,
                            method: 'post',
                            evalResponse: true,
                            data: 'option=com_hwdmediashare&task=get.html&id=' + object.id + '&format=raw&autoplay=1',
                            update: object.playerContainer,
                            onComplete: function (response) {
                            },
                            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                            }
                        }).send();
                });
        });
        $$('a.media-video-playlist-play').each(function(el){
                el.addEvent('click',function(e){
                        e.stop();
                        var rel = document.getElementById(this.get('id')).get('rel');
                        var object = JSON.decode(rel);
                        var playerId = object.playerId;
                        var playerContainer = object.playerContainer;

                        $(this.get('id')).addClass('active');

                        var targetUrl = hwdms_live_site;

                        var a = new Request.HTML({
                            url: targetUrl,
                            method: 'post',
                            evalResponse: true,
                            data: 'option=com_hwdmediashare&task=get.html&id=' + object.id + '&format=raw&autoplay=1&mediaitem_size=' + object.width,
                            update: object.playerContainer,
                            onComplete: function (response) {
                            },
                            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                            }
                        }).send();
                });
        });
});
/*
 * hwdms.ajax.functions
 *
 * If passed as a function, it is treated as callback function
 * that gets executed by 'success' event.
 * If passed as an object, it is treated as ajax settings.
 */
window.addEvent('domready', function () {
if (document.getElementById('media-subscribe')) {
    document.getElementById('media-subscribe').addEvent('click', function (event) {
            event.preventDefault();
            document.getElementById('media-subscribe').empty().addClass('ajax-loading-button');

            var targetUrl = hwdms_live_site;
            var href = document.getElementById('media-subscribe-form').get('action');
            var query = href.parseQueryString();
            
            var a = new Request({
                url: targetUrl,
                method: 'post',
                data: 'option=com_hwdmediashare&task=get.subscribe&id='+query.id+'&format=raw',
                onComplete: function (response) {
                    var error = false;
                    try {
                        var object = JSON.decode(response);

                        if (typeof object.errors === 'undefined' || object.errors > 0) {
                            var error = true;
                        }

                        if (error) {
                            document.getElementById('media-subscribe').removeClass('ajax-loading-button').addClass('ajax-error').set('value', object.data.error_msg);
                        } else {
                            document.getElementById('media-subscribe').removeClass('ajax-loading-button');
                            document.getElementById('media-subscribe').set('styles', {display: 'none'});
                            document.getElementById('media-unsubscribe').set('styles', {display: 'inline'});
                        }
                    } catch(error) {
                        document.getElementById('media-subscribe').removeClass('ajax-loading-button').addClass('ajax-error').set('value', 'error');
                    }
                }
            }).send();
        })
}
if (document.getElementById('media-unsubscribe')) {
        document.getElementById('media-unsubscribe').addEvent('click', function (event) {
            event.preventDefault();
            document.getElementById('media-unsubscribe').empty().addClass('ajax-loading-button');

            var targetUrl = hwdms_live_site;
            var href = document.getElementById('media-subscribe-form').get('action');
            var query = href.parseQueryString();
            
            var a = new Request({
                url: targetUrl,
                method: 'post',
                data: 'option=com_hwdmediashare&task=get.unsubscribe&id='+query.id+'&format=raw',
                onComplete: function (response) {
                    var error = false;
                    try {
                        var object = JSON.decode(response);

                        if (typeof object.errors === 'undefined' || object.errors > 0) {
                            var error = true;
                        }

                        if (error) {
                            document.getElementById('media-unsubscribe').removeClass('ajax-loading-button').addClass('ajax-error').set('value', object.data.error_msg);
                        } else {
                            document.getElementById('media-unsubscribe').removeClass('ajax-loading-button');
                            document.getElementById('media-unsubscribe').set('styles', {display: 'none'});
                            document.getElementById('media-subscribe').set('styles', {display: 'inline'});
                        }
                    } catch(error) {
                        document.getElementById('media-unsubscribe').removeClass('ajax-loading-button').addClass('ajax-error').set('value', 'error');
                    }
                }
            }).send();
        })
}
if (document.getElementById('media-like')) {
        document.getElementById('media-like').addEvent('click', function (event) {
            event.preventDefault();
            document.getElementById('media-like-link').set('html', '');
            document.getElementById('media-like-link').addClass('ajax-loading');

            var targetUrl = hwdms_live_site;
            var href = document.getElementById('media-like-link').get('href');

            // Split link so first variable can be parsed
            var hrefSplit = href.split('?');
            if (!hrefSplit[1]) { // No query
            event.stop();
            }
            var href = hrefSplit[1];
            var query = href.parseQueryString();

            var a = new Request({
                url: targetUrl,
                method: 'post',
                data: 'option=com_hwdmediashare&task=get.like&id='+query.id+'&format=raw',
                onComplete: function (response) {
                    var error = false;
                    try {
                        var object = JSON.decode(response);

                        if (typeof object.errors === 'undefined' || object.errors > 0) {
                            var error = true;
                        }

                        if (error) {
                            document.getElementById('media-like-link').removeClass('ajax-loading').addClass('ajax-error').set('text', object.data.error_msg);
                        } else {
                            document.getElementById('media-like-link').removeClass('ajax-loading').addClass('ajax-success').set('text', object.data.success_msg);
                            if (object.data.likes) {
                                document.getElementById('media-likes').set('text', object.data.likes);
                            }
                        }
                    } catch(error) {
                        document.getElementById('media-like-link').removeClass('ajax-loading').addClass('ajax-error').set('text', 'error');
                    }
                }
            }).send();
        })
}
if (document.getElementById('media-dislike')) {
        document.getElementById('media-dislike').addEvent('click', function (event) {
            event.preventDefault();
            document.getElementById('media-dislike-link').set('html', '');
            document.getElementById('media-dislike-link').addClass('ajax-loading');

            var targetUrl = hwdms_live_site;
            var href = document.getElementById('media-like-link').get('href');
            
            // Split link so first variable can be parsed
            var hrefSplit = href.split('?');
            if (!hrefSplit[1]) { // No query
            event.stop();
            }
            var href = hrefSplit[1];
            var query = href.parseQueryString();

            var a = new Request({
                url: targetUrl,
                method: 'post',
                data: 'option=com_hwdmediashare&task=get.dislike&id='+query.id+'&format=raw',
                onComplete: function (response) {
                    var error = false;
                    try {
                        var object = JSON.decode(response);

                        if (typeof object.errors === 'undefined' || object.errors > 0) {
                            var error = true;
                        }

                        if (error) {
                            document.getElementById('media-dislike-link').removeClass('ajax-loading').addClass('ajax-error').set('text', object.data.error_msg);
                        } else {
                            document.getElementById('media-dislike-link').removeClass('ajax-loading').addClass('ajax-success').set('text', object.data.success_msg);
                            if (object.data.dislikes) {
                                document.getElementById('media-dislikes').set('text', object.data.dislikes);
                            }
                        }
                    } catch(error) {
                        document.getElementById('media-dislike-link').removeClass('ajax-loading').addClass('ajax-error').set('text', 'error');
                    }
                }
            }).send();
        })
}
if (document.getElementById('media-fav')) {
        document.getElementById('media-fav').addEvent('click', function (event) {
            event.preventDefault();
            document.getElementById('media-fav-link').empty().removeProperty('class').addClass('ajax-loading');

            var targetUrl = hwdms_live_site;
            var href = document.getElementById('media-fav-link').get('href');
            var hrefURI = new URI(href);
            var rawQuery = hrefURI.get('query');
            var query = rawQuery.parseQueryString();
            var compound = query.task.split('.');

            var a = new Request({
                url: targetUrl,
                method: 'post',
                data: 'option=com_hwdmediashare&task=get.'+compound[1]+'&id='+query.id+'&format=raw',
                onComplete: function (response) {
                    var error = false;
                    try {
                        var object = JSON.decode(response);

                        if (typeof object.errors === 'undefined' || object.errors > 0) {
                            var error = true;
                        }

                        if (error) {
                            document.getElementById('media-fav-link').removeClass('ajax-loading').addClass('ajax-error').set('text', object.data.error_msg);
                        } else {
                            if (compound[1] == 'unfavour')
                            {
                                document.getElementById('media-fav-link').removeClass('ajax-loading').removeClass('media-favadd-link').addClass('ajax-success media-fav-link').set('text', object.data.success_msg).set('href', hwdms_live_site + 'index.php?option=com_hwdmediashare&task=mediaitem.favour&id='+query.id+'&return='+query['return']+'&tmpl='+query.tmpl+'&Itemid='+query.Itemid);
                            }
                            else
                            {
                                document.getElementById('media-fav-link').removeClass('ajax-loading').removeClass('media-fav-link').addClass('ajax-success media-favadd-link').set('text', object.data.success_msg).set('href', hwdms_live_site + 'index.php?option=com_hwdmediashare&task=mediaitem.unfavour&id='+query.id+'&return='+query['return']+'&tmpl='+query.tmpl+'&Itemid='+query.Itemid);
                            }
                        }
                    } catch(error) {
                        document.getElementById('media-fav-link').removeClass('ajax-loading').addClass('ajax-error').set('text', 'error');
                    }
                }
            }).send();
        })
}
if (document.getElementById('media-favadd')) {
        document.getElementById('media-favadd').addEvent('click', function (event) {
            event.preventDefault();
            document.getElementById('media-favadd-link').empty().removeProperty('class').addClass('ajax-loading');

            var targetUrl = hwdms_live_site;
            var href = document.getElementById('media-favadd-link').get('href');
            var hrefURI = new URI(href);
            var rawQuery = hrefURI.get('query');
            var query = rawQuery.parseQueryString();
            var compound = query.task.split('.');
            
            var a = new Request({
                url: targetUrl,
                method: 'post',
                data: 'option=com_hwdmediashare&task=get.'+compound[1]+'&id='+query.id+'&format=raw',
                onComplete: function (response) {
                    var error = false;
                    try {
                        var object = JSON.decode(response);

                        if (typeof object.errors === 'undefined' || object.errors > 0) {
                            var error = true;
                        }

                        if (error) {
                            document.getElementById('media-favadd-link').removeClass('ajax-loading').addClass('ajax-error').set('text', object.data.error_msg);
                        } else {
                            if (compound[1] == 'unfavour')
                            {
                                document.getElementById('media-favadd-link').removeClass('ajax-loading').removeClass('media-favadd-link').addClass('ajax-success media-fav-link').set('text', object.data.success_msg).set('href', hwdms_live_site + 'index.php?option=com_hwdmediashare&task=mediaitem.favour&id='+query.id+'&return='+query['return']+'&tmpl='+query.tmpl+'&Itemid='+query.Itemid);
                            }
                            else
                            {
                                document.getElementById('media-favadd-link').removeClass('ajax-loading').removeClass('media-fav-link').addClass('ajax-success media-favadd-link').set('text', object.data.success_msg).set('href', hwdms_live_site + 'index.php?option=com_hwdmediashare&task=mediaitem.unfavour&id='+query.id+'&return='+query['return']+'&tmpl='+query.tmpl+'&Itemid='+query.Itemid);
                            }
                        }
                    } catch(error) {
                        document.getElementById('media-favadd-link').removeClass('ajax-loading').addClass('ajax-error').set('text', 'error');
                    }
                }
            }).send();
        })
}
});