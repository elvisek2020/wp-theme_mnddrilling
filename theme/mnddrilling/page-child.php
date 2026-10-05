<?php
/**
 * Template Name: Šablona podstránky
 *
 * Podstránka divize: podmenu sekce vlevo, text a dokumenty ke stažení vpravo.
 *
 * @package MNDDrilling
 */

defined( 'ABSPATH' ) || exit;

get_header();
while ( have_posts() ) {
	the_post();
	get_template_part( 'template-parts/section', null, array( 'page' => get_post() ) );
}
get_footer();
