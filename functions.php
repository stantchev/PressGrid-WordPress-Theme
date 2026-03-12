<?php
/**
 * PressGrid Theme Functions
 *
 * @package PressGrid
 * @version 2.5.0
 * @license GPL-2.0-or-later
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'PRESSGRID_VERSION', '2.5.0' );
define( 'PRESSGRID_DIR', get_template_directory() );
define( 'PRESSGRID_URI', get_template_directory_uri() );

// Зареждане на модули.
require_once PRESSGRID_DIR . '/inc/security.php';
require_once PRESSGRID_DIR . '/inc/customizer.php';
require_once PRESSGRID_DIR . '/inc/typography.php';
require_once PRESSGRID_DIR . '/inc/ads.php';
require_once PRESSGRID_DIR . '/inc/layout-builder.php';
require_once PRESSGRID_DIR . '/inc/weather.php';
require_once PRESSGRID_DIR . '/inc/forex.php';

/**
 * Theme setup.
 */
function pressgrid_setup() {
	load_theme_textdomain( 'pressgrid', PRESSGRID_DIR . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );

	add_image_size( 'pressgrid-hero',  800,  480, true );
	add_image_size( 'pressgrid-card',  600,  360, true );
	add_image_size( 'pressgrid-thumb', 300,  200, true );
	add_image_size( 'pressgrid-wide',  1200, 600, true );

	register_nav_menus( array(
		'primary'   => esc_html__( 'Primary Menu', 'pressgrid' ),
		'footer'    => esc_html__( 'Footer Menu', 'pressgrid' ),
		'secondary' => esc_html__( 'Secondary Menu', 'pressgrid' ),
	) );

	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list',
		'gallery', 'caption', 'style', 'script',
	) );

	add_theme_support( 'custom-background', array( 'default-color' => 'ffffff' ) );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'custom-logo', array(
		'height'      => 100,
		'width'       => 300,
		'flex-width'  => true,
		'flex-height' => true,
	) );

	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'post-formats', array( 'video', 'audio', 'gallery', 'quote', 'link' ) );
}
add_action( 'after_setup_theme', 'pressgrid_setup' );

function pressgrid_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'pressgrid_content_width', 860 );
}
add_action( 'after_setup_theme', 'pressgrid_content_width', 0 );

/**
 * Register widget areas.
 */
