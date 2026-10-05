<?php
/**
 * Plugin Name: MND Drilling Core
 * Plugin URI: https://github.com/elvisek2020/wp-theme_mnddrilling
 * Description: Funkce webu nezávislé na šabloně — typy obsahu a pole, pořadí přetažením, kategorie volných pozic, ochrana a log přihlášení, hardening a bezpečnostní hlavičky, WebP, sitemap a /llms.txt, automatické aktualizace, Údržba webu a Zdraví webu. Nahrazuje 4 pluginy. Vše jde zapnout a vypnout v Nastavení → MND Drilling Core.
 * Version: 0.2.0
 * Requires at least: 6.6
 * Tested up to: 7.1
 * Requires PHP: 7.4
 * Author: Zdeněk Král (ElvisEK)
 * Update URI: https://github.com/elvisek2020/wp-theme_mnddrilling
 * Text Domain: mnddrilling-core
 */

defined( 'ABSPATH' ) || exit;

define( 'MND_CORE_FILE', __FILE__ );
define( 'MND_CORE_BASENAME', plugin_basename( __FILE__ ) );
define( 'MND_CORE_REPO', 'elvisek2020/wp-theme_mnddrilling' );
$mnd_core_meta = get_file_data( __FILE__, array( 'v' => 'Version', 't' => 'Tested up to' ) );
define( 'MND_CORE_VERSION', $mnd_core_meta['v'] );
define( 'MND_CORE_TESTED_WP', $mnd_core_meta['t'] ? $mnd_core_meta['t'] : '7.1' ); // při ověření nové verze WP zvednout v hlavičce
unset( $mnd_core_meta );

// Moduly z inc/ – každý jde vypnout zakomentováním řádku.
$mnd_core_modules = array(
	'core',          // přihlášení, info o serveru, komentáře, aktualizace, WebP, hardening, Údržba webu, updater
	'seo-extra',     // bezpečnostní hlavičky, sitemap, /llms.txt
	'health',        // widget Zdraví webu na Nástěnce
	'content-types', // typy obsahu a taxonomie (dřív ve staré šabloně)
	'fields',        // pole obsahu místo ACF, převod dokumentů ze Simple Fields
	'positions',     // kategorie a e-maily volných pozic (dřív Ultimate Theme Settings)
	'order',         // pořadí přetažením (místo Simple Custom Post Order)
);

foreach ( $mnd_core_modules as $mnd_core_module ) {
	require __DIR__ . '/inc/' . $mnd_core_module . '.php';
}
unset( $mnd_core_modules, $mnd_core_module );
