<?php
/**
 * Lidé (management) pod textem stránky: jména nahoře, medailonek vybraného člověka pod nimi.
 *
 * @package MNDDrilling
 */

defined( 'ABSPATH' ) || exit;

$mnd_people = get_posts(
	array(
		'post_type'      => 'person',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	)
);
if ( ! $mnd_people ) {
	return;
}
?>
<div class="mnd-items" data-mnd-items>
	<ul class="mnd-items__nav">
		<?php foreach ( $mnd_people as $mnd_person ) : ?>
			<li><a href="#item-<?php echo (int) $mnd_person->ID; ?>" data-item="<?php echo (int) $mnd_person->ID; ?>"><?php echo esc_html( mnd_title_text( $mnd_person ) ); ?></a></li>
		<?php endforeach; ?>
	</ul>
	<div class="mnd-items__list">
		<?php
		foreach ( $mnd_people as $mnd_person ) {
			get_template_part( 'template-parts/item', null, array( 'item' => $mnd_person ) );
		}
		?>
	</div>
</div>
