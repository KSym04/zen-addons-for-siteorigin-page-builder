/* [ZASO] Hover Card Template - Main JS */

(function ($) {

    var zasoHoverCardBackground;
    var zasoHCBackgroundPlacement;
    jQuery('.zaso-hover-card .zaso-hover-card__media').each(function(){
        zasoHCBackgroundPlacement = jQuery(this).parent();
        zasoHoverCardBackground = jQuery(this).find('img').attr('src');
        zasoHCBackgroundPlacement.css( 'background', 'url('+zasoHoverCardBackground+') no-repeat center center' );
        zasoHCBackgroundPlacement.css( 'background-size', 'cover' );
    });

    // Mouse convenience: clicking anywhere on the card follows the action link.
    // Keyboard users operate the real, focusable .zaso-hover-card__modal-action
    // link directly (the modal reveals on :focus-within), so this is a
    // progressive enhancement for pointer users only.
    jQuery('.zaso-hover-card').on('click', function( e ){
        if ( jQuery( e.target ).closest('.zaso-hover-card__modal-action').length ) {
            return; // Let the real link handle its own activation.
        }
        var zasoHCActionHref = jQuery(this).find('.zaso-hover-card__modal-action').attr('href');
        if ( zasoHCActionHref ) {
            window.location = zasoHCActionHref;
        }
    });

})(jQuery);