<?php
/**
 * Template Name: Šablona nadřazené stránky
 *
 * Hlavní stránka divize: zobrazí obsah první podstránky (ta je v podmenu aktivní).
 *
 * @package MNDDrilling
 */

defined( 'ABSPATH' ) || exit;

get_header();
while ( have_posts() ) {
	the_post();
	$mnd_children = mnd_child_pages( get_the_ID() );
	$mnd_first    = $mnd_children ? $mnd_children[0] : get_post();
	get_template_part(
		'template-parts/section',
		null,
		array(
			'page'   => $mnd_first,
			'active' => (int) $mnd_first->ID,
			'root'   => get_the_ID(),
		)
	);
}
get_footer();
