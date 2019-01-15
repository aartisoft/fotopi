(function() {
  jQuery(function($) {
    var DownloadSliders;
    DownloadSliders = {
      init: function() {
        this.el = '.download-gallery';
        this.elAsNav = '.download-gallery-navigation';
        if ($('.page-header--download').find($(this.el)).length > 0) {
          return this.initTopSlider();
        } else {
          return this.initContentSlider();
        }
      },
      initTopSlider: function() {
        return $(this.el).slick({
          adaptiveHeight: true
        });
      },
      initContentSlider: function() {
        $(this.el).slick({
          slidesToShow: 1,
          slidesToScroll: 1,
          arrows: false,
          fade: true,
          asNavFor: this.elAsNav,
          adaptiveHeight: true
        });
        return $(this.elAsNav).slick({
          slidesToShow: 6,
          slidesToScroll: 6,
          asNavFor: this.el,
          dots: true,
          focusOnSelect: true
        });
      }
    };
    return DownloadSliders.init();
  });

}).call(this);
