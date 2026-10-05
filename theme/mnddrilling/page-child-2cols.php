<?php
/**
 * Template Name: Šablona podstránky 2 sloupce
 *
 * Podstránka s textem ve dvou sloupcích.
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
			'page'          => get_post(),
			'content_class' => 'mnd-entry--columns',
		)
	);
}
get_footer();
