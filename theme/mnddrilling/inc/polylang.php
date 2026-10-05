<?php
/**
 * Integrace s Polylangem (čeština / angličtina).
 *
 * Texty šablony, které jde překládat v Jazyky → Překlady textů, mají stejné zdrojové
 * řetězce jako původní šablona – uložené anglické překlady zůstávají. Kde překlad
 * chybí, angličtina dostane výchozí text (dřív se na anglickém webu ukazovala čeština).
 * Šablona funguje i bez Polylangu – přepínač jazyků se pak jen nezobrazí.
 *
 * @package MNDDrilling
 */

defined( 'ABSPATH' ) || exit;

/**
 * Přeložitelné texty: zdroj (čeština) => výchozí angličtina.
 *
 * @return array
 */
function mnd_pll_strings() {
	return array(
		'Mohlo by Vás zajímat'        => 'Are interested in',
		'Sídlo firmy'                 => 'Headquarters',
		'Kontakty'                    => 'Contacts',
		'Compliance Hotline'          => 'Compliance Hotline',
		'Stáhnout'                    => 'Download',
		'Upravit'                     => 'Edit',
		'Mám zájem o tuto pozici'     => 'I am interested in this position',
		'Zpět na výpis'               => 'Back to the list',
		'Začátek'                     => 'Start',
		'Předchozí'                   => 'Previous',
		'Další'                       => 'Next',
		'Konec'                       => 'End',
		'Strana'                      => 'Page',
		'z'                           => 'of',
		'404 Stránka nebyla nalezena' => '404 Page not found',
		'Žádné výsledky nenalezeny'   => 'No results found',
		'Výsledky hledání pro:'       => 'Search results for:',
	);
}

/**
 * Registrace textů do Jazyky → Překlady textů (skupina „MND Drilling“).
 */
function mnd_pll_register_strings() {
	if ( ! function_exists( 'pll_register_string' ) ) {
		return;
	}
	foreach ( array_keys( mnd_pll_strings() ) as $string ) {
		pll_register_string( $string, $string, 'MND Drilling' );
	}
}
add_action( 'init', 'mnd_pll_register_strings' );

/**
 * Přeložený text šablony.
 *
 * @param string $string Zdrojový (český) text.
 * @return string
 */
function mnd_pll( $string ) {
	$translated = function_exists( 'pll__' ) ? pll__( $string ) : $string;
	if ( $translated === $string && mnd_is_en() ) {
		$defaults = mnd_pll_strings();
		if ( isset( $defaults[ $string ] ) ) {
			return $defaults[ $string ];
		}
	}
	return $translated;
}

/**
 * Jazyky pro přepínač (aktuální první): [ ['slug','name','url','current_lang'], … ].
 *
 * @return array
 */
function mnd_languages() {
	if ( ! function_exists( 'pll_the_languages' ) ) {
		return array();
	}
	$languages = pll_the_languages(
		array(
			'raw'           => 1,
			'hide_if_empty' => 0,
		)
	);
	if ( ! is_array( $languages ) || count( $languages ) < 2 ) {
		return array();
	}
	usort(
		$languages,
		function ( $a, $b ) {
			return (int) ! empty( $b['current_lang'] ) - (int) ! empty( $a['current_lang'] );
		}
	);
	return $languages;
}

/**
 * Po přepnutí šablony převezme přiřazení menu pro jednotlivé jazyky (doplní chybějící).
 *
 * Polylang si přiřazení menu ukládá zvlášť pro každou šablonu, takže by po
 * aktivaci nové šablony dlaždice a menu v patičce „zmizely“.
 *
 * @param string   $old_name  Název předchozí šablony.
 * @param WP_Theme $old_theme Předchozí šablona.
 */
function mnd_pll_migrate_menus( $old_name, $old_theme = null ) {
	if ( ! function_exists( 'PLL' ) || ! $old_theme instanceof WP_Theme ) {
		return;
	}

	$old_slug = $old_theme->get_stylesheet();
	$new_slug = get_stylesheet();
	$options  = PLL()->options;
	$menus    = isset( $options['nav_menus'] ) ? (array) $options['nav_menus'] : array();

	if ( empty( $menus[ $old_slug ] ) || ! is_array( $menus[ $old_slug ] ) ) {
		return;
	}

	$new     = isset( $menus[ $new_slug ] ) && is_array( $menus[ $new_slug ] ) ? $menus[ $new_slug ] : array();
	$changed = false;
	foreach ( $menus[ $old_slug ] as $location => $languages ) {
		foreach ( (array) $languages as $lang => $menu_id ) {
			if ( $menu_id && empty( $new[ $location ][ $lang ] ) ) {
				$new[ $location ][ $lang ] = (int) $menu_id;
				$changed                   = true;
			}
		}
	}
	if ( ! $changed ) {
		return;
	}
	$menus[ $new_slug ] = $new;

	if ( is_object( $options ) && method_exists( $options, 'set' ) ) {
		$options->set( 'nav_menus', $menus ); // Polylang 3.7+
	} else {
		$raw              = get_option( 'polylang', array() );
		$raw['nav_menus'] = $menus;
		update_option( 'polylang', $raw );
	}
}
// Priorita 20 = až po mapování menu, které při přepnutí šablony dělá WordPress.
add_action( 'after_switch_theme', 'mnd_pll_migrate_menus', 20, 2 );
