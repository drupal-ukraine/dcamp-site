/**
 * @file
 * Javascript to Countdown widget.
 */

(function ($, Drupal, drupalSettings) {

    'use strict';

    Drupal.behaviors.CountDownWidget = {

        attach: function (context) {
            var countdownDate = $('#countdown-clock-value').val();
            $('#countdown-clock').countdown(countdownDate, function(event) {
                var $this = $(this).html(event.strftime(''
                    + '<span>%w</span> weeks '
                    + '<span>%d</span> days '
                    + '<span>%H</span> hr '
                    + '<span>%M</span> min '
                    + '<span>%S</span> sec'));
            });
        },

        detach: function (context, settings, trigger) {

        }
    };

})(jQuery, Drupal, drupalSettings);
