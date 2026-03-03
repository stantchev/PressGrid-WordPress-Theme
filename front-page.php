<?php
/**
 * Front Page Template — driven by Layout Builder
 *
 * Homepage sections rendered in order:
 * hero → latest_posts (with sidebar) → trending → opinion → newsletter
 *
 * @package PressGrid
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();

$sections = pressgrid_get_sections();
$sidebar_pos = sanitize_key( get_theme_mod( 'pressgrid_sidebar_position', 'right' ) );

// Sections that need the sidebar wrapper
$sidebar_sections = array( 'latest_posts', 'category_grid' );

foreach ( $sections as $section ) {
	if ( empty( $section['enabled'] ) ) { continue; }

	$sid        = sanitize_key( $section['id'] );
	$layout     = sanitize_key( $section['layout']     ?? 'grid-3' );
	$category   = absint(       $section['category']   ?? 0 );
	$post_count = absint(       $section['post_count'] ?? 6 );

	// Latest news & category grid get sidebar layout
	if ( in_array( $sid, $sidebar_sections, true ) && 'none' !== $sidebar_pos ) :
		$content_class = 'right' === $sidebar_pos ? 'pg-content-area' : 'pg-content-area pg-content-left';
		?>
		<div class="<?php echo esc_attr( $content_class ); ?>">
			<div class="pg-main-col">
				<?php get_template_part( 'template-parts/layout/' . str_replace( '_', '-', $sid ), null, array(
					'layout'     => $layout,
					'category'   => $category,
					'post_count' => $post_count,
				) ); ?>
			</div>
			<?php get_sidebar(); ?>
		</div>
		<?php
		continue;
	endif;

	switch ( $sid ) {
		case 'hero':
			get_template_part( 'template-parts/layout/hero', null, array( 'post_count' => $post_count ) );
			break;
		case 'latest_posts':
		case 'category_grid':
			// sidebar disabled = full width
			get_template_part( 'template-parts/layout/' . str_replace( '_', '-', $sid ), null, array(
				'layout' => $layout, 'category' => $category, 'post_count' => $post_count,
			) );
			break;
		case 'trending':
			get_template_part( 'template-parts/layout/trending', null, array( 'post_count' => $post_count ) );
			break;
		case 'editor_picks':
			get_template_part( 'template-parts/layout/editor-picks', null, array( 'layout' => $layout, 'post_count' => $post_count ) );
			break;
		case 'opinion':
			get_template_part( 'template-parts/layout/opinion', null, array( 'category' => $category, 'post_count' => $post_count ) );
			break;
		case 'newsletter':
			get_template_part( 'template-parts/layout/newsletter' );
			break;
		case 'custom_html':
			if ( ! empty( $section['custom_html'] ) ) {
				echo '<div class="pg-section">' . wp_kses_post( $section['custom_html'] ) . '</div>';
			}
			break;
		case 'ad_block':
			pressgrid_render_ad( 'between_posts' );
			break;
	}
}

get_footer();
