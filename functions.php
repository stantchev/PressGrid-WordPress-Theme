<?php
/**
 * PressGrid Theme Functions
 *
 * @package PressGrid
 * @version 1.0.0
 * @license GPL-2.0-or-later
 */

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Theme version constant.
define( 'PRESSGRID_VERSION', '1.0.0' );
define( 'PRESSGRID_DIR', get_template_directory() );
define( 'PRESSGRID_URI', get_template_directory_uri() );

// Load required files.
require_once PRESSGRID_DIR . '/inc/security.php';
require_once PRESSGRID_DIR . '/inc/customizer.php';
require_once PRESSGRID_DIR . '/inc/typography.php';
require_once PRESSGRID_DIR . '/inc/ads.php';
require_once PRESSGRID_DIR . '/inc/layout-builder.php';
require_once PRESSGRID_DIR . '/inc/weather.php';

/**
 * Theme setup.
 */
function pressgrid_setup() {
	// Make theme available for translation.
	load_theme_textdomain( 'pressgrid', PRESSGRID_DIR . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	// Let WordPress manage the document title.
	add_theme_support( 'title-tag' );

	// Enable support for Post Thumbnails.
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'pressgrid-hero', 800, 480, true );
	add_image_size( 'pressgrid-card', 600, 360, true );
	add_image_size( 'pressgrid-thumb', 300, 200, true );
	add_image_size( 'pressgrid-wide', 1200, 600, true );

	// Register navigation menus.
	register_nav_menus(
		array(
			'primary'   => esc_html__( 'Primary Menu', 'pressgrid' ),
			'footer'    => esc_html__( 'Footer Menu', 'pressgrid' ),
			'secondary' => esc_html__( 'Secondary Menu', 'pressgrid' ),
		)
	);

	// Switch default core markup to output valid HTML5.
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		array(
			'default-color' => 'ffffff',
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	// Add support for custom logo.
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 100,
			'width'       => 300,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);

	// Add support for Block Styles.
	add_theme_support( 'wp-block-styles' );

	// Add support for wide-aligned images.
	add_theme_support( 'align-wide' );

	// Add support for editor styles.
	add_theme_support( 'editor-styles' );

	// Add support for responsive embeds.
	add_theme_support( 'responsive-embeds' );

	// Add support for post formats.
	add_theme_support(
		'post-formats',
		array( 'video', 'audio', 'gallery', 'quote', 'link' )
	);
}
add_action( 'after_setup_theme', 'pressgrid_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 */
function pressgrid_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'pressgrid_content_width', 860 );
}
add_action( 'after_setup_theme', 'pressgrid_content_width', 0 );

/**
 * Register widget areas.
 */
function pressgrid_widgets_init() {
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
		'footer-1' => array(
			'name'        => esc_html__( 'Footer Column 1', 'pressgrid' ),
			'id'          => 'footer-1',
			'description' => esc_html__( 'Footer column 1 widgets.', 'pressgrid' ),
		),
		'footer-2' => array(
			'name'        => esc_html__( 'Footer Column 2', 'pressgrid' ),
			'id'          => 'footer-2',
			'description' => esc_html__( 'Footer column 2 widgets.', 'pressgrid' ),
		),
		'footer-3' => array(
			'name'        => esc_html__( 'Footer Column 3', 'pressgrid' ),
			'id'          => 'footer-3',
			'description' => esc_html__( 'Footer column 3 widgets.', 'pressgrid' ),
		),
		'footer-4' => array(
			'name'        => esc_html__( 'Footer Column 4', 'pressgrid' ),
			'id'          => 'footer-4',
			'description' => esc_html__( 'Footer column 4 widgets.', 'pressgrid' ),
		),
	);

	$defaults = array(
		'before_widget' => '<div id="%1$s" class="pg-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="pg-widget-title">',
		'after_title'   => '</h3>',
	);

	foreach ( $sidebars as $sidebar ) {
		register_sidebar(
			array_merge( $defaults, $sidebar )
		);
	}
}
add_action( 'widgets_init', 'pressgrid_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function pressgrid_scripts() {
	// Main stylesheet.
	wp_enqueue_style(
		'pressgrid-style',
		get_stylesheet_uri(),
		array(),
		PRESSGRID_VERSION
	);

	// Main JS — deferred, no jQuery dependency.
	wp_enqueue_script(
		'pressgrid-main',
		PRESSGRID_URI . '/assets/js/main.js',
		array(),
		PRESSGRID_VERSION,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);

	// Comments reply script.
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'pressgrid_scripts' );

