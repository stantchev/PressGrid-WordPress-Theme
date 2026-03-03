<?php
/**
 * Newsletter Section
 *
 * @package PressGrid
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<section class="pg-newsletter-section" aria-label="<?php esc_attr_e( 'Newsletter signup', 'pressgrid' ); ?>">
	<div class="pg-container">
		<div class="pg-newsletter-inner">
			<div>
				<h3 class="pg-newsletter-title"><?php esc_html_e( 'Subscribe to Our Newsletter', 'pressgrid' ); ?></h3>
				<p class="pg-newsletter-desc"><?php esc_html_e( 'Get the latest news delivered straight to your inbox. No spam, ever.', 'pressgrid' ); ?></p>
			</div>
			<div>
				<div class="pg-nl-form">
					<label for="pg-nl-email-main" class="screen-reader-text"><?php esc_html_e( 'Your email address', 'pressgrid' ); ?></label>
					<input type="email" id="pg-nl-email-main" placeholder="<?php esc_attr_e( 'Enter your email address', 'pressgrid' ); ?>" autocomplete="email" />
					<button type="button"><?php esc_html_e( 'Subscribe', 'pressgrid' ); ?></button>
				</div>
			</div>
		</div>
	</div>
</section>