function pressgrid_widgets_init() {
	$defaults = array(
		'before_widget' => '<div id="%1$s" class="pg-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="pg-widget-title">',
		'after_title'   => '</h3>',
	);

	$sidebars = array(
		'sidebar-1' => array(
			'name'        => esc_html__( 'Primary Sidebar', 'pressgrid' ),
			'id'          => 'sidebar-1',
			'description' => esc_html__( 'Add widgets here to appear in the main sidebar.', 'pressgrid' ),
		),
		'sidebar-2' => array(
			'name'        => esc_html__( 'Secondary Sidebar', 'pressgrid' ),
			'id'          => 'sidebar-2',
			'description' => esc_html__( 'Add widgets here to appear in the secondary sidebar.', 'pressgrid' ),
		),
		'footer-1' => array( 'name' => esc_html__( 'Footer Column 1', 'pressgrid' ), 'id' => 'footer-1', 'description' => esc_html__( 'Footer column 1 widgets.', 'pressgrid' ) ),
		'footer-2' => array( 'name' => esc_html__( 'Footer Column 2', 'pressgrid' ), 'id' => 'footer-2', 'description' => esc_html__( 'Footer column 2 widgets.', 'pressgrid' ) ),
		'footer-3' => array( 'name' => esc_html__( 'Footer Column 3', 'pressgrid' ), 'id' => 'footer-3', 'description' => esc_html__( 'Footer column 3 widgets.', 'pressgrid' ) ),
		'footer-4' => array( 'name' => esc_html__( 'Footer Column 4', 'pressgrid' ), 'id' => 'footer-4', 'description' => esc_html__( 'Footer column 4 widgets.', 'pressgrid' ) ),
	);

	foreach ( $sidebars as $sidebar ) {
		register_sidebar( array_merge( $defaults, $sidebar ) );
	}
}
add_action( 'widgets_init', 'pressgrid_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function pressgrid_scripts() {
	wp_enqueue_style( 'pressgrid-style', get_stylesheet_uri(), array(), PRESSGRID_VERSION );

	wp_enqueue_script(
		'pressgrid-main',
		PRESSGRID_URI . '/assets/js/main.js',
		array(),
		PRESSGRID_VERSION,
		array( 'in_footer' => true, 'strategy' => 'defer' )
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'pressgrid_scripts' );

/**
 * Добавя preconnect hints за external ресурси.
 * Намалява латентността при зареждане на Google Fonts и OpenWeatherMap.
 */
function pressgrid_preconnect_hints() {
	// Google Fonts — винаги зареждаме шрифтовете
	echo '<link rel="preconnect" href="https://fonts.googleapis.com" />' . "\n";
	echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />' . "\n";

	// OpenWeatherMap — само ако weather модулът е включен
	if ( get_theme_mod( 'pressgrid_weather_enabled', false ) ) {
		echo '<link rel="preconnect" href="https://api.openweathermap.org" />' . "\n";
		echo '<link rel="dns-prefetch" href="https://openweathermap.org" />' . "\n";
	}

	// Frankfurter (ЕЦБ) — само ако forex тикерът ще се покаже
	if ( get_theme_mod( 'pressgrid_forex_force', false ) || pressgrid_has_business_section() ) {
		echo '<link rel="preconnect" href="https://api.frankfurter.app" />' . "\n";
	}
}
add_action( 'wp_head', 'pressgrid_preconnect_hints', 1 );

/**
 * Изчистване на transients при публикуване/изтриване на пост.
 */
function pressgrid_clear_transients( $post_id ) {
	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) { return; }
	delete_transient( 'pressgrid_hero_posts' );
	delete_transient( 'pressgrid_trending_posts' );
	delete_transient( 'pressgrid_editor_picks' );
	delete_transient( 'pressgrid_latest_posts' );
	$cats = get_categories( array( 'hide_empty' => true ) );
	foreach ( $cats as $cat ) {
		delete_transient( 'pressgrid_cat_grid_' . absint( $cat->term_id ) );
	}
}
add_action( 'save_post',   'pressgrid_clear_transients' );
add_action( 'delete_post', 'pressgrid_clear_transients' );

/**
 * Hero posts с transient cache.
 */
function pressgrid_get_hero_posts( $count = 3 ) {
	$key      = 'pressgrid_hero_posts';
	$post_ids = get_transient( $key );
	if ( false === $post_ids ) {
		$q        = new WP_Query( array(
			'post_type'              => 'post',
			'post_status'            => 'publish',
			'posts_per_page'         => absint( $count ),
			'orderby'                => 'date',
			'order'                  => 'DESC',
			'ignore_sticky_posts'    => false,
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
		) );
		$post_ids = wp_list_pluck( $q->posts, 'ID' );
		set_transient( $key, $post_ids, 5 * MINUTE_IN_SECONDS );
	}
	return new WP_Query( array(
		'post_type'      => 'post',
		'post__in'       => $post_ids,
		'orderby'        => 'post__in',
		'posts_per_page' => count( $post_ids ),
		'no_found_rows'  => true,
	) );
}

/**
 * Trending posts (по брой коментари, последната седмица).
 */
function pressgrid_get_trending_posts( $count = 6 ) {
	$key      = 'pressgrid_trending_posts';
	$post_ids = get_transient( $key );
	if ( false === $post_ids ) {
		$q        = new WP_Query( array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => absint( $count ),
			'orderby'        => 'comment_count',
			'order'          => 'DESC',
			'date_query'     => array( array( 'after' => '1 week ago' ) ),
			'no_found_rows'  => true,
		) );
		$post_ids = wp_list_pluck( $q->posts, 'ID' );
		set_transient( $key, $post_ids, 15 * MINUTE_IN_SECONDS );
	}
	return new WP_Query( array(
		'post_type'      => 'post',
		'post__in'       => $post_ids ?: array( 0 ),
		'orderby'        => 'post__in',
		'posts_per_page' => count( $post_ids ?: array( 0 ) ),
		'no_found_rows'  => true,
	) );
}

