<?php
/**
 * Detail volné pozice: v podmenu je aktivní „Volné pozice“, pod textem tlačítka
 * „Mám zájem o tuto pozici“ a „Zpět na výpis“.
 *
 * @package MNDDrilling
 */

defined( 'ABSPATH' ) || exit;

get_header();
while ( have_posts() ) {
	the_post();
	$mnd_list = mnd_page_id_by_cs_path( 'kariera/volne-pozice' );
	if ( $mnd_list ) {
		get_template_part(
			'template-parts/section',
			null,
			array(
				'page'     => get_post(),
				'active'   => $mnd_list,
				'body'     => 'position-links',
				'new_type' => '',
			)
		);
	} else {
		get_template_part( 'template-parts/single' );
	}
}
get_footer();
