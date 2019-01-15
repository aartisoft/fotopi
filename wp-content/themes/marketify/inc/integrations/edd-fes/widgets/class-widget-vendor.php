<?php

class Marketify_Widget_FES_Vendor extends Marketify_Widget {

    public function __construct() {
        $this->widget_cssclass    = 'marketify_widget_fes_vendor';
        $this->widget_description = __( 'Display the vendor avatar and extra information.', 'marketify' );
        $this->widget_id          = 'marketify_widget_fes_vendor';
        $this->widget_name        = __( 'Marketify - Vendor: Name + Avatar', 'marketify' );
        $this->settings           = array(
            'sidebar-vendor' => array(
                'type' => 'widget-area',
				'std'  => array( __( 'Vendor Sidebar', 'marketify' ), __( 'Download Sidebar', 'marketify' ) ),
				'sidebars' => array( 'sidebar-vendor', 'sidebar-download-single' )
            ),
            'desc' => array(
                'type'  => 'description',
                'std'   => __( 'This widget has no options', 'marketify' )
            ),
        );
        parent::__construct();
    }

    function widget( $args, $instance ) {
        $vendor = new Marketify_EDD_FES_Vendor( fes_get_vendor() );

        $url = $vendor->url();
        $display_name = $vendor->display_name();
        $registered = $vendor->date_registered();

        echo $args[ 'before_widget' ];
    ?>
        <div class="download-author widget-detail--author">
            <?php do_action( 'marketify_vendor_profile_before', $vendor ); ?>

            <?php printf(  '<a class="author-avatar" href="%s" rel="author">%s</a>', esc_url( $url ), get_avatar( $vendor->ID, 130 ) ); ?>
            <?php printf( '<a class="author-link" href="%s" rel="author">%s</a>', esc_url( $url ), $display_name ); ?>

            <span class="widget-detail__info"><?php 
                printf( 
                    __( 'Author since: %s', 'marketify' ),
                    $registered
                );
            ?></span>

            <?php do_action( 'marketify_vendor_profile_after', $vendor ); ?>
        </div>
        <div class="widget-detail widget-detail--pull widget-detail--top">
            <strong class="widget-detail__title"><?php echo $vendor->downloads_count(); ?></strong>
            <span class="widget-detail__info"><?php echo _n( edd_get_label_singular(), edd_get_label_plural(), $vendor->downloads_count(), 'marketify' ); ?></span>
        </div>
    <?php
        echo $args[ 'after_widget' ];
    }

}
