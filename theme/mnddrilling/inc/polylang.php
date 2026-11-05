<?php
/**
 * Polylang (čeština / angličtina): přepínač jazyků a přeložitelné texty šablony.
 *
 * Registr a překlad textů dělá kanonický modul inc/ekt/polylang.php (ekt_pll(), skupina „MND Drilling“
 * v Jazyky → Překlady textů). Zdrojové řetězce jsou stejné jako v původní šabloně, takže uložené
 * anglické překlady zůstávají; kde překlad chybí, angličtina dostane výchozí text níže.
 * Převzetí menu po jazycích při přepnutí šablony dělá inc/ekt/common.php.
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
 * Texty šablony do registru kanonu (filtr ekt_pll_strings).
 *
 * @param array $strings Registr.
 * @return array
 */
function mnd_pll_register( array $strings ): array {
	return $strings + mnd_pll_strings();
}
add_filter( 'ekt_pll_strings', 'mnd_pll_register' );

/**
 * Přeložený text šablony (dřívější název; volá ekt_pll()).
 *
 * @param string $string Zdrojový (český) text.
 * @return string
 */
function mnd_pll( $string ) {
	return function_exists( 'ekt_pll' ) ? ekt_pll( (string) $string ) : (string) $string;
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
