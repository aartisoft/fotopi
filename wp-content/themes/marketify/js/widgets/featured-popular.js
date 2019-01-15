(function() {
  var bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; };

  jQuery(function($) {
    var FeaturedPopular;
    FeaturedPopular = (function() {
      function FeaturedPopular() {
        this.initSliders = bind(this.initSliders, this);
        this.initSwitcher = bind(this.initSwitcher, this);
        this.initSliders();
        this.initSwitcher();
      }

      FeaturedPopular.prototype.initSwitcher = function() {
        $('.featured-popular-tabs > div:first-child').addClass('active');
        $('.featured-popular-tabs > div:last-child').addClass('inactive');
        if (typeof marketifyFeaturedPopular !== "undefined" && marketifyFeaturedPopular !== null ? marketifyFeaturedPopular.autoPlay : void 0) {
          $('.featured-popular-slick .edd_downloads_list').slick('slickPlay');
        }
        return $('.featured-popular-switcher span').click(function(e) {
          e.preventDefault();
          $('.featured-popular-tabs > div').removeClass('active').addClass('inactive');
          $($(this).data('tab')).addClass('active');
          return $('.featured-popular-slick .edd_downloads_list').slick('setPosition');
        });
      };

      FeaturedPopular.prototype.initSliders = function() {
        if (!$('.featured-popular-slick .edd_downloads_list').length) {
          return;
        }
        return $('.featured-popular-slick .edd_downloads_list').slick({
          autoplay: Boolean(typeof marketifyFeaturedPopular !== "undefined" && marketifyFeaturedPopular !== null ? marketifyFeaturedPopular.autoPlay : void 0),
          autoplaySpeed: parseInt(typeof marketifyFeaturedPopular !== "undefined" && marketifyFeaturedPopular !== null ? marketifyFeaturedPopular.autoPlaySpeed : void 0),
          slidesToShow: 3,
          slidesToScroll: 3,
          arrows: false,
          dots: true,
          adaptiveHeight: true,
          rtl: typeof marketifyFeaturedPopular !== "undefined" && marketifyFeaturedPopular !== null ? marketifyFeaturedPopular.isRTL : void 0,
          responsive: [
            {
              breakpoint: 992,
              settings: {
                slidesToShow: 2,
                slidesToScroll: 2
              }
            }, {
              breakpoint: 500,
              settings: {
                slidesToShow: 1,
                slidesToScroll: 1
              }
            }
          ]
        });
      };

      return FeaturedPopular;

    })();
    return new FeaturedPopular();
  });

}).call(this);
