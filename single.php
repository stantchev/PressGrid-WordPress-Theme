<?php
/**
 * Single Post Template
 *
 * Включва: progress bar, share бутони, related posts, back-to-top.
 *
 * @package PressGrid
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();

$sidebar = sanitize_key( get_theme_mod( 'pressgrid_sidebar_position', 'right' ) );
$full    = ( 'none' === $sidebar );
?>

<div class="pg-content-area <?php echo $full ? 'full-width' : ''; ?>">

	<article class="pg-main-col" id="pg-post-<?php the_ID(); ?>" <?php post_class(); ?>>

		<?php pressgrid_breadcrumbs(); ?>

		<?php while ( have_posts() ) : the_post(); ?>

			<header class="pg-single-header">
				<?php echo wp_kses_post( pressgrid_get_category_label( get_the_ID() ) ); ?>
				<h1 class="pg-single-title"><?php the_title(); ?></h1>
				<div class="pg-single-meta">
					<?php pressgrid_post_meta( get_the_ID() ); ?>
				</div>
			</header>

			<?php if ( has_post_thumbnail() ) : ?>
				<div class="pg-featured-image">
					<?php the_post_thumbnail( 'pressgrid-wide', array(
						'loading'       => 'eager',
						'fetchpriority' => 'high',
						'decoding'      => 'async',
						'alt'           => esc_attr( get_the_title() ),
					) ); ?>
				</div>
			<?php endif; ?>

			<?php pressgrid_render_ad( 'in_article' ); ?>

			<div class="pg-entry-content">
				<?php the_content(); ?>
				<?php wp_link_pages( array(
					'before'      => '<div class="pg-page-links"><span>' . esc_html__( 'Pages:', 'pressgrid' ) . '</span>',
					'after'       => '</div>',
					'link_before' => '<span>',
					'link_after'  => '</span>',
				) ); ?>
			</div>

			<?php
			// ── Тагове ──
			$tags = get_the_tags();
			if ( $tags && ! is_wp_error( $tags ) ) : ?>
				<div class="pg-post-tags">
					<span><?php esc_html_e( 'Tags:', 'pressgrid' ); ?></span>
					<?php foreach ( $tags as $tag ) : ?>
						<a class="pg-tag" href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>">
							<?php echo esc_html( $tag->name ); ?>
						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php
			// ── Share бутони ──────────────────────────────────────────────
			// Използва Web Share API (мобилни) + copy-to-clipboard fallback.
			// Нула external JS, нула Facebook/Twitter SDK.
			?>
			<div class="pg-share-bar" aria-label="<?php esc_attr_e( 'Сподели статията', 'pressgrid' ); ?>">
				<span class="pg-share-label"><?php esc_html_e( 'Сподели:', 'pressgrid' ); ?></span>

				<?php /* Web Share API — показва се само ако браузърът го поддържа (JS го прави visible) */ ?>
				<button class="pg-share-btn" id="pg-share-native"
				        style="display:none"
				        aria-label="<?php esc_attr_e( 'Сподели', 'pressgrid' ); ?>">
					<span class="pg-share-icon" aria-hidden="true">↗</span>
					<?php esc_html_e( 'Сподели', 'pressgrid' ); ?>
				</button>

				<?php /* Copy link */ ?>
				<button class="pg-share-btn" id="pg-share-copy"
				        data-url="<?php echo esc_url( get_permalink() ); ?>"
				        data-copied="<?php esc_attr_e( 'Копирано!', 'pressgrid' ); ?>"
				        aria-label="<?php esc_attr_e( 'Копирай линка', 'pressgrid' ); ?>">
					<span class="pg-share-icon" aria-hidden="true">🔗</span>
					<?php esc_html_e( 'Копирай линка', 'pressgrid' ); ?>
				</button>

				<?php /* LinkedIn (href, без JS) */ ?>
				<a class="pg-share-btn"
				   href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode( get_permalink() ); ?>"
				   target="_blank" rel="noopener noreferrer"
				   aria-label="<?php esc_attr_e( 'Сподели в LinkedIn', 'pressgrid' ); ?>">
					<span class="pg-share-icon" aria-hidden="true">in</span>
					LinkedIn
				</a>
			</div>

			<?php
			// ── Author box ──
			$author_id  = (int) get_the_author_meta( 'ID' );
			$author_bio = get_the_author_meta( 'description', $author_id );
			if ( $author_bio ) : ?>
				<div class="pg-author-box">
					<div class="pg-author-avatar">
						<?php echo get_avatar( $author_id, 80, '', esc_attr( get_the_author_meta( 'display_name', $author_id ) ) ); ?>
					</div>
					<div class="pg-author-info">
						<p class="pg-author-name">
							<a href="<?php echo esc_url( get_author_posts_url( $author_id ) ); ?>">
								<?php echo esc_html( get_the_author_meta( 'display_name', $author_id ) ); ?>
							</a>
						</p>
						<p class="pg-author-bio"><?php echo esc_html( $author_bio ); ?></p>
					</div>
				</div>
			<?php endif; ?>

			<?php
			// ── Предишна / Следваща статия ──
			the_post_navigation( array(
				'prev_text' => '<span class="nav-subtitle">' . esc_html__( 'Previous:', 'pressgrid' ) . '</span> <span class="nav-title">%title</span>',
				'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Next:', 'pressgrid' ) . '</span> <span class="nav-title">%title</span>',
			) );
			?>

			<?php
			// ── Related Posts ──────────────────────────────────────────────
			// Показва 3 статии от същата категория.
			// Без плъгин — чист WP_Query.
			$cats        = get_the_category();
			$related_ids = array();
			if ( $cats ) {
				$related_q = new WP_Query( array(
					'post_type'           => 'post',
					'post_status'         => 'publish',
					'posts_per_page'      => 3,
					'cat'                 => $cats[0]->term_id,
					'post__not_in'        => array( get_the_ID() ),
					'orderby'             => 'rand',
					'no_found_rows'       => true,
					'ignore_sticky_posts' => true,
				) );
				if ( $related_q->have_posts() ) : ?>
					<div class="pg-related-posts">
						<h3 class="pg-related-title"><?php esc_html_e( 'Свързани статии', 'pressgrid' ); ?></h3>
						<div class="pg-related-grid">
							<?php while ( $related_q->have_posts() ) : $related_q->the_post(); ?>
								<div class="pg-related-item">
									<?php if ( has_post_thumbnail() ) : ?>
										<a href="<?php the_permalink(); ?>" class="pg-related-item-thumb" tabindex="-1" aria-hidden="true">
											<?php the_post_thumbnail( 'pressgrid-card', array( 'loading' => 'lazy', 'decoding' => 'async', 'alt' => esc_attr( get_the_title() ) ) ); ?>
										</a>
									<?php endif; ?>
									<div class="pg-related-item-title">
										<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
									</div>
									<div class="pg-related-item-meta"><?php echo esc_html( get_the_date() ); ?></div>
								</div>
							<?php endwhile; wp_reset_postdata(); ?>
						</div>
					</div>
				<?php endif;
			} ?>

		<?php endwhile; ?>

		<?php comments_template(); ?>

	</article>

	<?php if ( ! $full ) : ?>
		<?php get_sidebar(); ?>
	<?php endif; ?>

</div>

<?php
// ── Back To Top button ────────────────────────────────────────────────────
// Инжектиран тук, управляван от main.js (появява се след 400px scroll)
?>
<button id="pg-back-to-top"
        aria-label="<?php esc_attr_e( 'Обратно нагоре', 'pressgrid' ); ?>"
        title="<?php esc_attr_e( 'Обратно нагоре', 'pressgrid' ); ?>">↑</button>

<?php get_footer(); ?>
