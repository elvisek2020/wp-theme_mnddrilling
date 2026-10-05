<?php
/**
 * Stránka sekce: dlaždice divizí, podmenu sekce vlevo a obsah vpravo.
 *
 * Parametry ($args):
 *  - page          WP_Post   obsah, který se zobrazí (povinné)
 *  - active        int       aktivní položka podmenu (výchozí ID stránky)
 *  - root          int       hlavní stránka sekce (výchozí nejvyšší předek aktivní položky)
 *  - title         string    nadpis místo názvu stránky
 *  - show_content  bool      vypsat text stránky (výchozí ano)
 *  - content_class string    další třída obsahu (např. sloupce)
 *  - body          string    doplňková část pod textem: items | people | positions | position-links
 *  - new_type      string    typ pro odkaz „Přidat…“ pro redaktory
 *
 * @package MNDDrilling
 */

defined( 'ABSPATH' ) || exit;

$mnd_page   = $args['page'];
$mnd_active = isset( $args['active'] ) ? (int) $args['active'] : (int) $mnd_page->ID;
$mnd_root   = isset( $args['root'] ) ? (int) $args['root'] : mnd_section_root( $mnd_active );
$mnd_title  = isset( $args['title'] ) ? (string) $args['title'] : mnd_title_text( $mnd_page );
?>
<div class="mnd-inner">
	<?php mnd_tiles(); ?>
	<div class="mnd-section">
		<?php mnd_sub_nav( $mnd_active, $mnd_root ); ?>
		<article class="mnd-page">
			<header class="mnd-page__header">
				<h1 class="mnd-page__title"><?php echo esc_html( $mnd_title ); ?></h1>
			</header>
			<?php if ( has_post_thumbnail( $mnd_page ) && 'page' === $mnd_page->post_type ) : ?>
				<div class="mnd-page__image"><?php echo get_the_post_thumbnail( $mnd_page, 'page-image' ); ?></div>
			<?php endif; ?>

			<?php if ( ! isset( $args['show_content'] ) || $args['show_content'] ) : ?>
				<div class="mnd-entry<?php echo empty( $args['content_class'] ) ? '' : ' ' . esc_attr( $args['content_class'] ); ?>">
					<?php echo apply_filters( 'the_content', $mnd_page->post_content ); // phpcs:ignore WordPress.Security.EscapeOutput -- výstup filtru the_content ?>
				</div>
			<?php endif; ?>

			<?php
			if ( ! empty( $args['body'] ) ) {
				get_template_part( 'template-parts/body', $args['body'], array( 'page' => $mnd_page ) );
			}
			mnd_page_documents( $mnd_page->ID );
			mnd_edit_links( $mnd_page->ID, isset( $args['new_type'] ) ? $args['new_type'] : 'page' );
			?>
		</article>
	</div>
</div>
