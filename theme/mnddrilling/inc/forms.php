<?php
/**
 * Formuláře Formidable: třída pro styly šablony a předvyplnění pozice.
 *
 * Formulář „Zájem o pozici“ dostane název pozice z adresy (?pozice=…) přímo na serveru
 * – dřív to dělal jQuery skript. Hodnota se očistí, do stránky se nikdy nevypíše surová.
 *
 * @package MNDDrilling
 */

defined( 'ABSPATH' ) || exit;

/**
 * Pozice z adresy (očištěná), prázdný řetězec = žádná.
 *
 * @return string
 */
function mnd_requested_position() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- jen čtení pro předvyplnění
	$value = isset( $_GET['pozice'] ) && is_string( $_GET['pozice'] ) ? sanitize_text_field( wp_unslash( $_GET['pozice'] ) ) : '';
	return mb_substr( $value, 0, 200 );
}

/**
 * Třída „form“ na formulářích (jako v původní šabloně).
 *
 * @param object $form Formulář.
 */
function mnd_form_classes( $form ) {
	echo ' form mnd-form';
}
add_action( 'frm_form_classes', 'mnd_form_classes' );

/**
 * Klíče polí s názvem pozice (formulář Kariéra, česká i anglická varianta pole).
 *
 * @return array
 */
function mnd_position_field_keys() {
	return (array) apply_filters( 'mnd_position_field_keys', array( 'e72h8n', 'e72h8n2' ) );
}

/**
 * Výchozí hodnota pole s pozicí z adresy.
 *
 * @param mixed  $value Výchozí hodnota.
 * @param object $field Pole.
 * @return mixed
 */
function mnd_form_position_default( $value, $field ) {
	if ( is_object( $field ) && isset( $field->field_key ) && in_array( $field->field_key, mnd_position_field_keys(), true ) ) {
		$position = mnd_requested_position();
		if ( '' !== $position ) {
			return $position;
		}
	}
	return $value;
}
add_filter( 'frm_get_default_value', 'mnd_form_position_default', 10, 2 );
