<?php
/**
 * Archive Template
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

	<div class="pg-main-col">
		<?php pressgrid_breadcrumbs(); ?>

		<header class="pg-archive-header">
			<?php
			the_archive_title( '<h1 class="pg-archive-title">', '</h1>' );
			the_archive_description( '<div class="pg-archive-description">', '</div>' );
			?>
			<?php
			if ( is_category() ) {
				$cat_id    = get_queried_object_id();
				$cat_image = get_term_meta( $cat_id, 'category_image', true );
				if ( $cat_image ) {
					echo '<div class="pg-cat-image"><img src="' . esc_url( $cat_image ) . '" alt="' . esc_attr( single_cat_title( '', false ) ) . '" loading="lazy" decoding="async" /></div>';
				}
			}
			?>
		</header>

		<?php if ( have_posts() ) : ?>

			<div class="pg-grid-3">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/content/post-card' );
				endwhile;
				?>
			</div>

			<?php pressgrid_pagination(); ?>

		<?php else : ?>
			<?php get_template_part( 'template-parts/content/none' ); ?>
		<?php endif; ?>

	</div>

	<?php if ( ! $full ) : ?>
		<aside class="pg-sidebar" aria-label="<?php esc_attr_e( 'Sidebar', 'pressgrid' ); ?>">
			<?php pressgrid_render_ad( 'sidebar_top' ); ?>
			<?php get_sidebar(); ?>
		</aside>
	<?php endif; ?>

</div>

<?php
get_footer();
