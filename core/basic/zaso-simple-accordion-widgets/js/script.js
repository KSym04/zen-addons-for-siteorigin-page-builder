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
    var currentTitle = $(this);
    var currentContent = $(this).next();
    var currentAccordionBox = $(this).parent();

    // Add class on click title.
    currentTitle.addClass('activate').siblings('.zaso-simple-accordion__title').removeClass('activate');

    if ( currentContent.hasClass('zaso-simple-accordion--open') ) {
      currentContent.slideUp().removeClass('zaso-simple-accordion--open');
      currentTitle.removeClass('activate');
    } else {
      currentContent.slideDown().addClass('zaso-simple-accordion--open');
    }
    
    // If setting set to single open only.
    if ( currentAccordionBox.hasClass('single_open') ) {
        $(this).siblings('.zaso-simple-accordion__title').next().slideUp().removeClass('zaso-simple-accordion--open');
    }

    return false;

  });

})(jQuery);