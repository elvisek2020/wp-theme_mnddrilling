<?php
/**
 * Bezpečnostní hlavičky, úpravy sitemap a /llms.txt (zapíná se v Nastavení → MND Drilling Core).
 *
 * @package MNDDrillingCore
 */

defined( 'ABSPATH' ) || exit;

/* =========================================================================
 * Bezpečnostní hlavičky
 * ====================================================================== */

/**
 * Bezpečnostní hlavičky na webu. Administrace a přihlášení posílají X-Frame-Options samy.
 * HSTS jen na HTTPS a mimo lokální vývoj, bez includeSubDomains (subdomény můžou jet i bez HTTPS).
 * Úprava filtrem mnd_core_security_headers.
 */
function mnd_core_security_headers() {
	if ( headers_sent() ) {
		return;
	}

	$headers = array(
		'X-Content-Type-Options' => 'nosniff',
		'X-Frame-Options'        => 'SAMEORIGIN',
		'Referrer-Policy'        => 'strict-origin-when-cross-origin',
		'Permissions-Policy'     => 'camera=(), microphone=(), geolocation=(), payment=(), browsing-topics=()',
	);
	if ( is_ssl() && mnd_core_on( 'hsts' ) && 'local' !== wp_get_environment_type() ) {
		$headers['Strict-Transport-Security'] = 'max-age=31536000';
	}

	foreach ( (array) apply_filters( 'mnd_core_security_headers', $headers ) as $name => $value ) {
		if ( $value ) {
			header( $name . ': ' . $value );
		}
	}
}
if ( mnd_core_on( 'security_headers' ) ) {
	add_action( 'send_headers', 'mnd_core_security_headers' );
}

/* =========================================================================
 * Sitemap: bez uživatelů (prozrazuje přihlašovací jméno) a s datem poslední změny
 * ====================================================================== */

/**
 * Sitemap bez uživatelů.
 *
 * @param WP_Sitemaps_Provider|false $provider Poskytovatel.
 * @param string                     $name     Název.
 * @return WP_Sitemaps_Provider|false
 */
function mnd_core_sitemap_providers( $provider, $name ) {
	return 'users' === $name ? false : $provider;
}
if ( mnd_core_on( 'sitemap_no_users' ) ) {
	add_filter( 'wp_sitemaps_add_provider', 'mnd_core_sitemap_providers', 10, 2 );
}

/**
 * Datum změny u stránek a příspěvků.
 *
 * @param array   $entry Položka sitemapy.
 * @param WP_Post $post  Obsah.
 * @return array
 */
function mnd_core_sitemap_entry_lastmod( $entry, $post ) {
	$entry['lastmod'] = wp_date( DATE_W3C, strtotime( $post->post_modified_gmt . ' UTC' ) );
	return $entry;
}

/**
 * Datum poslední změny v indexu sitemapy.
 *
 * @param array  $entry          Položka indexu.
 * @param string $object_type    Typ (post, term…).
 * @param string $object_subtype Podtyp (page, post…).
 * @return array
 */
function mnd_core_sitemap_index_lastmod( $entry, $object_type, $object_subtype ) {
	global $wpdb;
	if ( 'post' === $object_type && $object_subtype ) {
		$last = $wpdb->get_var( $wpdb->prepare( "SELECT MAX(post_modified_gmt) FROM {$wpdb->posts} WHERE post_type = %s AND post_status = 'publish'", $object_subtype ) );
		if ( $last ) {
			$entry['lastmod'] = wp_date( DATE_W3C, strtotime( $last . ' UTC' ) );
		}
	}
	return $entry;
}
if ( mnd_core_on( 'sitemap_lastmod' ) ) {
	add_filter( 'wp_sitemaps_posts_entry', 'mnd_core_sitemap_entry_lastmod', 10, 2 );
	add_filter( 'wp_sitemaps_index_entry', 'mnd_core_sitemap_index_lastmod', 10, 3 );
}

/* =========================================================================
 * /llms.txt – stručný přehled webu pro AI (https://llmstxt.org)
 * ====================================================================== */

/**
 * Text bez HTML, zkrácený na celé slovo.
 *
 * @param string $text  Text.
 * @param int    $words Počet slov.
 * @return string
 */
