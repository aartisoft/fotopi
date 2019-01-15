jQuery(document).ready(function ($) {

	/**
	 * Fraud Monitor Settings screen JS
	 */
	var EDD_FM_Settings = {

		init : function() {
			this.add();
			this.remove();
			this.email();
			this.countries();
			this.behavior();
		},

		clone_repeatable : function(row) {

			// Retrieve the highest current key
			var key = highest = 0;
			row.parent().find( 'tr' ).each(function() {
				var current = $(this).data( 'key' );
				if( parseInt( current ) > highest ) {
					highest = current;
				}
			});
			key = highest += 1;

			clone = row.clone();

			/** manually update any select box values */
			clone.find( 'select' ).each(function() {
				$( this ).val( row.find( 'select[name="' + $( this ).attr( 'name' ) + '"]' ).val() );
			});

			clone.removeClass( 'edd_add_blank' );

			clone.attr( 'data-key', key );
			clone.find( 'td input, td select' ).val( '' );
			clone.find( '.search-choice' ).remove();
			clone.find( 'td input, td select' ).each(function() {
				var name = $( this ).attr( 'name' );
				if ( name ) {
					name = name.replace( /\[(\d+)\]/, '[' + parseInt( key ) + ']');
					$( this ).attr( 'name', name ).attr( 'id', name );
				}
			});

			// Remove the Chosen container so we can re-create it
			clone.find( '.chosen-container' ).remove();

			return clone;
		},

		add : function() {
			$( '#eddfm_add_condition' ).click( function(e) {
				e.preventDefault();
				var button = $( this ),
				row = $('#eddfm-product-combinations tr:last');
				clone = EDD_FM_Settings.clone_repeatable(row);
				clone.insertAfter( row ).find('input, textarea, select').filter(':visible').eq(0).focus();
				clone.find('.edd-select-chosen').chosen({
					inherit_select_classes: true,
					placeholder_text_single: edd_vars.one_option,
					placeholder_text_multiple: edd_vars.one_or_more_option,
				});
				clone.find( '.edd-select-chosen', '#choose-download' ).css( 'width', '100%' );
			});
		},

		remove: function() {
			$( 'body' ).on( 'click', '.eddfm_remove_condition', function(e) {
				e.preventDefault();

				var row        = $(this).parent().parent( 'tr' ),
					count      = $('#eddfm-product-combinations .condition-row').length,
					repeatable = '#eddfm-product-combinations tr'

				var row_id = row.data('key');
				$( '.edd_repeatable_condition_field option[value="' + row_id + '"]' ).remove();

				if( count > 1 ) {
					row.remove();
				} else {
					row.find( 'td input, td select' ).val( '' );
					row.find( '.search-choice' ).remove();
				}

				/* re-index after deleting */
				var new_count = 0;
				$('#eddfm-product-combinations .condition-row').each( function() {
					$(this).attr( 'data-key', new_count );
					$(this).find( 'td input, td select' ).each(function() {
						var name = $( this ).attr( 'name' );
						if ( name ) {
							name = name.replace( /\[(\d+)\]/, '[' + parseInt( new_count ) + ']');
							$( this ).attr( 'name', name ).attr( 'id', name );
						}
					});
					new_count++;
				});
			});
		},

		email: function() {
			$('.email-check-box').change( function() {
				$(this).parent().next('.email-content').toggle();
			});

			$('.eddfm-show-tags').on( 'click', function(e) {
				e.preventDefault();
				$(this).parent().next('.eddfm-email-tags').toggle();
				$(this).toggle();
			});

			$('.eddfm-hide-tags').on( 'click', function(e) {
				e.preventDefault();
				$(this).parent().toggle();
				$(this).parent().prev().find('.eddfm-show-tags').toggle();
			});

		},

		countries: function() {
			$('#ip-lookup-service').change( function() {
				$('#geo-ip-service-notices span').hide();
				var selected_service = $(this).val();
				$('#geo-ip-service-notices [data-service="' + selected_service + '"]').show();
			});

			$('#geofence-enabled').change( function() {
				var enabled = $(this).is(':checked');
				if (enabled) {
					$('.geofence-settings .edd-fm-toggle').show();
					$('#edd_fm_geofence_fence').trigger('change');
				} else {
					$('.geofence-settings .edd-fm-toggle').hide();
				}
			});

			$('#edd_fm_geofence_fence').change( function() {
				var value = $(this).val();
				$('.geofence-settings .geofence-type').hide();
				$('.geofence-settings .geofence-type.' + value).show();

				if ( 'distance' === value ) {
					$('.edd-fm-toggle.geofence-range').show();
				} else {
					$('.edd-fm-toggle.geofence-range').hide();
				}
			});
		},

		behavior: function() {
			$('#user-has-purchases').change( function() {
				var checked = $(this).is(':checked');

				if ( checked ) {
					$('#user-created-threshold').show();
				} else {
					$('#user-created-threshold').hide();
				}
			});
		},

	}
	EDD_FM_Settings.init();

	var EDD_FM_Payments = {
		init : function() {
			this.actions();
		},

		actions : function() {
			$('#confirm-as-fraud').click( function(e) {
				e.preventDefault();

				$('#accept-as-valid').attr('disabled', 'disabled');
				$('#fraud-actions').trigger('change');
				$('#eddfm-fraud-actions-complete').slideDown();

			});

			$('#fraud-actions').change( function() {
				var value = $(this).val();
				var revokeDeleteWarning = $('.revoke-delete-warning');
				var refundWarning = $('.refund-warning');

				if ( value !== '-1' ) {
					switch( value ) {
						case 'revoked':
						case 'delete':
							refundWarning.hide();
							revokeDeleteWarning.show();
							break;
						case 'refunded':
						default:
							revokeDeleteWarning.hide();
							refundWarning.show();
							break;
					}
				}

			});

			$('#eddfm-cancel-is-fraud').click( function(e) {
				e.preventDefault();

				$('#eddfm-fraud-actions-complete').slideUp();
				$('#accept-as-valid').removeAttr('disabled');
			});

			$('.complete-fraud').click( function(e) {
				e.preventDefault();
				$('#eddfm-confirm').attr('disabled', 'disabled');
				$('#eddfm-cancel-is-fraud').hide();
				$('#eddfm-fraud-actions-complete .spinner').show().css('visibility', 'visible');

				var payment_id = $('input[name="eddfm_payment_id"]').val();
				var action     = $('input[name="eddfm_action"]').val();
				var status     = $('select[name="eddfm_actions"]').val();
				var nonce      = $('input[name="eddfm_fraud_nonce"]').val();

				var data = {
					payment_id: payment_id,
					edd_action: action,
					status: status,
					nonce: nonce,
				}

				$.post(ajaxurl, data, function (response) {
					var message_wrapper = $('.eddfm-message');
					message_wrapper.html(response.message).show();

					if( true === response.success ) {
						setTimeout( function() {
							window.location.replace( response.redirect );
						}, 2000);
					} else {
						$('#eddfm-confirm').removeAttr('disabled');
						$('#eddfm-cancel-is-fraud').show();
						$('#eddfm-fraud-actions-complete .spinner').hide().css('visibility', 'hidden');
					}
				}, 'json');

			});

			$('#edd-fm-action').change( function() {
				var value = $(this).val();

				if ( 'fraud' === value ) {
					$('#edd-payment-mark-as-fraud .edd-fm-toggle').show();
				} else {
					$('#edd-payment-mark-as-fraud .edd-fm-toggle').hide();
				}
			});

			$('#edd-fm-flag-payment').click( function(e) {
				$('#edd-fm-flag-payment').attr('disabled', 'disabled');
				e.preventDefault();

				var flag_as    = $('#edd-fm-action').val();
				var payment_id = $('#edd-fm-flag-payment').data('payment-id');
				var action     = 'manual_fraud';
				var status     = false;

				if ( 'fraud' === flag_as ) {
					status = $('input[name="edd-fm-status"]:checked').val();
				}


				var reason = $('#edd-payment-fraud-reason').val();
				var nonce  = $('input[name="edd-fm-flag-payment"]').val();
				$('#edd-payment-mark-as-fraud .spinner').show().css('visibility', 'visible');

				var data = {
					payment_id: payment_id,
					status: status,
					flag_as: flag_as,
					reason: reason,
					nonce: nonce,
					edd_action: action
				}

				$.post(ajaxurl, data, function (response) {
					var message_wrapper = $('.eddfm-message');
					message_wrapper.html(response.message).show();

					if( true === response.success ) {
						setTimeout( function() {
							window.location.replace( response.redirect );
						}, 2000);
					} else {
						$('#edd-fm-flag-payment').removeAttr('disabled');
						$('#edd-payment-mark-as-fraud .spinner').show().css('visibility', 'visible');
					}
				}, 'json');
			});

		}

	}
	EDD_FM_Payments.init();

});
