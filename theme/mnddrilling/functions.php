<?php
/**
 * MND Drilling — šablona pro www.mnd-drilling.eu
 *
 * Každý modul v inc/ jde vypnout zakomentováním řádku níže.
 */

defined( 'ABSPATH' ) || exit;

define( 'MND_VERSION', (string) wp_get_theme( get_template() )->get( 'Version' ) );
define( 'MND_DIR', get_template_directory() );
define( 'MND_URI', get_template_directory_uri() );

// Moduly se doplňují postupně podle docs/PLAN.md.
$mnd_modules = array(
);

foreach ( $mnd_modules as $mnd_module ) {
	require MND_DIR . '/inc/' . $mnd_module . '.php';
}
