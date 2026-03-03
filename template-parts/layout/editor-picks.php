<?php
/**
 * Editor Picks — standalone fallback (used if trending section disabled)
 *
 * @package PressGrid
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$args       = isset( $args ) ? (array) $args : array();
$layout     = sanitize_key( $args['layout']     ?? 'grid-4' );
$post_count = absint(       $args['post_count'] ?? 4 );
$query      = pressgrid_get_editor_picks( $post_count );

if ( ! $query->have_posts() ) { return; }

$grid_map   = array( 'grid-2' => 'pg-grid-2', 'grid-3' => 'pg-grid-3', 'grid-4' => 'pg-grid-4' );
$grid_class = $grid_map[ $layout ] ?? 'pg-grid-4';
?>
<section class="pg-section">
	<div class="pg-section-header">
		<h2 class="pg-section-title"><?php esc_html_e( "Editor's Picks", 'pressgrid' ); ?></h2>
	</div>
	<div class="<?php echo esc_attr( $grid_class ); ?>">
		<?php while ( $query->have_posts() ) : $query->the_post();
			get_template_part( 'template-parts/content/post-card' );
		endwhile; wp_reset_postdata(); ?>
	</div>
</section>
