<?php
/**
 * Header Template — Newspaper style
 * Top bar → Masthead → Nav → Breaking news → Ad zone → main opens
 *
 * @package PressGrid
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link rel="profile" href="https://gmpg.org/xfn/11" />
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="screen-reader-text" href="#pg-main-content"><?php esc_html_e( 'Skip to content', 'pressgrid' ); ?></a>

<div class="pg-site-wrapper">

	<!-- ═══ TOP BAR ═══ -->
	<div class="pg-topbar">
		<div class="pg-container">
			<div class="pg-topbar-inner">
				<span class="pg-topbar-date"><?php echo esc_html( date_i18n( get_option( 'date_format' ) ) ); ?></span>
				<div class="pg-topbar-social" aria-label="<?php esc_attr_e( 'Social Media', 'pressgrid' ); ?>">
					<?php
					$social_links = array(
						'pressgrid_social_facebook'  => array( 'label' => 'Facebook',  'icon' => 'f'  ),
						'pressgrid_social_twitter'   => array( 'label' => 'X/Twitter', 'icon' => '𝕏' ),
						'pressgrid_social_youtube'   => array( 'label' => 'YouTube',   'icon' => '▶' ),
						'pressgrid_social_instagram' => array( 'label' => 'Instagram', 'icon' => '◎' ),
					);
					foreach ( $social_links as $mod_key => $data ) {
						$url = get_theme_mod( $mod_key, '' );
						if ( ! $url ) { $url = '#'; }
						printf(
							'<a href="%s" %s aria-label="%s">%s</a>',
							esc_url( $url ),
							'#' !== $url ? 'target="_blank" rel="noopener noreferrer"' : '',
							esc_attr( $data['label'] ),
							$data['icon'] // phpcs:ignore — safe static string
						);
					}
					?>
				</div>
			</div>
		</div>
	</div>

	<!-- ═══ MASTHEAD ═══ -->
	<header class="pg-masthead" role="banner">
		<div class="pg-container">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else :
				$site_name = get_bloginfo( 'name' );
				$half      = (int) floor( mb_strlen( $site_name ) / 2 );
				$part1     = mb_substr( $site_name, 0, $half );
				$part2     = mb_substr( $site_name, $half );
				?>
				<a class="pg-masthead-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
					<?php echo esc_html( $part1 ); ?><span class="pg-logo-accent"><?php echo esc_html( $part2 ); ?></span>
				</a>
				<?php $desc = get_bloginfo( 'description', 'display' );
				if ( $desc || is_customize_preview() ) : ?>
					<p class="pg-masthead-tagline"><?php echo esc_html( $desc ); ?></p>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</header>

	<!-- ═══ PRIMARY NAV ═══ -->
	<div class="pg-nav-wrap" id="pg-nav-wrap">
		<div class="pg-container">
			<div class="pg-nav-inner">
				<button class="pg-nav-toggle" id="pg-nav-toggle"
					aria-controls="pg-primary-nav" aria-expanded="false"
					aria-label="<?php esc_attr_e( 'Toggle navigation', 'pressgrid' ); ?>">&#9776;</button>
				<nav id="pg-primary-nav" class="pg-primary-nav"
					role="navigation" aria-label="<?php esc_attr_e( 'Primary Navigation', 'pressgrid' ); ?>">
					<?php
					wp_nav_menu( array(
						'theme_location' => 'primary',
						'menu_id'        => 'primary-menu',
						'container'      => false,
						'fallback_cb'    => 'pressgrid_fallback_menu',
					) );
					?>
				</nav>
			</div>
		</div>
	</div>

	<!-- ═══ BREAKING NEWS ═══ -->
	<?php if ( get_theme_mod( 'pressgrid_breaking_news_enabled', true ) ) :
		$breaking_cat  = absint( get_theme_mod( 'pressgrid_breaking_news_category', 0 ) );
		$breaking_args = array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => 8,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'no_found_rows'  => true,
		);
		if ( $breaking_cat > 0 ) { $breaking_args['cat'] = $breaking_cat; }
		$breaking_q = new WP_Query( $breaking_args );
		if ( $breaking_q->have_posts() ) : ?>
		<div class="pg-breaking-bar" aria-live="polite" aria-label="<?php esc_attr_e( 'Breaking News', 'pressgrid' ); ?>">
			<div class="pg-container">
				<div class="pg-breaking-inner">
					<span class="pg-breaking-label"><?php esc_html_e( 'Breaking', 'pressgrid' ); ?></span>
					<div class="pg-breaking-ticker" role="marquee">
						<ul>
							<?php while ( $breaking_q->have_posts() ) : $breaking_q->the_post(); ?>
								<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
							<?php endwhile; wp_reset_postdata(); ?>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<?php endif; endif; ?>

	<?php pressgrid_render_ad( 'header' ); ?>

	<!-- ═══ MAIN ═══ -->
	<main id="pg-main-content" class="pg-main" role="main">
		<div class="pg-container">
