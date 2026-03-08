<?php
/**
 * PressGrid — OpenWeatherMap Integration
 *
 * Provides current weather + 5-day forecast via OWM free API.
 * Data is cached as WordPress transients to avoid hitting rate limits.
 *
 * Settings: Appearance → Customize → PressGrid: Weather
 *
 * @package PressGrid
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/* ============================================================
   CUSTOMIZER SETTINGS
   ============================================================ */
add_action( 'customize_register', 'pressgrid_weather_customize_register' );
function pressgrid_weather_customize_register( $wp_customize ) {
	$wp_customize->add_section( 'pressgrid_weather', array(
		'title'    => __( 'PressGrid: Времето', 'pressgrid' ),
		'priority' => 42,
	) );

	$fields = array(
		'pressgrid_weather_api_key'   => array( 'label' => __( 'OpenWeather API ключ', 'pressgrid' ),   'default' => '' ),
		'pressgrid_weather_city'      => array( 'label' => __( 'Град (напр. Sofia,BG)', 'pressgrid' ),  'default' => 'Sofia,BG' ),
		'pressgrid_weather_units'     => array( 'label' => __( 'Единици (metric / imperial)', 'pressgrid' ), 'default' => 'metric' ),
	);

	foreach ( $fields as $id => $cfg ) {
		$wp_customize->add_setting( $id, array( 'default' => $cfg['default'], 'sanitize_callback' => 'sanitize_text_field' ) );
		$wp_customize->add_control( $id, array(
			'label'   => $cfg['label'],
			'section' => 'pressgrid_weather',
			'type'    => 'text',
		) );
	}

	// Enable toggle
	$wp_customize->add_setting( 'pressgrid_weather_enabled', array( 'default' => false, 'sanitize_callback' => 'rest_sanitize_boolean' ) );
	$wp_customize->add_control( 'pressgrid_weather_enabled', array(
		'label'   => __( 'Покажи прогноза за времето', 'pressgrid' ),
		'section' => 'pressgrid_weather',
		'type'    => 'checkbox',
	) );

	// Topbar widget toggle
	$wp_customize->add_setting( 'pressgrid_weather_topbar', array( 'default' => true, 'sanitize_callback' => 'rest_sanitize_boolean' ) );
	$wp_customize->add_control( 'pressgrid_weather_topbar', array(
		'label'   => __( 'Покажи в горната лента', 'pressgrid' ),
		'section' => 'pressgrid_weather',
		'type'    => 'checkbox',
	) );
}

/* ============================================================
   DATA FETCHING — with transient cache
   ============================================================ */

/**
 * Get current weather data. Cached 30 minutes.
 *
 * @return array|false
 */
function pressgrid_get_weather() {
	$api_key = sanitize_text_field( get_theme_mod( 'pressgrid_weather_api_key', '' ) );
	$city    = sanitize_text_field( get_theme_mod( 'pressgrid_weather_city', 'Sofia,BG' ) );
	$units   = get_theme_mod( 'pressgrid_weather_units', 'metric' ) === 'imperial' ? 'imperial' : 'metric';

	if ( ! $api_key || ! $city ) { return false; }

	$cache_key = 'pressgrid_weather_' . md5( $city . $units );
	$cached    = get_transient( $cache_key );
	if ( false !== $cached ) { return $cached; }

	$url = add_query_arg( array(
		'q'     => $city,
		'appid' => $api_key,
		'units' => $units,
		'lang'  => 'bg',
	), 'https://api.openweathermap.org/data/2.5/weather' );

	$response = wp_remote_get( $url, array( 'timeout' => 8, 'sslverify' => true ) );

	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		return false;
	}

	$data = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( empty( $data['main'] ) ) { return false; }

	$result = array(
		'city'        => $data['name'],
		'country'     => $data['sys']['country'] ?? '',
		'temp'        => round( $data['main']['temp'] ),
		'feels_like'  => round( $data['main']['feels_like'] ),
		'temp_min'    => round( $data['main']['temp_min'] ),
		'temp_max'    => round( $data['main']['temp_max'] ),
		'humidity'    => $data['main']['humidity'],
		'wind_speed'  => round( $data['wind']['speed'] ?? 0, 1 ),
		'clouds'      => $data['clouds']['all'] ?? 0,
		'description' => $data['weather'][0]['description'] ?? '',
		'icon_code'   => $data['weather'][0]['icon'] ?? '01d',
		'units'       => $units,
		'timestamp'   => time(),
	);

	set_transient( $cache_key, $result, 30 * MINUTE_IN_SECONDS );
	return $result;
}

