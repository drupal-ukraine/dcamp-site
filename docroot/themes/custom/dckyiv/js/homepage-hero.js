/**
 * @file
 * Swiper init script for homepage hero.
 */

(function ($, Drupal) {
  'use strict';

  Drupal.behaviors.homepageHero = {
    attach: function (context, settings) {
      $('.paragraph--type--hero').once('homepageHero').each(function () {
        var mySwiper = new Swiper('.swiper-container', {
          speed: 400,
          spaceBetween: 20,
          slidesPerView: 3,
          loop: true,
          navigation: {
            nextEl: '.chevron-next',
            prevEl: '.chevron-prev'
          },
          breakpoints: {
            // when window width is <= 1024px
            1024: {
              slidesPerView: 2
            },
            560: {
              slidesPerView: 1
            }
          }
        });
      });
    }
  };

})(jQuery, Drupal);