/**
 * Editor's Picks (sticky posts).
 */
function pressgrid_get_editor_picks( $count = 4 ) {
	$key      = 'pressgrid_editor_picks';
	$post_ids = get_transient( $key );
	if ( false === $post_ids ) {
		$q        = new WP_Query( array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => absint( $count ),
			'orderby'        => 'date',
			'order'          => 'DESC',
			'post__in'       => get_option( 'sticky_posts' ) ?: array( 0 ),
			'no_found_rows'  => true,
		) );
		$post_ids = wp_list_pluck( $q->posts, 'ID' );
		set_transient( $key, $post_ids, 15 * MINUTE_IN_SECONDS );
	}
	return new WP_Query( array(
		'post_type'      => 'post',
		'post__in'       => $post_ids ?: array( 0 ),
		'orderby'        => 'post__in',
		'posts_per_page' => count( $post_ids ?: array( 0 ) ),
		'no_found_rows'  => true,
	) );
}

/**
 * Category posts с transient cache.
 */
function pressgrid_get_category_posts( $category = 0, $count = 4 ) {
	$key      = 'pressgrid_cat_grid_' . absint( $category );
	$post_ids = get_transient( $key );
	if ( false === $post_ids ) {
		$args = array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => absint( $count ),
			'orderby'        => 'date',
			'order'          => 'DESC',
			'no_found_rows'  => true,
		);
		if ( $category > 0 ) { $args['cat'] = $category; }
		$q        = new WP_Query( $args );
		$post_ids = wp_list_pluck( $q->posts, 'ID' );
		set_transient( $key, $post_ids, 10 * MINUTE_IN_SECONDS );
	}
	return new WP_Query( array(
		'post_type'      => 'post',
		'post__in'       => $post_ids ?: array( 0 ),
		'orderby'        => 'post__in',
		'posts_per_page' => count( $post_ids ?: array( 0 ) ),
		'no_found_rows'  => true,
	) );
}

/**
 * Breadcrumbs.
 */
function pressgrid_breadcrumbs() {
	if ( is_front_page() ) { return; }
	echo '<nav class="pg-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumbs', 'pressgrid' ) . '">';
	echo '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'pressgrid' ) . '</a>';
	echo '<span class="sep" aria-hidden="true"> › </span>';
	if ( is_singular() ) {
		$cats = get_the_category();
		if ( $cats ) {
			echo '<a href="' . esc_url( get_category_link( $cats[0]->term_id ) ) . '">' . esc_html( $cats[0]->name ) . '</a>';
			echo '<span class="sep" aria-hidden="true"> › </span>';
		}
		echo '<span>' . esc_html( get_the_title() ) . '</span>';
	} elseif ( is_category() ) {
		echo '<span>' . esc_html( single_cat_title( '', false ) ) . '</span>';
	} elseif ( is_tag() ) {
		echo '<span>' . esc_html( single_tag_title( '', false ) ) . '</span>';
	} elseif ( is_archive() ) {
		echo '<span>' . esc_html( get_the_archive_title() ) . '</span>';
	} elseif ( is_search() ) {
		echo '<span>' . esc_html__( 'Search', 'pressgrid' ) . ': ' . esc_html( get_search_query() ) . '</span>';
	}
	echo '</nav>';
}

/**
 * Post meta (автор, дата, категория, четене).
 */
function pressgrid_post_meta( $post_id = 0 ) {
	$pid    = absint( $post_id ) ?: get_the_ID();
	$author = get_the_author_meta( 'display_name', (int) get_post_field( 'post_author', $pid ) );
	$date   = get_the_date( '', $pid );
	$mins   = pressgrid_reading_time( $pid );
	printf(
		'<span>%s <a href="%s">%s</a></span><span>%s</span><span>%s ' . esc_html__( 'мин. четене', 'pressgrid' ) . '</span>',
		esc_html__( 'By', 'pressgrid' ),
		esc_url( get_author_posts_url( (int) get_post_field( 'post_author', $pid ) ) ),
		esc_html( $author ),
		esc_html( $date ),
		absint( $mins )
	);
	$modified = get_the_modified_date( '', $pid );
	if ( $modified !== $date ) {
		printf(
			'<span>(' . esc_html__( 'Updated: %s', 'pressgrid' ) . ')</span>',
			esc_html( $modified )
		);
	}
}

