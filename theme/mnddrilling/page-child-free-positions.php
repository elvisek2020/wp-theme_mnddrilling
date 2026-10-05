<?php
/**
 * Template Name: Šablona podstránky Volné pozice
 *
 * Výpis volných pozic se stránkováním (6 na stránku).
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
			'page'     => get_post(),
			'body'     => 'positions',
			'new_type' => 'position',
		)
	);
}
get_footer();
