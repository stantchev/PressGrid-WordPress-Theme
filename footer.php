<?php
/**
 * Footer Template
 *
 * @package PressGrid
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
		</div><!-- .pg-container -->
	</main><!-- #pg-main-content -->

	<?php pressgrid_render_ad( 'footer' ); ?>

	<footer class="pg-site-footer" role="contentinfo">
		<div class="pg-container">
			<div class="pg-footer-widgets">
				<?php for ( $i = 1; $i <= 4; $i++ ) : ?>
					<div>
						<?php if ( is_active_sidebar( 'footer-' . $i ) ) :
							dynamic_sidebar( 'footer-' . $i );
						else : ?>
							<?php if ( 1 === $i ) : ?>
								<p class="pg-footer-widget-title"><?php bloginfo( 'name' ); ?></p>
								<p style="font-size:12px;line-height:1.65;"><?php bloginfo( 'description' ); ?></p>
							<?php elseif ( 2 === $i ) : ?>
								<p class="pg-footer-widget-title"><?php esc_html_e( 'Navigate', 'pressgrid' ); ?></p>
								<?php wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false, 'fallback_cb' => false, 'depth' => 1 ) ); ?>
							<?php endif; ?>
						<?php endif; ?>
					</div>
				<?php endfor; ?>
			</div>

			<div class="pg-footer-bottom">
				<span>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>. <?php esc_html_e( 'All rights reserved.', 'pressgrid' ); ?></span>
				<?php wp_nav_menu( array( 'theme_location' => 'footer', 'container' => 'nav', 'container_class' => 'pg-footer-links', 'depth' => 1, 'fallback_cb' => false ) ); ?>
				<?php if ( get_theme_mod( 'pressgrid_show_footer_credit', true ) ) : ?>
					<span class="pg-footer-credit"><?php esc_html_e( 'Theme by', 'pressgrid' ); ?> <a href="https://stanchev.bg/" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Milen Stanchev', 'pressgrid' ); ?></a></span>
				<?php endif; ?>
			</div>
		</div>
	</footer>

</div><!-- .pg-site-wrapper -->
<?php wp_footer(); ?>
</body>
</html>
