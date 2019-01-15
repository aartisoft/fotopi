(function() {
  jQuery(function($) {
    var Testimonials;
    Testimonials = {
      init: function() {
        var $list;
        $list = $('.testimonials-list');
        if (!$list.length) {
          return;
        }
        return $list.each(function() {
          var $inner;
          $inner = $(this).children().filter(':first-child');
          if ($inner.hasClass('company-testimonial')) {
            return $inner.parent().slick({
              slidesToShow: 5,
              slidesToScroll: 1,
              arrows: true,
              dots: false,
              variableWidth: false,
              centerMode: false,
              responsive: [
                {
                  breakpoint: 992,
                  settings: {
                    slidesToShow: 3
                  }
                }, {
                  breakpoint: 500,
                  settings: {
                    slidesToShow: 1
                  }
                }
              ]
            });
          } else {
            return $inner.parent().slick({
              autoplay: true,
              autoplaySpeed: Marketify.widgets.testimonials.individualSliderSpeed,
              slidesToShow: 2,
              slidesToScroll: 2,
              arrows: false,
              dots: false,
              responsive: [
                {
                  breakpoint: 500,
                  settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                  }
                }
              ]
            });
          }
        });
      }
    };
    return Testimonials.init();
  });

}).call(this);
