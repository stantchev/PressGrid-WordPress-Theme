<?php
/**
 * Trending + Editor's Picks + Video — 3-column tab block (newspaper style)
 *
 * @package PressGrid
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$args       = isset( $args ) ? (array) $args : array();
$post_count = absint( $args['post_count'] ?? 4 );

$trending = pressgrid_get_trending_posts( $post_count );
$picks    = pressgrid_get_editor_picks( $post_count );

$video_q = new WP_Query( array(
	'post_type'      => 'post',
	'post_status'    => 'publish',
	'posts_per_page' => 1,
	'meta_key'       => '_thumbnail_id',
	'no_found_rows'  => true,
) );
?>
<section class="pg-section">
	<div class="pg-tab-block">

		<!-- ── Trending Now ── -->
		<div class="pg-tab-col">
			<div class="pg-tab-header"><?php esc_html_e( 'Trending Now', 'pressgrid' ); ?></div>
			<?php if ( $trending->have_posts() ) : ?>
				<ul class="pg-trending-list">
					<?php while ( $trending->have_posts() ) : $trending->the_post(); ?>
						<li class="pg-trending-item">
							<div class="pg-trending-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></div>
							<div class="pg-trending-meta"><?php echo esc_html( get_the_date() ); ?></div>
						</li>
					<?php endwhile; wp_reset_postdata(); ?>
				</ul>
			<?php else : ?>
				<p style="padding:12px;font-size:13px;color:#999;"><?php esc_html_e( 'No trending posts yet.', 'pressgrid' ); ?></p>
			<?php endif; ?>
		</div>

		<!-- ── Editor's Picks ── -->
		<div class="pg-tab-col">
			<div class="pg-tab-header"><?php esc_html_e( "Editor's Picks", 'pressgrid' ); ?></div>
			<?php if ( $picks->have_posts() ) : ?>
				<ul class="pg-trending-list">
					<?php while ( $picks->have_posts() ) : $picks->the_post(); ?>
						<li class="pg-trending-item">
							<div class="pg-trending-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></div>
							<div class="pg-trending-meta"><?php echo esc_html( get_the_date() ); ?></div>
						</li>
					<?php endwhile; wp_reset_postdata(); ?>
				</ul>
			<?php else : ?>
				<p style="padding:12px;font-size:13px;color:#999;"><?php esc_html_e( 'No picks yet.', 'pressgrid' ); ?></p>
			<?php endif; ?>
		</div>

		<!-- ── Video / Featured ── -->
		<div class="pg-tab-col">
			<div class="pg-tab-header dark"><?php esc_html_e( 'Video', 'pressgrid' ); ?></div>
			<?php if ( $video_q->have_posts() ) : $video_q->the_post(); ?>
				<div class="pg-video-wrap">
					<div class="pg-video-thumb">
						<?php the_post_thumbnail( 'pressgrid-card', array( 'loading' => 'lazy', 'decoding' => 'async', 'alt' => esc_attr( get_the_title() ) ) ); ?>
						<div class="pg-video-play" aria-hidden="true">&#9654;</div>
					</div>
					<div class="pg-video-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></div>
				</div>
				<?php wp_reset_postdata();
			else : ?>
				<p style="padding:12px;font-size:13px;color:#999;"><?php esc_html_e( 'No featured video yet.', 'pressgrid' ); ?></p>
			<?php endif; ?>
		</div>

	</div>
</section>
