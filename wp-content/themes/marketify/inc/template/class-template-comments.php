<?php

class Marketify_Template_Comments {

	public function comment( $comment, $args, $depth ) {
		global $post;

		$GLOBALS['comment'] = $comment;
	?>
		<li id="comment-<?php comment_ID(); ?>" <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ); ?>>
			<article id="div-comment-<?php comment_ID(); ?>" class="comment-body">

				<footer class="comment-meta">
					<div class="comment-author vcard">
						<?php if ( 0 != $args['avatar_size'] ) { echo get_avatar( $comment, $args['avatar_size'] );} ?>

						<?php printf( '<cite class="fn">%s</cite>', get_comment_author_link() ); ?>

						<?php
						if ( get_option( 'comment_registration' ) && edd_has_user_purchased( $comment->user_id, $post->ID ) ) :
						?>
						<a class="button purchased"><?php _e( 'Purchased', 'marketify' ); ?></a>
						<?php endif; ?>
					</div><!-- .comment-author -->
				</footer><!-- .comment-meta -->

				<div class="comment-content">
					<div class="comment-metadata">
						<?php printf( '<cite class="fn">%s</cite>', get_comment_author_link() ); ?>

						<?php if ( get_comment_meta( $comment->comment_ID, 'edd_rating', true ) ) : ?>
							<?php do_action( 'marketify_edd_rating', $comment ); ?>
						<?php endif; ?>

						<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
							<time datetime="<?php comment_time( 'c' ); ?>">
								<?php printf( _x( '%1$s at %2$s', '1: date, 2: time', 'marketify' ), get_comment_date(), get_comment_time() ); ?>
							</time>
						</a>

						<?php
							comment_reply_link( array_merge( $args, array(
								'add_below' => 'div-comment',
								'depth'     => $depth,
								'max_depth' => $args['max_depth'],
								'before'    => '<span class="reply-link"> &mdash; ',
								'after'     => '</span>',
							) ) );
						?>

						<?php edit_comment_link( __( 'Edit', 'marketify' ), ' &mdash; <span class="edit-link">', '</span>' ); ?>
					</div><!-- .comment-metadata -->

					<?php if ( '0' == $comment->comment_approved ) : ?>
					<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'marketify' ); ?></p>
					<?php endif; ?>

					<?php comment_text(); ?>
				</div><!-- .comment-content -->

			</article><!-- .comment-body -->

		<?php
	}

}
