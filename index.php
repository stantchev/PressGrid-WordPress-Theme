<?php
/**
 * Main Template File
 *
 * This is the most generic template file in a WordPress theme.
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

		<?php if ( have_posts() ) : ?>

			<div class="pg-section-header">
				<h1 class="pg-section-title">
					<?php
					if ( is_home() && ! is_front_page() ) {
						single_post_title();
					} else {
						esc_html_e( 'Latest Posts', 'pressgrid' );
					}
					?>
				</h1>
			</div>

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
	</div><!-- .pg-main-col -->

	<?php if ( ! $full ) : ?>
		<aside class="pg-sidebar" aria-label="<?php esc_attr_e( 'Sidebar', 'pressgrid' ); ?>">
			<?php get_sidebar(); ?>
		</aside>
	<?php endif; ?>

</div><!-- .pg-content-area -->

<?php
get_footer();
