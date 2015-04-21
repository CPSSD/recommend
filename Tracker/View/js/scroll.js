$(document).ready(function() {
// Infinite Ajax Scroll configuration
    jQuery.ias({
        container : '.show_container', // main container where data goes to append
        item: '.image', // single items
        pagination: '.navigation', // page navigation
        next: '.navigation a', // next page selector
        loader: '<img src="css/ajax-loader.gif">', // loading gif
        triggerPageThreshold: 3 // show load more if scroll more than this
     });
});
