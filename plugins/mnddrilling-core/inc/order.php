<?php
/**
 * Pořadí obsahu přetažením (nahrazuje Simple Custom Post Order).
 *
 *  - Výpisy stránek a typů obsahu se řadí podle pole „Pořadí“ (menu_order) vzestupně,
 *    pokud dotaz neurčí jiné řazení – stejně jako dřív (i get_posts() bez řazení).
 *  - V administraci jde pořadí měnit přetažením řádků v přehledu.
 *  - Skupiny společností se na webu řadí podle term_order (sloupec, který plugin přidal do DB).
 * Dokud je Simple Custom Post Order aktivní, modul nic nedělá.
 *
 * @package MNDDrillingCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Řazené typy obsahu.
 *
 * @return array
 */
function mnd_core_order_types() {
	return (array) apply_filters( 'mnd_core_order_types', array( 'page', 'slide', 'timelineitem', 'companies', 'position', 'document', 'item', 'mapitem', 'person' ) );
}

/**
 * Taxonomie řazené podle term_order.
 *
 * @return array
 */
function mnd_core_order_taxonomies() {
	return array( 'companygroup' );
}

/**
 * Je původní plugin ještě aktivní?
 *
 * @return bool
 */
function mnd_core_order_legacy() {
	return class_exists( 'SCPO_Engine' ) || class_exists( 'SCPO' ) || defined( 'SCPORDER_URL' );
}

/**
 * Výchozí řazení podle pořadí (chování převzaté ze Simple Custom Post Order).
 *
 * @param WP_Query $query Dotaz.
 */
