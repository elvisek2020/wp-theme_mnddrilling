<?php
/**
 * Titulka (výpis divizí pod sliderem) a záložní šablona.
 *
 * @package MNDDrilling
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="mnd-inner">
	<?php mnd_tiles(); ?>
	<?php if ( ! is_front_page() && have_posts() ) : ?>
		<div class="mnd-page mnd-page--wide">
			<?php while ( have_posts() ) : ?>
				<?php the_post(); ?>
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			<?php endwhile; ?>
		</div>
	<?php endif; ?>
</div>
<?php
get_footer();
