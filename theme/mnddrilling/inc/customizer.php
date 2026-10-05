<?php
/**
 * Nastavení šablony v Přizpůsobení (Vzhled → Přizpůsobit → MND Drilling).
 *
 * @package MNDDrilling
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registrace nastavení.
 *
 * @param WP_Customize_Manager $wp_customize Správce přizpůsobení.
 */
function mnd_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'mnddrilling',
		array(
			'title'    => __( 'MND Drilling', 'mnddrilling' ),
			'priority' => 30,
		)
	);

	$wp_customize->add_setting(
		'mnd_slider_interval',
		array(
			'default'           => 4,
			'sanitize_callback' => 'mnd_sanitize_interval',
		)
	);
	$wp_customize->add_control(
		'mnd_slider_interval',
		array(
			'label'       => __( 'Interval slideru na titulce (sekundy)', 'mnddrilling' ),
			'description' => __( 'Obrázky slideru se spravují v menu Slides (náhledový obrázek, pořadí přetažením).', 'mnddrilling' ),
			'section'     => 'mnddrilling',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 3,
				'max'  => 20,
				'step' => 1,
			),
		)
	);

	$wp_customize->add_setting(
		'mnd_ga_id',
		array(
			'default'           => '',
			'sanitize_callback' => 'mnd_sanitize_ga_id',
		)
	);
	$wp_customize->add_control(
		'mnd_ga_id',
		array(
			'label'       => __( 'Google Analytics 4 – ID měření', 'mnddrilling' ),
			'description' => __( 'Např. G-XXXXXXXXXX. Návštěvník nejdřív uvidí cookie lištu, měří se až po souhlasu. Prázdné = bez měření. Dokud je aktivní MonsterInsights, šablona GA nevkládá.', 'mnddrilling' ),
			'section'     => 'mnddrilling',
			'type'        => 'text',
		)
	);
}
add_action( 'customize_register', 'mnd_customize_register' );

/**
 * @param mixed $value Hodnota.
 * @return int
 */
function mnd_sanitize_interval( $value ) {
	return max( 3, min( 20, absint( $value ) ) );
}

/**
 * @param string $value Hodnota.
 * @return string
 */
function mnd_sanitize_ga_id( $value ) {
	$value = strtoupper( trim( (string) $value ) );
	return preg_match( '/^G-[A-Z0-9]+$/', $value ) ? $value : '';
}
