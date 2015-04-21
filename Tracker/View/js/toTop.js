$(function() {
    // cache scroll to top button
    var b = $('#back-top');
    // Hide scroll top button
    b.hide();
    // FadeIn or FadeOut scroll to top button on scroll
    $(window).on('scroll', function(){
    // if you scroll more then 400px then fadein goto top button
        if ($(this).scrollTop() > 400) {
            b.fadeIn();
       // otherwise fadeout button
        } else {
            b.fadeOut();
        }
    });
  
    // Animated scroll to top
    b.on('click', function(){
        $('html,body').animate({
            scrollTop: 0
        }, 500 );
        return false;
    });
});
