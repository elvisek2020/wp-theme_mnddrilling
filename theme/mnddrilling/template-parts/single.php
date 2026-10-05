<?php
/**
 * Obsah přes celou šířku pod dlaždicemi (detail položky, dokumentu…).
 *
 * @package MNDDrilling
 */

defined( 'ABSPATH' ) || exit;

$mnd_file = function_exists( 'mnd_core_document_url' ) && 'document' === get_post_type() ? mnd_core_document_url( get_the_ID() ) : '';
?>
<div class="mnd-inner">
	<?php mnd_tiles(); ?>
	<article class="mnd-page mnd-page--wide">
		<header class="mnd-page__header">
			<h1 class="mnd-page__title"><?php the_title(); ?></h1>
		</header>
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="mnd-page__image"><?php the_post_thumbnail( 'page-image' ); ?></div>
		<?php endif; ?>
		<div class="mnd-entry"><?php the_content(); ?></div>
		<?php if ( $mnd_file ) : ?>
			<div class="mnd-documents"><a href="<?php echo esc_url( $mnd_file ); ?>" class="mnd-btn mnd-btn--doc"><?php echo esc_html( mnd_pll( 'Stáhnout' ) . ' ' . mnd_title_text() ); ?></a></div>
		<?php endif; ?>
		<?php mnd_edit_links( get_the_ID(), '' ); ?>
	</article>
</div>
