<?php
/**
 * Medailonek položky nebo člověka: obrázek vlevo, text vpravo.
 *
 * @package MNDDrilling
 */

defined( 'ABSPATH' ) || exit;

$mnd_item  = $args['item'];
$mnd_image = has_post_thumbnail( $mnd_item );
?>
<article class="mnd-item<?php echo $mnd_image ? '' : ' mnd-item--no-image'; ?>" data-item-view="<?php echo (int) $mnd_item->ID; ?>">
	<?php if ( $mnd_image ) : ?>
		<div class="mnd-item__image"><?php echo get_the_post_thumbnail( $mnd_item, 'item-image', array( 'loading' => 'lazy' ) ); ?></div>
	<?php endif; ?>
	<div class="mnd-item__text">
		<h2 class="mnd-item__title"><?php echo esc_html( mnd_title_text( $mnd_item ) ); ?></h2>
		<div class="mnd-entry">
			<?php echo apply_filters( 'the_content', $mnd_item->post_content ); // phpcs:ignore WordPress.Security.EscapeOutput -- výstup filtru the_content ?>
		</div>
		<?php
		if ( current_user_can( 'edit_post', $mnd_item->ID ) ) {
			edit_post_link( mnd_pll( 'Upravit' ), '<p class="mnd-edit-links"><span class="mnd-edit-link">', '</span></p>', $mnd_item->ID );
		}
		?>
	</div>
</article>
