jQuery(document).ready(function() {
    var scParent = jQuery("div.hwd-soundcloud");
    var scParentWidth = scParent.width();
    jQuery('head').append('<style type="text/css">\n\
.hwd-soundcloud div.jp-interface > div {width: ' + parseInt(scParentWidth-124) + 'px;}\n\
.hwd-soundcloud div.jp-interface div.jp-progress {width: ' + parseInt(scParentWidth-189) + 'px;}\n\
.hwd-soundcloud div.jp-interface div.jp-time-holder {width: ' + parseInt(scParentWidth-124) + 'px;}\n\
.hwd-soundcloud div.jp-playlist ul li.jp-playlist-current div {width: ' + parseInt(scParentWidth-124) + 'px;}\n\
</style>');
});
