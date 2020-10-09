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

    jQuery('.zaso-hover-card').on('click', function(){
        window.location = jQuery(this).find('.zaso-hover-card__modal-action').attr('href');
    });

})(jQuery);