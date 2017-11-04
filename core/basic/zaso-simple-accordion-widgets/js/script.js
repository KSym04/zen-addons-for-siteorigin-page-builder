/* [ZASO] Simple Accordion Template - Main JS */

(function($) {

  // hide all panel by default
  var allPanels = $('.zaso-simple-accordion > dd').hide();

  // open state
  $('.zaso-simple-accordion__content').each(function(index, el) {
    if ( $(this).hasClass('zaso-simple-accordion--open') ) {
      $(this).slideDown();
    }
  });

  // event click
  $('.zaso-simple-accordion__title').click(function(event) {

    event.preventDefault();
    var currentContent = $(this).next();

    if ( currentContent.hasClass('zaso-simple-accordion--open') ) {
      currentContent.slideUp().removeClass('zaso-simple-accordion--open');
    } else {
      currentContent.slideDown().addClass('zaso-simple-accordion--open');
    }

    return false;

  });

})(jQuery);