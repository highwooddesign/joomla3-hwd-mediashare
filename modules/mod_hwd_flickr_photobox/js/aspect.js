jQuery( document ).ready(function( $ ) {
    $('.media-thumb').each(function() {
        $(this).load(function() {
            var margin = $(this).parents('.media-item').height()/2 - $(this).height()/2;
            $(this).css('margin-top', margin + 'px');
        });        
    })
});
