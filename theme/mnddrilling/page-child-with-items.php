<?php
/**
 * Template Name: Šablona podstránky s položkami
 *
 * Podstránka s katalogem položek (vrtné soupravy, vybavení…) z kategorie vybrané u stránky.
 *
 * @package MNDDrilling
 */

defined( 'ABSPATH' ) || exit;

get_header();
while ( have_posts() ) {
	the_post();
	get_template_part(
		'template-parts/section',
		null,
		array(
			'page' => get_post(),
			'body' => 'items',
		)
	);
}
get_footer();
