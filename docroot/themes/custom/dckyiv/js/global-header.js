/**
 * Global header behaviour.
 */

(function (Drupal, $) {
  'use strict';

  Drupal = Drupal || {};

  /**
   * The main menu behavior object.
   */
  Drupal.dckyivHeader = function () {

    this.header = $('header.main-header');
    this.body = $('body');
    this.mainContainer = $('main.container');
    this.alertBlock = $('.block-dckyiv-site-alert-block');
    this.window = $(window);

    var _self = this;

    /**
     * Public method to adjust content padding depenting on conditions.
     */
    this.recalculateContentPadding = function () {

      var containerPadding = 0;

      if (_self.window.width() < 1024) {
        containerPadding = _self.header.outerHeight();
      }
      else {

        if (_self.body.hasClass('transparent-header')) {
          containerPadding = _self.alertBlock.outerHeight();
        }
        else {
          containerPadding = _self.header.outerHeight();
        }
      }

      if (containerPadding > 0) {
        _self.mainContainer.css('padding-top', containerPadding + 'px');
      }
      else {
        _self.mainContainer.css('padding-top', 'inherit');
      }

    };

    /**
     * Public method to initialize header and add screen resize events.
     */
    this.init = function () {
      var throttledContentPadding = _.throttle(_self.recalculateContentPadding, 250);

      // Calling throttled recalculate on window resize
      $(window).on('resize', function () {
        throttledContentPadding();
      });

      _self.recalculateContentPadding();
    };
  };

  /**
   *
   * @type {{attach: Drupal.behaviors.dckyivHeader.attach}}
   */
  Drupal.behaviors.dckyivHeader = {
    attach: function (context, settings) {

      $('header.main-header').once('dckyivHeader').each(function () {
        Drupal.dckyivHeaderObj = new Drupal.dckyivHeader;
        Drupal.dckyivHeaderObj.init();
      });
    }
  };

})(Drupal, jQuery);
