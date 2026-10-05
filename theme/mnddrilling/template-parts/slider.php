<?php
/**
 * Slider na titulce – obrázky ze Slidů (náhledový obrázek, pořadí přetažením).
 * Bez JavaScriptu se ukáže první obrázek, na úzkých obrazovkách je skrytý (jako dřív).
 *
 * @package MNDDrilling
 */

defined( 'ABSPATH' ) || exit;

$mnd_slides = get_posts(
	array(
		'post_type'      => 'slide',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	)
);
$mnd_slides = array_values(
	array_filter(
		$mnd_slides,
		function ( $slide ) {
			return has_post_thumbnail( $slide );
		}
	)
);
if ( ! $mnd_slides ) {
	return;
}
?>
<section class="mnd-slider" aria-roledescription="carousel" aria-label="<?php echo esc_attr( mnd_t( 'Fotografie', 'Photos' ) ); ?>" data-mnd-slider>
	<div class="mnd-slider__track">
		<?php foreach ( $mnd_slides as $mnd_i => $mnd_slide ) : ?>
			<div class="mnd-slider__slide<?php echo 0 === $mnd_i ? ' is-active' : ''; ?>" aria-roledescription="slide"<?php echo 0 === $mnd_i ? '' : ' aria-hidden="true"'; ?>>
				<?php
				echo wp_get_attachment_image(
					get_post_thumbnail_id( $mnd_slide ),
					'slider-image',
					false,
					array(
						'alt'           => '',
						'loading'       => 0 === $mnd_i ? 'eager' : 'lazy',
						'fetchpriority' => 0 === $mnd_i ? 'high' : 'auto',
						'sizes'         => '(max-width: 1600px) 100vw, 1600px',
					)
				);
				?>
			</div>
		<?php endforeach; ?>
	</div>
	<?php if ( count( $mnd_slides ) > 1 ) : ?>
		<button type="button" class="mnd-slider__nav mnd-slider__nav--prev" data-mnd-slide="-1" aria-label="<?php echo esc_attr( mnd_t( 'Předchozí', 'Previous' ) ); ?>"></button>
		<button type="button" class="mnd-slider__nav mnd-slider__nav--next" data-mnd-slide="1" aria-label="<?php echo esc_attr( mnd_t( 'Další', 'Next' ) ); ?>"></button>
	<?php endif; ?>
</section>
