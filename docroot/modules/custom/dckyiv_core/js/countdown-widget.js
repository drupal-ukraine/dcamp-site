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
                    + '<div class="time-item"><span>%D</span> <span>' + Drupal.t('днів') + '</span></div>'
                    + '<div class="time-dots"><span>:</span></div>'
                    + '<div class="time-item"><span>%H</span> <span>' + Drupal.t('годин') + '</span></div>'
                    + '<div class="time-dots"><span>:</span></div>'
                    + '<div class="time-item"><span>%M</span> <span>' + Drupal.t('хвилин') + '</span></div>'
                    + '<div class="time-dots"><span>:</span></div>'
                    + '<div class="time-item"><span>%S</span> <span>' + Drupal.t('секунд') + '</span></div>'));
            });
        },

        detach: function (context, settings, trigger) {

        }
    };

})(jQuery, Drupal, drupalSettings);
