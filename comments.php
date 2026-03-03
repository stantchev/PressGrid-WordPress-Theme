<?php
/**
 * Comments Template
 *
 * @package PressGrid
 */

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Do not output if post is password protected.
if ( post_password_required() ) {
	?>
	<p class="pg-comments-protected"><?php esc_html_e( 'This post is password protected. Enter the password to view any comments.', 'pressgrid' ); ?></p>
	<?php
	return;
}
?>

<section class="pg-comments" id="comments">

	<?php if ( have_comments() ) : ?>
		<h2 class="pg-comments-title">
			<?php
			$comments_number = get_comments_number();
			if ( '1' === $comments_number ) {
				/* translators: %s: post title */
				printf( esc_html__( 'One thought on &ldquo;%s&rdquo;', 'pressgrid' ), get_the_title() );
			} else {
				printf(
					/* translators: 1: number of comments, 2: post title */
					esc_html( _nx( '%1$s thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', $comments_number, 'comments title', 'pressgrid' ) ),
					esc_html( number_format_i18n( $comments_number ) ),
					esc_html( get_the_title() )
				);
			}
			?>
		</h2>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
			<nav class="pg-comment-nav" aria-label="<?php esc_attr_e( 'Comment navigation', 'pressgrid' ); ?>">
				<div class="nav-previous"><?php previous_comments_link( esc_html__( 'Older Comments', 'pressgrid' ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( esc_html__( 'Newer Comments', 'pressgrid' ) ); ?></div>
			</nav>
		<?php endif; ?>

		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'      => 'ol',
					'short_ping' => true,
					'avatar_size' => 48,
					'callback'   => 'pressgrid_comment_callback',
				)
			);
			?>
		</ol>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
			<nav class="pg-comment-nav" aria-label="<?php esc_attr_e( 'Comment navigation', 'pressgrid' ); ?>">
				<div class="nav-previous"><?php previous_comments_link( esc_html__( 'Older Comments', 'pressgrid' ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( esc_html__( 'Newer Comments', 'pressgrid' ) ); ?></div>
			</nav>
		<?php endif; ?>

	<?php endif; ?>

	<?php
	comment_form(
		array(
			'title_reply_before' => '<h3 id="reply-title" class="comment-reply-title">',
			'title_reply_after'  => '</h3>',
			'class_submit'       => 'pg-btn',
			'comment_notes_before' => '',
		)
	);
	?>

</section>

<?php
/**
 * Custom comment callback.
 *
 * @param WP_Comment $comment Comment object.
 * @param array      $args    Comment arguments.
 * @param int        $depth   Comment depth.
 */
function pressgrid_comment_callback( $comment, $args, $depth ) {
	$tag = ( 'div' === $args['style'] ) ? 'div' : 'li';
	?>
	<<?php echo esc_html( $tag ); ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( 'comment', $comment ); ?>>
		<article class="comment-body">
			<footer class="comment-meta">
				<div class="comment-author">
					<?php echo get_avatar( $comment, $args['avatar_size'] ); ?>
					<b class="fn"><?php comment_author_link( $comment ); ?></b>
				</div>
				<div class="comment-metadata">
					<time datetime="<?php comment_time( 'c' ); ?>">
						<?php comment_date(); ?> <?php esc_html_e( 'at', 'pressgrid' ); ?> <?php comment_time(); ?>
					</time>
				</div>
			</footer>

			<div class="comment-content">
				<?php if ( '0' === $comment->comment_approved ) : ?>
					<p class="comment-awaiting-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'pressgrid' ); ?></p>
				<?php endif; ?>
				<?php comment_text(); ?>
			</div>

			<div class="reply">
				<?php
				comment_reply_link(
					array_merge(
						$args,
						array(
							'add_below' => 'comment',
							'depth'     => $depth,
							'max_depth' => $args['max_depth'],
							'before'    => '<span class="comment-reply-link">',
							'after'     => '</span>',
						)
					)
				);
				?>
			</div>
		</article>
	<?php
}
