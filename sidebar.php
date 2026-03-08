<?php
/**
 * Sidebar — Newspaper style: Weather + Ad + Widgets + Newsletter + Ad
 *
 * @package PressGrid
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<aside class="pg-sidebar" role="complementary" aria-label="<?php esc_attr_e( 'Sidebar', 'pressgrid' ); ?>">

	<!-- Weather widget -->
	<?php pressgrid_render_weather_widget(); ?>

	<!-- Ad zone top -->
	<div class="pg-widget-area pg-ad-zone">
		<span class="pg-ad-label"><?php esc_html_e( 'Реклама', 'pressgrid' ); ?></span>
		<?php pressgrid_render_ad( 'sidebar_top' ); ?>
	</div>

	<!-- Dynamic sidebar widgets -->
	<?php if ( is_active_sidebar( 'sidebar-1' ) ) :
		dynamic_sidebar( 'sidebar-1' );
	endif; ?>

	<!-- Newsletter widget -->
	<div class="pg-widget-area pg-sidebar-newsletter">
		<h3 class="pg-widget-title"><?php esc_html_e( 'Абонирайте се за бюлетина', 'pressgrid' ); ?></h3>
		<div class="pg-nl-input-row">
			<label for="pg-sidebar-email" class="screen-reader-text"><?php esc_html_e( 'Имейл адрес', 'pressgrid' ); ?></label>
			<input type="email" id="pg-sidebar-email" placeholder="<?php esc_attr_e( 'Вашият имейл', 'pressgrid' ); ?>" autocomplete="email" />
			<button type="button" class="pg-nl-arrow" aria-label="<?php esc_attr_e( 'Изпрати', 'pressgrid' ); ?>">&#9654;</button>
		</div>
		<button type="button" class="pg-btn-subscribe-full"><?php esc_html_e( 'Абониране', 'pressgrid' ); ?></button>
	</div>

	<!-- Ad zone middle -->
	<?php pressgrid_render_ad( 'sidebar_middle' ); ?>

	<?php if ( is_active_sidebar( 'sidebar-2' ) ) :
		dynamic_sidebar( 'sidebar-2' );
	endif; ?>

</aside>
