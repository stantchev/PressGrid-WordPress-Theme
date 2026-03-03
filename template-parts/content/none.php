<?php
/**
 * No Content Template Part
 *
 * @package PressGrid
 */

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="pg-no-results">
	<h2><?php esc_html_e( 'Nothing Found', 'pressgrid' ); ?></h2>
	<p>
		<?php
		if ( is_search() ) {
			esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'pressgrid' );
		} else {
			esc_html_e( 'It seems we cannot find what you are looking for. Perhaps searching can help.', 'pressgrid' );
		}
		?>
	</p>
	<?php get_search_form(); ?>
</div>
