<?php
/**
 * Some old template tags people might use in child themes.
 */

function marketify_purchase_link( $download_id ) {
	marketify()->get( 'edd' )->purchase_form->purchase_link( $download_id );
}

function marketify_is_multi_vendor() {
	return true;
}

function marketify_edd_fes_author_url( $id ) {
	$vendor = Marketify_EDD_FES_Vendor( $id );

	return $vendor->url();
}

function marketify_content_nav() {
	marketify()->template->pagination->output();
}

function marketify_get_theme_menu( $theme_location ) {
	return marketify()->template->navigation->get_theme_menu( $theme_location );
}
