//function for ajax pagination
function edd_points_ajax_pagination(pid){
	var data = {
					action: 'edd_points_next_page',
					paging: pid
				};

			jQuery('.edd-points-sales-loader').show();
			jQuery('.edd-points-paging').hide();

			jQuery.post(EDDPoints.ajaxurl, data, function(response) {
				var newresponse = jQuery( response ).filter( '.edd-points-user-log' ).html();
				jQuery('.edd-points-sales-loader').hide();
				jQuery('.edd-points-user-log').html( newresponse );
			});
	return false;
}

jQuery(document).ready(function($){
	$('body').on('edd_quantity_updated', function(e, data){
		if ( 'object' === typeof data ) {
			$.each(data, function(index, value){
				if(index.indexOf("points_redeem") >= 0){
					id = '#edd_cart_fee_'+index+' .edd_cart_fee_amount';
					$(id).html(value);
				}
			});
		}
	});
});