jQuery ($) ->

  DownloadSliders =
    init: ->
      @el = '.download-gallery'
      @elAsNav = '.download-gallery-navigation'

      if ( $( '.page-header--download' ).find( $(@el) ).length > 0 )
        @initTopSlider()
      else
        @initContentSlider()

    initTopSlider: ->
      $(@el).slick
        adaptiveHeight: true

    initContentSlider: ->
      $(@el).slick
        slidesToShow: 1
        slidesToScroll: 1
        arrows: false
        fade: true
        asNavFor: @elAsNav
        adaptiveHeight: true

      $(@elAsNav).slick
        slidesToShow: 6
        slidesToScroll: 6
        asNavFor: @el
        dots: true
        focusOnSelect: true

  DownloadSliders.init()
