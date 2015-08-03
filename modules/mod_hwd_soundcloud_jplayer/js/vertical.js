jQuery(document).ready(function() {
    var scParent = jQuery("div.hwd-soundcloud");
    var scParentWidth = scParent.width();
    jQuery('head').append('<style type="text/css">\n\
.hwd-soundcloud div.jp-interface > div {width: ' + parseInt(scParentWidth) + 'px;}\n\
.hwd-soundcloud div.jp-interface div.jp-time-holder {width: ' + parseInt(scParentWidth) + 'px;}\n\
.hwd-soundcloud div.jp-interface div.jp-time-holder div.jp-progress {width: ' + parseInt(scParentWidth-62) + 'px;}\n\
</style>');
});