function mnd_core_order_pre_get_posts( $query ) {
	if ( mnd_core_order_legacy() || $query->is_search() ) {
		return;
	}
	$type = isset( $query->query['post_type'] ) ? $query->query['post_type'] : null;
	if ( ! is_string( $type ) || ! in_array( $type, mnd_core_order_types(), true ) ) {
		return;
	}

	if ( is_admin() && ! wp_doing_ajax() ) {
		if ( isset( $_GET['orderby'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- řazení kliknutím na sloupec
			return;
		}
		if ( ! $query->get( 'orderby' ) ) {
			$query->set( 'orderby', 'menu_order' );
		}
		if ( ! $query->get( 'order' ) ) {
			$query->set( 'order', 'ASC' );
		}
		return;
	}

	if ( isset( $query->query['suppress_filters'] ) ) {
		// get_posts() má výchozí řazení date DESC – dřív ho plugin nahrazoval pořadím.
		if ( 'date' === $query->get( 'orderby' ) ) {
			$query->set( 'orderby', 'menu_order' );
		}
		if ( 'DESC' === $query->get( 'order' ) ) {
			$query->set( 'order', 'ASC' );
		}
		return;
	}
	if ( ! $query->get( 'orderby' ) ) {
		$query->set( 'orderby', 'menu_order' );
	}
	if ( ! $query->get( 'order' ) ) {
		$query->set( 'order', 'ASC' );
	}
}
add_action( 'pre_get_posts', 'mnd_core_order_pre_get_posts' );

/**
 * Skupiny společností na webu podle term_order.
 *
 * @param string $orderby ORDER BY.
 * @param array  $args    Parametry get_terms().
 * @return string
 */
function mnd_core_order_terms( $orderby, $args ) {
	if ( mnd_core_order_legacy() || ( is_admin() && ! wp_doing_ajax() ) || empty( $args['taxonomy'] ) ) {
		return $orderby;
	}
	$taxonomy = is_array( $args['taxonomy'] ) ? reset( $args['taxonomy'] ) : $args['taxonomy'];
	if ( ! in_array( $taxonomy, mnd_core_order_taxonomies(), true ) || ! mnd_core_order_has_term_order() ) {
		return $orderby;
	}
	return 't.term_order';
}
add_filter( 'get_terms_orderby', 'mnd_core_order_terms', 10, 2 );

/**
 * Má tabulka termínů sloupec term_order?
 *
 * @return bool
 */
function mnd_core_order_has_term_order() {
	static $has = null;
	if ( null === $has ) {
		global $wpdb;
		$has = (bool) $wpdb->get_var( "SHOW COLUMNS FROM {$wpdb->terms} LIKE 'term_order'" );
	}
	return $has;
}

/**
 * Přetahování řádků v přehledu (Stránky, Položky, Lidé…).
 *
 * @param string $hook Stránka administrace.
 */
function mnd_core_order_assets( $hook ) {
	if ( 'edit.php' !== $hook || mnd_core_order_legacy() ) {
		return;
	}
	$type = isset( $_GET['post_type'] ) ? sanitize_key( wp_unslash( $_GET['post_type'] ) ) : 'post'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	// Jen ve výchozím řazení, bez hledání a filtru stavu – jinak by pořadí řádků neodpovídalo.
	if ( ! in_array( $type, mnd_core_order_types(), true ) || isset( $_GET['orderby'] ) || ! empty( $_GET['s'] ) || ( isset( $_GET['post_status'] ) && 'all' !== $_GET['post_status'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}
	if ( ! current_user_can( get_post_type_object( $type )->cap->edit_others_posts ) ) {
		return;
	}
	wp_enqueue_script( 'mnd-core-order', plugins_url( 'assets/order.js', MND_CORE_FILE ), array(), (string) filemtime( dirname( MND_CORE_FILE ) . '/assets/order.js' ), true );
	wp_localize_script(
		'mnd-core-order',
		'mndCoreOrder',
		array(
			'ajax'  => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'mnd_core_order' ),
			'type'  => $type,
			'saved' => __( 'Pořadí uloženo.', 'mnddrilling-core' ),
			'error' => __( 'Pořadí se nepodařilo uložit.', 'mnddrilling-core' ),
			'hint'  => __( 'Pořadí změníte přetažením řádku.', 'mnddrilling-core' ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'mnd_core_order_assets' );

/**
 * Uložení nového pořadí řádků (AJAX).
 *
 * Řádky dostanou stejná čísla pořadí, jaká měly, jen v novém sledu – ostatní stránky
 * přehledu se tím nezmění. Když mají řádky stejná čísla, nejdřív se celý typ přečísluje.
 */
function mnd_core_order_save() {
	check_ajax_referer( 'mnd_core_order', 'nonce' );
	global $wpdb;

	$type = isset( $_POST['type'] ) ? sanitize_key( wp_unslash( $_POST['type'] ) ) : '';
	$ids  = isset( $_POST['ids'] ) ? array_values( array_filter( array_map( 'absint', (array) wp_unslash( $_POST['ids'] ) ) ) ) : array();
	if ( ! in_array( $type, mnd_core_order_types(), true ) || count( $ids ) < 2 || ! current_user_can( get_post_type_object( $type )->cap->edit_others_posts ) ) {
		wp_send_json_error( null, 403 );
	}

	$in      = implode( ',', $ids );
	$current = $wpdb->get_results( $wpdb->prepare( "SELECT ID, menu_order FROM {$wpdb->posts} WHERE post_type = %s AND ID IN ({$in})", $type ), OBJECT_K ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- ID jsou čísla
	if ( count( $current ) !== count( $ids ) ) {
		wp_send_json_error( null, 400 );
	}

	$orders = wp_list_pluck( $current, 'menu_order' );
	if ( count( array_unique( $orders ) ) !== count( $orders ) ) {
		// Duplicitní pořadí → přečíslovat celý typ 1…N podle současného řazení.
		$all = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_type = %s AND post_status NOT IN ('auto-draft','trash','inherit') ORDER BY menu_order ASC, post_date DESC", $type ) );
		foreach ( $all as $i => $id ) {
			$wpdb->update( $wpdb->posts, array( 'menu_order' => $i + 1 ), array( 'ID' => $id ) );
		}
		$current = $wpdb->get_results( $wpdb->prepare( "SELECT ID, menu_order FROM {$wpdb->posts} WHERE post_type = %s AND ID IN ({$in})", $type ), OBJECT_K ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$orders  = wp_list_pluck( $current, 'menu_order' );
	}

	$orders = array_map( 'intval', array_values( $orders ) );
	sort( $orders );
	foreach ( $ids as $i => $id ) {
		$wpdb->update( $wpdb->posts, array( 'menu_order' => $orders[ $i ] ), array( 'ID' => $id ) );
		clean_post_cache( $id );
	}
	wp_send_json_success();
}
add_action( 'wp_ajax_mnd_core_order', 'mnd_core_order_save' );
