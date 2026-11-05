<?php
/**
 * EKC module: comments-off
 * Version: 1.0.0
 * Requires: common
 * Replaces: disable-comments
 * Summary: Komentáře a pingbacky úplně vypnuté včetně administrace, kanálů a widgetu.
 */

defined( 'ABSPATH' ) || exit;

/*
 * Volitelný modul pro weby, které komentáře nepoužívají. Zapnutí se čte při načtení souboru.
 */

ekc_settings_register(
	'comments_off',
	array(
		'type'    => 'bool',
		'default' => true,
		'label'   => 'Vypnout komentáře',
		'help'    => 'Komentáře a pingbacky úplně vypnuté včetně administrace, kanálů a widgetu.',
		'section' => 'Komentáře',
	)
);

/**
 * Žádný typ obsahu nepodporuje komentáře ani trackbacky.
 */
function ekc_comments_off_support(): void {
	foreach ( get_post_types() as $post_type ) {
		if ( post_type_supports( $post_type, 'comments' ) ) {
			remove_post_type_support( $post_type, 'comments' );
		}
		if ( post_type_supports( $post_type, 'trackbacks' ) ) {
			remove_post_type_support( $post_type, 'trackbacks' );
		}
	}
}

/**
 * Kanál komentářů vrací 404.
 */
function ekc_comments_off_feed_404(): void {
	if ( is_comment_feed() ) {
		global $wp_query;
		$wp_query->set_404();
		status_header( 404 );
	}
}

/**
 * Administrace: bez menu Komentáře a Nastavení → Diskuze.
 */
function ekc_comments_off_admin_menu(): void {
	remove_menu_page( 'edit-comments.php' );
	remove_submenu_page( 'options-general.php', 'options-discussion.php' );
}

/**
 * Přímý přístup na stránky komentářů přesměrovat na Nástěnku.
 */
function ekc_comments_off_admin_redirect(): void {
	global $pagenow;
	if ( in_array( $pagenow, array( 'edit-comments.php', 'comment.php', 'options-discussion.php' ), true ) ) {
		wp_safe_redirect( admin_url() );
		exit;
	}
}

/**
 * Bez komentářů v liště administrace.
 *
 * @param WP_Admin_Bar $bar Lišta.
 */
function ekc_comments_off_admin_bar( $bar ): void {
	$bar->remove_node( 'comments' );
}

/**
 * Bez widgetu Nejnovější komentáře.
 */
function ekc_comments_off_widgets(): void {
	unregister_widget( 'WP_Widget_Recent_Comments' );
}

if ( ekc_opt( 'comments_off' ) ) {
	add_action( 'init', 'ekc_comments_off_support', 100 );
	add_filter( 'comments_open', '__return_false', 20 );
	add_filter( 'pings_open', '__return_false', 20 );
	add_filter( 'comments_array', '__return_empty_array', 20 );
	add_filter( 'get_comments_number', '__return_zero', 20 );
	add_filter( 'feed_links_show_comments_feed', '__return_false' );
	add_action( 'template_redirect', 'ekc_comments_off_feed_404', 1 );
	add_action( 'admin_menu', 'ekc_comments_off_admin_menu', 999 );
	add_action( 'admin_init', 'ekc_comments_off_admin_redirect' );
	add_action( 'admin_bar_menu', 'ekc_comments_off_admin_bar', 999 );
	add_action( 'widgets_init', 'ekc_comments_off_widgets', 20 );
}
