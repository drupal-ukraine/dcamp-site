/**
 * @file
 * Custom Select Dropdown init.
 */

(function ($, Drupal) {
  'use strict';

  /**
   * Change list to dropdown if mobile display.
   * Usage:
   *  1. if ul element add class 'list-as-dropdown'
   *  2. if paragraph create new template and add class 'wrap-list-as-dropdown' to wrapper.
   */
  Drupal.behaviors.listAsDropdown = {
    attach: function (context, settings) {
      $('#block-dckyiv-primary-local-tasks ul:first').once('list-as-dropdown').each(function () {
        var width = drupalSettings.widthBreakpoint || 580,
          mq = '(max-width: ' + width + 'px)',
          $this = $(this);

        // Detect window size and if less then 580px transform list to dropdown.
        if (window.matchMedia(mq).matches && !$this.hasClass('select-options')) {

          // Create wrapper element for select.
          $this.addClass('select-options');
          $this.wrap('<div class="select"></div>');
          $this.before('<div class="select-styled"></div>');

          var $styledSelect = $this.prev(),
            activeElement = $this.find('*.active');

          if (activeElement.length) {
            $styledSelect.text($this.find('*.active').text());
          }
          else {
            $styledSelect.text($this.find('li:first>*').text());
          }

          // On click show unordered list as dropdown list.
          $styledSelect.click(function (e) {
            e.stopPropagation();
            $('div.select-styled .active').not(this).each(function () {
              $(this).removeClass('active').next('ul.select-options').hide();
            });
            $(this).toggleClass('active').next('ul.select-options').toggle();
          });

          var $list = $this,
            $listItems = $list.children('li');

          // Change value in the select.
          $listItems.click(function (e) {
            e.stopPropagation();
            $styledSelect.text($(this).text()).removeClass('active');
            $this.val($(this).attr('rel')).change();
            $list.hide();
          });

          $(document).click(function () {
            $styledSelect.removeClass('active');
            $list.hide();
          });
        }
      });
    }
  };

})(jQuery, Drupal);
