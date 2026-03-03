<?php
/**
 * Page Template
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

	<article class="pg-main-col" id="pg-page-<?php the_ID(); ?>" <?php post_class(); ?>>
		<?php pressgrid_breadcrumbs(); ?>

		<?php while ( have_posts() ) : the_post(); ?>

			<header class="pg-single-header">
				<h1 class="pg-single-title"><?php the_title(); ?></h1>
			</header>

			<?php if ( has_post_thumbnail() ) : ?>
				<div class="pg-featured-image">
					<?php
					the_post_thumbnail(
						'pressgrid-wide',
						array(
							'loading'       => 'eager',
							'fetchpriority' => 'high',
							'decoding'      => 'async',
							'alt'           => esc_attr( get_the_title() ),
						)
					);
					?>
				</div>
			<?php endif; ?>

			<div class="pg-entry-content">
				<?php the_content(); ?>
				<?php
				wp_link_pages(
					array(
						'before' => '<div class="pg-page-links"><span>' . esc_html__( 'Pages:', 'pressgrid' ) . '</span>',
						'after'  => '</div>',
					)
				);
				?>
			</div>

		<?php endwhile; ?>

		<?php if ( comments_open() || get_comments_number() ) : ?>
			<?php comments_template(); ?>
		<?php endif; ?>

	</article>

	<?php if ( ! $full ) : ?>
		<aside class="pg-sidebar" aria-label="<?php esc_attr_e( 'Sidebar', 'pressgrid' ); ?>">
			<?php get_sidebar(); ?>
		</aside>
	<?php endif; ?>

</div>

<?php
get_footer();
