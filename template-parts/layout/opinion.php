<?php
/**
 * Opinion Section — 2-col items with author avatar
 *
 * @package PressGrid
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$args       = isset( $args ) ? (array) $args : array();
$category   = absint( $args['category']   ?? 0 );
$post_count = absint( $args['post_count'] ?? 4 );

$q_args = array(
	'post_type' => 'post', 'post_status' => 'publish',
	'posts_per_page' => $post_count, 'orderby' => 'date',
	'order' => 'DESC', 'no_found_rows' => true,
);
if ( $category > 0 ) { $q_args['cat'] = $category; }
$query = new WP_Query( $q_args );
if ( ! $query->have_posts() ) { return; }
$title = $category > 0 ? get_cat_name( $category ) : esc_html__( 'Opinion', 'pressgrid' );
?>
<section class="pg-section" aria-label="<?php echo esc_attr( $title ); ?>">
	<div class="pg-section-header">
		<h2 class="pg-section-title"><?php echo esc_html( $title ); ?></h2>
	</div>
	<div class="pg-opinion-grid">
		<?php while ( $query->have_posts() ) : $query->the_post();
			$author_id = (int) get_post_field( 'post_author', get_the_ID() );
		?>
			<div class="pg-opinion-item">
				<?php if ( has_post_thumbnail() ) : ?>
					<a href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
						<?php the_post_thumbnail( 'pressgrid-thumb', array( 'loading' => 'lazy', 'decoding' => 'async', 'alt' => esc_attr( get_the_title() ) ) ); ?>
					</a>
				<?php else : ?>
					<?php echo get_avatar( $author_id, 80 ); ?>
				<?php endif; ?>
				<div>
					<div class="pg-opinion-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></div>
					<div class="pg-opinion-author">
						<a href="<?php echo esc_url( get_author_posts_url( $author_id ) ); ?>">
							<?php echo esc_html( get_the_author_meta( 'display_name', $author_id ) ); ?>
						</a>
					</div>
				</div>
			</div>
		<?php endwhile; wp_reset_postdata(); ?>
	</div>
</section>
