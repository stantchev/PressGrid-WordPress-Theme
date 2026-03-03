<?php
/**
 * Hero — large left article + right column stack (newspaper style)
 *
 * @package PressGrid
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$args       = isset( $args ) ? (array) $args : array();
$post_count = max( 2, absint( $args['post_count'] ?? 4 ) );
$query      = pressgrid_get_hero_posts( $post_count );

if ( ! $query->have_posts() ) { return; }
$all_posts = $query->posts;
$main      = array_shift( $all_posts );
?>
<section class="pg-section pg-hero" aria-label="<?php esc_attr_e( 'Featured Stories', 'pressgrid' ); ?>">
	<div class="pg-hero-grid">

		<!-- ── LEFT: Main big article ── -->
		<?php if ( $main ) :
			$thumb_url = get_the_post_thumbnail_url( $main->ID, 'pressgrid-hero' );
		?>
		<div class="pg-hero-main">
			<?php if ( $thumb_url ) : ?>
				<img src="<?php echo esc_url( $thumb_url ); ?>"
				     alt="<?php echo esc_attr( get_the_title( $main->ID ) ); ?>"
				     loading="eager" fetchpriority="high" decoding="async"
				     width="800" height="360" />
			<?php else : ?>
				<div style="width:100%;height:360px;background:#222;display:flex;align-items:center;justify-content:center;">
					<span style="color:#555;font-size:3rem;">&#128247;</span>
				</div>
			<?php endif; ?>
			<div class="pg-hero-overlay" aria-hidden="true"></div>
			<div class="pg-hero-caption">
				<?php echo wp_kses_post( pressgrid_get_category_label( $main->ID ) ); ?>
				<h2><a href="<?php echo esc_url( get_permalink( $main->ID ) ); ?>"><?php echo esc_html( get_the_title( $main->ID ) ); ?></a></h2>
				<?php $exc = get_the_excerpt( $main->ID );
				if ( $exc ) : ?>
					<p><?php echo esc_html( wp_trim_words( $exc, 16 ) ); ?></p>
				<?php endif; ?>
			</div>
		</div>
		<?php endif; ?>

		<!-- ── RIGHT: Side stack ── -->
		<div class="pg-hero-sidebar">
			<?php foreach ( $all_posts as $side ) :
				$sthumb = get_the_post_thumbnail_url( $side->ID, 'pressgrid-thumb' );
			?>
				<div class="pg-hero-side-item">
					<div class="pg-hero-side-thumb">
						<?php if ( $sthumb ) : ?>
							<a href="<?php echo esc_url( get_permalink( $side->ID ) ); ?>" tabindex="-1" aria-hidden="true">
								<img src="<?php echo esc_url( $sthumb ); ?>"
								     alt="<?php echo esc_attr( get_the_title( $side->ID ) ); ?>"
								     loading="lazy" decoding="async" width="110" height="100" />
							</a>
						<?php else : ?>
							<div style="width:110px;height:100px;background:#eee;"></div>
						<?php endif; ?>
					</div>
					<div class="pg-hero-side-body">
						<h3><a href="<?php echo esc_url( get_permalink( $side->ID ) ); ?>"><?php echo esc_html( get_the_title( $side->ID ) ); ?></a></h3>
						<?php $sex = get_the_excerpt( $side->ID );
						if ( $sex ) : ?>
							<p><?php echo esc_html( wp_trim_words( $sex, 12 ) ); ?></p>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

	</div>
</section>
<?php wp_reset_postdata(); ?>
