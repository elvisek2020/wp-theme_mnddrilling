<?php
/**
 * Základní nastavení šablony: menu, widgety, velikosti obrázků, šířka obsahu.
 *
 * Umístění menu, oblasti widgetů a velikosti obrázků mají stejné názvy jako v původní
 * šabloně, takže se po přepnutí přiřazení zachová a obrázky není potřeba přegenerovat.
 *
 * @package MNDDrilling
 */

defined( 'ABSPATH' ) || exit;

/**
 * Nastavení šablony po jejím načtení.
 */
function mnd_setup() {
	// title-tag, post-thumbnails, responsive-embeds, editor-styles a html5 zapíná inc/ekt/common.php.
	// Editor vypadá jako web; paleta a velikosti písma jsou v theme.json.
	add_editor_style( 'assets/css/editor.css' );

	register_nav_menus(
		array(
			'homepage-menu' => __( 'Hlavní menu (dlaždice divizí)', 'mnddrilling' ),
			'footer-menu'   => __( 'Menu v patičce', 'mnddrilling' ),
		)
	);

	add_image_size( 'page-image', 630, 230, true );
	add_image_size( 'item-image', 630, 800, true );
	add_image_size( 'slider-image', 1600, 420, true );
	add_image_size( 'flag-image', 95, 50, true );
}
add_action( 'after_setup_theme', 'mnd_setup' );

/**
 * Bez odkazů na RSS v hlavičce: web nemá články a původní šablona je nevypisovala
 * (automatic-feed-links zapíná inc/ekt/common.php).
 */
function mnd_no_feed_links() {
	remove_theme_support( 'automatic-feed-links' );
}
add_action( 'after_setup_theme', 'mnd_no_feed_links', 20 );

/**
 * Šířka obsahu pro vložená média (sloupec vedle podmenu).
 */
function mnd_content_width() {
	$GLOBALS['content_width'] = 630;
}
add_action( 'after_setup_theme', 'mnd_content_width', 0 );

/**
 * Oblasti widgetů v patičce (adresa a kontakty) – stejná ID jako v původní šabloně.
 */
function mnd_widgets_init() {
	$common = array(
		'before_widget' => '<div id="%1$s" class="mnd-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4 class="mnd-widget__title">',
		'after_title'   => '</h4>',
	);
	register_sidebar(
		array_merge(
			$common,
			array(
				'id'          => 'homepage_footer_1',
				'name'        => __( 'Patička – Sídlo firmy', 'mnddrilling' ),
				'description' => __( 'Adresa ve druhém sloupci patičky. S Polylangem jeden widget pro každý jazyk.', 'mnddrilling' ),
			)
		)
	);
	register_sidebar(
		array_merge(
			$common,
			array(
				'id'          => 'homepage_footer_2',
				'name'        => __( 'Patička – Kontakty', 'mnddrilling' ),
				'description' => __( 'Telefon, e-mail a odkazy ve třetím sloupci patičky.', 'mnddrilling' ),
			)
		)
	);
}
add_action( 'widgets_init', 'mnd_widgets_init' );

// Stručný výpis u stránek (popis pro vyhledávače) zapíná modul inc/ekt/seo.php.
