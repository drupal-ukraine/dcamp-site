/**
 * @file
 * Custom Select Dropdown init.
 */

(function ($, Drupal, once) {
  'use strict';

  /**
   * Change list to dropdown if mobile display.
   */
  Drupal.behaviors.listAsDropdown = {
    attach: function (context, settings) {
      once('list-as-dropdown', '#block-dckyiv-primary-local-tasks').forEach(function (el) {
        $(el).on('click', function() {
          $(this).toggleClass('nav-is-visible');
        });
      })
    }
  };

})(jQuery, Drupal, once);
