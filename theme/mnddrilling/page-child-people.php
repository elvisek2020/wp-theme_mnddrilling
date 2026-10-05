<?php
/**
 * Template Name: Šablona podstránky lidé
 *
 * Podstránka s lidmi (management) – výběr jména a medailonek.
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
			'body' => 'people',
		)
	);
}
get_footer();
