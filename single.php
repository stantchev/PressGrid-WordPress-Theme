<?php
/**
 * Single Post Template
 *
 * @package PressGrid
 */

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$sidebar = sanitize_key( get_theme_mod( 'pressgrid_sidebar_position', 'right' ) );
$full    = ( 'none' === $sidebar );
?>

<div class="pg-content-area <?php echo $full ? 'full-width' : ''; ?>">

	<article class="pg-main-col" id="pg-post-<?php the_ID(); ?>" <?php post_class(); ?>>

		<?php pressgrid_breadcrumbs(); ?>

		<?php while ( have_posts() ) : the_post(); ?>

			<header class="pg-single-header">
				<?php echo wp_kses_post( pressgrid_get_category_label( get_the_ID() ) ); ?>
				<h1 class="pg-single-title"><?php the_title(); ?></h1>
				<div class="pg-single-meta">
					<?php pressgrid_post_meta( get_the_ID() ); ?>
				</div>
			</header>

			<?php if ( has_post_thumbnail() ) : ?>
				<div class="pg-featured-image">
					<?php
					the_post_thumbnail(
						'pressgrid-wide',
						array(
							'loading' => 'eager',
							'fetchpriority' => 'high',
							'decoding' => 'async',
							'alt'     => esc_attr( get_the_title() ),
						)
					);
					?>
				</div>
			<?php endif; ?>

			<?php pressgrid_render_ad( 'in_article' ); ?>

			<div class="pg-entry-content">
				<?php the_content(); ?>
				<?php
				wp_link_pages(
					array(
						'before'      => '<div class="pg-page-links"><span>' . esc_html__( 'Pages:', 'pressgrid' ) . '</span>',
						'after'       => '</div>',
						'link_before' => '<span>',
						'link_after'  => '</span>',
					)
				);
				?>
			</div>

			<?php
			$tags = get_the_tags();
			if ( $tags && ! is_wp_error( $tags ) ) :
				?>
				<div class="pg-post-tags">
					<span><?php esc_html_e( 'Tags:', 'pressgrid' ); ?></span>
					<?php foreach ( $tags as $tag ) : ?>
						<a class="pg-tag" href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>">
							<?php echo esc_html( $tag->name ); ?>
						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php
			// Author box.
			$author_id  = (int) get_the_author_meta( 'ID' );
			$author_bio = get_the_author_meta( 'description', $author_id );
			if ( $author_bio ) :
				?>
				<div class="pg-author-box">
					<div class="pg-author-avatar">
						<?php echo get_avatar( $author_id, 80, '', esc_attr( get_the_author_meta( 'display_name', $author_id ) ) ); ?>
					</div>
					<div class="pg-author-info">
						<p class="pg-author-name">
							<a href="<?php echo esc_url( get_author_posts_url( $author_id ) ); ?>">
								<?php echo esc_html( get_the_author_meta( 'display_name', $author_id ) ); ?>
							</a>
						</p>
						<p class="pg-author-bio"><?php echo esc_html( $author_bio ); ?></p>
					</div>
				</div>
			<?php endif; ?>

			<?php
			the_post_navigation(
				array(
					'prev_text' => '<span class="nav-subtitle">' . esc_html__( 'Previous:', 'pressgrid' ) . '</span> <span class="nav-title">%title</span>',
					'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Next:', 'pressgrid' ) . '</span> <span class="nav-title">%title</span>',
				)
			);
			?>

		<?php endwhile; ?>

		<?php comments_template(); ?>

	</article>

	<?php if ( ! $full ) : ?>
		<aside class="pg-sidebar" aria-label="<?php esc_attr_e( 'Sidebar', 'pressgrid' ); ?>">
			<?php pressgrid_render_ad( 'sidebar_top' ); ?>
			<?php get_sidebar(); ?>
			<?php pressgrid_render_ad( 'sidebar_middle' ); ?>
		</aside>
	<?php endif; ?>

</div>

<?php
get_footer();
