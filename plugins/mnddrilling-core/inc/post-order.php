<?php
/**
 * EKC module: post-order
 * Version: 1.0.0
 * Requires: common
 * Replaces: simple-custom-post-order, intuitive-custom-post-order, post-types-order
 * Summary: Pořadí stránek a typů obsahu přetažením řádků v přehledu, výpisy se řadí podle pořadí.
 */

defined( 'ABSPATH' ) || exit;

/*
 * Typy obsahu v Nastavení (order_types, filtr ekc_order_types). Výpisy těchto typů se řadí
 * podle pole Pořadí (menu_order) vzestupně, pokud dotaz neurčí jiné řazení – stejně jako
 * Simple Custom Post Order (i get_posts() s výchozím řazením podle data). V administraci jde
 * pořadí měnit přetažením řádků v přehledu (bez jQuery, uložení přes admin-ajax).
 * Dokud je některý z nahrazovaných pluginů aktivní, modul nic nedělá.
 */

ekc_settings_register(
	'order_types',
	array(
		'type'     => 'list',
		'default'  => array( 'page' ),
		'label'    => 'Řazení přetažením',
		'help'     => 'Typy obsahu (slug, např. page), které se řadí podle pořadí a v přehledu jdou přetahovat.',
		'section'  => 'Obsah',
		'sanitize' => static function ( $value ): array {
			$items = is_array( $value ) ? $value : preg_split( '/[\r\n,]+/', (string) $value );
			return array_values( array_unique( array_filter( array_map( 'sanitize_key', (array) $items ) ) ) );
		},
	)
);

/**
 * Řazené typy obsahu.
 *
 * @return string[]
 */
function ekc_order_types(): array {
	$types = (array) ekc_filter( 'ekc_order_types', (array) ekc_opt( 'order_types' ) );
	return array_values( array_filter( array_map( 'strval', $types ) ) );
}

/**
 * Běží ještě nahrazovaný plugin?
 *
 * @return bool
 */
function ekc_order_legacy(): bool {
	return class_exists( 'SCPO_Engine' ) || class_exists( 'SCPO' ) || defined( 'SCPORDER_URL' ) || class_exists( 'Hicpo' ) || class_exists( 'CPTO' );
}

/**
 * Výchozí řazení podle pořadí.
 *
 * @param WP_Query $query Dotaz.
 */
