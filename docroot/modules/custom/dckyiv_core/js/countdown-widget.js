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
                    + '<div class="time-item"><span>%D</span> <span>' + Drupal.t('days') + '</span></div>'
                    + '<div class="time-dots"><span>:</span></div>'
                    + '<div class="time-item"><span>%H</span> <span>' + Drupal.t('hours') + '</span></div>'
                    + '<div class="time-dots"><span>:</span></div>'
                    + '<div class="time-item"><span>%M</span> <span>' + Drupal.t('minutes') + '</span></div>'
                    + '<div class="time-dots"><span>:</span></div>'
                    + '<div class="time-item"><span>%S</span> <span>' + Drupal.t('seconds') + '</span></div>'));
            });
        },

        detach: function (context, settings, trigger) {

        }
    };

})(jQuery, Drupal, drupalSettings);
