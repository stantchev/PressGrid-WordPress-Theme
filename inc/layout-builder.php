<?php
/**
 * PressGrid Layout Builder
 *
 * @package PressGrid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function pressgrid_default_sections() {
	return array(
		array( 'id' => 'hero',          'label' => 'Hero',          'enabled' => true,  'layout' => 'hero-grid',   'category' => 0, 'post_count' => 3,  'custom_html' => '' ),
		array( 'id' => 'latest_posts',  'label' => 'Latest Posts',  'enabled' => true,  'layout' => 'grid-3',      'category' => 0, 'post_count' => 6,  'custom_html' => '' ),
		array( 'id' => 'category_grid', 'label' => 'Category Grid', 'enabled' => true,  'layout' => 'grid-4',      'category' => 0, 'post_count' => 4,  'custom_html' => '' ),
		array( 'id' => 'trending',      'label' => 'Trending',      'enabled' => true,  'layout' => 'list',        'category' => 0, 'post_count' => 6,  'custom_html' => '' ),
		array( 'id' => 'editor_picks',  'label' => 'Editor Picks',  'enabled' => false, 'layout' => 'grid-4',      'category' => 0, 'post_count' => 4,  'custom_html' => '' ),
		array( 'id' => 'newsletter',    'label' => 'Newsletter',     'enabled' => true,  'layout' => 'newsletter',  'category' => 0, 'post_count' => 0,  'custom_html' => '' ),
		array( 'id' => 'custom_html',   'label' => 'Custom HTML',   'enabled' => false, 'layout' => 'custom_html', 'category' => 0, 'post_count' => 0,  'custom_html' => '' ),
		array( 'id' => 'ad_block',      'label' => 'Ad Block',      'enabled' => false, 'layout' => 'ad_block',    'category' => 0, 'post_count' => 0,  'custom_html' => '' ),
		array( 'id' => 'opinion',       'label' => 'Opinion',        'enabled' => true,  'layout' => 'grid-2',      'category' => 0, 'post_count' => 4,  'custom_html' => '' ),
	);
}

function pressgrid_get_sections() {
	$saved = get_option( 'pressgrid_layout_sections', array() );
	return empty( $saved ) ? pressgrid_default_sections() : $saved;
}

function pressgrid_layout_builder_menu() {
	add_theme_page(
		esc_html__( 'Theme Layout Builder', 'pressgrid' ),
		esc_html__( 'Layout Builder', 'pressgrid' ),
		'manage_options',
		'pressgrid-layout-builder',
		'pressgrid_layout_builder_page'
	);
	add_theme_page(
		esc_html__( 'Theme Security', 'pressgrid' ),
		esc_html__( 'Theme Security', 'pressgrid' ),
		'manage_options',
		'pressgrid-security',
		'pressgrid_security_page'
	);
}
add_action( 'admin_menu', 'pressgrid_layout_builder_menu' );

function pressgrid_save_layout_builder() {
	if ( ! isset( $_POST['pressgrid_layout_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['pressgrid_layout_nonce'] ) ), 'pressgrid_save_layout' ) ) {
		wp_die( esc_html__( 'Security check failed.', 'pressgrid' ) );
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Insufficient permissions.', 'pressgrid' ) );
	}

	$defaults_raw   = pressgrid_default_sections();
	$defaults_by_id = array();
	foreach ( $defaults_raw as $d ) {
		$defaults_by_id[ $d['id'] ] = $d;
	}

	$valid_layouts = array( 'hero-grid', 'grid-2', 'grid-3', 'grid-4', 'list', 'newsletter', 'custom_html', 'ad_block' );
	$submitted     = ( isset( $_POST['sections'] ) && is_array( $_POST['sections'] ) ) ? $_POST['sections'] : array();
	$sections      = array();

	foreach ( $submitted as $raw ) {
		$id = isset( $raw['id'] ) ? sanitize_key( $raw['id'] ) : '';
		if ( ! isset( $defaults_by_id[ $id ] ) ) {
			continue;
		}
		$layout = isset( $raw['layout'] ) ? sanitize_key( $raw['layout'] ) : 'grid-3';
		if ( ! in_array( $layout, $valid_layouts, true ) ) {
			$layout = 'grid-3';
		}
		$sections[] = array(
			'id'          => $id,
			'label'       => $defaults_by_id[ $id ]['label'],
			'enabled'     => ! empty( $raw['enabled'] ),
			'layout'      => $layout,
			'category'    => absint( isset( $raw['category'] ) ? $raw['category'] : 0 ),
			'post_count'  => min( 20, max( 1, absint( isset( $raw['post_count'] ) ? $raw['post_count'] : 4 ) ) ),
			'custom_html' => isset( $raw['custom_html'] ) ? wp_kses_post( wp_unslash( $raw['custom_html'] ) ) : '',
		);
	}

	if ( empty( $sections ) ) {
		$sections = pressgrid_default_sections();
	}

	update_option( 'pressgrid_layout_sections', $sections );
	delete_transient( 'pressgrid_hero_posts' );
	delete_transient( 'pressgrid_trending_posts' );
	delete_transient( 'pressgrid_editor_picks' );
	delete_transient( 'pressgrid_latest_posts' );

	wp_safe_redirect( add_query_arg( array( 'page' => 'pressgrid-layout-builder', 'saved' => '1' ), admin_url( 'themes.php' ) ) );
	exit;
}
add_action( 'admin_post_pressgrid_save_layout', 'pressgrid_save_layout_builder' );

function pressgrid_layout_builder_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to access this page.', 'pressgrid' ) );
	}

	$sections      = pressgrid_get_sections();
	$categories    = get_categories( array( 'hide_empty' => false ) );
	$saved_msg     = isset( $_GET['saved'] ) && '1' === sanitize_key( $_GET['saved'] );
	$valid_layouts = array(
		'hero-grid'   => 'Hero Grid',
		'grid-2'      => '2-Column Grid',
		'grid-3'      => '3-Column Grid',
		'grid-4'      => '4-Column Grid',
		'list'        => 'List',
		'newsletter'  => 'Newsletter',
		'custom_html' => 'Custom HTML',
		'ad_block'    => 'Ad Block',
	);
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'PressGrid Layout Builder', 'pressgrid' ); ?></h1>
		<?php if ( $saved_msg ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Layout saved successfully.', 'pressgrid' ); ?></p></div>
		<?php endif; ?>
		<p><?php esc_html_e( 'Enable/disable and configure each homepage section. Order is fixed to section sequence.', 'pressgrid' ); ?></p>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="pressgrid_save_layout" />
			<?php wp_nonce_field( 'pressgrid_save_layout', 'pressgrid_layout_nonce' ); ?>
			<?php foreach ( $sections as $index => $section ) :
				$sid         = sanitize_key( $section['id'] );
				$enabled     = ! empty( $section['enabled'] );
				$layout      = isset( $section['layout'] ) ? $section['layout'] : 'grid-3';
				$category    = isset( $section['category'] ) ? absint( $section['category'] ) : 0;
				$post_count  = isset( $section['post_count'] ) ? absint( $section['post_count'] ) : 4;
				$custom_html = isset( $section['custom_html'] ) ? $section['custom_html'] : '';
				$idx         = absint( $index );
				?>
				<div style="background:#fff;border:1px solid #ccd0d4;padding:16px;margin-bottom:10px;border-radius:4px;">
					<input type="hidden" name="sections[<?php echo $idx; ?>][id]" value="<?php echo esc_attr( $sid ); ?>" />
					<h3 style="margin:0 0 10px;"><?php echo esc_html( $section['label'] ); ?></h3>
					<label style="display:inline-flex;align-items:center;gap:6px;margin-bottom:10px;">
						<input type="checkbox" name="sections[<?php echo $idx; ?>][enabled]" value="1" <?php checked( $enabled ); ?> />
						<?php esc_html_e( 'Enable', 'pressgrid' ); ?>
					</label>
					<div style="display:flex;gap:12px;flex-wrap:wrap;">
						<div>
							<label><?php esc_html_e( 'Layout', 'pressgrid' ); ?></label><br />
							<select name="sections[<?php echo $idx; ?>][layout]">
								<?php foreach ( $valid_layouts as $lv => $ll ) : ?>
									<option value="<?php echo esc_attr( $lv ); ?>" <?php selected( $layout, $lv ); ?>><?php echo esc_html( $ll ); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div>
							<label><?php esc_html_e( 'Category', 'pressgrid' ); ?></label><br />
							<select name="sections[<?php echo $idx; ?>][category]">
								<option value="0" <?php selected( $category, 0 ); ?>><?php esc_html_e( '— All —', 'pressgrid' ); ?></option>
								<?php foreach ( $categories as $cat ) : ?>
									<option value="<?php echo absint( $cat->term_id ); ?>" <?php selected( $category, $cat->term_id ); ?>><?php echo esc_html( $cat->name ); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div>
							<label><?php esc_html_e( 'Post Count', 'pressgrid' ); ?></label><br />
							<input type="number" name="sections[<?php echo $idx; ?>][post_count]" value="<?php echo $post_count; ?>" min="1" max="20" style="width:70px;" />
						</div>
					</div>
					<?php if ( 'custom_html' === $sid ) : ?>
					<div style="margin-top:10px;">
						<label><?php esc_html_e( 'Custom HTML', 'pressgrid' ); ?></label><br />
						<textarea name="sections[<?php echo $idx; ?>][custom_html]" rows="4" style="width:100%;font-family:monospace;"><?php echo esc_textarea( $custom_html ); ?></textarea>
					</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
			<?php submit_button( esc_html__( 'Save Layout', 'pressgrid' ) ); ?>
		</form>
	</div>
	<?php
}

function pressgrid_security_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to access this page.', 'pressgrid' ) );
	}
	$disable_xmlrpc = (bool) get_option( 'pressgrid_disable_xmlrpc', false );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'PressGrid Security Settings', 'pressgrid' ); ?></h1>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="pressgrid_save_security" />
			<?php wp_nonce_field( 'pressgrid_security_save', 'pressgrid_security_nonce' ); ?>
			<table class="form-table">
				<tr>
					<th><?php esc_html_e( 'XML-RPC', 'pressgrid' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="pressgrid_disable_xmlrpc" value="1" <?php checked( $disable_xmlrpc ); ?> />
							<?php esc_html_e( 'Disable XML-RPC endpoint', 'pressgrid' ); ?>
						</label>
						<p class="description"><?php esc_html_e( 'Improves security but may break some plugins.', 'pressgrid' ); ?></p>
					</td>
				</tr>
			</table>
			<?php submit_button( esc_html__( 'Save Security Settings', 'pressgrid' ) ); ?>
		</form>
	</div>
	<?php
}
