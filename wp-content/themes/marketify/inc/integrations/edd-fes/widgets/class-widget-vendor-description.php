<?php

class Marketify_Widget_FES_Vendor_Description extends Marketify_Widget {

	public function __construct() {
		$this->widget_cssclass    = 'marketify_widget_fes_vendor_description';
		$this->widget_description = __( 'Display the vendor description.', 'marketify' );
		$this->widget_id          = 'marketify_widget_fes_vendor_description';
		$this->widget_name        = __( 'Marketify - Vendor: Biography', 'marketify' );
		$this->settings           = array(
			'sidebar-vendor' => array(
				'type' => 'widget-area',
				'std'  => array( __( 'Vendor Sidebar', 'marketify' ), __( 'Download Sidebar', 'marketify' ) ),
				'sidebars' => array( 'sidebar-vendor', 'sidebar-download-single' ),
			),
			'desc' => array(
				'type'  => 'description',
				'std'   => __( 'This widget has no options', 'marketify' ),
			),
		);
		parent::__construct();
	}

	function widget( $args, $instance ) {
		$vendor = new Marketify_EDD_FES_Vendor( fes_get_vendor() );
		$description = $vendor->obj->description;

		if ( '' == $description ) {
			return;
		}

		echo $args['before_widget'];
	?>
		<div class="download-author-bio">
			<?php echo esc_html( $description ); ?>
		</div>

		<?php do_action( 'marketify_vendor_profile_after_bio', $vendor ); ?>
	<?php
		echo $args['after_widget'];
	}

}
