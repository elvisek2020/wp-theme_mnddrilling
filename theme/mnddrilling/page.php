<?php
/**
 * Výchozí šablona stránky: dlaždice a obsah přes celou šířku (např. Osobní dotazník).
 *
 * @package MNDDrilling
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="mnd-inner">
	<?php mnd_tiles(); ?>
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<article class="mnd-page mnd-page--wide">
			<header class="mnd-page__header">
				<h1 class="mnd-page__title"><?php the_title(); ?></h1>
			</header>
			<div class="mnd-entry"><?php the_content(); ?></div>
			<?php mnd_page_documents( get_the_ID() ); ?>
			<?php mnd_edit_links( get_the_ID() ); ?>
		</article>
	<?php endwhile; ?>
</div>
<?php
get_footer();
