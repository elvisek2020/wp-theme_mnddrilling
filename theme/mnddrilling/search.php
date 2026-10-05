<?php
/**
 * Výsledky hledání: nadřazená stránka / název.
 *
 * @package MNDDrilling
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="mnd-inner">
	<?php mnd_tiles(); ?>
	<section class="mnd-page mnd-page--wide mnd-search-results">
		<header class="mnd-page__header mnd-page__header--plain">
			<h1 class="mnd-page__title"><?php echo esc_html( mnd_pll( 'Výsledky hledání pro:' ) ); ?> <span><?php echo esc_html( get_search_query() ); ?></span></h1>
		</header>
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : ?>
				<?php
				the_post();
				$mnd_parent = wp_get_post_parent_id( get_the_ID() );
				?>
				<article class="mnd-search-result">
					<?php if ( $mnd_parent ) : ?>
						<span class="mnd-search-result__parent"><?php echo esc_html( mnd_title_text( $mnd_parent ) ); ?> / </span>
					<?php endif; ?>
					<h3 class="mnd-search-result__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
				</article>
			<?php endwhile; ?>
			<?php
			the_posts_pagination(
				array(
					'prev_text' => mnd_pll( 'Předchozí' ),
					'next_text' => mnd_pll( 'Další' ),
				)
			);
			?>
		<?php else : ?>
			<p><?php echo esc_html( mnd_pll( 'Žádné výsledky nenalezeny' ) ); ?>.</p>
		<?php endif; ?>
	</section>
</div>
<?php
get_footer();
