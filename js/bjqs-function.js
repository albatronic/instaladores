jQuery(document).ready(function($) {

  $('#banner-fade').bjqs({
    height      : 221,
    width       : 636,
    responsive  : true,

    // control and marker configuration
            showcontrols    : false,     // enable/disable next + previous UI elements
            showmarkers     : true,     // enable/disable individual slide UI markers
    
    usecaptions     : false,     // enable/disable captions using img title attribute
    animtype        : 'fade',
  });

});