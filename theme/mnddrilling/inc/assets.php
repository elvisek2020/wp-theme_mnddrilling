<?php
/**
 * Styly a skripty.
 *
 * @package MNDDrilling
 */

defined( 'ABSPATH' ) || exit;

/**
 * Verze souboru podle data změny – prohlížeč po nasazení nové verze nepoužije starou cache.
 *
 * @param string $path Cesta relativní ke složce šablony.
 * @return string
 */
function mnd_asset_version( $path ) {
	$file = MND_DIR . '/' . $path;
	return file_exists( $file ) ? (string) filemtime( $file ) : MND_VERSION;
}

/**
 * Načtení stylů a skriptů na webu.
 */
function mnd_enqueue_assets() {
	wp_enqueue_style( 'mnd-main', MND_URI . '/assets/css/main.css', array(), mnd_asset_version( 'assets/css/main.css' ) );
	wp_enqueue_style( 'mnd-print', MND_URI . '/assets/css/print.css', array( 'mnd-main' ), mnd_asset_version( 'assets/css/print.css' ), 'print' );

	wp_enqueue_script(
		'mnd-theme',
		MND_URI . '/assets/js/theme.js',
		array(),
		mnd_asset_version( 'assets/js/theme.js' ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
	wp_localize_script(
		'mnd-theme',
		'mndL10n',
		array(
			'prev'     => mnd_t( 'Předchozí', 'Previous' ),
			'next'     => mnd_t( 'Další', 'Next' ),
			'close'    => mnd_t( 'Zavřít', 'Close' ),
			'interval' => (int) get_theme_mod( 'mnd_slider_interval', 4 ) * 1000,
		)
	);

	// Nepotřebné pro návštěvníky.
	wp_dequeue_style( 'classic-theme-styles' );
	if ( ! is_user_logged_in() ) {
		wp_dequeue_style( 'dashicons' );
	}
}
add_action( 'wp_enqueue_scripts', 'mnd_enqueue_assets', 20 );

/**
 * Barva lišty prohlížeče na mobilu a záložní favicon (pokud není nastavena ikona webu).
 */
function mnd_head_meta() {
	echo '<meta name="theme-color" content="#ffffff">' . "\n";
	if ( ! has_site_icon() ) {
		printf( '<link rel="icon" href="%s" sizes="any">' . "\n", esc_url( MND_URI . '/favicon.ico' ) );
	}
}
add_action( 'wp_head', 'mnd_head_meta', 2 );
