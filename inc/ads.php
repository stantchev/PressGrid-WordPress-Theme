<?php
/**
 * PressGrid Advertisement System
 *
 * @package PressGrid
 */

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Allowed HTML tags for ad zones.
 *
 * @return array
 */
function pressgrid_ads_allowed_html() {
	return array(
		'a'      => array(
			'href'   => true,
			'target' => true,
			'rel'    => true,
			'title'  => true,
			'class'  => true,
			'id'     => true,
		),
		'img'    => array(
			'src'     => true,
			'alt'     => true,
			'width'   => true,
			'height'  => true,
			'class'   => true,
			'loading' => true,
			'decoding' => true,
		),
		'ins'    => array(
			'class'               => true,
			'style'               => true,
			'data-ad-client'      => true,
			'data-ad-slot'        => true,
			'data-ad-format'      => true,
			'data-full-width-responsive' => true,
		),
		'script' => array(
			'async'       => true,
			'src'         => true,
			'crossorigin' => true,
		),
		'div'    => array(
			'class' => true,
			'id'    => true,
			'style' => true,
		),
		'iframe' => array(
			'src'             => true,
			'width'           => true,
			'height'          => true,
			'frameborder'     => true,
			'scrolling'       => true,
			'allowfullscreen' => true,
			'loading'         => true,
			'title'           => true,
		),
	);
}

/**
 * Get all ad zones with their labels.
 *
 * @return array
 */
function pressgrid_get_ad_zones() {
	return array(
		'header'          => esc_html__( 'Header Ad', 'pressgrid' ),
		'sidebar_top'     => esc_html__( 'Sidebar Top', 'pressgrid' ),
		'sidebar_middle'  => esc_html__( 'Sidebar Middle', 'pressgrid' ),
		'in_article'      => esc_html__( 'In-Article Ad', 'pressgrid' ),
		'between_posts'   => esc_html__( 'Between Posts', 'pressgrid' ),
		'footer'          => esc_html__( 'Footer Ad', 'pressgrid' ),
	);
}

/**
 * Register Ads settings page.
 */
function pressgrid_ads_menu() {
	add_theme_page(
		esc_html__( 'Theme Ads', 'pressgrid' ),
		esc_html__( 'Theme Ads', 'pressgrid' ),
		'manage_options',
		'pressgrid-ads',
		'pressgrid_ads_page'
	);
}
add_action( 'admin_menu', 'pressgrid_ads_menu' );

/**
 * Save ad settings.
 */
