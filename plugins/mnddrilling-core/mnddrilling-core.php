<?php
/**
 * Plugin Name: MND Drilling Core
 * Plugin URI: https://github.com/elvisek2020/wp-theme_mnddrilling
 * Description: Funkce webu mnd-drilling.eu nezávislé na šabloně. Postupně nahrazuje dřívější pluginy (zatím kostra).
 * Version: 0.1.0
 * Requires at least: 6.6
 * Tested up to: 7.1
 * Requires PHP: 8.1
 * Author: Zdeněk Král (ElvisEK)
 * Update URI: https://github.com/elvisek2020/wp-theme_mnddrilling
 * Text Domain: mnddrilling-core
 */

defined( 'ABSPATH' ) || exit;

define( 'MND_CORE_FILE', __FILE__ );
define( 'MND_CORE_BASENAME', plugin_basename( __FILE__ ) );
$mnd_core_meta = get_file_data( __FILE__, array( 'v' => 'Version', 't' => 'Tested up to' ) );
define( 'MND_CORE_VERSION', $mnd_core_meta['v'] );
define( 'MND_CORE_TESTED_WP', $mnd_core_meta['t'] ?: '7.1' ); // při ověření nové verze WP zvednout v hlavičce
unset( $mnd_core_meta );

// Moduly z inc/ – každý jde vypnout zakomentováním řádku. Doplňují se postupně podle docs/PLAN.md.
$mnd_core_modules = array(
);

foreach ( $mnd_core_modules as $mnd_core_module ) {
	require __DIR__ . '/inc/' . $mnd_core_module . '.php';
}
unset( $mnd_core_modules, $mnd_core_module );
