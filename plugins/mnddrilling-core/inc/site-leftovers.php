<?php
/**
 * Pozůstatky po odebraných pluginech a staré šabloně, které obecný kanon (modul maint-leftovers)
 * nezná: Advanced Access Manager s rolí Career Manager, ACF, Simple Fields a šablony.
 * Ostatní (Simple Login Log, BackUpWordPress, MonsterInsights, Huge IT Lightbox, Responsive
 * Lightbox, Admin Menu Editor, Simple Custom Post Order) má kanon sám.
 *
 * @package MNDDrillingCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Projektové definice pozůstatků (filtr ekc_leftover_definitions).
 *
 * @param array $defs Definice.
 * @return array
 */
function mnd_core_site_leftovers( array $defs ): array {
	$defs['aam'] = array(
		'name'    => 'Advanced Access Manager',
		'plugin'  => 'advanced-access-manager/aam.php',
		'options' => array( 'aam_%', 'aam-%', 'widget_aam_%' ),
		// Role, kterou spravoval jen AAM: žádný uživatel ji nemá a měla nebezpečná oprávnění (editace šablon, pluginy, uživatelé).
		'roles'   => array( 'career manager' ),
	);
	$defs['acf'] = array(
		'name'    => 'Advanced Custom Fields',
		'plugin'  => 'advanced-custom-fields/acf.php',
		'options' => array( 'acf_%' ),
	);
	$defs['simple-fields'] = array(
		'name'     => 'Simple Fields',
		'plugin'   => 'simple-fields/simple_fields.php',
		'options'  => array( 'simple_fields_%' ),
		// Stará data polí; dokumenty stránek už převedl inc/site-fields.php do pole „doc“.
		'postmeta' => array( '_simple_fields_%' ),
		'requires' => 'mnd_core_fields_migrated',
	);
	$defs['old-theme'] = array(
		'name'    => 'Původní šablona Ultimate for MND Drilling',
		'theme'   => 'ultimate-for-mnd-drilling',
		// Volby email_count, email_N a category_N (kategorie volných pozic) se používají dál – viz inc/site-positions.php.
		'options' => array( 'theme_mods_ultimate-for-mnd-drilling', 'theme_mods_ultimate-for-mnd', 'theme_mods_twentyfifteen' ),
		'paths'   => array( 'themes/ultimate-for-mnd-drilling' ),
	);
	$defs['default-themes'] = array(
		'name'    => 'Nepoužívané výchozí šablony (Twenty Twenty-Three, Twenty Twenty-Four)',
		'theme'   => array( 'twentytwentythree', 'twentytwentyfour' ),
		'options' => array( 'theme_mods_twentytwentythree', 'theme_mods_twentytwentyfour' ),
		'paths'   => array( 'themes/twentytwentythree', 'themes/twentytwentyfour' ),
	);
	// ID měření GA4 si šablona převezme z nastavení MonsterInsights – do té doby se jeho volby nenabízí.
	if ( isset( $defs['monsterinsights'] ) ) {
		$defs['monsterinsights']['requires'] = 'mnd_core_ga_taken_over';
	}
	return $defs;
}
add_filter( 'ekc_leftover_definitions', 'mnd_core_site_leftovers' );

/**
 * Má šablona ID měření GA4 (převzaté z MonsterInsights), nebo MonsterInsights žádné neměl?
 *
 * @return bool
 */
function mnd_core_ga_taken_over(): bool {
	$profile = get_option( 'monsterinsights_site_profile' );
	return '' !== (string) get_theme_mod( 'ekt_ga_id', '' ) || '' !== (string) get_theme_mod( 'mnd_ga_id', '' )
		|| (bool) get_theme_mod( 'mnd_ga_migrated' ) || ! is_array( $profile ) || empty( $profile['v4'] );
}
