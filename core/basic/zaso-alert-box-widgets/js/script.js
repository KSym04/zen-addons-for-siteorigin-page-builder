/* [ZASO] Alert Box Template - Main JS */

(function ($) {

    var closeBtn = jQuery('.zaso-alert-box__closebtn');
    closeBtn.on('click', function(event) {
        event.preventDefault();
        jQuery(this).parents('.zaso-alert-box').fadeOut();
    });

})(jQuery);