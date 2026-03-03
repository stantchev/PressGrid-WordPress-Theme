<?php
/**
 * PressGrid Customizer Settings
 *
 * @package PressGrid
 */

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Customizer settings, sections, and controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager object.
 */
function pressgrid_customize_register( $wp_customize ) {

	// ─── PANEL: Colors ────────────────────────────────────────────────────────
	$wp_customize->add_panel(
		'pressgrid_colors_panel',
		array(
			'title'    => esc_html__( 'PressGrid: Colors', 'pressgrid' ),
			'priority' => 30,
		)
	);

	$wp_customize->add_section(
		'pressgrid_colors',
		array(
			'title'    => esc_html__( 'Theme Colors', 'pressgrid' ),
			'panel'    => 'pressgrid_colors_panel',
			'priority' => 10,
		)
	);

	$colors = array(
		'pressgrid_primary_color'    => array(
			'label'   => esc_html__( 'Primary Color', 'pressgrid' ),
			'default' => '#1a73e8',
		),
		'pressgrid_secondary_color'  => array(
			'label'   => esc_html__( 'Secondary Color', 'pressgrid' ),
			'default' => '#0d47a1',
		),
		'pressgrid_accent_color'     => array(
			'label'   => esc_html__( 'Accent Color', 'pressgrid' ),
			'default' => '#e91e63',
		),
		'pressgrid_bg_color'         => array(
			'label'   => esc_html__( 'Background Color', 'pressgrid' ),
			'default' => '#ffffff',
		),
		'pressgrid_text_color'       => array(
			'label'   => esc_html__( 'Text Color', 'pressgrid' ),
			'default' => '#212121',
		),
		'pressgrid_link_hover_color' => array(
			'label'   => esc_html__( 'Link Hover Color', 'pressgrid' ),
			'default' => '#0d47a1',
		),
	);

	foreach ( $colors as $setting_id => $args ) {
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => $args['default'],
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'postMessage',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				$setting_id,
				array(
					'label'   => $args['label'],
					'section' => 'pressgrid_colors',
				)
			)
		);
	}

	// ─── PANEL: Typography ────────────────────────────────────────────────────
	$wp_customize->add_panel(
		'pressgrid_typography_panel',
		array(
			'title'    => esc_html__( 'PressGrid: Typography', 'pressgrid' ),
			'priority' => 35,
		)
	);

	$wp_customize->add_section(
		'pressgrid_typography',
		array(
			'title'    => esc_html__( 'Font Settings', 'pressgrid' ),
			'panel'    => 'pressgrid_typography_panel',
			'priority' => 10,
		)
	);

	// Font URL upload (woff2).
	$wp_customize->add_setting(
		'pressgrid_font_url',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'pressgrid_font_url',
		array(
			'label'       => esc_html__( 'Custom Font URL (.woff2)', 'pressgrid' ),
			'description' => esc_html__( 'Enter the URL of an uploaded .woff2 font file. Use the Font Upload section in Theme Settings to upload safely.', 'pressgrid' ),
			'section'     => 'pressgrid_typography',
			'type'        => 'url',
		)
	);

	// Font family name.
	$wp_customize->add_setting(
		'pressgrid_font_family',
		array(
			'default'           => '',
			'sanitize_callback' => 'pressgrid_sanitize_font_family',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'pressgrid_font_family',
		array(
			'label'       => esc_html__( 'Font Family Name', 'pressgrid' ),
			'description' => esc_html__( 'Display name for your custom font (e.g. "MyFont"). Used in CSS font-family declarations.', 'pressgrid' ),
			'section'     => 'pressgrid_typography',
			'type'        => 'text',
		)
	);

	// Apply font to elements.
	$font_targets = array(
		'pressgrid_font_apply_body'     => esc_html__( 'Apply to Body Text', 'pressgrid' ),
		'pressgrid_font_apply_headings' => esc_html__( 'Apply to Headings', 'pressgrid' ),
		'pressgrid_font_apply_menu'     => esc_html__( 'Apply to Menu', 'pressgrid' ),
		'pressgrid_font_apply_buttons'  => esc_html__( 'Apply to Buttons', 'pressgrid' ),
	);

	foreach ( $font_targets as $setting_id => $label ) {
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => false,
				'sanitize_callback' => 'pressgrid_sanitize_checkbox',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			$setting_id,
			array(
				'label'   => $label,
				'section' => 'pressgrid_typography',
				'type'    => 'checkbox',
			)
		);
	}

	// ─── SECTION: Layout Options ──────────────────────────────────────────────
	$wp_customize->add_section(
		'pressgrid_layout',
		array(
			'title'    => esc_html__( 'PressGrid: Layout', 'pressgrid' ),
			'priority' => 40,
		)
	);

	$wp_customize->add_setting(
		'pressgrid_sidebar_position',
		array(
			'default'           => 'right',
			'sanitize_callback' => 'pressgrid_sanitize_sidebar',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'pressgrid_sidebar_position',
		array(
			'label'   => esc_html__( 'Sidebar Position', 'pressgrid' ),
			'section' => 'pressgrid_layout',
			'type'    => 'select',
			'choices' => array(
				'right' => esc_html__( 'Right', 'pressgrid' ),
				'left'  => esc_html__( 'Left', 'pressgrid' ),
				'none'  => esc_html__( 'No Sidebar (Full Width)', 'pressgrid' ),
			),
		)
	);

	// Breaking news.
	$wp_customize->add_setting(
		'pressgrid_breaking_news_enabled',
		array(
			'default'           => true,
			'sanitize_callback' => 'pressgrid_sanitize_checkbox',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'pressgrid_breaking_news_enabled',
		array(
			'label'   => esc_html__( 'Show Breaking News Bar', 'pressgrid' ),
			'section' => 'pressgrid_layout',
			'type'    => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'pressgrid_breaking_news_category',
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'pressgrid_breaking_news_category',
		array(
			'label'       => esc_html__( 'Breaking News Category ID', 'pressgrid' ),
			'description' => esc_html__( 'Enter the category ID for breaking news ticker. 0 = latest posts.', 'pressgrid' ),
			'section'     => 'pressgrid_layout',
			'type'        => 'number',
		)
	);

	// Footer credit.
	$wp_customize->add_setting(
		'pressgrid_show_footer_credit',
		array(
			'default'           => true,
			'sanitize_callback' => 'pressgrid_sanitize_checkbox',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'pressgrid_show_footer_credit',
		array(
			'label'   => esc_html__( 'Show Developer Credit in Footer', 'pressgrid' ),
			'section' => 'pressgrid_layout',
			'type'    => 'checkbox',
		)
	);
}
add_action( 'customize_register', 'pressgrid_customize_register' );

/**
 * Sanitize checkbox values.
 *
 * @param mixed $value Input value.
 * @return bool
 */
function pressgrid_sanitize_checkbox( $value ) {
	return (bool) $value;
}

/**
 * Sanitize sidebar position.
 *
 * @param string $value Input value.
 * @return string
 */
function pressgrid_sanitize_sidebar( $value ) {
	$valid = array( 'right', 'left', 'none' );
	return in_array( $value, $valid, true ) ? $value : 'right';
}

/**
 * Sanitize font family name: strip everything except letters, digits, spaces, hyphens.
 *
 * @param string $value Input value.
 * @return string
 */
function pressgrid_sanitize_font_family( $value ) {
	$value = sanitize_text_field( $value );
	$value = preg_replace( '/[^a-zA-Z0-9\s\-]/', '', $value );
	return trim( $value );
}

/**
 * Output Customizer live preview JS.
 */
function pressgrid_customize_preview_js() {
	wp_enqueue_script(
		'pressgrid-customizer-preview',
		PRESSGRID_URI . '/assets/js/customizer-preview.js',
		array( 'customize-preview' ),
		PRESSGRID_VERSION,
		true
	);
}
add_action( 'customize_preview_init', 'pressgrid_customize_preview_js' );

/**
 * Output CSS variables from Customizer settings.
 */
function pressgrid_customizer_css() {
	$primary    = sanitize_hex_color( get_theme_mod( 'pressgrid_primary_color', '#1a73e8' ) );
	$secondary  = sanitize_hex_color( get_theme_mod( 'pressgrid_secondary_color', '#0d47a1' ) );
	$accent     = sanitize_hex_color( get_theme_mod( 'pressgrid_accent_color', '#e91e63' ) );
	$bg         = sanitize_hex_color( get_theme_mod( 'pressgrid_bg_color', '#ffffff' ) );
	$text       = sanitize_hex_color( get_theme_mod( 'pressgrid_text_color', '#212121' ) );
	$link_hover = sanitize_hex_color( get_theme_mod( 'pressgrid_link_hover_color', '#0d47a1' ) );

	$font_family   = pressgrid_sanitize_font_family( get_theme_mod( 'pressgrid_font_family', '' ) );
	$font_url      = esc_url_raw( get_theme_mod( 'pressgrid_font_url', '' ) );
	$apply_body    = (bool) get_theme_mod( 'pressgrid_font_apply_body', false );
	$apply_heads   = (bool) get_theme_mod( 'pressgrid_font_apply_headings', false );
	$apply_menu    = (bool) get_theme_mod( 'pressgrid_font_apply_menu', false );
	$apply_buttons = (bool) get_theme_mod( 'pressgrid_font_apply_buttons', false );

	$fallback_stack = 'system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';

	// Build font-family CSS value.
	$font_body_val  = $fallback_stack;
	$font_head_val  = $fallback_stack;
	$font_menu_val  = $fallback_stack;
	$font_btn_val   = $fallback_stack;

	if ( $font_family ) {
		$safe_font = esc_attr( $font_family );
		if ( $apply_body ) {
			$font_body_val = '"' . $safe_font . '", ' . $fallback_stack;
		}
		if ( $apply_heads ) {
			$font_head_val = '"' . $safe_font . '", ' . $fallback_stack;
		}
		if ( $apply_menu ) {
			$font_menu_val = '"' . $safe_font . '", ' . $fallback_stack;
		}
		if ( $apply_buttons ) {
			$font_btn_val = '"' . $safe_font . '", ' . $fallback_stack;
		}
	}

	$css = '';

	// Output @font-face if a valid font URL and family name exist.
	if ( $font_url && $font_family ) {
		$css .= '@font-face {';
		$css .= 'font-family:"' . esc_attr( $font_family ) . '";';
		$css .= 'src:url("' . esc_url( $font_url ) . '") format("woff2");';
		$css .= 'font-display:swap;';
		$css .= '}';
	}

	$css .= ':root {';
	$css .= '--pg-primary:' . esc_attr( $primary ) . ';';
	$css .= '--pg-secondary:' . esc_attr( $secondary ) . ';';
	$css .= '--pg-accent:' . esc_attr( $accent ) . ';';
	$css .= '--pg-bg:' . esc_attr( $bg ) . ';';
	$css .= '--pg-text:' . esc_attr( $text ) . ';';
	$css .= '--pg-link-hover:' . esc_attr( $link_hover ) . ';';
	$css .= '--pg-font-body:' . $font_body_val . ';';
	$css .= '--pg-font-head:' . $font_head_val . ';';
	$css .= '--pg-font-menu:' . $font_menu_val . ';';
	$css .= '--pg-font-btn:' . $font_btn_val . ';';
	$css .= '}';

	wp_add_inline_style( 'pressgrid-style', $css );
}
add_action( 'wp_enqueue_scripts', 'pressgrid_customizer_css', 20 );

/**
 * Social media URL Customizer settings.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 */
function pressgrid_social_customize_register( $wp_customize ) {
	$wp_customize->add_section( 'pressgrid_social', array(
		'title'    => esc_html__( 'PressGrid: Social Media', 'pressgrid' ),
		'priority' => 50,
	) );
	$socials = array(
		'pressgrid_social_facebook'  => esc_html__( 'Facebook URL', 'pressgrid' ),
		'pressgrid_social_twitter'   => esc_html__( 'Twitter / X URL', 'pressgrid' ),
		'pressgrid_social_youtube'   => esc_html__( 'YouTube URL', 'pressgrid' ),
		'pressgrid_social_instagram' => esc_html__( 'Instagram URL', 'pressgrid' ),
	);
	foreach ( $socials as $id => $label ) {
		$wp_customize->add_setting( $id, array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( $id, array(
			'label'   => $label,
			'section' => 'pressgrid_social',
			'type'    => 'url',
		) );
	}
}
add_action( 'customize_register', 'pressgrid_social_customize_register' );
