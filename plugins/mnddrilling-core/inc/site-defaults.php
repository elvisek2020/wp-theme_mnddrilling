<?php
/**
 * Nastavení kanonických modulů, která web MND Drilling potřebuje jinak než výchozí
 * (jednorázový krok migrace, správce je pak může v Nastavení → MND Drilling Core měnit):
 *
 *  - Řazení přetažením: všechny typy obsahu, které se řadily dřív (Simple Custom Post Order
 *    a do 0.3.x inc/order.php). Výchozí kanon řadí jen stránky – bez toho by se na webu
 *    přeházely položky, lidé, pozice i slidy.
 *  - /llms.txt jako strom stránek po jazycích: titulka je výpis bez článků, režim „auto“
 *    by zvolil blog a vypsal prázdný přehled. Úvod z popisu firmy na stránce „O nás“.
 *
 * @package MNDDrillingCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Typy obsahu řazené podle pořadí (stejné jako do verze 0.3.x).
 *
 * @return string[]
 */
function mnd_core_site_order_types(): array {
	return array( 'page', 'slide', 'timelineitem', 'companies', 'position', 'document', 'item', 'mapitem', 'person' );
}

/**
 * Krok migrace site/defaults: doplní chybějící typy řazení a režim /llms.txt.
 * Idempotentní – typy jen přidává, nastavení správce nepřepíše.
 *
 * @return bool
 */
function mnd_core_site_defaults_migrate(): bool {
	if ( ! function_exists( 'ekc_opt' ) || ! function_exists( 'ekc_opt_set' ) ) {
		return false;
	}
	if ( function_exists( 'ekc_order_types' ) ) {
		$types   = (array) ekc_opt( 'order_types' );
		$missing = array_diff( mnd_core_site_order_types(), $types );
		if ( $missing ) {
			ekc_opt_set( 'order_types', array_values( array_merge( $types, $missing ) ) );
		}
	}
	$saved = get_option( 'ekc_settings', array() );
	$saved = is_array( $saved ) ? $saved : array();
	if ( function_exists( 'ekc_llms_mode' ) && ! array_key_exists( 'llms_mode', $saved ) ) {
		ekc_opt_set( 'llms_mode', 'pages' );
	}
	// Úvod /llms.txt jako do 0.3.x z popisu firmy na stránce „O nás“ (titulka je jen výpis divizí).
	$about = get_page_by_path( 'o-spolecnosti/about-company' );
	if ( function_exists( 'ekc_llms_mode' ) && ! array_key_exists( 'llms_intro', $saved ) && $about ) {
		$text = trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( strip_shortcodes( (string) $about->post_content ) ) ) );
		if ( '' !== $text ) {
			ekc_opt_set( 'llms_intro', wp_trim_words( $text, 60, '…' ) );
		}
	}
	return true;
}

add_filter(
	'ekc_migrations',
	static function ( array $steps ): array {
		$steps['site/defaults'] = 'mnd_core_site_defaults_migrate';
		return $steps;
	}
);
