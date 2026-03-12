<?php
/**
 * PressGrid — Frankfurter Exchange Rate Integration
 *
 * Показва валутен тикер в топ бара когато сайтът има активна "Бизнес" секция
 * в Layout Builder-а. Заменя Breaking News тикера автоматично.
 *
 * ════════════════════════════════════════════════════════════════
 *  КАК СЕ ВЗИМА API-ТО
 * ════════════════════════════════════════════════════════════════
 *  Frankfurter е напълно БЕЗПЛАТЕН и не изисква регистрация или API ключ.
 *  Данните идват директно от Европейската централна банка (ЕЦБ).
 *
 *  Endpoint:   https://api.frankfurter.app/latest?from=EUR&to=USD,GBP,BGN,CHF,JPY
 *  Документация: https://www.frankfurter.app/docs
 *
 *  Ограничения:
 *  — Обновява се веднъж дневно (работни дни, ~16:00 CET)
 *  — Няма rate limit за нормална употреба
 *  — Няма нужда от API ключ — просто работи
 *
 *  Настройки: Appearance → Customize → PressGrid: Валути
 * ════════════════════════════════════════════════════════════════
 *
 * @package PressGrid
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/* ============================================================
   CUSTOMIZER НАСТРОЙКИ
   ============================================================ */