function ekc_order_pre_get_posts( $query ): void {
	if ( ekc_order_legacy() || $query->is_search() ) {
		return;
	}
	$type = $query->query['post_type'] ?? null;
	if ( ! is_string( $type ) || ! in_array( $type, ekc_order_types(), true ) ) {
		return;
	}
	if ( is_admin() && ! wp_doing_ajax() ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- řazení kliknutím na sloupec
		if ( isset( $_GET['orderby'] ) ) {
			return;
		}
	} elseif ( isset( $query->query['suppress_filters'] ) ) {
		// get_posts() má výchozí řazení date DESC – nahrazovaný plugin ho měnil na pořadí.
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
add_action( 'pre_get_posts', 'ekc_order_pre_get_posts' );

/**
 * Přetahování řádků v přehledu (inline skript).
 *
 * @param string $hook Stránka administrace.
 */
function ekc_order_assets( $hook ): void {
	if ( 'edit.php' !== $hook || ekc_order_legacy() ) {
		return;
	}
	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- jen zobrazení
	$type = isset( $_GET['post_type'] ) ? sanitize_key( wp_unslash( $_GET['post_type'] ) ) : 'post';
	// Jen ve výchozím řazení, bez hledání a filtru stavu – jinak by pořadí řádků neodpovídalo.
	if ( ! in_array( $type, ekc_order_types(), true ) || isset( $_GET['orderby'] ) || ! empty( $_GET['s'] ) || ( isset( $_GET['post_status'] ) && 'all' !== $_GET['post_status'] ) ) {
		return;
	}
	// phpcs:enable
	$object = get_post_type_object( $type );
	if ( ! $object || ! current_user_can( $object->cap->edit_others_posts ) ) {
		return;
	}
	$cfg = array(
		'ajax'  => admin_url( 'admin-ajax.php' ),
		'nonce' => wp_create_nonce( 'ekc_order' ),
		'type'  => $type,
		'saved' => 'Pořadí uloženo.',
		'error' => 'Pořadí se nepodařilo uložit.',
		'hint'  => 'Pořadí změníte přetažením řádku.',
	);
	wp_register_script( 'ekc-post-order', false, array(), EKC_VERSION, true );
	wp_enqueue_script( 'ekc-post-order' );
	wp_add_inline_script( 'ekc-post-order', 'window.ekcPostOrder=' . wp_json_encode( $cfg ) . ';' . ekc_order_js() );
}
add_action( 'admin_enqueue_scripts', 'ekc_order_assets' );

/**
 * Skript přetahování: HTML5 drag & drop, po puštění uložení přes admin-ajax.
 *
 * @return string
 */
function ekc_order_js(): string {
	return <<<'JS'
(function () {
	'use strict';
	var cfg = window.ekcPostOrder, list = document.getElementById('the-list');
	if (!cfg || !list) { return; }
	var rows = function () {
		return Array.prototype.filter.call(list.children, function (tr) { return /^post-\d+$/.test(tr.id); });
	};
	if (rows().length < 2) { return; }
	var notice = document.createElement('p');
	notice.className = 'description';
	notice.style.margin = '6px 0';
	notice.textContent = cfg.hint;
	var table = list.closest('table');
	table.parentNode.insertBefore(notice, table);
	var dragged = null;
	rows().forEach(function (tr) { tr.draggable = true; tr.style.cursor = 'move'; });
	list.addEventListener('dragstart', function (e) {
		var tr = e.target.closest && e.target.closest('tr');
		if (!tr || (e.target.closest && e.target.closest('a, input, textarea, select, button'))) { e.preventDefault(); return; }
		dragged = tr;
		tr.style.opacity = '0.5';
		e.dataTransfer.effectAllowed = 'move';
		e.dataTransfer.setData('text/plain', tr.id);
	});
	list.addEventListener('dragover', function (e) {
		var tr = e.target.closest && e.target.closest('tr');
		if (!dragged || !tr || tr === dragged || tr.parentNode !== list) { return; }
		e.preventDefault();
		var box = tr.getBoundingClientRect();
		list.insertBefore(dragged, e.clientY > box.top + box.height / 2 ? tr.nextSibling : tr);
	});
	list.addEventListener('dragend', function () {
		if (!dragged) { return; }
		dragged.style.opacity = '';
		dragged = null;
		var body = new URLSearchParams();
		body.append('action', 'ekc_order');
		body.append('nonce', cfg.nonce);
		body.append('type', cfg.type);
		rows().forEach(function (tr) { body.append('ids[]', tr.id.replace('post-', '')); });
		fetch(cfg.ajax, { method: 'POST', credentials: 'same-origin', body: body })
			.then(function (r) { return r.json(); })
			.then(function (r) { notice.textContent = r && r.success ? cfg.saved : cfg.error; })
			.catch(function () { notice.textContent = cfg.error; });
	});
}());
JS;
}

/**
 * Uložení nového pořadí řádků (AJAX).
 *
 * Řádky dostanou stejná čísla pořadí, jaká měly, jen v novém sledu – ostatní stránky přehledu
 * se nezmění. Když mají řádky stejná čísla, nejdřív se celý typ přečísluje 1…N.
 */
function ekc_order_save(): void {
	check_ajax_referer( 'ekc_order', 'nonce' );
	global $wpdb;
	$type   = isset( $_POST['type'] ) ? sanitize_key( wp_unslash( $_POST['type'] ) ) : '';
	$ids    = isset( $_POST['ids'] ) ? array_values( array_unique( array_filter( array_map( 'absint', (array) wp_unslash( $_POST['ids'] ) ) ) ) ) : array();
	$object = get_post_type_object( $type );
	if ( ! $object || ! in_array( $type, ekc_order_types(), true ) || count( $ids ) < 2 || ! current_user_can( $object->cap->edit_others_posts ) ) {
		wp_send_json_error( null, 403 );
	}

	$in      = implode( ',', $ids );
	$current = $wpdb->get_results( $wpdb->prepare( "SELECT ID, menu_order FROM {$wpdb->posts} WHERE post_type = %s AND ID IN ({$in})", $type ), OBJECT_K ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- ID jsou čísla
	if ( count( $current ) !== count( $ids ) ) {
		wp_send_json_error( null, 400 );
	}
	$orders = wp_list_pluck( $current, 'menu_order' );
	if ( count( array_unique( $orders ) ) !== count( $orders ) ) {
		$all = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_type = %s AND post_status NOT IN ('auto-draft','trash','inherit') ORDER BY menu_order ASC, post_date DESC", $type ) );
		foreach ( $all as $i => $id ) {
			$wpdb->update( $wpdb->posts, array( 'menu_order' => $i + 1 ), array( 'ID' => (int) $id ) );
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
add_action( 'wp_ajax_ekc_order', 'ekc_order_save' );

/**
 * Migrace: typy obsahu ze Simple Custom Post Order (scporder_options → order_types).
 *
 * @param array $steps Kroky.
 * @return array
 */
function ekc_order_migrations( array $steps ): array {
	$steps['post-order/scpo'] = 'ekc_order_migrate_scpo';
	return $steps;
}
add_filter( 'ekc_migrations', 'ekc_order_migrations' );

/**
 * Převezme typy obsahu ze SCPO, jen když web ještě nemá vlastní nastavení. Volbu SCPO nemaže
 * (odebere ji sekce Pozůstatky pluginů).
 *
 * @return bool
 */
function ekc_order_migrate_scpo(): bool {
	$scpo  = get_option( 'scporder_options' );
	$saved = get_option( 'ekc_settings', array() );
	if ( ! is_array( $scpo ) || empty( $scpo['objects'] ) || ( is_array( $saved ) && array_key_exists( 'order_types', $saved ) ) ) {
		return true;
	}
	return ekc_opt_set( 'order_types', array_values( (array) $scpo['objects'] ) ) || array_key_exists( 'order_types', (array) get_option( 'ekc_settings', array() ) );
}