/**
 * Clear transients when a post is saved.
 *
 * @param int $post_id Post ID.
 */
function pressgrid_clear_transients( $post_id ) {
	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}
	delete_transient( 'pressgrid_hero_posts' );
	delete_transient( 'pressgrid_trending_posts' );
	// Clear per-category transients.
	$cats = get_categories( array( 'hide_empty' => true ) );
	foreach ( $cats as $cat ) {
		delete_transient( 'pressgrid_cat_grid_' . absint( $cat->term_id ) );
	}
	delete_transient( 'pressgrid_editor_picks' );
	delete_transient( 'pressgrid_latest_posts' );
}
add_action( 'save_post', 'pressgrid_clear_transients' );
add_action( 'delete_post', 'pressgrid_clear_transients' );

/**
 * Retrieve or cache hero posts.
 *
 * @param int $count Number of posts.
 * @return WP_Query
 */
function pressgrid_get_hero_posts( $count = 3 ) {
	$transient_key = 'pressgrid_hero_posts';
	$post_ids      = get_transient( $transient_key );

	if ( false === $post_ids ) {
		$query    = new WP_Query(
			array(
				'post_type'           => 'post',
				'post_status'         => 'publish',
				'posts_per_page'      => absint( $count ),
				'orderby'             => 'date',
				'order'               => 'DESC',
				'ignore_sticky_posts' => false,
				'no_found_rows'       => true,
				'update_post_term_cache' => false,
			)
		);
		$post_ids = wp_list_pluck( $query->posts, 'ID' );
		set_transient( $transient_key, $post_ids, 5 * MINUTE_IN_SECONDS );
	}

	return new WP_Query(
		array(
			'post_type'      => 'post',
			'post__in'       => $post_ids,
			'orderby'        => 'post__in',
			'posts_per_page' => count( $post_ids ),
			'no_found_rows'  => true,
		)
	);
}

/**
 * Retrieve or cache trending posts (by comment count).
 *
 * @param int $count Number of posts.
 * @return WP_Query
 */
function pressgrid_get_trending_posts( $count = 6 ) {
	$transient_key = 'pressgrid_trending_posts';
	$post_ids      = get_transient( $transient_key );

	if ( false === $post_ids ) {
		$query    = new WP_Query(
			array(
				'post_type'           => 'post',
				'post_status'         => 'publish',
				'posts_per_page'      => absint( $count ),
				'orderby'             => 'comment_count',
				'order'               => 'DESC',
				'date_query'          => array(
					array(
						'after' => '1 week ago',
					),
				),
				'no_found_rows'       => true,
				'update_post_term_cache' => false,
			)
		);
		$post_ids = wp_list_pluck( $query->posts, 'ID' );
		if ( empty( $post_ids ) ) {
			// Fallback: latest posts.
			$query    = new WP_Query(
				array(
					'post_type'      => 'post',
					'post_status'    => 'publish',
					'posts_per_page' => absint( $count ),
					'no_found_rows'  => true,
				)
			);
			$post_ids = wp_list_pluck( $query->posts, 'ID' );
		}
		set_transient( $transient_key, $post_ids, 10 * MINUTE_IN_SECONDS );
	}

	return new WP_Query(
		array(
			'post_type'      => 'post',
			'post__in'       => $post_ids,
			'orderby'        => 'post__in',
			'posts_per_page' => count( $post_ids ),
			'no_found_rows'  => true,
		)
	);
}

/**
 * Retrieve or cache category grid posts.
 *
 * @param int $cat_id  Category ID.
 * @param int $count   Number of posts.
 * @return WP_Query
 */
