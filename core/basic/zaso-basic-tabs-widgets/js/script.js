/* [ZASO] Basic Tabs Template - Main JS */

(function ($) {

  /**
   * Activate a tab: update aria-selected, roving tabindex, panel visibility,
   * and optionally move focus to the newly selected tab.
   *
   * @param {jQuery} $tab        The tab button to activate.
   * @param {boolean} moveFocus  Whether to move focus to the activated tab.
   */
  function activateTab($tab, moveFocus) {
    var $list = $tab.closest('.zaso-basic-tabs__list');
    var $tabs = $list.find('.zaso-basic-tabs__title');
    var ariaControlID = $tab.attr('aria-controls');
    var $tabsWrap = $tab.closest('.zaso-basic-tabs');

    // Roving tabindex + selected state across the tablist.
    $tabs.attr('aria-selected', 'false').attr('tabindex', '-1');
    $tab.attr('aria-selected', 'true').attr('tabindex', '0');

    // Show the matching panel, hide the rest (scoped to this widget instance).
    $tabsWrap.find('#' + ariaControlID).removeAttr('hidden')
      .siblings('.zaso-basic-tabs__content').attr('hidden', '');

    if (moveFocus) {
      $tab.trigger('focus');
    }
  }

  // Pointer activation.
  $('.zaso-basic-tabs__title').on('click', function () {
    activateTab($(this), false);
  });

  // Keyboard activation (WAI-ARIA tablist pattern).
  $('.zaso-basic-tabs__title').on('keydown', function (event) {
    var $tab = $(this);
    var $tabs = $tab.closest('.zaso-basic-tabs__list').find('.zaso-basic-tabs__title');
    var index = $tabs.index($tab);
    var lastIndex = $tabs.length - 1;
    var targetIndex = null;

    switch (event.key) {
      case 'ArrowLeft':
      case 'Left':
        targetIndex = (index <= 0) ? lastIndex : index - 1;
        break;
      case 'ArrowRight':
      case 'Right':
        targetIndex = (index >= lastIndex) ? 0 : index + 1;
        break;
      case 'Home':
        targetIndex = 0;
        break;
      case 'End':
        targetIndex = lastIndex;
        break;
      default:
        return;
    }

    event.preventDefault();
    activateTab($tabs.eq(targetIndex), true);
  });

})(jQuery);
