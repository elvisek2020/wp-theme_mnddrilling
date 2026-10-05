<?php
/**
 * Detail ostatních typů obsahu (položka, dokument, člověk…).
 *
 * @package MNDDrilling
 */

defined( 'ABSPATH' ) || exit;

get_header();
while ( have_posts() ) {
	the_post();
	get_template_part( 'template-parts/single' );
}
get_footer();