function pressgrid_get_category_posts( $cat_id, $count = 4 ) {
	$cat_id        = absint( $cat_id );
	$transient_key = 'pressgrid_cat_grid_' . $cat_id;
	$post_ids      = get_transient( $transient_key );

	if ( false === $post_ids ) {
		$query    = new WP_Query(
			array(
				'post_type'           => 'post',
				'post_status'         => 'publish',
				'posts_per_page'      => absint( $count ),
				'cat'                 => $cat_id,
				'orderby'             => 'date',
				'order'               => 'DESC',
				'no_found_rows'       => true,
				'update_post_term_cache' => false,
			)
		);
		$post_ids = wp_list_pluck( $query->posts, 'ID' );
		set_transient( $transient_key, $post_ids, 5 * MINUTE_IN_SECONDS );
	}

	if ( empty( $post_ids ) ) {
		return new WP_Query( array( 'post__in' => array( 0 ) ) );
	}

	return new WP_Query(
		array(
			'post_type'      => 'post',
			'post__in'       => $post_ids,
			'orderby'        => 'post__in',
			'posts_per_page' => count( $post_ids ),
			'no_found_rows'  => true,
		)
	);
}

/**
 * Retrieve or cache editor picks.
 *
 * @param int $count Number of posts.
 * @return WP_Query
 */
function pressgrid_get_editor_picks( $count = 4 ) {
	$transient_key = 'pressgrid_editor_picks';
	$post_ids      = get_transient( $transient_key );

	if ( false === $post_ids ) {
		$query    = new WP_Query(
			array(
				'post_type'      => 'post',
				'post_status'    => 'publish',
				'posts_per_page' => absint( $count ),
				'orderby'        => 'rand',
				'no_found_rows'  => true,
			)
		);
		$post_ids = wp_list_pluck( $query->posts, 'ID' );
		set_transient( $transient_key, $post_ids, 30 * MINUTE_IN_SECONDS );
	}

	if ( empty( $post_ids ) ) {
		return new WP_Query( array( 'post__in' => array( 0 ) ) );
	}

	return new WP_Query(
		array(
			'post_type'      => 'post',
			'post__in'       => $post_ids,
			'orderby'        => 'post__in',
			'posts_per_page' => count( $post_ids ),
			'no_found_rows'  => true,
		)
	);
}

/**
 * Output breadcrumbs.
 */
function pressgrid_breadcrumbs() {
	if ( is_front_page() ) {
		return;
	}
	echo '<nav class="pg-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumbs', 'pressgrid' ) . '">';
	echo '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'pressgrid' ) . '</a>';
	echo '<span class="sep" aria-hidden="true">/</span>';

	if ( is_category() ) {
		echo '<span>' . esc_html( single_cat_title( '', false ) ) . '</span>';
	} elseif ( is_tag() ) {
		echo '<span>' . esc_html( single_tag_title( '', false ) ) . '</span>';
	} elseif ( is_author() ) {
		echo '<span>' . esc_html( get_the_author() ) . '</span>';
	} elseif ( is_singular() ) {
		$cats = get_the_category();
		if ( $cats ) {
			echo '<a href="' . esc_url( get_category_link( $cats[0]->term_id ) ) . '">' . esc_html( $cats[0]->name ) . '</a>';
			echo '<span class="sep" aria-hidden="true">/</span>';
		}
		echo '<span>' . esc_html( get_the_title() ) . '</span>';
	} elseif ( is_search() ) {
		/* translators: %s: search query */
		echo '<span>' . sprintf( esc_html__( 'Search: %s', 'pressgrid' ), esc_html( get_search_query() ) ) . '</span>';
	} elseif ( is_404() ) {
		echo '<span>' . esc_html__( '404 Not Found', 'pressgrid' ) . '</span>';
	} elseif ( is_page() ) {
		echo '<span>' . esc_html( get_the_title() ) . '</span>';
	}
	echo '</nav>';
}

/**
 * Output post meta.
 *
 * @param int $post_id Post ID.
 */
function pressgrid_post_meta( $post_id = 0 ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}
	$post_id = absint( $post_id );
	$author  = get_the_author_meta( 'display_name', get_post_field( 'post_author', $post_id ) );
	$time    = get_the_date( '', $post_id );
	$mtime   = get_the_modified_date( '', $post_id );
	?>
	<span class="pg-meta-author">
		<?php esc_html_e( 'By', 'pressgrid' ); ?>
		<a href="<?php echo esc_url( get_author_posts_url( get_post_field( 'post_author', $post_id ) ) ); ?>">
			<?php echo esc_html( $author ); ?>
		</a>
	</span>
	<time datetime="<?php echo esc_attr( get_the_date( 'c', $post_id ) ); ?>">
		<?php echo esc_html( $time ); ?>
	</time>
	<?php if ( $mtime !== $time ) : ?>
	<span class="pg-meta-modified">
		<?php
		/* translators: %s: modified date */
		printf( esc_html__( '(Updated: %s)', 'pressgrid' ), esc_html( $mtime ) );
		?>
	</span>
	<?php endif; ?>
	<?php
	$reading_time = pressgrid_reading_time( $post_id );
	if ( $reading_time ) :
		?>
		<span class="pg-reading-time">
			<?php
			/* translators: %d: number of minutes */
			printf( esc_html( _n( '%d min read', '%d min read', $reading_time, 'pressgrid' ) ), absint( $reading_time ) );
			?>
		</span>
	<?php endif; ?>
	<?php
}

