/* [ZASO] Simple Accordion Template - Main JS */
(function($) {

  var allPanels = $('.zaso-simple-accordion > dd').hide();

  $('.zaso-simple-accordion > dt > a').click(function(event) {
    event.preventDefault();
    allPanels.slideUp();
    $(this).parent().next().slideDown();
    return false;
  });

})(jQuery);