/**
 * Get 5-day forecast (every 3h from OWM free tier, we take one per day).
 * Cached 1 hour.
 *
 * @return array
 */
function pressgrid_get_forecast() {
	$api_key = sanitize_text_field( get_theme_mod( 'pressgrid_weather_api_key', '' ) );
	$city    = sanitize_text_field( get_theme_mod( 'pressgrid_weather_city', 'Sofia,BG' ) );
	$units   = get_theme_mod( 'pressgrid_weather_units', 'metric' ) === 'imperial' ? 'imperial' : 'metric';

	if ( ! $api_key || ! $city ) { return array(); }

	$cache_key = 'pressgrid_forecast_' . md5( $city . $units );
	$cached    = get_transient( $cache_key );
	if ( false !== $cached ) { return $cached; }

	$url = add_query_arg( array(
		'q'     => $city,
		'appid' => $api_key,
		'units' => $units,
		'cnt'   => 40,
		'lang'  => 'bg',
	), 'https://api.openweathermap.org/data/2.5/forecast' );

	$response = wp_remote_get( $url, array( 'timeout' => 8, 'sslverify' => true ) );

	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		return array();
	}

	$data = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( empty( $data['list'] ) ) { return array(); }

	// Pick one entry per day (closest to noon), skip today
	$days  = array();
	$today = gmdate( 'Y-m-d' );

	foreach ( $data['list'] as $entry ) {
		$date = gmdate( 'Y-m-d', $entry['dt'] );
		$hour = (int) gmdate( 'H', $entry['dt'] );
		if ( $date === $today ) { continue; }
		if ( ! isset( $days[ $date ] ) ) {
			$days[ $date ] = $entry;
		} else {
			// Prefer entry closest to 12:00
			$cur_h = (int) gmdate( 'H', $days[ $date ]['dt'] );
			if ( abs( $hour - 12 ) < abs( $cur_h - 12 ) ) {
				$days[ $date ] = $entry;
			}
		}
	}

	$result = array();
	foreach ( array_slice( $days, 0, 5 ) as $date => $entry ) {
		$result[] = array(
			'date'      => $date,
			'day_name'  => pressgrid_weather_day_name( $date ),
			'temp'      => round( $entry['main']['temp'] ),
			'icon_code' => $entry['weather'][0]['icon'] ?? '01d',
		);
	}

	set_transient( $cache_key, $result, HOUR_IN_SECONDS );
	return $result;
}

/* ============================================================
   HELPERS
   ============================================================ */

/**
 * Map OWM icon code → emoji.
 */
function pressgrid_weather_emoji( $icon_code ) {
	$map = array(
		'01d' => '☀️',  '01n' => '🌙',
		'02d' => '🌤️', '02n' => '🌤️',
		'03d' => '🌥️', '03n' => '🌥️',
		'04d' => '☁️',  '04n' => '☁️',
		'09d' => '🌧️', '09n' => '🌧️',
		'10d' => '🌦️', '10n' => '🌧️',
		'11d' => '⛈️',  '11n' => '⛈️',
		'13d' => '❄️',  '13n' => '❄️',
		'50d' => '🌫️', '50n' => '🌫️',
	);
	return $map[ $icon_code ] ?? '🌡️';
}

/**
 * Translate day name to Bulgarian abbreviation.
 */