/**
 * Estimate reading time.
 */
function pressgrid_reading_time( $post_id = 0 ) {
	$content    = get_post_field( 'post_content', absint( $post_id ) );
	$word_count = str_word_count( wp_strip_all_tags( $content ) );
	return max( 1, (int) ceil( $word_count / 200 ) );
}

/**
 * Category label HTML.
 */
function pressgrid_get_category_label( $post_id = 0 ) {
	$cats = get_the_category( absint( $post_id ) );
	if ( empty( $cats ) ) { return ''; }
	$cat = $cats[0];
	return '<div class="pg-post-category"><a href="' . esc_url( get_category_link( $cat->term_id ) ) . '">' . esc_html( $cat->name ) . '</a></div>';
}

/**
 * NewsArticle JSON-LD Schema.
 */
function pressgrid_schema_output() {
	if ( ! is_singular( 'post' ) ) { return; }
	$post        = get_post();
	$author_id   = (int) $post->post_author;
	$featured    = '';
	if ( has_post_thumbnail() ) {
		$img_data = wp_get_attachment_image_src( get_post_thumbnail_id(), 'pressgrid-wide' );
		$featured = $img_data ? esc_url( $img_data[0] ) : '';
	}
	$schema = array(
		'@context'         => 'https://schema.org',
		'@type'            => 'NewsArticle',
		'headline'         => get_the_title(),
		'description'      => wp_strip_all_tags( get_the_excerpt() ),
		'datePublished'    => get_the_date( 'c' ),
		'dateModified'     => get_the_modified_date( 'c' ),
		'author'           => array(
			'@type' => 'Person',
			'name'  => get_the_author_meta( 'display_name', $author_id ),
			'url'   => get_author_posts_url( $author_id ),
		),
		'publisher'        => array(
			'@type' => 'Organization',
			'name'  => get_bloginfo( 'name' ),
			'url'   => home_url( '/' ),
		),
		'mainEntityOfPage' => array( '@type' => 'WebPage', '@id' => get_permalink() ),
	);
	if ( $featured ) { $schema['image'] = $featured; }
	$logo_id = get_theme_mod( 'custom_logo' );
	if ( $logo_id ) {
		$logo_src = wp_get_attachment_image_src( $logo_id, 'full' );
		if ( $logo_src ) {
			$schema['publisher']['logo'] = array( '@type' => 'ImageObject', 'url' => esc_url( $logo_src[0] ) );
		}
	}
	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'pressgrid_schema_output' );

/**
 * Open Graph + Twitter Card мета тагове.
 */
