/* [ZASO] Basic Tabs Template - Main JS */

(function ($) {

  jQuery('.zaso-basic-tabs__title').on('click', function(event) {

    // vars
    var $this = jQuery(this);
    var ariaControlID = $this.attr('aria-controls');

    // set selected on title
    $this.attr('aria-selected', 'true').siblings('button').attr('aria-selected', 'false');

    // set selected on content
    jQuery('#'+ariaControlID).removeAttr('hidden').siblings('.zaso-basic-tabs__content').attr('hidden','');

  });

})(jQuery);