function pressgrid_weather_day_name( $date_str ) {
	$days_bg = array( 'НД', 'ПН', 'ВТ', 'СР', 'ЧТ', 'ПТ', 'СБ' );
	$dow = (int) gmdate( 'w', strtotime( $date_str ) );
	return $days_bg[ $dow ];
}

/**
 * Return degree symbol + unit label.
 */
function pressgrid_weather_unit_sym( $units ) {
	return 'imperial' === $units ? '°F' : '°C';
}

/* ============================================================
   SIDEBAR WEATHER WIDGET — full card
   ============================================================ */
function pressgrid_render_weather_widget() {
	if ( ! get_theme_mod( 'pressgrid_weather_enabled', false ) ) { return; }

	$api_key = get_theme_mod( 'pressgrid_weather_api_key', '' );
	$unit_sym = pressgrid_weather_unit_sym( get_theme_mod( 'pressgrid_weather_units', 'metric' ) );
	?>
	<div class="pg-weather-widget">
		<div class="pg-weather-widget-header">
			<span><?php esc_html_e( 'Времето', 'pressgrid' ); ?></span>
			<button class="pg-ww-locate" id="pg-ww-locate-btn"
				title="<?php esc_attr_e( 'Засичане на локация', 'pressgrid' ); ?>"
				aria-label="<?php esc_attr_e( 'Засичане на локация', 'pressgrid' ); ?>">⊕</button>
		</div>

		<?php if ( ! $api_key ) : ?>
			<div class="pg-weather-error">
				<?php esc_html_e( 'Въведете OpenWeather API ключ в Customizer → PressGrid: Времето', 'pressgrid' ); ?>
			</div>
		<?php else :
			$weather  = pressgrid_get_weather();
			$forecast = pressgrid_get_forecast();

			if ( ! $weather ) : ?>
				<div class="pg-weather-error">
					<?php esc_html_e( 'Данните за времето не са налични. Проверете API ключа и града.', 'pressgrid' ); ?>
				</div>
			<?php else : ?>
			<div class="pg-weather-main">
				<div class="pg-weather-city"><?php echo esc_html( strtoupper( $weather['city'] ) ); ?></div>
				<div class="pg-weather-desc"><?php echo esc_html( ucfirst( $weather['description'] ) ); ?></div>
				<div class="pg-weather-current">
					<div class="pg-weather-icon">
						<img src="https://openweathermap.org/img/wn/<?php echo esc_attr( $weather['icon_code'] ); ?>@2x.png"
							 alt="<?php echo esc_attr( $weather['description'] ); ?>"
							 width="56" height="56" loading="lazy" />
					</div>
					<div>
						<div class="pg-weather-temp-main">
							<?php echo esc_html( $weather['temp'] ); ?><sup><?php echo esc_html( $unit_sym ); ?></sup>
						</div>
						<div class="pg-weather-feels">
							<?php
							printf(
								/* translators: %s: temperature */
								esc_html__( 'Усеща се като %s', 'pressgrid' ),
								esc_html( $weather['feels_like'] . $unit_sym )
							);
							?>
						</div>
					</div>
					<div class="pg-weather-hilo">
						<span class="hi">▲ <?php echo esc_html( $weather['temp_max'] . $unit_sym ); ?></span>
						<span class="lo">▼ <?php echo esc_html( $weather['temp_min'] . $unit_sym ); ?></span>
					</div>
				</div>
			</div>

			<div class="pg-weather-details">
				<div class="pg-weather-detail-item">
					<div class="pg-weather-detail-icon">💧</div>
					<div class="pg-weather-detail-val"><?php echo esc_html( $weather['humidity'] ); ?> %</div>
					<div class="pg-weather-detail-lbl"><?php esc_html_e( 'Влажност', 'pressgrid' ); ?></div>
				</div>
				<div class="pg-weather-detail-item">
					<div class="pg-weather-detail-icon">💨</div>
					<div class="pg-weather-detail-val">
						<?php
						$wind = $weather['wind_speed'];
						echo esc_html( 'metric' === get_theme_mod( 'pressgrid_weather_units', 'metric' ) ? $wind . ' km/h' : $wind . ' mph' );
						?>
					</div>
					<div class="pg-weather-detail-lbl"><?php esc_html_e( 'Вятър', 'pressgrid' ); ?></div>
				</div>
				<div class="pg-weather-detail-item">
					<div class="pg-weather-detail-icon">☁️</div>
					<div class="pg-weather-detail-val"><?php echo esc_html( $weather['clouds'] . ' %' ); ?></div>
					<div class="pg-weather-detail-lbl"><?php esc_html_e( 'Облачност', 'pressgrid' ); ?></div>
				</div>
			</div>

			<?php if ( ! empty( $forecast ) ) : ?>
			<div class="pg-weather-forecast">
				<?php foreach ( $forecast as $day ) : ?>
				<div class="pg-weather-forecast-day">
					<div class="pg-wfd-name"><?php echo esc_html( $day['day_name'] ); ?></div>
					<div class="pg-wfd-icon">
						<img src="https://openweathermap.org/img/wn/<?php echo esc_attr( $day['icon_code'] ); ?>.png"
							 alt="" width="32" height="32" loading="lazy" />
					</div>
					<div class="pg-wfd-temp"><?php echo esc_html( $day['temp'] . '°' ); ?></div>
				</div>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>

			<div class="pg-weather-powered">
				<a href="https://openweathermap.org/" target="_blank" rel="noopener noreferrer">OpenWeatherMap</a>
			</div>
			<?php endif; ?>
		<?php endif; ?>
	</div>
	<?php
}

