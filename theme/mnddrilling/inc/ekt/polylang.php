<?php
/**
 * EKT module: polylang
 * Version: 1.0.0
 * Requires: common
 * Replaces: –
 * Assets: –
 * Summary: Texty šablony ve více jazycích s Polylangem: ekt_t( cs, en ) a registr přeložitelných textů ekt_pll().
 */

defined( 'ABSPATH' ) || exit;

/*
 * Menu po jazycích při přepnutí šablony doplňuje common (ekt_switch_polylang_menus), og:locale
 * modul seo. Tady jen texty šablony:
 *  - ekt_t( 'Česky', 'English' ) – pevný text podle jazyka stránky (angličtina pro 'en', jinak čeština);
 *  - ekt_pll( 'Zdrojový text' ) – text z registru, přeložitelný v Jazyky → Překlady textů
 *    (skupina = název šablony). Registr: filtr ekt_pll_strings( $strings ) – zdroj (čeština)
 *    => výchozí angličtina; kde překlad v Polylangu chybí, angličtina dostane výchozí text.
 *    add_filter( 'ekt_pll_strings', fn( $s ) => $s + array( 'Kontakty' => 'Contacts' ) );
 * Bez Polylangu vrací ekt_pll() zdroj a ekt_t() češtinu (nebo angličtinu podle locale webu).
 * Texty kanonických modulů jdou přeložit jejich filtry (ekt_consent_texts…) s ekt_t().
 *
 * Veřejné funkce: ekt_lang(), ekt_t( $cs, $en ), ekt_pll( $string ), ekt_pll_strings().
 */

/**
 * Kód jazyka aktuální stránky (cs, en…): Polylang, jinak locale webu.
 *
 * @return string
 */
function ekt_lang(): string {
	$lang = function_exists( 'pll_current_language' ) ? (string) pll_current_language( 'slug' ) : '';
	return '' !== $lang ? $lang : substr( determine_locale(), 0, 2 );
}

/**
 * Pevný text šablony v češtině nebo angličtině podle jazyka stránky.
 *
 * @param string $cs Česky.
 * @param string $en Anglicky.
 * @return string
 */
function ekt_t( string $cs, string $en ): string {
	return 'en' === ekt_lang() ? $en : $cs;
}

/**
 * Registr přeložitelných textů: zdroj (čeština) => výchozí angličtina.
 *
 * @return array<string,string>
 */
function ekt_pll_strings(): array {
	return array_map( 'strval', (array) apply_filters( 'ekt_pll_strings', array() ) );
}

/**
 * Registrace textů do Jazyky → Překlady textů.
 */
function ekt_pll_register_strings(): void {
	if ( ! function_exists( 'pll_register_string' ) ) {
		return;
	}
	foreach ( array_keys( ekt_pll_strings() ) as $string ) {
		pll_register_string( (string) $string, (string) $string, EKT_NAME );
	}
}
add_action( 'init', 'ekt_pll_register_strings' );

/**
 * Přeložený text šablony.
 *
 * @param string $string Zdrojový (český) text z registru.
 * @return string
 */
function ekt_pll( string $string ): string {
	$translated = function_exists( 'pll__' ) ? (string) pll__( $string ) : $string;
	if ( $translated === $string && 'en' === ekt_lang() ) {
		$defaults = ekt_pll_strings();
		if ( isset( $defaults[ $string ] ) && '' !== $defaults[ $string ] ) {
			return $defaults[ $string ];
		}
	}
	return $translated;
}
