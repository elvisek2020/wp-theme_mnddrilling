<?php
/**
 * Upozornění v administraci, když chybí doprovodný plugin mnddrilling-core (typy obsahu, pole, bezpečnost).
 *
 * @package MNDDrilling
 */

defined( 'ABSPATH' ) || exit;

/**
 * Výpis upozornění pro administrátory.
 */
function mnd_plugin_check_notice() {
	if ( defined( 'MND_CORE_VERSION' ) || ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	$installed = file_exists( WP_PLUGIN_DIR . '/mnddrilling-core/mnddrilling-core.php' );
	$link      = $installed
		? admin_url( 'plugins.php' )
		: 'https://github.com/' . ( defined( 'MND_UPDATE_REPO' ) ? MND_UPDATE_REPO : 'elvisek2020/wp-theme_mnddrilling' ) . '/releases/latest';

	printf(
		'<div class="notice notice-warning"><p><strong>%1$s</strong> %2$s <a href="%3$s"%4$s>%5$s</a></p></div>',
		esc_html__( 'MND Drilling:', 'mnddrilling' ),
		$installed
			? esc_html__( 'doprovodný plugin „MND Drilling Core“ je nainstalovaný, ale není aktivní. Obsahuje typy obsahu (položky, dokumenty, volné pozice…), pole stránek a zabezpečení – bez něj se část webu nezobrazí.', 'mnddrilling' )
			: esc_html__( 'chybí doprovodný plugin „MND Drilling Core“. Obsahuje typy obsahu (položky, dokumenty, volné pozice…), pole stránek a zabezpečení – bez něj se část webu nezobrazí.', 'mnddrilling' ),
		esc_url( $link ),
		$installed ? '' : ' target="_blank" rel="noopener"',
		$installed ? esc_html__( 'Aktivovat v Pluginech', 'mnddrilling' ) : esc_html__( 'Stáhnout mnddrilling-core.zip', 'mnddrilling' )
	);
}
add_action( 'admin_notices', 'mnd_plugin_check_notice' );
