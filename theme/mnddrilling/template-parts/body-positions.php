<?php
/**
 * Výpis volných pozic (6 na stránku): název, datum a kategorie.
 *
 * @package MNDDrilling
 */

defined( 'ABSPATH' ) || exit;

$mnd_paged     = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
$mnd_positions = new WP_Query(
	array(
		'post_type'      => 'position',
		'post_status'    => 'publish',
		'posts_per_page' => 6,
		'paged'          => $mnd_paged,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	)
);
?>
<div class="mnd-positions">
	<?php while ( $mnd_positions->have_posts() ) : ?>
		<?php
		$mnd_positions->the_post();
		$mnd_category = function_exists( 'mnd_core_position_category_name' ) ? mnd_core_position_category_name( get_the_ID() ) : '';
		?>
		<article class="mnd-position">
			<h3 class="mnd-position__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
			<p class="mnd-position__meta"><?php echo esc_html( get_the_date( 'd. m. Y' ) ); ?> | <?php echo esc_html( $mnd_category ); ?></p>
		</article>
	<?php endwhile; ?>
</div>
<?php
mnd_pagination( $mnd_positions, $mnd_paged );
wp_reset_postdata();
