<?php
/**
 * Typy obsahu a taxonomie webu – dřív je registrovala šablona Ultimate for MND Drilling.
 *
 * V pluginu přežijí výměnu šablony. Názvy, adresy (rewrite), podporované části editoru
 * i vyloučení z hledání jsou stejné jako dřív, takže se obsah ani odkazy nemění.
 * Plugin registruje dřív (init, priorita 5); dokud je aktivní stará šablona, registruje je
 * znovu se svými argumenty a platí její verze – chování je tedy stejné jako bez pluginu.
 *
 * @package MNDDrillingCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Popis typů obsahu: typ => [jednotné, množné, Přidat nový…, ikona, podpora, hierarchický, skrýt v hledání].
 *
 * @return array
 */
function mnd_core_content_type_defs() {
	return array(
		'slide'        => array( __( 'Slide', 'mnddrilling-core' ), __( 'Slidy', 'mnddrilling-core' ), __( 'Přidat slide', 'mnddrilling-core' ), 'dashicons-images-alt2', array( 'title', 'thumbnail', 'editor' ), false, true ),
		'timelineitem' => array( __( 'Událost', 'mnddrilling-core' ), __( 'Historie', 'mnddrilling-core' ), __( 'Přidat novou událost', 'mnddrilling-core' ), 'dashicons-backup', array( 'title', 'editor' ), true, true ),
		'companies'    => array( __( 'Společnost', 'mnddrilling-core' ), __( 'Společnosti', 'mnddrilling-core' ), __( 'Přidat novou společnost', 'mnddrilling-core' ), 'dashicons-building', array( 'title', 'editor' ), true, true ),
		'position'     => array( __( 'Volná pozice', 'mnddrilling-core' ), __( 'Volné pozice', 'mnddrilling-core' ), __( 'Přidat novou pozici', 'mnddrilling-core' ), 'dashicons-id', array( 'title', 'editor' ), true, false ),
		'document'     => array( __( 'Dokument', 'mnddrilling-core' ), __( 'Dokumenty', 'mnddrilling-core' ), __( 'Přidat nový dokument', 'mnddrilling-core' ), 'dashicons-media-document', array( 'title' ), true, false ),
		'item'         => array( __( 'Položka', 'mnddrilling-core' ), __( 'Položky', 'mnddrilling-core' ), __( 'Přidat novou položku', 'mnddrilling-core' ), 'dashicons-hammer', array( 'title', 'editor', 'thumbnail' ), true, false ),
		'mapitem'      => array( __( 'Oblast', 'mnddrilling-core' ), __( 'Oblasti', 'mnddrilling-core' ), __( 'Přidat novou oblast', 'mnddrilling-core' ), 'dashicons-location', array( 'title', 'editor', 'thumbnail' ), false, true ),
		'person'       => array( __( 'Člověk', 'mnddrilling-core' ), __( 'Lidé', 'mnddrilling-core' ), __( 'Přidat nového člověka', 'mnddrilling-core' ), 'dashicons-groups', array( 'title', 'thumbnail', 'editor' ), true, false ),
	);
}

/**
 * Adresa (rewrite slug) – „person“ měl ve staré šabloně „personm“, ostatní stejné jako typ.
 *
 * @param string $type Typ obsahu.
 * @return string
 */
function mnd_core_content_type_slug( $type ) {
	return 'person' === $type ? 'personm' : $type;
}

/**
 * Registrace typů obsahu a taxonomií.
 */
function mnd_core_register_content_types() {
	foreach ( mnd_core_content_type_defs() as $type => $def ) {
		if ( post_type_exists( $type ) ) {
			continue;
		}
		list( $singular, $plural, $add_new, $icon, $supports, $hierarchical, $exclude ) = $def;
		register_post_type(
			$type,
			array(
				'labels'              => array(
					'name'               => $plural,
					'singular_name'      => $singular,
					'all_items'          => $plural,
					'add_new'            => __( 'Přidat', 'mnddrilling-core' ),
					'add_new_item'       => $add_new,
					/* translators: %s: item name. */
					'edit_item'          => sprintf( __( 'Upravit: %s', 'mnddrilling-core' ), mb_strtolower( $singular ) ),
					'new_item'           => $add_new,
					'view_item'          => __( 'Zobrazit', 'mnddrilling-core' ),
					'search_items'       => __( 'Hledat', 'mnddrilling-core' ),
					'not_found'          => __( 'Nic nenalezeno.', 'mnddrilling-core' ),
					'not_found_in_trash' => __( 'V koši nic není.', 'mnddrilling-core' ),
				),
				'public'              => true,
				'publicly_queryable'  => true,
				'exclude_from_search' => $exclude,
				'query_var'           => true,
				'menu_position'       => 4,
				'menu_icon'           => $icon,
				'rewrite'             => array(
					'slug'       => mnd_core_content_type_slug( $type ),
					'with_front' => false,
				),
				'has_archive'         => 'slide' === $type ? 'slide' : false,
				'capability_type'     => 'post',
				'hierarchical'        => $hierarchical,
				'supports'            => $supports,
			)
		);
	}

	$taxonomies = array(
		'companygroup' => array( 'companies', __( 'Skupiny společností', 'mnddrilling-core' ), __( 'Skupina společností', 'mnddrilling-core' ) ),
		'itemcategory' => array( 'item', __( 'Kategorie položek', 'mnddrilling-core' ), __( 'Kategorie položek', 'mnddrilling-core' ) ),
	);
	foreach ( $taxonomies as $taxonomy => $def ) {
		if ( taxonomy_exists( $taxonomy ) ) {
			continue;
		}
		register_taxonomy(
			$taxonomy,
			array( $def[0] ),
			array(
				'hierarchical'      => true,
				'labels'            => array(
					'name'          => $def[1],
					'singular_name' => $def[2],
					'menu_name'     => $def[1],
					'all_items'     => $def[1],
					'add_new_item'  => __( 'Přidat', 'mnddrilling-core' ),
					'edit_item'     => __( 'Upravit', 'mnddrilling-core' ),
					'search_items'  => __( 'Hledat', 'mnddrilling-core' ),
					'parent_item'   => __( 'Nadřazená', 'mnddrilling-core' ),
				),
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => $taxonomy ),
			)
		);
	}
}
add_action( 'init', 'mnd_core_register_content_types', 5 );
