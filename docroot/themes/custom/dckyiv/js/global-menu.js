/**
 * Global menu behaviour.
 */

(function (Drupal, $) {
  'use strict';

  Drupal = Drupal || {};

  /**
   * The main menu behavior object.
   */
  Drupal.meganavMenu = function ($links, options) {

    var defaults = {
      menuBehaviour: 'click' // or mouseover.
    };

    /**
     * The timer ID for for menu item to hide.
     *
     * @type {number}
     */
    this.timeId = 0;

    /**
     * Reference to self.
     *
     * @type {Drupal}
     * @private
     */
    var _self = this;

    /**
     * Helper method to show/hide meganav item link content.
     *
     * @param $link
     */
    var showHide = function ($link) {
      if ($link.parent('li').hasClass('open')) {
        $link.parent('li').removeClass('open');
      }
      else {
        $links.each(function () {
          $(this).parent('li').removeClass('open');
        });
        $link.parent('li').addClass('open');
      }
    };

    /**
     * Toggle mobile navigation
     */
    var mobileNavToggle = function () {
      $('.mobilenavtoggle').click(function () {
        $('#mobile-navigation').toggleClass('open');
        $('html, body').toggleClass('no-scroll');
      });
    };

    /**
     * Toggle mobile navigation
     */
    var searchToggle = function () {
      $('.search-toggle, .opensearch').click(function (e) {
        e.preventDefault();
        $('.block-globalsearch-block').toggleClass('open');
        $($(this)).toggleClass('search-open');
      });
      $('body').on('click touchstart', function (e) {
        // Hide search if clicked anywhere else.
        if ($(e.target).closest('.block-globalsearch-block.open').length === 0 &&
          $(e.target).closest('a.search-open').length === 0 &&
          $(e.target).closest('.opensearch').length === 0) {
          $('.block-globalsearch-block').removeClass('open');
          $('.opensearch').removeClass('search-open');
        }
      });
    };

    /**
     * Mobile navigation accordion.
     */
    var mobileNavAccordion = function () {

      var submenuOpen = true;

      $('.subnav-toggle').on('click', function (e) {
        var $expandable = $(this).closest('.menu-item--expanded');
        var $parents = $expandable.parents('.menu-item--expanded');

        // Adding class to parent li
        $expandable.toggleClass('expanded');

        // If this is nested menu
        if ($parents.hasClass('expanded')) {
          $parents.removeClass('expanded').addClass('parent');
        }

        // If parent is clicked - remove all classes in nested elements
        if ($expandable.hasClass('parent')) {
          $expandable.removeClass('parent').find('li').removeClass('expanded parent');
        }

        // If this is nested menu
        if ($parents.hasClass('parent')) {
          $parents.removeClass('parent').addClass('expanded');
        }

        if ($expandable.hasClass('expanded')) {
          $parents.removeClass('expanded parent');
          if ($parents.length > 0) {
            $parents.addClass('parent');
          }
        }

        // Removing expanded and parent classes on all child elements when
        // parent element is clicked
        if ($expandable.hasClass('parent')) {
          $(this).find('li').removeClass('expandable parent');
        }

        // Checking if submenu is open and adding / removing class from parent
        // nav and wrapper
        submenuOpen = $(this).closest('nav').find('.expanded, .parent').length > 0;
        $('.region-mobile-navigation').toggleClass('open', submenuOpen);
        $(this).closest('nav').toggleClass('open', submenuOpen);
      });
    };

    /**
     * Public method to initialize menu behaviour.
     */
    this.init = function () {

      var params = $.extend(true, defaults, options);

      switch (params.menuBehaviour) {
        case 'mouseover':
          $links.on('mouseover', function (e) {
            showHide($(this));

            $links.on('mouseleave', function () {
              _self.timeId = setTimeout(function () {
                showHide($(this));
              }, 500);
            });

            e.preventDefault();
            clearTimeout(_self.timeId);

          });
          break;

        case 'click':
          $links.on('click', function (e) {
            e.preventDefault();
            showHide($(this));
          });

          $('body').on('click touchstart', function (e) {
            // Hide the mega menu if clicking outside of the mega menu content.
            if ($(e.target).closest('li.open').length === 0) {
              $links.each(function () {
                $(this).parent('li').removeClass('open');
              });
            }

          });
          break;
      }

      // Adding class to menu li element if it has paragraph in it
      $('ul.menu .field--name-field-menu-paragraph').closest('li').addClass('has-content');

      // Initialize mobile nav open / close toggle
      mobileNavToggle();
      // Initialize mobile nav accordion behavior
      mobileNavAccordion();
      // Desktop nav Search toggle
      searchToggle();

    };

  };

  /**
   *
   * @type {{attach: Drupal.behaviors.meganavMenu.attach}}
   */
  Drupal.behaviors.meganavMenu = {
    attach: function (context, settings) {
      var $menuContainer = $('#block-meganav-main-menu');

      $menuContainer.once('meganavMenu').each(function () {
        var $links = $('.menu-level-0 > li > .link-container', $(this));
        if ($links.length > 0) {
          var menu = new Drupal.meganavMenu($links);
          menu.init();
        }
      });
    }
  };
})(Drupal, jQuery);
