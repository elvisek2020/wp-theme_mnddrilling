<?php
/**
 * Template Name: Šablona podstránky Volné pozice - Formulář
 *
 * Formulář „Zájem o pozici“: nadpis je název pozice z adresy (?pozice=…), pole formuláře
 * se předvyplní (inc/forms.php). Bez pozice se formulář nezobrazí – jako dřív, odkaz vede
 * zpět na výpis pozic.
 *
 * @package MNDDrilling
 */

defined( 'ABSPATH' ) || exit;

get_header();
while ( have_posts() ) {
	the_post();
	$mnd_position = mnd_requested_position();
	$mnd_list     = mnd_page_id_by_cs_path( 'kariera/volne-pozice' );
	get_template_part(
		'template-parts/section',
		null,
		array(
			'page'         => get_post(),
			'active'       => $mnd_list ? $mnd_list : get_the_ID(),
			'title'        => '' !== $mnd_position ? $mnd_position : mnd_title_text(),
			'show_content' => '' !== $mnd_position,
			'body'         => '' !== $mnd_position ? '' : 'position-links',
		)
	);
}
get_footer();
