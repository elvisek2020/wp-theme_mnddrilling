<?php
/**
 * Dlaždice divizí – hlavní menu (umístění „homepage-menu“) na titulce i nad každou stránkou.
 * Na mobilu jsou na podstránkách jen v hamburger menu (kompaktní mřížka), na titulce kompaktní.
 *
 * Ikona a odstín zelené jsou podle pořadí položky (1.–6.) stejně jako v původní šabloně,
 * aktivní divize (aktuální stránka nebo její předek) je šedá.
 *
 * @package MNDDrilling
 */

defined( 'ABSPATH' ) || exit;

/**
 * Výpis dlaždic jen z první úrovně menu.
 */
class MND_Tiles_Walker extends Walker_Nav_Menu {

	/**
	 * Pořadí dlaždice.
	 *
	 * @var int
	 */
	private $index = 0;

	/**
	 * Podúrovně se nevypisují.
	 *
	 * @param string   $output Výstup.
	 * @param int      $depth  Hloubka.
	 * @param stdClass $args   Parametry.
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {}

	/**
	 * Podúrovně se nevypisují.
	 *
	 * @param string   $output Výstup.
	 * @param int      $depth  Hloubka.
	 * @param stdClass $args   Parametry.
	 */
	public function end_lvl( &$output, $depth = 0, $args = null ) {}

	/**
	 * Začátek dlaždice.
	 *
	 * @param string   $output Výstup.
	 * @param WP_Post  $item   Položka menu.
	 * @param int      $depth  Hloubka.
	 * @param stdClass $args   Parametry.
	 * @param int      $id     ID položky.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		if ( $depth > 0 ) {
			return;
		}
		$this->index++;
		$classes = (array) $item->classes;
		$active  = (bool) array_intersect( $classes, array( 'current-menu-item', 'current-page-ancestor', 'current-menu-ancestor', 'current-menu-parent', 'current-page-parent' ) );
		$output .= sprintf(
			'<li class="mnd-tile mnd-tile--%1$d%2$s"><a class="mnd-tile__link" href="%3$s"%4$s><span class="mnd-tile__icon" aria-hidden="true"></span><span class="mnd-tile__title">%5$s</span></a>',
			min( $this->index, 6 ),
			$active ? ' is-active' : '',
			esc_url( $item->url ),
			$active ? ' aria-current="page"' : '',
			esc_html( $item->title )
		);
	}

	/**
	 * Konec dlaždice.
	 *
	 * @param string   $output Výstup.
	 * @param WP_Post  $item   Položka menu.
	 * @param int      $depth  Hloubka.
	 * @param stdClass $args   Parametry.
	 */
	public function end_el( &$output, $item, $depth = 0, $args = null ) {
		if ( 0 === $depth ) {
			$output .= '</li>';
		}
	}
}

/**
 * Dlaždice divizí.
 *
 * @param string $variant „menu“ = kompaktní mřížka v mobilním panelu (na desktopu skrytá).
 */
function mnd_tiles( $variant = '' ) {
	if ( ! has_nav_menu( 'homepage-menu' ) ) {
		return;
	}
	wp_nav_menu(
		array(
			'theme_location'  => 'homepage-menu',
			'container'       => 'nav',
			'container_class' => 'menu' === $variant ? 'mnd-tiles mnd-tiles--menu' : 'mnd-tiles',
			'container_aria_label' => 'menu' === $variant ? mnd_t( 'Menu', 'Menu' ) : mnd_t( 'Divize', 'Divisions' ),
			'menu_class'      => 'mnd-tiles__list',
			'depth'           => 1,
			'fallback_cb'     => false,
			'walker'          => new MND_Tiles_Walker(),
		)
	);
}
