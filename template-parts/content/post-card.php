<?php
/**
 * Post Card — newspaper style
 *
 * @package PressGrid
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'pg-post-card' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a class="pg-post-card-thumb" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
			<?php the_post_thumbnail( 'pressgrid-card', array( 'loading' => 'lazy', 'decoding' => 'async', 'alt' => esc_attr( get_the_title() ) ) ); ?>
		</a>
	<?php endif; ?>
	<div class="pg-post-card-body">
		<?php echo wp_kses_post( pressgrid_get_category_label( get_the_ID() ) ); ?>
		<h2 class="pg-post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<div class="pg-post-meta"><?php pressgrid_post_meta( get_the_ID() ); ?></div>
		<?php if ( has_excerpt() ) : ?>
			<p class="pg-post-excerpt"><?php the_excerpt(); ?></p>
		<?php endif; ?>
	</div>
</article>