/**
 * Estimate reading time.
 *
 * @param int $post_id Post ID.
 * @return int Minutes.
 */
function pressgrid_reading_time( $post_id = 0 ) {
	$content    = get_post_field( 'post_content', absint( $post_id ) );
	$word_count = str_word_count( wp_strip_all_tags( $content ) );
	return max( 1, (int) ceil( $word_count / 200 ) );
}

/**
 * Output Schema.org JSON-LD.
 */
function pressgrid_schema_output() {
	if ( ! is_singular( 'post' ) ) {
		return;
	}

	$post         = get_post();
	$author_id    = (int) $post->post_author;
	$author_name  = get_the_author_meta( 'display_name', $author_id );
	$featured_img = '';
	if ( has_post_thumbnail() ) {
		$img_data     = wp_get_attachment_image_src( get_post_thumbnail_id(), 'pressgrid-wide' );
		$featured_img = $img_data ? esc_url( $img_data[0] ) : '';
	}
	$description  = wp_strip_all_tags( get_the_excerpt() );

	$schema = array(
		'@context'         => 'https://schema.org',
		'@type'            => 'NewsArticle',
		'headline'         => get_the_title(),
		'description'      => $description,
		'datePublished'    => get_the_date( 'c' ),
		'dateModified'     => get_the_modified_date( 'c' ),
		'author'           => array(
			'@type' => 'Person',
			'name'  => $author_name,
			'url'   => get_author_posts_url( $author_id ),
		),
		'publisher'        => array(
			'@type' => 'Organization',
			'name'  => get_bloginfo( 'name' ),
			'url'   => home_url( '/' ),
		),
		'mainEntityOfPage' => array(
			'@type' => 'WebPage',
			'@id'   => get_permalink(),
		),
	);

	if ( $featured_img ) {
		$schema['image'] = $featured_img;
	}

	$logo_id = get_theme_mod( 'custom_logo' );
	if ( $logo_id ) {
		$logo_src = wp_get_attachment_image_src( $logo_id, 'full' );
		if ( $logo_src ) {
			$schema['publisher']['logo'] = array(
				'@type' => 'ImageObject',
				'url'   => esc_url( $logo_src[0] ),
			);
		}
	}

	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'pressgrid_schema_output' );

/**
 * Output Open Graph and Twitter Card meta tags.
 */
function pressgrid_social_meta() {
	if ( is_feed() || is_admin() ) {
		return;
	}

	$title       = '';
	$description = '';
	$image       = '';
	$type        = 'website';
	$url         = '';

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
		// Fallback: custom logo.
		$logo_id = get_theme_mod( 'custom_logo' );
		if ( $logo_id ) {
			$logo_src = wp_get_attachment_image_src( $logo_id, 'full' );
			$image    = $logo_src ? $logo_src[0] : '';
		}
	}

	$site_name = get_bloginfo( 'name' );
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '" />' . "\n";
	echo '<meta property="og:description" content="' . esc_attr( $description ) . '" />' . "\n";
	echo '<meta property="og:type" content="' . esc_attr( $type ) . '" />' . "\n";
	echo '<meta property="og:url" content="' . esc_url( $url ) . '" />' . "\n";
	echo '<meta property="og:site_name" content="' . esc_attr( $site_name ) . '" />' . "\n";
	if ( $image ) {
		echo '<meta property="og:image" content="' . esc_url( $image ) . '" />' . "\n";
	}
	echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
	echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '" />' . "\n";
	echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '" />' . "\n";
	if ( $image ) {
		echo '<meta name="twitter:image" content="' . esc_url( $image ) . '" />' . "\n";
	}
	echo '<link rel="canonical" href="' . esc_url( $url ) . '" />' . "\n";
}
add_action( 'wp_head', 'pressgrid_social_meta' );

