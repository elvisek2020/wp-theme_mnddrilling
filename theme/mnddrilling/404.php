<?php
/**
 * Stránka nenalezena: nadpis a dlaždice divizí jako cesta zpět na web.
 *
 * @package MNDDrilling
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="mnd-inner">
	<section class="mnd-page mnd-page--wide mnd-404">
		<h1 class="mnd-404__title"><?php echo esc_html( mnd_pll( '404 Stránka nebyla nalezena' ) ); ?></h1>
		<p><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( mnd_t( 'Přejít na úvodní stránku', 'Go to the homepage' ) ); ?></a></p>
	</section>
	<?php mnd_tiles(); ?>
</div>
<?php
get_footer();