function mnd_core_llms_text( $text, $words = 60 ) {
	$text = wp_strip_all_tags( strip_shortcodes( excerpt_remove_blocks( (string) $text ) ) );
	$text = trim( preg_replace( '/\s+/u', ' ', html_entity_decode( $text, ENT_QUOTES, 'UTF-8' ) ) );
	return wp_trim_words( $text, $words, '…' );
}

/**
 * Obsah /llms.txt.
 *
 * @return string
 */
function mnd_core_llms_content() {
	$out   = array();
	$out[] = '# ' . html_entity_decode( get_bloginfo( 'name' ), ENT_QUOTES, 'UTF-8' );
	$out[] = '';

	// Titulka je výpis divizí, popis firmy je na stránce „O nás“.
	$about_page = get_page_by_path( 'o-spolecnosti/about-company' );
	$about      = $about_page ? mnd_core_llms_text( $about_page->post_content ) : '';
	$desc       = html_entity_decode( get_bloginfo( 'description' ), ENT_QUOTES, 'UTF-8' );
	$out[]      = '> ' . ( $desc ? $desc : wp_trim_words( $about, 25, '…' ) );
	$out[]      = '';
	if ( $about ) {
		$out[] = $about;
		$out[] = '';
	}

	$langs = function_exists( 'pll_languages_list' ) ? (array) pll_languages_list( array( 'fields' => '' ) ) : array();
	if ( ! $langs ) {
		$langs = array( (object) array( 'slug' => '', 'name' => __( 'Stránky', 'mnddrilling-core' ) ) );
	}

	foreach ( $langs as $lang ) {
		$args = array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => 300,
			'orderby'        => 'menu_order title',
			'order'          => 'ASC',
			'lang'           => $lang->slug,
		);
		$pages = get_posts( $args );
		if ( ! $pages ) {
			continue;
		}
		$home  = function_exists( 'pll_home_url' ) && $lang->slug ? pll_home_url( $lang->slug ) : home_url( '/' );
		$out[] = '## ' . $lang->name . ' (' . $home . ')';
		$out[] = '';

		// Hlavní stránky (divize) a pod nimi podstránky.
		$children = array();
		foreach ( $pages as $page ) {
			$children[ (int) $page->post_parent ][] = $page;
		}
		$walk = function ( $parent, $depth ) use ( &$walk, &$out, $children ) {
			foreach ( isset( $children[ $parent ] ) ? $children[ $parent ] : array() as $page ) {
				$summary = $depth ? mnd_core_llms_text( has_excerpt( $page ) ? $page->post_excerpt : $page->post_content, 18 ) : '';
				$out[]   = sprintf( '%s- [%s](%s)%s', str_repeat( '  ', $depth ), html_entity_decode( get_the_title( $page ), ENT_QUOTES, 'UTF-8' ), get_permalink( $page ), $summary ? ': ' . $summary : '' );
				$walk( (int) $page->ID, $depth + 1 );
			}
		};
		$walk( 0, 0 );
		$out[] = '';
	}

	$out[] = '## ' . __( 'Další', 'mnddrilling-core' );
	$out[] = '';
	$out[] = '- [Sitemap](' . home_url( '/wp-sitemap.xml' ) . ')';

	return implode( "\n", (array) apply_filters( 'mnd_core_llms_lines', $out ) ) . "\n";
}

/**
 * Obsluha adresy /llms.txt (bez přepisovacích pravidel).
 */
function mnd_core_llms_txt() {
	if ( ! isset( $_SERVER['REQUEST_URI'] ) ) {
		return;
	}
	$path = trim( (string) wp_parse_url( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH ), '/' );
	$home = trim( (string) wp_parse_url( home_url(), PHP_URL_PATH ), '/' );
	if ( ( $home ? $home . '/' : '' ) . 'llms.txt' !== $path ) {
		return;
	}
	status_header( 200 );
	header( 'Content-Type: text/plain; charset=utf-8' );
	header( 'Cache-Control: public, max-age=3600' );
	echo mnd_core_llms_content(); // phpcs:ignore WordPress.Security.EscapeOutput -- prostý text
	exit;
}
if ( mnd_core_on( 'llms_txt' ) ) {
	add_action( 'parse_request', 'mnd_core_llms_txt', 1 );
}
