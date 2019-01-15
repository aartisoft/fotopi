<?php
/**
 * Template Name: Page: Meet the Team
 *
 * @package Marketify
 */

get_header(); ?>

	<?php do_action( 'marketify_entry_before' ); ?>

	<div id="content" class="site-content container">

		<div role="main" class="content-area">

		<?php if ( have_posts() ) : ?>

			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'content', 'page' ); ?>

			<?php endwhile; ?>

			<div class="the-team">
				<?php
					$users = array();
					$roles = apply_filters( 'marketify_the_team_roles', array( 'author', 'shop_worker', 'shop_manager', 'editor', 'administrator' ) );

				foreach ( $roles as $role ) {
					$user_ids = get_users( array(
						'role'    => $role,
						'fields'  => 'IDs',
					) );

					foreach ( $user_ids as $user_id ) {
						$users[] = $user_id;
					}
				}

				foreach ( $users as $user_id ) :
					$social = marketify()->template->entry->social_profiles( $user_id );
				?>

				<div class="team-member">
				<div class="user-bubble user-bubble--team <?php if ( ! empty( $social ) ) : ?>user-bubble--with-social<?php endif; ?>">
				<?php
					printf( '<div class="user-bubble__gravatar">%1$s %2$s</div>',
						sprintf( '<div class="user-bubble__social-profiles">%1$s</div>', $social ),
						get_avatar( $user_id, 400 )
					);
				?>
				</div>
				<div class="team-member__byline">
				<?php
				printf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a></span>',
					esc_url( get_author_posts_url( $user_id ) ),
					esc_attr( sprintf( __( 'View all posts by %s', 'marketify' ), get_the_author_meta( 'display_name', $user_id ) ) ),
					esc_html( get_the_author_meta( 'display_name', $user_id ) )
				);
				?>
				</div>
				<div class="team-member__description">
				<?php echo wpautop( get_the_author_meta( 'description', $user_id ) ); ?>
				</div>
				</div>

				<?php endforeach; ?>
			</div>

		<?php else : ?>

			<?php get_template_part( 'no-results', 'index' ); ?>

		<?php endif; ?>

		</div><!-- #primary -->

	</div>

<?php get_footer(); ?>
