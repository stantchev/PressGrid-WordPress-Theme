<?php
/**
 * 404 Not Found Template
 *
 * @package PressGrid
 */

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="pg-404">
	<h1>404</h1>
	<h2><?php esc_html_e( 'Page Not Found', 'pressgrid' ); ?></h2>
	<p><?php esc_html_e( "The page you're looking for doesn't exist or has been moved.", 'pressgrid' ); ?></p>
	<div class="pg-search-form">
		<?php get_search_form(); ?>
	</div>
	<p style="margin-top:1.5rem;">
		<a class="pg-btn" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php esc_html_e( '&larr; Back to Home', 'pressgrid' ); ?>
		</a>
	</p>
</div>

<?php
get_footer();