/**
 * Pagination for archives.
 */
function pressgrid_pagination() {
	global $wp_query;
	$total = isset( $wp_query->max_num_pages ) ? (int) $wp_query->max_num_pages : 1;
	if ( $total <= 1 ) {
		return;
	}
	$links = paginate_links(
		array(
			'base'      => str_replace( PHP_INT_MAX, '%#%', esc_url( get_pagenum_link( PHP_INT_MAX ) ) ),
			'format'    => '?paged=%#%',
			'current'   => max( 1, get_query_var( 'paged' ) ),
			'total'     => $total,
			'type'      => 'array',
			'prev_text' => esc_html__( '&laquo; Previous', 'pressgrid' ),
			'next_text' => esc_html__( 'Next &raquo;', 'pressgrid' ),
		)
	);
	if ( $links ) {
		echo '<nav class="pg-pagination" aria-label="' . esc_attr__( 'Posts pagination', 'pressgrid' ) . '">';
		foreach ( $links as $link ) {
			echo wp_kses_post( $link );
		}
		echo '</nav>';
	}
}

/**
 * Get post categories as links (first category only).
 *
 * @param int $post_id Post ID.
 * @return string HTML.
 */
function pressgrid_get_category_label( $post_id = 0 ) {
	$cats = get_the_category( absint( $post_id ) );
	if ( empty( $cats ) ) {
		return '';
	}
	$cat = $cats[0];
	return '<div class="pg-post-category"><a href="' . esc_url( get_category_link( $cat->term_id ) ) . '">' . esc_html( $cat->name ) . '</a></div>';
}

/**
 * Disable the emoji scripts (performance).
 */
function pressgrid_disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
}
add_action( 'init', 'pressgrid_disable_emojis' );

/**
 * Remove oEmbed discovery links (performance + privacy).
 */
function pressgrid_remove_oembed() {
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
	remove_action( 'wp_head', 'wp_oembed_add_host_js' );
}
add_action( 'after_setup_theme', 'pressgrid_remove_oembed' );

/**
 * Remove RSD link from head.
 */
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );

/**
 * Custom excerpt length.
 *
 * @param int $length Default length.
 * @return int
 */
function pressgrid_excerpt_length( $length ) {
	if ( is_admin() ) {
		return $length;
	}
	return 20;
}
add_filter( 'excerpt_length', 'pressgrid_excerpt_length' );

/**
 * Custom excerpt more string.
 *
 * @param string $more Default more string.
 * @return string
 */
function pressgrid_excerpt_more( $more ) {
	if ( is_admin() ) {
		return $more;
	}
	return '&hellip;';
}
add_filter( 'excerpt_more', 'pressgrid_excerpt_more' );

/**
 * Render the post thumbnail with lazy loading + srcset.
 *
 * @param string $size Image size.
 * @param array  $attrs Additional attributes.
 */
function pressgrid_post_thumbnail( $size = 'pressgrid-card', $attrs = array() ) {
	if ( ! has_post_thumbnail() ) {
		return;
	}
	$default_attrs = array(
		'loading' => 'lazy',
		'decoding' => 'async',
	);
	$attrs = array_merge( $default_attrs, $attrs );
	the_post_thumbnail( $size, $attrs );
}

/**
 * Add defer/async to theme scripts only.
 *
 * @param string $tag    Script tag HTML.
 * @param string $handle Script handle.
 * @return string Modified script tag.
 */
function pressgrid_script_loader_tag( $tag, $handle ) {
	if ( 'pressgrid-main' === $handle ) {
		return str_replace( ' src', ' defer src', $tag );
	}
	return $tag;
}
add_filter( 'script_loader_tag', 'pressgrid_script_loader_tag', 10, 2 );

/**
 * Fallback menu when no menu is assigned.
 */
function pressgrid_fallback_menu() {
	echo '<ul id="primary-menu">';
	echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'pressgrid' ) . '</a></li>';
	wp_list_pages(
		array(
			'title_li'    => '',
			'depth'       => 1,
			'sort_column' => 'menu_order',
			'echo'        => true,
		)
	);
	echo '</ul>';
}
