<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <main id="main">
 *
 * @package Marketify
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<div id="page" class="hfeed site">

	<div <?php echo apply_filters( 'marketify_page_header', array() ); ?>>

		<header id="masthead" class="site-header" role="banner">
			<div class="container">

				<div class="site-header-inner">

					<div class="site-branding">
						<?php $header_image = get_header_image(); ?>
						<?php if ( ! empty( $header_image ) ) : ?>
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home" class="custom-header"><img src="<?php echo esc_url( $header_image ); ?>" alt=""></a>
						<?php endif; ?>

						<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
						<h2 class="site-description screen-reader-text"><?php bloginfo( 'description' ); ?></h2>
					</div>

					<button class="js-toggle-nav-menu--primary nav-menu--primary-toggle"><span class="screen-reader-text"><?php _e( 'Menu', 'marketify' ); ?></span></button>

					<?php
						$args = array(
							'theme_location' => 'primary',
						);

						if ( has_nav_menu( 'primary' ) ) {
							$args['container_class'] = 'nav-menu nav-menu--primary';
						} else {
							$args['menu_class'] = 'nav-menu nav-menu--primary';
						}

						wp_nav_menu( $args );
					?>

				</div>

			</div>
		</header><!-- #masthead -->

		<div class="search-form-overlay">
			<?php
				add_filter( 'get_search_form', array( marketify()->template->header, 'search_form' ) );
				get_search_form();
				remove_filter( 'get_search_form', array( marketify()->template->header, 'search_form' ) );
			?>
		</div>
