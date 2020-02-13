
/**
 * @file
 * Swiper init.
 */

(function ($, Drupal) {
  'use strict';

  /**
   * Swiper.
   */
  Drupal.behaviors.swiper = {
    attach: function (context, settings) {

      $('.paragraph--type--swiper-slider').once('swiper').each(function () {

        var $slider = $('.swiper-container');

        if ($slider.find('.swiper-wrapper').length === 1) {

        var element = $slider,
          elementDirection = element.attr('data-direction'),
          elementSpeed = element.attr('data-speed'),
          elementAutoPlay = element.attr('data-autoplay'),
          elementLoop = element.attr('data-loop'),
          elementEffect = element.attr('data-effect'),
          elementGrabCursor = element.attr('data-grab'),
          slideNumberTotal = element.find('.slide-number-total'),
          slideNumberCurrent = element.find('.slide-number-current'),
          sliderVideoAutoPlay = element.attr('data-video-autoplay');

        if( !elementSpeed ) { elementSpeed = 300; }
        if( !elementDirection ) { elementDirection = 'horizontal'; }
        if( elementAutoPlay ) { elementAutoPlay = Number( elementAutoPlay ); }
        if( elementLoop === 'true' ) { elementLoop = true; } else { elementLoop = false; }
        if( !elementEffect ) { elementEffect = 'slide'; }
        if( elementGrabCursor === 'false' ) { elementGrabCursor = false; } else { elementGrabCursor = true; }
        if( sliderVideoAutoPlay === 'false' ) { sliderVideoAutoPlay = false; } else { sliderVideoAutoPlay = true; }

        if (element.find('.swiper-pagination').length > 0) {
          var elementPagination = '.swiper-pagination',
            elementPaginationClickable = true;
        }
        else {
          var elementPagination = '',
            elementPaginationClickable = false;
        }

        if (element.find('.swiper-button-next').length > 0 && element.find('.swiper-button-prev').length > 0) {
          var elementNavNext = element.find('.swiper-button-next').first(),
            elementNavPrev = element.find('.swiper-button-prev').first();
        }

        var swiperSlider = new Swiper(element, {
          direction: elementDirection,
          speed: Number(elementSpeed),
          autoplay: {
            delay: elementAutoPlay
          },
          loop: elementLoop,
          effect: elementEffect,
          slidesPerView: 1,
          grabCursor: elementGrabCursor,
          pagination: {
            el: elementPagination,
            clickable: elementPaginationClickable
          },
          navigation: {
            nextEl: elementNavNext,
            prevEl: elementNavPrev
          },
          preloadImages: false,
          lazy: true,
          onHover: function (swiper) {
            swiper.stopAutoplay();
          }
        });

        if (slideNumberCurrent.length > 0) {
          if (elementLoop === true) {
            slideNumberCurrent.html(Number(element.find('.swiper-slide.swiper-slide-active').attr('data-swiper-slide-index')) + 1);
          }
          else {
            slideNumberCurrent.html(swiperSlider.activeIndex + 1);
          }
        }
        if (slideNumberTotal.length > 0) {
          slideNumberTotal.html(element.find('.swiper-slide:not(.swiper-slide-duplicate)').length);
        }
      }
      });
    }
  };

})(jQuery, Drupal);