function pressgrid_social_meta() {
	if ( is_feed() || is_admin() ) { return; }
	$title = $description = $image = $url = '';
	$type  = 'website';
	if ( is_singular() ) {
		$title       = get_the_title();
		$description = wp_strip_all_tags( get_the_excerpt() );
		$type        = 'article';
		$url         = get_permalink();
		if ( has_post_thumbnail() ) {
			$img   = wp_get_attachment_image_src( get_post_thumbnail_id(), 'pressgrid-wide' );
			$image = $img ? $img[0] : '';
		}
	} else {
		$title       = get_bloginfo( 'name' );
		$description = get_bloginfo( 'description' );
		$url         = home_url( '/' );
	}
	if ( ! $image ) {
		$logo_id = get_theme_mod( 'custom_logo' );
		if ( $logo_id ) {
			$src   = wp_get_attachment_image_src( $logo_id, 'full' );
			$image = $src ? $src[0] : '';
		}
	}
	$site = get_bloginfo( 'name' );
	echo '<meta property="og:title" content="'       . esc_attr( $title )       . '" />' . "\n";
	echo '<meta property="og:description" content="' . esc_attr( $description ) . '" />' . "\n";
	echo '<meta property="og:type" content="'        . esc_attr( $type )        . '" />' . "\n";
	echo '<meta property="og:url" content="'         . esc_url( $url )          . '" />' . "\n";
	echo '<meta property="og:site_name" content="'   . esc_attr( $site )        . '" />' . "\n";
	if ( $image ) { echo '<meta property="og:image" content="' . esc_url( $image ) . '" />' . "\n"; }
	echo '<meta name="twitter:card" content="summary_large_image" />'                    . "\n";
	echo '<meta name="twitter:title" content="'       . esc_attr( $title )       . '" />' . "\n";
	echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '" />' . "\n";
	if ( $image ) { echo '<meta name="twitter:image" content="' . esc_url( $image ) . '" />' . "\n"; }
	echo '<link rel="canonical" href="' . esc_url( $url ) . '" />' . "\n";
}
add_action( 'wp_head', 'pressgrid_social_meta' );

/**
 * Pagination.
 */
function pressgrid_pagination() {
	global $wp_query;
	$total = isset( $wp_query->max_num_pages ) ? (int) $wp_query->max_num_pages : 1;
	if ( $total <= 1 ) { return; }
	$links = paginate_links( array(
		'base'      => str_replace( PHP_INT_MAX, '%#%', esc_url( get_pagenum_link( PHP_INT_MAX ) ) ),
		'format'    => '?paged=%#%',
		'current'   => max( 1, get_query_var( 'paged' ) ),
		'total'     => $total,
		'type'      => 'array',
		'prev_text' => esc_html__( '&laquo; Previous', 'pressgrid' ),
		'next_text' => esc_html__( 'Next &raquo;', 'pressgrid' ),
	) );
	if ( $links ) {
		echo '<nav class="pg-pagination" aria-label="' . esc_attr__( 'Posts pagination', 'pressgrid' ) . '">';
		foreach ( $links as $link ) { echo wp_kses_post( $link ); }
		echo '</nav>';
	}
}

/**
 * Post thumbnail helper.
 */
function pressgrid_post_thumbnail( $size = 'pressgrid-card', $attrs = array() ) {
	if ( ! has_post_thumbnail() ) { return; }
	the_post_thumbnail( $size, array_merge( array( 'loading' => 'lazy', 'decoding' => 'async' ), $attrs ) );
}

/**
 * Defer pressgrid-main script.
 */
function pressgrid_script_loader_tag( $tag, $handle ) {
	if ( 'pressgrid-main' === $handle ) {
		return str_replace( ' src', ' defer src', $tag );
	}
	return $tag;
}
add_filter( 'script_loader_tag', 'pressgrid_script_loader_tag', 10, 2 );

/**
 * Fallback menu.
 */
function pressgrid_fallback_menu() {
	echo '<ul id="primary-menu">';
	echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'pressgrid' ) . '</a></li>';
	wp_list_pages( array( 'title_li' => '', 'depth' => 1, 'sort_column' => 'menu_order', 'echo' => true ) );
	echo '</ul>';
}

/**
 * Disable emoji scripts (performance).
 */
function pressgrid_disable_emojis() {
	remove_action( 'wp_head',             'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles',     'print_emoji_styles' );
	remove_action( 'admin_print_styles',  'print_emoji_styles' );
	remove_filter( 'the_content_feed',    'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss',    'wp_staticize_emoji' );
	remove_filter( 'wp_mail',             'wp_staticize_emoji_for_email' );
}
add_action( 'init', 'pressgrid_disable_emojis' );

// Remove noisy wp_head output.
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );
add_action( 'after_setup_theme', function() {
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
	remove_action( 'wp_head', 'wp_oembed_add_host_js' );
} );

// Excerpt tweaks.
add_filter( 'excerpt_length', function( $l ) { return is_admin() ? $l : 20; } );
add_filter( 'excerpt_more',   function( $m ) { return is_admin() ? $m : '&hellip;'; } );