/* ============================================================
   TOP BAR MINI WIDGET
   ============================================================ */
function pressgrid_render_topbar_weather() {
	if ( ! get_theme_mod( 'pressgrid_weather_enabled', false ) ) { return; }
	if ( ! get_theme_mod( 'pressgrid_weather_topbar', true ) ) { return; }

	$weather  = pressgrid_get_weather();
	if ( ! $weather ) { return; }

	$unit_sym = pressgrid_weather_unit_sym( get_theme_mod( 'pressgrid_weather_units', 'metric' ) );
	$emoji    = pressgrid_weather_emoji( $weather['icon_code'] );

	printf(
		'<div class="pg-topbar-weather" aria-label="%s">
			<span class="pg-tw-icon" aria-hidden="true">%s</span>
			<span class="pg-tw-temp">%s</span>
			<span class="pg-tw-sep" aria-hidden="true">·</span>
			<span class="pg-tw-city">%s</span>
		</div>',
		esc_attr__( 'Прогноза за времето', 'pressgrid' ),
		$emoji,
		esc_html( $weather['temp'] . $unit_sym ),
		esc_html( $weather['city'] )
	);
}

/* ============================================================
   CLEAR CACHE on settings save
   ============================================================ */
add_action( 'customize_save_after', 'pressgrid_clear_weather_cache' );
function pressgrid_clear_weather_cache() {
	$city    = get_theme_mod( 'pressgrid_weather_city', 'Sofia,BG' );
	$units   = get_theme_mod( 'pressgrid_weather_units', 'metric' );
	$suffix  = md5( $city . $units );
	delete_transient( 'pressgrid_weather_' . $suffix );
	delete_transient( 'pressgrid_forecast_' . $suffix );
	delete_transient( 'pressgrid_weather_raw_' . $suffix );
}

/* ============================================================
   AJAX — locate by coordinates (browser geolocation)
   Uses OWM reverse geocoding via lat/lon.
   ============================================================ */
