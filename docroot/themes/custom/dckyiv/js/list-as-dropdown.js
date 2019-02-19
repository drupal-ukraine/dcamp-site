/**
 * @file
 * Custom Select Dropdown init.
 */

(function ($, Drupal) {
  'use strict';

  /**
   * Change list to dropdown if mobile display.
   */
  Drupal.behaviors.listAsDropdown = {
    attach: function (context, settings) {
      $('#block-dckyiv-primary-local-tasks').once('list-as-dropdown').on('click', function() {
        $(this).toggleClass('nav-is-visible');
      });
    }
  };

})(jQuery, Drupal);
