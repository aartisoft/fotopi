<style>
	.edd-invoices-field {
		float: left;
		width: 25%;
	}

	.edd-invoices-div {
		margin-top: 2em;
		margin-bottom: 2em;
	}
</style>
<form action="" method="post" id="<?php echo 'edd-invoices'; ?>">
	<div class="edd-invoices-div edd-invoices-custom-div">
		<label class="edd-invoices-field edd-invoices-custom-field" for="name"><?php _e( 'Billing Name:', 'edd-invoices' ); ?></label>
		<input type="text" name="edd-payment-user-name" value="<?php echo( isset( $user['first_name'] ) ? $user['first_name'] : '' ); ?>" id="name" />
	</div>
	<div class="edd-invoices-div edd-invoices-custom-div">
		<label class="edd-invoices-field edd-invoices-custom-field" for="address1"><?php _e( 'Billing Address:', 'edd-invoices' ); ?></label>
		<input type="text" name="edd-payment-address[0][line1]" value="<?php echo( isset( $user['address'] ) && is_array( $user['address'] ) ? $user['address']['line1'] : '' ); ?>" id="address1" />
	</div>
	<div class="edd-invoices-div edd-invoices-custom-div">
		<label class="edd-invoices-field edd-invoices-custom-field" for="address2"><?php _e( 'Line 2:', 'edd-invoices' ); ?></label>
		<input type="text" name="edd-payment-address[0][line2]" value="<?php echo( isset( $user['address'] ) && is_array( $user['address'] ) ? $user['address']['line2'] : '' ); ?>" id="address2" />
	</div>
	<div class="edd-invoices-div edd-invoices-custom-div">
		<label class="edd-invoices-field edd-invoices-custom-field" for="city"><?php _e( 'City:', 'edd-invoices' ); ?></label>
		<input type="text" name="edd-payment-address[0][city]" value="<?php echo( isset( $user['address'] ) && is_array( $user['address'] ) ? $user['address']['city'] : '' ); ?>" id="city" />
	</div>
	<div class="edd-invoices-div edd-invoices-custom-div">
		<label class="edd-invoices-field edd-invoices-custom-field" for="zip"><?php _e( 'Zip / Postal Code:', 'edd-invoices' ); ?></label>
		<input type="text" name="edd-payment-address[0][zip]" value="<?php echo( isset( $user['address'] ) && is_array( $user['address'] ) ? $user['address']['zip'] : '' ); ?>" id="zip" />
	</div>
	<div class="edd-invoices-div edd-invoices-custom-div">
		<label class="edd-invoices-field edd-invoices-custom-field" for="country"><?php _e( 'Country:', 'edd-invoices' ); ?></label>
		<select name="edd-payment-address[0][country]" size="1" id="country">
			<?php
			$countries = edd_get_country_list();
			foreach ( $countries as $key => $value ) {
				?>
				<option value="<?php echo $key; ?>"<?php echo( ( isset( $user['address'] ) && is_array( $user['address'] ) AND $user['address']['country'] == $key ) ? ' selected' : '' ); ?>><?php echo $value; ?></option>
				<?php
			}
			?>
		</select>
	</div>
	<div class="edd-invoices-div edd-invoices-custom-div">
		<label class="edd-invoices-field edd-invoices-custom-field" for="state"><?php _e( 'County / State:', 'edd-invoices' ); ?></label>
		<input type="text" name="edd-payment-address[0][state]" value="<?php echo( isset( $user['address'] ) && is_array( $user['address'] ) ? $user['address']['state'] : '' ); ?>" id="state" />
	</div>
	<div class="edd-invoices-div edd-invoices-custom-div">
		<label class="edd-invoices-field edd-invoices-custom-field" for="vat"><?php _e( 'Tax/VAT Number:', 'edd-invoices' ); ?></label>
		<input type="text" name="edd-payment-address[0][vat]" value="<?php echo( isset( $user['address'] ) && is_array( $user['address'] ) && isset( $user['address']['vat'] ) ? $user['address']['vat'] : '' ); ?>" id="vat" />
	</div>
	<div class="edd-invoices-div edd-invoices-custom-div">
		<label class="edd-invoices-field edd-invoices-custom-field" for="notes"><?php _e( 'Custom Notes:', 'edd-invoices' ); ?></label>
		<textarea name="edd-payment-address[0][notes]"><?php echo( isset( $user['address'] ) && is_array( $user['address'] ) && isset( $user['address']['notes'] ) ? $user['address']['notes'] : '' ); ?></textarea>
	</div>
	<div class="edd-invoices-div edd-invoices-custom-div">
		<?php wp_nonce_field('edd-invoices'.'-generate-invoice', 'edd-invoices'.'-nonce'); ?>
		<input type="submit" class="edd-invoices-generate-invoice-button edd_submit button button-secondary" value="<?php _e('Save Billing Details &amp; Generate Invoice', 'edd-invoices'); ?>" />
	</div>
</form>