add_action( 'wp_ajax_nopriv_pressgrid_weather_by_coords', 'pressgrid_weather_by_coords_ajax' );
add_action( 'wp_ajax_pressgrid_weather_by_coords',        'pressgrid_weather_by_coords_ajax' );
function pressgrid_weather_by_coords_ajax() {
	check_ajax_referer( 'pressgrid_weather_nonce', 'nonce' );

	$lat     = round( (float) ( $_GET['lat'] ?? 0 ), 4 );
	$lon     = round( (float) ( $_GET['lon'] ?? 0 ), 4 );
	$api_key = get_theme_mod( 'pressgrid_weather_api_key', '' );
	$units   = get_theme_mod( 'pressgrid_weather_units', 'metric' ) === 'imperial' ? 'imperial' : 'metric';

	if ( ! $lat || ! $lon || ! $api_key ) {
		wp_send_json_error( array( 'message' => 'Невалидни данни.' ) );
	}

	$url = add_query_arg( array(
		'lat'   => $lat,
		'lon'   => $lon,
		'appid' => $api_key,
		'units' => $units,
		'lang'  => 'bg',
	), 'https://api.openweathermap.org/data/2.5/weather' );

	$response = wp_remote_get( $url, array( 'timeout' => 8 ) );

	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		wp_send_json_error( array( 'message' => 'Грешка при зареждане на данните.' ) );
	}

	$data = json_decode( wp_remote_retrieve_body( $response ), true );

	wp_send_json_success( array(
		'city'       => $data['name'],
		'temp'       => round( $data['main']['temp'] ),
		'feels_like' => round( $data['main']['feels_like'] ),
		'temp_min'   => round( $data['main']['temp_min'] ),
		'temp_max'   => round( $data['main']['temp_max'] ),
		'humidity'   => $data['main']['humidity'],
		'wind_speed' => round( $data['wind']['speed'] ?? 0, 1 ),
		'description'=> $data['weather'][0]['description'] ?? '',
		'icon_code'  => $data['weather'][0]['icon'] ?? '01d',
		'units'      => $units,
	) );
}

/* ============================================================
   ENQUEUE inline JS for geolocation + live update
   ============================================================ */
add_action( 'wp_footer', 'pressgrid_weather_js' );
function pressgrid_weather_js() {
	if ( ! get_theme_mod( 'pressgrid_weather_enabled', false ) ) { return; }
	?>
<script>
(function(){
	var btn = document.getElementById('pg-ww-locate-btn');
	if(!btn) return;
	btn.addEventListener('click', function(){
		if(!navigator.geolocation){ alert('Геолокацията не е поддържана от браузъра.'); return; }
		btn.textContent = '⟳';
		navigator.geolocation.getCurrentPosition(function(pos){
			var xhr = new XMLHttpRequest();
			xhr.open('GET', '<?php echo esc_js( admin_url( 'admin-ajax.php' ) ); ?>?action=pressgrid_weather_by_coords&nonce=<?php echo esc_js( wp_create_nonce( 'pressgrid_weather_nonce' ) ); ?>&lat=' + pos.coords.latitude + '&lon=' + pos.coords.longitude);
			xhr.onload = function(){
				try{
					var r = JSON.parse(xhr.responseText);
					if(r.success && r.data){
						var d = r.data;
						var u = d.units === 'metric' ? '°C' : '°F';
						var city = document.querySelector('.pg-weather-city');
						var temp = document.querySelector('.pg-weather-temp-main');
						var desc = document.querySelector('.pg-weather-desc');
						if(city) city.textContent = d.city.toUpperCase();
						if(temp) temp.innerHTML = d.temp + '<sup>' + u + '</sup>';
						if(desc) desc.textContent = d.description.charAt(0).toUpperCase() + d.description.slice(1);
						// topbar
						var tw = document.querySelector('.pg-topbar-weather .pg-tw-temp');
						var tc = document.querySelector('.pg-topbar-weather .pg-tw-city');
						if(tw) tw.textContent = d.temp + u;
						if(tc) tc.textContent = d.city;
					}
				}catch(e){}
				btn.textContent = '⊕';
			};
			xhr.onerror = function(){ btn.textContent = '⊕'; };
			xhr.send();
		}, function(){ btn.textContent = '⊕'; alert('Не може да се засече локацията.'); });
	});
})();
</script>
	<?php
}
