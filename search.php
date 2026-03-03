<?php
/**
 * Search Results Template
 *
 * @package PressGrid
 */

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
		<header class="pg-search-header">
			<h1 class="pg-archive-title">
				<?php printf( esc_html__( 'Search Results for: %s', 'pressgrid' ), '<span>' . esc_html( get_search_query() ) . '</span>' ); ?>
			</h1>
			<?php if ( have_posts() ) : global $wp_query; ?>
				<p class="pg-search-count">
					<?php printf( esc_html( _n( '%d result found', '%d results found', (int) $wp_query->found_posts, 'pressgrid' ) ), (int) $wp_query->found_posts ); ?>
				</p>
			<?php endif; ?>
		</header>

		<?php if ( have_posts() ) : ?>
			<div class="pg-grid-3">
				<?php while ( have_posts() ) : the_post();
					get_template_part( 'template-parts/content/post-card' );
				endwhile; ?>
			</div>
			<?php pressgrid_pagination(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'No results found. Try a different search term.', 'pressgrid' ); ?></p>
			<?php get_search_form(); ?>
		<?php endif; ?>
	</div>

	<?php if ( ! $full ) : ?>
		<aside class="pg-sidebar" aria-label="<?php esc_attr_e( 'Sidebar', 'pressgrid' ); ?>">
			<?php get_sidebar(); ?>
		</aside>
	<?php endif; ?>
</div>
<?php get_footer();
