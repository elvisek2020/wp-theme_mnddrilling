<?php
/**
 * Katalog položek pod textem stránky (vrtné soupravy, vybavení…).
 *
 * Kategorie položek je vybraná u stránky (pole „item_category“). Nahoře výběr: podkategorie
 * a položky přímo v kategorii, u podkategorie druhý řádek s jejími položkami, pod tím
 * medailonek vybrané položky. Bez JavaScriptu se ukážou všechny položky pod sebou.
 * Adresy #item-ID z původní šablony fungují dál.
 *
 * @package MNDDrilling
 */

defined( 'ABSPATH' ) || exit;

$mnd_root = (int) get_post_meta( $args['page']->ID, 'item_category', true );
if ( ! $mnd_root ) {
	return;
}

$mnd_lang  = function_exists( 'pll_current_language' ) ? (string) pll_current_language() : '';
$mnd_query = function ( $terms, $exclude = array() ) use ( $mnd_lang ) {
	$tax = array(
		array(
			'taxonomy' => 'itemcategory',
			'field'    => 'term_id',
			'terms'    => $terms,
		),
	);
	if ( $exclude ) {
		$tax[] = array(
			'taxonomy' => 'itemcategory',
			'field'    => 'term_id',
			'terms'    => $exclude,
			'operator' => 'NOT IN',
		);
	}
	$query = array(
		'post_type'      => 'item',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
		'tax_query'      => $tax, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
	);
	if ( $mnd_lang ) {
		$query['lang'] = $mnd_lang;
	}
	return get_posts( $query );
};

$mnd_groups = get_terms(
	array(
		'taxonomy' => 'itemcategory',
		'parent'   => $mnd_root,
	)
);
$mnd_groups = is_array( $mnd_groups ) ? $mnd_groups : array();
$mnd_all    = $mnd_query( $mnd_root );
if ( ! $mnd_all ) {
	return;
}
$mnd_direct = $mnd_groups ? $mnd_query( $mnd_root, wp_list_pluck( $mnd_groups, 'term_id' ) ) : $mnd_all;
?>
<div class="mnd-items" data-mnd-items>
	<ul class="mnd-items__nav">
		<?php foreach ( $mnd_groups as $mnd_group ) : ?>
			<li><a href="#group-<?php echo (int) $mnd_group->term_id; ?>" data-group="<?php echo (int) $mnd_group->term_id; ?>"><?php echo esc_html( html_entity_decode( $mnd_group->name, ENT_QUOTES, 'UTF-8' ) ); ?></a></li>
		<?php endforeach; ?>
		<?php foreach ( $mnd_direct as $mnd_item ) : ?>
			<li><a href="#item-<?php echo (int) $mnd_item->ID; ?>" data-item="<?php echo (int) $mnd_item->ID; ?>"><?php echo esc_html( mnd_title_text( $mnd_item ) ); ?></a></li>
		<?php endforeach; ?>
	</ul>

	<?php foreach ( $mnd_groups as $mnd_group ) : ?>
		<ul class="mnd-items__nav mnd-items__nav--group" data-group-list="<?php echo (int) $mnd_group->term_id; ?>">
			<?php foreach ( $mnd_query( $mnd_group->term_id ) as $mnd_item ) : ?>
				<li><a href="#item-<?php echo (int) $mnd_item->ID; ?>" data-item="<?php echo (int) $mnd_item->ID; ?>" data-in-group="<?php echo (int) $mnd_group->term_id; ?>"><?php echo esc_html( mnd_title_text( $mnd_item ) ); ?></a></li>
			<?php endforeach; ?>
		</ul>
	<?php endforeach; ?>

	<div class="mnd-items__list">
		<?php
		foreach ( $mnd_all as $mnd_item ) {
			get_template_part( 'template-parts/item', null, array( 'item' => $mnd_item ) );
		}
		?>
	</div>
</div>