add_action( 'customize_register', 'pressgrid_forex_customize_register' );
function pressgrid_forex_customize_register( $wp_customize ) {
	$wp_customize->add_section( 'pressgrid_forex', array(
		'title'       => __( 'PressGrid: Валути (Forex)', 'pressgrid' ),
		'description' => __( 'Валутният тикер се показва автоматично в топ бара когато има активна секция с бизнес категория в Layout Builder. Не е нужен API ключ — използва безплатния Frankfurter API (ЕЦБ данни).', 'pressgrid' ),
		'priority'    => 43,
	) );

	// Базова валута
	$wp_customize->add_setting( 'pressgrid_forex_base', array(
		'default'           => 'EUR',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'pressgrid_forex_base', array(
		'label'       => __( 'Базова валута', 'pressgrid' ),
		'description' => __( 'EUR, USD, BGN, GBP и т.н.', 'pressgrid' ),
		'section'     => 'pressgrid_forex',
		'type'        => 'text',
	) );

	// Целеви валути
	$wp_customize->add_setting( 'pressgrid_forex_targets', array(
		'default'           => 'USD,GBP,BGN,CHF,JPY',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'pressgrid_forex_targets', array(
		'label'       => __( 'Показвани валути', 'pressgrid' ),
		'description' => __( 'Разделени със запетая. Напр: USD,GBP,BGN,CHF', 'pressgrid' ),
		'section'     => 'pressgrid_forex',
		'type'        => 'text',
	) );

	// Slug на бизнес категорията (auto-detect override)
	$wp_customize->add_setting( 'pressgrid_forex_category_slug', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'pressgrid_forex_category_slug', array(
		'label'       => __( 'Slug на бизнес категорията (незадължително)', 'pressgrid' ),
		'description' => __( 'Оставете празно за auto-detect (търси "business", "biznes", "бизнес", "finance", "финанси"). Попълнете само ако категорията има различен slug.', 'pressgrid' ),
		'section'     => 'pressgrid_forex',
		'type'        => 'text',
	) );

	// Принудително показване (без да чака бизнес секция)
	$wp_customize->add_setting( 'pressgrid_forex_force', array(
		'default'           => false,
		'sanitize_callback' => 'rest_sanitize_boolean',
	) );
	$wp_customize->add_control( 'pressgrid_forex_force', array(
		'label'   => __( 'Показвай винаги (независимо от Layout Builder)', 'pressgrid' ),
		'section' => 'pressgrid_forex',
		'type'    => 'checkbox',
	) );
}

/* ============================================================
   ЛОГИКА: ИМА ЛИ БИЗНЕС СЕКЦИЯ В LAYOUT BUILDER?
   ============================================================ */

/**
 * Проверява дали Layout Builder има активна секция с бизнес/финанси категория.
 * Използва се за условно показване на forex тикера вместо breaking news.
 *
 * @return bool
 */
function pressgrid_has_business_section() {
	// Принудително показване от Customizer
	if ( get_theme_mod( 'pressgrid_forex_force', false ) ) {
		return true;
	}

	// Transient cache — проверяваме само веднъж на страница зареждане
	$cache_key = 'pressgrid_has_biz_section';
	$cached    = get_transient( $cache_key );
	if ( false !== $cached ) {
		return (bool) $cached;
	}

	// Вземи всички активни секции от Layout Builder
	$sections = pressgrid_get_sections();
	if ( empty( $sections ) ) {
		set_transient( $cache_key, 0, 5 * MINUTE_IN_SECONDS );
		return false;
	}

	// Намери category ID-та на бизнес категориите
	$biz_ids = pressgrid_get_business_category_ids();
	if ( empty( $biz_ids ) ) {
		set_transient( $cache_key, 0, 5 * MINUTE_IN_SECONDS );
		return false;
	}

	// Провери дали някоя активна секция е таргетирала бизнес категория
	$found = false;
	foreach ( $sections as $section ) {
		if ( empty( $section['enabled'] ) ) { continue; }
		$cat_id = absint( $section['category'] ?? 0 );
		if ( $cat_id > 0 && in_array( $cat_id, $biz_ids, true ) ) {
			$found = true;
			break;
		}
	}

	set_transient( $cache_key, $found ? 1 : 0, 5 * MINUTE_IN_SECONDS );
	return $found;
}

/**
 * Връща масив с category ID-та на бизнес/финанси категории.
 * Търси по slug — auto-detect + ръчен override от Customizer.
 *
 * @return int[]
 */
function pressgrid_get_business_category_ids() {
	// Ръчен slug override от Customizer
	$manual_slug = sanitize_text_field( get_theme_mod( 'pressgrid_forex_category_slug', '' ) );

	// Auto-detect slugs (широк обхват, поддържа BG и EN)
	$auto_slugs = array(
		'business', 'biznes', 'бизнес',
		'finance', 'finances', 'finansi', 'финанси',
		'economy', 'ikonomika', 'икономика',
		'markets', 'pazari', 'пазари',
		'money', 'pari', 'пари',
	);

	$slugs_to_check = ! empty( $manual_slug )
		? array( $manual_slug )
		: $auto_slugs;

	$ids = array();
	foreach ( $slugs_to_check as $slug ) {
		$cat = get_category_by_slug( $slug );
		if ( $cat && ! is_wp_error( $cat ) ) {
			$ids[] = (int) $cat->term_id;
		}
	}

	return array_unique( $ids );
}

/* ============================================================
   DATA FETCHING — Frankfurter API с transient cache
   ============================================================ */

/**
 * Взима курсовете от Frankfurter API.
 * Cache: 6 часа (курсовете се обновяват веднъж дневно).
 *
 * @return array|false  ['base' => 'EUR', 'rates' => ['USD' => 1.08, ...], 'date' => '2025-03-07']
 */
function pressgrid_get_forex_rates() {
	$base    = strtoupper( sanitize_text_field( get_theme_mod( 'pressgrid_forex_base', 'EUR' ) ) );
	$targets = strtoupper( sanitize_text_field( get_theme_mod( 'pressgrid_forex_targets', 'USD,GBP,BGN,CHF,JPY' ) ) );

	// Почисти и валидирай валутите (само букви и запетаи)
	$targets = preg_replace( '/[^A-Z,]/', '', $targets );
	$targets = trim( $targets, ',' );

	if ( ! $base || ! $targets ) { return false; }

	$cache_key = 'pressgrid_forex_' . md5( $base . $targets );
	$cached    = get_transient( $cache_key );
	if ( false !== $cached ) { return $cached; }

	$url = add_query_arg(
		array(
			'from' => $base,
			'to'   => $targets,
		),
		'https://api.frankfurter.app/latest'
	);

	$response = wp_remote_get( $url, array(
		'timeout'    => 6,
		'sslverify'  => true,
		'user-agent' => 'PressGrid WordPress Theme/2.5',
	) );

	if ( is_wp_error( $response ) ) { return false; }
	if ( 200 !== wp_remote_retrieve_response_code( $response ) ) { return false; }

	$body = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( empty( $body['rates'] ) ) { return false; }

	$result = array(
		'base'  => $body['base']  ?? $base,
		'date'  => $body['date']  ?? gmdate( 'Y-m-d' ),
		'rates' => $body['rates'] ?? array(),
	);

	// Cache 6 часа — данните на ЕЦБ се обновяват веднъж дневно
	set_transient( $cache_key, $result, 6 * HOUR_IN_SECONDS );
	return $result;
}

/* ============================================================
   RENDER — Forex тикер за топ бара
   ============================================================ */

/**
 * Рендира валутния тикер в топ бара.
 * Извиква се от header.php вместо breaking news когато има бизнес секция.
 */
function pressgrid_render_forex_ticker() {
	$data = pressgrid_get_forex_rates();

	if ( ! $data || empty( $data['rates'] ) ) {
		// Показваме placeholder ако API-то не отговаря
		echo '<div class="pg-forex-bar pg-forex-error">';
		echo '<span class="pg-forex-label">FX</span>';
		echo '<span style="font-size:11px;color:#888;">' . esc_html__( 'Данните не са налични', 'pressgrid' ) . '</span>';
		echo '</div>';
		return;
	}

	$base  = esc_html( $data['base'] );
	$date  = esc_html( $data['date'] );
	$rates = $data['rates'];
	?>
	<div class="pg-forex-bar" aria-label="<?php esc_attr_e( 'Валутни курсове', 'pressgrid' ); ?>">
		<span class="pg-forex-label">
			<span class="pg-forex-base"><?php echo $base; ?></span>
		</span>
		<div class="pg-forex-ticker-wrap">
			<ul class="pg-forex-ticker" aria-live="off">
				<?php foreach ( $rates as $currency => $rate ) :
					// Форматиране: JPY без десетични, останалите с 4 знака
					$formatted = ( $rate >= 100 )
						? number_format( $rate, 2 )
						: number_format( $rate, 4 );

					// Посока (винаги показваме, без историческо сравнение)
					?>
				<li class="pg-forex-item">
					<span class="pg-forex-pair"><?php echo esc_html( $base . '/' . $currency ); ?></span>
					<span class="pg-forex-rate"><?php echo esc_html( $formatted ); ?></span>
				</li>
				<?php endforeach; ?>
				<li class="pg-forex-item pg-forex-source">
					<a href="https://www.frankfurter.app/" target="_blank" rel="noopener noreferrer"
					   title="<?php echo esc_attr__( 'Данни: Европейска централна банка', 'pressgrid' ); ?>">
						ECB · <?php echo $date; ?>
					</a>
				</li>
			</ul>
		</div>
	</div>
	<?php
}

/* ============================================================
   ИЗЧИСТВАНЕ НА CACHE при смяна на Customizer настройките
   ============================================================ */
add_action( 'customize_save_after', 'pressgrid_clear_forex_cache' );
function pressgrid_clear_forex_cache() {
	$base    = strtoupper( get_theme_mod( 'pressgrid_forex_base', 'EUR' ) );
	$targets = strtoupper( get_theme_mod( 'pressgrid_forex_targets', 'USD,GBP,BGN,CHF,JPY' ) );
	delete_transient( 'pressgrid_forex_' . md5( $base . $targets ) );
	delete_transient( 'pressgrid_has_biz_section' );
}

// Изчисти и при смяна на Layout Builder секциите
add_action( 'update_option_pressgrid_layout_sections', function() {
	delete_transient( 'pressgrid_has_biz_section' );
} );
