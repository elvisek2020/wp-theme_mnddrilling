<?php
/**
 * Tlačítka u volné pozice: „Mám zájem o tuto pozici“ (formulář s předvyplněnou pozicí)
 * a „Zpět na výpis“. Na stránce formuláře bez pozice jen návrat na výpis.
 *
 * @package MNDDrilling
 */

defined( 'ABSPATH' ) || exit;

$mnd_form = mnd_career_form_page_id();
$mnd_list = mnd_page_id_by_cs_path( 'kariera/volne-pozice' );
?>
<div class="mnd-position-links">
	<?php if ( 'position' === $args['page']->post_type && $mnd_form ) : ?>
		<a class="mnd-btn mnd-btn--caps" href="<?php echo esc_url( add_query_arg( 'pozice', rawurlencode( $args['page']->post_title ), get_permalink( $mnd_form ) ) ); ?>"><?php echo esc_html( mnd_pll( 'Mám zájem o tuto pozici' ) ); ?></a>
	<?php endif; ?>
	<?php if ( $mnd_list ) : ?>
		<a class="mnd-btn mnd-btn--caps" href="<?php echo esc_url( get_permalink( $mnd_list ) ); ?>"><?php echo esc_html( mnd_pll( 'Zpět na výpis' ) ); ?></a>
	<?php endif; ?>
</div>
