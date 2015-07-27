jQuery(document).ready(function(){
    var HWD = function () {
        var errorMsg = "An error has occured";

        function post(event) {
            event.preventDefault();
            jQuery(event.data.selector).attr("disabled", true);

            var task = jQuery(event.data.selector).attr("data-media-task");  
            var id = jQuery(event.data.selector).attr("data-media-id");  
            var token = jQuery(event.data.selector).attr("data-media-token");  
            var url = hwdms_live_site;

            var data = {};
            data["option"] = "com_hwdmediashare";
            data["task"] = "get." + task;
            data["id"] = id;
            data["format"] = "json";
            data[token] = "1";

            // Send the data using post.
            var posting = jQuery.post(url, data);

            posting.done(function(data) {
                try {
                    var results = jQuery.parseJSON(data);
                    if (results.status == "success") {
                        switch (task) {
                            case "favourite":
                                jQuery(event.data.selector).attr("disabled", false);
                                jQuery(event.data.selector).attr("data-media-task", "unfavourite");
                                jQuery(event.data.selector).addClass("active");
                                jQuery(event.data.selector + " i:first-child").addClass("red"); 
                                break;
                            case "unfavourite":
                                jQuery(event.data.selector).attr("disabled", false);
                                jQuery(event.data.selector).attr("data-media-task", "favourite");
                                jQuery(event.data.selector).removeClass("active");
                                jQuery(event.data.selector + " i:first-child").removeClass("red"); 
                                break;
                            case "subscribe":
                                jQuery(event.data.selector).attr("disabled", false);
                                jQuery(event.data.selector).attr("data-media-task", "unsubscribe");
                                jQuery(event.data.selector).addClass("active");
                                jQuery(event.data.selector).html('<i class="icon-checkmark"></i> ' + hwdms_text_subscribed);
                                break;
                            case "unsubscribe":
                                jQuery(event.data.selector).attr("disabled", false);
                                jQuery(event.data.selector).attr("data-media-task", "subscribe");
                                jQuery(event.data.selector).removeClass("active");
                                jQuery(event.data.selector).html('<i class="icon-user"></i> ' + hwdms_text_subscribe);
                                break;                                         
                            case "like":
                                jQuery(event.data.selector).addClass("active");
                                var likes = parseInt(jQuery("#media-likes").html()) + 1;
                                var dislikes = parseInt(jQuery("#media-dislikes").html());
                                var percent = parseInt((likes * 100) / (likes + dislikes));
                                jQuery("#media-likes").html(likes);
                                jQuery("#percentbar-active").css({"width": percent + "%"});
                                break;
                            case "dislike":
                                jQuery(event.data.selector).addClass("active");
                                var likes = parseInt(jQuery("#media-likes").html());
                                var dislikes = parseInt(jQuery("#media-dislikes").html()) + 1;
                                var percent = parseInt((likes * 100) / (likes + dislikes));
                                jQuery("#media-dislikes").html(dislikes);
                                jQuery("#percentbar-active").css({"width": percent + "%"});
                                break;                                    
                        }
                    } else {
                        HWD.popup(results.message);
                        jQuery(event.data.selector).attr("disabled", false);
                    }                
                }
                catch(err) {
                    HWD.popup(hwdms_text_error_occured);
                    jQuery(event.data.selector).attr("disabled", false);
                }
            });

            posting.fail(function(data) {
                HWD.popup(hwdms_text_error_occured);
                jQuery(event.data.selector).attr("disabled", false);
            });
        }

        function popup(msg) {
            jQuery.magnificPopup.open({
                items: {
                    src: jQuery('<div>' + msg + '</div>') // Dynamically created element.
                },
                type: 'inline',
                preloader: false,
                closeOnBgClick: true,
                mainClass: 'mfp-alert',
                closeOnContentClick: true,
                removalDelay: 0
            });

        }

        // Reveal public pointers to private functions and properties.
        return {
            popup: popup,
            post: post
        };
    };

    var HWD = new HWD();

    // Bind handlers to button click events.
    jQuery("#media-favourite-btn").click({selector: "#media-favourite-btn"}, HWD.post);
    jQuery("#media-subscribe-btn").click({selector: "#media-subscribe-btn"}, HWD.post);
    jQuery("#media-like-btn").click({selector: "#media-like-btn"}, HWD.post);
    jQuery("#media-dislike-btn").click({selector: "#media-dislike-btn"}, HWD.post);

    // Look for file selector inputs.
    if (jQuery('.hwd-form-filedata').length) { jQuery('.hwd-form-filedata').bootstrapFileInput(); }
    
    // Convert radio inputs into working buttons.
    if (jQuery('#hwd-container .radio.btn-group').length) { 
        jQuery('#hwd-container .radio.btn-group label').addClass('btn');
        jQuery("#hwd-container .btn-group label:not(.active)").click(function()
        {
                var label = jQuery(this);
                var input = jQuery('#' + label.attr('for'));

                if (!input.prop('checked')) {
                        label.closest('.btn-group').find("label").removeClass('active btn-success btn-danger btn-primary');
                        if (input.val() == '') {
                                label.addClass('active btn-primary');
                        } else if (input.val() == 0) {
                                label.addClass('active btn-danger');
                        } else {
                                label.addClass('active btn-success');
                        }
                        input.prop('checked', true);
                }
        });
        jQuery("#hwd-container .btn-group input[checked=checked]").each(function()
        {
                if (jQuery(this).val() == '') {
                        jQuery("label[for=" + jQuery(this).attr('id') + "]").addClass('active btn-primary');
                } else if (jQuery(this).val() == 0) {
                        jQuery("label[for=" + jQuery(this).attr('id') + "]").addClass('active btn-danger');
                } else {
                        jQuery("label[for=" + jQuery(this).attr('id') + "]").addClass('active btn-success');
                }
        });
    }
    
    // Keyboard navigation for media.
    jQuery(document).on("keydown", function(e) {
        jQuery("#hwd-container div.media-item-navigation a.prev").on("click", function() {
            window.location =  jQuery("#hwd-container div.media-item-navigation a.prev").attr("href");
        }); 
        jQuery("#hwd-container div.media-item-navigation a.next").on("click", function() {
            window.location =  jQuery("#hwd-container div.media-item-navigation a.next").attr("href");
        });   
        if (jQuery(e.target.nodeName).is('body')) {
            switch(e.which) {
                case 37: // left
                    jQuery("#hwd-container div.media-item-navigation a.prev").trigger("click");
                break;
                case 39: // right
                    jQuery("#hwd-container div.media-item-navigation a.next").trigger("click");
                break;
                default: return; // exit this handler for other keys
            }

            e.preventDefault(); // prevent the default action (scroll / move caret)
        }
    });
});
                    
