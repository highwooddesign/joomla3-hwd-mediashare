/* CSS Document Copyright (C) 2015 Highwood Design Limited. All rights reserved.
/**********************************************************************************************/
jQuery(document).ready(function() {
  jQuery('.media-thumb').each(function() {
    jQuery(this).load(function() {
      var margin = jQuery(this).parents('.media-item').height()/2 - jQuery(this).height()/2;
      jQuery(this).css('margin-top', margin + 'px');
    });        
  })
});