jQuery ($) ->

  class FeaturedPopular

    constructor: ->
      @initSliders()
      @initSwitcher()

    initSwitcher: =>
      $( '.featured-popular-tabs > div:first-child' ).addClass( 'active' )
      $( '.featured-popular-tabs > div:last-child' ).addClass( 'inactive' )

      if marketifyFeaturedPopular?.autoPlay
        $( '.featured-popular-slick .edd_downloads_list' ).slick 'slickPlay'

      $( '.featured-popular-switcher span' ).click (e) ->
        e.preventDefault();

        $( '.featured-popular-tabs > div' ).removeClass( 'active' ).addClass( 'inactive' )
        $( $(@).data( 'tab' ) ).addClass( 'active' )

        $( '.featured-popular-slick .edd_downloads_list' ).slick 'setPosition'

    initSliders: =>
      if ! $( '.featured-popular-slick .edd_downloads_list' ).length then return

      $( '.featured-popular-slick .edd_downloads_list' ).slick(
        autoplay: Boolean marketifyFeaturedPopular?.autoPlay
        autoplaySpeed: parseInt marketifyFeaturedPopular?.autoPlaySpeed
        slidesToShow: 3
        slidesToScroll: 3
        arrows: false
        dots: true
        adaptiveHeight: true
        rtl: marketifyFeaturedPopular?.isRTL
        responsive: [
          {
            breakpoint: 992,
            settings: {
              slidesToShow: 2,
              slidesToScroll: 2
            }
          }
          {
            breakpoint: 500,
            settings: {
              slidesToShow: 1,
              slidesToScroll: 1 
            }
          }
       ]
     )

  new FeaturedPopular()