//http://www.sanwebe.com/2013/03/addremove-input-fields-dynamically-with-jquery
jQuery(document).ready(function() {
    var max_fields      = 10; //maximum input boxes allowed
    var wrapper         = jQuery('.stream_fields'); //Fields wrapper
    var add_button      = jQuery('.add_field_button'); //Add button ID

    var x = 1; //initlal text box count
    jQuery(add_button).click(function(e){ //on add input button click
        e.preventDefault();
        if(x < max_fields){ //max input box allowed
            x++; //text box increment
            
    var fields_html      = '<div>' +
'<div class="control-group">' +
    '<div class="control-label">' +
            '<label id="jform_source_type_' + x + '-lbl" for="jform_source_type_' + x + '">Source Type</label>' +
    '</div>' +
    '<div class="controls">' +
            '<select id="jform_source_type_' + x + '" name="jform[source_type][' + x + ']">' +
                    '<option value="1">RTMP Stream</option>' +
                    '<option value="2">HLS Stream</option>' +
                    '<option value="3">MP4 Fallback</option>' +                                         
            '</select>' +                          
    '</div>' +
'</div>' +
'<div class="control-group">' +
    '<div class="control-label">' +
            '<label id="jform_source_quality_' + x + '-lbl" for="jform_source_quality_' + x + '">Quality</label>' +
    '</div>' +
    '<div class="controls">' +
            '<select id="jform_source_quality_' + x + '" name="jform[source_quality][' + x + ']">' +
                    '<option value="240">240p</option>' +
                    '<option value="360">360p</option>' +
                    '<option value="480">480p</option>' +
                    '<option value="720">720p</option>' +
                    '<option value="1080">1080p</option>' +
            '</select>' +
    '</div>' +
'</div>' +
'<div class="control-group">' +
    '<div class="control-label">' +
            '<label id="jform_source_url_' + x + '-lbl" for="jform_source_url_' + x + '" class="required">Source File</label>' +
    '</div>' +
    '<div class="controls">' +
            '<input type="text" name="jform[source_url][' + x + ']" id="jform_source_url_' + x + '" value="" size="40" />' +
    '</div>' +
'</div>' +
'<a href="#" class="remove_field btn btn-info">Remove</a>' +
'<hr />' +
'</div>';

            jQuery(wrapper).append(fields_html); //add input box
            jQuery('.stream_fields select').chosen();
        }
    });
   
    jQuery(wrapper).on('click','.remove_field', function(e){ //user click on remove text
        e.preventDefault(); jQuery(this).parent('div').remove(); x--;
    })
});