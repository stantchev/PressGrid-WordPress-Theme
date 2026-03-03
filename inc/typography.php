<?php
/**
 * PressGrid Typography & Font Upload Admin
 *
 * @package PressGrid
 */

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add Font Upload submenu under Appearance.
 */
function pressgrid_typography_menu() {
	add_theme_page(
		esc_html__( 'Font Upload', 'pressgrid' ),
		esc_html__( 'Font Upload', 'pressgrid' ),
		'manage_options',
		'pressgrid-font-upload',
		'pressgrid_font_upload_page'
	);
}
add_action( 'admin_menu', 'pressgrid_typography_menu' );

/**
 * Font upload admin page.
 */
function pressgrid_font_upload_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to access this page.', 'pressgrid' ) );
	}

	$message = '';
	$error   = '';

	if (
		'POST' === $_SERVER['REQUEST_METHOD'] &&
		isset( $_POST['pressgrid_font_nonce'] ) &&
		wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['pressgrid_font_nonce'] ) ), 'pressgrid_font_upload' )
	) {
		if ( ! empty( $_FILES['pressgrid_font_file']['name'] ) ) {
			$result = pressgrid_handle_font_upload( $_FILES['pressgrid_font_file'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			if ( $result['success'] ) {
				$message = sprintf(
					/* translators: %s: font URL */
					esc_html__( 'Font uploaded successfully. URL: %s', 'pressgrid' ),
					esc_url( $result['url'] )
				);
				// Auto-save to customizer setting.
				set_theme_mod( 'pressgrid_font_url', esc_url_raw( $result['url'] ) );
			} else {
				$error = $result['error'];
			}
		} else {
			$error = esc_html__( 'No file was uploaded.', 'pressgrid' );
		}
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'PressGrid Font Upload', 'pressgrid' ); ?></h1>
		<p><?php esc_html_e( 'Upload a custom .woff2 font file (max 1MB). After uploading, set the Font Family Name in Appearance → Customize → Typography.', 'pressgrid' ); ?></p>

		<?php if ( $message ) : ?>
			<div class="notice notice-success"><p><?php echo esc_html( $message ); ?></p></div>
		<?php endif; ?>

		<?php if ( $error ) : ?>
			<div class="notice notice-error"><p><?php echo esc_html( $error ); ?></p></div>
		<?php endif; ?>

		<form method="post" enctype="multipart/form-data">
			<?php wp_nonce_field( 'pressgrid_font_upload', 'pressgrid_font_nonce' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="pressgrid_font_file"><?php esc_html_e( 'Font File (.woff2)', 'pressgrid' ); ?></label>
					</th>
					<td>
						<input type="file" name="pressgrid_font_file" id="pressgrid_font_file" accept=".woff2" />
						<p class="description"><?php esc_html_e( 'Only .woff2 files, maximum 1MB.', 'pressgrid' ); ?></p>
					</td>
				</tr>
			</table>
			<?php submit_button( esc_html__( 'Upload Font', 'pressgrid' ) ); ?>
		</form>

		<?php
		$current_url = esc_url( get_theme_mod( 'pressgrid_font_url', '' ) );
		if ( $current_url ) :
			?>
			<hr />
			<h2><?php esc_html_e( 'Current Font', 'pressgrid' ); ?></h2>
			<p><?php esc_html_e( 'URL:', 'pressgrid' ); ?> <code><?php echo esc_url( $current_url ); ?></code></p>
		<?php endif; ?>
	</div>
	<?php
}
