var Marketify = Marketify || {};

Marketify.App = ( function($) {
	function menuToggle() {
		$( '.js-toggle-nav-menu--primary' ).click(function(e) {
			e.preventDefault();

			$( '.nav-menu--primary' ).toggleClass( 'active' );
		});
	}
	function menuSearch() {
		$( '.js-toggle-search' ).click(function(e) {
			e.preventDefault();

			$( '.search-form-overlay' ).toggleClass( 'active' );
		});
	}

	function soliloquySliders() {
		if ( $(window).width() < 500 ) {
			var sliders = $( '.soliloquy' );

			$.each(sliders, function() {
				var image = $(this).find( 'img' ),
					src   = image.prop( 'src' );

				$(this)
					.find( 'li' )
					.css({
						'height'           : $(window).outerHeight(),
						'background-image' : 'url(' + src + ')',
						'background-size'  : 'cover'
					});

				image.hide();
			});
		}
	}

	function initVideos() {
		var video = $( '.header-outer .wp-video video' ).get(0);

		if ( typeof video === 'undefined' ) {
			return;
		}

		var vide = $( '.header-outer .wp-video' ).vide();

		vide.resize();

		function adjustHeight() {
			if ( $(window).width() < 768 ) {
				$( video ).hide();
			} else {
				$( video ).show();
			}
		}

		adjustHeight();

		$(window).resize(function() {
			adjustHeight();
		});

		return;
	}

	function initPurchaseForms() {
		$( '.buy-now.popup-trigger' ).on( 'click', function(e) {
			e.preventDefault();

			$button = $(this);
			$form = $button.next();

			Marketify.App.popup({
				items : {
					src : '#marketify-price-options-popup',
					fixedContentPos: false,
					fixedBgPos: false,
					overflowY: 'scroll'
				},
				callbacks: {
					beforeOpen: function() {
						$clone = $form.clone();
						$( '#marketify-price-options' ).html( $clone );
					}
				}
			});
		});
	}

	function initSectionTitles() {
		$( '.edd-slg-login-wrapper' ).each(function() {
			var link  = $(this).find( 'a' );
			var title = link.attr( 'title' );

			link.html(title).prepend( '<span></span' );
		});

		// section title shims
		$( '.edd_form fieldset > span legend' ).unwrap();

		var shims = [
			$( '.gform_title' ),
			$( '.fes-form h1' ),
			$( '.fes-headers' ),
			$( '.edd_form *:not(span) > legend' ),
			$( '.pm-section-title' ),
			$( '.edd-reviews-title' ),
			$( '.edd-reviews-heading' ),
			$( '.edd-reviews-vendor-feedback-item h4' ),
			$( '.edd-csau-products h2' ),
			$( '#edd_checkout_user_info legend' ),
			$( '#edd_cc_fields legend' ),
			$( '#edd_cc_address legend' ),
			$( '#edd_register_account_fields legend' ),
		]

		$.each(shims, function() {
			if ( 0 === $(this).find( 'span' ).length ) {
				$(this).wrapInner( '<span></span>' );
			}
		});

		$('body').on('click.eddwlOpenModal', '.edd-add-to-wish-list', function (e) {
			$( '#edd-wl-modal-label' ).wrapInner( '<span></span>' );
		});
	}

	return {
		init : function() {
			menuToggle();
			menuSearch();
			soliloquySliders();
			initVideos();
			initPurchaseForms();
			initSectionTitles();

			$(window).resize(function() {
				soliloquySliders();
			});

			$(document).on( 'click', '.popup-trigger', function(e) {
				e.preventDefault();

				Marketify.App.popup({
					items : {
						src : $(this).attr( 'href' ),
						fixedContentPos: false,
						fixedBgPos: false,
						overflowY: 'scroll'
					}
				});
			});

			$( 'body' ).on( 'edd_gateway_loaded', function() {
				initSectionTitles();
			});

			$( '.edd_download.content-grid-download' ).attr( 'style', '' );

			// sorting widget
			$( '.download-sorting input, .download-sorting select' ).change(function(){
				$(this).closest( 'form' ).submit();
			});

			$( '.download-sorting span' ).click( function(e) {
				e.preventDefault();
				$(this).prev().attr( 'checked', true );
				$(this).closest( 'form' ).submit();
			});

			$( '.content-grid-download__entry-image' ).bind( 'ontouchstart', function(e) {
				$(this).toggleClass( 'hover' );
			});

			$( '.individual-testimonial .avatar' ).wrap( '<div class="avatar-wrap"></div>' );

			function pagi() {
				$( '.edd_downloads_list' ).each(function() {
					var pagi = $(this).find( $( '#edd_download_pagination' ) );

					pagi.insertAfter( '.edd_downloads_list' );
				});

			}

			pagi();

			$( '.download-gallery__image' ).magnificPopup({
				delegate: 'a',
				type: 'image',
				gallery: {
					enabled: true,
					navigateByImgClick: true,
					preload: [0,1] // Will preload 0 - before current, and 1 after the current image
				}
			});

		},

		popup : function( args ) {
			return $.magnificPopup.open( $.extend( args, {
				type         : 'inline',
				removalDelay : 250
			} ) );
		},

	}
} )(jQuery);

jQuery(document).ready(function() {
	Marketify.App.init();
});
