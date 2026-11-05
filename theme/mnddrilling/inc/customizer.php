<?php
/**
 * Projektová volba v Přizpůsobit (Vzhled → Přizpůsobit → MND Drilling → Obecné): interval slideru.
 *
 * ID měření GA4 je v sekci SEO a analytika (kanonický modul analytics, dřív theme_mod mnd_ga_id –
 * převede ho migrace common), sítě v sekci Sociální sítě (modul social).
 *
 * @package MNDDrilling
 */

defined( 'ABSPATH' ) || exit;

ekt_setting_register(
	'site_slider_interval',
	array(
		'type'        => 'number',
		'default'     => 4,
		'label'       => 'Interval slideru na titulce (sekundy)',
		'description' => 'Obrázky slideru se spravují v menu Slidy (náhledový obrázek, pořadí přetažením).',
		'section'     => 'general',
		'priority'    => 10,
		'input_attrs' => array(
			'min'  => 3,
			'max'  => 20,
			'step' => 1,
		),
		'sanitize'    => static function ( $value ): int {
			return max( 3, min( 20, absint( $value ) ) );
		},
	)
);

/**
 * Starý theme_mod mnd_slider_interval → ekt_site_slider_interval (migrace common, jednou).
 *
 * @param array  $map Mapa starý klíč => nový.
 * @param string $p   Starý prefix.
 * @return array
 */
function mnd_customizer_legacy_map( array $map, string $p ): array {
	if ( 'mnd' === $p ) {
		$map['mnd_slider_interval'] = 'ekt_site_slider_interval';
	}
	return $map;
}
add_filter( 'ekt_legacy_mod_map', 'mnd_customizer_legacy_map', 10, 2 );
