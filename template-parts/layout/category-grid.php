<?php
/**
 * Category Grid Section
 *
 * @package PressGrid
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$args       = isset( $args ) ? (array) $args : array();
$layout     = sanitize_key( $args['layout']     ?? 'grid-4' );
$category   = absint(       $args['category']   ?? 0 );
$post_count = absint(       $args['post_count'] ?? 4 );

$query = pressgrid_get_category_posts( $category, $post_count );
if ( ! $query->have_posts() ) { return; }

$grid_map   = array( 'grid-2' => 'pg-grid-2', 'grid-3' => 'pg-grid-3', 'grid-4' => 'pg-grid-4' );
$grid_class = $grid_map[ $layout ] ?? 'pg-grid-4';
$title      = $category > 0 ? get_cat_name( $category ) : esc_html__( 'Latest News', 'pressgrid' );
$link       = $category > 0 ? get_category_link( $category ) : '';
?>
<section class="pg-section" aria-label="<?php echo esc_attr( $title ); ?>">
	<div class="pg-section-header">
		<h2 class="pg-section-title"><?php echo esc_html( $title ); ?></h2>
		<?php if ( $link ) : ?>
			<a class="pg-section-link" href="<?php echo esc_url( $link ); ?>"><?php esc_html_e( 'View All', 'pressgrid' ); ?> &rarr;</a>
		<?php endif; ?>
	</div>
	<div class="<?php echo esc_attr( $grid_class ); ?>">
		<?php while ( $query->have_posts() ) : $query->the_post();
			get_template_part( 'template-parts/content/post-card' );
		endwhile; wp_reset_postdata(); ?>
	</div>
</section>
