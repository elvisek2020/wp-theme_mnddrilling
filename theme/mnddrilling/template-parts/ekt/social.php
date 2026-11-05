<?php
/**
 * Ikony sítí ve spodní liště (override výstupu kanonického modulu social).
 *
 * Vzhled jako v původní šabloně: šedá kolečka, YouTube před LinkedIn, při najetí barva sítě.
 * Odkazy se vyplňují v Přizpůsobit → MND Drilling → Sociální sítě.
 *
 * @package MNDDrilling
 *
 * @var array $args items (network, label, icon, url – escapovaná, external), class.
 */

defined( 'ABSPATH' ) || exit;

$mnd_items = (array) ( $args['items'] ?? array() );
if ( ! $mnd_items ) {
	return;
}
// Pořadí a modifikátory tříd z původní patičky.
$mnd_order = array( 'youtube' => 'yt', 'linkedin' => 'li' );
usort(
	$mnd_items,
	static function ( $a, $b ) use ( $mnd_order ) {
		$pos = array_flip( array_keys( $mnd_order ) );
		return ( $pos[ $a['network'] ] ?? 99 ) <=> ( $pos[ $b['network'] ] ?? 99 );
	}
);
?>
<ul class="mnd-social">
	<?php foreach ( $mnd_items as $mnd_item ) : ?>
		<?php $mnd_mod = $mnd_order[ $mnd_item['network'] ] ?? sanitize_html_class( $mnd_item['network'] ); ?>
		<li><a href="<?php echo $mnd_item['url']; // phpcs:ignore WordPress.Security.EscapeOutput -- escapováno v ekt_social_items() ?>" class="mnd-social__link mnd-social__link--<?php echo esc_attr( $mnd_mod ); ?>"<?php echo $mnd_item['external'] ? ' target="_blank" rel="noopener"' : ''; ?> title="<?php echo esc_attr( 'MND Drilling & Services ' . mnd_t( 'na', 'on' ) . ' ' . $mnd_item['label'] ); ?>"><?php echo mnd_icon( $mnd_item['icon'], 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span class="screen-reader-text"><?php echo esc_html( $mnd_item['label'] ); ?></span></a></li>
	<?php endforeach; ?>
</ul>
