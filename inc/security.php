<?php
/**
 * PressGrid Security Hardening
 *
 * @package PressGrid
 */

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Remove WordPress version from head and feeds.
 */
function pressgrid_remove_wp_version() {
	return '';
}
add_filter( 'the_generator', 'pressgrid_remove_wp_version' );

/**
 * Remove version from scripts and styles.
 *
 * @param string $src Source URL.
 * @return string Cleaned URL.
 */
function pressgrid_remove_version_scripts_styles( $src ) {
	if ( strpos( $src, 'ver=' ) ) {
		$src = remove_query_arg( 'ver', $src );
	}
	return $src;
}
add_filter( 'style_loader_src', 'pressgrid_remove_version_scripts_styles', 9999 );
add_filter( 'script_loader_src', 'pressgrid_remove_version_scripts_styles', 9999 );

/**
 * Add security headers.
 *
 * @param array $headers Headers array.
 * @return array Modified headers.
 */
function pressgrid_security_headers( $headers ) {
	$headers['X-Content-Type-Options']    = 'nosniff';
	$headers['X-Frame-Options']           = 'SAMEORIGIN';
	$headers['X-XSS-Protection']         = '1; mode=block';
	$headers['Referrer-Policy']           = 'strict-origin-when-cross-origin';
	$headers['Permissions-Policy']        = 'geolocation=(), microphone=(), camera=()';
	$headers['X-Permitted-Cross-Domain-Policies'] = 'none';
	return $headers;
}
add_filter( 'wp_headers', 'pressgrid_security_headers' );

/**
 * Disable XML-RPC if option is set.
 */
function pressgrid_maybe_disable_xmlrpc() {
	if ( get_option( 'pressgrid_disable_xmlrpc' ) ) {
		add_filter( 'xmlrpc_enabled', '__return_false' );
	}
}
add_action( 'init', 'pressgrid_maybe_disable_xmlrpc' );

/**
 * Add XML-RPC toggle to Security settings page (registered in layout-builder).
 * Store option securely.
 */
function pressgrid_save_security_settings() {
	if ( ! isset( $_POST['pressgrid_security_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['pressgrid_security_nonce'] ) ), 'pressgrid_security_save' ) ) {
		wp_die( esc_html__( 'Security check failed.', 'pressgrid' ) );
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Insufficient permissions.', 'pressgrid' ) );
	}
	$disable_xmlrpc = isset( $_POST['pressgrid_disable_xmlrpc'] ) ? 1 : 0;
	update_option( 'pressgrid_disable_xmlrpc', absint( $disable_xmlrpc ) );
}
add_action( 'admin_post_pressgrid_save_security', 'pressgrid_save_security_settings' );

/**
 * Disallow user enumeration via /?author=N redirects.
 */
function pressgrid_block_author_scan() {
	if ( ! is_admin() && isset( $_GET['author'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'pressgrid_block_author_scan' );

/**
 * Validate and handle secure font upload.
 *
 * @param array $file $_FILES array entry.
 * @return array Result with 'success', 'url', 'error' keys.
 */
function pressgrid_handle_font_upload( $file ) {
	// Check user capability.
	if ( ! current_user_can( 'manage_options' ) ) {
		return array( 'success' => false, 'error' => esc_html__( 'Insufficient permissions.', 'pressgrid' ) );
	}

	// Check file extension.
	$file_name = isset( $file['name'] ) ? sanitize_file_name( $file['name'] ) : '';
	if ( ! preg_match( '/\.woff2$/i', $file_name ) ) {
		return array( 'success' => false, 'error' => esc_html__( 'Only .woff2 files are allowed.', 'pressgrid' ) );
	}

	// Check file size (1MB max).
	$max_size = 1 * 1024 * 1024;
	if ( isset( $file['size'] ) && (int) $file['size'] > $max_size ) {
		return array( 'success' => false, 'error' => esc_html__( 'File must be under 1MB.', 'pressgrid' ) );
	}

	// Validate MIME type by reading file signature.
	$tmp_path = isset( $file['tmp_name'] ) ? $file['tmp_name'] : '';
	if ( ! $tmp_path || ! is_uploaded_file( $tmp_path ) ) {
		return array( 'success' => false, 'error' => esc_html__( 'Invalid upload.', 'pressgrid' ) );
	}

	// woff2 magic bytes: 77 4F 46 32.
	$handle = fopen( $tmp_path, 'rb' ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	if ( ! $handle ) {
		return array( 'success' => false, 'error' => esc_html__( 'Could not read file.', 'pressgrid' ) );
	}
	$magic = fread( $handle, 4 ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	fclose( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions

	if ( "\x77\x4F\x46\x32" !== $magic ) {
		return array( 'success' => false, 'error' => esc_html__( 'Invalid WOFF2 file signature.', 'pressgrid' ) );
	}

	// Generate safe filename.
	$safe_name = 'pressgrid-font-' . substr( md5( uniqid( '', true ) ), 0, 8 ) . '.woff2';

	// Get upload directory.
	$upload_dir = wp_upload_dir();
	$dest_dir   = trailingslashit( $upload_dir['basedir'] ) . 'pressgrid-fonts/';
	$dest_url   = trailingslashit( $upload_dir['baseurl'] ) . 'pressgrid-fonts/';

	// Create directory if not exists.
	if ( ! wp_mkdir_p( $dest_dir ) ) {
		return array( 'success' => false, 'error' => esc_html__( 'Could not create font directory.', 'pressgrid' ) );
	}

	// Write .htaccess to only allow woff2.
	$htaccess = $dest_dir . '.htaccess';
	if ( ! file_exists( $htaccess ) ) {
		file_put_contents( // phpcs:ignore WordPress.WP.AlternativeFunctions
			$htaccess,
			"Order Deny,Allow\nDeny from all\n<Files ~ \"\\.woff2$\">\nAllow from all\n</Files>"
		);
	}

	$dest_file = $dest_dir . $safe_name;
	if ( ! move_uploaded_file( $tmp_path, $dest_file ) ) {
		return array( 'success' => false, 'error' => esc_html__( 'Could not save file.', 'pressgrid' ) );
	}

	return array(
		'success' => true,
		'url'     => esc_url( $dest_url . $safe_name ),
	);
}

/**
 * Limit login attempts feedback (remove user enumeration from login errors).
 *
 * @return string Generic error.
 */
function pressgrid_login_errors() {
	return esc_html__( 'Login failed. Please check your credentials and try again.', 'pressgrid' );
}
add_filter( 'login_errors', 'pressgrid_login_errors' );