function pressgrid_save_ads() {
	if ( ! isset( $_POST['pressgrid_ads_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['pressgrid_ads_nonce'] ) ), 'pressgrid_save_ads' ) ) {
		wp_die( esc_html__( 'Security check failed.', 'pressgrid' ) );
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Insufficient permissions.', 'pressgrid' ) );
	}

	$zones  = pressgrid_get_ad_zones();
	$ads    = array();

	foreach ( array_keys( $zones ) as $zone ) {
		$zone = sanitize_key( $zone );
		$ads[ $zone ] = array(
			'enabled'       => isset( $_POST['ads'][ $zone ]['enabled'] ) ? 1 : 0,
			'html'          => isset( $_POST['ads'][ $zone ]['html'] )
				? wp_kses( wp_unslash( $_POST['ads'][ $zone ]['html'] ), pressgrid_ads_allowed_html() )
				: '',
			'show_desktop'  => isset( $_POST['ads'][ $zone ]['show_desktop'] ) ? 1 : 0,
			'show_mobile'   => isset( $_POST['ads'][ $zone ]['show_mobile'] ) ? 1 : 0,
			'async_load'    => isset( $_POST['ads'][ $zone ]['async_load'] ) ? 1 : 0,
		);
	}

	update_option( 'pressgrid_ads', $ads );
	add_settings_error( 'pressgrid_ads', 'saved', esc_html__( 'Ad settings saved.', 'pressgrid' ), 'success' );
}
add_action( 'admin_post_pressgrid_save_ads', 'pressgrid_save_ads' );

/**
 * Ads admin page output.
 */
function pressgrid_ads_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to access this page.', 'pressgrid' ) );
	}

	settings_errors( 'pressgrid_ads' );

	$zones    = pressgrid_get_ad_zones();
	$saved    = get_option( 'pressgrid_ads', array() );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'PressGrid Ad Zones', 'pressgrid' ); ?></h1>
		<p><?php esc_html_e( 'Manage advertisement zones. Paste ad code (HTML/JS) for each zone. Use wp_kses-safe code only.', 'pressgrid' ); ?></p>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="pressgrid_save_ads" />
			<?php wp_nonce_field( 'pressgrid_save_ads', 'pressgrid_ads_nonce' ); ?>
			<?php foreach ( $zones as $zone_id => $zone_label ) :
				$zone_data = isset( $saved[ $zone_id ] ) ? $saved[ $zone_id ] : array();
				$enabled      = ! empty( $zone_data['enabled'] );
				$html         = isset( $zone_data['html'] ) ? $zone_data['html'] : '';
				$show_desktop = isset( $zone_data['show_desktop'] ) ? (bool) $zone_data['show_desktop'] : true;
				$show_mobile  = isset( $zone_data['show_mobile'] ) ? (bool) $zone_data['show_mobile'] : true;
				$async_load   = ! empty( $zone_data['async_load'] );
				?>
				<div style="background:#fff;border:1px solid #ddd;padding:20px;margin-bottom:20px;border-radius:4px;">
					<h2 style="margin-top:0;"><?php echo esc_html( $zone_label ); ?></h2>
					<table class="form-table">
						<tr>
							<th><?php esc_html_e( 'Enable Zone', 'pressgrid' ); ?></th>
							<td>
								<label>
									<input type="checkbox" name="ads[<?php echo esc_attr( $zone_id ); ?>][enabled]" value="1" <?php checked( $enabled ); ?> />
									<?php esc_html_e( 'Enable this ad zone', 'pressgrid' ); ?>
								</label>
							</td>
						</tr>
						<tr>
							<th><label for="ad_html_<?php echo esc_attr( $zone_id ); ?>"><?php esc_html_e( 'Ad Code (HTML)', 'pressgrid' ); ?></label></th>
							<td>
								<textarea
									name="ads[<?php echo esc_attr( $zone_id ); ?>][html]"
									id="ad_html_<?php echo esc_attr( $zone_id ); ?>"
									rows="6"
									style="width:100%;font-family:monospace;"
								><?php echo esc_textarea( $html ); ?></textarea>
								<p class="description"><?php esc_html_e( 'Paste ad HTML or image code. Scripts are sanitized.', 'pressgrid' ); ?></p>
							</td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Visibility', 'pressgrid' ); ?></th>
							<td>
								<label>
									<input type="checkbox" name="ads[<?php echo esc_attr( $zone_id ); ?>][show_desktop]" value="1" <?php checked( $show_desktop ); ?> />
									<?php esc_html_e( 'Show on Desktop', 'pressgrid' ); ?>
								</label>
								&nbsp;&nbsp;
								<label>
									<input type="checkbox" name="ads[<?php echo esc_attr( $zone_id ); ?>][show_mobile]" value="1" <?php checked( $show_mobile ); ?> />
									<?php esc_html_e( 'Show on Mobile', 'pressgrid' ); ?>
								</label>
							</td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Options', 'pressgrid' ); ?></th>
							<td>
								<label>
									<input type="checkbox" name="ads[<?php echo esc_attr( $zone_id ); ?>][async_load]" value="1" <?php checked( $async_load ); ?> />
									<?php esc_html_e( 'Async Load (wrap in lazy observer)', 'pressgrid' ); ?>
								</label>
							</td>
						</tr>
					</table>
				</div>
			<?php endforeach; ?>
			<?php submit_button( esc_html__( 'Save Ad Settings', 'pressgrid' ) ); ?>
		</form>
	</div>
	<?php
}

/**
 * Render an ad zone on the front end.
 *
 * @param string $zone_id Zone identifier.
 */
function pressgrid_render_ad( $zone_id ) {
	$zone_id = sanitize_key( $zone_id );
	$ads     = get_option( 'pressgrid_ads', array() );

	if ( empty( $ads[ $zone_id ]['enabled'] ) ) {
		return;
	}

	$zone_data    = $ads[ $zone_id ];
	$html         = isset( $zone_data['html'] ) ? $zone_data['html'] : '';
	$show_desktop = ! empty( $zone_data['show_desktop'] );
	$show_mobile  = ! empty( $zone_data['show_mobile'] );
	$async_load   = ! empty( $zone_data['async_load'] );

	if ( ! $html ) {
		return;
	}

	// Build visibility CSS class.
	$visibility_class = '';
	if ( $show_desktop && ! $show_mobile ) {
		$visibility_class = 'pg-ad-desktop-only';
	} elseif ( ! $show_desktop && $show_mobile ) {
		$visibility_class = 'pg-ad-mobile-only';
	}

	echo '<div class="pg-ad-zone ' . esc_attr( $visibility_class ) . '" data-zone="' . esc_attr( $zone_id ) . '">';
	echo '<p class="pg-ad-label">' . esc_html__( 'Advertisement', 'pressgrid' ) . '</p>';

	if ( $async_load ) {
		echo '<div class="pg-ad-lazy" data-ad-content="1">';
		// Output a data attribute to be populated by JS for truly async loading.
		echo wp_kses( $html, pressgrid_ads_allowed_html() );
		echo '</div>';
	} else {
		echo wp_kses( $html, pressgrid_ads_allowed_html() );
	}

	echo '</div>';
}

/**
 * Add responsive CSS for ad visibility.
 */
function pressgrid_ads_visibility_css() {
	$css = '@media(max-width:768px){.pg-ad-desktop-only{display:none!important}}';
	$css .= '@media(min-width:769px){.pg-ad-mobile-only{display:none!important}}';
	wp_add_inline_style( 'pressgrid-style', $css );
}
add_action( 'wp_enqueue_scripts', 'pressgrid_ads_visibility_css', 25 );
